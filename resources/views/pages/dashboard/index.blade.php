@extends('layouts.app')

@section('title', 'Dashboard - Sistem Koordinasi')

@section('content')
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Card 1 -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium mb-2">Total Pekerjaan</p>
            <div class="text-3xl font-bold text-gray-800 mb-2">{{ $totalPekerjaan }}</div>
            <p class="text-chart-green text-[11px] font-bold"><i class="fas fa-arrow-up mr-1"></i> Baru</p>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-xs font-medium mb-2">On Progress</p>
            <div class="text-3xl font-bold text-chart-blue mb-2">{{ $pekerjaanProgress }}</div>
        </div>
        <!-- Lanjutkan card lain sesuai desain UI... -->
    </div>

    <!-- Grafik & Agenda Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tempatkan div chart donut dan agenda dari index.html di sini -->
    </div>
@endsection