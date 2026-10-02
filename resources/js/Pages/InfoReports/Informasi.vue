<template>
  <AppLayout title="Pusat Arahan & Informasi Koordinasi">
    <div class="space-y-6">

      <!-- Header Action & Filter Bar -->
      <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-bullhorn text-blue-600"></i> Cascading Information & Arahan Manajemen
          </h2>
          <p class="text-xs text-gray-500 mt-1">Alur distribusi arahan, kebijakan, dan regulasi dari MUP hingga pelaksana teknis (FR-09 & BR-11).</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button 
            v-if="['TL', 'ASMAN', 'MUP', 'ADMIN'].includes(userRole)"
            @click="showCreateModal = true" 
            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg flex items-center gap-2 shadow-xs transition cursor-pointer"
          >
            <i class="fas fa-plus"></i> Terbitkan Arahan / Informasi
          </button>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div>
            <label class="block text-[11px] font-bold text-gray-600 mb-1">Cari Kata Kunci</label>
            <div class="relative">
              <input 
                type="text" 
                v-model="filters.search" 
                @input="onSearchInput"
                placeholder="Cari judul arahan atau isi..." 
                class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"
              />
              <i class="fas fa-search absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-gray-600 mb-1">Kategori</label>
            <select 
              v-model="filters.kategori" 
              @change="applyFilter"
              class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white"
            >
              <option value="Semua">Semua Kategori</option>
              <option value="ARAHAN">Arahan Manajemen</option>
              <option value="KEBIJAKAN">Kebijakan & SOP</option>
              <option value="PENGUMUMAN">Pengumuman Operasional</option>
              <option value="REGULASI">Regulasi K3 / Lingkungan</option>
            </select>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-gray-600 mb-1">Target Sasaran Level</label>
            <select 
              v-model="filters.target_level" 
              @change="applyFilter"
              class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white"
            >
              <option value="Semua">Semua Level Sasaran</option>
              <option value="ALL">Semua Level (Seluruh Unit)</option>
              <option value="MUP">Level MUP</option>
              <option value="ASMAN">Level ASMAN</option>
              <option value="TL">Level Team Leader (TL)</option>
              <option value="STAFF">Level Staf & Pelaksana</option>
            </select>
          </div>

          <div class="flex items-end gap-2">
            <button 
              type="button" 
              @click="applyFilter"
              class="w-full bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold py-2 rounded-lg transition flex items-center justify-center gap-1.5 cursor-pointer"
            >
              <i class="fas fa-filter"></i> Filter
            </button>
            <button 
              type="button" 
              @click="resetFilter"
              class="px-3 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg text-xs font-semibold cursor-pointer"
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Grid Kartu Informasi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <template v-if="informations.data && informations.data.length > 0">
          <div 
            v-for="info in informations.data" 
            :key="info.id" 
            class="bg-white rounded-xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between"
          >
            <div>
              <div class="flex items-center justify-between gap-2 mb-3">
                <span :class="categoryBadgeClass(info.category)" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border">
                  {{ info.category }}
                </span>
                <span class="text-[10px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                  Target: {{ info.target_level }}
                </span>
              </div>

              <h4 class="font-bold text-gray-800 text-sm mb-2 hover:text-blue-600 transition">
                {{ info.title }}
              </h4>

              <p class="text-xs text-gray-600 leading-relaxed mb-4 whitespace-pre-line">
                {{ info.content }}
              </p>
            </div>

            <div class="border-t border-gray-100 pt-3 text-[11px] text-gray-500 flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-700">{{ info.publisher?.name || 'Pimpinan' }}</p>
                <p class="text-[10px] text-gray-400">{{ info.published_at ? formatDateOnly(info.published_at) : '-' }}</p>
              </div>

              <button 
                v-if="info.publisher_id === authUserId || ['MUP', 'ADMIN'].includes(userRole)"
                @click="deleteInfo(info.id)" 
                class="text-rose-500 hover:text-rose-700 p-1 text-xs cursor-pointer" 
                title="Hapus Informasi"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        </template>
        <div v-else class="col-span-3 bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400">
          <i class="fas fa-bullhorn text-4xl mb-3 text-gray-300"></i>
          <p class="text-sm font-semibold text-gray-600">Belum ada arahan atau pengumuman yang sesuai kriteria.</p>
          <p class="text-xs text-gray-400 mt-1">Gunakan tombol 'Terbitkan Arahan / Informasi' untuk menyebarkan instruksi berjenjang.</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="informations.links && informations.links.length > 3" class="mt-4 flex justify-between items-center">
        <span class="text-xs text-gray-500">
          Total {{ informations.total }} informasi
        </span>
        <div class="flex gap-1">
          <Link 
            v-for="(link, i) in informations.links" 
            :key="i" 
            :href="link.url || '#'" 
            v-html="link.label"
            :class="[
              'px-3 py-1 text-xs rounded border transition',
              link.active ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50',
              !link.url ? 'opacity-50 cursor-not-allowed' : ''
            ]"
            :preserve-scroll="true"
          />
        </div>
      </div>

    </div>

    <!-- Modal Terbitkan Informasi Baru -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden">
        <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <i class="fas fa-bullhorn text-blue-400"></i>
            <h3 class="font-bold text-sm">Terbitkan Arahan / Informasi Berjenjang</h3>
          </div>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-white text-lg cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitCreate" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Arahan / Informasi <span class="text-red-500">*</span></label>
            <input 
              type="text" 
              v-model="createForm.title" 
              required 
              placeholder="Contoh: Arahan Siaga Pasokan Energi Primer Minggu Ini" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
              <select 
                v-model="createForm.category" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="ARAHAN">Arahan Manajemen</option>
                <option value="KEBIJAKAN">Kebijakan & SOP</option>
                <option value="PENGUMUMAN">Pengumuman Operasional</option>
                <option value="REGULASI">Regulasi K3 / Lingkungan</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Sasaran Level <span class="text-red-500">*</span></label>
              <select 
                v-model="createForm.target_level" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="ALL">Semua Level (Seluruh Unit)</option>
                <option value="MUP">Level MUP</option>
                <option value="ASMAN">Level ASMAN</option>
                <option value="TL">Level Team Leader (TL)</option>
                <option value="STAFF">Level Staf & Pelaksana</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Isi Lengkap Arahan <span class="text-red-500">*</span></label>
            <textarea 
              v-model="createForm.content" 
              rows="4" 
              required 
              placeholder="Tuliskan butir arahan instruksi atau kebijakan operasional yang wajib dipedomani..." 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button 
              type="button" 
              @click="showCreateModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 font-semibold cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="createForm.processing"
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer"
            >
              <i class="fas fa-paper-plane"></i> 
              {{ createForm.processing ? 'Menerbitkan...' : 'Publikasikan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  informations: Object,
  units: Array,
  userRole: String,
});

