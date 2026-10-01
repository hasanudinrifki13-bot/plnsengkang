@extends('layouts.app')

@section('title', 'Laporan & Kinerja Koordinasi')
@section('page_title', 'Laporan Kinerja & Koordinasi Berjenjang')

@section('content')
<div class="space-y-6">

    <!-- Header Filter & Export Bar -->
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i> Laporan Berkala Koordinasi & Eskalasi UP Sengkang
            </h2>
            <p class="text-xs text-gray-500 mt-1">Audit trail kepatuhan Lowest Competent Level, SLA pekerjaan, dan efektivitas eskalasi (FR-12 & BSD Section 9.6).</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Periode -->
            <form method="GET" action="{{ route('laporan.index') }}" class="flex items-center gap-2">
                <select name="periode" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-semibold text-gray-700 outline-none focus:ring-1 focus:ring-blue-500 bg-white shadow-sm">
                    <option value="bulan_ini" {{ $periode === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini ({{ now()->isoFormat('MMMM Y') }})</option>
                    <option value="minggu_ini" {{ $periode === 'minggu_ini' ? 'selected' : '' }}>Minggu Ini (W{{ now()->isoWeek() }})</option>
                    <option value="minggu_lalu" {{ $periode === 'minggu_lalu' ? 'selected' : '' }}>Minggu Lalu (W{{ now()->subWeek()->isoWeek() }})</option>
                    <option value="semua" {{ $periode === 'semua' ? 'selected' : '' }}>Semua Data</option>
                </select>
            </form>

            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-50 transition flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>

            <a href="{{ route('laporan.export') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-file-csv"></i> Export CSV / Excel
            </a>
        </div>
    </div>

    <!-- 4 KPI Performance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Lowest Competent Level Resolution Rate -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Resolusi Level Terendah (TL)</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-black text-blue-600">{{ $tlResolutionRate }}%</h3>
                    <span class="text-xs text-gray-500">({{ $resolvedByTL }}/{{ $totalDecisions }})</span>
                </div>
            </div>
            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden mt-3">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min($tlResolutionRate, 100) }}%"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-2"><i class="fas fa-check-circle text-blue-500"></i> Prinsip BSD: Masalah diselesaikan di TL</p>
        </div>

        <!-- 2. Status Penanganan Isu -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Total Isu & Kendala</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-black text-gray-800">{{ $totalIssues }}</h3>
                    <span class="text-xs text-emerald-600 font-semibold">({{ $resolvedIssues }} Selesai)</span>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-3 text-[11px]">
                <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded font-semibold">{{ $openIssues }} Open</span>
                <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded font-semibold">{{ $escalatedIssues }} Eskalasi</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-2">Akuntabilitas alur koordinasi</p>
        </div>

        <!-- 3. Tingkat Penyelesaian Pekerjaan (SLA) -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Realisasi Task (SLA)</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-black text-emerald-600">{{ $taskCompletionRate }}%</h3>
                    <span class="text-xs text-gray-500">({{ $completedTasks }}/{{ $totalTasks }})</span>
                </div>
            </div>
            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden mt-3">
                <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min($taskCompletionRate, 100) }}%"></div>
            </div>
            <p class="text-[10px] text-rose-500 font-semibold mt-2">
                @if($overdueTasks > 0)
                    <i class="fas fa-exclamation-triangle"></i> {{ $overdueTasks }} Task Overdue / Terlambat
                @else
                    <i class="fas fa-check"></i> Seluruh task on-track
                @endif
            </p>
        </div>

        <!-- 4. Putusan Resmi Berjenjang -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Putusan Berjenjang</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-black text-indigo-600">{{ $totalDecisions }}</h3>
                    <span class="text-xs text-gray-500">Keputusan Resmi</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-1 mt-3 text-center text-[10px]">
                <div class="bg-blue-50 text-blue-700 py-1 rounded font-bold">TL: {{ $resolvedByTL }}</div>
                <div class="bg-indigo-50 text-indigo-700 py-1 rounded font-bold">ASMAN: {{ $resolvedByAsman }}</div>
                <div class="bg-purple-50 text-purple-700 py-1 rounded font-bold">MUP: {{ $resolvedByMUP }}</div>
            </div>
            <p class="text-[10px] text-gray-400 mt-2">Audit trail tersimpan permanen</p>
        </div>

    </div>

    <!-- Tabbed Detail Tables -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        
        <!-- Tab Navigation -->
        <div class="border-b border-gray-100 px-6 pt-4 bg-slate-50/70 flex gap-4 overflow-x-auto">
            <button onclick="switchReportTab('tab-keputusan')" id="btn-tab-keputusan" class="pb-3 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2">
                <i class="fas fa-gavel"></i> Rekapitulasi Putusan Resmi ({{ count($recentDecisions) }})
            </button>
            <button onclick="switchReportTab('tab-pekerjaan')" id="btn-tab-pekerjaan" class="pb-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2">
                <i class="fas fa-tasks"></i> Rekap Pekerjaan & Progres ({{ count($tasks) }})
            </button>
            <button onclick="switchReportTab('tab-eskalasi')" id="btn-tab-eskalasi" class="pb-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2">
                <i class="fas fa-level-up-alt"></i> Log Riwayat Eskalasi ({{ count($recentEscalations) }})
            </button>
        </div>

        <div class="p-6">
            
            <!-- ==================== TAB 1: REKAP KEPUTUSAN ==================== -->
            <div id="content-tab-keputusan" class="space-y-4">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-xs whitespace-nowrap">
                        <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Issue Terkait</th>
                                <th class="px-4 py-3">Pernyataan Keputusan</th>
                                <th class="px-4 py-3">Pertimbangan (Rationale)</th>
                                <th class="px-4 py-3">Jenjang Pemutus</th>
                                <th class="px-4 py-3">Pejabat</th>
                                <th class="px-4 py-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($recentDecisions as $idx => $d)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 text-gray-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-blue-600">
                                        {{ $d->issue->issue_no ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-800 max-w-xs truncate" title="{{ $d->decision }}">
                                        {{ $d->decision }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $d->rationale }}">
                                        {{ $d->rationale ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $d->decision_level === 'MUP' ? 'bg-purple-100 text-purple-700' : ($d->decision_level === 'ASMAN' ? 'bg-indigo-100 text-indigo-700' : 'bg-blue-100 text-blue-700') }}">
                                            Level {{ $d->decision_level }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $d->decisionMaker->name ?? 'Pejabat' }}</td>
                                    <td class="px-4 py-3 text-gray-400 font-mono text-[11px]">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada keputusan resmi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== TAB 2: REKAP PEKERJAAN ==================== -->
            <div id="content-tab-pekerjaan" class="hidden space-y-4">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-xs whitespace-nowrap">
                        <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Pekerjaan</th>
                                <th class="px-4 py-3">Issue Terkait</th>
                                <th class="px-4 py-3">PIC Pelaksana</th>
                                <th class="px-4 py-3">Target Selesai</th>
                                <th class="px-4 py-3">Progres</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Status SLA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($tasks as $t)
                                @php
                                    $isOverdue = $t->due_date && $t->due_date < now()->toDateString() && !in_array($t->status, ['Selesai', 'Closed', 'Resolved']);
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $t->title }}</td>
                                    <td class="px-4 py-3 font-mono text-blue-600">{{ $t->issue ? $t->issue->issue_no : '-' }}</td>
                                    <td class="px-4 py-3">{{ $t->assignee->name ?? 'Belum Ditugaskan' }}</td>
                                    <td class="px-4 py-3 font-mono text-[11px]">{{ $t->due_date ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-[11px]">{{ $t->progress_percent }}%</span>
                                            <div class="w-16 bg-gray-100 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $t->progress_percent }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                                            {{ $t->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($isOverdue)
                                            <span class="bg-rose-100 text-rose-700 font-bold px-2 py-0.5 rounded text-[10px]">
                                                <i class="fas fa-clock mr-1"></i> OVERDUE
                                            </span>
                                        @else
                                            <span class="bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px]">
                                                <i class="fas fa-check mr-1"></i> ON TRACK
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada aktivitas pekerjaan tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== TAB 3: LOG ESKALASI ==================== -->
            <div id="content-tab-eskalasi" class="hidden space-y-4">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-xs whitespace-nowrap">
                        <thead class="bg-slate-50 text-gray-600 font-semibold border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Issue No</th>
                                <th class="px-4 py-3">Dari Jenjang</th>
                                <th class="px-4 py-3">Ke Jenjang</th>
                                <th class="px-4 py-3">Pengeskalasi</th>
                                <th class="px-4 py-3">Alasan / Batas Wewenang</th>
                                <th class="px-4 py-3">Waktu Eskalasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($recentEscalations as $esc)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 font-mono font-bold text-blue-600">
                                        {{ $esc->issue->issue_no ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-gray-600">Level {{ $esc->from_level }}</td>
                                    <td class="px-4 py-3">
                                        <span class="bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded text-[10px]">
                                            &rarr; Level {{ $esc->to_level }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $esc->fromUser->name ?? 'Pejabat' }}</td>
                                    <td class="px-4 py-3 text-gray-700 max-w-sm truncate" title="{{ $esc->reason }}">
                                        {{ $esc->reason }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-gray-400 text-[11px]">{{ $esc->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada riwayat eskalasi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
function switchReportTab(tabId) {
    const tabs = ['tab-keputusan', 'tab-pekerjaan', 'tab-eskalasi'];
    tabs.forEach(t => {
        const content = document.getElementById('content-' + t);
        const btn = document.getElementById('btn-' + t);
        if (content && btn) {
            if (t === tabId) {
                content.classList.remove('hidden');
                btn.className = 'pb-3 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2';
            } else {
                content.classList.add('hidden');
                btn.className = 'pb-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2';
            }
        }
    });
}
</script>
@endsection
