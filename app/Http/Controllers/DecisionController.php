<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Issue;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DecisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Decision::with(['issue', 'decisionMaker', 'tasks.assignee'])->latest();

        if ($request->filled('level') && $request->level !== 'Semua') {
            $query->where('decision_level', $request->level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('decision', 'like', "%{$search}%")
                  ->orWhere('rationale', 'like', "%{$search}%")
                  ->orWhere('follow_up', 'like', "%{$search}%")
                  ->orWhereHas('issue', function ($iq) use ($search) {
                      $iq->where('issue_no', 'like', "%{$search}%")
                         ->orWhere('title', 'like', "%{$search}%");
                  });
            });
        }

        $decisions = $query->paginate(15)->withQueryString();

        return view('keputusan.index', compact('decisions'));
    }

    public function store(Request $request, $issue_id)
    {
        $request->validate([
            'decision' => 'required|string|min:5',
            'rationale' => 'nullable|string',
            'follow_up' => 'nullable|string',
            'mark_resolved' => 'nullable|boolean',
            'create_task' => 'nullable|boolean',
            'task_assignee_id' => 'nullable|exists:users,id',
            'task_due_date' => 'nullable|date',
            'task_title' => 'nullable|string|max:255',
        ]);

        $issue = Issue::findOrFail($issue_id);
        $userRole = strtoupper(auth()->user()->role->name ?? '');

        // Validasi wewenang jenjang (Lowest Competent Level BR-01, BR-02, BR-04)
        // MUP berwenang memutuskan segala level, sedangkan TL dan ASMAN sesuai jenjang isu
        $isAuthorized = ($userRole === 'MUP' || $userRole === 'ADMIN') || 
                        ($issue->current_level === $userRole);

        if (!in_array($userRole, ['TL', 'ASMAN', 'MUP', 'ADMIN']) || !$isAuthorized) {
            return back()->with('error', "Anda ({$userRole}) tidak berwenang memutuskan isu pada jenjang {$issue->current_level}. Prinsip BSD mewajibkan putusan di Lowest Competent Level.");
        }

        $decision = Decision::create([
            'issue_id' => $issue->id,
            'decision_by' => auth()->id(),
            'decision_level' => in_array($userRole, ['TL', 'ASMAN', 'MUP']) ? $userRole : 'TL',
            'decision' => $request->decision,
            'rationale' => $request->rationale,
            'follow_up' => $request->follow_up,
        ]);

        // Otomatisasi Tindak Lanjut Menjadi Task (BR-07 & AT-06)
        $createdTaskMsg = '';
        if ($request->boolean('create_task') || ($request->filled('task_assignee_id') && ($request->filled('follow_up') || $request->filled('task_title')))) {
            $taskTitle = $request->task_title ?: ('Tindak Lanjut: ' . Str::limit($request->decision, 60));
            $dueDate = $request->task_due_date ?: now()->addDays(3)->toDateString();
            $assigneeId = $request->task_assignee_id ?: auth()->id();

            $task = Task::create([
                'issue_id' => $issue->id,
                'decision_id' => $decision->id,
                'title' => $taskTitle,
                'description' => $request->follow_up ?: $request->decision,
                'assigned_to' => $assigneeId,
                'assigned_by' => auth()->id(),
                'start_date' => now()->toDateString(),
                'due_date' => $dueDate,
                'progress_percent' => 0,
                'status' => 'In Progress',
                'week_number' => now()->isoWeek(),
                'year' => now()->year,
            ]);

            $createdTaskMsg = " dan otomatis dibuatkan Task #{$task->id} untuk PIC pelaksana";
        }

        $newStatus = $request->boolean('mark_resolved') ? 'Resolved' : 'Decided';
        $issue->update([
            'status' => $newStatus,
        ]);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Keputusan berhasil disimpan{$createdTaskMsg}.");
        }

        return redirect()->route('issues.show', $issue->id)->with('success', "Keputusan atasan berhasil dicatat{$createdTaskMsg}.");
    }
}
