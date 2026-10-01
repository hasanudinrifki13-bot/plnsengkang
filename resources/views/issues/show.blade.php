@extends('layouts.app')

@section('title', 'Detail Isu ' . $issue->issue_no)
@section('page_title', 'Detail Isu & Riwayat Eskalasi')

@section('content')
<div class="space-y-6">

    <!-- Tombol Navigasi Kembali -->
    <div>
        <a href="{{ route('issues.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Eskalasi / Isu
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- TAHAP 3 (BAGIAN 1): BAGIAN ATAS - DETAIL ISSUE (BOOTSTRAP CARD STYLE)      -->
    <!-- Menampilkan: Nomor Isu, Judul, Deskripsi, Prioritas, Level Saat Ini, Status -->
    <!-- ========================================================================= -->
    <div class="card bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Card Header: Metadata Utama Isu -->
        <div class="card-header bg-slate-50/80 p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- 1. Nomor Isu -->
                    <span class="badge bg-slate-800 text-white font-mono text-xs font-bold px-2.5 py-1 rounded shadow-xs">
                        #{{ $issue->issue_no }}
                    </span>

                    <!-- 2. Prioritas -->
                    @if(in_array($issue->priority, ['CRITICAL', 'HIGH']))
                        <span class="badge bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded text-xs font-bold">
                            Prioritas: {{ $issue->priority }}
                        </span>
                    @elseif($issue->priority == 'MEDIUM')
                        <span class="badge bg-amber-100 text-amber-800 border border-amber-200 px-2.5 py-1 rounded text-xs font-bold">
                            Prioritas: Medium
                        </span>
                    @else
                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 px-2.5 py-1 rounded text-xs font-bold">
                            Prioritas: Low
                        </span>
                    @endif

                    <!-- 3. Level Saat Ini (current_level) -->
                    <span class="badge bg-blue-100 text-blue-800 border border-blue-200 px-2.5 py-1 rounded text-xs font-bold flex items-center gap-1">
                        <i class="fas fa-layer-group text-blue-600"></i> Level Saat Ini: Level {{ $issue->current_level }}
                    </span>

                    <!-- 4. Status -->
                    @php
                        $statusBadgeClass = match($issue->status) {
                            'Resolved', 'Closed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'Escalated' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'Waiting Decision', 'Need Decision', 'Under Review' => 'bg-red-100 text-red-800 border-red-200',
                            default => 'bg-blue-100 text-blue-800 border-blue-200',
                        };
                    @endphp
                    <span class="badge {{ $statusBadgeClass }} border px-2.5 py-1 rounded text-xs font-bold">
                        Status: {{ $issue->status }}
                    </span>
                </div>

                <!-- Judul Isu -->
                <h1 class="text-xl font-bold text-gray-900 leading-snug">{{ $issue->title }}</h1>
            </div>

            <!-- Info Pelapor & Waktu Dibuat -->
            <div class="text-right text-xs text-gray-500 shrink-0 bg-white p-3 rounded-lg border border-gray-100 shadow-xs">
                <p>Dilaporkan Oleh: <b class="text-gray-800">{{ $issue->reporter->name ?? 'Staff Pelaksana' }}</b></p>
                <p class="font-mono text-[11px] text-gray-400 mt-0.5">
                    {{ $issue->created_at->format('d M Y - H:i') }} WITA ({{ $issue->created_at->diffForHumans() }})
                </p>
            </div>
        </div>

        <!-- Card Body: Deskripsi Lengkap & Info SLA -->
        <div class="card-body p-6 space-y-4">
            <div>
                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i class="fas fa-align-left text-blue-600"></i> Deskripsi & Uraian Masalah
                </h4>
                <div class="p-4 bg-slate-50 border border-gray-200 rounded-xl text-xs text-gray-700 leading-relaxed whitespace-pre-line font-normal">
                    {{ $issue->description }}
                </div>
            </div>

            <!-- Ringkasan Atribut Tambahan -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
                <div class="p-3 bg-white border border-gray-100 rounded-lg">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">Kategori</span>
                    <span class="font-semibold text-gray-800">{{ $issue->issue_type ?? 'PROBLEM' }}</span>
                </div>
                <div class="p-3 bg-white border border-gray-100 rounded-lg">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">Target Penyelesaian</span>
                    <span class="font-semibold text-gray-800 font-mono">{{ $issue->due_date ? \Carbon\Carbon::parse($issue->due_date)->format('d M Y') : '-' }}</span>
                </div>
                <div class="p-3 bg-white border border-gray-100 rounded-lg">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">Durasi Tiket</span>
                    <span class="font-semibold text-gray-800">{{ max(1, (int) round($issue->created_at ? $issue->created_at->diffInDays(now()) : 1)) }} Hari Berjalan</span>
                </div>
                <div class="p-3 bg-white border border-gray-100 rounded-lg">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">Batas SLA</span>
                    @php $sla = $issue->sla_info; @endphp
                    <span class="{{ $sla['badge_class'] }} px-2 py-0.5 rounded text-[10px] inline-flex items-center gap-1 mt-0.5">
                        <i class="{{ $sla['icon'] }} text-[9px]"></i> {{ $sla['label'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAHAP 3 (BAGIAN 2): BAGIAN TENGAH - TIMELINE ESKALASI                     -->
    {{-- Loop riwayat escalations menggunakan foreach / forelse --}}
    <!-- ========================================================================= -->
    <div class="card bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="card-header bg-slate-50/80 p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-stream text-blue-600"></i>
                <h3 class="text-sm font-bold text-gray-800">Timeline Riwayat Perjalanan Eskalasi</h3>
            </div>
            <span class="badge bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded text-xs font-bold">
                {{ $issue->escalations->count() }} Kali Rekam Jejak
            </span>
        </div>

        <div class="card-body p-6">
            <div class="list-group relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-blue-200">
                
                <!-- Titik Awal: Pelaporan Pertama -->
                <div class="relative">
                    <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                    <div class="bg-slate-50/80 p-3.5 rounded-xl border border-gray-200 text-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                            <span class="font-bold text-gray-800 flex items-center gap-1.5">
                                <i class="fas fa-flag text-emerald-600"></i> Isu Dibuat / Dilaporkan Pertama Kali
                            </span>
                            <span class="text-gray-400 font-mono text-[11px]">{{ $issue->created_at->format('d M Y - H:i') }} WITA</span>
                        </div>
                        <p class="text-gray-600">
                            Masalah dicatat oleh <b class="text-gray-800">{{ $issue->reporter->name ?? 'Staff Pelaksana' }}</b> dan otomatis masuk ke antrean tinjauan awal <b>Level TL</b>.
                        </p>
                    </div>
                </div>

                <!-- Loop Riwayat Escalation Sesuai Instruksi User -->
                @forelse($issue->escalations as $escalation)
                    <div class="relative">
                        <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-blue-600 ring-4 ring-blue-100"></span>
                        <div class="bg-blue-50/40 p-4 rounded-xl border border-blue-100 text-xs space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="badge bg-blue-600 text-white font-bold px-2 py-0.5 rounded text-[10px]">
                                        Eskalasi ke Level {{ $escalation->to_level }}
                                    </span>
                                    <span class="text-gray-600">
                                        Jenjang Asal: <b>Level {{ $escalation->from_level }}</b>
                                    </span>
                                </div>
                                <span class="text-gray-400 font-mono text-[11px]">
                                    {{ $escalation->created_at->format('d M Y - H:i') }} WITA ({{ $escalation->created_at->diffForHumans() }})
                                </span>
                            </div>

                            <!-- Format Tampilan Persis Instruksi User: -->
                            <!-- "Dieskalasi oleh [Nama fromUser] ke level [to_level] pada tanggal [created_at]. Alasan: [reason]" -->
                            <p class="text-gray-800 text-xs leading-relaxed">
                                Dieskalasi oleh <b class="text-blue-900">{{ $escalation->fromUser->name ?? 'Pejabat Unit' }}</b> ke level <span class="font-bold text-blue-700">[{{ $escalation->to_level }}]</span> pada tanggal <b class="font-mono text-gray-700">{{ $escalation->created_at->format('d M Y H:i') }} WITA</b>.
                            </p>

                            <!-- Kotak Alasan Eskalasi -->
                            <div class="bg-white p-3 rounded-lg border border-blue-200 text-gray-700 shadow-2xs">
                                <span class="font-bold text-gray-800 block text-[11px] mb-0.5">
                                    <i class="fas fa-comment-dots text-blue-500 mr-1"></i> Alasan Eskalasi:
                                </span>
                                <p class="text-xs italic text-gray-700 leading-relaxed">{{ $escalation->reason }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Jika belum ada riwayat eskalasi (masih level awal) -->
                    <div class="relative">
                        <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-slate-400 ring-4 ring-slate-100"></span>
                        <div class="bg-slate-50 p-4 rounded-xl border border-dashed border-gray-300 text-xs text-gray-500">
                            <i class="fas fa-info-circle text-blue-500 mr-1.5"></i> Belum ada riwayat eskalasi. Isu ini masih berada pada level awal penanganan (Level TL).
                        </div>
                    </div>
                @endforelse

                <!-- Jika sudah ada keputusan resmi yang diterbitkan -->
                @foreach($issue->decisions as $dec)
                    <div class="relative">
                        <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-emerald-600 ring-4 ring-emerald-100"></span>
                        <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-200 text-xs space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="badge bg-emerald-600 text-white font-bold px-2 py-0.5 rounded text-[10px]">
                                    Keputusan Diterbitkan
                                </span>
                                <span class="text-gray-400 font-mono text-[11px]">{{ $dec->created_at->format('d M Y - H:i') }} WITA</span>
                            </div>
                            <p class="text-gray-800">
                                Ditetapkan oleh <b class="text-emerald-900">{{ $dec->decisionMaker->name ?? 'Pejabat Unit' }}</b> pada kewenangan <span class="font-bold text-emerald-800">Level {{ $dec->decision_level }}</span>.
                            </p>
                            <div class="p-3 bg-white border border-emerald-200 rounded-lg text-gray-800 font-medium">
                                "{{ $dec->decision }}"
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAHAP 3 (BAGIAN 3): BAGIAN BAWAH - TOMBOL AKSI DENGAN HAK AKSES ROLE      -->
    <!-- Tombol: "Beri Keputusan" & "Eskalasi ke Atasan" (Sesuai current_level)    -->
    <!-- ========================================================================= -->
    <div class="card bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        @php
            $userRole = strtoupper(auth()->user()->role->name ?? '');
            $currentLevel = strtoupper($issue->current_level);
            $isResolved = in_array($issue->status, ['Resolved', 'Closed']);
            $canAct = ($userRole === $currentLevel) && !$isResolved;
        @endphp

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-user-shield text-blue-600"></i> Kontrol Aksi & Wewenang Keputusan
                </h4>
                <p class="text-xs text-gray-500 mt-0.5">
                    Role Login Anda: <b class="text-gray-800">{{ $userRole ?: 'Staff' }}</b> | Wewenang Penanganan: <b class="text-blue-600">Level {{ $currentLevel }}</b>
                </p>
            </div>

            <!-- Tombol Aksi Berdasarkan Role dan Current Level -->
            <div class="flex items-center gap-3 flex-wrap">
                @if($canAct)
                    <!-- Tombol 1: Beri Keputusan -->
                    <button type="button" data-bs-toggle="modal" data-bs-target="#decisionModal" onclick="openModal('decisionModal')" class="btn btn-success px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i class="fas fa-gavel"></i> Beri Keputusan (Level {{ $currentLevel }})
                    </button>

                    <!-- Tombol 2: Eskalasi ke Atasan (Muncul jika belum di level puncak MUP) -->
                    @if(in_array($currentLevel, ['TL', 'ASMAN']))
                        @php
                            $targetLevelName = $currentLevel === 'TL' ? 'ASMAN' : 'MUP';
                        @endphp
                        <button type="button" data-bs-toggle="modal" data-bs-target="#escalateModal" onclick="openModal('escalateModal')" class="btn btn-primary px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2">
                            <i class="fas fa-arrow-up"></i> Eskalasi ke {{ $targetLevelName }}
                        </button>
                    @endif
                @elseif($isResolved)
                    <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 rounded-lg text-xs font-bold flex items-center gap-1.5">
                        <i class="fas fa-check-circle text-emerald-600"></i> Isu ini telah diselesaikan (Status: {{ $issue->status }})
                    </span>
                @else
                    <span class="text-xs text-gray-500 bg-slate-50 border border-gray-200 px-3.5 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-lock text-amber-500"></i> Isu ini sedang berada dalam kewenangan tindakan <b>Level {{ $currentLevel }}</b>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- ==================== TABS TAMBAHAN: DISKUSI, KEPUTUSAN & TASK TINDAK LANJUT ==================== -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <!-- Tabs Selector -->
        <div class="flex gap-6 border-b border-gray-200 text-xs font-semibold pb-3">
            <button type="button" onclick="switchIssueTab('diskusi')" id="tab-btn-diskusi" class="text-blue-600 border-b-2 border-blue-600 font-bold pb-2 flex items-center gap-1.5">
                <i class="far fa-comments"></i> Forum Diskusi ({{ $issue->discussions ? $issue->discussions->count() : 0 }})
            </button>
            <button type="button" onclick="switchIssueTab('keputusan')" id="tab-btn-keputusan" class="text-gray-400 hover:text-gray-700 pb-2 flex items-center gap-1.5">
                <i class="fas fa-gavel"></i> Arsip Putusan ({{ $issue->decisions ? $issue->decisions->count() : 0 }})
            </button>
            <button type="button" onclick="switchIssueTab('tindaklanjut')" id="tab-btn-tindaklanjut" class="text-gray-400 hover:text-gray-700 pb-2 flex items-center gap-1.5">
                <i class="fas fa-tasks"></i> Task Tindak Lanjut ({{ $issue->tasks ? $issue->tasks->count() : 0 }})
            </button>
        </div>

        <!-- Tab 1: Diskusi -->
        <div id="issue-tab-diskusi" class="pt-5 space-y-4">
            <div class="space-y-3 max-h-[350px] overflow-y-auto pr-2">
                @forelse($issue->discussions as $disc)
                    <div class="flex gap-3 items-start bg-slate-50/70 p-3.5 rounded-xl border border-gray-100 text-xs">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($disc->user->name ?? 'User', 0, 2)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-gray-800">{{ $disc->user->name ?? 'Pegawai' }} ({{ $disc->user->role->name ?? 'Staff' }})</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $disc->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $disc->message }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400 text-xs">
                        Belum ada diskusi untuk isu ini. Berikan masukan atau konfirmasi lapangan di bawah.
                    </div>
                @endforelse
            </div>

            <!-- Form Tambah Diskusi -->
            <form action="{{ route('issues.discussions.store', $issue->id) }}" method="POST" class="pt-3 border-t border-gray-100">
                @csrf
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700">Tulis Catatan / Tanggapan Tambahan:</label>
                    <textarea name="message" rows="2" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Ketik catatan teknis atau tanggapan terkait isu ini..."></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-paper-plane text-[10px]"></i> Kirim Tanggapan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab 2: Keputusan Arsip -->
        <div id="issue-tab-keputusan" class="hidden pt-5 space-y-3">
            @forelse($issue->decisions as $dec)
                <div class="border border-emerald-100 bg-emerald-50/20 p-4 rounded-xl text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="badge bg-emerald-600 text-white px-2 py-0.5 rounded text-[10px] font-bold">Level {{ $dec->decision_level }}</span>
                        <span class="text-gray-400 font-mono text-[11px]">{{ $dec->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <p class="text-gray-800 font-medium">"{{ $dec->decision }}"</p>
                    <p class="text-gray-500 text-[11px]">Ditetapkan oleh: <b>{{ $dec->decisionMaker->name ?? 'Pejabat Unit' }}</b></p>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-xs">Belum ada keputusan formal yang dicatat.</div>
            @endforelse
        </div>

        <!-- Tab 3: Task Tindak Lanjut -->
        <div id="issue-tab-tindaklanjut" class="hidden pt-5 space-y-3">
            @forelse($issue->tasks as $task)
                <div class="border border-gray-200 p-3.5 rounded-xl flex items-center justify-between text-xs hover:bg-slate-50">
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $task->title }}</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5">PIC: {{ $task->assignee->name ?? 'Staff' }} | Target: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-700">{{ $task->progress_percent }}%</span>
                        <span class="badge bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold">{{ $task->status }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-xs">Belum ada task tindak lanjut yang terikat dengan isu ini.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- ==================== MODAL ESKALASI KE ATASAN ==================== -->
<div id="escalateModal" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 modal-container">
        @php
            $targetLevel = $issue->current_level === 'TL' ? 'ASMAN' : 'MUP';
        @endphp
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-3">
            <h3 class="text-sm font-bold text-gray-800">Eskalasi Isu ke {{ $targetLevel }}</h3>
            <button type="button" data-bs-dismiss="modal" onclick="closeModal('escalateModal')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>
        <p class="text-xs text-gray-500 mb-4">Isu akan diteruskan ke kewenangan tingkat <b>{{ $targetLevel }}</b>.</p>
        
        <form action="{{ route('issues.escalate', $issue->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Eskalasi & Rekomendasi Putusan <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="4" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Jelaskan alasan batas kewenangan teknis, kebutuhan anggaran, atau koordinasi lintas bidang..."></textarea>
                @error('reason')
                    <p class="text-[10px] text-red-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" data-bs-dismiss="modal" onclick="closeModal('escalateModal')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-sm">Kirim Eskalasi</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL PENGAMBILAN KEPUTUSAN ==================== -->
<div id="decisionModal" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 modal-container">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-3">
            <h3 class="text-sm font-bold text-gray-800">Ambil Keputusan (Level {{ $issue->current_level }})</h3>
            <button type="button" data-bs-dismiss="modal" onclick="closeModal('decisionModal')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>
        <p class="text-xs text-gray-500 mb-4">Berikan instruksi putusan resmi untuk penyelesaian isu ini.</p>
        
        <form action="{{ route('decisions.store', $issue->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pernyataan Keputusan <span class="text-red-500">*</span></label>
                <textarea name="decision" rows="3" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Tuliskan keputusan resmi..."></textarea>
                @error('decision')
                    <p class="text-[10px] text-red-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Dasar Pertimbangan / Rationale (Opsional)</label>
                <textarea name="rationale" rows="2" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Alasan teknis, keandalan, atau anggaran..."></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Tindak Lanjut (Opsional)</label>
                <textarea name="follow_up" rows="2" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Tindakan korektif atau perbaikan yang harus dilakukan..."></textarea>
            </div>

            <!-- Otomatisasi Pembuatan Task Tindak Lanjut -->
            <div class="bg-blue-50/60 p-3 rounded-lg border border-blue-100 space-y-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="create_task" id="create_task" value="1" class="rounded text-blue-600 focus:ring-blue-500" onchange="document.getElementById('taskDetailsArea').classList.toggle('hidden', !this.checked)">
                    <label for="create_task" class="text-xs text-blue-900 font-bold cursor-pointer">
                        <i class="fas fa-magic text-blue-600 mr-1"></i> Buat Task Tindak Lanjut Otomatis
                    </label>
                </div>

                <div id="taskDetailsArea" class="hidden space-y-2 pt-1 border-t border-blue-200/60">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-blue-900 mb-0.5">Tugaskan ke (PIC Pelaksana)</label>
                            <select name="task_assignee_id" class="w-full text-xs border border-blue-200 rounded p-1.5 bg-white">
                                <option value="">-- Pilih Karyawan PIC --</option>
                                @if(isset($users))
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->employee_no ?? 'NID' }}) - {{ $u->role->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-blue-900 mb-0.5">Target Selesai (Due Date)</label>
                            <input type="date" name="task_due_date" value="{{ now()->addDays(3)->toDateString() }}" class="w-full text-xs border border-blue-200 rounded p-1.5 bg-white">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="mark_resolved" id="mark_resolved" value="1" class="rounded text-emerald-600">
                <label for="mark_resolved" class="text-xs text-gray-700 font-semibold cursor-pointer">Tandai Masalah Selesai (Status: Resolved)</label>
            </div>
            
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" data-bs-dismiss="modal" onclick="closeModal('decisionModal')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold shadow-sm">Simpan Keputusan</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchIssueTab(tabName) {
    const tabs = ['diskusi', 'keputusan', 'tindaklanjut'];
    tabs.forEach(t => {
        const el = document.getElementById('issue-tab-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (el && btn) {
            if (t === tabName) {
                el.classList.remove('hidden');
                btn.className = 'text-blue-600 border-b-2 border-blue-600 font-bold pb-2 flex items-center gap-1.5';
            } else {
                el.classList.add('hidden');
                btn.className = 'text-gray-400 hover:text-gray-700 pb-2 flex items-center gap-1.5';
            }
        }
    });
}
</script>
@endsection
