@extends('layouts.app')

@section('title', 'Agenda Rapat Mingguan')
@section('page_title', 'Rapat Koordinasi Mingguan')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                <span class="text-xs font-semibold text-emerald-800">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 text-sm">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-rose-500 text-lg"></i>
                <span class="text-xs font-semibold text-rose-800">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-700 text-sm">&times;</button>
        </div>
    @endif

    <!-- Header Banner & Action Button -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="far fa-calendar-check text-blue-600"></i> Siklus Rapat Koordinasi Mingguan UP Sengkang
            </h2>
            <p class="text-xs text-gray-500 mt-1">Forum evaluasi N-1, perencanaan N, dan pengambilan keputusan di level berwenang (FR-05 & BSD Section 2 & 3).</p>
        </div>
        <button type="button" onclick="openModal('modalBuatRapat')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-2 shrink-0">
            <i class="fas fa-plus"></i> Buat Agenda Rapat
        </button>
    </div>

    <!-- Tabs Navigasi: Jadwal Rapat vs Riwayat Selesai -->
    <div class="flex justify-between items-center border-b border-gray-200">
        <div class="flex gap-4">
            <a href="{{ route('rapat.index', ['tab' => 'jadwal']) }}" class="pb-3 px-3 text-xs font-bold transition flex items-center gap-2 {{ ($tab ?? 'jadwal') === 'jadwal' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="far fa-clock"></i> Jadwal Rapat Aktif / Mendatang
            </a>
            <a href="{{ route('rapat.index', ['tab' => 'riwayat']) }}" class="pb-3 px-3 text-xs font-bold transition flex items-center gap-2 {{ ($tab ?? '') === 'riwayat' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-archive"></i> Riwayat Rapat Selesai
            </a>
        </div>
        <span class="text-xs text-gray-400 pb-2">Total Sesi: <strong>{{ count($meetings) }}</strong></span>
    </div>

    <!-- Daftar Kartu Rapat Dinamis (Loop Database) -->
    <div class="space-y-4">
        @forelse($meetings as $m)
            @php
                $carbonDate = \Carbon\Carbon::parse($m->meeting_date);
                $dayName = strtoupper($carbonDate->isoFormat('ddd'));
                $dayNum = $carbonDate->format('d');
                $monthYear = $carbonDate->isoFormat('MMM Y');
                $isLive = ($m->status === 'Berlangsung');
                $isSelesai = ($m->status === 'Selesai');
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col md:flex-row items-start md:items-center justify-between hover:shadow-md transition gap-4">
                <div class="flex items-center gap-5 w-full">
                    
                    <!-- Kolom Tanggal Kalender -->
                    <div class="flex flex-col items-center justify-center w-20 border-r border-gray-100 pr-5 shrink-0 text-center">
                        <span class="text-[11px] font-bold text-gray-500">{{ $dayName }}</span>
                        <span class="text-3xl font-black text-gray-800 leading-none my-1">{{ $dayNum }}</span>
                        <span class="text-[10px] font-medium text-gray-400">{{ $monthYear }}</span>
                    </div>
                    
                    <!-- Kolom Detail Rapat -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                            <h3 class="text-base font-bold text-gray-800">{{ $m->type }}</h3>
                            @if($isLive)
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 animate-pulse">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span> LIVE
                                </span>
                            @elseif($isSelesai)
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded">
                                    Selesai
                                </span>
                            @else
                                <span class="bg-orange-50 text-orange-600 border border-orange-200 text-[10px] font-bold px-2 py-0.5 rounded">
                                    Akan Datang
                                </span>
                            @endif

                            @if(($m->meeting_mode ?? 'Offline') === 'Online' || !empty($m->zoom_link))
                                <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                                    <i class="fas fa-video text-blue-500"></i> Zoom Meeting
                                </span>
                            @elseif(($m->meeting_mode ?? '') === 'Hybrid')
                                <span class="bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                                    <i class="fas fa-network-wired text-purple-500"></i> Hybrid (Offline + Zoom)
                                </span>
                            @endif

                            @if($m->issues && $m->issues->count() > 0)
                                <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                                    <i class="fas fa-list-check text-indigo-500"></i> {{ $m->issues->count() }} Isu Diagendakan
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-gray-500 font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="far fa-clock text-blue-500"></i>
                                {{ $m->time_start ?? '08:00' }} - {{ $m->time_end ?? '10:00' }} WITA
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-map-marker-alt text-rose-500"></i>
                                {{ $m->location ?? 'Ruang Rapat Utama' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-user-tie text-slate-500"></i>
                                Pimpinan: <strong>{{ $m->leader->name ?? 'Pimpinan Unit' }}</strong>
                            </span>
                        </div>

                        @if(!empty($m->notes))
                            <p class="text-xs text-gray-600 mt-2 line-clamp-1 italic bg-slate-50 px-2 py-1 rounded border border-gray-100">
                                <i class="fas fa-sticky-note text-amber-500 mr-1"></i> {{ $m->notes }}
                            </p>
                        @endif
                    </div>
                    
                    <!-- Kolom Aksi -->
                    <div class="flex flex-col sm:flex-row md:flex-col items-end gap-2 shrink-0">
                        <a href="{{ route('rapat.workspace', $m->id) }}" class="px-4 py-1.5 rounded-md text-xs font-bold text-center min-w-[130px] transition flex items-center justify-center gap-1.5 shadow-sm {{ $isLive ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-slate-800 hover:bg-slate-900 text-white' }}">
                            <i class="fas fa-laptop-code"></i> Workspace
                        </a>

                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('rapat.show', $m->id) }}" class="border border-gray-200 hover:bg-gray-50 text-gray-700 px-3 py-1 rounded text-xs font-semibold text-center transition">
                                Detail
                            </a>
                            <a href="{{ route('rapat.cetak', $m->id) }}" target="_blank" class="border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1 rounded text-xs font-semibold text-center transition" title="Cetak Notulen">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400">
                <i class="far fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                <p class="text-sm font-semibold text-gray-600">Belum ada agenda rapat yang terdaftar.</p>
                <p class="text-xs text-gray-400 mt-1">Gunakan tombol 'Buat Agenda Rapat' di atas untuk menjadwalkan rapat koordinasi baru.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- ==================== MODAL BUAT AGENDA RAPAT TERSTRUKTUR BSD ==================== -->
