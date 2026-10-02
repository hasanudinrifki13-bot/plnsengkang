<template>
  <AppLayout title="Task Management">
    <!-- Subtitle Navigasi -->
    <p class="text-xs text-gray-500 -mt-3 mb-4">
      Kelola penugasan task, progres realisasi, dan mitigasi keterlambatan kerja
    </p>

    <!-- ==================== KONTEN UTAMA: TABEL TASK ==================== -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
      
      <!-- Area Filter & Tombol Tambah Task -->
      <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-white rounded-t-xl gap-4">
        <div class="flex flex-wrap gap-3 flex-1 w-full">
          <select 
            v-model="filters.status" 
            @change="applyFilter" 
            class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36"
          >
            <option value="">Semua Status</option>
            <option value="In Progress">On Progress</option>
            <option value="Selesai">Selesai</option>
            <option value="Terlambat">Terlambat (Overdue)</option>
            <option value="Open">Open</option>
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
              placeholder="Cari judul task atau no eskalasi..." 
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

        <!-- Tombol Tambah Task -->
        <button 
          type="button" 
          @click="openCreateModal" 
          class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-xs transition flex items-center justify-center gap-2 shrink-0 cursor-pointer"
        >
          <i class="fas fa-plus"></i> Tambah Task
        </button>
      </div>

      <!-- Tabel Daftar Task -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
            <tr>
              <th class="px-6 py-4 w-12 text-center">No</th>
              <th class="px-6 py-4">Judul Task</th>
              <th class="px-6 py-4">Terkait Eskalasi</th>
              <th class="px-6 py-4">PIC</th>
              <th class="px-6 py-4">Tenggat Waktu</th>
              <th class="px-6 py-4 w-44">Progress</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
            <tr v-for="(task, index) in tasks.data" :key="task.id" class="hover:bg-blue-50/40 transition">
              <!-- No -->
              <td class="px-6 py-4 text-center text-gray-500 font-bold">
                {{ (tasks.from || 1) + index }}
              </td>

              <!-- Judul Task -->
              <td class="px-6 py-4">
                <span class="font-bold text-gray-800 text-sm block">{{ task.title }}</span>
                <span v-if="task.description" class="text-[11px] text-gray-400 font-normal line-clamp-1 max-w-xs block">
                  {{ task.description }}
                </span>
              </td>

              <!-- Terkait Eskalasi -->
              <td class="px-6 py-4">
                <Link 
                  v-if="task.issue" 
                  :href="`/issues/${task.issue.id}`" 
                  class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-mono font-bold bg-blue-50 px-2 py-1 rounded border border-blue-200"
                >
                  <i class="fas fa-link text-[10px]"></i>
                  <span>#{{ task.issue.issue_no }}</span>
                </Link>
                <span v-else class="text-gray-400 italic text-[11px]">Mandiri (Rutin)</span>
              </td>

              <!-- PIC -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 shrink-0">
                    <img 
                      :src="task.assignee?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(task.assignee?.name || 'User')}&size=60`" 
                      class="w-full h-full object-cover" 
                    />
                  </div>
                  <span class="font-bold text-gray-800">{{ task.assignee?.name || 'Belum ditentukan' }}</span>
                </div>
              </td>

              <!-- Tenggat Waktu -->
              <td class="px-6 py-4 font-mono font-semibold text-gray-700">
                <div class="flex items-center gap-1.5">
                  <i class="far fa-calendar-alt text-gray-400"></i>
                  <span>{{ task.due_date }}</span>
                </div>
                <span class="text-[10px] text-gray-400 block mt-0.5">Siklus W{{ task.week_number }}</span>
              </td>

              <!-- Progress -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                    <div 
                      :class="progressColor(task.calculated_status || task.status)" 
                      class="h-2.5 rounded-full transition-all duration-300" 
                      :style="{ width: (task.progress_percent || 0) + '%' }"
                    ></div>
                  </div>
                  <span class="font-bold text-xs text-gray-700 w-10 text-right">{{ task.progress_percent || 0 }}%</span>
                </div>
                <a 
                  v-if="task.completion_attachment" 
                  :href="`/storage/${task.completion_attachment}`" 
                  target="_blank" 
                  class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded mt-1.5 shadow-2xs"
                >
                  <i class="fas fa-paperclip"></i> Bukti Lampiran
                </a>
              </td>

              <!-- Status -->
              <td class="px-6 py-4">
                <span :class="statusBadgeClass(task.calculated_status || task.status)">
                  <i :class="statusIcon(task.calculated_status || task.status)" class="mr-1"></i>
                  {{ task.calculated_status || task.status }}
                </span>
              </td>

              <!-- Aksi -->
              <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button 
                    type="button" 
                    @click="openProgressModal(task)" 
                    class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded text-[11px] font-bold border border-blue-200 transition flex items-center gap-1 cursor-pointer"
                    title="Perbarui Progres"
                  >
                    <i class="fas fa-sliders-h"></i> Progres
                  </button>

                  <button 
                    type="button" 
                    @click="openEditModal(task)" 
                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-gray-100 rounded transition cursor-pointer" 
                    title="Edit Data Task"
                  >
                    <i class="fas fa-edit"></i>
                  </button>

                  <button 
                    type="button" 
                    @click="deleteTask(task)" 
                    class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-gray-100 rounded transition cursor-pointer" 
                    title="Hapus Task"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!tasks.data || tasks.data.length === 0">
              <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                <div class="max-w-xs mx-auto space-y-2">
                  <i class="fas fa-tasks text-3xl text-gray-300"></i>
                  <p class="text-xs font-semibold text-gray-500">Tidak ada data task yang cocok</p>
                  <p class="text-[11px] text-gray-400">Silakan gunakan tombol "Tambah Task" untuk membuat pekerjaan baru.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="tasks.links && tasks.links.length > 3" class="p-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
          Menampilkan <span class="font-bold">{{ tasks.from || 0 }}</span> - <span class="font-bold">{{ tasks.to || 0 }}</span> dari <span class="font-bold">{{ tasks.total }}</span> pekerjaan
        </p>
        <div class="flex items-center gap-1">
          <template v-for="(link, i) in tasks.links" :key="i">
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

    <!-- ==================== MODAL TAMBAH TASK ==================== -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Tambah Task Baru</h3>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Task <span class="text-rose-500">*</span></label>
            <input 
              type="text" 
              v-model="createForm.title" 
              placeholder="Contoh: Penggantian Filter Udara GT-1" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required 
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Pekerjaan</label>
            <textarea 
              v-model="createForm.description" 
              rows="3" 
              placeholder="Jelaskan instruksi atau ruang lingkup pekerjaan..." 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">PIC Pelaksana <span class="text-rose-500">*</span></label>
              <select v-model="createForm.assigned_to" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required>
                <option value="">Pilih Pegawai...</option>
                <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }} ({{ u.employee_no || '-' }})</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tenggat Waktu (Due Date) <span class="text-rose-500">*</span></label>
              <input type="date" v-model="createForm.due_date" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Terkait Isu / Eskalasi (Opsional)</label>
            <select v-model="createForm.issue_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
              <option value="">Mandiri (Bukan Tindak Lanjut Isu)</option>
              <option v-for="iss in issues" :key="iss.id" :value="iss.id">#{{ iss.issue_no }} - {{ iss.title }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Lampiran / Bukti Penugasan (Opsional)</label>
            <input 
              type="file" 
              @change="createForm.completion_attachment = $event.target.files[0]" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-blue-50 file:text-blue-700"
            />
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer">Batal</button>
            <button type="submit" :disabled="createForm.processing" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer">
              {{ createForm.processing ? 'Menyimpan...' : 'Simpan Task' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL QUICK PROGRESS UPDATE ==================== -->
    <div v-if="showProgressModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Update Progres Pekerjaan</h3>
            <p class="text-[11px] text-gray-400 truncate max-w-xs">{{ activeTask?.title }}</p>
          </div>
          <button @click="showProgressModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitProgress" class="space-y-4">
          <div>
            <div class="flex justify-between items-center mb-1">
              <label class="text-xs font-bold text-gray-700">Progres Realisasi</label>
              <span class="text-base font-black text-blue-600 font-mono">{{ progressForm.progress_percent }}%</span>
            </div>
            <input 
              type="range" 
              min="0" 
              max="100" 
              step="5" 
              v-model="progressForm.progress_percent" 
              class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
            />
            <div class="flex justify-between text-[10px] text-gray-400 mt-1 font-mono">
              <span>0% (Mulai)</span>
              <span>50% (Berjalan)</span>
              <span>100% (Selesai)</span>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Hasil / Kendala</label>
            <textarea 
              v-model="progressForm.completion_notes" 
              rows="2" 
              placeholder="Tuliskan catatan progres atau verifikasi lapangan..." 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Unggah Bukti Foto / Dokumen (Opsional)</label>
            <input 
              type="file" 
              @change="progressForm.completion_attachment = $event.target.files[0]" 
              accept="image/*,.pdf,.doc,.docx" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-blue-50 file:text-blue-700"
            />
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
            <button type="button" @click="showProgressModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer">Batal</button>
            <button type="submit" :disabled="progressForm.processing" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer">
              {{ progressForm.processing ? 'Menyimpan...' : 'Perbarui Progres' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL EDIT LENGKAP ==================== -->
    <div v-if="showEditModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Edit Task</h3>
          <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Task <span class="text-rose-500">*</span></label>
            <input type="text" v-model="editForm.title" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Pekerjaan</label>
            <textarea v-model="editForm.description" rows="3" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">PIC Pelaksana <span class="text-rose-500">*</span></label>
              <select v-model="editForm.assigned_to" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required>
                <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tenggat Waktu <span class="text-rose-500">*</span></label>
              <input type="date" v-model="editForm.due_date" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
              <select v-model="editForm.status" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                <option value="Open">Open</option>
                <option value="In Progress">In Progress</option>
                <option value="Selesai">Selesai</option>
                <option value="Terlambat">Terlambat</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Progress (%)</label>
              <input type="number" min="0" max="100" v-model="editForm.progress_percent" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Ganti File Bukti / Lampiran (Opsional)</label>
            <input 
              type="file" 
              @change="editForm.completion_attachment = $event.target.files[0]" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-blue-50 file:text-blue-700"
            />
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
            <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer">Batal</button>
            <button type="submit" :disabled="editForm.processing" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer">
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
  tasks: Object,
  users: Array,
  staffUsers: Array,
  issues: Array,
  currentWeek: Number,
});

const urlParams = new URLSearchParams(window.location.search);
const filters = ref({
  status: urlParams.get('status') || '',
  siklus: urlParams.get('siklus') || 'semua',
  search: urlParams.get('search') || '',
});

const hasActiveFilter = computed(() => {
  return filters.value.status !== '' || filters.value.siklus !== 'semua' || filters.value.search !== '';
});

function applyFilter() {
  const query = {};
  if (filters.value.status) query.status = filters.value.status;
  if (filters.value.siklus && filters.value.siklus !== 'semua') query.siklus = filters.value.siklus;
  if (filters.value.search) query.search = filters.value.search;

  router.get('/pekerjaan', query, { preserveState: true, preserveScroll: true, replace: true });
}

let searchDebounce = null;
function onSearchInput() {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    applyFilter();
  }, 350);
}

