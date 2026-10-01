@extends('layouts.app')

@section('title', 'Daftar Eskalasi')
@section('page_title', 'Daftar Eskalasi')

@section('content')
<!-- Subtitle Sesuai Terminologi Baru -->
<p class="text-xs text-gray-500 -mt-3 mb-4">Catat, pantau, dan selesaikan eskalasi operasional hingga tuntas</p>

<!-- ==================== KONTEN UTAMA: TABEL ISSUE / ESKALASI ==================== -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col">
    
    <!-- Area Filter & Tombol Buat Eskalasi -->
    <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-white rounded-t-xl gap-4">
        <form method="GET" action="{{ route('issues.index') }}" class="flex flex-wrap gap-3 flex-1 w-full">
            <select name="level" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36">
                <option value="">Semua Level</option>
                <option value="TL" {{ request('level') == 'TL' ? 'selected' : '' }}>Level TL</option>
                <option value="ASMAN" {{ request('level') == 'ASMAN' ? 'selected' : '' }}>Level Asman</option>
                <option value="MUP" {{ request('level') == 'MUP' ? 'selected' : '' }}>Level MUP</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36">
                <option value="">Semua Status</option>
                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                <option value="Escalated" {{ request('status') == 'Escalated' ? 'selected' : '' }}>Escalated</option>
                <option value="Need Decision" {{ request('status') == 'Need Decision' ? 'selected' : '' }}>Need Decision</option>
                <option value="Under Review" {{ request('status') == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="siklus" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-48">
                <option value="semua">Semua Siklus</option>
                <option value="N" {{ request('siklus') == 'N' ? 'selected' : '' }}>Minggu N (W{{ now()->isoWeek() }} - Berjalan)</option>
                <option value="N-1" {{ request('siklus') == 'N-1' ? 'selected' : '' }}>Minggu N-1 (W{{ now()->subWeek()->isoWeek() }} - Evaluasi)</option>
            </select>

            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari eskalasi..." class="border border-gray-200 rounded-md pl-8 pr-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-full">
            </div>
            
            @if(request()->hasAny(['level', 'status', 'siklus', 'search']))
                <a href="{{ route('issues.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-md text-xs font-semibold flex items-center gap-1">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>

        <!-- Tombol Pemanggil Modal Buat Eskalasi -->
        <button type="button" data-bs-toggle="modal" data-bs-target="#modalBuatIssue" onclick="openModal('modalBuatIssue')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-xs font-semibold shadow-sm transition flex items-center gap-2 shrink-0">
            <i class="fas fa-plus"></i> Buat Eskalasi
        </button>
    </div>

    <!-- Tabel Eskalasi -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 w-12">No</th>
                    <th class="px-6 py-4">Judul Eskalasi</th>
                    <th class="px-6 py-4">Level Saat Ini</th>
                    <th class="px-6 py-4">Prioritas</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Durasi & SLA</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                @forelse($issues as $idx => $issue)
                    <tr class="hover:bg-blue-50/50 transition">
                        <td class="px-6 py-4 text-gray-500">{{ $issues->firstItem() ? $issues->firstItem() + $idx : $idx + 1 }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('issues.show', $issue->id) }}" class="font-bold text-gray-800 hover:text-blue-600">
                                {{ $issue->title }}
                            </a>
                            <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $issue->issue_no }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-700">{{ $issue->current_level }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if(in_array($issue->priority, ['CRITICAL', 'HIGH']))
                                <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded text-[10px] font-bold">High</span>
                            @elseif($issue->priority == 'MEDIUM')
                                <span class="text-orange-500 bg-orange-50 px-2 py-0.5 rounded text-[10px] font-bold">Medium</span>
                            @else
                                <span class="text-green-500 bg-green-50 px-2 py-0.5 rounded text-[10px] font-bold">Low</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $issue->status;
                                if ($status == 'Open') {
                                    $badge = 'border border-red-200 text-red-600 bg-red-50';
                                } elseif ($status == 'Escalated') {
                                    $badge = 'border border-orange-200 text-orange-600 bg-orange-50';
                                } elseif (in_array($status, ['Waiting Decision', 'Under Review', 'Need Decision'])) {
                                    $badge = 'border border-red-200 text-red-600 bg-red-50';
                                    $status = 'Need Decision';
                                } elseif ($status == 'Closed') {
                                    $badge = 'border border-green-200 text-green-600 bg-green-50';
                                } else {
                                    $badge = 'border border-blue-200 text-blue-600 bg-blue-50';
                                }
                            @endphp
                            <span class="{{ $badge }} px-3 py-1 rounded-md text-[10px] font-bold">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1 items-start">
                                <span class="text-gray-600 text-xs font-semibold">
                                    {{ max(1, (int) round($issue->created_at ? $issue->created_at->diffInDays(now()) : 1)) }} Hari
                                </span>
                                @php $sla = $issue->sla_info; @endphp
                                <span class="{{ $sla['badge_class'] }} px-2 py-0.5 rounded text-[10px] inline-flex items-center gap-1 shadow-xs">
                                    <i class="{{ $sla['icon'] }} text-[9px]"></i> {{ $sla['label'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('issues.show', $issue->id) }}" class="text-gray-400 hover:text-blue-600 p-1 inline-block" title="Lihat Detail & Eskalasi">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            <i class="fas fa-folder-open text-3xl mb-2 text-gray-300"></i>
                            <p class="text-xs">Tidak ada eskalasi yang sesuai dengan filter atau kata kunci pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($issues->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $issues->links() }}
        </div>
    @endif