<div id="modalBuatRapat" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2">
                <i class="far fa-calendar-plus text-blue-600 text-base"></i>
                <h3 class="text-sm font-bold text-gray-800">Buat Agenda Rapat Koordinasi</h3>
            </div>
            <button type="button" onclick="closeModal('modalBuatRapat')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('rapat.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- 1. Tipe Rapat Standar BSD -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                    Forum / Jenis Rapat <span class="text-red-500">*</span>
                </label>
                <select name="type" id="select_meeting_type" required onchange="toggleCustomType(this.value)" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                    <option value="Rapat TL + Staf">Rapat TL + Staf (Seksi / Operasional Mingguan - Senin Pagi)</option>
                    <option value="Rapat Asman + TL">Rapat Asman + TL (Bidang / Koordinasi Mingguan - Senin Siang)</option>
                    <option value="Rapat Tinjauan Manajemen">Rapat Tinjauan Manajemen / RTM (MUP & Asman - Selasa Pagi)</option>
                    <option value="CUSTOM">Lainnya (Ketik Judul Rapat Khusus / Insidental)</option>
                </select>
            </div>

            <!-- Input Custom Type jika memilih opsi CUSTOM -->
            <div id="wrapper_custom_type" class="hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1">Judul Rapat Khusus <span class="text-red-500">*</span></label>
                <input type="text" name="custom_type" id="input_custom_type" placeholder="Contoh: Rapat Tanggap Darurat Trip Turbin Blok 1" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <!-- 2. Tanggal & Pimpinan Rapat -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Rapat <span class="text-red-500">*</span></label>
                    <input type="date" name="meeting_date" required value="{{ now()->toDateString() }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pimpinan Rapat (Leader) <span class="text-red-500">*</span></label>
                    <select name="leader_id" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                        @foreach($leaders as $leader)
                            <option value="{{ $leader->id }}" {{ $leader->id === auth()->id() ? 'selected' : '' }}>
                                {{ $leader->name }} ({{ $leader->role->name ?? 'Pejabat' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 3. Waktu Pelaksanaan (Jam Mulai & Selesai) -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Waktu Mulai (WITA) <span class="text-red-500">*</span></label>
                    <input type="time" name="time_start" value="08:00" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Waktu Selesai (WITA) <span class="text-red-500">*</span></label>
                    <input type="time" name="time_end" value="10:00" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- 4. Lokasi / Ruangan Rapat & Status Awal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tipe / Mode Pertemuan <span class="text-red-500">*</span></label>
                    <select name="meeting_mode" id="select_meeting_mode" onchange="toggleMeetingMode(this.value)" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                        <option value="Offline" selected>Tatap Muka (Offline di Ruangan)</option>
                        <option value="Online">Online (Zoom / Video Conference)</option>
                        <option value="Hybrid">Hybrid (Tatap Muka & Zoom)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi / Ruangan Rapat</label>
                    <select name="location" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                        <option value="Ruang Rapat Operasi & Keandalan" selected>Ruang Rapat Operasi & Keandalan</option>
                        <option value="Ruang Rapat Utama UP Sengkang">Ruang Rapat Utama UP Sengkang</option>
                        <option value="Ruang Rapat Pemeliharaan">Ruang Rapat Pemeliharaan</option>
                        <option value="Ruang Rapat VIP Manajemen (MUP)">Ruang Rapat VIP Manajemen (MUP)</option>
                        <option value="Online / Video Conference (Virtual)">Online / Video Conference (Virtual)</option>
                    </select>
                </div>
            </div>

            <!-- Bagian Khusus Input Zoom / Video Meeting (Muncul jika Online/Hybrid) -->
            <div id="wrapper_zoom_fields" class="hidden p-3.5 bg-blue-50/70 border border-blue-200 rounded-xl space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
                    <i class="fas fa-video text-blue-600"></i> Detail Tautan Rapat Zoom / Online
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tautan / Link Zoom Meeting</label>
                    <input type="url" name="zoom_link" id="input_zoom_link" placeholder="Contoh: https://zoom.us/j/987654321?pwd=..." class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Meeting ID (Opsional)</label>
                        <input type="text" name="zoom_meeting_id" placeholder="890 1234 5678" class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Passcode (Opsional)</label>
                        <input type="text" name="zoom_passcode" placeholder="PLNNP2026" class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Status Sesi</label>
                    <select name="status" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                        <option value="Akan Datang" selected>Akan Datang (Terjadwal)</option>
                        <option value="Berlangsung">Berlangsung Sekarang (LIVE)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Siklus Mingguan</label>
                    <input type="text" disabled value="Minggu ke-{{ now()->isoWeek() }} (Tahun {{ now()->year }})" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-slate-50 text-gray-500">
                </div>
            </div>

            <!-- 5. Pemilihan Isu untuk Masuk ke Agenda Rapat (Feature Agendakan Isu) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                    <span><i class="fas fa-list-check text-blue-600 mr-1"></i> Pilih Isu yang Diagendakan ke Rapat</span>
                    <span class="text-[10px] text-gray-400 font-normal">Centang isu untuk dibahas di ruang rapat</span>
                </label>
                <div class="max-h-36 overflow-y-auto border border-gray-200 rounded-xl p-2.5 space-y-2 bg-slate-50/50 divide-y divide-gray-100">
                    @forelse($availableIssues ?? [] as $issue)
                        <label class="flex items-start gap-2.5 pt-1.5 first:pt-0 cursor-pointer hover:bg-white p-1 rounded transition">
                            <input type="checkbox" name="issue_ids[]" value="{{ $issue->id }}" class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="text-[11px] leading-tight">
                                <span class="font-bold text-gray-800">#{{ $issue->issue_no }}</span>: {{ $issue->title }}
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[9px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Level {{ $issue->current_level }}</span>
                                    <span class="text-[9px] font-semibold {{ $issue->priority === 'CRITICAL' ? 'text-red-600 bg-red-50' : 'text-amber-600 bg-amber-50' }} px-1.5 py-0.5 rounded">{{ $issue->priority }}</span>
                                </div>
                            </div>
                        </label>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2">Tidak ada isu aktif yang perlu diagendakan.</p>
                    @endforelse
                </div>
            </div>

            <!-- 6. Agenda Ringkas Pembahasan -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Topik Utama / Catatan Awal Pembahasan</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Evaluasi pekerjaan minggu N-1, tindak lanjut kebocoran seal pompa, dan rencana pemeliharaan turbin..." class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"></textarea>
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalBuatRapat')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Simpan & Jadwalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCustomType(val) {
    const wrapper = document.getElementById('wrapper_custom_type');
    const input = document.getElementById('input_custom_type');
    if (val === 'CUSTOM') {
        wrapper.classList.remove('hidden');
        input.required = true;
    } else {
        wrapper.classList.add('hidden');
        input.required = false;
    }
}

function toggleMeetingMode(val) {
    const zoomWrapper = document.getElementById('wrapper_zoom_fields');
    if (val === 'Online' || val === 'Hybrid') {
        zoomWrapper.classList.remove('hidden');
    } else {
        zoomWrapper.classList.add('hidden');
    }
}
</script>
@endsection
