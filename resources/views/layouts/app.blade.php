<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Koordinasi UP Sengkang') - PLN Nusantara Power</title>

    <!-- 1. Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- 2. FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- 3. Tailwind CSS CDN (Palette Resmi BUMN PLN NP) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        pln: { 
                            dark: '#1e293b', 
                            blue: '#1d4ed8', 
                            lightblue: '#eff6ff', 
                            yellow: '#fcd34d' 
                        },
                        chart: { 
                            green: '#10b981', 
                            blue: '#3b82f6', 
                            red: '#ef4444', 
                            orange: '#f59e0b' 
                        }
                    }
                }
            }
        }
    </script>

    <!-- 4. Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Custom scrollbar untuk tampilan presisi */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .modal.active { display: flex !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-[#f8fafc] flex flex-col md:flex-row h-screen overflow-hidden text-sm font-sans antialiased text-gray-800">

    <!-- ==================== MOBILE TOPBAR HEADER (< md) ==================== -->
    <header class="md:hidden bg-pln-dark text-white px-4 py-3 flex items-center justify-between shadow-md shrink-0 z-30">
        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleMobileSidebar()" class="text-gray-300 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition focus:outline-none">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <div class="flex items-center gap-2">
                <img src="{{ asset('assets/bumn/logo-pln.png') }}" alt="Logo PLN" class="w-7 h-7 rounded object-contain">
                <span class="text-xs font-bold tracking-wide">PLN NP UP Sengkang</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <!-- Icon Notifikasi Mobile -->
            <a href="{{ route('issues.index') }}" class="relative p-1.5 text-gray-300 hover:text-white">
                <i class="far fa-bell text-base"></i>
                @if(count($systemNotifications ?? []) > 0)
                    <span class="absolute top-0 right-0 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-red-500 text-[8px] font-bold text-white">
                        {{ count($systemNotifications) }}
                    </span>
                @endif
            </a>
            <a href="{{ route('profile.edit') }}" class="w-8 h-8 rounded-full bg-slate-600 text-white font-bold flex items-center justify-center text-xs shadow overflow-hidden">
                @if(auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=64748b&color=fff&size=80" alt="Avatar" class="w-full h-full object-cover">
                @endif
            </a>
        </div>
    </header>

    <!-- Mobile Backdrop Overlay -->
    <div id="sidebarBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-900/60 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <!-- ==================== SIDEBAR PERSIS MOCKUP (RESPONSIVE DRAWER) ==================== -->
    <aside id="sidebarNav" class="fixed md:static inset-y-0 left-0 w-64 bg-pln-dark text-gray-300 flex flex-col h-full shrink-0 z-50 shadow-2xl md:shadow-xl select-none -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        
        <!-- Logo Area -->
        <div class="h-20 flex items-center justify-between px-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/bumn/logo-pln.png') }}" alt="Logo PLN" class="w-10 h-10 rounded-md object-contain shadow-sm">
                <div class="leading-tight">
                    <span class="text-white text-xs font-bold tracking-wide">PLN NP</span><br>
                    <span class="text-gray-300 text-xs font-bold">UP Sengkang</span>
                </div>
            </div>
            <button type="button" onclick="toggleMobileSidebar()" class="md:hidden text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-white/10">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Menu Navigasi Sesuai Mockup -->
        <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto text-xs font-medium">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }}">
                <i class="fas fa-home w-5 text-center text-base"></i> Dashboard
            </a>
            
            <a href="{{ route('pekerjaan') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pekerjaan*') || request()->routeIs('tasks*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }}">
                <i class="fas fa-tasks w-5 text-center text-base"></i> Task
            </a>

            <a href="{{ route('issues.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('issues*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }}">
                <i class="fas fa-level-up-alt w-5 text-center text-base"></i> Issue & Eskalasi
            </a>

            <a href="{{ route('rapat.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('rapat*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }}">
                <i class="far fa-calendar-alt w-5 text-center text-base"></i> Rapat Mingguan
            </a>

            <a href="{{ route('informasi.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('informasi*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }}">
                <i class="fas fa-info-circle w-5 text-center text-base"></i> Informasi
            </a>

            <a href="{{ route('keputusan.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('keputusan*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }}">
                <i class="fas fa-file-signature w-5 text-center text-base"></i> Keputusan
            </a>

            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('laporan*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }}">
                <i class="far fa-file-alt w-5 text-center text-base"></i> Laporan
            </a>

            @if(in_array(strtoupper(auth()->user()->role->name ?? ''), ['MUP', 'ADMIN']))
            <a href="{{ route('master.index') }}" class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('master*') ? 'bg-pln-blue text-white rounded-lg shadow-md font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors' }} mt-3">
                <div class="flex items-center gap-3">
                    <i class="fas fa-cog w-5 text-center text-base"></i> Master Data
                </div>
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
            @endif
        </nav>

        <!-- Profile Bottom Bar -->
        <div class="p-4 border-t border-slate-700/50 bg-slate-900/40">
            <div class="flex items-center justify-between">
                <a href="{{ route('profile.edit') }}" title="Pengaturan Profil & Password" class="flex items-center gap-3 overflow-hidden group hover:opacity-90 transition">
                    <div class="w-9 h-9 rounded-full bg-slate-700 group-hover:bg-slate-600 text-white font-bold flex items-center justify-center text-xs shadow-md overflow-hidden shrink-0">
                        @if(auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=64748b&color=fff&size=100" alt="Avatar" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-white group-hover:text-blue-300 truncate">{{ auth()->user()->name ?? 'Andi Pratama' }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">{{ auth()->user()->position->name ?? (auth()->user()->role->name ?? 'Team Leader Operasi 1A') }}</p>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar / Logout" class="text-gray-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-white/10 transition">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-[1200px] mx-auto space-y-6">

                <!-- TOPBAR SECTION PERSIS PANEL 2 MOCKUP (RESPONSIVE) -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4 pb-2 border-b border-gray-100 sm:border-0 sm:pb-0">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">@yield('page_title', 'Dashboard')</h1>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('profile.edit') }}" title="Pengaturan Profil & Foto" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-600 overflow-hidden shadow-sm flex items-center justify-center font-bold text-white hover:ring-2 hover:ring-blue-400 transition shrink-0 group">
                                @if(auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=64748b&color=fff&size=120" alt="Avatar" class="w-full h-full object-cover">
                                @endif
                            </a>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm text-gray-500 truncate">Selamat datang, <span class="font-bold text-gray-800">{{ auth()->user()->name ?? 'Andi Pratama' }}</span></p>
                                <p class="text-[11px] sm:text-xs text-gray-500 truncate">{{ auth()->user()->position->name ?? (auth()->user()->role->name ?? 'Team Leader Operasi 1A') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between md:justify-end w-full md:w-auto gap-4 flex-wrap sm:flex-nowrap">
                        <div class="flex items-center gap-3">
                            <!-- Icon Notifikasi Interaktif Dinamis -->
                            <div class="relative" id="notifDropdownContainer">
                                <button type="button" onclick="document.getElementById('notifMenu').classList.toggle('hidden')" class="relative p-1.5 text-gray-500 hover:text-gray-800 focus:outline-none rounded-lg hover:bg-gray-100 transition">
                                    <i class="far fa-bell text-xl"></i>
                                    @if(count($systemNotifications ?? []) > 0)
                                        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white border-2 border-[#f8fafc]">
                                            {{ count($systemNotifications) }}
                                        </span>
                                    @endif
                                </button>
                                <div id="notifMenu" class="hidden absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 py-2">
                                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                        <span class="font-bold text-xs text-gray-800">Pemberitahuan Sistem</span>
                                        @if(count($systemNotifications ?? []) > 0)
                                            <span class="text-[10px] bg-red-50 text-red-600 font-semibold px-2 py-0.5 rounded">{{ count($systemNotifications) }} Baru</span>
                                        @else
                                            <span class="text-[10px] bg-emerald-50 text-emerald-600 font-semibold px-2 py-0.5 rounded">Semua Teratasi</span>
                                        @endif
                                    </div>
                                    <div class="divide-y divide-gray-50 text-xs max-h-72 overflow-y-auto">
                                        @forelse($systemNotifications ?? [] as $notif)
                                            <a href="{{ $notif['link'] }}" class="block px-4 py-3 hover:bg-slate-50 transition">
                                                <p class="font-bold text-gray-800 text-[11px] mb-0.5"><i class="{{ $notif['icon'] }} mr-1"></i> {{ $notif['title'] }}</p>
                                                <p class="text-gray-500 text-[11px] leading-tight">{{ $notif['text'] }}</p>
                                                <p class="text-[10px] text-gray-400 mt-1">{{ $notif['time'] }}</p>
                                            </a>
                                        @empty
                                            <div class="px-4 py-6 text-center text-gray-400 text-xs">
                                                <i class="far fa-bell-slash text-2xl mb-1 text-gray-300"></i>
                                                <p>Tidak ada pemberitahuan baru saat ini.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Date & Week Selector Sesuai Carbon Dinamis -->
                        <div class="text-right">
                            <p class="text-[11px] sm:text-xs text-gray-500 mb-0.5">{{ $topbarDate ?? now()->isoFormat('dddd, D MMMM Y') }}</p>
                            <div class="bg-white border border-gray-200 rounded-md px-2.5 py-1 sm:px-3 sm:py-1.5 flex items-center gap-2 shadow-sm text-right">
                                <span class="text-[10px] sm:text-xs text-gray-700 font-medium">Minggu ke-{{ $topbarWeek ?? now()->isoWeek() }} ({{ $topbarWeekRange ?? '' }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FLASH NOTIFIKASI -->
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center justify-between text-xs shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg flex items-center justify-between text-xs shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
                    </div>
                @endif

                <!-- YIELD KONTEN UTAMA -->
                @yield('content')

            </div>
        </div>
    </main>

    <!-- ==================== URUTAN SCRIPT RESMI & ANTI-GAGAL ==================== -->
    <!-- 1. jQuery (Wajib Paling Atas) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 2. Popper.js (Untuk Dropdown & Tooltip) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- 3. Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 4. Universal Modal & Dropdown Handler (Fallback Anti-Gagal) -->
    <script>
        // Global Modal Functions
        window.openModal = function(id) {
            const cleanId = id.replace('#', '');
            const el = document.getElementById(cleanId);
            if (el) {
                // Tailwind modal
                el.classList.remove('hidden');
                el.classList.add('flex');
                el.style.display = 'flex';
                // Jika elemen adalah Bootstrap modal
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try {
                        const bsModal = bootstrap.Modal.getOrCreateInstance(el);
                        if (bsModal) bsModal.show();
                    } catch (err) {}
                }
            }
        };

        window.closeModal = function(id) {
            const cleanId = id.replace('#', '');
            const el = document.getElementById(cleanId);
            if (el) {
                el.classList.add('hidden');
                el.classList.remove('flex');
                el.style.display = 'none';
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try {
                        const bsModal = bootstrap.Modal.getInstance(el);
                        if (bsModal) bsModal.hide();
                    } catch (err) {}
                }
            }
        };

        // Otomatis bind event listener saat dokumen siap
        $(document).ready(function () {
            // Handler untuk semua pemicu modal (data-bs-toggle="modal", data-toggle="modal", data-modal-target)
            $(document).on('click', '[data-bs-toggle="modal"], [data-toggle="modal"], [data-modal-target]', function (e) {
                e.preventDefault();
                const target = $(this).attr('data-bs-target') || $(this).attr('data-target') || $(this).attr('data-modal-target');
                if (target) {
                    window.openModal(target);
                }
            });

            // Handler tombol penutup (data-bs-dismiss="modal", data-dismiss="modal", data-modal-close)
            $(document).on('click', '[data-bs-dismiss="modal"], [data-dismiss="modal"], [data-modal-close], .close-modal', function (e) {
                e.preventDefault();
                const modal = $(this).closest('.modal-container, [id^="modal"], .modal');
                if (modal.length) {
                    window.closeModal(modal.attr('id'));
                }
            });

            // Tutup modal jika area luar (backdrop) diklik
            $('.modal-container, [id^="modal"]').on('click', function (e) {
                if (e.target === this) {
                    window.closeModal($(this).attr('id'));
                }
            });
        });

        // Mobile Sidebar Drawer Toggle
        window.toggleMobileSidebar = function() {
            const sidebar = document.getElementById('sidebarNav');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
            if (backdrop) {
                backdrop.classList.toggle('hidden');
            }
        };
    </script>

    <!-- 5. Script Khusus per Halaman -->
    @stack('scripts')
</body>
</html>