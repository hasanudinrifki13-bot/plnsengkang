<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Information;
use App\Models\Issue;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');

        $tab = $request->get('tab', 'jadwal');
        $query = Meeting::with('leader')->latest('meeting_date');

        if ($tab === 'riwayat') {
            $query->where('status', 'Selesai');
        } else {
            $query->where('status', '!=', 'Selesai');
        }

        if ($request->filled('type') && $request->type !== 'semua') {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        // Scoping visibilitas rapat berdasarkan role & hierarki
        if ($userRole === 'STAFF') {
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $query->where(function($q) use ($user, $authorizedUnitIds) {
                $q->where('leader_id', $user->id)
                  ->orWhereHas('leader', fn($lq) => $lq->whereIn('unit_id', $authorizedUnitIds))
                  ->orWhere('type', 'like', '%Staf%');
            });
        } elseif ($userRole === 'TL') {
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $query->where(function($q) use ($user, $authorizedUnitIds) {
                $q->where('leader_id', $user->id)
                  ->orWhereHas('leader', fn($lq) => $lq->whereIn('unit_id', $authorizedUnitIds))
                  ->orWhere('type', 'like', '%TL%')
                  ->orWhere('type', 'like', '%Staf%');
            });
        } elseif ($userRole === 'ASMAN') {
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $query->where(function($q) use ($user, $authorizedUnitIds) {
                $q->where('leader_id', $user->id)
                  ->orWhereHas('leader', fn($lq) => $lq->whereIn('unit_id', $authorizedUnitIds))
                  ->orWhere('type', 'like', '%Asman%')
                  ->orWhere('type', 'like', '%Manajemen%')
                  ->orWhere('type', 'like', '%RTM%');
            });
        }

        $meetings = $query->get();

        // Jika belum ada meeting di database sama sekali, siapkan default sesuai alur BSD
        if ($meetings->isEmpty() && Meeting::count() === 0) {
            $tl = User::whereHas('role', fn($q) => $q->where('name', 'TL'))->first();
            $asman = User::whereHas('role', fn($q) => $q->where('name', 'ASMAN'))->first();
            $mup = User::whereHas('role', fn($q) => $q->where('name', 'MUP'))->first();

            Meeting::create([
                'meeting_date' => now()->startOfWeek()->toDateString(),
                'time_start' => '08:00',
                'time_end' => '10:00',
                'location' => 'Ruang Rapat Operasi & Keandalan',
                'type' => 'Rapat TL + Staf',
                'status' => 'Berlangsung',
                'leader_id' => $tl?->id,
                'week_number' => now()->isoWeek(),
                'year' => now()->year,
            ]);
            Meeting::create([
                'meeting_date' => now()->startOfWeek()->toDateString(),
                'time_start' => '13:00',
                'time_end' => '15:00',
                'location' => 'Ruang Rapat Utama',
                'type' => 'Rapat Asman + TL',
                'status' => 'Akan Datang',
                'leader_id' => $asman?->id,
                'week_number' => now()->isoWeek(),
                'year' => now()->year,
            ]);
            Meeting::create([
                'meeting_date' => now()->startOfWeek()->addDay()->toDateString(),
                'time_start' => '08:00',
                'time_end' => '10:00',
                'location' => 'Ruang Rapat VIP Pimpinan',
                'type' => 'Rapat Tinjauan Manajemen',
                'status' => 'Akan Datang',
                'leader_id' => $mup?->id,
                'week_number' => now()->isoWeek(),
                'year' => now()->year,
            ]);
            $meetings = Meeting::with('leader')->where('status', '!=', 'Selesai')->latest('meeting_date')->get();
        }

        $subordinateIds = $user->getSubordinateUserIds();
        if (in_array($userRole, ['MUP', 'ADMIN'])) {
            $leaders = User::where('is_active', true)->orderBy('name')->get();
        } else {
            $leaders = User::whereIn('id', array_merge([$user->id], $subordinateIds))->where('is_active', true)->orderBy('name')->get();
            if ($leaders->isEmpty()) {
                $leaders = User::where('id', $user->id)->get();
            }
        }

        $authorizedUnitIds = $user->getAuthorizedUnitIds();
        $issueQuery = Issue::whereNotIn('status', ['Resolved', 'Closed'])->latest();
        if ($userRole === 'STAFF') {
            $issueQuery->where('reported_by', $user->id);
        } elseif ($userRole === 'TL') {
            // TL: isu milik staf seksinya (level TL)
            $issueQuery->whereIn('unit_id', $authorizedUnitIds)->where('current_level', 'TL');
        } elseif ($userRole === 'ASMAN') {
            // ASMAN: hanya isu yang dibawa oleh TL ke level ASMAN
            $subordinateIds = $user->getSubordinateUserIds();
            $issueQuery->whereIn('unit_id', $authorizedUnitIds)
                ->where(function ($q) use ($user, $subordinateIds) {
                    $q->whereIn('current_level', ['ASMAN', 'MUP'])
                      ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'))
                      ->orWhere('reported_by', $user->id);
                });
        } elseif ($userRole === 'MUP') {
            // MUP: hanya isu yang dibawa oleh Asman ke level MUP
            $issueQuery->where(function ($q) use ($user) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'))
                  ->orWhere('reported_by', $user->id);
            });
        }
        $availableIssues = $issueQuery->get();

        return Inertia::render('Meetings/Index', [
            'meetings' => $meetings,
            'leaders' => $leaders,
            'availableIssues' => $availableIssues,
            'tab' => $tab,
        ]);
    }

    public function show($id)
    {
        $meeting = Meeting::with(['leader', 'issues'])->find($id);
        if (!$meeting) {
            $meeting = Meeting::first() ?? (object)[
                'id' => $id,
                'type' => 'Rapat TL + Staf',
                'meeting_date' => now()->toDateString(),
                'time_start' => '08:00',
                'time_end' => '10:00',
                'location' => 'Ruang Operasi',
                'status' => 'Akan Datang',
                'leader' => auth()->user(),
            ];
        }

        $issues = $meeting->issues->isNotEmpty() ? $meeting->issues : Issue::where('current_level', 'TL')->take(5)->get();
        $tasks = Task::with('assignee')->take(6)->get();
        $users = User::where('is_active', true)->take(12)->get();

        return Inertia::render('Meetings/Show', [
            'meeting' => $meeting,
            'issues' => $issues,
            'tasks' => $tasks,
            'users' => $users,
        ]);
    }

    public function workspace($id)
    {
        $meeting = Meeting::with(['leader', 'issues.reporter', 'issues.owner', 'issues.decisions'])->find($id);
        if (!$meeting) {
            $meeting = Meeting::first() ?? (object)[
                'id' => $id,
                'type' => 'Rapat TL + Staf',
                'meeting_date' => now()->toDateString(),
                'time_start' => '08:00',
                'time_end' => '10:00',
                'location' => 'Ruang Operasi',
                'meeting_mode' => 'Offline',
                'status' => 'Berlangsung',
                'leader' => auth()->user(),
            ];
        }

        // Tentukan level rapat sesuai tipe
        $type = $meeting->type ?? 'Rapat TL + Staf';
        if (str_contains($type, 'Asman')) {
            $meetingLevel = 'ASMAN';
        } elseif (str_contains($type, 'Manajemen') || str_contains($type, 'RTM')) {
            $meetingLevel = 'MUP';
        } else {
            $meetingLevel = 'TL';
        }

        // Agenda 1: Informasi Cascading dari level atas
        $informations = Information::with('publisher')
            ->where('is_active', true)
            ->whereIn('target_level', ['ALL', $meetingLevel, 'STAFF'])
            ->latest('published_at')
            ->take(6)
            ->get();

        // =========================================================================
        // TAHAP 2: MEETING AGENDA QUERY (OTOMATISASI REVIEW N-1 & TASK OUTSTANDING)
        // Menarik seluruh pekerjaan yang belum selesai (backlog) dari minggu sebelumnya
        // Terisolasi ketat sesuai lingkup pimpinan rapat (TL vs Asman vs MUP)
        // =========================================================================
        $today = now()->toDateString();
        $outstandingQuery = Task::with(['assignee', 'issue'])
            ->where(function ($q) {
                $q->whereNull('progress_percent')
                  ->orWhere('progress_percent', '<', 100);
            })
            ->where(function ($q) use ($today) {
                $q->whereIn('status', ['Open', 'Assigned', 'In Progress', 'On Progress', 'Blocked', 'Overdue', 'Terlambat'])
                  ->orWhere('due_date', '<', $today);
            });

        $leader = $meeting->leader ?? auth()->user();
        $leaderUnits = $leader ? $leader->getAuthorizedUnitIds() : [];

        if ($meetingLevel === 'TL') {
            // Untuk rapat TL: Pekerjaan staf di bawah seksinya atau yang ditugaskan ke TL
            $outstandingQuery->where(function ($q) use ($leader, $leaderUnits) {
                if (!empty($leaderUnits)) {
                    $q->whereIn('unit_id', $leaderUnits)
                      ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                      ->orWhere('assigned_to', $leader->id)
                      ->orWhere('assigned_by', $leader->id);
                } else {
                    $q->where('assigned_to', $leader->id ?? auth()->id());
                }
            });
        } elseif ($meetingLevel === 'ASMAN') {
            // Untuk rapat ASMAN: Pekerjaan seluruh seksi di bawah bidang Asman
            if (!empty($leaderUnits)) {
                $outstandingQuery->where(function ($q) use ($leader, $leaderUnits) {
                    $q->whereIn('unit_id', $leaderUnits)
                      ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                      ->orWhere('assigned_by', $leader->id);
                });
            }
        }
        // Untuk MUP / RTM: Memuat seluruh tunggakan pekerjaan UP Sengkang secara komprehensif

        $evalTasks = $outstandingQuery->orderBy('due_date', 'asc')->get();
        if ($evalTasks->isEmpty()) {
            $fallbackQuery = Task::with('assignee');
            if (($meetingLevel === 'TL' || $meetingLevel === 'ASMAN') && !empty($leaderUnits)) {
                $fallbackQuery->where(function($q) use ($leaderUnits, $leader) {
                    $q->whereIn('unit_id', $leaderUnits)
                      ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                      ->orWhere('assigned_to', $leader->id);
                });
            }
            $evalTasks = $fallbackQuery->latest()->take(10)->get();
        }

        // Isu yang diagendakan pada rapat ini atau fallback sesuai level & otoritas unit
        if ($meeting->issues && $meeting->issues->isNotEmpty()) {
            $activeIssues = $meeting->issues;
        } else {
            $activeIssuesQuery = Issue::with(['reporter', 'owner'])
                ->where('current_level', $meetingLevel)
                ->whereNotIn('status', ['Resolved', 'Closed']);

            if (($meetingLevel === 'TL' || $meetingLevel === 'ASMAN') && !empty($leaderUnits)) {
                $activeIssuesQuery->whereIn('unit_id', $leaderUnits);
            }

            $activeIssues = $activeIssuesQuery->latest()->get();
        }

        // Semua isu aktif yang bisa ditambahkan ke agenda rapat sesuai alur bertingkat
        $allOpenIssuesQuery = Issue::whereNotIn('status', ['Resolved', 'Closed']);
        if ($meetingLevel === 'TL') {
            // Rapat TL: hanya isu staf di unit seksinya pada level TL
            if (!empty($leaderUnits)) {
                $allOpenIssuesQuery->whereIn('unit_id', $leaderUnits)->where('current_level', 'TL');
            }
        } elseif ($meetingLevel === 'ASMAN') {
            // Rapat Asman: hanya isu yang dibawa oleh TL ke level ASMAN (atau dieskalasikan)
            if (!empty($leaderUnits)) {
                $allOpenIssuesQuery->whereIn('unit_id', $leaderUnits)
                    ->where(function ($q) {
                        $q->whereIn('current_level', ['ASMAN', 'MUP'])
                          ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'));
                    });
            }
        } elseif ($meetingLevel === 'MUP') {
            // Rapat MUP: hanya isu yang dibawa oleh Asman ke level MUP
            $allOpenIssuesQuery->where(function ($q) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'));
            });
        }
        $allOpenIssues = $allOpenIssuesQuery->latest()->get();

        // Agenda 3: Perencanaan Pekerjaan Minggu N
        $plannedQuery = Task::with('assignee');
        if (($meetingLevel === 'TL' || $meetingLevel === 'ASMAN') && !empty($leaderUnits)) {
            $plannedQuery->where(function($q) use ($leaderUnits, $leader) {
                $q->whereIn('unit_id', $leaderUnits)
                  ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                  ->orWhere('assigned_to', $leader->id);
            });
        }
        $plannedTasks = $plannedQuery->latest()->take(12)->get();

        // Agenda 4: Rekap Keputusan & Tindak Lanjut
        $decisionsQuery = Decision::with(['decisionMaker', 'issue', 'tasks.assignee']);
        if (($meetingLevel === 'TL' || $meetingLevel === 'ASMAN') && !empty($leaderUnits)) {
            $decisionsQuery->whereHas('issue', fn($iq) => $iq->whereIn('unit_id', $leaderUnits));
        }
        $decisions = $decisionsQuery->latest()->take(10)->get();

        // Opsi pengguna bawahan yang dapat ditugaskan task dalam rapat
        $subordinateIds = $leader ? $leader->getSubordinateUserIds() : [];
        if ($meetingLevel === 'MUP') {
            $users = User::where('is_active', true)->orderBy('name')->get();
        } else {
            $users = User::whereIn('id', $subordinateIds)->where('is_active', true)->orderBy('name')->get();
            if ($users->isEmpty() && $leader) {
                $users = User::where('id', $leader->id)->get();
            }
        }

        return Inertia::render('Meetings/Workspace', [
            'meeting' => $meeting,
            'meetingLevel' => $meetingLevel,
            'informations' => $informations,
            'evalTasks' => $evalTasks,
            'activeIssues' => $activeIssues,
            'allOpenIssues' => $allOpenIssues,
            'plannedTasks' => $plannedTasks,
            'decisions' => $decisions,
            'users' => $users,
        ]);
    }

    public function attachIssue(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $request->validate(['issue_id' => 'required|exists:issues,id']);
        $meeting->issues()->syncWithoutDetaching([$request->issue_id]);
        return back()->with('success', 'Isu terpilih berhasil dimasukkan ke agenda rapat.');
    }

    public function detachIssue(Request $request, $id, $issueId)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->issues()->detach($issueId);
        return back()->with('success', 'Isu berhasil dikeluarkan dari agenda rapat.');
    }

    public function storeWorkspaceTask(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $assignee = User::find($request->assigned_to);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => auth()->id(),
            'unit_id' => $assignee?->unit_id ?? auth()->user()->unit_id,
            'start_date' => now()->toDateString(),
            'due_date' => $request->due_date,
            'progress_percent' => 0,
            'status' => 'Open',
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        return redirect()->route('rapat.workspace', $id)->with('success', 'Pekerjaan baru untuk Minggu N berhasil ditambahkan langsung dari Workspace Rapat.');
    }

    public function finishMeeting(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $notes = $request->input('notes');

        $meeting->update([
            'status' => 'Selesai',
            'notes' => $notes ?: 'Rapat koordinasi mingguan telah selesai dilaksanakan dengan baik.',
        ]);

        return redirect()->route('rapat.show', $meeting->id)->with('success', 'Rapat koordinasi berhasil diselesaikan dan notulen otomatis tercatat.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'custom_type' => 'nullable|string|max:255',
            'meeting_date' => 'required|date',
            'time_start' => 'nullable|string',
            'time_end' => 'nullable|string',
            'location' => 'nullable|string',
            'meeting_mode' => 'nullable|string',
            'zoom_link' => 'nullable|string',
            'zoom_meeting_id' => 'nullable|string',
            'zoom_passcode' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'issue_ids' => 'nullable|array',
            'issue_ids.*' => 'exists:issues,id',
        ]);

        $meetingType = $validated['type'];
        if ($meetingType === 'CUSTOM' && !empty($validated['custom_type'])) {
            $meetingType = $validated['custom_type'];
        }

        $status = $validated['status'] ?: 'Akan Datang';

        $meeting = Meeting::create([
            'type' => $meetingType,
            'meeting_date' => $validated['meeting_date'],
            'time_start' => $validated['time_start'] ?? '08:00',
            'time_end' => $validated['time_end'] ?? '10:00',
            'location' => $validated['location'] ?? 'Ruang Rapat Operasi & Keandalan',
            'meeting_mode' => $validated['meeting_mode'] ?? 'Offline',
            'zoom_link' => $validated['zoom_link'] ?? null,
            'zoom_meeting_id' => $validated['zoom_meeting_id'] ?? null,
            'zoom_passcode' => $validated['zoom_passcode'] ?? null,
            'leader_id' => $validated['leader_id'] ?? auth()->id(),
            'status' => $status,
            'notes' => $validated['notes'] ?? null,
            'week_number' => \Carbon\Carbon::parse($validated['meeting_date'])->isoWeek(),
            'year' => \Carbon\Carbon::parse($validated['meeting_date'])->year,
        ]);

        // Hubungkan isu terpilih ke agenda rapat
        if (!empty($request->input('issue_ids'))) {
            $meeting->issues()->sync($request->input('issue_ids'));
        }

        return redirect()->route('rapat.index')->with('success', "Agenda rapat '{$meetingType}' berhasil dibuat.");
    }

    public function cetak($id)
    {
        $meeting = Meeting::with('leader')->find($id);
        if (!$meeting) {
            $meeting = (object)[
                'id' => $id,
                'type' => 'Rapat TL + Staf',
                'meeting_date' => now()->toDateString(),
                'status' => 'Selesai',
                'leader' => auth()->user(),
                'notes' => 'Rapat koordinasi mingguan telah dilaksanakan sesuai siklus N-1 dan perencanaan N.',
            ];
        }

        $type = $meeting->type ?? 'Rapat TL + Staf';
        $level = str_contains($type, 'Asman') ? 'ASMAN' : (str_contains($type, 'Manajemen') || str_contains($type, 'RTM') ? 'MUP' : 'TL');

        $issues = Issue::with(['reporter', 'owner'])->where('current_level', $level)->get();
        $tasks = Task::with('assignee')->latest()->take(15)->get();
        $decisions = Decision::with(['decisionMaker', 'issue'])->latest()->take(10)->get();
        $users = User::where('is_active', true)->take(12)->get();

        $asman = User::whereHas('role', fn($q) => $q->where('name', 'ASMAN'))->first();
        $mup = User::whereHas('role', fn($q) => $q->where('name', 'MUP'))->first();

        return Inertia::render('Meetings/Cetak', compact('meeting', 'issues', 'tasks', 'decisions', 'users', 'level', 'asman', 'mup'));
    }
}
