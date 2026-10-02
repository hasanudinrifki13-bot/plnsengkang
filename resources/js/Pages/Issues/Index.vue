<template>
  <AppLayout title="Daftar Issue & Eskalasi">
    <!-- Subtitle Navigasi -->
    <p class="text-xs text-gray-500 -mt-3 mb-4">
      Catat, pantau, dan selesaikan eskalasi operasional hingga tuntas
    </p>

    <!-- ==================== TABEL ISSUE / ESKALASI ==================== -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
      
      <!-- Area Filter & Tombol Buat Eskalasi -->
      <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-white rounded-t-xl gap-4">
        <div class="flex flex-wrap gap-3 flex-1 w-full">
          <select 
            v-model="filters.level" 
            @change="applyFilter" 
            class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36"
          >
            <option value="">Semua Level</option>
            <option value="TL">Level TL</option>
            <option value="ASMAN">Level Asman</option>
            <option value="MUP">Level MUP</option>
          </select>

          <select 
            v-model="filters.status" 
            @change="applyFilter" 
            class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36"
          >
            <option value="">Semua Status</option>
            <option value="Open">Open</option>
            <option value="Escalated">Escalated</option>
            <option value="Need Decision">Need Decision</option>
            <option value="Under Review">Under Review</option>
            <option value="Closed">Closed</option>
          </select>

          <select 
            v-model="filters.siklus" 
            @change="applyFilter" 
            class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-48"
          >
            <option value="semua">Semua Siklus</option>
            <option value="N">Minggu N (W{{ currentWeek }} - Berjalan)</option>
            <option value="N-1">Minggu N-1 (W{{ currentWeek - 1 }} - Evaluasi)</option>
          </select>

          <div class="relative flex-1 min-w-[200px]">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input 
              type="text" 
              v-model="filters.search" 
              @input="onSearchInput" 
              placeholder="Cari no tiket atau judul eskalasi..." 
              class="border border-gray-200 rounded-md pl-8 pr-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-full"
            />
          </div>

          <button 
            v-if="hasActiveFilter" 
            @click="resetFilter" 
            type="button" 
            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-md text-xs font-semibold flex items-center gap-1 cursor-pointer"
          >
            <i class="fas fa-times"></i> Reset
          </button>
        </div>

        <!-- Tombol Buat Eskalasi -->
        <button 
          type="button" 
          @click="showCreateModal = true" 
          class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-xs transition flex items-center justify-center gap-2 shrink-0 cursor-pointer"
        >
          <i class="fas fa-plus"></i> Buat Eskalasi Baru
        </button>
      </div>

      <!-- Tabel Eskalasi -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
            <tr>
              <th class="px-6 py-4 w-12 text-center">No</th>
              <th class="px-6 py-4">Judul Eskalasi</th>
              <th class="px-6 py-4">Level Saat Ini</th>
              <th class="px-6 py-4">Prioritas</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Target / SLA</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
            <tr v-for="(iss, idx) in issues.data" :key="iss.id" class="hover:bg-blue-50/40 transition">
              <td class="px-6 py-4 text-center text-gray-500 font-bold">
                {{ (issues.from || 1) + idx }}
              </td>

              <td class="px-6 py-4">
                <Link :href="`/issues/${iss.id}`" class="font-bold text-gray-800 hover:text-blue-600 text-sm block">
                  {{ iss.title }}
                </Link>
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1 text-[11px] text-gray-500">
                  <span class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.5 rounded font-bold">#{{ iss.issue_no }}</span>
                  <span class="flex items-center gap-1" :title="'Staff Pelapor: ' + (iss.reporter?.name || 'Staff')">
                    <i class="fas fa-user text-[10px] text-slate-400"></i>
                    <span class="text-gray-700 font-medium">{{ iss.reporter?.name || 'Staff' }}</span>
                  </span>
                  <span class="text-gray-300 font-bold">➔</span>
                  <span class="flex items-center gap-1" :title="'Team Leader: ' + (iss.hierarchy_line?.tl?.name || '-')">
                    <i class="fas fa-user-shield text-[10px] text-blue-500"></i>
                    <span class="text-gray-600">TL: <b class="text-gray-800">{{ iss.hierarchy_line?.tl?.name || '-' }}</b></span>
                  </span>
                  <span class="text-gray-300 font-bold">➔</span>
                  <span class="flex items-center gap-1" :title="'Asisten Manajer: ' + (iss.hierarchy_line?.asman?.name || 'Langsung ke MUP')">
                    <i class="fas fa-user-tie text-[10px] text-indigo-500"></i>
                    <span class="text-gray-600">Asman: <b class="text-gray-800">{{ iss.hierarchy_line?.asman?.name || 'MUP' }}</b></span>
                  </span>
                </div>
              </td>

              <td class="px-6 py-4">
                <span class="font-bold text-gray-800 bg-slate-100 px-2 py-0.5 rounded border border-gray-200">
                  Level {{ iss.current_level }}
                </span>
              </td>

              <td class="px-6 py-4">
                <span :class="priorityBadgeClass(iss.priority)">
                  {{ iss.priority }}
                </span>
              </td>

              <td class="px-6 py-4">
                <span :class="statusBadgeClass(iss.status)">
                  {{ iss.status }}
                </span>
              </td>

              <td class="px-6 py-4 font-mono text-[11px] text-gray-600">
                <span>{{ iss.due_date || '-' }}</span>
              </td>

              <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <Link 
                    :href="`/issues/${iss.id}`" 
                    class="px-2.5 py-1 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded text-xs font-bold transition border border-blue-200"
                    title="Detail & Eskalasi"
                  >
                    Detail
                  </Link>
                  <button 
                    type="button" 
                    @click="openEditModal(iss)"
                    class="px-2 py-1 bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 rounded text-xs font-bold transition border border-amber-200 cursor-pointer"
                    title="Edit Isu"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                  <button 
                    type="button" 
                    @click="confirmDelete(iss)"
                    class="px-2 py-1 bg-red-50 hover:bg-red-600 hover:text-white text-red-700 rounded text-xs font-bold transition border border-red-200 cursor-pointer"
                    title="Hapus Isu"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!issues.data || issues.data.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                <div class="max-w-xs mx-auto space-y-2">
                  <i class="fas fa-inbox text-3xl text-gray-300"></i>
                  <p class="text-xs font-semibold text-gray-500">Tidak ada eskalasi yang sesuai filter</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="issues.links && issues.links.length > 3" class="p-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
          Menampilkan <span class="font-bold">{{ issues.from || 0 }}</span> - <span class="font-bold">{{ issues.to || 0 }}</span> dari <span class="font-bold">{{ issues.total }}</span> isu
        </p>
        <div class="flex items-center gap-1">
          <template v-for="(link, i) in issues.links" :key="i">
            <Link 
              v-if="link.url" 
              :href="link.url" 
              v-html="link.label" 
              :class="[
                'px-3 py-1 text-xs rounded border transition',
                link.active ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'border-gray-200 text-gray-600 hover:bg-gray-50'
              ]"
            />
            <span 
              v-else 
              v-html="link.label" 
              class="px-3 py-1 text-xs rounded border border-gray-200 text-gray-300 cursor-not-allowed"
            />
          </template>
        </div>
      </div>

    </div>

    <!-- ==================== MODAL BUAT ESKALASI ==================== -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Lapor & Buat Eskalasi Baru</h3>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Isu / Kendala <span class="text-rose-500">*</span></label>
            <input 
              type="text" 
              v-model="createForm.title" 
              placeholder="Contoh: Tekanan Minyak Pelumas Turbin Turun" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required 
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tingkat Prioritas <span class="text-rose-500">*</span></label>
              <select v-model="createForm.priority" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required>
                <option value="CRITICAL">Critical (Darurat)</option>
                <option value="HIGH">High (Tinggi)</option>
                <option value="MEDIUM">Medium (Sedang)</option>
                <option value="LOW">Low (Rendah)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Target Penyelesaian</label>
              <input type="date" v-model="createForm.due_date" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Kendala Secara Detail <span class="text-rose-500">*</span></label>
            <textarea 
              v-model="createForm.description" 
              rows="4" 
              placeholder="Uraikan kronologi, indikasi kegagalan, dan dampak terhadap operasional unit..." 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              required
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Lampiran Pendukung (Foto / Dokumen Max 10MB)</label>
            <input 
              type="file" 
              @change="createForm.attachment = $event.target.files[0]" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-blue-50 file:text-blue-700" 
            />
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer">Batal</button>
            <button type="submit" :disabled="createForm.processing" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer">
              {{ createForm.processing ? 'Menyimpan...' : 'Kirim Isu' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL EDIT ESKALASI ==================== -->
    <div v-if="showEditModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Edit Issue & Eskalasi</h3>
          <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Isu / Kendala <span class="text-rose-500">*</span></label>
            <input 
              type="text" 
              v-model="editForm.title" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required 
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tingkat Prioritas <span class="text-rose-500">*</span></label>
              <select v-model="editForm.priority" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required>
                <option value="CRITICAL">Critical (Darurat)</option>
                <option value="HIGH">High (Tinggi)</option>
                <option value="MEDIUM">Medium (Sedang)</option>
                <option value="LOW">Low (Rendah)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
              <select v-model="editForm.status" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                <option value="Open">Open</option>
                <option value="Escalated">Escalated</option>
                <option value="Under Review">Under Review</option>
                <option value="Resolved">Resolved</option>
                <option value="Closed">Closed</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Target Penyelesaian</label>
            <input type="date" v-model="editForm.due_date" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Kendala Secara Detail <span class="text-rose-500">*</span></label>
            <textarea 
              v-model="editForm.description" 
              rows="4" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required
            ></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button 
              type="button" 
              @click="showEditModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 cursor-pointer font-medium"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="editForm.processing"
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs cursor-pointer transition disabled:opacity-50"
            >
              {{ editForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  issues: Object,
});

const currentWeek = new Date().getWeek ? new Date().getWeek() : 40;

const urlParams = new URLSearchParams(window.location.search);
const filters = ref({
  level: urlParams.get('level') || '',
  status: urlParams.get('status') || '',
  siklus: urlParams.get('siklus') || 'semua',
  search: urlParams.get('search') || '',
});

const hasActiveFilter = computed(() => {
  return filters.value.level !== '' || filters.value.status !== '' || filters.value.siklus !== 'semua' || filters.value.search !== '';
});

function applyFilter() {
  const query = {};
  if (filters.value.level) query.level = filters.value.level;
  if (filters.value.status) query.status = filters.value.status;
  if (filters.value.siklus && filters.value.siklus !== 'semua') query.siklus = filters.value.siklus;
  if (filters.value.search) query.search = filters.value.search;

  router.get('/issues', query, { preserveState: true, preserveScroll: true, replace: true });
}

let searchDebounce = null;
function onSearchInput() {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    applyFilter();
  }, 350);
}

