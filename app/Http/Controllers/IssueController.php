<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $roleName = strtoupper($user->role->name ?? 'STAFF');

        $query = Issue::with(['reporter', 'owner', 'organizationUnit'])->latest();

        // Isolasi Akses Data Berjenjang Sesuai Alur Eskalasi (Lowest Competent Level)
        if ($roleName === 'STAFF') {
            // Staff HANYA melihat issue yang dibuat oleh dirinya sendiri (antar staf tidak bisa saling lihat)
            $query->where('reported_by', $user->id);
        } elseif ($roleName === 'TL') {
            // TL HANYA melihat milik staf-stafnya (seksinya sendiri)
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $query->where(function ($q) use ($authorizedUnitIds, $user) {
                $q->whereIn('unit_id', $authorizedUnitIds)
                  ->orWhere('reported_by', $user->id)
                  ->orWhere('owner_id', $user->id);
            });
        } elseif ($roleName === 'ASMAN') {
            // Asman HANYA melihat apa yang DIBAWA oleh TL-nya (unit binaan Asman & sudah dieskalasikan ke ASMAN/MUP, atau dilaporkan oleh TL/Asman)
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $subordinateIds = $user->getSubordinateUserIds();
            $query->whereIn('unit_id', $authorizedUnitIds)
                  ->where(function ($q) use ($user, $subordinateIds) {
                      $q->whereIn('current_level', ['ASMAN', 'MUP'])
                        ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'))
                        ->orWhere('reported_by', $user->id)
                        ->orWhere(function ($sq) use ($subordinateIds) {
                            $sq->whereIn('reported_by', $subordinateIds)
                               ->whereHas('reporter.role', fn($rq) => $rq->where('name', 'TL'));
                        });
                  });
        } elseif ($roleName === 'MUP') {
            // MUP HANYA melihat apa yang DIBAWA oleh ASMAN-nya (sudah dieskalasikan ke level MUP atau dilaporkan oleh Asman/MUP)
            $query->where(function ($q) use ($user) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'))
                  ->orWhere('reported_by', $user->id)
                  ->orWhereHas('reporter.role', fn($rq) => $rq->whereIn('name', ['ASMAN', 'MUP']));
            });
        }
        // Role ADMIN melihat seluruh issue

        // Filter Level
        if ($request->filled('level') && $request->level !== 'Semua') {
            $query->where('current_level', strtoupper($request->level));
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'Semua') {
            $status = $request->status;
            if ($status === 'Need Decision') {
                $query->whereIn('status', ['Waiting Decision', 'Under Review']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter Priority
        if ($request->filled('priority') && $request->priority !== 'Semua') {
            $query->where('priority', strtoupper($request->priority));
        }

        // Filter Siklus Mingguan (BR-09)
        if ($request->filled('siklus') && $request->siklus !== 'semua') {
            if ($request->siklus === 'N') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->siklus === 'N-1') {
                $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            }
        }

        // Search Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('issue_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $issues = $query->paginate(15)->withQueryString();

        return Inertia::render('Issues/Index', [
            'issues' => $issues,
        ]);
    }

    public function create()
    {
        return redirect()->route('issues.index');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'issue_type' => 'nullable|string',
                'priority' => 'required|string',
                'seksi' => 'nullable|string',
                'due_date' => 'nullable|date',
                'attachment' => 'nullable|file|max:10240', // Max 10MB
            ]);

            // Normalisasi Priority
            $rawPriority = strtoupper(trim($request->priority));
            $priority = in_array($rawPriority, ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']) ? $rawPriority : 'MEDIUM';

            // Normalisasi Issue Type
            $rawType = strtoupper(trim($request->issue_type ?? 'PROBLEM'));
            $issueType = in_array($rawType, ['PROBLEM', 'DECISION_REQUIRED', 'ACTION', 'INFORMATION']) ? $rawType : 'PROBLEM';

            // Auto-generate Issue No: ISS-YYYY-XXXX
            $year = now()->format('Y');
            $randomSeq = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $issueNo = "ISS-{$year}-{$randomSeq}";

            // Handle attachment file jika di-upload
            $description = $request->description;
            if ($request->filled('seksi')) {
                $description = "[Lokasi/Seksi: " . $request->seksi . "]\n" . $description;
            }

            $attachmentFilename = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $uploadPath = public_path('uploads/issues');
                if (!is_dir($uploadPath)) {
                    @mkdir($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $attachmentFilename);

                try {
                    $storagePath = storage_path('app/public/issues');
                    if (!is_dir($storagePath)) {
                        @mkdir($storagePath, 0755, true);
                    }
                    @copy($uploadPath . '/' . $attachmentFilename, $storagePath . '/' . $attachmentFilename);
                } catch (\Throwable $e) {}
            }

            $currentUser = auth()->user();
            $unitId = $currentUser?->unit_id ?? 1;

            $issue = Issue::create([
                'issue_no' => $issueNo,
                'title' => $request->title,
                'description' => $description,
                'attachment' => $attachmentFilename,
                'issue_type' => $issueType,
                'reported_by' => $currentUser?->id ?? 1,
                'unit_id' => $unitId,
                'owner_id' => $currentUser?->id ?? 1,
                'current_level' => 'TL', // Mulai di Level TL untuk pembahasan rapat Senin pagi
                'priority' => $priority,
                'status' => 'Open',
                'due_date' => $request->due_date ?? now()->addDays(3)->toDateString(),
            ]);

            return redirect()->route('issues.index')->with('success', "Issue #{$issue->issue_no} berhasil dilaporkan dan masuk ke antrean Rapat Senin Pagi (Level TL).");
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput()->with('error', 'Mohon lengkapi formulir sesuai ketentuan yang tertera.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan issue: ' . $e->getMessage());
        }
    }

    public function show(Issue $issue)
    {
        $user = auth()->user();
        $roleName = strtoupper($user->role->name ?? 'STAFF');

        // Validasi hak akses visualisasi isu berjenjang (Gatekeeping Alur)
        if ($roleName === 'STAFF') {
            if ($issue->reported_by !== $user->id) {
                return redirect()->route('issues.index')->with('error', 'Anda tidak memiliki hak akses melihat isu milik staf lain.');
            }
        } elseif ($roleName === 'TL') {
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            if (!in_array($issue->unit_id, $authorizedUnitIds) && $issue->reported_by !== $user->id) {
                return redirect()->route('issues.index')->with('error', 'Anda hanya berhak melihat isu milik staf di seksi Anda.');
            }
        } elseif ($roleName === 'ASMAN') {
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $isBroughtByTl = in_array($issue->current_level, ['ASMAN', 'MUP'])
                || $issue->escalations()->where('to_level', 'ASMAN')->exists()
                || $issue->reported_by === $user->id
                || ($issue->reporter && $issue->reporter->role && $issue->reporter->role->name === 'TL');

            if (!in_array($issue->unit_id, $authorizedUnitIds) || !$isBroughtByTl) {
                return redirect()->route('issues.index')->with('error', 'Isu ini masih berada di level staf/TL dan belum dibawa/dieskalasikan oleh Team Leader ke jenjang Asman.');
            }
        } elseif ($roleName === 'MUP') {
            $isBroughtByAsman = $issue->current_level === 'MUP'
                || $issue->escalations()->where('to_level', 'MUP')->exists()
                || $issue->reported_by === $user->id
                || ($issue->reporter && $issue->reporter->role && in_array($issue->reporter->role->name, ['ASMAN', 'MUP']));

            if (!$isBroughtByAsman) {
                return redirect()->route('issues.index')->with('error', 'Isu ini belum dibawa/dieskalasikan oleh Asisten Manajer ke jenjang Manajemen (MUP).');
            }
        }

        $issue->load([
            'reporter', 
            'owner', 
            'organizationUnit',
            'escalations.fromUser', 
            'escalations.toUser', 
            'decisions.decisionMaker', 
            'tasks.assignee',
            'discussions.user.role'
        ]);

        // Dropdown PIC hanya menampilkan personil di bawah lingkup kewenangan pengguna
        $subordinateIds = $user->getSubordinateUserIds();
        $users = \App\Models\User::where('is_active', true)
            ->whereIn('id', $subordinateIds)
            ->orderBy('name')
            ->get();
        if ($users->isEmpty()) {
            $users = \App\Models\User::where('is_active', true)->orderBy('name')->take(20)->get();
        }

        return Inertia::render('Issues/Show', [
            'issue' => $issue,
            'users' => $users,
        ]);
    }

    public function storeDiscussion(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $issue = Issue::findOrFail($id);

        \App\Models\IssueDiscussion::create([
            'issue_id' => $issue->id,
            'user_id' => auth()->id() ?? 1,
            'message' => $request->message,
        ]);

        return redirect()->route('issues.show', $issue->id)->with('success', 'Pesan diskusi berhasil dikirim.');
    }

    public function escalate(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        $issue = Issue::findOrFail($id);
        $userRole = strtoupper(auth()->user()->role->name ?? 'TL');

        $isDirectToMup = !empty($issue->hierarchy_line['is_direct_to_mup']) 
            || empty($issue->hierarchy_line['asman']);

        if ($issue->current_level === 'TL') {
            $fromLevel = 'TL';
            $toLevel = $isDirectToMup ? 'MUP' : 'ASMAN';
        } elseif ($issue->current_level === 'ASMAN') {
            $fromLevel = 'ASMAN';
            $toLevel = 'MUP';
        } else {
            return back()->with('error', 'Isu sudah berada pada level tertinggi (MUP) atau tidak dapat dieskalasi lagi.');
        }

        // Catat riwayat eskalasi
        Escalation::create([
            'issue_id' => $issue->id,
            'from_level' => $fromLevel,
            'from_user_id' => auth()->id() ?? 1,
            'to_level' => $toLevel,
            'to_user_id' => null,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        // Perbarui jenjang isu
        $issue->update([
            'current_level' => $toLevel,
            'status' => 'Escalated',
        ]);

        return redirect()->route('issues.show', $issue->id)->with('success', "Issue berhasil dieskalasi ke jenjang {$toLevel}.");
    }

    public function update(Request $request, $id)
    {
        $issue = Issue::findOrFail($id);
        $user = auth()->user();
        $roleName = strtoupper($user->role->name ?? 'STAFF');

        // Hak akses edit: Pelapornya sendiri, TL seksinya, Asman bidangnya, atau MUP/Admin
        $isAuthorized = ($issue->reported_by === $user->id)
            || ($roleName === 'TL' && in_array($issue->unit_id, $user->getAuthorizedUnitIds()))
            || ($roleName === 'ASMAN' && in_array($issue->unit_id, $user->getAuthorizedUnitIds()))
            || in_array($roleName, ['MUP', 'ADMIN']);

        if (!$isAuthorized) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengedit issue ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string',
            'status' => 'nullable|string',
            'due_date' => 'nullable|date',
            'issue_type' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $rawPriority = strtoupper(trim($validated['priority']));
        $priority = in_array($rawPriority, ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']) ? $rawPriority : $issue->priority;

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $priority,
            'due_date' => $validated['due_date'] ?? $issue->due_date,
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $uploadPath = public_path('uploads/issues');
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $attachmentFilename);

            try {
                $storagePath = storage_path('app/public/issues');
                if (!is_dir($storagePath)) {
                    @mkdir($storagePath, 0755, true);
                }
                @copy($uploadPath . '/' . $attachmentFilename, $storagePath . '/' . $attachmentFilename);
            } catch (\Throwable $e) {}

            $updateData['attachment'] = $attachmentFilename;
        }

        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }
        if (!empty($validated['issue_type'])) {
            $updateData['issue_type'] = $validated['issue_type'];
        }

        $issue->update($updateData);

        return back()->with('success', "Issue #{$issue->issue_no} berhasil diperbarui.");
    }

    public function destroy(Request $request, $id)
    {
        $issue = Issue::findOrFail($id);
        $user = auth()->user();
        $roleName = strtoupper($user->role->name ?? 'STAFF');

        // Hak akses hapus: Pelapornya sendiri, TL seksinya, Asman bidangnya, atau MUP/Admin
        $isAuthorized = ($issue->reported_by === $user->id)
            || ($roleName === 'TL' && in_array($issue->unit_id, $user->getAuthorizedUnitIds()))
            || ($roleName === 'ASMAN' && in_array($issue->unit_id, $user->getAuthorizedUnitIds()))
            || in_array($roleName, ['MUP', 'ADMIN']);

        if (!$isAuthorized) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menghapus issue ini.');
        }

        $issueNo = $issue->issue_no;

        // Bersihkan relasi keterkaitan
        if (method_exists($issue, 'discussions')) {
            $issue->discussions()->delete();
        }
        if (method_exists($issue, 'escalations')) {
            $issue->escalations()->delete();
        }
        if (method_exists($issue, 'decisions')) {
            $issue->decisions()->delete();
        }
        if (method_exists($issue, 'meetings')) {
            $issue->meetings()->detach();
        }
        if (method_exists($issue, 'tasks')) {
            $issue->tasks()->update(['issue_id' => null]);
        }
        $issue->delete();

        return redirect()->route('issues.index')->with('success', "Issue #{$issueNo} berhasil dihapus.");
    }
}
