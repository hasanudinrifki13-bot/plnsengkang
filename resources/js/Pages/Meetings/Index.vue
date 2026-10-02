<template>
  <AppLayout title="Rapat Koordinasi Mingguan">
    <div class="space-y-6">

      <!-- Header Banner & Action Button -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="far fa-calendar-check text-blue-600"></i> Siklus Rapat Koordinasi Mingguan UP Sengkang
          </h2>
          <p class="text-xs text-gray-500 mt-1">Forum evaluasi N-1, perencanaan N, dan pengambilan keputusan di level berwenang (FR-05 & BSD Section 2 & 3).</p>
        </div>
        <button 
          type="button" 
          @click="showCreateModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-xs font-semibold shadow-xs transition flex items-center gap-2 shrink-0 cursor-pointer"
        >
          <i class="fas fa-plus"></i> Buat Agenda Rapat
        </button>
      </div>

      <!-- Tabs Navigasi: Jadwal Rapat vs Riwayat Selesai -->
      <div class="flex justify-between items-center border-b border-gray-200">
        <div class="flex gap-4">
          <Link 
            :href="`/rapat?tab=jadwal`" 
            :class="[
              'pb-3 px-3 text-xs font-bold transition flex items-center gap-2',
              tab === 'jadwal' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'
            ]"
          >
            <i class="far fa-clock"></i> Jadwal Rapat Aktif / Mendatang
          </Link>
          <Link 
            :href="`/rapat?tab=riwayat`" 
            :class="[
              'pb-3 px-3 text-xs font-bold transition flex items-center gap-2',
              tab === 'riwayat' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'
            ]"
          >
            <i class="fas fa-archive"></i> Riwayat Rapat Selesai
          </Link>
        </div>
        <span class="text-xs text-gray-400 pb-2">Total Sesi: <strong>{{ meetings.length }}</strong></span>
      </div>

      <!-- Daftar Kartu Rapat Dinamis -->
      <div class="space-y-4">
        <template v-if="meetings && meetings.length > 0">
          <div 
            v-for="m in meetings" 
            :key="m.id" 
            class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 flex flex-col md:flex-row items-start md:items-center justify-between hover:shadow-md transition gap-4"
          >
            <div class="flex items-center gap-5 w-full">
              <!-- Kolom Tanggal Kalender -->
              <div class="flex flex-col items-center justify-center w-20 border-r border-gray-100 pr-5 shrink-0 text-center">
                <span class="text-[11px] font-bold text-gray-500">{{ getDayName(m.meeting_date) }}</span>
                <span class="text-3xl font-black text-gray-800 leading-none my-1">{{ getDayNum(m.meeting_date) }}</span>
                <span class="text-[10px] font-medium text-gray-400">{{ getMonthYear(m.meeting_date) }}</span>
              </div>
              
              <!-- Kolom Detail Rapat -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                  <h3 class="text-base font-bold text-gray-800">{{ m.type }}</h3>
                  
                  <span v-if="m.status === 'Berlangsung'" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 animate-pulse">
                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span> LIVE
                  </span>
                  <span v-else-if="m.status === 'Selesai'" class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded">
                    Selesai
                  </span>
                  <span v-else class="bg-orange-50 text-orange-600 border border-orange-200 text-[10px] font-bold px-2 py-0.5 rounded">
                    Akan Datang
                  </span>

                  <span v-if="m.meeting_mode === 'Online' || m.zoom_link" class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fas fa-video text-blue-500"></i> Zoom Meeting
                  </span>
                  <span v-else-if="m.meeting_mode === 'Hybrid'" class="bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fas fa-network-wired text-purple-500"></i> Hybrid (Offline + Zoom)
                  </span>

                  <span v-if="m.issues && m.issues.length > 0" class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fas fa-list-check text-indigo-500"></i> {{ m.issues.length }} Isu Diagendakan
                  </span>
                </div>

                <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-gray-500 font-medium">
                  <span class="flex items-center gap-1.5">
                    <i class="far fa-clock text-blue-500"></i>
                    {{ m.time_start || '08:00' }} - {{ m.time_end || '10:00' }} WITA
                  </span>
                  <span class="flex items-center gap-1.5">
                    <i class="fas fa-map-marker-alt text-rose-500"></i>
                    {{ m.location || 'Ruang Rapat Utama' }}
                  </span>
                  <span class="flex items-center gap-1.5">
                    <i class="fas fa-user-tie text-slate-500"></i>
                    Pimpinan: <strong>{{ m.leader?.name || 'Pimpinan Unit' }}</strong>
                  </span>
                </div>

                <p v-if="m.notes" class="text-xs text-gray-600 mt-2 line-clamp-1 italic bg-slate-50 px-2 py-1 rounded border border-gray-100">
                  <i class="fas fa-sticky-note text-amber-500 mr-1"></i> {{ m.notes }}
                </p>
              </div>
              
              <!-- Kolom Aksi -->
              <div class="flex flex-col sm:flex-row md:flex-col items-end gap-2 shrink-0">
                <Link 
                  :href="`/rapat/${m.id}/workspace`" 
                  :class="[
                    'px-4 py-1.5 rounded-md text-xs font-bold text-center min-w-[130px] transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer',
                    m.status === 'Berlangsung' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-slate-800 hover:bg-slate-900 text-white'
                  ]"
                >
                  <i class="fas fa-laptop-code"></i> Workspace
                </Link>

                <div class="flex items-center gap-1.5">
                  <Link 
                    :href="`/rapat/${m.id}`" 
                    class="border border-gray-200 hover:bg-gray-50 text-gray-700 px-3 py-1 rounded text-xs font-semibold text-center transition cursor-pointer"
                  >
                    Detail
                  </Link>
                  <a 
                    :href="`/rapat/${m.id}/cetak`" 
                    target="_blank" 
                    class="border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1 rounded text-xs font-semibold text-center transition" 
                    title="Cetak Notulen"
                  >
                    <i class="fas fa-print"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </template>
        <div v-else class="bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400">
          <i class="far fa-calendar-times text-4xl mb-3 text-gray-300"></i>
          <p class="text-sm font-semibold text-gray-600">Belum ada agenda rapat yang terdaftar.</p>
          <p class="text-xs text-gray-400 mt-1">Gunakan tombol 'Buat Agenda Rapat' di atas untuk menjadwalkan rapat koordinasi baru.</p>
        </div>
      </div>

    </div>

    <!-- ==================== MODAL BUAT AGENDA RAPAT ==================== -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <div class="flex items-center gap-2">
            <i class="far fa-calendar-plus text-blue-600 text-base"></i>
            <h3 class="text-sm font-bold text-gray-800">Buat Agenda Rapat Koordinasi</h3>
          </div>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <!-- 1. Tipe Rapat -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              Forum / Jenis Rapat <span class="text-red-500">*</span>
            </label>
            <select 
              v-model="createForm.type" 
              required 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
            >
              <option value="Rapat TL + Staf">Rapat TL + Staf (Seksi / Operasional Mingguan - Senin Pagi)</option>
              <option value="Rapat Asman + TL">Rapat Asman + TL (Bidang / Koordinasi Mingguan - Senin Siang)</option>
              <option value="Rapat Tinjauan Manajemen">Rapat Tinjauan Manajemen / RTM (MUP & Asman - Selasa Pagi)</option>
              <option value="CUSTOM">Lainnya (Ketik Judul Rapat Khusus / Insidental)</option>
            </select>
          </div>

          <div v-if="createForm.type === 'CUSTOM'">
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Rapat Khusus <span class="text-red-500">*</span></label>
            <input 
              type="text" 
              v-model="createForm.custom_type" 
              placeholder="Contoh: Rapat Tanggap Darurat Trip Turbin Blok 1" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            />
          </div>

          <!-- 2. Tanggal & Pimpinan Rapat -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Rapat <span class="text-red-500">*</span></label>
              <input 
                type="date" 
                v-model="createForm.meeting_date" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Pimpinan Rapat (Leader) <span class="text-red-500">*</span></label>
              <select 
                v-model="createForm.leader_id" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option v-for="ldr in leaders" :key="ldr.id" :value="ldr.id">
                  {{ ldr.name }} ({{ ldr.role?.name || 'Pejabat' }})
                </option>
              </select>
            </div>
          </div>

          <!-- 3. Waktu Mulai & Selesai -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Waktu Mulai (WITA) <span class="text-red-500">*</span></label>
              <input 
                type="time" 
                v-model="createForm.time_start" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Waktu Selesai (WITA) <span class="text-red-500">*</span></label>
              <input 
                type="time" 
                v-model="createForm.time_end" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>
          </div>

          <!-- 4. Mode & Lokasi -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tipe / Mode Pertemuan <span class="text-red-500">*</span></label>
              <select 
                v-model="createForm.meeting_mode" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="Offline">Tatap Muka (Offline di Ruangan)</option>
                <option value="Online">Online (Zoom / Video Conference)</option>
                <option value="Hybrid">Hybrid (Tatap Muka & Zoom)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi / Ruangan Rapat</label>
              <select 
                v-model="createForm.location" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="Ruang Rapat Operasi & Keandalan">Ruang Rapat Operasi & Keandalan</option>
                <option value="Ruang Rapat Utama UP Sengkang">Ruang Rapat Utama UP Sengkang</option>
                <option value="Ruang Rapat Pemeliharaan">Ruang Rapat Pemeliharaan</option>
                <option value="Ruang Rapat VIP Manajemen (MUP)">Ruang Rapat VIP Manajemen (MUP)</option>
                <option value="Online / Video Conference (Virtual)">Online / Video Conference (Virtual)</option>
              </select>
            </div>
          </div>

          <!-- Bagian Zoom (Jika Online/Hybrid) -->
          <div v-if="createForm.meeting_mode === 'Online' || createForm.meeting_mode === 'Hybrid'" class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-xl space-y-3">
            <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
              <i class="fas fa-video text-blue-600"></i> Detail Tautan Rapat Zoom / Online
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tautan / Link Zoom Meeting</label>
              <input 
                type="url" 
                v-model="createForm.zoom_link" 
                placeholder="https://zoom.us/j/987654321?pwd=..." 
                class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white"
              />
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Meeting ID (Opsional)</label>
                <input 
                  type="text" 
                  v-model="createForm.zoom_meeting_id" 
                  placeholder="890 1234 5678" 
                  class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white"
                />
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Passcode (Opsional)</label>
                <input 
                  type="text" 
                  v-model="createForm.zoom_passcode" 
                  placeholder="PLNNP2026" 
                  class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white"
                />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Status Sesi</label>
              <select 
                v-model="createForm.status" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="Akan Datang">Akan Datang (Terjadwal)</option>
                <option value="Berlangsung">Berlangsung Sekarang (LIVE)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Siklus Mingguan</label>
              <input 
                type="text" 
                disabled 
                :value="`Tahun ${new Date().getFullYear()}`" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-slate-50 text-gray-500"
              />
            </div>
          </div>

          <!-- Isu yang Diagendakan -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
              <span><i class="fas fa-list-check text-blue-600 mr-1"></i> Pilih Isu yang Diagendakan ke Rapat</span>
              <span class="text-[10px] text-gray-400 font-normal">Centang isu untuk dibahas</span>
            </label>
            <div class="max-h-36 overflow-y-auto border border-gray-200 rounded-xl p-2.5 space-y-2 bg-slate-50/50 divide-y divide-gray-100">
              <template v-if="availableIssues && availableIssues.length > 0">
                <label 
                  v-for="iss in availableIssues" 
                  :key="iss.id" 
                  class="flex items-start gap-2.5 pt-1.5 first:pt-0 cursor-pointer hover:bg-white p-1 rounded transition"
                >
                  <input 
                    type="checkbox" 
                    :value="iss.id" 
                    v-model="createForm.issue_ids" 
                    class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <div class="text-[11px] leading-tight">
                    <span class="font-bold text-gray-800">#{{ iss.issue_no }}</span>: {{ iss.title }}
                    <div class="flex items-center gap-2 mt-0.5">
                      <span class="text-[9px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Level {{ iss.current_level }}</span>
                      <span :class="iss.priority === 'CRITICAL' ? 'text-red-600 bg-red-50' : 'text-amber-600 bg-amber-50'" class="text-[9px] font-semibold px-1.5 py-0.5 rounded">{{ iss.priority }}</span>
                    </div>
                  </div>
                </label>
              </template>
              <p v-else class="text-xs text-gray-400 text-center py-2">Tidak ada isu aktif yang perlu diagendakan.</p>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Topik Utama / Catatan Awal Pembahasan</label>
            <textarea 
              v-model="createForm.notes" 
              rows="2" 
              placeholder="Contoh: Evaluasi pekerjaan minggu N-1, tindak lanjut kebocoran seal pompa..." 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <!-- Tombol Submit -->
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
              <i class="fas fa-save"></i> 
              {{ createForm.processing ? 'Menyimpan...' : 'Simpan & Jadwalkan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  meetings: Array,
  leaders: Array,
  availableIssues: Array,
  tab: {
    type: String,
    default: 'jadwal',
  },
});

const page = usePage();
const authUserId = page.props.auth?.user?.id || 1;

const showCreateModal = ref(false);

const todayStr = new Date().toISOString().split('T')[0];
const createForm = useForm({
  type: 'Rapat TL + Staf',
  custom_type: '',
  meeting_date: todayStr,
  time_start: '08:00',
  time_end: '10:00',
  meeting_mode: 'Offline',
  location: 'Ruang Rapat Operasi & Keandalan',
  zoom_link: '',
  zoom_meeting_id: '',
  zoom_passcode: '',
  leader_id: authUserId,
  status: 'Akan Datang',
  notes: '',
  issue_ids: [],
});

function submitCreate() {
  createForm.post('/rapat', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

function getDayName(dStr) {
  if (!dStr) return '';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', { weekday: 'short' }).toUpperCase();
}

function getDayNum(dStr) {
  if (!dStr) return '';
  const d = new Date(dStr);
  return String(d.getDate()).padStart(2, '0');
}

function getMonthYear(dStr) {
  if (!dStr) return '';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
}
</script>
