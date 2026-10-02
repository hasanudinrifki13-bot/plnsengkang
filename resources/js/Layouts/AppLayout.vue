<template>
  <div class="flex h-screen w-full bg-[#f8fafc] text-slate-800 font-sans overflow-hidden">
    
    <!-- Mobile Backdrop -->
    <div 
      v-if="mobileSidebarOpen" 
      @click="mobileSidebarOpen = false" 
      class="fixed inset-0 bg-slate-900/60 z-40 md:hidden backdrop-blur-xs transition-opacity"
    ></div>

    <!-- ==================== SIDEBAR ==================== -->
    <aside 
      :class="[
        'fixed md:static inset-y-0 left-0 w-64 bg-pln-dark text-gray-300 flex flex-col h-full shrink-0 z-50 shadow-2xl md:shadow-xl select-none transition-transform duration-300 ease-in-out',
        mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
      ]"
    >
      <!-- Logo Area -->
      <div class="h-20 flex items-center justify-between px-6 border-b border-slate-700/30">
        <div class="flex items-center gap-3">
          <img src="/assets/bumn/logo-pln.png" alt="Logo PLN" class="w-10 h-10 rounded-md object-contain shadow-sm" />
          <div class="leading-tight">
            <span class="text-white text-xs font-bold tracking-wide">PLN NP</span><br />
            <span class="text-gray-300 text-xs font-bold">UP Sengkang</span>
          </div>
        </div>
        <button 
          type="button" 
          @click="mobileSidebarOpen = false" 
          class="md:hidden text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-white/10"
        >
          <i class="fas fa-times text-lg"></i>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto text-xs font-medium">
        <Link 
          href="/dashboard" 
          :class="navClass('/dashboard')"
        >
          <i class="fas fa-home w-5 text-center text-base"></i>
          <span>Dashboard</span>
        </Link>

        <Link 
          href="/pekerjaan" 
          :class="navClass(['/pekerjaan', '/tasks'])"
        >
          <i class="fas fa-tasks w-5 text-center text-base"></i>
          <span>Task Management</span>
        </Link>

        <Link 
          href="/issues" 
          :class="navClass('/issues')"
        >
          <i class="fas fa-level-up-alt w-5 text-center text-base"></i>
          <span>Issue & Eskalasi</span>
        </Link>

        <Link 
          href="/rapat" 
          :class="navClass('/rapat')"
        >
          <i class="far fa-calendar-alt w-5 text-center text-base"></i>
          <span>Rapat Mingguan</span>
        </Link>

        <Link 
          href="/informasi" 
          :class="navClass('/informasi')"
        >
          <i class="fas fa-info-circle w-5 text-center text-base"></i>
          <span>Informasi</span>
        </Link>

        <Link 
          href="/keputusan" 
          :class="navClass('/keputusan')"
        >
          <i class="fas fa-file-signature w-5 text-center text-base"></i>
          <span>Keputusan</span>
        </Link>

        <Link 
          href="/laporan" 
          :class="navClass('/laporan')"
        >
          <i class="far fa-file-alt w-5 text-center text-base"></i>
          <span>Laporan</span>
        </Link>

        <Link 
          v-if="isMupOrAdmin" 
          href="/master-data" 
          :class="navClass('/master-data')"
        >
          <div class="flex items-center gap-3">
            <i class="fas fa-cog w-5 text-center text-base"></i>
            <span>Master Data</span>
          </div>
          <i class="fas fa-chevron-right text-[10px]"></i>
        </Link>
      </nav>

      <!-- Bottom Profile Bar -->
      <div class="p-4 border-t border-slate-700/50 bg-slate-900/40">
        <div class="flex items-center justify-between">
          <Link href="/profil" class="flex items-center gap-3 overflow-hidden group hover:opacity-90 transition">
            <div class="w-9 h-9 rounded-full bg-slate-700 group-hover:bg-slate-600 text-white font-bold flex items-center justify-center text-xs shadow-md overflow-hidden shrink-0">
              <img :src="user?.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
            </div>
            <div class="truncate">
              <p class="text-xs font-bold text-white group-hover:text-blue-300 truncate">{{ user?.name }}</p>
              <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold truncate">
                {{ user?.position?.name || user?.role?.name || 'Karyawan' }}
              </p>
            </div>
          </Link>
          <button 
            type="button" 
            @click="logout" 
            title="Keluar / Logout" 
            class="text-gray-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-white/10 transition"
          >
            <i class="fas fa-sign-out-alt"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">

      <!-- Mobile Top Bar -->
      <header class="md:hidden bg-pln-dark text-white px-4 py-3 flex items-center justify-between shadow-sm z-30 shrink-0">
        <button 
          type="button" 
          @click="mobileSidebarOpen = true" 
          class="p-2 -ml-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/10"
        >
          <i class="fas fa-bars text-lg"></i>
        </button>
        <div class="flex items-center gap-2">
          <img src="/assets/bumn/logo-pln.png" alt="PLN" class="w-6 h-6 object-contain" />
          <span class="font-bold text-xs">UP Sengkang</span>
        </div>
        <div class="flex items-center gap-2">
          <Link href="/profil" class="w-8 h-8 rounded-full bg-slate-600 text-white font-bold flex items-center justify-center text-xs shadow overflow-hidden">
            <img :src="user?.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
          </Link>
        </div>
      </header>

      <!-- Flash Message Toast Banner -->
      <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div 
          v-if="flashMessage" 
          class="absolute top-4 right-4 z-50 max-w-md w-full shadow-lg rounded-xl overflow-hidden border p-4 flex items-start gap-3 backdrop-blur-md"
          :class="flashClasses"
        >
          <i :class="flashIconClass" class="text-base mt-0.5"></i>
          <div class="flex-1 text-xs">
            <p class="font-bold mb-0.5">{{ flashTitle }}</p>
            <p class="text-slate-700 leading-relaxed">{{ flashMessage }}</p>
          </div>
          <button @click="dismissFlash" class="text-slate-400 hover:text-slate-600 p-1">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
      </transition>

      <!-- Scrollable Main View -->
      <main class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-[1240px] mx-auto space-y-6">

          <!-- Header Section (Title & User Summary) -->
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4 pb-2 border-b border-gray-100 sm:border-0 sm:pb-0">
            <div>
              <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 tracking-tight">{{ title }}</h1>
              <div class="flex items-center gap-3 mt-1.5">
                <Link href="/profil" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-600 overflow-hidden shadow-sm flex items-center justify-center font-bold text-white hover:ring-2 hover:ring-blue-400 transition shrink-0">
                  <img :src="user?.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
                </Link>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-500 truncate">
                    Selamat datang, <span class="font-bold text-gray-800">{{ user?.name }}</span>
                  </p>
                  <p class="text-[11px] sm:text-xs text-gray-500 truncate flex flex-wrap items-center gap-1.5 mt-0.5">
                    <span>{{ user?.position?.name || user?.role?.name }} &bull; {{ user?.organization_unit?.name || 'UP Sengkang' }}</span>
                    <template v-if="user?.hierarchy_info?.tl && !user.hierarchy_info?.tl?.is_self">
                      <span class="text-gray-300">&bull;</span>
                      <span class="text-blue-600 font-semibold">TL: {{ user.hierarchy_info.tl.name }}</span>
                    </template>
                    <template v-if="user?.hierarchy_info?.asman && !user.hierarchy_info?.asman?.is_self">
                      <span class="text-gray-300">&bull;</span>
                      <span class="text-indigo-600 font-semibold">Asman: {{ user.hierarchy_info.asman.name }}</span>
                    </template>
                  </p>
                </div>
              </div>
            </div>

            <!-- Right Controls (e.g. Current Date / Quick Action Slot) -->
            <div class="flex items-center justify-between md:justify-end w-full md:w-auto gap-3">
              <slot name="actions" />
              <div class="hidden sm:flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 shadow-2xs">
                <i class="far fa-calendar-check text-blue-600"></i>
                <span>{{ currentDateFormatted }}</span>
              </div>
            </div>
          </div>

          <!-- Page Content Slot -->
          <slot />

        </div>
      </main>

    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
});

