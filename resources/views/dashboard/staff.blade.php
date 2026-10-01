@extends('layouts.app')

@section('title', 'Dashboard Staff')
@section('page_title', 'Dashboard Pekerjaan Staff')

@section('content')
<!-- 4 Card KPI -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-5">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tugas Open</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $openCount }}</h3>
        </div>
        <div class="w-12 h-12 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xl">
            <i class="fas fa-folder-open"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">In Progress</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $inProgressCount }}</h3>
        </div>
        <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
            <i class="fas fa-spinner"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Overdue</p>
            <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $overdueCount }}</h3>
        </div>
        <div class="w-12 h-12 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
            <i class="fas fa-clock"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tuntas / Closed</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $closedCount }}</h3>
        </div>
        <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
</div>

<!-- Tabel Pekerjaan Saya -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold text-gray-800">Daftar Pekerjaan Saya</h3>
            <p class="text-xs text-gray-500">Tugas yang dibebankan kepada Anda</p>
        </div>
        <a href="{{ route('issues.create') }}" class="px-3.5 py-2 bg-pln-blue text-white rounded-lg text-xs font-medium hover:bg-sky-600 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Laporkan Isu Baru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-gray-600 uppercase font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3.5">Judul Pekerjaan / Tugas</th>
                    <th class="px-6 py-3.5">Ref Isu</th>
                    <th class="px-6 py-3.5">Batas Waktu</th>
                    <th class="px-6 py-3.5">Progress</th>
                    <th class="px-6 py-3.5">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($myTasks as $task)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            {{ $task->title }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            @if($task->issue)
                                <a href="{{ route('issues.show', $task->issue->id) }}" class="text-sky-600 hover:underline font-mono">
                                    {{ $task->issue->issue_no }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 {{ $task->due_date && $task->due_date < now()->toDateString() && $task->status !== 'Closed' ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 w-48">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-sky-600 h-2 rounded-full" style="width: {{ $task->progress_percent }}%"></div>
                                </div>
                                <span class="text-[11px] font-semibold text-gray-600">{{ $task->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusBadge = match($task->status) {
                                    'Open' => 'bg-slate-100 text-slate-700',
                                    'In Progress' => 'bg-amber-100 text-amber-800',
                                    'Blocked' => 'bg-rose-100 text-rose-800',
                                    'Resolved', 'Closed' => 'bg-emerald-100 text-emerald-800',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $statusBadge }}">
                                {{ $task->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            Belum ada pekerjaan yang dibebankan kepada Anda saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
