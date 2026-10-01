@extends('layouts.app')

@section('title', 'Informasi & Arahan Berjenjang')
@section('page_title', 'Pusat Arahan & Informasi Koordinasi')

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

    <!-- Header Action & Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-bullhorn text-blue-600"></i> Cascading Information & Arahan Manajemen
            </h2>
            <p class="text-xs text-gray-500 mt-1">Alur distribusi arahan, kebijakan, dan regulasi dari MUP hingga pelaksana teknis (FR-09 & BR-11).</p>
        </div>
        
        <div class="flex items-center gap-3">
            @if(in_array($userRole, ['TL', 'ASMAN', 'MUP', 'ADMIN']))
                <button onclick="openModal('modalCreateInformasi')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-plus"></i> Terbitkan Arahan / Informasi
                </button>
            @endif
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <form method="GET" action="{{ route('informasi.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1">Cari Kata Kunci</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul arahan atau isi..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1">Kategori</label>
                <select name="kategori" class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="Semua">Semua Kategori</option>
                    <option value="ARAHAN" {{ request('kategori') == 'ARAHAN' ? 'selected' : '' }}>Arahan Manajemen</option>
                    <option value="KEBIJAKAN" {{ request('kategori') == 'KEBIJAKAN' ? 'selected' : '' }}>Kebijakan & SOP</option>
                    <option value="PENGUMUMAN" {{ request('kategori') == 'PENGUMUMAN' ? 'selected' : '' }}>Pengumuman Operasional</option>
                    <option value="REGULASI" {{ request('kategori') == 'REGULASI' ? 'selected' : '' }}>Regulasi K3 / Lingkungan</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1">Target Sasaran Level</label>
                <select name="target_level" class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="Semua">Semua Level Sasaran</option>
                    <option value="ALL" {{ request('target_level') == 'ALL' ? 'selected' : '' }}>Semua Level (Seluruh Unit)</option>
                    <option value="MUP" {{ request('target_level') == 'MUP' ? 'selected' : '' }}>Level MUP</option>
                    <option value="ASMAN" {{ request('target_level') == 'ASMAN' ? 'selected' : '' }}>Level ASMAN</option>
                    <option value="TL" {{ request('target_level') == 'TL' ? 'selected' : '' }}>Level Team Leader (TL)</option>
                    <option value="STAFF" {{ request('target_level') == 'STAFF' ? 'selected' : '' }}>Level Staf & Pelaksana</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold py-2 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('informasi.index') }}" class="px-3 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg text-xs font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Pinned / Highlight Banner Arahan MUP Terkini -->
    @php
        $latestHighlight = $informations->firstWhere('category', 'ARAHAN') ?? $informations->first();
    @endphp
    @if($latestHighlight)
        <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-2 max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-500/40 text-blue-200 border border-blue-400/30 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                            {{ $latestHighlight->category }}
                        </span>
                        <span class="text-xs text-blue-200">
                            Target: <strong>{{ $latestHighlight->target_level === 'ALL' ? 'SELURUH PEGAWAI' : 'LEVEL ' . $latestHighlight->target_level }}</strong>
                        </span>
                    </div>
                    <h3 class="text-xl font-bold">{{ $latestHighlight->title }}</h3>
                    <p class="text-xs text-slate-200 leading-relaxed">{{ $latestHighlight->content }}</p>
                    <div class="pt-2 flex items-center gap-3 text-[11px] text-blue-200">
                        <span><i class="fas fa-user-circle"></i> {{ $latestHighlight->publisher->name ?? 'Pimpinan Unit' }}</span>
                        <span>&bull;</span>
                        <span><i class="far fa-clock"></i> {{ $latestHighlight->published_at ? $latestHighlight->published_at->format('d M Y, H:i') : '-' }} WITA</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Grid Kartu Informasi -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @forelse($informations as $info)
            @php
                $catColors = [
                    'ARAHAN' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'KEBIJAKAN' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'PENGUMUMAN' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'REGULASI' => 'bg-purple-50 text-purple-700 border-purple-200',
                ];
                $badgeClass = $catColors[$info->category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border {{ $badgeClass }}">
                            {{ $info->category }}
                        </span>
                        <span class="text-[10px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                            Target: {{ $info->target_level }}
                        </span>
                    </div>

                    <h4 class="font-bold text-gray-800 text-sm mb-2 hover:text-blue-600 transition">
                        {{ $info->title }}
                    </h4>

                    <p class="text-xs text-gray-600 leading-relaxed mb-4 whitespace-pre-line">
                        {{ $info->content }}
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-3 text-[11px] text-gray-500 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-700">{{ $info->publisher->name ?? 'Pimpinan' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $info->published_at ? $info->published_at->format('d M Y') : '-' }}</p>
                    </div>

                    @if($info->publisher_id === auth()->id() || in_array($userRole, ['MUP', 'ADMIN']))
                        <form action="{{ route('informasi.destroy', $info->id) }}" method="POST" onsubmit="return confirm('Hapus informasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 p-1 text-xs" title="Hapus Informasi">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400">
                <i class="fas fa-bullhorn text-4xl mb-3 text-gray-300"></i>
                <p class="text-sm font-semibold text-gray-600">Belum ada arahan atau pengumuman yang sesuai kriteria.</p>
                <p class="text-xs text-gray-400 mt-1">Gunakan tombol 'Terbitkan Arahan / Informasi' untuk menyebarkan instruksi berjenjang.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $informations->links() }}
    </div>

</div>

<!-- Modal Terbitkan Arahan / Informasi Baru -->
<div id="modalCreateInformasi" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden animate-fade-in">
        <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-bullhorn text-blue-400"></i>
                <h3 class="font-bold text-sm">Terbitkan Arahan / Informasi Berjenjang</h3>
            </div>
            <button onclick="closeModal('modalCreateInformasi')" class="text-gray-400 hover:text-white text-lg">&times;</button>
        </div>

        <form action="{{ route('informasi.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Judul Arahan / Informasi <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Kesiapan Siaga Beban Puncak Sulselrabar" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="ARAHAN">Arahan Manajemen</option>
                        <option value="KEBIJAKAN">Kebijakan Operasional</option>
                        <option value="PENGUMUMAN">Pengumuman Umum</option>
                        <option value="REGULASI">Regulasi K3 / Lingkungan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target Sasaran Level <span class="text-red-500">*</span></label>
                    <select name="target_level" required class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @if(in_array($userRole, ['MUP', 'ADMIN']))
                            <option value="ALL">Semua Level (Seluruh UP Sengkang)</option>
                            <option value="ASMAN">Asisten Manajer (ASMAN)</option>
                            <option value="TL">Team Leader (TL)</option>
                            <option value="STAFF">Staf / Pelaksana Teknis</option>
                        @elseif($userRole === 'ASMAN')
                            <option value="ALL">Seluruh Seksi di Bawah Asman</option>
                            <option value="TL">Team Leader (TL)</option>
                            <option value="STAFF">Staf / Pelaksana Teknis</option>
                        @elseif($userRole === 'TL')
                            <option value="STAFF">Staf Seksi Saya</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Target Unit Kerja Spesifik (Opsional)</label>
                    <select name="target_unit_id" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Seluruh Unit Kerja --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Berlaku Hingga (Opsional)</label>
                    <input type="date" name="expires_at" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Isi Arahan / Instruksi Detail <span class="text-red-500">*</span></label>
                <textarea name="content" rows="4" required placeholder="Tuliskan butir arahan, instruksi tindakan, atau pengumuman secara jelas dan terukur..." class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalCreateInformasi')" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                    <i class="fas fa-paper-plane"></i> Publikasikan Arahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