function resetFilter() {
  filters.value = { level: '', status: '', siklus: 'semua', search: '' };
  router.get('/issues');
}

// Create Modal
const showCreateModal = ref(false);
const createForm = useForm({
  title: '',
  description: '',
  priority: 'MEDIUM',
  due_date: '',
  attachment: null,
});

function submitCreate() {
  createForm.post('/issues', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

// Edit Modal
const showEditModal = ref(false);
const editForm = useForm({
  id: null,
  title: '',
  description: '',
  priority: 'MEDIUM',
  status: 'Open',
  due_date: '',
});

function openEditModal(iss) {
  editForm.id = iss.id;
  editForm.title = iss.title;
  editForm.description = iss.description;
  editForm.priority = iss.priority || 'MEDIUM';
  editForm.status = iss.status || 'Open';
  editForm.due_date = iss.due_date ? String(iss.due_date).substring(0, 10) : '';
  showEditModal.value = true;
}

function submitEdit() {
  editForm.put(`/issues/${editForm.id}`, {
    onSuccess: () => {
      showEditModal.value = false;
    },
  });
}

function confirmDelete(iss) {
  if (confirm(`Apakah Anda yakin ingin menghapus issue "${iss.title}" (#${iss.issue_no})?`)) {
    router.delete(`/issues/${iss.id}`);
  }
}

function priorityBadgeClass(p) {
  const priority = String(p || '').toUpperCase();
  if (priority === 'CRITICAL' || priority === 'HIGH') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
  }
  if (priority === 'MEDIUM') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
}

function statusBadgeClass(s) {
  const status = String(s || '').toLowerCase();
  if (status.includes('closed') || status.includes('resolved')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
  }
  if (status.includes('escalated')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200';
  }
  if (status.includes('review') || status.includes('decision')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200';
}
</script>
