<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $query = Issue::with(['reporter', 'owner'])->latest();

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

        return view('issues.index', compact('issues'));
    }

    public function create()
    {
        return view('issues.create');
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

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/issues'), $filename);
                $description .= "\n[Lampiran: " . $filename . "]";
            }

            $issue = Issue::create([
                'issue_no' => $issueNo,
                'title' => $request->title,
                'description' => $description,
                'issue_type' => $issueType,
                'reported_by' => auth()->id() ?? 1,
                'owner_id' => auth()->id() ?? 1,
                'current_level' => 'TL', // Lowest competent level (mulai dari level TL)
                'priority' => $priority,
                'status' => 'Open',
                'due_date' => $request->due_date ?? now()->addDays(3)->toDateString(),
            ]);

            return redirect()->route('issues.index')->with('success', "Issue #{$issue->issue_no} berhasil disimpan dan masuk ke antrean Level TL.");
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput()->with('error', 'Mohon lengkapi formulir sesuai ketentuan yang tertera.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan issue: ' . $e->getMessage());
        }
    }

    public function show(Issue $issue)
    {
        $issue->load([
            'reporter', 
            'owner', 
            'escalations.fromUser', 
            'escalations.toUser', 
            'decisions.decisionMaker', 
            'tasks.assignee',
            'discussions.user.role'
        ]);

        $users = \App\Models\User::where('is_active', true)->orderBy('name')->get();

        return view('issues.show', compact('issue', 'users'));
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

        if ($issue->current_level === 'TL') {
            $fromLevel = 'TL';
            $toLevel = 'ASMAN';
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
}
