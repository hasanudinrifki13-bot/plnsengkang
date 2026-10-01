@extends('layouts.app')

@section('title', 'Dashboard Asisten Manajer')
@section('page_title', 'Dashboard Asisten Manajer (ASMAN)')

@section('content')
<div class="space-y-6">

    <!-- ==================== HEADER BANNER & SIKLUS FILTER ==================== -->
    <div class="bg-[#0f2744] text-white p-5 rounded-2xl shadow-md border border-slate-700 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-indigo-600/80 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">ASMAN Workspace</span>
                    <span class="text-[11px] text-emerald-400 font-medium flex items-center gap-1">
                        <i class="fas fa-circle text-emerald-400 text-[8px] animate-pulse"></i> UP Sengkang Monitoring Real-Time
                    </span>
                </div>
                <h2 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">DASHBOARD MONITORING & RINGKASAN ASMAN</h2>
                <p class="text-xs text-sky-200 mt-1 font-medium">Monitoring terpadu eskalasi bidang, review kendala Team Leader, dan manajemen tugas operasional</p>
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

    <!-- 3 Card KPI Khusus ASMAN -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Isu Dieskalasi ke ASMAN</p>
                <h3 class="text-3xl font-bold text-rose-700 mt-1">{{ $escalatedIssues->count() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                <i class="fas fa-arrow-up"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menunggu Keputusan / Aksi</p>
                <h3 class="text-3xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-gavel"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tingkat Kewenangan</p>
                <h3 class="text-xl font-bold text-indigo-700 mt-1">Level 2 (Bidang)</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fas fa-sitemap"></i>
            </div>
        </div>
    </div>

    <!-- Daftar Isu Dieskalasi dari TL -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Daftar Isu Dieskalasi dari Team Leader</h3>
                <p class="text-xs text-gray-500">Isu yang melampaui batas kewenangan TL dan memerlukan putusan Asman atau eskalasi ke MUP</p>
            </div>
            <span class="text-xs bg-rose-50 text-rose-700 border border-rose-200 font-bold px-2.5 py-1 rounded-lg">
                {{ $escalatedIssues->count() }} Isu Masuk
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-gray-600 uppercase font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3.5">No. Isu</th>
                        <th class="px-6 py-3.5">Judul Isu</th>
                        <th class="px-6 py-3.5">Dieskalasi Oleh</th>
                        <th class="px-6 py-3.5">Prioritas</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($escalatedIssues as $issue)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 font-mono font-bold text-rose-700">
                                #{{ $issue->issue_no }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                {{ $issue->title }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $issue->escalations->first()?->fromUser->name ?? ($issue->reporter->name ?? '-') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badge = match($issue->priority) {
                                        'CRITICAL' => 'bg-rose-100 text-rose-800',
                                        'HIGH' => 'bg-amber-100 text-amber-800',
                                        'MEDIUM' => 'bg-sky-100 text-sky-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $badge }}">{{ $issue->priority }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                    {{ $issue->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('issues.show', $issue->id) }}" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm">
                                    Tindak Lanjut <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fas fa-check-circle text-3xl mb-2 text-emerald-400 block"></i>
                                Tidak ada isu yang dieskalasi ke tingkat ASMAN saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== MONITORING MANAJEMEN TUGAS TERPADU ==================== -->
    <div class="mt-4">
        @include('dashboard.partials.monitoring_manajemen_tugas')
    </div>

</div>
@endsection