</div>

<!-- ==================== MODAL FORM BUAT ESKALASI ==================== -->
<div id="modalBuatIssue" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full p-7 shadow-2xl border border-gray-100 modal-container max-h-[90vh] overflow-y-auto">
        
        <!-- Header Modal -->
        <div class="flex justify-between items-center pb-4 border-b border-gray-100 mb-5">
            <h3 class="text-base font-bold text-gray-800">Buat Eskalasi Baru</h3>
            <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalBuatIssue')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg">
                <p class="font-bold mb-1">Periksa kembali data yang dimasukkan:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Eskalasi -->
        <form action="{{ route('issues.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Baris 1: Judul Eskalasi & Lokasi / Seksi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul Eskalasi <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul eskalasi..." class="w-full text-xs border border-gray-200 rounded-lg p-3 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Lokasi / Seksi <span class="text-red-500">*</span></label>
                    <select name="seksi" class="w-full text-xs border border-gray-200 rounded-lg p-3 outline-none focus:border-blue-500 text-gray-600">
                        <option value="Seksi Operasi 1A" selected>Seksi Operasi 1A</option>
                        <option value="Seksi Pemeliharaan Mesin">Seksi Pemeliharaan Mesin</option>
                        <option value="Seksi Listrik & Instrumen">Seksi Listrik & Instrumen</option>
                        <option value="Seksi K3 & Lingkungan">Seksi K3 & Lingkungan</option>
                    </select>
                </div>
            </div>

            <!-- Baris 2: Kategori & Prioritas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="issue_type" required class="w-full text-xs border border-gray-200 rounded-lg p-3 outline-none focus:border-blue-500 text-gray-600">
                        <option value="PROBLEM" selected>PROBLEM (Kendala Lapangan)</option>
                        <option value="DECISION_REQUIRED">DECISION_REQUIRED (Perlu Putusan)</option>
                        <option value="ACTION">ACTION (Tindakan Terjadwal)</option>
                        <option value="INFORMATION">INFORMATION (Pemberitahuan)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                    <select name="priority" required class="w-full text-xs border border-gray-200 rounded-lg p-3 outline-none focus:border-blue-500 text-gray-600">
                        <option value="HIGH">High (Tinggi)</option>
                        <option value="MEDIUM" selected>Medium (Sedang)</option>
                        <option value="LOW">Low (Rendah)</option>
                        <option value="CRITICAL">Critical (Kritis Pembangkit)</option>
                    </select>
                </div>
            </div>

            <!-- Baris 3: Uraian Masalah & Lampiran -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Uraian Masalah <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="5" required placeholder="Jelaskan permasalahan eskalasi secara detail..." class="w-full text-xs border border-gray-200 rounded-lg p-3 outline-none focus:border-blue-500 leading-relaxed">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Lampiran (jika ada)</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-lg p-6 flex flex-col items-center justify-center text-center cursor-pointer hover:border-blue-400 bg-slate-50/50 h-[120px] relative">
                        <input type="file" name="attachment" id="fileInput" onchange="document.getElementById('fileName').innerText = this.files[0]?.name || 'Klik untuk upload'" class="absolute inset-0 opacity-0 cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-2xl text-blue-500 mb-2"></i>
                        <p id="fileName" class="text-xs text-gray-600 font-semibold">Klik untuk upload</p>
                        <p class="text-[10px] text-gray-400">atau drag and drop file</p>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalBuatIssue')" class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-md transition">
                    Simpan Eskalasi
                </button>
            </div>
        </form>

    </div>
</div>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            openModal('modalBuatIssue');
        });
    </script>
@endif
@endsection
