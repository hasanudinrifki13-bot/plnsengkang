<template>
  <div class="bg-[#f8fafc] flex flex-col h-screen overflow-hidden text-sm font-sans antialiased text-gray-800">
    
    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-2.5 flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 z-10 shrink-0 shadow-xs">
      <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
        <Link :href="`/rapat/${meeting.id}`" class="text-gray-400 hover:text-gray-700 p-1">
          <i class="fas fa-arrow-left"></i>
        </Link>
        <div>
          <h2 class="text-xs sm:text-sm font-bold text-gray-800 flex items-center gap-1.5 sm:gap-2">
            <span class="truncate max-w-[200px] sm:max-w-none">{{ meeting.type }}</span>
            <span class="text-gray-400 font-normal">|</span>
            <span class="text-[11px] sm:text-xs font-semibold text-gray-500">
              {{ formatDateFull(meeting.meeting_date) }}
            </span>
          </h2>
        </div>
        <span v-if="meeting.status === 'Berlangsung'" class="bg-red-500 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 animate-pulse">
          <span class="w-1.5 h-1.5 bg-white rounded-full"></span> LIVE
        </span>
        <span v-else class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full">
          {{ meeting.status }}
        </span>
      </div>

      <div class="flex items-center gap-2 sm:gap-3">
        <a 
          :href="`/rapat/${meeting.id}/cetak`" 
          target="_blank" 
          class="border border-blue-500 text-blue-600 hover:bg-blue-50 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition"
        >
          <i class="fas fa-print"></i> <span class="hidden sm:inline">Cetak Notulen</span> PDF
        </a>
        <button 
          v-if="meeting.status !== 'Selesai'"
          @click="showFinishModal = true" 
          class="bg-slate-800 hover:bg-slate-900 text-white px-3 sm:px-4 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-xs transition cursor-pointer"
        >
          <i class="fas fa-check-double"></i> Selesaikan Rapat
        </button>
        <Link 
          v-else 
          href="/rapat" 
          class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 sm:px-4 py-1.5 rounded-lg text-xs font-semibold"
        >
          Kembali
        </Link>
      </div>
    </header>

    <!-- Main 2-Column Split Workspace -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
      
      <!-- Sisi Kiri: Video Hub & Roster Peserta -->
      <div class="w-full lg:w-2/5 bg-slate-900 p-4 sm:p-6 flex flex-col justify-start relative border-b lg:border-b-0 lg:border-r border-slate-800 overflow-y-auto space-y-4 max-h-[35vh] sm:max-h-[45vh] lg:max-h-full shrink-0">
        <!-- Panel Zoom Hub -->
        <div class="w-full bg-slate-800/90 rounded-2xl p-5 border border-slate-700 shadow-xl space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-700">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full" :class="meeting.status === 'Berlangsung' ? 'bg-red-500 animate-pulse' : 'bg-emerald-400'"></span>
              <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                Mode: {{ meeting.meeting_mode || 'Online (Zoom)' }}
              </h3>
            </div>
            <span class="text-[10px] bg-blue-900/60 text-blue-300 font-semibold px-2 py-0.5 rounded border border-blue-700">
              {{ meeting.time_start || '08:00' }} - {{ meeting.time_end || '10:00' }} WITA
            </span>
          </div>

          <div v-if="meeting.zoom_link" class="space-y-3">
            <a 
              :href="meeting.zoom_link" 
              target="_blank" 
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg transition"
            >
              <i class="fas fa-video text-sm"></i> Buka Zoom Video Meeting
            </a>
            <div class="grid grid-cols-2 gap-2 text-[11px] text-gray-300 bg-slate-900/70 p-2.5 rounded-lg border border-slate-700">
              <div>
                <span class="text-gray-400 block text-[10px]">Meeting ID</span>
                <span class="font-mono font-bold text-white">{{ meeting.zoom_meeting_id || '-' }}</span>
              </div>
              <div>
                <span class="text-gray-400 block text-[10px]">Passcode</span>
                <span class="font-mono font-bold text-white">{{ meeting.zoom_passcode || '-' }}</span>
              </div>
            </div>
          </div>
          <div v-else class="text-xs text-gray-300 text-center py-2 bg-slate-900/50 rounded-lg p-3">
            <i class="fas fa-building text-blue-400 mr-1.5"></i> Rapat Tatap Muka di {{ meeting.location || 'Ruang Rapat UP Sengkang' }}
          </div>
        </div>

        <!-- Daftar Peserta Rapat -->
        <div class="w-full bg-slate-800/80 rounded-2xl p-5 border border-slate-700 space-y-3 flex-1">
          <div class="flex items-center justify-between pb-2 border-b border-slate-700">
            <h4 class="text-xs font-bold text-gray-200 flex items-center gap-1.5">
              <i class="fas fa-users text-blue-400"></i> Roster Peserta Rapat ({{ users.length }})
            </h4>
            <span class="text-[10px] text-emerald-400 bg-emerald-950/60 border border-emerald-800 px-2 py-0.5 rounded-full font-bold">
              Hadir & Aktif
            </span>
          </div>

          <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
            <div 
              v-for="u in users" 
              :key="u.id" 
              class="flex items-center justify-between p-2 rounded-lg bg-slate-900/50 border border-slate-700/60 hover:border-blue-500/50 transition"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 overflow-hidden">
                  <img v-if="u.avatar" :src="`/storage/${u.avatar}`" class="w-full h-full object-cover" />
                  <span v-else>{{ (u.name || 'U').substring(0, 2).toUpperCase() }}</span>
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-semibold text-gray-200 truncate">{{ u.name }}</p>
                  <p class="text-[10px] text-gray-400 truncate">{{ u.position?.name || u.role?.name || 'Staff' }}</p>
                </div>
              </div>
              <span class="text-[9px] font-mono text-blue-400 bg-blue-950/80 px-1.5 py-0.5 rounded border border-blue-900 shrink-0">
                {{ u.role?.name || 'STAFF' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Sisi Kanan: 4 Tahapan Agenda BSD Interaktif -->
      <div class="flex-1 flex flex-col bg-white overflow-hidden">
        
        <!-- Tab Selector Bar -->
        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 pt-3 flex items-center justify-between gap-4 overflow-x-auto shrink-0">
          <div class="flex gap-2 sm:gap-4 whitespace-nowrap text-xs font-bold">
            <button 
              type="button" 
              @click="activeAgendaTab = 1"
              :class="activeAgendaTab === 1 ? 'border-b-2 border-blue-600 text-blue-600 pb-3' : 'text-gray-500 hover:text-gray-700 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer"
            >
              <span class="w-5 h-5 rounded-full text-[10px] flex items-center justify-center" :class="activeAgendaTab === 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">1</span>
              <span>Informasi Atasan</span>
            </button>

            <button 
              type="button" 
              @click="activeAgendaTab = 2"
              :class="activeAgendaTab === 2 ? 'border-b-2 border-blue-600 text-blue-600 pb-3' : 'text-gray-500 hover:text-gray-700 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer"
            >
              <span class="w-5 h-5 rounded-full text-[10px] flex items-center justify-center" :class="activeAgendaTab === 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">2</span>
              <span>Evaluasi N-1 & Tunggakan</span>
              <span v-if="evalTasks.length > 0" class="bg-rose-100 text-rose-700 text-[10px] px-1.5 py-0.2 rounded-full font-bold ml-0.5">
                {{ evalTasks.length }}
              </span>
            </button>

            <button 
              type="button" 
              @click="activeAgendaTab = 3"
              :class="activeAgendaTab === 3 ? 'border-b-2 border-blue-600 text-blue-600 pb-3' : 'text-gray-500 hover:text-gray-700 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer"
            >
              <span class="w-5 h-5 rounded-full text-[10px] flex items-center justify-center" :class="activeAgendaTab === 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">3</span>
              <span>Rencana Minggu N</span>
            </button>

            <button 
              type="button" 
              @click="activeAgendaTab = 4"
              :class="activeAgendaTab === 4 ? 'border-b-2 border-blue-600 text-blue-600 pb-3' : 'text-gray-500 hover:text-gray-700 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer"
            >
              <span class="w-5 h-5 rounded-full text-[10px] flex items-center justify-center" :class="activeAgendaTab === 4 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">4</span>
              <span>Rekap Keputusan</span>
            </button>
          </div>
        </div>

        <!-- Konten Agenda Sesuai Tab -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
          
          <!-- TAB 1: INFORMASI CASCADING -->
          <div v-if="activeAgendaTab === 1" class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Agenda 1: Penyampaian Arahan & Informasi Atasan</h3>
                <p class="text-xs text-gray-500">Arahan resmi manajemen yang harus disosialisasikan secara berjenjang kepada tim.</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <template v-if="informations && informations.length > 0">
                <div 
                  v-for="info in informations" 
                  :key="info.id" 
                  class="bg-slate-50/70 border border-gray-200 p-4 rounded-xl space-y-2 hover:shadow-sm transition"
                >
                  <div class="flex items-center justify-between">
                    <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded">
                      {{ info.category || 'ARAHAN' }}
                    </span>
                    <span class="text-[10px] text-gray-400 font-mono">
                      {{ formatDateOnly(info.published_at) }}
                    </span>
                  </div>
                  <h4 class="text-xs font-bold text-gray-800">{{ info.title }}</h4>
                  <p class="text-xs text-gray-600 line-clamp-3 leading-relaxed">{{ info.content }}</p>
                  <div class="text-[10px] text-gray-400 pt-1 border-t border-gray-100">
                    Oleh: <b class="text-gray-700">{{ info.publisher?.name || 'Manajemen UP Sengkang' }}</b>
                  </div>
                </div>
              </template>
              <div v-else class="col-span-2 text-center py-12 text-gray-400 text-xs">
                Belum ada instruksi atau arahan cascading khusus untuk jenjang ini.
              </div>
            </div>
          </div>

          <!-- TAB 2: EVALUASI N-1 & TASK OUTSTANDING -->
          <div v-if="activeAgendaTab === 2" class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Agenda 2: Evaluasi Pekerjaan Minggu N-1 & Task Backlog</h3>
                <p class="text-xs text-gray-500">Pekerjaan tertunda atau belum selesai dari siklus sebelumnya otomatis ditarik ke rapat ini.</p>
              </div>
            </div>

            <!-- Kartu Tunggakan Pekerjaan -->
            <div class="space-y-3">
              <template v-if="evalTasks && evalTasks.length > 0">
                <div 
                  v-for="tsk in evalTasks" 
                  :key="tsk.id" 
                  class="border border-gray-200 p-4 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/60 transition bg-white"
                >
                  <div class="space-y-1">
                    <div class="flex items-center gap-2">
                      <span class="font-bold text-xs text-gray-800">{{ tsk.title }}</span>
                      <span v-if="isTaskOverdue(tsk)" class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded border border-rose-200">
                        OVERDUE
                      </span>
                    </div>
                    <p class="text-[11px] text-gray-500">
                      PIC: <b class="text-gray-700">{{ tsk.assignee?.name || 'Staff' }}</b> | Tenggat: <span class="font-mono text-gray-600">{{ tsk.due_date ? formatDateOnly(tsk.due_date) : '-' }}</span>
                    </p>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="text-right">
                      <span class="text-xs font-bold text-gray-800">{{ tsk.progress_percent }}%</span>
                      <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                        <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${tsk.progress_percent}%`"></div>
                      </div>
                    </div>
                    <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded text-xs font-bold">
                      {{ tsk.status }}
                    </span>
                  </div>
                </div>
              </template>
              <div v-else class="text-center py-10 text-gray-400 text-xs">
                Tidak ada tunggakan pekerjaan yang terdeteksi. Seluruh target berjalan tepat waktu.
              </div>
            </div>
          </div>

          <!-- TAB 3: PERENCANAAN MINGGU N -->
          <div v-if="activeAgendaTab === 3" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Agenda 3: Perencanaan Pekerjaan Minggu Berjalan (N)</h3>
                <p class="text-xs text-gray-500">Tambah komitmen penugasan baru langsung ke PIC selama sesi rapat berlangsung.</p>
              </div>
              <button 
                type="button" 
                @click="showAddTaskModal = true"
                class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-xs shrink-0 cursor-pointer"
              >
                <i class="fas fa-plus"></i> Tambah Task Minggu N
              </button>
            </div>

            <div class="space-y-3">
              <template v-if="plannedTasks && plannedTasks.length > 0">
                <div 
                  v-for="ptsk in plannedTasks" 
                  :key="ptsk.id" 
                  class="border border-gray-200 p-4 rounded-xl flex items-center justify-between text-xs hover:bg-slate-50 transition bg-white"
                >
                  <div>
                    <h4 class="font-bold text-gray-800">{{ ptsk.title }}</h4>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                      PIC: {{ ptsk.assignee?.name || 'Staff' }} | Target: {{ ptsk.due_date ? formatDateOnly(ptsk.due_date) : '-' }}
                    </p>
                  </div>
                  <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-gray-700">{{ ptsk.progress_percent }}%</span>
                    <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold">
                      {{ ptsk.status }}
                    </span>
                  </div>
                </div>
              </template>
              <div v-else class="text-center py-10 text-gray-400 text-xs">
                Belum ada pekerjaan baru yang direncanakan. Gunakan tombol 'Tambah Task Minggu N' di atas.
              </div>
            </div>
          </div>

          <!-- TAB 4: REKAP KEPUTUSAN -->
          <div v-if="activeAgendaTab === 4" class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Agenda 4: Rekap Putusan & Instruksi Rapat</h3>
                <p class="text-xs text-gray-500">Daftar keputusan resmi yang telah dikonfirmasi dan ditetapkan oleh pimpinan rapat.</p>
              </div>
            </div>

            <div class="space-y-3">
              <template v-if="decisions && decisions.length > 0">
                <div 
                  v-for="dec in decisions" 
                  :key="dec.id" 
                  class="bg-emerald-50/30 border border-emerald-200 p-4 rounded-xl text-xs space-y-2"
                >
                  <div class="flex items-center justify-between">
                    <span class="bg-emerald-600 text-white font-bold px-2 py-0.5 rounded text-[10px]">
                      Putusan Level {{ dec.decision_level }}
                    </span>
                    <span class="text-gray-400 font-mono text-[11px]">{{ formatDateOnly(dec.created_at) }}</span>
                  </div>
                  <p class="font-bold text-gray-800 text-sm">"{{ dec.decision }}"</p>
                  <div v-if="dec.follow_up" class="text-gray-600">
                    <span class="font-semibold text-gray-700">Tindak Lanjut:</span> {{ dec.follow_up }}
                  </div>
                  <p class="text-[11px] text-gray-500">
                    Ditetapkan oleh: <b class="text-emerald-900">{{ dec.decision_maker?.name || 'Pimpinan' }}</b>
                  </p>
                </div>
              </template>
              <div v-else class="text-center py-10 text-gray-400 text-xs">
                Belum ada rekap putusan baru yang dicatat.
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- MODAL SELESAIKAN RAPAT -->
    <div v-if="showFinishModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Selesaikan Rapat Koordinasi</h3>
          <button @click="showFinishModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>
        <p class="text-xs text-gray-500 mb-3">Tuliskan ringkasan notulen rapat koordinasi untuk arsip resmi dan berita acara.</p>

        <form @submit.prevent="submitFinish" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Notulen / Kesimpulan Akhir</label>
            <textarea 
              v-model="finishForm.notes" 
              rows="4" 
              required
              placeholder="Tuliskan kesimpulan evaluasi mingguan dan arahan khusus pimpinan..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button 
              type="button" 
              @click="showFinishModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="finishForm.processing"
              class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer"
            >
              {{ finishForm.processing ? 'Menyimpan...' : 'Selesaikan & Rekap' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL TAMBAH TASK MINGGU N -->
    <div v-if="showAddTaskModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Tambah Penugasan Minggu N</h3>
          <button @click="showAddTaskModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitAddTask" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama / Uraian Pekerjaan <span class="text-red-500">*</span></label>
            <input 
              type="text" 
              v-model="addTaskForm.title" 
              required 
              placeholder="Contoh: Penggantian filter oli pelumas turbin..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tugaskan ke (PIC) <span class="text-red-500">*</span></label>
              <select 
                v-model="addTaskForm.assigned_to" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="">-- Pilih Karyawan --</option>
                <option v-for="u in users" :key="u.id" :value="u.id">
                  {{ u.name }} ({{ u.role?.name || '' }})
                </option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Target Selesai (Due Date) <span class="text-red-500">*</span></label>
              <input 
                type="date" 
                v-model="addTaskForm.due_date" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Detail Keterangan</label>
            <textarea 
              v-model="addTaskForm.description" 
              rows="2" 
              placeholder="Catatan teknis atau instruksi khusus..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button 
              type="button" 
              @click="showAddTaskModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="addTaskForm.processing"
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer"
            >
              {{ addTaskForm.processing ? 'Menyimpan...' : 'Simpan Task' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  meeting: Object,
  meetingLevel: String,
  informations: Array,
  evalTasks: Array,
  activeIssues: Array,
  allOpenIssues: Array,
  plannedTasks: Array,
  decisions: Array,
  users: Array,
});

const activeAgendaTab = ref(1);
const showFinishModal = ref(false);
const showAddTaskModal = ref(false);

const finishForm = useForm({
  notes: props.meeting?.notes || '',
});

function submitFinish() {
  finishForm.post(`/rapat/${props.meeting.id}/finish`, {
    onSuccess: () => {
      showFinishModal.value = false;
    },
  });
}

const defaultTaskDueDate = new Date();
defaultTaskDueDate.setDate(defaultTaskDueDate.getDate() + 5);
const addTaskForm = useForm({
  title: '',
  assigned_to: '',
  due_date: defaultTaskDueDate.toISOString().split('T')[0],
  description: '',
});

function submitAddTask() {
  addTaskForm.post(`/rapat/${props.meeting.id}/task`, {
    preserveScroll: true,
    onSuccess: () => {
      showAddTaskModal.value = false;
      addTaskForm.reset();
    },
  });
}

function formatDateFull(dStr) {
  if (!dStr) return '-';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
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

function isTaskOverdue(task) {
  if (!task.due_date) return false;
  const today = new Date().toISOString().split('T')[0];
  return task.due_date < today && !['Selesai', 'Closed', 'Resolved'].includes(task.status);
}
</script>
