@extends('layouts.app')

@section('title', 'Keputusan')
@section('page_title', 'Daftar Keputusan Atasan')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-xl gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Rekap Keputusan Resmi</h3>
            <p class="text-xs text-gray-500">Daftar keputusan berjenjang dari TL, Asman, dan Manajer Unit Pelaksana</p>
        </div>
        <form method="GET" action="{{ route('keputusan.index') }}" class="flex gap-3">
            <select name="level" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36">
                <option value="">Semua Level</option>
                <option value="TL" {{ request('level') == 'TL' ? 'selected' : '' }}>Level TL</option>
                <option value="ASMAN" {{ request('level') == 'ASMAN' ? 'selected' : '' }}>Level Asman</option>
                <option value="MUP" {{ request('level') == 'MUP' ? 'selected' : '' }}>Level MUP</option>
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 w-12">No</th>
                    <th class="px-6 py-4">Terkait Issue</th>
                    <th class="px-6 py-4">Tingkat Putusan</th>
                    <th class="px-6 py-4">Pernyataan Keputusan</th>
                    <th class="px-6 py-4">Diputuskan Oleh</th>
                    <th class="px-6 py-4">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                @forelse($decisions as $idx => $dec)
                    <tr class="hover:bg-blue-50/40 transition">
                        <td class="px-6 py-4 text-gray-500">{{ $idx + 1 }}</td>
                        <td class="px-6 py-4">
                            @if($dec->issue)
                                <a href="{{ route('issues.show', $dec->issue->id) }}" class="font-bold text-blue-600 hover:underline">
                                    {{ $dec->issue->title }}
                                </a>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $dec->issue->issue_no }}</p>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded text-[10px] font-bold">
                                Level {{ $dec->decision_level }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800 max-w-md truncate">
                            {{ $dec->decision }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $dec->decisionMaker->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $dec->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-blue-50/40 transition">
                        <td class="px-6 py-4 text-gray-500">1</td>
                        <td class="px-6 py-4"><span class="font-bold text-gray-800">Pengadaan spare part strainer</span><p class="text-[10px] text-gray-400">#ISS-2026-0001</p></td>
                        <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded text-[10px] font-bold">Level ASMAN</span></td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Disetujui penggantian darurat dengan vendor lokal terakreditasi</td>
                        <td class="px-6 py-4 text-gray-600">Ahmad Fauzi, ST (ASMAN)</td>
                        <td class="px-6 py-4 text-gray-500">15 Sep 2026</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($decisions->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $decisions->links() }}
        </div>
    @endif
</div>
@endsection
