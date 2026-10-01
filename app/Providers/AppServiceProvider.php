<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $now = now();
            $todayIndo = $now->isoFormat('dddd, D MMMM Y');
            $weekNumber = $now->isoWeek();
            $weekRange = $now->startOfWeek()->isoFormat('D') . ' - ' . $now->endOfWeek()->isoFormat('D MMM Y');

            $notifications = [];
            if (auth()->check()) {
                $user = auth()->user();
                $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');

                // 1. Issue menunggu keputusan / tindakan
                if ($userRole === 'TL') {
                    $pendingIssues = \App\Models\Issue::where('current_level', 'TL')
                        ->whereIn('status', ['Open', 'Under Review', 'Waiting Decision'])
                        ->latest()->take(3)->get();
                    foreach ($pendingIssues as $pi) {
                        $notifications[] = [
                            'title' => 'Issue Menunggu Tindakan TL',
                            'text' => '#' . $pi->issue_no . ' - ' . \Illuminate\Support\Str::limit($pi->title, 40),
                            'time' => $pi->created_at->diffForHumans(),
                            'icon' => 'fas fa-exclamation-circle text-amber-500',
                            'link' => route('issues.show', $pi->id),
                        ];
                    }
                } elseif ($userRole === 'ASMAN') {
                    $pendingIssues = \App\Models\Issue::where('current_level', 'ASMAN')
                        ->whereIn('status', ['Escalated', 'Under Review', 'Waiting Decision'])
                        ->latest()->take(3)->get();
                    foreach ($pendingIssues as $pi) {
                        $notifications[] = [
                            'title' => 'Eskalasi Isu ke ASMAN',
                            'text' => '#' . $pi->issue_no . ' - ' . \Illuminate\Support\Str::limit($pi->title, 40),
                            'time' => $pi->updated_at->diffForHumans(),
                            'icon' => 'fas fa-arrow-up text-rose-500',
                            'link' => route('issues.show', $pi->id),
                        ];
                    }
                } elseif ($userRole === 'MUP') {
                    $criticalIssues = \App\Models\Issue::where('current_level', 'MUP')
                        ->orWhere('priority', 'CRITICAL')
                        ->latest()->take(3)->get();
                    foreach ($criticalIssues as $ci) {
                        $notifications[] = [
                            'title' => 'Isu Strategis / Level MUP',
                            'text' => '#' . $ci->issue_no . ' - ' . \Illuminate\Support\Str::limit($ci->title, 40),
                            'time' => $ci->updated_at->diffForHumans(),
                            'icon' => 'fas fa-gavel text-purple-500',
                            'link' => route('issues.show', $ci->id),
                        ];
                    }
                }

                // 2. Tugas aktif yang di-assign ke user
                $myActiveTasks = \App\Models\Task::where('assigned_to', $user->id)
                    ->whereNotIn('status', ['Selesai', 'Closed', 'Resolved'])
                    ->latest()->take(2)->get();
                foreach ($myActiveTasks as $mat) {
                    $notifications[] = [
                        'title' => 'Tugas Ditugaskan ke Anda',
                        'text' => \Illuminate\Support\Str::limit($mat->title, 45) . ' (Target: ' . ($mat->due_date ?? '-') . ')',
                        'time' => $mat->created_at->diffForHumans(),
                        'icon' => 'fas fa-clipboard-check text-blue-500',
                        'link' => route('pekerjaan'),
                    ];
                }

                // 3. Rapat mendatang
                $upcomingMeetings = \App\Models\Meeting::where('meeting_date', '>=', now()->toDateString())
                    ->where('status', '!=', 'Selesai')
                    ->orderBy('meeting_date')
                    ->take(2)->get();
                foreach ($upcomingMeetings as $um) {
                    $notifications[] = [
                        'title' => 'Agenda Rapat: ' . $um->type,
                        'text' => 'Tgl ' . \Carbon\Carbon::parse($um->meeting_date)->isoFormat('D MMM') . ' (' . ($um->time_start ?? '08:00') . ' WITA) di ' . ($um->location ?? 'Ruang Rapat'),
                        'time' => \Carbon\Carbon::parse($um->meeting_date)->diffForHumans(),
                        'icon' => 'far fa-calendar-alt text-emerald-500',
                        'link' => route('rapat.show', $um->id),
                    ];
                }
            }

            $view->with([
                'topbarDate' => $todayIndo,
                'topbarWeek' => $weekNumber,
                'topbarWeekRange' => $weekRange,
                'systemNotifications' => $notifications,
            ]);
        });
    }
}
