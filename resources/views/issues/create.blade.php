@extends('layouts.app')

@section('title', 'Lapor Eskalasi Baru')
@section('page_title', 'Form Pelaporan Eskalasi Baru')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-gray-200 shadow-sm p-8">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h3 class="text-base font-bold text-gray-800">Form Laporan Eskalasi Operasional</h3>
        <p class="text-xs text-gray-500">Eskalasi yang Anda buat akan langsung masuk ke review Team Leader (TL) seksi Anda.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5"><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan pengisian:</div>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('issues.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul Eskalasi <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Terjadi penurunan tekanan minyak pelumas turbin unit 2" class="w-full text-xs border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Tipe Eskalasi <span class="text-rose-500">*</span></label>
                <select name="issue_type" required class="w-full text-xs border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="PROBLEM" {{ old('issue_type') == 'PROBLEM' ? 'selected' : '' }}>PROBLEM (Kendala Operasi / Teknis)</option>
                    <option value="DECISION_REQUIRED" {{ old('issue_type') == 'DECISION_REQUIRED' ? 'selected' : '' }}>DECISION_REQUIRED (Perlu Keputusan)</option>
                    <option value="ACTION" {{ old('issue_type') == 'ACTION' ? 'selected' : '' }}>ACTION (Tindakan Lanjutan)</option>
                    <option value="INFORMATION" {{ old('issue_type') == 'INFORMATION' ? 'selected' : '' }}>INFORMATION (Pemberitahuan Penting)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Tingkat Prioritas <span class="text-rose-500">*</span></label>
                <select name="priority" required class="w-full text-xs border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>LOW (Rendah)</option>
                    <option value="MEDIUM" {{ old('priority', 'MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>MEDIUM (Sedang)</option>
                    <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>HIGH (Tinggi)</option>
                    <option value="CRITICAL" {{ old('priority') == 'CRITICAL' ? 'selected' : '' }}>CRITICAL (Kritis / Dampak Pembangkit)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Target Penyelesaian (Due Date)</label>
            <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full text-xs border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi Lengkap Masalah <span class="text-rose-500">*</span></label>
            <textarea name="description" rows="5" required placeholder="Jelaskan secara mendalam kronologi kejadian, indikasi teknis, potensi dampak, dan upaya mitigasi awal..." class="w-full text-xs border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-sky-500 focus:outline-none leading-relaxed">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('issues.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 bg-pln-blue hover:bg-sky-600 text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2">
                <i class="fas fa-paper-plane"></i> Kirim Laporan Eskalasi
            </button>
        </div>
    </form>
</div>
@endsection
