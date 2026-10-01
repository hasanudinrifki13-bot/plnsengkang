<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Escalation;
use App\Models\Information;
use App\Models\Issue;
use App\Models\OrganizationUnit;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InfoReportController extends Controller
{
    public function informasi(Request $request)
    {
        $user = auth()->user();
        $userRole = strtoupper($user->role->name ?? 'STAFF');

        $query = Information::with(['publisher', 'targetUnit'])->where('is_active', true);

        // Filter kategori
        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            $query->where('category', strtoupper($request->kategori));
        }

        // Filter target level
        if ($request->filled('target_level') && $request->target_level !== 'Semua') {
            $query->where('target_level', strtoupper($request->target_level));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $informations = $query->latest('published_at')->paginate(12)->withQueryString();
        $units = OrganizationUnit::all();

        return view('informasi.index', compact('informations', 'units', 'userRole'));
    }

    public function storeInformasi(Request $request)
    {
        $user = auth()->user();
        $userRole = strtoupper($user->role->name ?? 'STAFF');

        if (!in_array($userRole, ['TL', 'ASMAN', 'MUP', 'ADMIN'])) {
            return back()->with('error', 'Hanya Team Leader, Asisten Manajer, atau Manager Unit yang berwenang menerbitkan arahan/informasi.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:ARAHAN,KEBIJAKAN,PENGUMUMAN,REGULASI',
            'target_level' => 'required|in:ALL,MUP,ASMAN,TL,STAFF',
            'target_unit_id' => 'nullable|exists:organization_units,id',
            'expires_at' => 'nullable|date',
        ]);

        Information::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'target_level' => $validated['target_level'],
            'target_unit_id' => $validated['target_unit_id'] ?? null,
            'publisher_id' => $user->id,
            'published_at' => now(),
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('informasi.index')->with('success', 'Arahan / Informasi baru berhasil dipublikasikan secara berjenjang.');
    }

    public function destroyInformasi($id)
    {
        $info = Information::findOrFail($id);
        $user = auth()->user();
        $userRole = strtoupper($user->role->name ?? 'STAFF');

        // Only publisher or MUP/Admin can delete
        if ($info->publisher_id !== $user->id && !in_array($userRole, ['MUP', 'ADMIN'])) {
            return back()->with('error', 'Anda tidak berwenang menghapus informasi ini.');
        }

        $info->delete();

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil dihapus.');
    }

    public function laporan(Request $request)
    {
        $periode = $request->get('periode', 'bulan_ini');
        $unitFilter = $request->get('unit_id');

        // Query Tasks
        $taskQuery = Task::with(['assignee', 'issue']);
        $issueQuery = Issue::with(['reporter', 'owner', 'decisions']);
        $escalationQuery = Escalation::with(['issue', 'fromUser', 'toUser'])->latest();
        $decisionQuery = Decision::with(['issue', 'decisionMaker'])->latest();

        // Periode filtering
        if ($periode === 'minggu_ini') {
            $taskQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            $issueQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'minggu_lalu') {
            $taskQuery->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            $issueQuery->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
        } elseif ($periode === 'bulan_ini') {
            $taskQuery->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            $issueQuery->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        $totalIssues = (clone $issueQuery)->count();
        $openIssues = (clone $issueQuery)->whereIn('status', ['Open', 'Under Review', 'Waiting Decision'])->count();
        $escalatedIssues = (clone $issueQuery)->where('status', 'Escalated')->count();
        $resolvedIssues = (clone $issueQuery)->whereIn('status', ['Resolved', 'Decided', 'Closed'])->count();

        // Lowest Competent Level resolution counts
        $resolvedByTL = Decision::where('decision_level', 'TL')->count();
        $resolvedByAsman = Decision::where('decision_level', 'ASMAN')->count();
        $resolvedByMUP = Decision::where('decision_level', 'MUP')->count();
        $totalDecisions = $resolvedByTL + $resolvedByAsman + $resolvedByMUP;

        // Resolution at lowest level percentage (TL resolution rate)
        $tlResolutionRate = $totalDecisions > 0 ? round(($resolvedByTL / $totalDecisions) * 100, 1) : 0;

        // Task metrics
        $totalTasks = (clone $taskQuery)->count();
        $completedTasks = (clone $taskQuery)->whereIn('status', ['Selesai', 'Closed', 'Resolved'])->count();
        $inProgressTasks = (clone $taskQuery)->whereIn('status', ['In Progress', 'On Progress', 'Assigned'])->count();
        $overdueTasks = (clone $taskQuery)->where('due_date', '<', now()->toDateString())
            ->whereNotIn('status', ['Selesai', 'Closed', 'Resolved'])->count();

        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        $recentDecisions = $decisionQuery->take(10)->get();
        $recentEscalations = $escalationQuery->take(10)->get();
        $tasks = $taskQuery->take(15)->get();
        $units = OrganizationUnit::all();

        return view('laporan.index', compact(
            'periode',
            'totalIssues',
            'openIssues',
            'escalatedIssues',
            'resolvedIssues',
            'resolvedByTL',
            'resolvedByAsman',
            'resolvedByMUP',
            'totalDecisions',
            'tlResolutionRate',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'overdueTasks',
            'taskCompletionRate',
            'recentDecisions',
            'recentEscalations',
            'tasks',
            'units'
        ));
    }

    public function exportCsv(Request $request)
    {
        $tasks = Task::with(['assignee', 'issue'])->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Laporan_Koordinasi_UP_Sengkang_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, [
                'ID Task',
                'Terkait Issue',
                'Nama Pekerjaan',
                'PIC / Assignee',
                'Tanggal Mulai',
                'Target Selesai',
                'Progres (%)',
                'Status',
                'Status Deadline'
            ]);

            foreach ($tasks as $task) {
                $isOverdue = $task->due_date && $task->due_date < now()->toDateString() && !in_array($task->status, ['Selesai', 'Closed', 'Resolved']);
                $deadlineStatus = $isOverdue ? 'OVERDUE' : 'ON TRACK';

                fputcsv($file, [
                    $task->id,
                    $task->issue ? $task->issue->issue_no : '-',
                    $task->title,
                    $task->assignee ? $task->assignee->name : 'Belum Ditugaskan',
                    $task->start_date ?? '-',
                    $task->due_date ?? '-',
                    $task->progress_percent . '%',
                    $task->status,
                    $deadlineStatus
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