function resetFilter() {
  filters.value = { status: '', siklus: 'semua', search: '' };
  router.get('/pekerjaan');
}

// ==================== CREATE MODAL ====================
const showCreateModal = ref(false);
const createForm = useForm({
  title: '',
  description: '',
  assigned_to: '',
  due_date: '',
  issue_id: '',
  completion_attachment: null,
});

function openCreateModal() {
  createForm.reset();
  showCreateModal.value = true;
}

function submitCreate() {
  createForm.post('/pekerjaan', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

// ==================== PROGRESS MODAL ====================
const showProgressModal = ref(false);
const activeTask = ref(null);
const progressForm = useForm({
  progress_percent: 0,
  completion_notes: '',
  completion_attachment: null,
});

function openProgressModal(task) {
  activeTask.value = task;
  progressForm.progress_percent = task.progress_percent || 0;
  progressForm.completion_notes = task.completion_notes || '';
  progressForm.completion_attachment = null;
  showProgressModal.value = true;
}

function submitProgress() {
  if (!activeTask.value) return;
  progressForm.post(`/tasks/${activeTask.value.id}/progress`, {
    headers: { 'X-HTTP-Method-Override': 'PATCH' },
    onSuccess: () => {
      showProgressModal.value = false;
      activeTask.value = null;
    },
  });
}

// ==================== EDIT MODAL ====================
const showEditModal = ref(false);
const editForm = useForm({
  title: '',
  description: '',
  assigned_to: '',
  due_date: '',
  status: 'In Progress',
  progress_percent: 0,
  completion_attachment: null,
});

function openEditModal(task) {
  activeTask.value = task;
  editForm.title = task.title;
  editForm.description = task.description || '';
  editForm.assigned_to = task.assigned_to;
  editForm.due_date = task.due_date;
  editForm.status = task.status;
  editForm.progress_percent = task.progress_percent || 0;
  editForm.completion_attachment = null;
  showEditModal.value = true;
}

function submitEdit() {
  if (!activeTask.value) return;
  editForm.post(`/pekerjaan/${activeTask.value.id}`, {
    headers: { 'X-HTTP-Method-Override': 'PUT' },
    onSuccess: () => {
      showEditModal.value = false;
      activeTask.value = null;
    },
  });
}

// ==================== DELETE ====================
function deleteTask(task) {
  if (confirm(`Apakah Anda yakin ingin menghapus task "${task.title}"?`)) {
    router.delete(`/pekerjaan/${task.id}`);
  }
}

// Helpers
function progressColor(status) {
  const s = String(status || '').toLowerCase();
  if (s.includes('selesai') || s.includes('closed') || s.includes('resolved')) return 'bg-emerald-500';
  if (s.includes('terlambat') || s.includes('overdue')) return 'bg-rose-500';
  return 'bg-blue-600';
}

function statusBadgeClass(status) {
  const s = String(status || '').toLowerCase();
  if (s.includes('selesai') || s.includes('closed') || s.includes('resolved')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
  }
  if (s.includes('terlambat') || s.includes('overdue')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
  }
  if (s.includes('progress')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200';
}

function statusIcon(status) {
  const s = String(status || '').toLowerCase();
  if (s.includes('selesai') || s.includes('closed') || s.includes('resolved')) return 'fas fa-check-circle';
  if (s.includes('terlambat') || s.includes('overdue')) return 'fas fa-exclamation-triangle';
  if (s.includes('progress')) return 'fas fa-spinner';
  return 'fas fa-folder-open';
}
</script>
