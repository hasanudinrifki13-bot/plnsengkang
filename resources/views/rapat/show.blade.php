@extends('layouts.app')

@section('title', 'Detail Agenda Rapat - ' . $meeting->type)
@section('page_title', 'Detail Agenda Rapat')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
    
    <!-- Header Rapat Persis Mockup Panel 4 -->
    <div class="p-8 pb-0">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('rapat.index') }}" class="text-gray-400 hover:text-gray-700">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">{{ $meeting->type }}</h1>
        </div>

        <!-- Meta Info Rapat -->
        <div class="flex flex-wrap items-center gap-6 ml-9 text-xs text-gray-500 font-medium mb-6">
            <div class="flex items-center gap-2">
                <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($meeting->meeting_date)->isoFormat('dddd, D MMMM Y') }}
            </div>
            <span class="text-gray-300">|</span>
            <div class="flex items-center gap-2">
                <i class="far fa-clock"></i> 08:00 - 10:00 WITA
            </div>
            <span class="text-gray-300">|</span>
            <div class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt"></i> Ruang Rapat UP Sengkang
            </div>
            <div class="ml-auto">
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-md text-xs font-bold">{{ $meeting->status ?? 'Akan Datang' }}</span>
            </div>
        </div>

        <!-- Tabs Menu Interaktif Persis Mockup Panel 4 -->
        <div class="flex gap-8 border-b border-gray-200 text-xs font-semibold">
            <button type="button" onclick="switchMeetingTab('agenda')" id="tab-btn-agenda" class="pb-3 border-b-[3px] border-blue-600 text-blue-600 font-bold">
                Agenda
            </button>
            <button type="button" onclick="switchMeetingTab('peserta')" id="tab-btn-peserta" class="pb-3 text-gray-500 hover:text-gray-800 transition">
                Peserta ({{ $users->count() }})
            </button>
            <button type="button" onclick="switchMeetingTab('issue')" id="tab-btn-issue" class="pb-3 text-gray-500 hover:text-gray-800 transition">
                Issue ({{ $issues->count() }})
            </button>
            <button type="button" onclick="switchMeetingTab('notulen')" id="tab-btn-notulen" class="pb-3 text-gray-500 hover:text-gray-800 transition">
                Notulen
            </button>
            <button type="button" onclick="switchMeetingTab('tindaklanjut')" id="tab-btn-tindaklanjut" class="pb-3 text-gray-500 hover:text-gray-800 transition">
                Tindak Lanjut ({{ $tasks->count() }})
            </button>
        </div>
    </div>

    <!-- ==================== TAB CONTENT 1: AGENDA (PANEL 4 MOCKUP) ==================== -->
    <div id="meeting-tab-agenda" class="p-8 flex-1">
        <div class="space-y-6">
            <!-- Agenda 1 -->
            <div class="flex gap-5">
                <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">1</div>
                <div class="flex-1 border-b border-gray-100 pb-6 flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Penyampaian informasi dari manajemen atasnya</h3>
                        <p class="text-xs text-gray-500 mt-1">Sosialisasi arahan MUP dan pembaruan KPI operasional mingguan.</p>
                    </div>
                    <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                        <i class="far fa-clock"></i> 15 menit
                    </div>
                </div>
            </div>

            <!-- Agenda 2 -->
            <div class="flex gap-5">
                <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">2</div>
                <div class="flex-1 border-b border-gray-100 pb-6 flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Evaluasi pekerjaan minggu sebelumnya (N-1)</h3>
                        <ul class="list-disc pl-5 mt-2 space-y-1 text-xs text-gray-500 font-medium">
                            <li>Pekerjaan yang selesai</li>
                            <li>Pekerjaan yang belum selesai</li>
                            <li>Kendala dan resolusi kendala</li>
                        </ul>
                    </div>
                    <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                        <i class="far fa-clock"></i> 30 menit
                    </div>
                </div>
            </div>

            <!-- Agenda 3 -->
            <div class="flex gap-5">
                <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">3</div>
                <div class="flex-1 border-b border-gray-100 pb-6 flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Perencanaan pekerjaan minggu ini (N)</h3>
                        <ul class="list-disc pl-5 mt-2 space-y-1 text-xs text-gray-500 font-medium">
                            <li>Rencana pekerjaan rutin dan preventif</li>
                            <li>Penetapan PIC dan target waktu selesai</li>
                        </ul>
                    </div>
                    <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                        <i class="far fa-clock"></i> 30 menit
                    </div>
                </div>
            </div>

            <!-- Agenda 4 -->
            <div class="flex gap-5">
                <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">4</div>
                <div class="flex-1 pb-2 flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Pengambilan keputusan di level TL (jika ada)</h3>
                        <p class="text-xs text-gray-500 mt-1">Penyelesaian isu internal atau penetapan eskalasi ke Asisten Manajer.</p>
                    </div>
                    <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                        <i class="far fa-clock"></i> 15 menit
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TAB CONTENT 2: PESERTA RAPAT ==================== -->
    <div id="meeting-tab-peserta" class="hidden p-8 flex-1">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($users as $user)
                <div class="p-4 rounded-xl border border-gray-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ $user->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $user->role->name ?? 'Staff' }}</p>
                        </div>
                    </div>
                    <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded border border-emerald-200">
                        Hadir
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ==================== TAB CONTENT 3: ISSUE RAPAT ==================== -->
    <div id="meeting-tab-issue" class="hidden p-8 flex-1">
        <div class="space-y-3">
            @forelse($issues as $issue)
                <div class="p-4 rounded-xl border border-gray-100 flex items-center justify-between hover:bg-slate-50 transition">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs font-bold text-blue-600">#{{ $issue->issue_no }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $issue->priority === 'CRITICAL' || $issue->priority === 'HIGH' ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600' }}">
                                {{ $issue->priority }}
                            </span>
                            <span class="text-gray-400 text-xs">| PIC: {{ $issue->owner->name ?? 'Staff' }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-800">{{ $issue->title }}</h4>
                    </div>
                    <a href="{{ route('issues.show', $issue->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-md text-xs font-semibold transition">
                        Buka Issue &rarr;
                    </a>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 text-xs">Tidak ada issue spesifik yang dibahas.</div>
            @endforelse
        </div>
    </div>

    <!-- ==================== TAB CONTENT 4: NOTULEN ==================== -->
    <div id="meeting-tab-notulen" class="hidden p-8 flex-1 space-y-4">
        <div class="p-5 border border-gray-100 bg-slate-50/60 rounded-xl space-y-3 text-xs">
            <h4 class="font-bold text-gray-800 text-sm border-b border-gray-200 pb-2">Catatan Risalah & Notulen Rapat:</h4>
            <div>
                <p class="font-bold text-gray-700">1. Evaluasi Operasional:</p>
                <p class="text-gray-600">Pemeliharaan periodik PLTG Blok 1 berjalan sesuai jadwal. Seluruh log parameter tekanan dan temperatur berada pada rentang batas aman.</p>
            </div>
            <div>
                <p class="font-bold text-gray-700">2. Kendala Lapangan:</p>
                <p class="text-gray-600">Filter oli kompresor mengalami kenaikan differential pressure mendekati ambang batas kritis. Dibutuhkan penggantian segera.</p>
            </div>
            <div>
                <p class="font-bold text-gray-700">3. Arahan Pimpinan Rapat:</p>
                <p class="text-gray-600">Team Leader menginstruksikan regu pemeliharaan untuk melakukan eksekusi penggantian filter pada window pemeliharaan terdekat sebelum akhir pekan.</p>
            </div>
        </div>
    </div>

    <!-- ==================== TAB CONTENT 5: TINDAK LANJUT ==================== -->
    <div id="meeting-tab-tindaklanjut" class="hidden p-8 flex-1">
        <div class="space-y-3">
            @foreach($tasks as $task)
                <div class="p-4 rounded-xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-gray-800">{{ $task->title }}</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5">PIC: {{ $task->assignee->name ?? 'Staff Pelaksana' }} | Target: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '15 Sep 2026' }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="text-xs font-bold text-gray-700">{{ $task->progress_percent }}%</span>
                            <div class="w-20 bg-gray-100 rounded-full h-1.5 overflow-hidden mt-1">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $task->progress_percent }}%"></div>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $task->progress_percent >= 100 ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-blue-50 text-blue-600 border border-blue-200' }}">
                            {{ $task->status }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Footer Buttons Sesuai Mockup Panel 4 -->
    <div class="p-8 pt-4 flex justify-end gap-4 mt-auto border-t border-gray-100 bg-slate-50/50">
        <a href="{{ route('rapat.cetak', $meeting->id ?? 1) }}" target="_blank" class="border border-red-400 text-red-500 hover:bg-red-50 px-5 py-2.5 rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-sm">
            <i class="fas fa-print"></i> Cetak / Unduh Notulen
        </a>
        <a href="{{ route('rapat.workspace', $meeting->id ?? 1) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-lg text-xs font-bold transition shadow-sm inline-block">
            Mulai Rapat
        </a>
    </div>

</div>

<script>
function switchMeetingTab(tabName) {
    const tabs = ['agenda', 'peserta', 'issue', 'notulen', 'tindaklanjut'];
    tabs.forEach(t => {
        const el = document.getElementById('meeting-tab-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (el && btn) {
            if (t === tabName) {
                el.classList.remove('hidden');
                btn.className = 'pb-3 border-b-[3px] border-blue-600 text-blue-600 font-bold';
            } else {
                el.classList.add('hidden');
                btn.className = 'pb-3 text-gray-500 hover:text-gray-800 transition';
            }
        }
    });
}
</script>
@endsection
