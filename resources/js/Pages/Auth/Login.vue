<template>
  <Head title="Login - Sistem Koordinasi UP Sengkang" />

  <div class="flex w-full h-screen overflow-hidden relative bg-slate-900 font-sans antialiased">
    <!-- BAGIAN KANAN (Background Gambar Power Plant) -->
    <div class="absolute inset-0 w-full h-full z-0">
      <img 
        src="/assets/bumn/bg-sengkang.png" 
        alt="Power Plant Background" 
        class="w-full h-full object-cover opacity-80"
        @error="$event.target.src = 'https://images.unsplash.com/photo-1513828583688-c52646db42da?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'"
      />
      <div class="absolute inset-0 bg-gradient-to-l from-slate-950/70 via-slate-900/40 to-transparent"></div>
      
      <div class="absolute bottom-16 right-16 text-right hidden md:block">
        <h2 class="text-white text-3xl lg:text-4xl font-extrabold leading-tight drop-shadow-xl">
          Kolaborasi<br />
          Hari Ini<br />
          untuk Energi<br />
          Masa Depan
        </h2>
        <p class="text-sky-300 text-sm mt-3 font-semibold tracking-wide">
          PLN Nusantara Power - Unit Pembangkitan Sengkang
        </p>
      </div>
    </div>

    <!-- BAGIAN KIRI (Form Login) -->
    <div class="w-full md:w-[45%] lg:w-[38%] h-full bg-white relative z-10 flex flex-col justify-between shadow-[20px_0_30px_rgba(0,0,0,0.15)] md:rounded-r-[40px] overflow-y-auto">
      
      <div class="w-full max-w-sm m-auto px-8 py-10 relative z-20">
        
        <!-- Logo PLN -->
        <div class="flex items-center gap-3 mb-8">
          <img src="/assets/bumn/logo-pln.png" alt="Logo PLN" class="w-12 h-12 object-contain drop-shadow-xs" />
          <div class="leading-tight">
            <span class="text-sky-600 font-bold text-lg">PLN</span><br />
            <span class="text-gray-500 text-xs font-semibold">Nusantara Power UP Sengkang</span>
          </div>
        </div>

        <!-- Judul -->
        <h1 class="text-2xl font-bold text-gray-800 leading-snug mb-1">Sistem Koordinasi & Eskalasi</h1>
        <p class="text-gray-500 text-xs mb-6 font-medium">Masuk menggunakan kredensial akun terdaftar</p>

        <!-- Error Banner -->
        <div v-if="form.errors.email || form.errors.password" class="mb-5 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2">
          <i class="fas fa-exclamation-circle mt-0.5"></i>
          <span>{{ form.errors.email || form.errors.password }}</span>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Email / NID Pegawai</label>
            <input 
              type="text" 
              v-model="form.email" 
              placeholder="Email atau NID (contoh: 8108050JA)" 
              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-xs text-gray-700 outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition" 
              required 
              autofocus 
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Kata Sandi</label>
            <input 
              type="password" 
              v-model="form.password" 
              placeholder="••••••••" 
              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-xs text-gray-700 outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition" 
              required 
            />
          </div>

          <div class="flex items-center justify-between mt-2">
            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
              <input 
                type="checkbox" 
                v-model="form.remember" 
                class="rounded border-gray-300 text-sky-600 focus:ring-sky-500 cursor-pointer" 
              />
              <span>Ingat saya</span>
            </label>
          </div>

          <button 
            type="submit" 
            :disabled="form.processing" 
            class="w-full bg-sky-600 hover:bg-sky-700 disabled:opacity-70 text-white rounded-lg py-3 text-xs font-bold shadow-md hover:shadow-lg transition-all mt-4 flex items-center justify-center gap-2 cursor-pointer"
          >
            <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-sign-in-alt"></i>
            <span>{{ form.processing ? 'Memproses Masuk...' : 'Masuk ke Sistem' }}</span>
          </button>
        </form>

      </div>

      <!-- 5 Core Values BUMN -->
      <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/60 text-center">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Nilai Utama BUMN</p>
        <div class="flex justify-center items-center gap-2 text-[11px] font-bold text-sky-800 flex-wrap">
          <span class="bg-sky-100/70 px-2 py-0.5 rounded">Amanah</span>
          <span class="bg-sky-100/70 px-2 py-0.5 rounded">Kompeten</span>
          <span class="bg-sky-100/70 px-2 py-0.5 rounded">Harmonis</span>
          <span class="bg-sky-100/70 px-2 py-0.5 rounded">Loyal</span>
          <span class="bg-sky-100/70 px-2 py-0.5 rounded">Adaptif</span>
          <span class="bg-sky-100/70 px-2 py-0.5 rounded">Kolaboratif</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
}
</script>