const page = usePage();
const mobileSidebarOpen = ref(false);

const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash || {});

const flashDismissed = ref(false);

const flashMessage = computed(() => {
  if (flashDismissed.value) return null;
  return flash.value.success || flash.value.error || flash.value.info || null;
});

const isMupOrAdmin = computed(() => {
  const role = (user.value?.role?.name || '').toUpperCase();
  return ['MUP', 'ADMIN'].includes(role);
});

const flashTitle = computed(() => {
  if (flash.value.error) return 'Terjadi Kesalahan';
  if (flash.value.info) return 'Informasi';
  return 'Berhasil';
});

const flashClasses = computed(() => {
  if (flash.value.error) return 'bg-rose-50/95 border-rose-200 text-rose-800';
  if (flash.value.info) return 'bg-sky-50/95 border-sky-200 text-sky-800';
  return 'bg-emerald-50/95 border-emerald-200 text-emerald-800';
});

const flashIconClass = computed(() => {
  if (flash.value.error) return 'fas fa-exclamation-triangle text-rose-600';
  if (flash.value.info) return 'fas fa-info-circle text-sky-600';
  return 'fas fa-check-circle text-emerald-600';
});

watch(() => [flash.value.success, flash.value.error, flash.value.info], () => {
  flashDismissed.value = false;
  if (flashMessage.value) {
    setTimeout(() => {
      flashDismissed.value = true;
    }, 5000);
  }
});

function dismissFlash() {
  flashDismissed.value = true;
}

function navClass(pathOrPaths) {
  const currentUrl = page.url;
  const paths = Array.isArray(pathOrPaths) ? pathOrPaths : [pathOrPaths];
  const isActive = paths.some(p => currentUrl === p || (p !== '/' && currentUrl.startsWith(p)));

  if (isActive) {
    return 'flex items-center gap-3 px-4 py-2.5 bg-pln-blue text-white rounded-lg shadow-md font-bold transition';
  }
  return 'flex items-center gap-3 px-4 py-2.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors';
}

function logout() {
  if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
    router.post('/logout');
  }
}

const currentDateFormatted = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
});
</script>
