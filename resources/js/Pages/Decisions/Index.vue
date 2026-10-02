<template>
  <AppLayout title="Daftar Keputusan Atasan">
    <!-- Subtitle Navigasi -->
    <p class="text-xs text-gray-500 -mt-3 mb-4">
      Rekap seluruh keputusan resmi berjenjang dari TL, Asman, dan Manajer Unit Pelaksana
    </p>

    <div class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
      <!-- Area Filter -->
      <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-white rounded-t-xl gap-4">
        <div>
          <h3 class="text-base font-bold text-gray-800">Rekap Keputusan Resmi</h3>
          <p class="text-xs text-gray-500">Daftar keputusan berjenjang dan tindak lanjutnya</p>
        </div>

        <div class="flex flex-wrap gap-3 w-full md:w-auto">
          <select 
            v-model="levelFilter" 
            @change="applyFilter" 
            class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36"
          >
            <option value="">Semua Level</option>
            <option value="TL">Level TL</option>
            <option value="ASMAN">Level Asman</option>
            <option value="MUP">Level MUP</option>
          </select>

          <div class="relative flex-1 md:w-64">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input 
              type="text" 
              v-model="searchKeyword" 
              @input="onSearchInput" 
              placeholder="Cari keputusan / isu..." 
              class="border border-gray-200 rounded-md pl-8 pr-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-full"
            />
          </div>
        </div>
      </div>

      <!-- Tabel Keputusan -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
            <tr>
              <th class="px-6 py-4 w-12">No</th>
              <th class="px-6 py-4">Terkait Issue</th>
              <th class="px-6 py-4">Tingkat Putusan</th>
              <th class="px-6 py-4">Pernyataan Keputusan</th>
              <th class="px-6 py-4">Diputuskan Oleh</th>
              <th class="px-6 py-4">Tanggal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
            <template v-if="decisions.data && decisions.data.length > 0">
              <tr 
                v-for="(dec, idx) in decisions.data" 
                :key="dec.id" 
                class="hover:bg-blue-50/40 transition"
              >
                <td class="px-6 py-4 text-gray-500">
                  {{ (decisions.current_page - 1) * decisions.per_page + idx + 1 }}
                </td>
                <td class="px-6 py-4">
                  <template v-if="dec.issue">
                    <Link :href="`/issues/${dec.issue.id}`" class="font-bold text-blue-600 hover:underline">
                      {{ dec.issue.title }}
                    </Link>
                    <p class="text-[10px] text-gray-400 font-mono">#{{ dec.issue.issue_no }}</p>
                  </template>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4">
                  <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded text-[10px] font-bold">
                    Level {{ dec.decision_level }}
                  </span>
                </td>
                <td class="px-6 py-4 font-semibold text-gray-800 max-w-md truncate" :title="dec.decision">
                  {{ dec.decision }}
                </td>
                <td class="px-6 py-4 text-gray-600">
                  {{ dec.decision_maker?.name || '-' }}
                </td>
                <td class="px-6 py-4 text-gray-500">
                  {{ formatDate(dec.created_at) }}
                </td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="6" class="text-center py-8 text-gray-400">
                Tidak ada data keputusan yang ditemukan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginasi -->
      <div v-if="decisions.links && decisions.links.length > 3" class="p-4 border-t border-gray-100 flex justify-between items-center">
        <span class="text-xs text-gray-500">
          Menampilkan {{ decisions.from || 0 }} - {{ decisions.to || 0 }} dari {{ decisions.total }} keputusan
        </span>
        <div class="flex gap-1">
          <Link 
            v-for="(link, i) in decisions.links" 
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
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  decisions: Object,
});

const urlParams = new URLSearchParams(window.location.search);
const levelFilter = ref(urlParams.get('level') || '');
const searchKeyword = ref(urlParams.get('search') || '');

function applyFilter() {
  const query = {};
  if (levelFilter.value) query.level = levelFilter.value;
  if (searchKeyword.value) query.search = searchKeyword.value;

  router.get('/keputusan', query, { preserveState: true, preserveScroll: true, replace: true });
}

let searchTimer = null;
function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    applyFilter();
  }, 350);
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}
</script>
