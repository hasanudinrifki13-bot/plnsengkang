<!-- ==================== DASHBOARD MONITORING MANAJEMEN TUGAS PERSIS GAMBAR 2 ==================== -->
<div class="space-y-6">

    <!-- HEADER BANNER GELAP PERSIS GAMBAR 2 -->
    <div class="bg-[#0f2744] text-white p-5 rounded-xl shadow-md text-center border border-slate-700">
        <h2 class="text-xl md:text-2xl font-black uppercase tracking-wider">DASHBOARD MONITORING MANAJEMEN TUGAS</h2>
        <p class="text-xs text-sky-200 mt-1 font-medium italic">Monitoring meeting, action item, PIC, eskalasi, progres dan deadline — sumber: Struktur Data Manajemen Tugas</p>
    </div>

    <!-- SIKLUS MINGGUAN FILTER BAR (BR-09: SIKLUS N-1 & N BERBASIS ISO-8601) -->
    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                <i class="far fa-calendar-alt text-blue-600"></i> Siklus Koordinasi:
            </span>
            <span class="text-xs bg-blue-50 text-blue-800 font-semibold px-2 py-0.5 rounded border border-blue-200">
                Minggu Berjalan: W{{ $manajemenTugas['minggu_berjalan'] ?? now()->isoWeek() }}
            </span>
        </div>

        <div class="flex items-center gap-1.5">
            <a href="{{ route('dashboard', ['siklus' => 'semua']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? 'semua') === 'semua' ? 'bg-slate-800 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                Semua Siklus
            </a>
            <a href="{{ route('dashboard', ['siklus' => 'N']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? '') === 'N' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                <i class="fas fa-play-circle mr-1"></i> Minggu N (Perencanaan)
            </a>
            <a href="{{ route('dashboard', ['siklus' => 'N-1']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($manajemenTugas['siklus_aktif'] ?? '') === 'N-1' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                <i class="fas fa-history mr-1"></i> Minggu N-1 (Evaluasi)
            </a>
        </div>
    </div>

    <!-- 4 KARTU KPI UTAMA PERSIS GAMBAR 2 -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- 1. PEMBAHASAN (BIRU) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-blue-200 bg-blue-50/30 flex flex-col">
            <div class="bg-[#1e40af] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                PEMBAHASAN
            </div>
            <div class="p-6 text-center my-auto">
                <span class="text-4xl md:text-5xl font-black text-gray-800">{{ $manajemenTugas['kpi']['pembahasan'] }}</span>
            </div>
        </div>

        <!-- 2. OPEN (ORANYE) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-orange-200 bg-orange-50/30 flex flex-col">
            <div class="bg-[#ea580c] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                OPEN
            </div>
            <div class="p-6 text-center my-auto">
                <span class="text-4xl md:text-5xl font-black text-gray-800">{{ $manajemenTugas['kpi']['open'] }}</span>
            </div>
        </div>

        <!-- 3. ON PROGRESS (HIJAU) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-emerald-200 bg-emerald-50/30 flex flex-col">
            <div class="bg-[#16a34a] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                ON PROGRESS
            </div>
            <div class="p-6 text-center my-auto">
                <span class="text-4xl md:text-5xl font-black text-gray-800">{{ $manajemenTugas['kpi']['on_progress'] }}</span>
            </div>
        </div>

        <!-- 4. ALERT / OVERDUE (MERAH) -->
        <div class="rounded-xl overflow-hidden shadow-sm border border-red-200 bg-red-50/30 flex flex-col">
            <div class="bg-[#dc2626] text-white py-2 px-3 text-center text-xs font-black uppercase tracking-wider">
                OVERDUE / ALERT
            </div>
            <div class="p-6 text-center my-auto">
                <span class="text-4xl md:text-5xl font-black text-red-600">{{ $manajemenTugas['kpi']['overdue'] }}</span>
            </div>
        </div>
    </div>

    <!-- MID GRID: 4 PANEL PEMANTAUAN PERSIS GAMBAR 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- PANEL 1: STATUS PEKERJAAN (TABEL + CHART) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#1e3a8a] text-white px-4 py-2 text-xs font-bold uppercase tracking-wider">
                STATUS PEKERJAAN
            </div>
            <div class="p-4 space-y-4 flex-1 flex flex-col justify-between">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-1.5 font-bold">Status</th>
                            <th class="pb-1.5 font-bold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        <tr>
                            <td class="py-1.5">Open</td>
                            <td class="py-1.5 text-right font-mono font-bold">{{ $manajemenTugas['status_pekerjaan']['open'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Assigned / On Progress</td>
                            <td class="py-1.5 text-right font-mono font-bold">{{ $manajemenTugas['status_pekerjaan']['in_progress'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Closed / Selesai</td>
                            <td class="py-1.5 text-right font-mono font-bold">{{ $manajemenTugas['status_pekerjaan']['closed'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Mini Bar Chart -->
                <div class="bg-slate-50 p-3 rounded-lg border border-gray-100 h-44 flex flex-col justify-end">
                    <p class="text-[10px] text-gray-400 font-bold mb-2 text-center">Grafik Status Pekerjaan</p>
                    <div class="h-32 flex items-end justify-around gap-2 px-6">
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_pekerjaan']['open'] }}</span>
                            <div class="w-full bg-blue-600 rounded-t" style="height: {{ max(10, min(100, $manajemenTugas['status_pekerjaan']['open'] * 15)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Open</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_pekerjaan']['in_progress'] }}</span>
                            <div class="w-full bg-amber-500 rounded-t" style="height: {{ max(10, min(100, $manajemenTugas['status_pekerjaan']['in_progress'] * 15)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Progress</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_pekerjaan']['closed'] }}</span>
                            <div class="w-full bg-emerald-600 rounded-t" style="height: {{ max(10, min(100, $manajemenTugas['status_pekerjaan']['closed'] * 15)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 2: STATUS ESKALASI (TABEL + CHART) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#1e3a8a] text-white px-4 py-2 text-xs font-bold uppercase tracking-wider">
                STATUS ESKALASI
            </div>
            <div class="p-4 space-y-4 flex-1 flex flex-col justify-between">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-1.5 font-bold">Status</th>
                            <th class="pb-1.5 font-bold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        <tr>
                            <td class="py-1.5">Closed - As Information</td>
                            <td class="py-1.5 text-right font-mono font-bold">{{ $manajemenTugas['status_eskalasi']['closed_info'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Escalated - 1 Level Up (TL/ASMAN)</td>
                            <td class="py-1.5 text-right font-mono font-bold">{{ $manajemenTugas['status_eskalasi']['escalated_level1'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Escalated - MUP (Puncak)</td>
                            <td class="py-1.5 text-right font-mono font-bold">{{ $manajemenTugas['status_eskalasi']['escalated_mup'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Mini Bar Chart -->
                <div class="bg-slate-50 p-3 rounded-lg border border-gray-100 h-44 flex flex-col justify-end">
                    <p class="text-[10px] text-gray-400 font-bold mb-2 text-center">Grafik Status Eskalasi Berjenjang</p>
                    <div class="h-32 flex items-end justify-around gap-2 px-6">
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_eskalasi']['closed_info'] }}</span>
                            <div class="w-full bg-slate-500 rounded-t" style="height: {{ max(10, min(100, $manajemenTugas['status_eskalasi']['closed_info'] * 20)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Info/Closed</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_eskalasi']['escalated_level1'] }}</span>
                            <div class="w-full bg-blue-600 rounded-t" style="height: {{ max(10, min(100, $manajemenTugas['status_eskalasi']['escalated_level1'] * 20)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Level 1 Up</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 w-1/3">
                            <span class="text-[10px] font-bold text-gray-600">{{ $manajemenTugas['status_eskalasi']['escalated_mup'] }}</span>
                            <div class="w-full bg-purple-600 rounded-t" style="height: {{ max(10, min(100, $manajemenTugas['status_eskalasi']['escalated_mup'] * 20)) }}px"></div>
                            <span class="text-[9px] text-gray-500 font-semibold truncate">Level MUP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 3: DEADLINE (ORANYE) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#ea580c] text-white px-4 py-2 text-xs font-bold uppercase tracking-wider">
                DEADLINE
            </div>
            <div class="p-4 flex-1">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-2 font-bold">Kategori</th>
                            <th class="pb-2 font-bold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Overdue (Terlambat)
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-red-600">{{ $manajemenTugas['deadline']['overdue'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-orange-400"></span> Due &le; 3 Days (Mendekati)
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-orange-600">{{ $manajemenTugas['deadline']['due_3_days'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-400"></span> On Track (Aman)
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-blue-600">{{ $manajemenTugas['deadline']['on_track'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Closed / Selesai
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-emerald-600">{{ $manajemenTugas['deadline']['closed'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PANEL 4: MONITORING PIC (HIJAU) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-[#16a34a] text-white px-4 py-2 text-xs font-bold uppercase tracking-wider">
                MONITORING PIC
            </div>
            <div class="p-4 flex-1">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-left">
                            <th class="pb-2 font-bold w-1/3">PIC</th>
                            <th class="pb-2 font-bold">Tugas & Instruksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($manajemenTugas['monitoring_pic'] as $mPic)
                            <tr>
                                <td class="py-2 font-bold text-gray-800">{{ $mPic->assignee->name ?? 'Staff Pelaksana' }}</td>
                                <td class="py-2 text-gray-600">{{ $mPic->title }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-2 font-medium">Asman Operasi</td>
                                <td class="py-2 text-gray-500">Butuh persetujuan tindak lanjut overhaul</td>
                            </tr>
                            <tr>
                                <td class="py-2 font-medium">Team Leader CBM</td>
                                <td class="py-2 text-gray-500">Mempelajari klausul analisis getaran turbin</td>
                            </tr>
                            <tr>
                                <td class="py-2 font-medium">Sekretaris Teknik</td>
                                <td class="py-2 text-gray-500">Menyusun undangan rapat koordinasi mingguan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ==================== DAFTAR PRIORITAS / ALERT PERSIS GAMBAR 2 ==================== -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Header Merah Tua -->
        <div class="bg-[#991b1b] text-white px-4 py-2.5 text-xs font-black uppercase tracking-wider flex items-center justify-between">
            <span class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> DAFTAR PRIORITAS / ALERT
            </span>
            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded font-mono font-bold">{{ count($manajemenTugas['prioritas_alert']) }} Item Kritis</span>
        </div>

        <!-- Tabel Monitoring Persis Gambar 2 -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#1e3a8a] text-white font-bold">
                    <tr>
                        <th class="px-4 py-2.5 w-10 text-center">No</th>
                        <th class="px-4 py-2.5">PIC</th>
                        <th class="px-4 py-2.5">Informasi</th>
                        <th class="px-4 py-2.5">Action</th>
                        <th class="px-4 py-2.5">Due Date</th>
                        <th class="px-4 py-2.5">Status Pekerjaan</th>
                        <th class="px-4 py-2.5">Status Deadline</th>
                        <th class="px-4 py-2.5 text-center">Eskalasi</th>
                        <th class="px-4 py-2.5 text-center">Sifat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700 font-medium">
                    @forelse($manajemenTugas['prioritas_alert'] as $idx => $alert)
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="px-4 py-2.5 text-center text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-2.5 font-bold text-gray-800">{{ $alert->owner->name ?? ($alert->reporter->name ?? 'Staff') }}</td>
                            <td class="px-4 py-2.5 text-gray-800 font-semibold max-w-xs truncate" title="{{ $alert->title }}">
                                <a href="{{ route('issues.show', $alert->id) }}" class="hover:text-blue-600 hover:underline">
                                    {{ $alert->title }}
                                </a>
                            </td>
                            <td class="px-4 py-2.5 text-gray-500 max-w-xs truncate" title="{{ $alert->description }}">
                                {{ Str::limit($alert->description, 35) }}
                            </td>
                            <td class="px-4 py-2.5 font-mono text-gray-600">
                                {{ $alert->due_date ? \Carbon\Carbon::parse($alert->due_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-2.5">
                                @if(in_array($alert->status, ['Resolved', 'Closed', 'Selesai']))
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold">Closed</span>
                                @elseif($alert->status === 'Open')
                                    <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold">Open</span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded text-[10px] font-bold">On Progress</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
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
                            <td class="px-4 py-2.5 text-center">
                                <span class="bg-slate-100 text-slate-800 font-bold px-2 py-0.5 rounded text-[10px] border border-slate-200">
                                    {{ $alert->current_level }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
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
