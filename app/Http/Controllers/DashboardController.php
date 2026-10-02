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
        $roleName = strtoupper(optional($user->role)->name ?? 'STAFF');
        $authorizedUnitIds = $user->getAuthorizedUnitIds();
        $currentWeek = now()->isoWeek();
        $siklus = $request->get('siklus', 'semua');

        // Filter siklus mingguan (BR-09 ISO-8601)
        $taskQuery = Task::query();
        $issueQuery = Issue::query();

        // 1. Role-based scoping (Isolasi Unit / Seksi)
        if ($roleName === 'STAFF') {
            $taskQuery->where('assigned_to', $user->id);
            $issueQuery->where('reported_by', $user->id);
        } elseif ($roleName === 'TL') {
            $taskQuery->where(function ($q) use ($authorizedUnitIds, $user) {
                $q->whereIn('unit_id', $authorizedUnitIds)
                  ->orWhere('assigned_to', $user->id);
            });
            $issueQuery->whereIn('unit_id', $authorizedUnitIds);
        } elseif ($roleName === 'ASMAN') {
            $taskQuery->whereIn('unit_id', $authorizedUnitIds);
            // Asman HANYA melihat apa yang DIBAWA oleh TL-nya (sudah dieskalasikan ke ASMAN/MUP atau dilaporkan oleh TL/Asman)
            $subordinateIds = $user->getSubordinateUserIds();
            $issueQuery->whereIn('unit_id', $authorizedUnitIds)
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
            // MUP HANYA melihat apa yang DIBAWA oleh ASMAN-nya (sudah dieskalasikan ke MUP atau dilaporkan oleh Asman/MUP)
            $issueQuery->where(function ($q) use ($user) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'))
                  ->orWhere('reported_by', $user->id)
                  ->orWhereHas('reporter.role', fn($rq) => $rq->whereIn('name', ['ASMAN', 'MUP']));
            });
        }
        // Role ADMIN melihat seluruh data UP Sengkang

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
        } elseif ($roleName === 'TL') {
            $cumulativeOverdueQuery->where(function ($q) use ($authorizedUnitIds, $user) {
                $q->whereIn('unit_id', $authorizedUnitIds)
                  ->orWhere('assigned_to', $user->id);
            });
        } elseif ($roleName === 'ASMAN') {
            $cumulativeOverdueQuery->whereIn('unit_id', $authorizedUnitIds);
        }
        // MUP melihat tunggakan seluruh unit

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

        // ==================== DATA MONITORING MANAJEMEN TUGAS ====================
        $totalPembahasan = (clone $issueQuery)->count() + (clone $taskQuery)->count();
        $totalOpen = (clone $taskQuery)->where('status', 'Open')->count() + (clone $issueQuery)->where('status', 'Open')->count();
        $totalOnProgressTasks = (clone $taskQuery)->whereIn('status', ['In Progress', 'On Progress'])->count();
        $totalClosedTasks = (clone $taskQuery)->whereIn('status', ['Resolved', 'Closed', 'Selesai'])->count();
        $totalOverdue = $overdueTasks;

        // Scoped monitoring queries
        $scopedMonitoringPic = Task::with('assignee')->whereNotIn('status', ['Selesai', 'Closed', 'Resolved']);
        $scopedPrioritasAlert = Issue::with(['owner', 'reporter', 'decisions', 'tasks']);

        if ($roleName === 'STAFF') {
            $scopedMonitoringPic->where('assigned_to', $user->id);
            $scopedPrioritasAlert->where('reported_by', $user->id);
        } elseif ($roleName === 'TL') {
            $scopedMonitoringPic->where(fn($q) => $q->whereIn('unit_id', $authorizedUnitIds)->orWhere('assigned_to', $user->id));
            $scopedPrioritasAlert->whereIn('unit_id', $authorizedUnitIds);
        } elseif ($roleName === 'ASMAN') {
            $scopedMonitoringPic->whereIn('unit_id', $authorizedUnitIds);
            $subordinateIds = $user->getSubordinateUserIds();
            $scopedPrioritasAlert->whereIn('unit_id', $authorizedUnitIds)
                ->where(function ($q) use ($user, $subordinateIds) {
                    $q->whereIn('current_level', ['ASMAN', 'MUP'])
                      ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'))
                      ->orWhere('reported_by', $user->id);
                });
        } elseif ($roleName === 'MUP') {
            $scopedPrioritasAlert->where(function ($q) use ($user) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'))
                  ->orWhere('reported_by', $user->id);
            });
        }

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
            'monitoring_pic' => $scopedMonitoringPic->latest()->take(6)->get(),
            'prioritas_alert' => $scopedPrioritasAlert->latest()->take(8)->get(),
        ];

        // Isu yang berada di level review sesuai jenjang wewenang & alur eskalasi
        $issuesUnderReviewQuery = Issue::with(['reporter', 'owner', 'organizationUnit'])->latest();
        if ($roleName === 'STAFF') {
            $issuesUnderReviewQuery->where('reported_by', $user->id);
        } elseif ($roleName === 'TL') {
            $issuesUnderReviewQuery->whereIn('unit_id', $authorizedUnitIds)->where('current_level', 'TL');
        } elseif ($roleName === 'ASMAN') {
            $issuesUnderReviewQuery->whereIn('unit_id', $authorizedUnitIds)->where('current_level', 'ASMAN');
        } elseif ($roleName === 'MUP') {
            $issuesUnderReviewQuery->where('current_level', 'MUP');
        } else {
            $issuesUnderReviewQuery->whereIn('current_level', ['TL', 'ASMAN', 'MUP']);
        }
        $issuesUnderReview = $issuesUnderReviewQuery->take(10)->get();

        // Pekerjaan Tim Terkini (Scoped)
        $tasksScopedQuery = Task::with(['assignee', 'issue'])->latest();
        if ($roleName === 'STAFF') {
            $tasksScopedQuery->where('assigned_to', $user->id);
        } elseif ($roleName === 'TL') {
            $tasksScopedQuery->where(fn($q) => $q->whereIn('unit_id', $authorizedUnitIds)->orWhere('assigned_to', $user->id));
        } elseif ($roleName === 'ASMAN') {
            $tasksScopedQuery->whereIn('unit_id', $authorizedUnitIds);
        }
        $tasks = $tasksScopedQuery->take(10)->get();

        $myTasks = null;
        $myTaskStats = null;
        $escalatedIssues = null;
        $criticalIssues = null;

        if ($roleName === 'STAFF') {
            $myTasks = Task::with('issue')->where('assigned_to', $user->id)->get();
            $myTaskStats = [
                'open' => $myTasks->where('status', 'Open')->count(),
                'in_progress' => $myTasks->whereIn('status', ['In Progress', 'On Progress'])->count(),
                'overdue' => $myTasks->filter(fn($t) => $t->calculated_status === 'Terlambat')->count(),
                'completed' => $myTasks->filter(fn($t) => $t->calculated_status === 'Selesai')->count(),
            ];
        } elseif ($roleName === 'ASMAN') {
            $escalatedIssues = Issue::whereIn('unit_id', $authorizedUnitIds)
                ->where('current_level', 'ASMAN')
                ->with(['reporter', 'escalations.fromUser'])
                ->latest()
                ->get();
        } elseif ($roleName === 'MUP') {
            $criticalIssues = Issue::where('current_level', 'MUP')
                ->with(['reporter', 'owner', 'escalations.fromUser'])->latest()->get();
        }

        return \Inertia\Inertia::render('Dashboard/Index', [
            'roleName' => $roleName,
            'kpiData' => $kpiData,
            'manajemenTugas' => $manajemenTugas,
            'tasks' => $tasks,
            'issuesUnderReview' => $issuesUnderReview,
            'escalatedIssues' => $escalatedIssues,
            'criticalIssues' => $criticalIssues,
            'myTasks' => $myTasks,
            'myTaskStats' => $myTaskStats,
        ]);
    }
}