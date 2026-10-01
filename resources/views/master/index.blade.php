@extends('layouts.app')

@section('title', 'Master Data Organisasi & Pegawai')
@section('page_title', 'Master Data Organisasi')

@section('content')
<div class="space-y-6">

    <!-- Header & Navigasi Tab Master Data Sesuai Excel Workbook -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Master Organisasi & Data Manajemen UP Sengkang</h2>
                <p class="text-xs text-gray-500 mt-1">Struktur relasi hirarki Incumbent-Parent, data 125+ karyawan, dan organogram visual PLN NP UP Sengkang.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalTambahHierarchy" onclick="openModal('modalTambahHierarchy')" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2.5 rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                    <i class="fas fa-layer-group"></i> Tambah Relasi Hirarki
                </button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalTambahUser" onclick="openModal('modalTambahUser')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                    <i class="fas fa-user-plus"></i> Tambah Pegawai Baru
                </button>
            </div>
        </div>

        <!-- Tab Sesuai Tab Excel (Master_Organisasi, Struktur Organisasi, Data Karyawan, Referensi) -->
        <div class="flex gap-6 border-b border-gray-100 text-xs font-semibold pt-4 overflow-x-auto">
            <button type="button" onclick="switchMasterTab('hierarchy')" id="tab-btn-hierarchy" class="pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap">
                <i class="fas fa-table mr-1.5"></i> Master_Organisasi (Incumbent & Parent)
            </button>
            <button type="button" onclick="switchMasterTab('organogram')" id="tab-btn-organogram" class="pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap">
                <i class="fas fa-sitemap mr-1.5"></i> Struktur Organisasi (Organogram)
            </button>
            <button type="button" onclick="switchMasterTab('users')" id="tab-btn-users" class="pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap">
                <i class="fas fa-users mr-1.5"></i> Data Karyawan ({{ $users->total() }})
            </button>
            <button type="button" onclick="switchMasterTab('units')" id="tab-btn-units" class="pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap">
                <i class="fas fa-building mr-1.5"></i> Referensi Unit Kerja ({{ $units->count() }})
            </button>
            <button type="button" onclick="switchMasterTab('roles')" id="tab-btn-roles" class="pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap">
                <i class="fas fa-shield-alt mr-1.5"></i> Matriks Wewenang BSD
            </button>
        </div>
    </div>

    <!-- ==================== TAB 1: MASTER_ORGANISASI (PERSIS GAMBAR 1 SPREADSHEET) ==================== -->
    <div id="master-tab-hierarchy" class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col">
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3 bg-slate-50/50 rounded-t-xl">
            <div>
                <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Tabel Pemetaan Hirarki Relasi: Incumbent &rarr; Parent
                </span>
                <p class="text-[11px] text-gray-500 mt-0.5">Format pemetaan struktural sesuai lembar kerja sheet <code>Master_Organisasi</code></p>
            </div>
            <div class="text-xs text-gray-600 font-semibold bg-white px-3 py-1.5 rounded-md border border-gray-200">
                Total Relasi: <span class="text-blue-600 font-bold font-mono">{{ $hierarchies->count() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#0f2744] text-white font-bold">
                    <tr>
                        <th class="px-6 py-3.5 w-12 text-center">No</th>
                        <th class="px-6 py-3.5 text-sky-200 font-mono">Incumbent</th>
                        <th class="px-6 py-3.5 text-amber-200 font-mono">Parent</th>
                        <th class="px-6 py-3.5">Uraian Jabatan Incumbent</th>
                        <th class="px-6 py-3.5">Atasan Langsung (Parent Title)</th>
                        <th class="px-6 py-3.5">Pejabat Saat Ini</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700 font-medium">
                    @forelse($hierarchies as $idx => $h)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="px-6 py-3.5 text-center text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-6 py-3.5 font-mono font-black text-blue-700 bg-blue-50/30">
                                {{ $h->incumbent }}
                            </td>
                            <td class="px-6 py-3.5 font-mono font-black text-amber-700 bg-amber-50/30">
                                {{ $h->parent }}
                            </td>
                            <td class="px-6 py-3.5 font-bold text-gray-800">{{ $h->incumbent_title ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-600">{{ $h->parent_title ?? '-' }}</td>
                            <td class="px-6 py-3.5 font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px] flex items-center justify-center">
                                    {{ strtoupper(substr($h->person_name ?? 'P', 0, 1)) }}
                                </span>
                                {{ $h->person_name ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="editHierarchy({{ json_encode($h) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Edit Relasi">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('master.hierarchies.destroy', $h->id) }}" method="POST" onsubmit="return confirm('Hapus relasi {{ $h->incumbent }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition" title="Hapus Relasi">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada data relasi incumbent. Silakan tambahkan atau jalankan seeder.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== TAB 2: BAGAN STRUKTUR ORGANISASI (ORGANOGRAM RESMI) ==================== -->
    <div id="master-tab-organogram" class="hidden bg-white rounded-xl border border-gray-100 shadow-sm p-6 overflow-x-auto">
        <div class="min-w-[1050px] pb-6">
            
            <div class="text-center mb-6">
                <span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                    Struktur Organisasi Unit Pelaksana
                </span>
                <h3 class="text-base font-extrabold text-gray-800 mt-2">PT PLN NUSANTARA POWER UP SENGKANG</h3>
            </div>

            <!-- LEVEL 1: MANAGER UP SENGKANG & ATTACHED TL K3 & LINGKUNGAN -->
            <div class="flex items-center justify-center gap-12 relative mb-10">
                
                <!-- TOP CARD: MANAGER UP SENGKANG -->
                <div class="relative z-10 w-72 bg-white rounded-xl border-2 border-sky-500 shadow-lg p-3 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-sky-100 text-sky-700 border-2 border-sky-500 flex items-center justify-center font-bold text-sm shadow-sm overflow-hidden mb-2">
                        <img src="https://ui-avatars.com/api/?name=Moh+Furqon+Akhsani&background=0284c7&color=fff" class="w-full h-full object-cover" alt="MUP">
                    </div>
                    <div class="bg-sky-600 text-white py-1 px-3 rounded-md text-[10px] font-extrabold uppercase tracking-wider mb-1">
                        MANAGER UP SENGKANG
                    </div>
                    <h4 class="text-xs font-bold text-gray-900">MOH. FURQON AKHSANI</h4>
                    <p class="text-[10px] text-gray-500 font-mono mt-0.5">NID: 8108050JA</p>
                </div>

                <!-- RIGHT SIDE: TL K3 & TL LINGKUNGAN -->
                <div class="flex flex-col gap-3 pl-8 border-l-2 border-dashed border-sky-400">
                    <div class="w-64 bg-red-600 text-white rounded-lg p-2.5 shadow-md flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs shrink-0">
                            YK
                        </div>
                        <div class="truncate">
                            <span class="block text-[9px] font-extrabold uppercase tracking-wider text-red-100">TEAM LEADER K3 & KEAMANAN</span>
                            <span class="block text-xs font-bold truncate">YOGA KURNIAWAN</span>
                            <span class="block text-[9px] text-red-200 font-mono">NID: 9315231TA</span>
                        </div>
                    </div>

                    <div class="w-64 bg-emerald-700 text-white rounded-lg p-2.5 shadow-md flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs shrink-0">
                            AM
                        </div>
                        <div class="truncate">
                            <span class="block text-[9px] font-extrabold uppercase tracking-wider text-emerald-100">TEAM LEADER LINGKUNGAN</span>
                            <span class="block text-xs font-bold truncate">H. ABDUL MALIK</span>
                            <span class="block text-[9px] text-emerald-200 font-mono">NID: 7223005PKW</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- GARIS DISTRIBUSI CABANG 4 ASMAN -->
            <div class="w-full flex justify-center mb-6">
                <div class="w-[90%] border-t-2 border-sky-500 relative">
                    <span class="absolute -top-3 left-[12%] w-2 h-6 border-l-2 border-sky-500"></span>
                    <span class="absolute -top-3 left-[37%] w-2 h-6 border-l-2 border-sky-500"></span>
                    <span class="absolute -top-3 left-[63%] w-2 h-6 border-l-2 border-sky-500"></span>
                    <span class="absolute -top-3 left-[88%] w-2 h-6 border-l-2 border-sky-500"></span>
                </div>
            </div>

            <!-- 4 PILAR UTAMA ASISTEN MANAJER & TEAM LEADERNYA -->
            <div class="grid grid-cols-4 gap-4">
                
                <!-- KOLOM 1: OPERASI (KUNING) -->
                <div class="space-y-3">
                    <div class="bg-amber-500 text-gray-900 rounded-xl p-3 shadow-md border-2 border-amber-600 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-amber-700 text-xs shadow-sm mb-1.5 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name=Muhammad+Ismail&background=f59e0b&color=fff" class="w-full h-full object-cover" alt="Asman Operasi">
                        </div>
                        <p class="text-[9px] font-black uppercase tracking-wider text-gray-950">ASSISTANT MANAGER OPERASI</p>
                        <h5 class="text-xs font-bold text-gray-900">H. MUHAMMAD ISMAIL</h5>
                        <p class="text-[9px] font-mono text-gray-800">NID: 7323002PKW</p>
                    </div>

                    <div class="space-y-2 border-t-2 border-amber-400 pt-2">
                        @php
                            $tlsOperasi = [
                                ['TL PRODUKSI BLOK. 1A', 'MURSALAM', '8915056TD'],
                                ['TL PRODUKSI BLOK. 1B', 'MEDY IVANTRY TANDI BUA', '8917074BL'],
                                ['TL PRODUKSI BLOK. 1C', 'H. HERMAN JAYA, ST', '7423003PKW'],
                                ['TL PRODUKSI BLOK. 1D', 'MUH NASAR D', '7223009PKW'],
                                ['TL PRODUKSI BLOK. 2B', 'NADAR', '8223015PKW'],
                                ['TL PRODUKSI BLOK. 2C', 'AGUSNAWAN / KHARAS ANDRI', '8323018PKW'],
                                ['TL PRODUKSI BLOK. 2D', 'SUNARDI SM', '8623017PKW'],
                                ['TL RENDAL OPERASI', 'SINUNG RAHARJO', '8812558PC'],
                            ];
                        @endphp
                        @foreach($tlsOperasi as $tl)
                            <div class="bg-amber-100/90 border border-amber-300 rounded-lg p-2 text-xs hover:shadow-sm transition">
                                <span class="block text-[8px] font-black text-amber-900 uppercase tracking-tight">{{ $tl[0] }}</span>
                                <span class="block font-bold text-gray-900 text-[11px] truncate">{{ $tl[1] }}</span>
                                <span class="block text-[9px] text-gray-600 font-mono">NID: {{ $tl[2] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- KOLOM 2: PEMELIHARAAN (BIRU TUA) -->
                <div class="space-y-3">
                    <div class="bg-blue-900 text-white rounded-xl p-3 shadow-md border-2 border-blue-950 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-blue-900 text-xs shadow-sm mb-1.5 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name=Cahya+Patria&background=1e3a8a&color=fff" class="w-full h-full object-cover" alt="Asman Har">
                        </div>
                        <p class="text-[9px] font-black uppercase tracking-wider text-blue-200">ASSISTANT MANAGER PEMELIHARAAN</p>
                        <h5 class="text-xs font-bold text-white">CAHYA PATRIA C.T</h5>
                        <p class="text-[9px] font-mono text-blue-300">NID: 8208036JA</p>
                    </div>

                    <div class="space-y-2 border-t-2 border-blue-800 pt-2">
                        @php
                            $tlsHar = [
                                ['TL HAR MESIN 2 & SIPIL', 'SAINAL TAJANG', '7823006PKW'],
                                ['TL RENDAL HAR', 'YERSON', '9015144ZJY'],
                                ['TL HAR LISTRIK', 'SUHAIMI', '9115227KP'],
                                ['TL HAR KONTROL & INSTRUMENT', 'HENDRA GULANG', '8323007PKW'],
                                ['TL INVENTORY & GUDANG', 'NURHALIM', '8716092PC'],
                                ['TL OUTAGE MANAGEMENT', 'MUHAMMAD SIDDIQ B.', '9015132ZJY'],
                            ];
                        @endphp
                        @foreach($tlsHar as $tl)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-2 text-xs hover:shadow-sm transition">
                                <span class="block text-[8px] font-black text-blue-900 uppercase tracking-tight">{{ $tl[0] }}</span>
                                <span class="block font-bold text-gray-900 text-[11px] truncate">{{ $tl[1] }}</span>
                                <span class="block text-[9px] text-gray-600 font-mono">NID: {{ $tl[2] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- KOLOM 3: ENGINEERING (HITAM / GELAP) -->
                <div class="space-y-3">
                    <div class="bg-slate-900 text-white rounded-xl p-3 shadow-md border-2 border-black text-center">
                        <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-slate-900 text-xs shadow-sm mb-1.5 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name=Fuad+Anwar&background=0f172a&color=fff" class="w-full h-full object-cover" alt="Asman Eng">
                        </div>
                        <p class="text-[9px] font-black uppercase tracking-wider text-slate-300">ASSISTANT MANAGER ENGINEERING</p>
                        <h5 class="text-xs font-bold text-white">FUAD ANWAR</h5>
                        <p class="text-[9px] font-mono text-slate-400">NID: 8609070JA</p>
                    </div>

                    <div class="space-y-2 border-t-2 border-slate-700 pt-2">
                        @php
                            $tlsEng = [
                                ['TL HAR CONDITION BASE MAIN. (CBM)', 'ARMAN ARIFIN', '8312589KP'],
                                ['TL SYSTEM OWNER & CA', 'HARI AKBAR SYUAIB', '9417078BL'],
                                ['TL MMRK', 'AFFANDY HERMAWAN', '7423008PKW'],
                            ];
                        @endphp
                        @foreach($tlsEng as $tl)
                            <div class="bg-slate-100 border border-slate-300 rounded-lg p-2 text-xs hover:shadow-sm transition">
                                <span class="block text-[8px] font-black text-slate-800 uppercase tracking-tight">{{ $tl[0] }}</span>
                                <span class="block font-bold text-gray-900 text-[11px] truncate">{{ $tl[1] }}</span>
                                <span class="block text-[9px] text-gray-600 font-mono">NID: {{ $tl[2] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- KOLOM 4: BUSINESS SUPPORT (SILVER / SLATE) -->
                <div class="space-y-3">
                    <div class="bg-slate-600 text-white rounded-xl p-3 shadow-md border-2 border-slate-700 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-slate-700 text-xs shadow-sm mb-1.5 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name=Pran+Moch+Soleh&background=475569&color=fff" class="w-full h-full object-cover" alt="Asman BS">
                        </div>
                        <p class="text-[9px] font-black uppercase tracking-wider text-slate-200">ASSISTANT MANAGER BUSINESS SUPPORT</p>
                        <h5 class="text-xs font-bold text-white">PRAN MOCH SOLEH</h5>
                        <p class="text-[9px] font-mono text-slate-300">NID: 8713108ZJY</p>
                    </div>

                    <div class="space-y-2 border-t-2 border-slate-500 pt-2">
                        @php
                            $tlsBs = [
                                ['TL KEUANGAN', 'ZUL QADRI A. SADIK', '8915104ZJY'],
                                ['TL SDM, UMUM & CSR', 'ANDI RIDWANSYAH B.P', '9117374KP'],
                                ['TL PENGADAAN & LOGISTIK', 'AGUS SALIM HANAFI', '7823014PKW'],
                            ];
                        @endphp
                        @foreach($tlsBs as $tl)
                            <div class="bg-zinc-100 border border-zinc-300 rounded-lg p-2 text-xs hover:shadow-sm transition">
                                <span class="block text-[8px] font-black text-zinc-800 uppercase tracking-tight">{{ $tl[0] }}</span>
                                <span class="block font-bold text-gray-900 text-[11px] truncate">{{ $tl[1] }}</span>
                                <span class="block text-[9px] text-gray-600 font-mono">NID: {{ $tl[2] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- ==================== TAB 3: DATA KARYAWAN (TABEL 125+ PERSONIL) ==================== -->
    <div id="master-tab-users" class="hidden bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col">
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('master.index') }}" class="flex flex-wrap gap-3 flex-1 w-full">
                <input type="hidden" name="tab" value="users">
                <select name="role_id" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-40">
                    <option value="">Semua Peran (Role)</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>

                <select name="unit_id" onchange="this.form.submit()" class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-48">
                    <option value="">Semua Unit Kerja</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>

                <div class="relative flex-1 min-w-[200px]">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NID, nama pegawai, atau jabatan..." class="border border-gray-200 rounded-md pl-8 pr-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-full">
                </div>

                @if(request('search') || request('role_id') || request('unit_id'))
                    <a href="{{ route('master.index', ['tab' => 'users']) }}" class="px-3 py-2 border border-gray-200 text-gray-500 hover:text-gray-700 rounded-md text-xs font-semibold flex items-center gap-1.5 transition">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4 w-12">No</th>
                        <th class="px-6 py-4">NID</th>
                        <th class="px-6 py-4">Nama Pegawai</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4">Bidang Kerja / Unit</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($users as $idx => $u)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="px-6 py-4 text-gray-500">{{ $users->firstItem() + $idx }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-800">{{ $u->employee_no ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $u->name }}</p>
                                        <p class="text-[11px] text-gray-400 font-mono">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 font-medium">{{ $u->position->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $u->organizationUnit->name ?? 'UP Sengkang' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $roleName = strtoupper($u->role->name ?? '');
                                    $roleColor = match($roleName) {
                                        'MUP' => 'bg-purple-100 text-purple-800 border-purple-300 font-black',
                                        'ASMAN' => 'bg-amber-100 text-amber-800 border-amber-300 font-black',
                                        'TL' => 'bg-blue-100 text-blue-800 border-blue-300 font-bold',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200 font-medium',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded text-[10px] border {{ $roleColor }}">
                                    {{ $u->role->name ?? 'STAFF' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($u->is_active)
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold">Aktif</span>
                                @else
                                    <span class="bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded text-[10px] font-bold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="editUser({{ json_encode($u) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Edit Pegawai">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(auth()->id() !== $u->id)
                                        <form action="{{ route('master.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $u->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition" title="Hapus Pegawai">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">Tidak ada data pegawai yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100 flex justify-end">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- ==================== TAB 4: REFERENSI UNIT KERJA ==================== -->
    <div id="master-tab-units" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($units as $unit)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded border border-emerald-200">Aktif</span>
                    </div>
                    <h3 class="text-xs font-bold text-gray-800">{{ $unit->name }}</h3>
                    <p class="text-[11px] text-gray-500 mt-1">Induk: <strong class="text-gray-700">{{ $unit->parentUnit->name ?? 'Kantor Induk (Root)' }}</strong></p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400 flex items-center justify-between">
                    <span>Total Personil: <strong class="text-gray-700 font-bold">{{ $unit->users->count() }} Orang</strong></span>
                    <a href="{{ route('master.index', ['unit_id' => $unit->id]) }}" class="text-blue-600 hover:underline font-semibold">Lihat Anggota &rarr;</a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ==================== TAB 5: MATRIKS PERAN & KEWENANGAN BSD ==================== -->
    <div id="master-tab-roles" class="hidden bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
        <div>
            <h3 class="text-sm font-bold text-gray-800 mb-1">Matriks Wewenang & Alur Eskalasi Berjenjang (BSD)</h3>
            <p class="text-xs text-gray-500">Prinsip dasar koordinasi berjenjang memastikan masalah diselesaikan pada tingkat kewenangan terendah yang kompeten.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="border border-gray-200 rounded-xl p-5 bg-slate-50/50">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-full bg-slate-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                    <h4 class="font-bold text-gray-800 text-xs">Staff Pelaksana</h4>
                </div>
                <ul class="text-xs text-gray-600 space-y-2 list-disc pl-4">
                    <li>Input & lapor kendala operasional (Issue).</li>
                    <li>Update progres pekerjaan (Tasks 0-100%).</li>
                    <li>Menghadiri rapat mingguan bersama TL.</li>
                    <li>Memberikan tanggapan di forum diskusi issue.</li>
                </ul>
            </div>

            <div class="border border-emerald-200 rounded-xl p-5 bg-emerald-50/20">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                    <h4 class="font-bold text-gray-800 text-xs">Team Leader (TL)</h4>
                </div>
                <ul class="text-xs text-gray-600 space-y-2 list-disc pl-4">
                    <li>Penyaring awal kendala (Lowest Competent Level).</li>
                    <li><strong>Beri Keputusan:</strong> Mengesahkan solusi teknis & menugaskan PIC.</li>
                    <li><strong>Eskalasi:</strong> Meneruskan ke ASMAN jika butuh anggaran/wewenang lintas bidang.</li>
                </ul>
            </div>

            <div class="border border-amber-200 rounded-xl p-5 bg-amber-50/20">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-full bg-amber-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                    <h4 class="font-bold text-gray-800 text-xs">Asisten Manajer (ASMAN)</h4>
                </div>
                <ul class="text-xs text-gray-600 space-y-2 list-disc pl-4">
                    <li>Penyaring tingkat taktis & manajerial seksi.</li>
                    <li><strong>Beri Keputusan:</strong> Menetapkan alokasi sumber daya seksi.</li>
                    <li><strong>Eskalasi:</strong> Meneruskan ke MUP bila berkaitan dengan kebijakan strategis/investasi besar.</li>
                </ul>
            </div>

            <div class="border border-sky-200 rounded-xl p-5 bg-sky-50/20">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-full bg-sky-600 text-white font-bold text-xs flex items-center justify-center">4</span>
                    <h4 class="font-bold text-gray-800 text-xs">Manajer Unit (MUP)</h4>
                </div>
                <ul class="text-xs text-gray-600 space-y-2 list-disc pl-4">
                    <li>Kewenangan eksekutif tertinggi unit pelaksana.</li>
                    <li><strong>Beri Putusan MUP:</strong> Arahan resmi kebijakan operasional puncak.</li>
                    <li>Pemantauan Dashboard Executive (KPI & Keandalan).</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- ==================== MODAL TAMBAH HIRARKI (INCUMBENT & PARENT) ==================== -->
<div id="modalTambahHierarchy" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 modal-container">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-bold text-gray-800">Tambah Relasi Incumbent &rarr; Parent</h3>
            <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalTambahHierarchy')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('master.hierarchies.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kode Incumbent <span class="text-red-500">*</span></label>
                    <input type="text" name="incumbent" required placeholder="Contoh: TL RENDALOP" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 uppercase font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kode Parent (Atasan) <span class="text-red-500">*</span></label>
                    <input type="text" name="parent" required placeholder="Contoh: ASMAN OP" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 uppercase font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Uraian Jabatan Incumbent</label>
                <input type="text" name="incumbent_title" placeholder="Contoh: Team Leader Rendal Operasi" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Pejabat Incumbent Saat Ini</label>
                <input type="text" name="person_name" placeholder="Contoh: Sinung Raharjo" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalTambahHierarchy')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-sm">Simpan Relasi</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL EDIT HIRARKI ==================== -->
<div id="modalEditHierarchy" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 modal-container">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-bold text-gray-800">Edit Relasi Incumbent</h3>
            <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalEditHierarchy')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form id="formEditHierarchy" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_to" id="edit_hierarchy_redirect_to" value="{{ request()->fullUrl() }}">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kode Incumbent <span class="text-red-500">*</span></label>
                    <input type="text" name="incumbent" id="edit_h_incumbent" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 uppercase font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kode Parent <span class="text-red-500">*</span></label>
                    <input type="text" name="parent" id="edit_h_parent" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 uppercase font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Uraian Jabatan Incumbent</label>
                <input type="text" name="incumbent_title" id="edit_h_incumbent_title" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Pejabat Incumbent Saat Ini</label>
                <input type="text" name="person_name" id="edit_h_person_name" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalEditHierarchy')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL TAMBAH USER ==================== -->
<div id="modalTambahUser" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 modal-container max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-bold text-gray-800">Tambah Data Pegawai Baru</h3>
            <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalTambahUser')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('master.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap Pegawai <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Contoh: Muhammad Irfan">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NID Pegawai <span class="text-red-500">*</span></label>
                    <input type="text" name="employee_no" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Contoh: 9418290ZY">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email Resmi PLN <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="irfan@plnnp.co.id">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jabatan Spesifik</label>
                    <select name="position_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        <option value="">Pilih Jabatan...</option>
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Unit Organisasi</label>
                    <select name="unit_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        <option value="">Pilih Unit...</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Peran (Role BSD) <span class="text-red-500">*</span></label>
                    <select name="role_id" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Password Default <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" value="password123">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalTambahUser')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-sm">Simpan Pegawai</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL EDIT USER ==================== -->
<div id="modalEditUser" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 modal-container max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-bold text-gray-800">Edit Data Pegawai</h3>
            <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalEditUser')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>

        <form id="formEditUser" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_to" id="edit_user_redirect_to" value="{{ request()->fullUrl() }}">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap Pegawai <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="edit_name" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NID Pegawai</label>
                    <input type="text" name="employee_no" id="edit_employee_no" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email Resmi PLN <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="edit_email" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jabatan Spesifik</label>
                    <select name="position_id" id="edit_position_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Unit Organisasi</label>
                    <select name="unit_id" id="edit_unit_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Peran (Role BSD) <span class="text-red-500">*</span></label>
                    <select name="role_id" id="edit_role_id" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Status Keaktifan</label>
                    <select name="is_active" id="edit_is_active" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" placeholder="Password baru...">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" data-bs-dismiss="modal" onclick="closeModal('modalEditUser')" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchMasterTab(tabName) {
    const tabs = ['hierarchy', 'organogram', 'users', 'units', 'roles'];
    tabs.forEach(t => {
        const el = document.getElementById('master-tab-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (el && btn) {
            if (t === tabName) {
                el.classList.remove('hidden');
                btn.className = 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap';
            } else {
                el.classList.add('hidden');
                btn.className = 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap';
            }
        }
    });

    // Perbarui query parameter di address bar agar refresh atau navigasi tetap di tab yang sama
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', currentUrl.toString());

    // Sinkronkan seluruh input redirect_to pada form modal
    document.querySelectorAll('input[name="redirect_to"]').forEach(input => {
        input.value = window.location.href;
    });
}

function editHierarchy(h) {
    document.getElementById('formEditHierarchy').action = '/master-data/hierarchies/' + h.id;
    document.getElementById('edit_h_incumbent').value = h.incumbent || '';
    document.getElementById('edit_h_parent').value = h.parent || '';
    document.getElementById('edit_h_incumbent_title').value = h.incumbent_title || '';
    document.getElementById('edit_h_person_name').value = h.person_name || '';
    const redirectInput = document.getElementById('edit_hierarchy_redirect_to');
    if (redirectInput) redirectInput.value = window.location.href;
    openModal('modalEditHierarchy');
}

function editUser(user) {
    document.getElementById('formEditUser').action = '/master-data/users/' + user.id;
    document.getElementById('edit_name').value = user.name || '';
    document.getElementById('edit_employee_no').value = user.employee_no || '';
    document.getElementById('edit_email').value = user.email || '';
    document.getElementById('edit_position_id').value = user.position_id || '';
    document.getElementById('edit_role_id').value = user.role_id || '';
    document.getElementById('edit_unit_id').value = user.unit_id || '';
    document.getElementById('edit_is_active').value = user.is_active ? '1' : '0';
    const redirectInput = document.getElementById('edit_user_redirect_to');
    if (redirectInput) redirectInput.value = window.location.href;
    openModal('modalEditUser');
}

// Cek apakah ada query param ?tab= di URL atau session aktif
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab') || '{{ $activeTab ?? "hierarchy" }}';
    if (tabParam) {
        switchMasterTab(tabParam);
    }
});
</script>
@endsection
