<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace & Notulen Rapat - Sistem Koordinasi UP Sengkang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        pln: { dark: '#1e293b', blue: '#0284c7', lightblue: '#f0f9ff', yellow: '#f59e0b' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f8fafc] flex flex-col h-screen overflow-hidden text-sm font-sans antialiased text-gray-800">

    <!-- Top Navigation Bar (Responsive) -->
    <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-2.5 flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 z-10 shrink-0 shadow-sm">
        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
            <a href="{{ route('rapat.show', $meeting->id ?? 1) }}" class="text-gray-400 hover:text-gray-700 p-1">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-gray-800 flex items-center gap-1.5 sm:gap-2">
                    <span class="truncate max-w-[200px] sm:max-w-none">{{ $meeting->type }}</span>
                    <span class="text-gray-400 font-normal">|</span>
                    <span class="text-[11px] sm:text-xs font-semibold text-gray-500">
                        {{ \Carbon\Carbon::parse($meeting->meeting_date)->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </h2>
            </div>
            @if(($meeting->status ?? '') === 'Berlangsung')
                <span class="bg-red-500 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 animate-pulse">
                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span> LIVE
                </span>
            @else
                <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full">
                    {{ $meeting->status }}
                </span>
            @endif
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('rapat.cetak', $meeting->id ?? 1) }}" target="_blank" class="border border-blue-500 text-blue-600 hover:bg-blue-50 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
                <i class="fas fa-print"></i> <span class="hidden sm:inline">Cetak Notulen</span> PDF
            </a>
            @if(($meeting->status ?? '') !== 'Selesai')
                <button onclick="openModal('modalFinishMeeting')" class="bg-slate-800 hover:bg-slate-900 text-white px-3 sm:px-4 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition">
                    <i class="fas fa-check-double"></i> Selesaikan
                </button>
            @else
                <a href="{{ route('rapat.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 sm:px-4 py-1.5 rounded-lg text-xs font-semibold">
                    Kembali
                </a>
            @endif
        </div>
    </header>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="bg-emerald-600 text-white px-4 sm:px-6 py-2 text-xs font-semibold flex items-center justify-between shrink-0 shadow">
            <span><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-emerald-100">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-600 text-white px-4 sm:px-6 py-2 text-xs font-semibold flex items-center justify-between shrink-0 shadow">
            <span><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-rose-100">&times;</button>
        </div>
    @endif

    <!-- Main 2-Column Split Workspace (Responsive for Tablet & Mobile) -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        
        <!-- Sisi Kiri: Zoom Hub / Video Room & Roster Peserta Nyata Karyawan UP Sengkang -->
        <div class="w-full lg:w-2/5 bg-slate-900 p-4 sm:p-6 flex flex-col justify-start relative border-b lg:border-b-0 lg:border-r border-slate-800 overflow-y-auto space-y-4 max-h-[35vh] sm:max-h-[45vh] lg:max-h-full shrink-0">
            
            <!-- Panel Zoom & Status Sesi Rapat -->
            <div class="w-full bg-slate-800/90 rounded-2xl p-5 border border-slate-700 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ ($meeting->status ?? '') === 'Berlangsung' ? 'bg-red-500 animate-pulse' : 'bg-emerald-400' }}"></span>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                            Mode: {{ $meeting->meeting_mode ?? 'Online (Zoom)' }}
                        </h3>
                    </div>
                    <span class="text-[10px] bg-blue-900/60 text-blue-300 font-semibold px-2 py-0.5 rounded border border-blue-700">
                        {{ $meeting->time_start ?? '08:00' }} - {{ $meeting->time_end ?? '10:00' }} WITA
                    </span>
                </div>

                @if(!empty($meeting->zoom_link))
                    <!-- Card Zoom Rapat Aktif -->
                    <div class="bg-gradient-to-br from-blue-900/80 to-slate-900 p-4 rounded-xl border border-blue-600/50 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-blue-400 tracking-wider">Tautan Zoom Resmi</span>
                                <h4 class="text-sm font-bold text-white mt-0.5 flex items-center gap-2">
                                    <i class="fas fa-video text-blue-400"></i> Video Conference UP Sengkang
                                </h4>
                            </div>
                            <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 text-[10px] font-bold rounded-full border border-blue-500/30">Aktif</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-700">
                                <span class="text-[9px] text-gray-400 block uppercase">Meeting ID</span>
                                <span class="font-mono font-bold text-gray-200 text-[11px]">{{ $meeting->zoom_meeting_id ?: '890 2345 6789' }}</span>
                            </div>
                            <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-700">
                                <span class="text-[9px] text-gray-400 block uppercase">Passcode</span>
                                <span class="font-mono font-bold text-gray-200 text-[11px]">{{ $meeting->zoom_passcode ?: 'PLNNP2026' }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-1">
                            <a href="{{ $meeting->zoom_link }}" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-3 rounded-lg text-xs flex items-center justify-center gap-2 shadow-lg transition">
                                <i class="fas fa-external-link-alt"></i> Buka Zoom Meeting
                            </a>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $meeting->zoom_link }}'); alert('Tautan Zoom berhasil disalin!');" class="bg-slate-700 hover:bg-slate-600 text-gray-200 px-3 rounded-lg text-xs transition" title="Salin Tautan">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Ruang Rapat Tatap Muka / Offline -->
                    <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-400 text-xs font-bold">
                            <i class="fas fa-building"></i> Pertemuan Tatap Muka di Ruangan
                        </div>
                        <p class="text-xs text-gray-300 font-medium">{{ $meeting->location ?? 'Ruang Rapat Operasi & Keandalan' }}</p>
                        <p class="text-[11px] text-gray-400">Pastikan seluruh peserta mengisi lembar kehadiran dan notulen dicatat melalui workspace ini.</p>
                    </div>
                @endif

                <!-- Pimpinan Rapat Card -->
                <div class="bg-slate-900/90 rounded-xl p-3 border border-amber-500/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center text-sm shadow">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-amber-400 uppercase tracking-wider block">Pimpinan Rapat (Leader)</span>
                            <h4 class="text-xs font-bold text-white">{{ $meeting->leader->name ?? auth()->user()->name }}</h4>
                            <p class="text-[10px] text-gray-400">{{ $meeting->leader->position->name ?? ($meeting->leader->role->name ?? 'Pimpinan Unit') }}</p>
                        </div>
                    </div>
                    <span class="bg-emerald-500/20 text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span> Mic Aktif
                    </span>
                </div>
            </div>

            <!-- Panel Peserta Hadir (Attendance Roster Karyawan Riil) -->
            <div class="bg-slate-800/80 rounded-2xl p-4 border border-slate-700 text-white space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-700">
                    <h4 class="text-xs font-bold text-slate-200 flex items-center gap-2">
                        <i class="fas fa-users text-blue-400"></i> Peserta Rapat Terdaftar ({{ count($users ?? []) }} Personil)
                    </h4>
                    <span class="text-[10px] bg-emerald-900/60 text-emerald-300 px-2 py-0.5 rounded font-semibold border border-emerald-700">
                        Online
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1">
                    @foreach(($users ?? []) as $usr)
                        <div class="bg-slate-900/70 border border-slate-700/80 p-2 rounded-lg flex items-center gap-2.5 hover:border-slate-500 transition">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0">
                                {{ strtoupper(substr($usr->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold text-slate-200 truncate">{{ $usr->name }}</p>
                                <p class="text-[9px] text-gray-400 truncate">{{ $usr->position->name ?? ($usr->role->name ?? 'Staf') }}</p>
                            </div>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: 4 Agendas Workspace (Responsive on Mobile & Tablet) -->
        <div class="w-full lg:w-3/5 flex flex-col bg-white overflow-hidden flex-1">
            
            <!-- Agenda Tabs Header -->
            <div class="border-b border-gray-200 px-3 sm:px-6 pt-2.5 sm:pt-4 bg-slate-50/70 flex gap-1 sm:gap-2 overflow-x-auto shrink-0 whitespace-nowrap">
                <button onclick="switchAgenda('agenda1')" id="btn-agenda1" class="pb-2.5 sm:pb-3 px-2 sm:px-3 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[9px] sm:text-[10px]">1</span>
                    Informasi Level Atas
                </button>
                <button onclick="switchAgenda('agenda2')" id="btn-agenda2" class="pb-2.5 sm:pb-3 px-2 sm:px-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[9px] sm:text-[10px]">2</span>
                    Evaluasi N-1 & Kendala
                    @if(count($activeIssues) > 0)
                        <span class="bg-red-500 text-white text-[9px] px-1.5 py-0.2 rounded-full">{{ count($activeIssues) }}</span>
                    @endif
                </button>
                <button onclick="switchAgenda('agenda3')" id="btn-agenda3" class="pb-2.5 sm:pb-3 px-2 sm:px-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[9px] sm:text-[10px]">3</span>
                    Rencana Minggu N
                </button>
                <button onclick="switchAgenda('agenda4')" id="btn-agenda4" class="pb-2.5 sm:pb-3 px-2 sm:px-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[9px] sm:text-[10px]">4</span>
                    Keputusan & Tindak Lanjut
                </button>
            </div>

            <!-- Agenda Content Container -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                
                <!-- ==================== AGENDA 1: INFORMASI LEVEL ATAS ==================== -->
                <div id="panel-agenda1" class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Agenda 1: Arahan & Cascading Information (FR-09 & BR-11)</h3>
                            <p class="text-xs text-gray-500">Arahan resmi dari level MUP / Asisten Manajer yang relevan untuk dibahas dalam rapat.</p>
                        </div>
                        <a href="{{ route('informasi.index') }}" target="_blank" class="text-blue-600 hover:underline text-xs font-semibold flex items-center gap-1">
                            Buka Pusat Arahan <i class="fas fa-external-link-alt text-[10px]"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        @forelse($informations as $info)
                            <div class="p-4 rounded-xl border border-gray-100 bg-slate-50/50 hover:bg-white hover:shadow-sm transition">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                                        {{ $info->category }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">
                                        {{ $info->published_at ? $info->published_at->format('d M Y') : '-' }} | Oleh {{ $info->publisher->name ?? 'Pimpinan' }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-xs mb-1">{{ $info->title }}</h4>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $info->content }}</p>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400 text-xs">
                                <i class="fas fa-info-circle text-2xl mb-2 text-gray-300 block"></i>
                                Tidak ada arahan cascading khusus untuk sesi rapat ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- ==================== AGENDA 2: EVALUASI N-1 & KENDALA / ISSUE ==================== -->
                <div id="panel-agenda2" class="hidden space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Agenda 2: Evaluasi Minggu N-1 & Kendala / Issue Aktif</h3>
                            <p class="text-xs text-gray-500">Tinjau progres pekerjaan lalu dan putuskan issue pada Lowest Competent Level (Level {{ $meetingLevel }}).</p>
                        </div>
                    </div>

                    <!-- Sub-bagian A: Daftar Isu Aktif Membutuhkan Putusan / Eskalasi -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-triangle text-amber-500"></i> Isu & Kendala Diagendakan ({{ count($activeIssues) }})
                                </h4>
                                <span class="text-[11px] text-gray-400">Pembahasan dan pengambilan keputusan resmi pada rapat</span>
                            </div>
                            <button type="button" onclick="openModal('modalAttachIssue')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition">
                                <i class="fas fa-plus-circle"></i> Tambah Isu ke Agenda Rapat
                            </button>
                        </div>

                        <div class="overflow-x-auto border border-gray-100 rounded-xl">
                            <table class="w-full text-left text-xs whitespace-nowrap">
                                <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-gray-100">
                                    <tr>
                                        <th class="px-3 py-2.5">No Issue</th>
                                        <th class="px-3 py-2.5">Judul Isu</th>
                                        <th class="px-3 py-2.5">Prioritas</th>
                                        <th class="px-3 py-2.5">Pelapor</th>
                                        <th class="px-3 py-2.5">Batas Waktu</th>
                                        <th class="px-3 py-2.5 text-center">Aksi Langsung</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    @forelse($activeIssues as $issue)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-3 py-3 font-mono font-bold text-blue-600">
                                                <a href="{{ route('issues.show', $issue->id) }}" target="_blank" class="hover:underline">
                                                    {{ $issue->issue_no }}
                                                </a>
                                            </td>
                                            <td class="px-3 py-3 font-medium text-gray-800 max-w-xs truncate" title="{{ $issue->title }}">
                                                {{ $issue->title }}
                                            </td>
                                            <td class="px-3 py-3">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $issue->priority === 'CRITICAL' ? 'bg-red-100 text-red-700' : ($issue->priority === 'HIGH' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                                                    {{ $issue->priority }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3">{{ $issue->reporter->name ?? 'Staff' }}</td>
                                            <td class="px-3 py-3 font-mono text-[11px]">{{ $issue->due_date ?? '-' }}</td>
                                            <td class="px-3 py-3 text-center">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <button type="button" onclick="triggerDecisionModal({{ $issue->id }}, '{{ addslashes($issue->title) }}')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold px-2.5 py-1 rounded shadow-sm">
                                                        <i class="fas fa-gavel"></i> Putuskan
                                                    </button>
                                                    @if(in_array($meetingLevel, ['TL', 'ASMAN']))
                                                        <button type="button" onclick="triggerEscalateModal({{ $issue->id }}, '{{ addslashes($issue->title) }}', '{{ $issue->current_level === 'TL' ? 'ASMAN' : 'MUP' }}')" class="bg-amber-600 hover:bg-amber-700 text-white text-[11px] font-semibold px-2.5 py-1 rounded shadow-sm">
                                                            <i class="fas fa-level-up-alt"></i> Eskalasi
                                                        </button>
                                                    @endif
                                                    <form action="{{ route('rapat.detach_issue', ['id' => $meeting->id ?? 1, 'issueId' => $issue->id]) }}" method="POST" onsubmit="return confirm('Keluarkan isu ini dari agenda rapat?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Keluarkan dari agenda rapat" class="text-gray-400 hover:text-red-500 p-1 rounded text-xs transition">
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                                Tidak ada kendala aktif yang tertunda pada level {{ $meetingLevel }}.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sub-bagian B: Evaluasi Task Pekerjaan N-1 & Tunggakan Backlog -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-history text-amber-500"></i> Evaluasi Pekerjaan Lalu & Tunggakan (Outstanding Backlog)
                                </h4>
                                <p class="text-[11px] text-gray-400">Pekerjaan dari minggu-minggu sebelumnya yang belum tuntas 100% otomatis ditarik untuk evaluasi rapat</p>
                            </div>
                            <span class="text-[10px] bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded font-mono font-bold flex items-center gap-1">
                                <i class="fas fa-sync-alt fa-spin text-[9px]"></i> Continuous Cycle
                            </span>
                        </div>
                        <div class="overflow-x-auto border border-gray-100 rounded-xl">
                            <table class="w-full text-left text-xs whitespace-nowrap">
                                <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-gray-100">
                                    <tr>
                                        <th class="px-3 py-2.5">Pekerjaan</th>
                                        <th class="px-3 py-2.5">Asal Minggu</th>
                                        <th class="px-3 py-2.5">PIC</th>
                                        <th class="px-3 py-2.5">Tenggat Waktu</th>
                                        <th class="px-3 py-2.5">Progres</th>
                                        <th class="px-3 py-2.5">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    @forelse($evalTasks as $t)
                                        @php
                                            $isOverdue = ($t->due_date && $t->due_date < now()->toDateString() && (int)$t->progress_percent < 100) || $t->calculated_status === 'Terlambat';
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'bg-red-50/30 hover:bg-red-50/50' : 'hover:bg-slate-50/70' }} transition">
                                            <td class="px-3 py-2.5 font-medium text-gray-800">
                                                <div class="flex items-center gap-1.5">
                                                    @if($isOverdue)
                                                        <i class="fas fa-exclamation-circle text-rose-500 text-xs"></i>
                                                    @endif
                                                    <span class="line-clamp-1 max-w-xs" title="{{ $t->title }}">{{ $t->title }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2.5 font-mono text-[11px] text-gray-500">
                                                <span class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold">W{{ $t->week_number ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 py-2.5 font-semibold text-gray-700">{{ $t->assignee->name ?? 'Belum Ditugaskan' }}</td>
                                            <td class="px-3 py-2.5 font-mono text-[11px] {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-gray-500' }}">
                                                {{ $t->due_date ? \Carbon\Carbon::parse($t->due_date)->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-3 py-2.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-[11px] w-8 text-right">{{ $t->progress_percent }}%</span>
                                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                        <div class="{{ $isOverdue ? 'bg-red-500' : 'bg-blue-600' }} h-1.5 rounded-full" style="width: {{ $t->progress_percent }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2.5">
                                                @if($isOverdue)
                                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded border border-rose-200 inline-flex items-center gap-1">
                                                        <i class="fas fa-clock text-[8px]"></i> OVERDUE
                                                    </span>
                                                @elseif($t->progress_percent >= 100 || in_array($t->status, ['Selesai', 'Closed', 'Resolved']))
                                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded border border-emerald-200 inline-flex items-center gap-1">
                                                        <i class="fas fa-check text-[8px]"></i> Selesai
                                                    </span>
                                                @else
                                                    <span class="bg-blue-50 text-blue-700 text-[10px] font-semibold px-2 py-0.5 rounded border border-blue-200">{{ $t->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-400">Tidak ada data tunggakan pekerjaan. Seluruh pekerjaan terselesaikan tepat waktu.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== AGENDA 3: PERENCANAAN PEKERJAAN MINGGU N ==================== -->
                <div id="panel-agenda3" class="hidden space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Agenda 3: Perencanaan Pekerjaan Minggu N (FR-01)</h3>
                            <p class="text-xs text-gray-500">Tugaskan sasaran kerja dan jadwal operasional baru untuk tim minggu ini.</p>
                        </div>
                        <button onclick="openModal('modalAddTask')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition">
                            <i class="fas fa-plus"></i> Tambah Pekerjaan Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="w-full text-left text-xs whitespace-nowrap">
                            <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-gray-100">
                                <tr>
                                    <th class="px-3 py-2.5">Nama Pekerjaan</th>
                                    <th class="px-3 py-2.5">Penanggung Jawab (PIC)</th>
                                    <th class="px-3 py-2.5">Target Selesai</th>
                                    <th class="px-3 py-2.5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($plannedTasks as $pt)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-3 py-2.5 font-semibold text-gray-800">{{ $pt->title }}</td>
                                        <td class="px-3 py-2.5">{{ $pt->assignee->name ?? 'Belum Ditugaskan' }}</td>
                                        <td class="px-3 py-2.5 font-mono text-[11px]">{{ $pt->due_date ?? '-' }}</td>
                                        <td class="px-3 py-2.5">
                                            <span class="bg-sky-50 text-sky-700 font-semibold px-2 py-0.5 rounded text-[10px]">
                                                {{ $pt->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                            Belum ada rencana pekerjaan untuk Minggu N. Klik "Tambah Pekerjaan Baru" di atas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ==================== AGENDA 4: PENGAMBILAN KEPUTUSAN & NOTULEN ==================== -->
                <div id="panel-agenda4" class="hidden space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Agenda 4: Rekap Putusan & Notulen Koordinasi (FR-06)</h3>
                            <p class="text-xs text-gray-500">Hasil keputusan resmi dan task otomatis yang dihasilkan dalam siklus rapat ini.</p>
                        </div>
                        <a href="{{ route('rapat.cetak', $meeting->id ?? 1) }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                            <i class="fas fa-file-pdf"></i> Format Cetak Notulen
                        </a>
                    </div>

                    <!-- Tabel Hasil & Keputusan Mockup Panel 9 -->
                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="w-full text-left text-xs whitespace-nowrap">
                            <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-gray-100">
                                <tr>
                                    <th class="px-3 py-2.5">No</th>
                                    <th class="px-3 py-2.5">Issue Terkait</th>
                                    <th class="px-3 py-2.5">Keputusan Resmi</th>
                                    <th class="px-3 py-2.5">Pejabat Pemutus</th>
                                    <th class="px-3 py-2.5">Tindak Lanjut & Task</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($decisions as $idx => $dec)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-3 py-3 text-gray-400 font-mono">{{ $idx + 1 }}</td>
                                        <td class="px-3 py-3">
                                            @if($dec->issue)
                                                <span class="font-bold text-blue-600 font-mono">{{ $dec->issue->issue_no }}</span>
                                                <p class="text-[11px] text-gray-500 truncate max-w-xs">{{ $dec->issue->title }}</p>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 font-semibold text-gray-800">
                                            {{ $dec->decision }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px]">
                                                {{ $dec->decision_level }}
                                            </span>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $dec->decisionMaker->name ?? 'Pejabat' }}</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if(count($dec->tasks ?? []) > 0)
                                                @foreach($dec->tasks as $tsk)
                                                    <span class="inline-block bg-blue-50 text-blue-700 text-[10px] font-semibold px-2 py-0.5 rounded border border-blue-100">
                                                        Task #{{ $tsk->id }} &rarr; {{ $tsk->assignee->name ?? 'PIC' }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-xs text-gray-500">{{ $dec->follow_up ?: 'Selesai tanpa task tambahan' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                            Belum ada keputusan yang ditetapkan dalam sesi rapat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- ==================== MODAL AMBIL PUTUSAN DI WORKSPACE ==================== -->
    <div id="modalWorkspaceDecision" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in">
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-gavel text-emerald-400"></i>
                    <h3 class="font-bold text-sm">Ambil Putusan Langsung di Rapat</h3>
                </div>
                <button type="button" onclick="closeModal('modalWorkspaceDecision')" class="text-gray-400 hover:text-white text-lg">&times;</button>
            </div>

            <form id="formWorkspaceDecision" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Issue Terpilih:</label>
                    <p id="decisionIssueTitle" class="text-xs font-bold text-gray-800 bg-slate-50 p-2.5 rounded-lg border border-gray-100"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pernyataan Keputusan Resmi <span class="text-red-500">*</span></label>
                    <textarea name="decision" rows="3" required placeholder="Tuliskan keputusan atau instruksi teknis..." class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Tindak Lanjut</label>
                    <textarea name="follow_up" rows="2" placeholder="Instruksi pelaksanaan lebih lanjut..." class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <!-- Otomatisasi Task (BR-07) -->
                <div class="bg-blue-50/70 p-3 rounded-xl border border-blue-100 space-y-3">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="create_task" id="ws_create_task" value="1" class="rounded text-blue-600 focus:ring-blue-500" onchange="document.getElementById('ws_taskDetails').classList.toggle('hidden', !this.checked)">
                        <label for="ws_create_task" class="text-xs text-blue-900 font-bold cursor-pointer">
                            <i class="fas fa-magic text-blue-600 mr-1"></i> Buat Task Tindak Lanjut Otomatis (BR-07)
                        </label>
                    </div>

                    <div id="ws_taskDetails" class="hidden space-y-2 pt-1 border-t border-blue-200/60">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-blue-900 mb-0.5">PIC Pelaksana</label>
                                <select name="task_assignee_id" class="w-full text-xs border border-blue-200 rounded p-1.5 bg-white">
                                    <option value="">-- Pilih PIC --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role->name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-blue-900 mb-0.5">Target Selesai</label>
                                <input type="date" name="task_due_date" value="{{ now()->addDays(3)->toDateString() }}" class="w-full text-xs border border-blue-200 rounded p-1.5 bg-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="mark_resolved" id="ws_mark_resolved" value="1" class="rounded text-emerald-600">
                    <label for="ws_mark_resolved" class="text-xs text-gray-700 font-semibold cursor-pointer">Tandai Masalah Selesai (Resolved)</label>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeModal('modalWorkspaceDecision')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL ESKALASI DI WORKSPACE ==================== -->
    <div id="modalWorkspaceEscalate" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in">
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-level-up-alt text-amber-400"></i>
                    <h3 class="font-bold text-sm">Eskalasi Issue ke Level Atas</h3>
                </div>
                <button type="button" onclick="closeModal('modalWorkspaceEscalate')" class="text-gray-400 hover:text-white text-lg">&times;</button>
            </div>

            <form id="formWorkspaceEscalate" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Issue Terpilih:</label>
                    <p id="escalateIssueTitle" class="text-xs font-bold text-gray-800 bg-slate-50 p-2.5 rounded-lg border border-gray-100"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Eskalasi & Rekomendasi Putusan <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="4" required placeholder="Jelaskan alasan batas wewenang atau kebutuhan persetujuan atasan..." class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeModal('modalWorkspaceEscalate')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-sm">Kirim Eskalasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL TAMBAH TASK MINGGU N ==================== -->
    <div id="modalAddTask" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in">
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-tasks text-blue-400"></i>
                    <h3 class="font-bold text-sm">Tambah Pekerjaan Minggu N</h3>
                </div>
                <button type="button" onclick="closeModal('modalAddTask')" class="text-gray-400 hover:text-white text-lg">&times;</button>
            </div>

            <form action="{{ route('rapat.tasks.store', $meeting->id ?? 1) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Penggantian Filter Udara Kompresor" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">PIC Pelaksana <span class="text-red-500">*</span></label>
                        <select name="assigned_to" required class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->employee_no ?? 'NID' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Target Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" required value="{{ now()->addDays(5)->toDateString() }}" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Keterangan / Sasaran Teknis</label>
                    <textarea name="description" rows="3" placeholder="Uraian langkah kerja atau instruksi..." class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeModal('modalAddTask')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm">Simpan Rencana Kerja</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL SELESAIKAN RAPAT ==================== -->
    <div id="modalFinishMeeting" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in">
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-double text-emerald-400"></i>
                    <h3 class="font-bold text-sm">Selesaikan Rapat Koordinasi</h3>
                </div>
                <button type="button" onclick="closeModal('modalFinishMeeting')" class="text-gray-400 hover:text-white text-lg">&times;</button>
            </div>

            <form action="{{ route('rapat.finish', $meeting->id ?? 1) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-xs text-gray-600 leading-relaxed">
                    Menyelesaikan rapat akan mengubah status menjadi <strong>Selesai</strong> dan membekukan notulen resmi untuk diarsipkan dan dicetak.
                </p>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Kesimpulan / Rangkuman Rapat</label>
                    <textarea name="notes" rows="4" placeholder="Tuliskan intisari pembahasan dan instruksi penutup pimpinan rapat..." class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeModal('modalFinishMeeting')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-sm">Konfirmasi & Tutup Rapat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL TAMBAH ISU KE AGENDA RAPAT ==================== -->
    <div id="modalAttachIssue" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in">
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-400"></i>
                    <h3 class="font-bold text-sm">Tambahkan Isu ke Agenda Rapat Ini</h3>
                </div>
                <button type="button" onclick="closeModal('modalAttachIssue')" class="text-gray-400 hover:text-white text-lg">&times;</button>
            </div>

            <form action="{{ route('rapat.attach_issue', $meeting->id ?? 1) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Isu Aktif dari Database</label>
                    <select name="issue_id" required class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Isu untuk Dibahas --</option>
                        @foreach($allOpenIssues ?? [] as $oi)
                            <option value="{{ $oi->id }}">
                                #{{ $oi->issue_no }} - {{ \Illuminate\Support\Str::limit($oi->title, 45) }} (Level {{ $oi->current_level }} | {{ $oi->priority }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Isu yang dipilih akan langsung masuk ke daftar pembahasan rapat dan tercatat di notulen resmi.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeModal('modalAttachIssue')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm">Masukkan ke Agenda</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchAgenda(agendaId) {
            const agendas = ['agenda1', 'agenda2', 'agenda3', 'agenda4'];
            agendas.forEach(a => {
                const panel = document.getElementById('panel-' + a);
                const btn = document.getElementById('btn-' + a);
                if (panel && btn) {
                    if (a === agendaId) {
                        panel.classList.remove('hidden');
                        btn.className = 'pb-3 px-3 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2';
                    } else {
                        panel.classList.add('hidden');
                        btn.className = 'pb-3 px-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2';
                    }
                }
            });
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('hidden');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('hidden');
        }

        function triggerDecisionModal(issueId, issueTitle) {
            const form = document.getElementById('formWorkspaceDecision');
            const titleEl = document.getElementById('decisionIssueTitle');
            if (form && titleEl) {
                form.action = `/issues/${issueId}/decisions`;
                titleEl.textContent = issueTitle;
                openModal('modalWorkspaceDecision');
            }
        }

        function triggerEscalateModal(issueId, issueTitle, targetLevel) {
            const form = document.getElementById('formWorkspaceEscalate');
            const titleEl = document.getElementById('escalateIssueTitle');
            if (form && titleEl) {
                form.action = `/issues/${issueId}/escalate`;
                titleEl.textContent = `${issueTitle} (Target Eskalasi: Level ${targetLevel})`;
                openModal('modalWorkspaceEscalate');
            }
        }
    </script>

</body>
</html>
