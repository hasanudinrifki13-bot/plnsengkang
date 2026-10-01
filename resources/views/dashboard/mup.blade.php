@extends('layouts.app')

@section('title', 'Dashboard Unit MUP')
@section('page_title', 'Dashboard Unit')

@section('content')
<div class="space-y-6">

    <!-- ==================== HEADER BANNER & SIKLUS FILTER (TERPADU) ==================== -->
    <div class="bg-[#0f2744] text-white p-5 rounded-2xl shadow-md border border-slate-700 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-blue-600/80 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Manager Unit Workspace</span>
                    <span class="text-[11px] text-emerald-400 font-medium flex items-center gap-1">
                        <i class="fas fa-circle text-emerald-400 text-[8px] animate-pulse"></i> UP Sengkang Monitoring Real-Time
                    </span>
                </div>
                <h2 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">DASHBOARD MONITORING & RINGKASAN EKSEKUTIF</h2>
                <p class="text-xs text-sky-200 mt-1 font-medium">Monitoring terpadu rapat koordinasi, progres pekerjaan, eskalasi berjenjang, dan keputusan strategis unit</p>
            </div>
            
            <!-- Siklus Koordinasi Selector (BR-09: ISO-8601) -->
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
                        Semua Siklus
                    </a>
                    <a href="{{ route('dashboard', ['siklus' => 'N']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? '') === 'N' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-700/60 hover:bg-slate-700 text-slate-300' }}">
                        <i class="fas fa-play-circle mr-1"></i> Minggu N
                    </a>
                    <a href="{{ route('dashboard', ['siklus' => 'N-1']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? '') === 'N-1' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-700/60 hover:bg-slate-700 text-slate-300' }}">
                        <i class="fas fa-history mr-1"></i> Minggu N-1
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 5 KARTU KPI UTAMA (GABUNGAN TERPADU) ==================== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <!-- 1. PEMBAHASAN (BIRU) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-blue-200 bg-white flex flex-col hover:shadow-md transition">
            <div class="bg-[#1e40af] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                PEMBAHASAN
            </div>
            <div class="p-5 text-center my-auto">
                <span class="text-3xl md:text-4xl font-black text-gray-800">{{ $manajemenTugas['kpi']['pembahasan'] ?? $kpiData['total_pekerjaan'] }}</span>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Total Isu & Pekerjaan</p>
            </div>
        </div>

        <!-- 2. OPEN (ORANYE) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-orange-200 bg-white flex flex-col hover:shadow-md transition">
            <div class="bg-[#ea580c] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                OPEN
            </div>
            <div class="p-5 text-center my-auto">
                <span class="text-3xl md:text-4xl font-black text-gray-800">{{ $manajemenTugas['kpi']['open'] }}</span>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Menunggu Eksekusi</p>
            </div>
        </div>

        <!-- 3. ON PROGRESS (HIJAU) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-emerald-200 bg-white flex flex-col hover:shadow-md transition">
            <div class="bg-[#16a34a] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                ON PROGRESS
            </div>
            <div class="p-5 text-center my-auto">
                <span class="text-3xl md:text-4xl font-black text-gray-800">{{ $manajemenTugas['kpi']['on_progress'] }}</span>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">Dalam Pengerjaan</p>
            </div>
        </div>

        <!-- 4. OVERDUE / ALERT (MERAH) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-red-200 bg-white flex flex-col hover:shadow-md transition">
            <div class="bg-[#dc2626] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                OVERDUE / ALERT
            </div>
            <div class="p-5 text-center my-auto">
                <span class="text-3xl md:text-4xl font-black text-red-600">{{ $manajemenTugas['kpi']['overdue'] }}</span>
                <p class="text-[10px] text-red-500 font-semibold mt-1">Perlu Perhatian Khusus</p>
            </div>
        </div>

        <!-- 5. KEPUTUSAN MUP (UNGU) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-purple-200 bg-white flex flex-col col-span-2 sm:col-span-1 hover:shadow-md transition">
            <div class="bg-purple-700 text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                KEPUTUSAN MUP
            </div>
            <div class="p-5 text-center my-auto">
                <span class="text-3xl md:text-4xl font-black text-purple-700">{{ $criticalIssues->count() }}</span>
                <p class="text-[10px] text-purple-500 font-semibold mt-1">Isu Level MUP / Kritis</p>
            </div>
        </div>
    </div>

    <!-- ==================== RINGKASAN EKSEKUTIF MUP: STATUS PEKERJAAN & TOP ISU ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Kolom Kiri: Status Pekerjaan Unit (Doughnut Chart Dinamis) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Status Pekerjaan Unit (Tingkat Selesai)</h3>
                    <p class="text-[11px] text-gray-400">Distribusi status seluruh penugasan operasional di UP Sengkang</p>
                </div>
                <span class="text-xs bg-slate-100 text-slate-700 font-bold px-2.5 py-1 rounded-lg">
                    Total: {{ $kpiData['total_pekerjaan'] }}
                </span>
            </div>
            
            <div class="flex items-center gap-6 my-auto">
                <!-- Doughnut Canvas -->
                <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
                    <canvas id="donutUnitChart" class="w-full h-full"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-black text-gray-800 leading-none">{{ $kpiData['total_pekerjaan'] }}</span>
                        <span class="text-[10px] text-gray-400 font-semibold mt-0.5">Pekerjaan</span>
                    </div>
                </div>

                <!-- Legend Dinamis -->
                <div class="space-y-2.5 text-xs w-full">
                    @php
                        $tot = max(1, $kpiData['total_pekerjaan']);
                        $pctSelesai = round(($kpiData['selesai'] / $tot) * 100);
                        $pctProgress = round(($kpiData['on_progress'] / $tot) * 100);
                        $pctTerlambat = round(($kpiData['terlambat'] / $tot) * 100);
                    @endphp
                    <div class="flex justify-between items-center p-2 rounded-lg bg-emerald-50/50 border border-emerald-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-gray-700 font-medium">Selesai</span>
                        </div>
                        <span class="text-emerald-700 font-bold font-mono">{{ $kpiData['selesai'] }} ({{ $pctSelesai }}%)</span>
                    </div>
                    <div class="flex justify-between items-center p-2 rounded-lg bg-blue-50/50 border border-blue-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-gray-700 font-medium">On Progress</span>
                        </div>
                        <span class="text-blue-700 font-bold font-mono">{{ $kpiData['on_progress'] }} ({{ $pctProgress }}%)</span>
                    </div>
                    <div class="flex justify-between items-center p-2 rounded-lg bg-rose-50/50 border border-rose-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-gray-700 font-medium">Terlambat</span>
                        </div>
                        <span class="text-rose-700 font-bold font-mono">{{ $kpiData['terlambat'] }} ({{ $pctTerlambat }}%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Top Isu Perlu Keputusan MUP (Dinamis dari DB) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fas fa-gavel text-purple-600"></i> Top Isu Perlu Keputusan MUP
                    </h3>
                    <p class="text-[11px] text-gray-400">Isu dengan prioritas kritis atau dieskalasikan ke Manager Unit</p>
                </div>
                <a href="{{ route('issues.index', ['level' => 'MUP']) }}" class="text-xs text-blue-600 hover:underline font-bold flex items-center gap-1">
                    Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-2.5 flex-1 flex flex-col justify-center">
                @forelse($criticalIssues->take(4) as $idx => $cIssue)
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 text-xs font-bold flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <a href="{{ route('issues.show', $cIssue->id) }}" class="text-xs font-bold text-gray-800 hover:text-blue-600 line-clamp-1">
                                    {{ $cIssue->title }}
                                </a>
                                <p class="text-[10px] text-gray-400">
                                    Pelapor: {{ $cIssue->reporter->name ?? 'Staff' }} &bull; Level: <span class="font-bold text-purple-600">{{ $cIssue->current_level }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(in_array($cIssue->priority, ['CRITICAL', 'HIGH']))
                                <span class="text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded text-[10px] font-bold">Kritis</span>
                            @else
                                <span class="text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded text-[10px] font-bold">{{ $cIssue->priority }}</span>
                            @endif
                            <a href="{{ route('issues.show', $cIssue->id) }}" class="px-2.5 py-1 bg-purple-50 hover:bg-purple-600 text-purple-600 hover:text-white rounded-lg text-[11px] font-bold transition">
                                Putuskan
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-check-double text-3xl mb-2 text-emerald-400 block"></i>
                        <p class="text-xs font-semibold text-gray-600">Tidak ada isu pending yang memerlukan putusan MUP.</p>
                        <p class="text-[11px] text-gray-400">Seluruh kendala unit terkendali pada level TL dan ASMAN.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ==================== 4 PANEL MATRIKS MONITORING MANAJEMEN TUGAS ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- PANEL 1: STATUS PEKERJAAN (TABEL + CHART) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#1e3a8a] text-white px-5 py-3 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                <span>STATUS PEKERJAAN</span>
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded font-mono">Tugas Operasional</span>
            </div>
            <div class="p-5 space-y-4 flex-1 flex flex-col justify-between">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-2 font-bold">Status</th>
                            <th class="pb-2 font-bold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        <tr>
                            <td class="py-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span> Open (Baru)
                            </td>
                            <td class="py-2 text-right font-mono font-bold">{{ $manajemenTugas['status_pekerjaan']['open'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Assigned / On Progress
                            </td>
                            <td class="py-2 text-right font-mono font-bold">{{ $manajemenTugas['status_pekerjaan']['in_progress'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Closed / Selesai
                            </td>
                            <td class="py-2 text-right font-mono font-bold">{{ $manajemenTugas['status_pekerjaan']['closed'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Mini Bar Chart -->
                <div class="bg-slate-50 p-3 rounded-xl border border-gray-100 h-44 flex flex-col justify-end">
                    <p class="text-[10px] text-gray-400 font-bold mb-2 text-center">Grafik Distribusi Status Pekerjaan</p>
                    <div class="h-32 flex items-end justify-around gap-2 px-6">
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_pekerjaan']['open'] }}</span>
                            <div class="w-full bg-blue-600 rounded-t transition-all duration-500" style="height: {{ max(10, min(100, $manajemenTugas['status_pekerjaan']['open'] * 15)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Open</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_pekerjaan']['in_progress'] }}</span>
                            <div class="w-full bg-amber-500 rounded-t transition-all duration-500" style="height: {{ max(10, min(100, $manajemenTugas['status_pekerjaan']['in_progress'] * 15)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Progress</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_pekerjaan']['closed'] }}</span>
                            <div class="w-full bg-emerald-600 rounded-t transition-all duration-500" style="height: {{ max(10, min(100, $manajemenTugas['status_pekerjaan']['closed'] * 15)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 2: STATUS ESKALASI (TABEL + CHART) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#1e3a8a] text-white px-5 py-3 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                <span>STATUS ESKALASI</span>
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded font-mono">Hirarki Solusi</span>
            </div>
            <div class="p-5 space-y-4 flex-1 flex flex-col justify-between">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-2 font-bold">Status Eskalasi</th>
                            <th class="pb-2 font-bold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        <tr>
                            <td class="py-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-500"></span> Closed - As Information
                            </td>
                            <td class="py-2 text-right font-mono font-bold">{{ $manajemenTugas['status_eskalasi']['closed_info'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span> Escalated - Level 1 Up (TL/ASMAN)
                            </td>
                            <td class="py-2 text-right font-mono font-bold">{{ $manajemenTugas['status_eskalasi']['escalated_level1'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span> Escalated - MUP (Puncak)
                            </td>
                            <td class="py-2 text-right font-mono font-bold">{{ $manajemenTugas['status_eskalasi']['escalated_mup'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Mini Bar Chart -->
                <div class="bg-slate-50 p-3 rounded-xl border border-gray-100 h-44 flex flex-col justify-end">
                    <p class="text-[10px] text-gray-400 font-bold mb-2 text-center">Grafik Status Eskalasi Berjenjang</p>
                    <div class="h-32 flex items-end justify-around gap-2 px-6">
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_eskalasi']['closed_info'] }}</span>
                            <div class="w-full bg-slate-500 rounded-t transition-all duration-500" style="height: {{ max(10, min(100, $manajemenTugas['status_eskalasi']['closed_info'] * 20)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Info/Closed</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_eskalasi']['escalated_level1'] }}</span>
                            <div class="w-full bg-blue-600 rounded-t transition-all duration-500" style="height: {{ max(10, min(100, $manajemenTugas['status_eskalasi']['escalated_level1'] * 20)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Level 1 Up</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_eskalasi']['escalated_mup'] }}</span>
                            <div class="w-full bg-purple-600 rounded-t transition-all duration-500" style="height: {{ max(10, min(100, $manajemenTugas['status_eskalasi']['escalated_mup'] * 20)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Level MUP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 3: DEADLINE (ORANYE) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#ea580c] text-white px-5 py-3 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                <span>DEADLINE & KEPATUHAN WAKTU</span>
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded font-mono">SLA Monitoring</span>
            </div>
            <div class="p-5 flex-1">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-2.5 font-bold">Kategori Waktu</th>
                            <th class="pb-2.5 font-bold text-right">Jumlah Tugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Overdue (Terlambat)
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-red-600">{{ $manajemenTugas['deadline']['overdue'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-400"></span> Due &le; 3 Days (Mendekati Tenggat)
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-orange-600">{{ $manajemenTugas['deadline']['due_3_days'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span> On Track (Sesuai Jadwal)
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-blue-600">{{ $manajemenTugas['deadline']['on_track'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Closed / Selesai Tepat Waktu
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-emerald-600">{{ $manajemenTugas['deadline']['closed'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PANEL 4: MONITORING PIC (HIJAU) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#16a34a] text-white px-5 py-3 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                <span>MONITORING PIC</span>
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded font-mono">Beban Kerja</span>
            </div>
            <div class="p-5 flex-1">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-2.5 font-bold w-1/3">PIC Pelaksana</th>
                            <th class="pb-2.5 font-bold">Tugas & Instruksi Kerja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($manajemenTugas['monitoring_pic'] as $mPic)
                            <tr>
                                <td class="py-2.5 font-bold text-gray-800">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 text-[9px] font-bold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($mPic->assignee->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span>{{ $mPic->assignee->name ?? 'Staff Pelaksana' }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 text-gray-600 line-clamp-1" title="{{ $mPic->title }}">{{ $mPic->title }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-2.5 font-medium">Asman Operasi</td>
                                <td class="py-2.5 text-gray-500">Persetujuan tindak lanjut overhaul</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 font-medium">Team Leader CBM</td>
                                <td class="py-2.5 text-gray-500">Analisis getaran turbin gas blok 1</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 font-medium">Sekretaris Teknik</td>
                                <td class="py-2.5 text-gray-500">Menyusun risalah rapat koordinasi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ==================== DAFTAR PRIORITAS / ALERT (TABEL MONITORING MANAJEMEN TUGAS) ==================== -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Header Merah Tua Persis Sistem Awal -->
        <div class="bg-[#991b1b] text-white px-5 py-3 text-xs font-black uppercase tracking-wider flex items-center justify-between">
            <span class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> DAFTAR PRIORITAS / ALERT (MONITORING MANAJEMEN TUGAS)
            </span>
            <span class="text-[10px] bg-white/20 px-2.5 py-1 rounded-lg font-mono font-bold">
                {{ count($manajemenTugas['prioritas_alert']) }} Item Kritis
            </span>
        </div>

        <!-- Tabel Monitoring Detail -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#1e3a8a] text-white font-bold">
                    <tr>
                        <th class="px-4 py-3 w-10 text-center">No</th>
                        <th class="px-4 py-3">PIC</th>
                        <th class="px-4 py-3">Informasi</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Status Pekerjaan</th>
                        <th class="px-4 py-3">Status Deadline</th>
                        <th class="px-4 py-3 text-center">Eskalasi</th>
                        <th class="px-4 py-3 text-center">Sifat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700 font-medium">
                    @forelse($manajemenTugas['prioritas_alert'] as $idx => $alert)
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="px-4 py-3 text-center text-gray-500 font-bold">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-gray-800">{{ $alert->owner->name ?? ($alert->reporter->name ?? 'Staff') }}</td>
                            <td class="px-4 py-3 text-gray-800 font-semibold max-w-xs truncate" title="{{ $alert->title }}">
                                <a href="{{ route('issues.show', $alert->id) }}" class="hover:text-blue-600 hover:underline">
                                    {{ $alert->title }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $alert->description }}">
                                {{ Str::limit($alert->description, 35) }}
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-600">
                                {{ $alert->due_date ? \Carbon\Carbon::parse($alert->due_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if(in_array($alert->status, ['Resolved', 'Closed', 'Selesai']))
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold">Closed</span>
                                @elseif($alert->status === 'Open')
                                    <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold">Open</span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded text-[10px] font-bold">On Progress</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $isOverdue = $alert->due_date && $alert->due_date < now()->toDateString() && !in_array($alert->status, ['Resolved', 'Closed']);
                                    $isDueSoon = $alert->due_date && $alert->due_date >= now()->toDateString() && $alert->due_date <= now()->addDays(3)->toDateString();
                                @endphp
                                @if($isOverdue)
                                    <span class="bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 w-max">
                                        <i class="fas fa-clock text-[9px]"></i> Overdue
                                    </span>
                                @elseif($isDueSoon)
                                    <span class="bg-orange-50 text-orange-600 border border-orange-200 px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 w-max">
                                        <i class="fas fa-exclamation text-[9px]"></i> Due &le; 3 Days
                                    </span>
                                @else
                                    <span class="bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 w-max">
                                        <i class="fas fa-check text-[9px]"></i> On Track
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-slate-100 text-slate-800 font-bold px-2 py-0.5 rounded text-[10px] border border-slate-200">
                                    {{ $alert->current_level }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(in_array($alert->priority, ['CRITICAL', 'HIGH']))
                                    <span class="bg-red-500 text-white font-black px-2 py-0.5 rounded text-[9px] uppercase">
                                        Kritis
                                    </span>
                                @elseif($alert->priority === 'MEDIUM')
                                    <span class="bg-amber-400 text-gray-900 font-bold px-2 py-0.5 rounded text-[9px] uppercase">
                                        Mendesak
                                    </span>
                                @else
                                    <span class="bg-slate-200 text-gray-700 font-semibold px-2 py-0.5 rounded text-[9px] uppercase">
                                        Rutin
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400">Tidak ada item alert prioritas saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxDonut = document.getElementById('donutUnitChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'On Progress', 'Terlambat'],
                    datasets: [{
                        data: [
                            {{ $kpiData['selesai'] ?? 0 }},
                            {{ $kpiData['on_progress'] ?? 0 }},
                            {{ $kpiData['terlambat'] ?? 0 }}
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
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endpush
