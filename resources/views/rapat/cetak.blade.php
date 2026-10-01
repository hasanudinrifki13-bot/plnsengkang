<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risalah Rapat - {{ $meeting->type }} - PLN NP UP Sengkang</title>
    <!-- Tailwind CSS & Google Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                padding: 0 !important; 
                margin: 0 !important;
            }
            .print-page { 
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 !important; 
                width: 100% !important;
                max-width: 100% !important;
                padding: 20mm !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="bg-slate-100 py-10 px-4 min-h-screen">

    <!-- FLOATING ACTIONS BAR (NO-PRINT) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-200 gap-3">
        <a href="{{ route('rapat.show', $meeting->id) }}" class="text-xs font-semibold text-gray-600 hover:text-blue-600 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Agenda Rapat
        </a>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 font-medium hidden sm:inline">Format: Kertas A4 Standar Risalah PLN</span>
            <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak / Ekspor PDF
            </button>
        </div>
    </div>

    <!-- PRINT DOCUMENT CONTAINER (A4 SIZE) -->
    <div class="print-page max-w-4xl mx-auto bg-white p-12 rounded-xl shadow-lg border border-gray-200 text-gray-800 text-xs leading-normal">
        
        <!-- KOP SURAT RESMI PT PLN NUSANTARA POWER -->
        <div class="border-b-2 border-gray-900 pb-4 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 bg-blue-700 text-white font-black text-xl rounded-lg flex items-center justify-center shadow-sm">
                        PLN
                    </div>
                    <div>
                        <h1 class="text-base font-extrabold tracking-tight text-gray-900 uppercase">PT PLN NUSANTARA POWER</h1>
                        <p class="text-[11px] font-bold text-gray-700 uppercase">UNIT PELAKSANA PENGENDALIAN PEMBANGKITAN SENGKANG</p>
                        <p class="text-[10px] text-gray-500">Jl. Poros Sengkang - Siwa Km. 20, Kabupaten Wajo, Sulawesi Selatan 90911</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[9px] font-mono text-gray-400 block uppercase tracking-wider">Nomor Risalah Rapat</span>
                    <span class="text-xs font-mono font-bold text-gray-900 bg-slate-50 px-2.5 py-1 rounded border border-gray-200 inline-block mt-0.5">
                        RIS-SKG/{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('Y') }}/W{{ $meeting->week_number ?? now()->isoWeek() }}/00{{ $meeting->id }}
                    </span>
                    <span class="text-[9px] text-emerald-700 font-semibold block mt-1">Status: Terverifikasi Sistem</span>
                </div>
            </div>
        </div>

        <!-- JUDUL RISALAH -->
        <div class="text-center mb-6">
            <h2 class="text-sm font-extrabold uppercase underline tracking-wider text-gray-900">NOTULEN & RISALAH RAPAT KOORDINASI MINGGUAN</h2>
            <p class="text-xs font-bold text-blue-700 mt-1 uppercase">{{ $meeting->type }}</p>
            <p class="text-[11px] text-gray-500">Siklus Mingguan: Minggu Ke-{{ $meeting->week_number ?? now()->isoWeek() }} Tahun {{ $meeting->year ?? now()->year }}</p>
        </div>

        <!-- INFORMASI PELAKSANAAN RAPAT -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
            <table class="w-full text-xs">
                <tbody>
                    <tr>
                        <td class="w-32 py-1 font-bold text-gray-700">Hari / Tanggal</td>
                        <td class="w-4">:</td>
                        <td class="py-1">{{ \Carbon\Carbon::parse($meeting->meeting_date)->isoFormat('dddd, D MMMM Y') }}</td>
                        <td class="w-32 py-1 font-bold text-gray-700">Pimpinan Rapat</td>
                        <td class="w-4">:</td>
                        <td class="py-1 font-semibold text-gray-900">{{ $meeting->leader->name ?? 'Pimpinan Rapat' }} ({{ $meeting->leader->position->name ?? 'Pejabat Unit' }})</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-bold text-gray-700">Waktu Pelaksanaan</td>
                        <td>:</td>
                        <td class="py-1">{{ $meeting->time_start ?? '08:00' }} - {{ $meeting->time_end ?? '10:00' }} WITA</td>
                        <td class="py-1 font-bold text-gray-700">Mode Rapat</td>
                        <td>:</td>
                        <td class="py-1">{{ $meeting->meeting_mode ?? 'Offline' }} {{ !empty($meeting->zoom_meeting_id) ? '(Zoom ID: ' . $meeting->zoom_meeting_id . ')' : '' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-bold text-gray-700">Tempat Pelaksanaan</td>
                        <td>:</td>
                        <td class="py-1">{{ $meeting->location ?? 'Ruang Rapat UP Sengkang' }}</td>
                        <td class="py-1 font-bold text-gray-700">Seksi / Unit</td>
                        <td>:</td>
                        <td class="py-1">Operasi & Pemeliharaan UP Sengkang</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- CATATAN & KESIMPULAN RAPAT -->
        @if(!empty($meeting->notes))
            <div class="mb-6 bg-blue-50/70 p-4 rounded-lg border border-blue-200">
                <h3 class="font-bold text-blue-950 text-xs uppercase mb-1 flex items-center gap-1.5">
                    <i class="fas fa-quote-left text-blue-600"></i> Arahan Pokok & Kesimpulan Pimpinan:
                </h3>
                <p class="text-xs text-blue-900 leading-relaxed font-normal">{{ $meeting->notes }}</p>
            </div>
        @endif

        <!-- I. AGENDA PEMBAHASAN -->
        <div class="mb-6">
            <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
                <i class="fas fa-list-ol text-gray-500"></i> I. Agenda Pembahasan Terstruktur
            </h3>
            <ol class="list-decimal pl-5 space-y-1 text-xs text-gray-700">
                <li>Penyampaian arahan kebijakan dan keselamatan ketenagalistrikan (K3L) pembangkit.</li>
                <li>Evaluasi ketercapaian task dan mitigasi pekerjaan tertunda dari minggu sebelumnya (Siklus N-1).</li>
                <li>Identifikasi dan pembahasan eskalasi kendala operasional unit pembangkit.</li>
                <li>Perumusan penetapan keputusan resmi pimpinan berwenang (*Lowest Competent Level*).</li>
                <li>Penyusunan target task baru dan penugasan Person in Charge (PIC) minggu berjalan (Siklus N).</li>
            </ol>
        </div>

        <!-- II. PEMBAHASAN ESKALASI -->
        <div class="mb-6">
            <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
                <i class="fas fa-level-up-alt text-gray-500"></i> II. Rekapitulasi Eskalasi Kendala Operasional
            </h3>
            <table class="w-full text-left text-xs border border-gray-300">
                <thead class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                    <tr>
                        <th class="p-2 border-r border-gray-300 w-24">No Eskalasi</th>
                        <th class="p-2 border-r border-gray-300">Uraian Eskalasi Kendala</th>
                        <th class="p-2 border-r border-gray-300 w-20 text-center">Jenjang</th>
                        <th class="p-2 border-r border-gray-300 w-20 text-center">Prioritas</th>
                        <th class="p-2 w-28">Status Putusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($issues as $iss)
                        <tr>
                            <td class="p-2 border-r border-gray-200 font-mono font-bold text-blue-700">#{{ $iss->issue_no }}</td>
                            <td class="p-2 border-r border-gray-200 font-medium">{{ $iss->title }}</td>
                            <td class="p-2 border-r border-gray-200 text-center font-bold">Level {{ $iss->current_level }}</td>
                            <td class="p-2 border-r border-gray-200 text-center">
                                <span class="font-semibold {{ $iss->priority === 'CRITICAL' || $iss->priority === 'HIGH' ? 'text-red-600' : 'text-gray-700' }}">{{ $iss->priority }}</span>
                            </td>
                            <td class="p-2 font-medium">{{ $iss->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-3 text-center text-gray-400 italic">Tidak ada eskalasi kendala yang terdaftar pada agenda rapat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- III. KEPUTUSAN RESMI PIMPINAN BERWENANG -->
        <div class="mb-6">
            <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
                <i class="fas fa-gavel text-gray-500"></i> III. Keputusan Resmi Pimpinan Rapat (Decisions)
            </h3>
            <table class="w-full text-left text-xs border border-gray-300">
                <thead class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                    <tr>
                        <th class="p-2 border-r border-gray-300 w-10 text-center">No</th>
                        <th class="p-2 border-r border-gray-300 w-28">Eskalasi Terkait</th>
                        <th class="p-2 border-r border-gray-300">Pernyataan Keputusan Eksekutif</th>
                        <th class="p-2 border-r border-gray-300 w-24 text-center">Level Pemutus</th>
                        <th class="p-2 w-40">Pejabat Pemutus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($decisions as $dIdx => $dec)
                        <tr>
                            <td class="p-2 border-r border-gray-200 text-center font-mono">{{ $dIdx + 1 }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono font-bold text-blue-700">#{{ $dec->issue->issue_no ?? '-' }}</td>
                            <td class="p-2 border-r border-gray-200 font-medium">{{ $dec->decision }}</td>
                            <td class="p-2 border-r border-gray-200 text-center font-bold text-emerald-700">Level {{ $dec->decision_level }}</td>
                            <td class="p-2 font-semibold">{{ $dec->decisionMaker->name ?? 'Pejabat Unit' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-3 text-center text-gray-400 italic">Belum ada keputusan formal yang dicatat pada sesi rapat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- IV. RENCANA TINDAK LANJUT (ACTION ITEMS / TASK MANAGEMENT) -->
        <div class="mb-6">
            <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
                <i class="fas fa-tasks text-gray-500"></i> IV. Rencana Tindak Lanjut Task (Action Items)
            </h3>
            <table class="w-full text-left text-xs border border-gray-300">
                <thead class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                    <tr>
                        <th class="p-2 border-r border-gray-300 w-10 text-center">No</th>
                        <th class="p-2 border-r border-gray-300">Uraian Task Tindak Lanjut</th>
                        <th class="p-2 border-r border-gray-300 w-36">PIC Pelaksana</th>
                        <th class="p-2 border-r border-gray-300 w-24">Batas Target</th>
                        <th class="p-2 w-24 text-center">Progres Fisik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($tasks->take(6) as $idx => $t)
                        <tr>
                            <td class="p-2 border-r border-gray-200 text-center font-mono">{{ $idx + 1 }}</td>
                            <td class="p-2 border-r border-gray-200 font-medium">
                                {{ $t->title }}
                                @if($t->completion_attachment)
                                    <span class="text-[9px] text-emerald-700 font-bold block mt-0.5"><i class="fas fa-paperclip"></i> Bukti Lampiran Tersedia</span>
                                @endif
                            </td>
                            <td class="p-2 border-r border-gray-200 font-semibold">{{ $t->assignee->name ?? 'Staff Pelaksana' }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $t->due_date ? \Carbon\Carbon::parse($t->due_date)->format('d/m/Y') : '-' }}</td>
                            <td class="p-2 text-center font-bold">
                                <span class="{{ $t->progress_percent >= 100 ? 'text-emerald-700' : 'text-blue-700' }}">{{ $t->progress_percent }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-3 text-center text-gray-400 italic">Belum ada item tindak lanjut yang ditetapkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- V. DAFTAR KEHADIRAN PESERTA RAPAT -->
        <div class="mb-8">
            <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
                <i class="fas fa-users text-gray-500"></i> V. Presensi & Daftar Hadir Peserta Koordinasi
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px]">
                @foreach($users->take(9) as $u)
                    <div class="p-2 bg-slate-50 border border-gray-200 rounded flex items-center justify-between">
                        <div>
                            <p class="font-bold text-gray-800">{{ $u->name }}</p>
                            <p class="text-[9px] text-gray-500">{{ $u->position->name ?? 'Staf Pelaksana' }}</p>
                        </div>
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                            Hadir
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- VERIFIKASI DIGITAL & LEMBAR PENGESAHAN -->
        <div class="mt-8 pt-6 border-t-2 border-gray-300">
            <div class="flex flex-col sm:flex-row justify-between items-end gap-6 text-xs">
                
                <!-- Digital QR Verification Box -->
                <div class="flex items-center gap-3 bg-slate-50 border border-gray-200 p-3 rounded-lg max-w-sm">
                    <div class="w-16 h-16 bg-white p-1 border border-gray-300 rounded shrink-0 flex items-center justify-center">
                        <!-- Barcode SVG Simulation -->
                        <svg class="w-14 h-14" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="100" height="100" fill="white"/>
                            <rect x="10" y="10" width="25" height="25" fill="#1e3a8a"/>
                            <rect x="15" y="15" width="15" height="15" fill="white"/>
                            <rect x="65" y="10" width="25" height="25" fill="#1e3a8a"/>
                            <rect x="70" y="15" width="15" height="15" fill="white"/>
                            <rect x="10" y="65" width="25" height="25" fill="#1e3a8a"/>
                            <rect x="15" y="70" width="15" height="15" fill="white"/>
                            <rect x="45" y="10" width="10" height="15" fill="#1e3a8a"/>
                            <rect x="40" y="35" width="20" height="10" fill="#1e3a8a"/>
                            <rect x="45" y="55" width="10" height="25" fill="#1e3a8a"/>
                            <rect x="65" y="45" width="25" height="10" fill="#1e3a8a"/>
                            <rect x="65" y="65" width="15" height="25" fill="#1e3a8a"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[9px] font-mono text-gray-500 uppercase block">Digital Document Signature</span>
                        <p class="text-[10px] text-gray-600 leading-tight mt-0.5">
                            Risalah rapat ini disahkan secara elektronik melalui sistem Koordinasi & Eskalasi <b>PT PLN Nusantara Power UP Sengkang</b>.
                        </p>
                    </div>
                </div>

                <!-- Tanda Tangan Pejabat Pengesah -->
                <div class="text-right sm:text-center w-full sm:w-60">
                    <p class="font-bold text-gray-700 mb-14">
                        Sengkang, {{ \Carbon\Carbon::parse($meeting->meeting_date)->isoFormat('D MMMM Y') }}<br>
                        <span class="font-normal text-gray-500">Pimpinan Rapat Koordinasi,</span>
                    </p>
                    <p class="font-bold text-gray-900 underline uppercase tracking-wide">
                        {{ $meeting->leader->name ?? ($asman->name ?? 'Pimpinan Unit') }}
                    </p>
                    <p class="text-gray-500 text-[10px] font-mono">
                        NID: {{ $meeting->leader->employee_no ?? '9216281ZX' }} | {{ $meeting->leader->position->name ?? 'Team Leader / Asman' }}
                    </p>
                </div>

            </div>
        </div>

    </div>

    <!-- Script auto-trigger print dialog if requested with ?print=1 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.search.includes('print=1')) {
                window.print();
            }
        });
    </script>
</body>
</html>
