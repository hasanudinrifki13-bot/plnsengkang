@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<!-- ==================== HEADER BANNER & SIKLUS FILTER ==================== -->
<div class="bg-[#0f2744] text-white p-5 rounded-2xl shadow-md border border-slate-700 relative overflow-hidden mb-6">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-blue-600/80 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">TL Workspace</span>
                <span class="text-[11px] text-emerald-400 font-medium flex items-center gap-1">
                    <i class="fas fa-circle text-emerald-400 text-[8px] animate-pulse"></i> UP Sengkang Monitoring Real-Time
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">DASHBOARD MONITORING & OPERASIONAL TL</h2>
            <p class="text-xs text-sky-200 mt-1 font-medium">Monitoring terpadu tugas staf, evaluasi isu seksi, dan progres pekerjaan mingguan</p>
        </div>
        
        <!-- Siklus Koordinasi Selector -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 bg-slate-800/80 p-2.5 rounded-xl border border-slate-600/60 shrink-0">
            <div class="flex items-center gap-1.5 text-xs text-slate-300 font-medium mr-1">
                <i class="far fa-calendar-alt text-sky-400"></i>
                <span>Siklus:</span>
                <span class="bg-blue-500/20 text-sky-300 font-mono font-bold px-2 py-0.5 rounded text-[11px] border border-sky-400/30">
                    W{{ $manajemenTugas['minggu_berjalan'] ?? now()->isoWeek() }}
                </span>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('dashboard', ['siklus' => 'semua']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? 'semua') === 'semua' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-700/60 hover:bg-slate-700 text-slate-300' }}">
                    Semua
                </a>
                <a href="{{ route('dashboard', ['siklus' => 'N']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? '') === 'N' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-700/60 hover:bg-slate-700 text-slate-300' }}">
                    Minggu N
                </a>
                <a href="{{ route('dashboard', ['siklus' => 'N-1']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? '') === 'N-1' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-700/60 hover:bg-slate-700 text-slate-300' }}">
                    Minggu N-1
                </a>
            </div>
        </div>
    </div>
</div>

<div class="space-y-6">
<!-- ==================== 5 CARD KPI PERSIS MOCKUP PANEL 2 (RESPONSIVE) ==================== -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
    <!-- Card 1: Total Pekerjaan -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs font-medium mb-2">Total Pekerjaan</p>
        <div class="text-3xl font-bold text-gray-800 mb-2">{{ $kpiData['total_pekerjaan'] }}</div>
        <p class="text-chart-green text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> 2 dari minggu lalu
        </p>
    </div>

    <!-- Card 2: On Progress -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs font-medium mb-2">On Progress</p>
        <div class="text-3xl font-bold text-chart-blue mb-2">{{ $kpiData['on_progress'] }}</div>
        <p class="text-gray-400 text-[11px] font-medium">Dalam pengerjaan</p>
    </div>

    <!-- Card 3: Selesai -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs font-medium mb-2">Selesai</p>
        <div class="text-3xl font-bold text-chart-green mb-2">{{ $kpiData['selesai'] }}</div>
        <p class="text-chart-green text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> 33%
        </p>
    </div>

    <!-- Card 4: Terlambat -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs font-medium mb-2">Terlambat</p>
        <div class="text-3xl font-bold text-chart-red mb-2">{{ $kpiData['terlambat'] }}</div>
        <p class="text-chart-red text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-arrow-down"></i> 1 dari minggu lalu
        </p>
    </div>

    <!-- Card 5: Issue Saya -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs font-medium mb-2">Issue Saya</p>
        <div class="text-3xl font-bold text-chart-red mb-2">{{ $kpiData['issue_saya'] }}</div>
        <p class="text-chart-red text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> 1 baru
        </p>
    </div>
</div>

