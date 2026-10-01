<aside class="w-64 bg-pln-dark text-gray-300 flex flex-col h-full shrink-0 z-10 shadow-xl">
    <div class="h-20 flex items-center px-6">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-pln.png') }}" alt="PLN" class="w-10 h-10 rounded-md object-cover shadow-sm">
            <div class="leading-tight">
                <span class="text-white text-xs font-bold">PLN NP</span><br>
                <span class="text-white text-xs font-bold">UP Sengkang</span>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto text-xs font-medium">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-pln-blue text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
            <i class="fas fa-home w-5 text-center text-lg"></i> Dashboard
        </a>
        <a href="{{ route('issues.index') }}" class="{{ request()->routeIs('issues.*') ? 'bg-pln-blue text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
            <i class="fas fa-exclamation-circle w-5 text-center text-lg"></i> Issue & Task
        </a>
        <!-- Tambahkan menu lainnya sesuai index.html -->
    </nav>
</aside>