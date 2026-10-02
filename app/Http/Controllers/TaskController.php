<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Tampilkan daftar pekerjaan sesuai hak akses Role (Staff vs TL vs Asman/MUP).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');

        $query = Task::with(['assignee', 'issue', 'assigner'])->latest();

        // 1. Role-based scoping (Aturan Bisnis Sengkang Alignment)
        if ($userRole === 'STAFF') {
            // Staff hanya melihat task yang di-assign ke dirinya
            $query->where('assigned_to', $user->id);
        } elseif ($userRole === 'TL') {
            // TL melihat task milik semua staf yang berada di unit / seksinya (tidak bisa melihat antar TL lain)
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $query->where(function ($q) use ($user, $authorizedUnitIds) {
                $q->whereIn('unit_id', $authorizedUnitIds)
                  ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $authorizedUnitIds))
                  ->orWhere('assigned_to', $user->id)
                  ->orWhere('assigned_by', $user->id);
            });
        } elseif ($userRole === 'ASMAN') {
            // Asman melihat task milik seluruh unit/seksi di bawah bidangnya (tidak bisa melihat antar Asman lain)
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $query->where(function ($q) use ($user, $authorizedUnitIds) {
                $q->whereIn('unit_id', $authorizedUnitIds)
                  ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $authorizedUnitIds))
                  ->orWhere('assigned_to', $user->id)
                  ->orWhere('assigned_by', $user->id);
            });
        }
        // Catatan: Role MUP & ADMIN dapat melihat seluruh task unit secara komprehensif

        // 2. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Filter Pencarian Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('issue', fn($iq) => $iq->where('issue_no', 'like', "%{$search}%"));
            });
        }

        // 4. Filter Siklus Mingguan (ISO-8601 BR-09)
        $currentWeek = now()->isoWeek();
        if ($request->filled('siklus') && $request->siklus !== 'semua') {
            if ($request->siklus === 'N') {
                $query->where(function ($q) use ($currentWeek) {
                    $q->where('week_number', $currentWeek)
                      ->orWhereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                });
            } elseif ($request->siklus === 'N-1') {
                $prevWeek = now()->subWeek()->isoWeek();
                $query->where(function ($q) use ($prevWeek) {
                    $q->where('week_number', $prevWeek)
                      ->orWhereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                });
            }
        }

        $tasks = $query->paginate(15)->withQueryString();

        // Data Karyawan untuk penugasan PIC (hanya bawahan langsung / unit kerja sendiri untuk menjaga integritas data)
        $subordinateIds = $user->getSubordinateUserIds();
        if (in_array($userRole, ['MUP', 'ADMIN'])) {
            $usersQuery = User::where('is_active', true);
        } else {
            $usersQuery = User::whereIn('id', $subordinateIds)->where('is_active', true);
        }

        $users = (clone $usersQuery)->orderBy('name')->get();
        if ($users->isEmpty()) {
            $users = User::where('id', $user->id)->get();
        }

        $staffUsers = (clone $usersQuery)->whereHas('role', fn($q) => $q->where('name', 'STAFF'))->orderBy('name')->get();
        if ($staffUsers->isEmpty()) {
            $staffUsers = $users;
        }

        // Data Isu Aktif terfilter sesuai unit otoritas & jenjang eskalasi
        $authorizedUnitIds = $user->getAuthorizedUnitIds();
        $issuesQuery = Issue::whereNotIn('status', ['Resolved', 'Closed'])->latest();
        if ($userRole === 'STAFF') {
            $issuesQuery->where('reported_by', $user->id);
        } elseif ($userRole === 'TL') {
            // TL: isu milik staf seksinya (level TL)
            $issuesQuery->whereIn('unit_id', $authorizedUnitIds);
        } elseif ($userRole === 'ASMAN') {
            // ASMAN: hanya isu yang dibawa oleh TL ke level ASMAN
            $subordinateIds = $user->getSubordinateUserIds();
            $issuesQuery->whereIn('unit_id', $authorizedUnitIds)
                ->where(function ($q) use ($user, $subordinateIds) {
                    $q->whereIn('current_level', ['ASMAN', 'MUP'])
                      ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'))
                      ->orWhere('reported_by', $user->id);
                });
        } elseif ($userRole === 'MUP') {
            // MUP: hanya isu yang dibawa oleh Asman ke level MUP
            $issuesQuery->where(function ($q) use ($user) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'))
                  ->orWhere('reported_by', $user->id);
            });
        }
        $issues = $issuesQuery->get();

        return \Inertia\Inertia::render('Tasks/Index', compact('tasks', 'users', 'staffUsers', 'issues', 'currentWeek'));
    }

    /**
     * Simpan pekerjaan baru (Bisa standalone maupun terhubung ke issue_id).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'issue_id' => 'nullable|exists:issues,id',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|string',
            'completion_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
            'completion_notes' => 'nullable|string',
        ]);

        $progress = (int) ($validated['progress_percent'] ?? 0);
        $status = $validated['status'] ?? ($progress >= 100 ? 'Selesai' : 'In Progress');

        $dueDate = $validated['due_date'];
        $weekNumber = Carbon::parse($dueDate)->isoWeek();
        $year = Carbon::parse($dueDate)->year;

        $attachmentPath = null;
        if ($request->hasFile('completion_attachment')) {
            $attachmentPath = $request->file('completion_attachment')->store('tasks/proofs', 'public');
            try {
                $targetDir = public_path('storage/tasks/proofs');
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                @copy(storage_path('app/public/' . $attachmentPath), public_path('storage/' . $attachmentPath));
            } catch (\Throwable $e) {}
        }

        $assignee = User::find($validated['assigned_to']);
        $unitId = $assignee?->unit_id ?? auth()->user()->unit_id;

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => auth()->id(),
            'unit_id' => $unitId,
            'issue_id' => $validated['issue_id'] ?? null,
            'start_date' => $validated['start_date'] ?? now()->toDateString(),
            'due_date' => $dueDate,
            'progress_percent' => $progress,
            'status' => $status,
            'completion_attachment' => $attachmentPath,
            'completion_notes' => $validated['completion_notes'] ?? null,
            'week_number' => $weekNumber,
            'year' => $year,
        ]);

        $msg = $task->issue_id ? "Task tindak lanjut untuk Eskalasi #{$task->issue->issue_no} berhasil ditambahkan." : "Task baru berhasil ditambahkan.";

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', $msg);
        }

        return back()->with('success', $msg);
    }

    /**
     * Update progress pekerjaan secara spesifik (0-100%) beserta upload bukti fisik opsional/wajib saat 100%.
     */
    public function updateProgress(Request $request, Task $task)
    {
        $validated = $request->validate([
            'progress_percent' => 'required|integer|min:0|max:100',
            'completion_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
            'completion_notes' => 'nullable|string',
        ]);

        $progress = (int) $validated['progress_percent'];

        // Jika 100%, otomatis set status menjadi Selesai / Closed
        $status = $progress >= 100 ? 'Selesai' : ($task->status === 'Open' ? 'In Progress' : $task->status);

        $updateData = [
            'progress_percent' => $progress,
            'status' => $status,
        ];

        if ($request->hasFile('completion_attachment')) {
            if ($task->completion_attachment) {
                if (Storage::disk('public')->exists($task->completion_attachment)) {
                    Storage::disk('public')->delete($task->completion_attachment);
                }
                if (file_exists(public_path('storage/' . $task->completion_attachment))) {
                    @unlink(public_path('storage/' . $task->completion_attachment));
                }
            }
            $attachmentPath = $request->file('completion_attachment')->store('tasks/proofs', 'public');
            $updateData['completion_attachment'] = $attachmentPath;
            try {
                $targetDir = public_path('storage/tasks/proofs');
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                @copy(storage_path('app/public/' . $attachmentPath), public_path('storage/' . $attachmentPath));
            } catch (\Throwable $e) {}
        }

        if ($request->filled('completion_notes')) {
            $updateData['completion_notes'] = $request->completion_notes;
        }

        $task->update($updateData);

        $extraMsg = $request->hasFile('completion_attachment') ? " dan bukti penyelesaian berhasil diunggah." : ".";
        return back()->with('success', "Progres task '{$task->title}' berhasil diperbarui menjadi {$progress}%{$extraMsg}");
    }

    /**
     * Update data pekerjaan lengkap (Modal Edit).
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'progress_percent' => 'required|integer|min:0|max:100',
            'issue_id' => 'nullable|exists:issues,id',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'completion_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
            'completion_notes' => 'nullable|string',
        ]);

        $status = $validated['status'];
        if ((int) $validated['progress_percent'] >= 100) {
            $status = 'Selesai';
        }

        $assignee = User::find($validated['assigned_to']);

        $updateData = [
            'title' => $validated['title'],
            'assigned_to' => $validated['assigned_to'],
            'unit_id' => $assignee?->unit_id ?? $task->unit_id,
            'start_date' => $validated['start_date'] ?? $task->start_date,
            'due_date' => $validated['due_date'],
            'progress_percent' => $validated['progress_percent'],
            'issue_id' => $validated['issue_id'] ?? null,
            'status' => $status,
            'description' => $validated['description'] ?? $task->description,
        ];

        if ($request->hasFile('completion_attachment')) {
            if ($task->completion_attachment) {
                if (Storage::disk('public')->exists($task->completion_attachment)) {
                    Storage::disk('public')->delete($task->completion_attachment);
                }
                if (file_exists(public_path('storage/' . $task->completion_attachment))) {
                    @unlink(public_path('storage/' . $task->completion_attachment));
                }
            }
            $attachmentPath = $request->file('completion_attachment')->store('tasks/proofs', 'public');
            $updateData['completion_attachment'] = $attachmentPath;
            try {
                $targetDir = public_path('storage/tasks/proofs');
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                @copy(storage_path('app/public/' . $attachmentPath), public_path('storage/' . $attachmentPath));
            } catch (\Throwable $e) {}
        }

        if ($request->filled('completion_notes')) {
            $updateData['completion_notes'] = $request->completion_notes;
        }

        $task->update($updateData);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Data task '{$task->title}' berhasil diperbarui.");
        }

        return back()->with('success', "Data task '{$task->title}' berhasil diperbarui.");
    }

    /**
     * Hapus data pekerjaan.
     */
    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $title = $task->title;
        $task->delete();

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Pekerjaan '{$title}' berhasil dihapus.");
        }

        return back()->with('success', "Pekerjaan '{$title}' berhasil dihapus.");
    }
}