<!-- ==================== GRAFIK & AGENDA PERSIS MOCKUP PANEL 2 ==================== -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
    
    <!-- 1. Donut Chart: Status Pekerjaan -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
        <h3 class="font-bold text-gray-800 text-sm mb-4">Status Pekerjaan</h3>
        
        <div class="flex items-center gap-5 my-auto">
            <!-- Canvas Chart.js dengan Text Donut di Tengah -->
            <div class="relative w-32 h-32 shrink-0 flex items-center justify-center">
                <canvas id="donutStatusChart" class="w-full h-full"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xl font-bold text-gray-800 leading-none">{{ $kpiData['total_pekerjaan'] }}</span>
                    <span class="text-[9px] text-gray-500 font-medium mt-0.5">Pekerjaan</span>
                </div>
            </div>

            <!-- Legend Persis Mockup -->
            <div class="space-y-3 text-xs w-full">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-chart-green"></span>
                        <span class="text-gray-600">Selesai</span>
                    </div>
                    <span class="text-gray-600 font-semibold">{{ $kpiData['chart_status']['selesai'] }} (33%)</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-chart-blue"></span>
                        <span class="text-gray-600">On Progress</span>
                    </div>
                    <span class="text-gray-600 font-semibold">{{ $kpiData['chart_status']['on_progress'] }} (58%)</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-chart-red"></span>
                        <span class="text-gray-600">Terlambat</span>
                    </div>
                    <span class="text-gray-600 font-semibold">{{ $kpiData['chart_status']['terlambat'] }} (9%)</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 2. Bar Chart: Prioritas Issue -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
        <h3 class="font-bold text-gray-800 text-sm mb-2">Prioritas Issue</h3>
        <div class="relative h-44 w-full pt-2">
            <canvas id="barPriorityChart"></canvas>
        </div>
    </div>

    <!-- 3. Agenda Minggu Ini Persis Mockup -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-gray-800 text-sm">Agenda Minggu Ini</h3>
            <a href="#" class="text-[11px] text-blue-600 hover:text-blue-800 hover:underline font-medium">Lihat Semua</a>
        </div>
        
        <div class="space-y-4">
            <!-- Hari Senin -->
            <div>
                <p class="text-xs font-bold text-gray-800 mb-2">Senin, 15 Sep 2026</p>
                <div class="space-y-2.5">
                    <div class="border-l-[3px] border-chart-blue pl-3 flex gap-3">
                        <span class="text-[11px] text-gray-500 w-10 shrink-0 font-medium">08:00</span>
                        <div>
                            <p class="text-xs text-gray-800 font-bold leading-tight">Rapat TL + Staf</p>
                            <p class="font-normal text-gray-500 text-[10px] mt-0.5">Ruang Operasi</p>
                        </div>
                    </div>
                    <div class="border-l-[3px] border-chart-blue pl-3 flex gap-3">
                        <span class="text-[11px] text-gray-500 w-10 shrink-0 font-medium">13:00</span>
                        <div>
                            <p class="text-xs text-gray-800 font-bold leading-tight">Rapat Asman + TL</p>
                            <p class="font-normal text-gray-500 text-[10px] mt-0.5">Ruang Rapat Utama</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hari Selasa -->
            <div>
                <p class="text-xs font-bold text-gray-800 mb-2">Selasa, 16 Sep 2026</p>
                <div class="space-y-2.5">
                    <div class="border-l-[3px] border-chart-red pl-3 flex gap-3">
                        <span class="text-[11px] text-gray-500 w-10 shrink-0 font-medium">08:00</span>
                        <div>
                            <p class="text-xs text-gray-800 font-bold leading-tight">Rapat Tinjauan Manajemen</p>
                            <p class="font-normal text-gray-500 text-[10px] mt-0.5">Ruang Rapat Utama</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ==================== TABEL PEKERJAAN CEPAT / TASK TEAM ==================== -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mt-6">
    <div class="p-5 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="font-bold text-gray-800 text-sm">Monitoring Pekerjaan Terkini</h3>
            <p class="text-xs text-gray-500">Progres harian operasional dan pemeliharaan</p>
        </div>
        <a href="{{ route('pekerjaan') }}" class="text-xs text-blue-600 hover:underline font-semibold flex items-center gap-1">
            Lihat Semua Pekerjaan <i class="fas fa-arrow-right text-[10px]"></i>
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
                <tr>
                    <th class="px-6 py-3.5 w-12">No</th>
                    <th class="px-6 py-3.5">Judul Pekerjaan</th>
                    <th class="px-6 py-3.5">PIC</th>
                    <th class="px-6 py-3.5">Target</th>
                    <th class="px-6 py-3.5">Progres</th>
                    <th class="px-6 py-3.5">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                @forelse($tasks->take(4) as $idx => $t)
                    <tr class="hover:bg-blue-50/40 transition">
                        <td class="px-6 py-3.5 text-gray-500">{{ $idx + 1 }}</td>
                        <td class="px-6 py-3.5 font-bold text-gray-800">{{ $t->title }}</td>
                        <td class="px-6 py-3.5 text-gray-600">{{ $t->assignee->name ?? 'Staff Pelaksana' }}</td>
                        <td class="px-6 py-3.5 text-gray-500">{{ $t->due_date ? \Carbon\Carbon::parse($t->due_date)->format('d M Y') : '15 Sep 2026' }}</td>
                        <td class="px-6 py-3.5 w-40">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $t->progress_percent }}%"></div>
                                </div>
                                <span class="text-[10px] text-gray-500">{{ $t->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            @if($t->progress_percent >= 100)
                                <span class="border border-green-200 text-green-600 bg-green-50 px-2.5 py-0.5 rounded-md text-[10px] font-bold">Selesai</span>
                            @elseif($t->due_date && $t->due_date < now()->toDateString())
                                <span class="border border-red-200 text-red-600 bg-red-50 px-2.5 py-0.5 rounded-md text-[10px] font-bold">Terlambat</span>
                            @else
                                <span class="border border-blue-200 text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-md text-[10px] font-bold">On Progress</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-400">Belum ada tugas operasional aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== MONITORING MANAJEMEN TUGAS TERPADU ==================== -->
<div class="mt-6">
    @include('dashboard.partials.monitoring_manajemen_tugas')
</div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Inisialisasi Donut Chart: Status Pekerjaan
        const ctxDonut = document.getElementById('donutStatusChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'On Progress', 'Terlambat'],
                    datasets: [{
                        data: [
                            {{ $kpiData['chart_status']['selesai'] }},
                            {{ $kpiData['chart_status']['on_progress'] }},
                            {{ $kpiData['chart_status']['terlambat'] }}
                        ],
                        backgroundColor: ['#10b981', '#3b82f6', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.raw + ' Pekerjaan';
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Inisialisasi Bar Chart: Prioritas Issue
        const ctxBar = document.getElementById('barPriorityChart');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['High', 'Medium', 'Low'],
                    datasets: [{
                        data: [
                            {{ $kpiData['chart_priority']['high'] }},
                            {{ $kpiData['chart_priority']['medium'] }},
                            {{ $kpiData['chart_priority']['low'] }}
                        ],
                        backgroundColor: ['#ef4444', '#f59e0b', '#cbd5e1'],
                        borderRadius: 4,
                        barThickness: 34
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 3,
                            ticks: { stepSize: 1, font: { size: 10 } },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10, weight: '500' } }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endpush