const page = usePage();
const authUserId = page.props.auth?.user?.id || 1;

const urlParams = new URLSearchParams(window.location.search);
const filters = ref({
  search: urlParams.get('search') || '',
  kategori: urlParams.get('kategori') || 'Semua',
  target_level: urlParams.get('target_level') || 'Semua',
});

function applyFilter() {
  const query = {};
  if (filters.value.search) query.search = filters.value.search;
  if (filters.value.kategori && filters.value.kategori !== 'Semua') query.kategori = filters.value.kategori;
  if (filters.value.target_level && filters.value.target_level !== 'Semua') query.target_level = filters.value.target_level;

  router.get('/informasi', query, { preserveState: true, preserveScroll: true, replace: true });
}

let searchTimer = null;
function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    applyFilter();
  }, 350);
}

function resetFilter() {
  filters.value = { search: '', kategori: 'Semua', target_level: 'Semua' };
  router.get('/informasi');
}

const showCreateModal = ref(false);
const createForm = useForm({
  title: '',
  content: '',
  category: 'ARAHAN',
  target_level: 'ALL',
});

function submitCreate() {
  createForm.post('/informasi', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

function deleteInfo(id) {
  if (confirm('Hapus informasi ini?')) {
    router.delete(`/informasi/${id}`, { preserveScroll: true });
  }
}

function formatDateOnly(dStr) {
  if (!dStr) return '-';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}

function categoryBadgeClass(cat) {
  const map = {
    ARAHAN: 'bg-blue-50 text-blue-700 border-blue-200',
    KEBIJAKAN: 'bg-amber-50 text-amber-700 border-amber-200',
    PENGUMUMAN: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    REGULASI: 'bg-purple-50 text-purple-700 border-purple-200',
  };
  return map[cat] || 'bg-gray-50 text-gray-700 border-gray-200';
}
</script>
