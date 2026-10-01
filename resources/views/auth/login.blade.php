<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Koordinasi UP Sengkang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Inter', 'sans-serif'] }, 
                    colors: { 
                        pln: { dark: '#1e293b', blue: '#0284c7', lightblue: '#eff6ff', yellow: '#fcd34d' }
                    } 
                } 
            } 
        }
    </script>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden font-sans antialiased">

    <!-- Container Utama -->
    <div class="flex w-full h-full relative bg-slate-900">
        
        <!-- BAGIAN KANAN (Background Gambar) -->
        <div class="absolute inset-0 w-full h-full z-0">
            <img src="{{ file_exists(public_path('assets/bumn/bg-sengkang.png')) ? asset('assets/bumn/bg-sengkang.png') : 'https://images.unsplash.com/photo-1513828583688-c52646db42da?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}" alt="Power Plant Background" class="w-full h-full object-cover opacity-80">
            <div class="absolute inset-0 bg-gradient-to-l from-slate-950/70 via-slate-900/40 to-transparent"></div>
            
            <div class="absolute bottom-16 right-16 text-right hidden md:block">
                <h2 class="text-white text-3xl lg:text-4xl font-bold leading-tight drop-shadow-xl">
                    Kolaborasi<br>
                    Hari Ini<br>
                    untuk Energi<br>
                    Masa Depan
                </h2>
                <p class="text-sky-300 text-sm mt-3 font-semibold tracking-wide">PLN Nusantara Power - Unit Pembangkitan Sengkang</p>
            </div>
        </div>

        <!-- BAGIAN KIRI (Form Login) -->
        <div class="w-full md:w-[45%] lg:w-[38%] h-full bg-white relative z-10 flex flex-col justify-between shadow-[20px_0_30px_rgba(0,0,0,0.15)] md:rounded-r-[40px] overflow-y-auto">
            
            <div class="w-full max-w-sm m-auto px-8 py-10 relative z-20">
                
                <!-- Logo PLN -->
                <div class="flex items-center gap-3 mb-8">
                    <img src="{{ asset('assets/bumn/logo-pln.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain drop-shadow-sm">
                    <div class="leading-tight">
                        <span class="text-sky-600 font-bold text-lg">PLN</span><br>
                        <span class="text-gray-500 text-xs font-semibold">Nusantara Power UP Sengkang</span>
                    </div>
                </div>

                <!-- Judul -->
                <h1 class="text-2xl font-bold text-gray-800 leading-snug mb-1">Sistem Koordinasi & Eskalasi</h1>
                <p class="text-gray-500 text-xs mb-6 font-medium">Masuk menggunakan kredensial akun terdaftar</p>

                @if ($errors->any())
                    <div class="mb-5 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Email / NID Pegawai</label>
                        <input type="text" name="email" value="{{ old('email') }}" placeholder="Email atau NID (contoh: 8108050JA)" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-xs text-gray-700 outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition" required autofocus>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Kata Sandi</label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-xs text-gray-700 outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition" required>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <label class="flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white rounded-lg py-3 text-xs font-bold shadow-md hover:shadow-lg transition-all mt-4 flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
                    </button>
                </form>

            </div>

            <!-- 5 Core Values BUMN Sesuai Mockup Panel 1 -->
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50/50 flex justify-between items-center text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-check-shield text-sky-600 text-xs mb-1"></i>
                    <span class="text-[9px] font-semibold text-gray-500">Integritas</span>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-user-tie text-sky-600 text-xs mb-1"></i>
                    <span class="text-[9px] font-semibold text-gray-500">Profesional</span>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-hands-helping text-sky-600 text-xs mb-1"></i>
                    <span class="text-[9px] font-semibold text-gray-500">Sinergi</span>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-heart text-sky-600 text-xs mb-1"></i>
                    <span class="text-[9px] font-semibold text-gray-500">Pelayanan</span>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-award text-sky-600 text-xs mb-1"></i>
                    <span class="text-[9px] font-semibold text-gray-500">Kesempurnaan</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
