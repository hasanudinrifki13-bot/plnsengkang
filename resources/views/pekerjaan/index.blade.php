@extends('layouts.app')

@section('title', 'Daftar Task')
@section('page_title', 'Task Management')

@section('content')
<!-- Subtitle Navigasi -->
<p class="text-xs text-gray-500 -mt-3 mb-4">Kelola penugasan task, progres realisasi, dan mitigasi keterlambatan kerja</p>

<!-- ==================== KONTEN UTAMA: TABEL TASK ==================== -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col">
    
    <!-- Area Filter & Tombol Tambah Task -->
    <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-white rounded-t-xl gap-4">
        <form method="GET" action="{{ route('pekerjaan') }}" class="flex flex-wrap gap-3 flex-1 w-full">
            <select name="status" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36">
                <option value="">Semua Status</option>
                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>On Progress</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat (Overdue)</option>
                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
            </select>

            <select name="siklus" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-48">
                <option value="semua">Semua Siklus</option>
                <option value="N" {{ request('siklus') == 'N' ? 'selected' : '' }}>Minggu N (W{{ $currentWeek ?? now()->isoWeek() }} - Berjalan)</option>
                <option value="N-1" {{ request('siklus') == 'N-1' ? 'selected' : '' }}>Minggu N-1 (W{{ ($currentWeek ?? now()->isoWeek()) - 1 }} - Evaluasi)</option>
            </select>

            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul task atau no eskalasi..." class="border border-gray-200 rounded-md pl-8 pr-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-full">
            </div>

            @if(request()->hasAny(['status', 'siklus', 'search']))
                <a href="{{ route('pekerjaan') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-md text-xs font-semibold flex items-center gap-1">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>

        <!-- Tombol Pemicu Modal Tambah Task -->
        <button type="button" data-bs-toggle="modal" data-bs-target="#modalTambahPekerjaan" onclick="openModal('modalTambahPekerjaan')" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition flex items-center justify-center gap-2 shrink-0">
            <i class="fas fa-plus"></i> Tambah Task
        </button>
    </div>

    <!-- Tabel Daftar Task -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap table">
            <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Judul Task</th>
                    <th class="px-6 py-4">Terkait Eskalasi</th>
                    <th class="px-6 py-4">PIC</th>
                    <th class="px-6 py-4">Tenggat Waktu</th>
                    <th class="px-6 py-4 w-44">Progress</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                @forelse($tasks as $index => $task)
                    @php
                        // Memanfaatkan Model Accessor getCalculatedStatusAttribute() (Tahap 1)
                        $statusBadge = $task->calculated_status;
                        
                        // Badge Bootstrap styling
                        if ($statusBadge === 'Terlambat' || $statusBadge === 'Overdue') {
                            $badgeClass = 'badge bg-danger text-white bg-red-600';
                            $icon = 'fas fa-exclamation-triangle';
                            $progressBarColor = 'bg-danger bg-red-600';
                        } elseif ($statusBadge === 'Selesai' || $statusBadge === 'Closed' || $statusBadge === 'Resolved') {
                            $badgeClass = 'badge bg-success text-white bg-emerald-600';
                            $icon = 'fas fa-check-circle';
                            $progressBarColor = 'bg-success bg-emerald-600';
                        } else {
                            $badgeClass = 'badge bg-primary text-white bg-blue-600';
                            $icon = 'fas fa-spinner';
                            $progressBarColor = 'bg-primary bg-blue-600';
                        }
                    @endphp
                    <tr class="hover:bg-blue-50/40 transition">
                        <!-- 1. No -->
                        <td class="px-6 py-4 text-center text-gray-500 font-bold">
                            {{ $tasks->firstItem() ? $tasks->firstItem() + $index : $index + 1 }}
                        </td>

                        <!-- 2. Judul Pekerjaan -->
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 text-sm block">{{ $task->title }}</span>
                            @if($task->description)
                                <span class="text-[11px] text-gray-400 font-normal line-clamp-1 max-w-xs">{{ $task->description }}</span>
                            @endif
                        </td>

                        <!-- 3. Terkait Eskalasi (jika ada) -->
                        <td class="px-6 py-4">
                            @if($task->issue)
                                <a href="{{ route('issues.show', $task->issue->id) }}" class="badge bg-info text-white bg-sky-600 px-2.5 py-1 rounded text-[11px] font-mono font-bold hover:bg-sky-700 transition inline-flex items-center gap-1 shadow-sm" title="{{ $task->issue->title }}">
                                    <i class="fas fa-link text-[9px]"></i> #{{ $task->issue->issue_no }}
                                </a>
                            @else
                                <span class="text-gray-400 text-xs italic">Task Rutin</span>
                            @endif
                        </td>

                        <!-- 4. PIC -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($task->assignee->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-gray-700 font-semibold">{{ $task->assignee->name ?? 'Staff Pelaksana' }}</span>
                            </div>
                        </td>

                        <!-- 5. Tenggat Waktu (Due Date) -->
                        <td class="px-6 py-4 text-gray-600 font-mono text-xs">
                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}
                        </td>

                        <!-- 6. Progress (Bootstrap Progress Bar) -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-700 mb-1">
                                <span>Realisasi</span>
                                <span>{{ $task->progress_percent }}%</span>
                            </div>
                            <!-- Bootstrap Progress Bar Component -->
                            <div class="progress h-2 bg-gray-100 rounded-full overflow-hidden" style="height: 8px;">
                                <div class="progress-bar {{ $progressBarColor }} rounded-full transition-all duration-300" role="progressbar" style="width: {{ $task->progress_percent }}%;" aria-valuenow="{{ $task->progress_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>

                        <!-- 7. Status (Calculated Status Badge & Bukti Selesai) -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5 items-start">
                                <span class="{{ $badgeClass }} px-2.5 py-1 rounded-full text-[10px] font-bold inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="{{ $icon }} text-[9px]"></i> {{ $statusBadge }}
                                </span>
                                @if($task->completion_attachment)
                                    <a href="{{ asset('storage/' . $task->completion_attachment) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded shadow-xs" title="Lihat Bukti Foto / Dokumen">
                                        <i class="fas fa-paperclip text-[9px]"></i> Bukti Selesai
                                    </a>
                                @endif
                            </div>
                        </td>

                        <!-- 8. Aksi (Tombol Update Progress & Edit) -->
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-2">
                                <!-- Tombol Khusus: Update Progress (Tahap 4) -->
                                <button type="button" onclick="openQuickProgressModal({{ $task->id }}, '{{ addslashes($task->title) }}', {{ $task->progress_percent }})" class="btn btn-sm btn-outline-primary px-3 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg text-xs font-bold border border-blue-200 transition flex items-center gap-1.5 shadow-sm" title="Perbarui Nilai Progres">
                                    <i class="fas fa-sliders-h"></i> Update Progress
                                </button>

                                <!-- Tombol Edit Detail / Hapus -->
                                <button type="button" onclick="openUpdateTaskModal({{ json_encode($task) }})" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Edit Lengkap">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-clipboard-list text-4xl mb-2 text-gray-300 block"></i>
                            <p class="font-semibold text-gray-600 text-sm">Tidak ada task yang ditemukan.</p>
                            <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Tambah Task" untuk menugaskan task baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
        <div class="p-4 border-t border-gray-100 flex justify-between items-center">
            <span class="text-xs text-gray-500">Menampilkan {{ $tasks->firstItem() }} - {{ $tasks->lastItem() }} dari {{ $tasks->total() }} task</span>
            {{ $tasks->links() }}
        </div>
    @endif
