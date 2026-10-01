<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Issue;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $roleName = strtoupper(optional($user->role)->name ?? 'TL');
        $currentWeek = now()->isoWeek();
        $siklus = $request->get('siklus', 'semua');

        // Filter siklus mingguan (BR-09 ISO-8601)
        $taskQuery = Task::query();
        $issueQuery = Issue::query();

        if ($siklus === 'N') {
            $taskQuery->where(function ($q) use ($currentWeek) {
                $q->where('week_number', $currentWeek)
                  ->orWhereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            });
            $issueQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($siklus === 'N-1') {
            $prevWeek = now()->subWeek()->isoWeek();
            $taskQuery->where(function ($q) use ($prevWeek) {
                $q->where('week_number', $prevWeek)
                  ->orWhereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            });
            $issueQuery->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
        }

        $today = now()->toDateString();
        $threeDaysLater = now()->addDays(3)->toDateString();

        // =========================================================================
        // TAHAP 1: TASK BACKLOG (WEEKLY CONTINUOUS CYCLE - BSD BUSINESS RULES)
        // Kueri Overdue/Tunggakan TIDAK difilter oleh minggu berjalan ($siklus).
        // Tugas yang belum selesai dari minggu lalu (misal W38) tetap masuk sebagai Overdue.
        // =========================================================================
        $cumulativeOverdueQuery = Task::where(function ($q) use ($today) {
            $q->where(function ($sub) use ($today) {
                $sub->whereNotIn('status', ['Resolved', 'Closed', 'Selesai'])
                    ->where('due_date', '<', $today);
            })->orWhere('status', 'Terlambat')
              ->orWhere('status', 'Overdue');
        })->where(function ($q) {
            $q->whereNull('progress_percent')
              ->orWhere('progress_percent', '<', 100);
        });

        // Scope berdasarkan hak akses role user yang login
        if ($roleName === 'STAFF') {
            $cumulativeOverdueQuery->where('assigned_to', $user->id);
        } elseif ($roleName === 'TL' && $user->unit_id) {
            $cumulativeOverdueQuery->where(function ($q) use ($user) {
                $q->whereHas('assignee', fn($sq) => $sq->where('unit_id', $user->unit_id))
                  ->orWhere('assigned_to', $user->id);
            });
        }
        // ASMAN & MUP melihat tunggakan seluruh unit

        $overdueTasks = (clone $cumulativeOverdueQuery)->count();

        $totalTasks = (clone $taskQuery)->count();
        $onProgressTasks = (clone $taskQuery)->whereIn('status', ['In Progress', 'On Progress'])->count();
        $completedTasks = (clone $taskQuery)->whereIn('status', ['Resolved', 'Closed', 'Selesai'])->count();

        // Issue Saya
        $myIssuesCount = (clone $issueQuery)->where(function ($q) use ($user) {
            $q->where('reported_by', $user->id)
              ->orWhere('owner_id', $user->id);
        })->count();

        // Data Prioritas Issue
        $highIssues = (clone $issueQuery)->whereIn('priority', ['HIGH', 'CRITICAL'])->count();
        $mediumIssues = (clone $issueQuery)->where('priority', 'MEDIUM')->count();
        $lowIssues = (clone $issueQuery)->where('priority', 'LOW')->count();

        // Paket data KPI untuk Chart.js
        $kpiData = [
            'total_pekerjaan' => max($totalTasks, $completedTasks + $onProgressTasks + $overdueTasks),
            'on_progress' => $onProgressTasks,
            'selesai' => $completedTasks,
            'terlambat' => $overdueTasks,
            'issue_saya' => $myIssuesCount,
            'chart_status' => [
                'selesai' => $completedTasks,
                'on_progress' => $onProgressTasks,
                'terlambat' => $overdueTasks,
            ],
            'chart_priority' => [
                'high' => $highIssues,
                'medium' => $mediumIssues,
                'low' => $lowIssues,
            ]
        ];

        // ==================== DATA MONITORING MANAJEMEN TUGAS (PERSIS GAMBAR 2) ====================
        $totalPembahasan = (clone $issueQuery)->count() + (clone $taskQuery)->count();
        $totalOpen = (clone $taskQuery)->where('status', 'Open')->count() + (clone $issueQuery)->where('status', 'Open')->count();
        $totalOnProgressTasks = (clone $taskQuery)->whereIn('status', ['In Progress', 'On Progress'])->count();
        $totalClosedTasks = (clone $taskQuery)->whereIn('status', ['Resolved', 'Closed', 'Selesai'])->count();
        $totalOverdue = $overdueTasks;

        $manajemenTugas = [
            'siklus_aktif' => $siklus,
            'minggu_berjalan' => $currentWeek,
            'kpi' => [
                'pembahasan' => $totalPembahasan,
                'open' => $totalOpen,
                'on_progress' => $totalOnProgressTasks,
                'overdue' => $totalOverdue,
            ],
            'status_pekerjaan' => [
                'open' => (clone $taskQuery)->where('status', 'Open')->count(),
                'in_progress' => (clone $taskQuery)->whereIn('status', ['In Progress', 'On Progress'])->count(),
                'closed' => $totalClosedTasks,
            ],
            'status_eskalasi' => [
                'closed_info' => (clone $issueQuery)->where('status', 'Closed')->orWhere('issue_type', 'INFORMATION')->count(),
                'escalated_level1' => (clone $issueQuery)->whereIn('current_level', ['TL', 'ASMAN'])->count(),
                'escalated_mup' => (clone $issueQuery)->where('current_level', 'MUP')->count(),
            ],
            'deadline' => [
                'overdue' => $totalOverdue,
                'due_3_days' => (clone $taskQuery)->whereNotIn('status', ['Selesai', 'Closed', 'Resolved'])->whereBetween('due_date', [$today, $threeDaysLater])->count(),
                'on_track' => (clone $taskQuery)->whereNotIn('status', ['Selesai', 'Closed', 'Resolved'])->where('due_date', '>', $threeDaysLater)->count(),
                'closed' => $totalClosedTasks,
            ],
            'monitoring_pic' => Task::with('assignee')->whereNotIn('status', ['Selesai', 'Closed', 'Resolved'])->latest()->take(6)->get(),
            'prioritas_alert' => Issue::with(['owner', 'reporter', 'decisions', 'tasks'])->latest()->take(8)->get(),
        ];

        // Isu yang berada di level TL
        $issuesUnderReview = Issue::where('current_level', 'TL')
            ->with(['reporter', 'owner'])
            ->latest()
            ->take(10)
            ->get();

        // Pekerjaan Tim
        $tasks = Task::with(['assignee', 'issue'])
            ->latest()
            ->take(10)
            ->get();

        switch ($roleName) {
            case 'STAFF':
                $myTasks = Task::with('issue')->where('assigned_to', $user->id)->get();
                $openCount = $myTasks->where('status', 'Open')->count();
                $inProgressCount = $myTasks->whereIn('status', ['In Progress', 'On Progress'])->count();
                $overdueCount = $myTasks->filter(fn($t) => $t->calculated_status === 'Terlambat')->count();
                $completedCount = $myTasks->filter(fn($t) => $t->calculated_status === 'Selesai')->count();
                $closedCount = $completedCount;
                return view('dashboard.staff', compact('kpiData', 'manajemenTugas', 'myTasks', 'tasks', 'openCount', 'inProgressCount', 'overdueCount', 'completedCount', 'closedCount'));

            case 'TL':
                return view('dashboard.tl', compact('kpiData', 'manajemenTugas', 'issuesUnderReview', 'tasks'));

            case 'ASMAN':
                $escalatedIssues = Issue::where('current_level', 'ASMAN')->with(['reporter', 'escalations.fromUser'])->latest()->get();
                $pendingCount = $escalatedIssues->count();
                return view('dashboard.asman', compact('kpiData', 'manajemenTugas', 'escalatedIssues', 'pendingCount', 'tasks'));

            case 'MUP':
                $criticalIssues = Issue::where('priority', 'CRITICAL')->orWhere('current_level', 'MUP')->with(['reporter', 'owner'])->latest()->get();
                $totalIssues = Issue::count();
                $resolvedIssues = Issue::whereIn('status', ['Resolved', 'Closed'])->count();
                return view('dashboard.mup', compact('kpiData', 'manajemenTugas', 'criticalIssues', 'totalIssues', 'resolvedIssues', 'tasks'));

            default:
                return view('dashboard.tl', compact('kpiData', 'manajemenTugas', 'issuesUnderReview', 'tasks'));
        }
    }
}