</div>

<!-- ==================== TAHAP 3: MODAL TAMBAH TASK (STANDALONE ATAU DARI ESKALASI) ==================== -->
<div id="modalTambahPekerjaan" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-600 text-base"></i>
                <h3 class="text-sm font-bold text-gray-800">Tambah Task Baru</h3>
            </div>
            <button type="button" onclick="closeModal('modalTambahPekerjaan')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">

            <!-- 1. Judul Task -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Judul Task <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Pemeliharaan Rutin Pompa Pendingin Blok 1" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <!-- 2. Deskripsi Task -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi & Instruksi Kerja</label>
                <textarea name="description" rows="2" placeholder="Tuliskan rincian instruksi pelaksanaan task..." class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"></textarea>
            </div>

            <!-- 3. PIC Pelaksana (User dengan Role Staff) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Penanggung Jawab / PIC (Staf) <span class="text-red-500">*</span></label>
                <select name="assigned_to" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                    <option value="">-- Pilih Staf Pelaksana --</option>
                    @foreach($staffUsers as $staff)
                        <option value="{{ $staff->id }}">
                            {{ $staff->name }} (NID: {{ $staff->employee_no ?? '-' }} - {{ $staff->position->name ?? 'Staff' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Tanggal Mulai & Tenggat Waktu (Due Date) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ now()->toDateString() }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tenggat Waktu (Due Date) <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" required value="{{ now()->addDays(3)->toDateString() }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- 5. Keterkaitan dengan Eskalasi (Opsional - Standalone vs Tindak Lanjut Isu) -->
            <div class="p-3 bg-slate-50 border border-gray-200 rounded-xl space-y-1.5">
                <label class="block text-xs font-bold text-gray-700 flex items-center gap-1.5">
                    <i class="fas fa-link text-sky-600"></i> Terkait Eskalasi / Tindak Lanjut Isu (Opsional)
                </label>
                <select name="issue_id" class="w-full text-xs border border-gray-200 rounded-lg p-2 outline-none focus:border-blue-500 bg-white">
                    <option value="">-- Standalone / Task Rutin (Tidak Terkait Eskalasi) --</option>
                    @foreach($issues as $issue)
                        <option value="{{ $issue->id }}" {{ request('issue_id') == $issue->id ? 'selected' : '' }}>
                            #{{ $issue->issue_no }} - {{ Str::limit($issue->title, 40) }} (Level {{ $issue->current_level }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400">Pilih nomor eskalasi jika task ini merupakan realisasi keputusan atau mitigasi kendala di lapangan.</p>
            </div>

            <!-- Tombol Batal & Simpan -->
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalTambahPekerjaan')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Simpan Task
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== TAHAP 4: MODAL CEPAT UPDATE PROGRESS (SLIDER & NUMERIC & BUKTI FISIK) ==================== -->
<div id="modalQuickProgress" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 animate-fade-in max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-sliders-h text-blue-600"></i> Update Progres Task
            </h3>
            <button type="button" onclick="closeModal('modalQuickProgress')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form id="formQuickProgress" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <p class="text-xs text-gray-500 mb-1">Judul Task:</p>
                <h4 id="quickProgressTitle" class="text-xs font-bold text-gray-800 bg-slate-50 p-2.5 rounded-lg border border-gray-100 line-clamp-2"></h4>
            </div>

            <!-- Slider Range & Number Input (Sinkron Otomatis) -->
            <div class="bg-blue-50/60 p-4 rounded-xl border border-blue-100 space-y-3">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-blue-900">Nilai Progres (%)</label>
                    <div class="flex items-center gap-1">
                        <input type="number" id="quickProgressNumber" name="progress_percent" min="0" max="100" step="1" required class="w-16 text-center text-base font-bold bg-white border border-blue-300 rounded-lg p-1 outline-none focus:ring-2 focus:ring-blue-500" oninput="syncFromNumber(this.value)">
                        <span class="text-xs font-bold text-blue-900">%</span>
                    </div>
                </div>

                <!-- Range Slider Component -->
                <input type="range" id="quickProgressSlider" min="0" max="100" step="5" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer accent-blue-600" oninput="syncFromSlider(this.value)">

                <!-- Shortcut Buttons -->
                <div class="flex justify-between gap-1 pt-1">
                    <button type="button" onclick="setQuickVal(0)" class="text-[10px] font-bold bg-white border border-gray-200 px-2 py-0.5 rounded hover:bg-gray-100">0%</button>
                    <button type="button" onclick="setQuickVal(25)" class="text-[10px] font-bold bg-white border border-gray-200 px-2 py-0.5 rounded hover:bg-gray-100">25%</button>
                    <button type="button" onclick="setQuickVal(50)" class="text-[10px] font-bold bg-white border border-gray-200 px-2 py-0.5 rounded hover:bg-gray-100">50%</button>
                    <button type="button" onclick="setQuickVal(75)" class="text-[10px] font-bold bg-white border border-gray-200 px-2 py-0.5 rounded hover:bg-gray-100">75%</button>
                    <button type="button" onclick="setQuickVal(100)" class="text-[10px] font-bold bg-emerald-100 border border-emerald-300 text-emerald-800 px-2 py-0.5 rounded hover:bg-emerald-200">100% Selesai</button>
                </div>
            </div>

            <!-- Upload Bukti Penyelesaian Fisik (Muncul Saat 100% Selesai) -->
            <div id="quickProofSection" class="hidden bg-emerald-50/70 p-3.5 rounded-xl border border-emerald-200 space-y-2.5 transition-all">
                <div class="flex items-center gap-1.5 text-emerald-800 font-bold text-xs">
                    <i class="fas fa-camera text-emerald-600"></i>
                    <span>Lampiran Bukti Selesai (Foto / Dokumen)</span>
                </div>
                <p class="text-[10px] text-emerald-700 leading-snug">Wajib/dianjurkan unggah foto pekerjaan di unit atau dokumen berita acara:</p>
                <input type="file" name="completion_attachment" accept="image/*,.pdf,.doc,.docx" class="w-full text-[11px] text-gray-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 bg-white border border-emerald-200 rounded-lg p-1.5">
                <textarea name="completion_notes" rows="2" placeholder="Catatan ringkas hasil penyelesaian di lapangan..." class="w-full text-xs border border-emerald-200 rounded-lg p-2 outline-none focus:border-emerald-500 bg-white placeholder-gray-400"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalQuickProgress')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                    Simpan Progres Task
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL FORM: EDIT LENGKAP PEKERJAAN ==================== -->
<div id="modalUpdatePekerjaan" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 modal-container max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-bold text-gray-800">Edit Data Task</h3>
            <button type="button" onclick="closeModal('modalUpdatePekerjaan')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form id="formUpdatePekerjaan" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Judul Task <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="edit_task_title" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">PIC Pelaksana <span class="text-red-500">*</span></label>
                    <select name="assigned_to" id="edit_task_assigned_to" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" id="edit_task_due_date" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Progres (%)</label>
                    <input type="number" name="progress_percent" id="edit_task_progress" min="0" max="100" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Status Operasional <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_task_status" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                        <option value="In Progress">In Progress</option>
                        <option value="Open">Open</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Keterkaitan Eskalasi (Opsional)</label>
                <select name="issue_id" id="edit_task_issue_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                    <option value="">-- Standalone (Tidak Terkait Eskalasi) --</option>
                    @foreach($issues as $issue)
                        <option value="{{ $issue->id }}">#{{ $issue->issue_no }} - {{ Str::limit($issue->title, 40) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Progress / Lapangan</label>
                <textarea name="description" id="edit_task_description" rows="2" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Keterangan singkat hasil pengerjaan..."></textarea>
            </div>

            <!-- Upload / Update Bukti Penyelesaian -->
            <div class="p-3 bg-slate-50 border border-gray-200 rounded-xl space-y-2">
                <label class="block text-xs font-bold text-gray-700 flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fas fa-paperclip text-emerald-600"></i> Bukti Penyelesaian Fisik</span>
                    <span id="edit_task_attachment_preview" class="hidden">
                        <a id="edit_task_attachment_link" href="#" target="_blank" class="text-blue-600 hover:underline text-[10px] font-bold">
                            <i class="fas fa-external-link-alt"></i> Lihat File Saat Ini
                        </a>
                    </span>
                </label>
                <input type="file" name="completion_attachment" accept="image/*,.pdf,.doc,.docx" class="w-full text-xs text-gray-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 bg-white border border-gray-200 rounded-lg p-1.5">
                <textarea name="completion_notes" id="edit_task_completion_notes" rows="2" placeholder="Catatan penutup penyelesaian pekerjaan..." class="w-full text-xs border border-gray-200 rounded-lg p-2 outline-none focus:border-blue-500 bg-white placeholder-gray-400"></textarea>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                <button type="button" onclick="deleteTaskAction()" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                    <i class="fas fa-trash"></i> Hapus
                </button>

                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('modalUpdatePekerjaan')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm">Simpan Perubahan Task</button>
                </div>
            </div>
        </form>

        <!-- Hidden delete form -->
        <form id="formDeletePekerjaan" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
        </form>
    </div>
</div>

<script>
let currentTaskId = null;

// Tampilkan / sembunyikan section upload bukti fisik ketika progres = 100%
function checkQuickCompletionProof(val) {
    const proofSec = document.getElementById('quickProofSection');
    if (proofSec) {
        if (parseInt(val) >= 100) {
            proofSec.classList.remove('hidden');
        } else {
            proofSec.classList.add('hidden');
        }
    }
}

// Sinkronisasi slider & number input pada Modal Quick Progress
function syncFromSlider(val) {
    document.getElementById('quickProgressNumber').value = val;
    checkQuickCompletionProof(val);
}

function syncFromNumber(val) {
    const clamped = Math.max(0, Math.min(100, parseInt(val) || 0));
    document.getElementById('quickProgressSlider').value = clamped;
    checkQuickCompletionProof(clamped);
}

function setQuickVal(val) {
    document.getElementById('quickProgressSlider').value = val;
    document.getElementById('quickProgressNumber').value = val;
    checkQuickCompletionProof(val);
}

function openQuickProgressModal(taskId, taskTitle, currentProgress) {
    const form = document.getElementById('formQuickProgress');
    const titleEl = document.getElementById('quickProgressTitle');
    if (form && titleEl) {
        form.action = `/tasks/${taskId}/progress`;
        titleEl.textContent = taskTitle;
        setQuickVal(currentProgress || 0);
        checkQuickCompletionProof(currentProgress || 0);
        openModal('modalQuickProgress');
    }
}

function openUpdateTaskModal(task) {
    if (!task) return;
    currentTaskId = task.id;
    document.getElementById('formUpdatePekerjaan').action = '/tasks/' + task.id;
    document.getElementById('edit_task_title').value = task.title || '';
    document.getElementById('edit_task_assigned_to').value = task.assigned_to || '';
    document.getElementById('edit_task_due_date').value = task.due_date ? task.due_date.substring(0, 10) : '';
    document.getElementById('edit_task_progress').value = task.progress_percent || 0;
    document.getElementById('edit_task_status').value = task.status || 'In Progress';
    document.getElementById('edit_task_issue_id').value = task.issue_id || '';
    document.getElementById('edit_task_description').value = task.description || '';
    document.getElementById('edit_task_completion_notes').value = task.completion_notes || '';

    const previewContainer = document.getElementById('edit_task_attachment_preview');
    const previewLink = document.getElementById('edit_task_attachment_link');
    if (task.completion_attachment) {
        previewContainer.classList.remove('hidden');
        previewLink.href = '/storage/' + task.completion_attachment;
    } else {
        previewContainer.classList.add('hidden');
    }
    
    openModal('modalUpdatePekerjaan');
}

function deleteTaskAction() {
    if (!currentTaskId) return;
    if (confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?')) {
        const deleteForm = document.getElementById('formDeletePekerjaan');
        deleteForm.action = '/tasks/' + currentTaskId;
        deleteForm.submit();
    }
}
</script>
@endsection
