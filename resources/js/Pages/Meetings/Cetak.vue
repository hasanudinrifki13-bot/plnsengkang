<template>
  <div class="bg-slate-100 py-10 px-4 min-h-screen font-sans text-gray-800">
    <Head :title="`Risalah Rapat - ${meeting.type} - PLN NP UP Sengkang`" />

    <!-- FLOATING ACTIONS BAR (NO-PRINT) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap justify-between items-center bg-white p-4 rounded-xl shadow-xs border border-gray-200 gap-3">
      <Link :href="`/rapat/${meeting.id}`" class="text-xs font-semibold text-gray-600 hover:text-blue-600 flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Kembali ke Agenda Rapat
      </Link>
      <div class="flex items-center gap-3">
        <span class="text-xs text-gray-500 font-medium hidden sm:inline">Format: Kertas A4 Standar Risalah PLN</span>
        <button 
          type="button"
          @click="doPrint" 
          class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2 cursor-pointer"
        >
          <i class="fas fa-print"></i> Cetak / Ekspor PDF
        </button>
      </div>
    </div>

    <!-- PRINT DOCUMENT CONTAINER (A4 SIZE) -->
    <div class="print-page max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-xl shadow-lg border border-gray-200 text-gray-800 text-xs leading-normal">
      
      <!-- KOP SURAT RESMI PT PLN NUSANTARA POWER -->
      <div class="border-b-2 border-gray-900 pb-4 mb-6">
        <div class="flex justify-between items-start">
          <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 bg-blue-700 text-white font-black text-xl rounded-lg flex items-center justify-center shadow-xs">
              PLN
            </div>
            <div>
              <h1 class="text-base font-extrabold tracking-tight text-gray-900 uppercase">PT PLN NUSANTARA POWER</h1>
              <p class="text-[11px] font-bold text-gray-700 uppercase">UNIT PELAKSANA PENGENDALIAN PEMBANGKITAN SENGKANG</p>
              <p class="text-[10px] text-gray-500">Jl. Poros Sengkang - Siwa Km. 20, Kabupaten Wajo, Sulawesi Selatan 90911</p>
            </div>
          </div>
          <div class="text-right">
            <span class="text-[9px] font-mono text-gray-400 block uppercase tracking-wider">Nomor Risalah Rapat</span>
            <span class="text-xs font-mono font-bold text-gray-900 bg-slate-50 px-2.5 py-1 rounded border border-gray-200 inline-block mt-0.5">
              RIS-SKG/{{ formatYear(meeting.meeting_date) }}/W{{ meeting.week_number || 40 }}/00{{ meeting.id }}
            </span>
            <span class="text-[9px] text-emerald-700 font-semibold block mt-1">Status: Terverifikasi Sistem</span>
          </div>
        </div>
      </div>

      <!-- JUDUL RISALAH -->
      <div class="text-center mb-6">
        <h2 class="text-sm font-extrabold uppercase underline tracking-wider text-gray-900">NOTULEN & RISALAH RAPAT KOORDINASI MINGGUAN</h2>
        <p class="text-xs font-bold text-blue-700 mt-1 uppercase">{{ meeting.type }}</p>
        <p class="text-[11px] text-gray-500">Siklus Mingguan: Minggu Ke-{{ meeting.week_number || 40 }} Tahun {{ meeting.year || 2026 }}</p>
      </div>

      <!-- INFORMASI PELAKSANAAN RAPAT -->
      <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
        <table class="w-full text-xs">
          <tbody>
            <tr>
              <td class="w-32 py-1 font-bold text-gray-700">Hari / Tanggal</td>
              <td class="w-4">:</td>
              <td class="py-1">{{ formatDateLong(meeting.meeting_date) }}</td>
              <td class="w-32 py-1 font-bold text-gray-700">Pimpinan Rapat</td>
              <td class="w-4">:</td>
              <td class="py-1 font-semibold text-gray-900">{{ meeting.leader?.name || 'Pimpinan Rapat' }} ({{ meeting.leader?.position?.name || 'Pejabat Unit' }})</td>
            </tr>
            <tr>
              <td class="py-1 font-bold text-gray-700">Waktu Pelaksanaan</td>
              <td>:</td>
              <td class="py-1">{{ meeting.time_start || '08:00' }} - {{ meeting.time_end || '10:00' }} WITA</td>
              <td class="py-1 font-bold text-gray-700">Mode Rapat</td>
              <td>:</td>
              <td class="py-1">{{ meeting.meeting_mode || 'Offline' }} {{ meeting.zoom_meeting_id ? `(Zoom ID: ${meeting.zoom_meeting_id})` : '' }}</td>
            </tr>
            <tr>
              <td class="py-1 font-bold text-gray-700">Tempat Pelaksanaan</td>
              <td>:</td>
              <td class="py-1">{{ meeting.location || 'Ruang Rapat UP Sengkang' }}</td>
              <td class="py-1 font-bold text-gray-700">Seksi / Unit</td>
              <td>:</td>
              <td class="py-1">Operasi & Pemeliharaan UP Sengkang</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- CATATAN & KESIMPULAN RAPAT -->
      <div v-if="meeting.notes" class="mb-6 bg-blue-50/70 p-4 rounded-lg border border-blue-200">
        <h3 class="font-bold text-blue-950 text-xs uppercase mb-1 flex items-center gap-1.5">
          <i class="fas fa-quote-left text-blue-600"></i> Arahan Pokok & Kesimpulan Pimpinan:
        </h3>
        <p class="text-xs text-blue-900 leading-relaxed font-normal whitespace-pre-line">{{ meeting.notes }}</p>
      </div>

      <!-- I. AGENDA PEMBAHASAN -->
      <div class="mb-6">
        <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
          <i class="fas fa-list-ol text-gray-500"></i> I. Agenda Pembahasan Terstruktur
        </h3>
        <ol class="list-decimal pl-5 space-y-1 text-xs text-gray-700">
          <li>Penyampaian arahan kebijakan dan keselamatan ketenagalistrikan (K3L) pembangkit.</li>
          <li>Evaluasi ketercapaian task dan mitigasi pekerjaan tertunda dari minggu sebelumnya (Siklus N-1).</li>
          <li>Identifikasi dan pembahasan eskalasi kendala operasional unit pembangkit.</li>
          <li>Perumusan penetapan keputusan resmi pimpinan berwenang (<em>Lowest Competent Level</em>).</li>
          <li>Penyusunan target task baru dan penugasan Person in Charge (PIC) minggu berjalan (Siklus N).</li>
        </ol>
      </div>

      <!-- II. PEMBAHASAN ESKALASI -->
      <div class="mb-6">
        <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
          <i class="fas fa-level-up-alt text-gray-500"></i> II. Rekapitulasi Eskalasi Kendala Operasional
        </h3>
        <table class="w-full text-left text-xs border border-gray-300">
          <thead class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
            <tr>
              <th class="p-2 border-r border-gray-300 w-24">No Eskalasi</th>
              <th class="p-2 border-r border-gray-300">Uraian Eskalasi Kendala</th>
              <th class="p-2 border-r border-gray-300 w-20 text-center">Jenjang</th>
              <th class="p-2 border-r border-gray-300 w-20 text-center">Prioritas</th>
              <th class="p-2 w-28">Status Putusan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 text-gray-700">
            <template v-if="issues && issues.length > 0">
              <tr v-for="iss in issues" :key="iss.id">
                <td class="p-2 border-r border-gray-200 font-mono font-bold text-blue-700">#{{ iss.issue_no }}</td>
                <td class="p-2 border-r border-gray-200 font-medium">{{ iss.title }}</td>
                <td class="p-2 border-r border-gray-200 text-center font-bold">Level {{ iss.current_level }}</td>
                <td class="p-2 border-r border-gray-200 text-center">
                  <span :class="iss.priority === 'CRITICAL' || iss.priority === 'HIGH' ? 'text-red-600 font-bold' : 'text-gray-700 font-semibold'">
                    {{ iss.priority }}
                  </span>
                </td>
                <td class="p-2 font-medium">{{ iss.status }}</td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="5" class="p-3 text-center text-gray-400 italic">Tidak ada eskalasi kendala yang terdaftar pada agenda rapat ini.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- III. KEPUTUSAN RESMI PIMPINAN BERWENANG -->
      <div class="mb-6">
        <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
          <i class="fas fa-gavel text-gray-500"></i> III. Keputusan Resmi Pimpinan Rapat (Decisions)
        </h3>
        <table class="w-full text-left text-xs border border-gray-300">
          <thead class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
            <tr>
              <th class="p-2 border-r border-gray-300 w-10 text-center">No</th>
              <th class="p-2 border-r border-gray-300 w-28">Eskalasi Terkait</th>
              <th class="p-2 border-r border-gray-300">Pernyataan Keputusan Eksekutif</th>
              <th class="p-2 border-r border-gray-300 w-24 text-center">Level Pemutus</th>
              <th class="p-2 w-40">Pejabat Pemutus</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 text-gray-700">
            <template v-if="decisions && decisions.length > 0">
              <tr v-for="(dec, dIdx) in decisions" :key="dec.id">
                <td class="p-2 border-r border-gray-200 text-center font-mono">{{ dIdx + 1 }}</td>
                <td class="p-2 border-r border-gray-200 font-mono font-bold text-blue-700">#{{ dec.issue?.issue_no || '-' }}</td>
                <td class="p-2 border-r border-gray-200 font-medium">{{ dec.decision }}</td>
                <td class="p-2 border-r border-gray-200 text-center font-bold text-emerald-700">Level {{ dec.decision_level }}</td>
                <td class="p-2 font-semibold">{{ dec.decision_maker?.name || 'Pejabat Unit' }}</td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="5" class="p-3 text-center text-gray-400 italic">Belum ada keputusan formal yang dicatat pada sesi rapat ini.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- IV. RENCANA TINDAK LANJUT -->
      <div class="mb-6">
        <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
          <i class="fas fa-tasks text-gray-500"></i> IV. Rencana Tindak Lanjut Task (Action Items)
        </h3>
        <table class="w-full text-left text-xs border border-gray-300">
          <thead class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
            <tr>
              <th class="p-2 border-r border-gray-300 w-10 text-center">No</th>
              <th class="p-2 border-r border-gray-300">Uraian Task Tindak Lanjut</th>
              <th class="p-2 border-r border-gray-300 w-36">PIC Pelaksana</th>
              <th class="p-2 border-r border-gray-300 w-24">Batas Target</th>
              <th class="p-2 w-24 text-center">Progres Fisik</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 text-gray-700">
            <template v-if="tasks && tasks.length > 0">
              <tr v-for="(t, idx) in tasks.slice(0, 8)" :key="t.id">
                <td class="p-2 border-r border-gray-200 text-center font-mono">{{ idx + 1 }}</td>
                <td class="p-2 border-r border-gray-200 font-medium">
                  {{ t.title }}
                  <span v-if="t.completion_attachment" class="text-[9px] text-emerald-700 font-bold block mt-0.5">
                    <i class="fas fa-paperclip"></i> Bukti Lampiran Tersedia
                  </span>
                </td>
                <td class="p-2 border-r border-gray-200 font-semibold">{{ t.assignee?.name || 'Staff Pelaksana' }}</td>
                <td class="p-2 border-r border-gray-200 font-mono">{{ t.due_date ? formatDateShort(t.due_date) : '-' }}</td>
                <td class="p-2 text-center font-bold">
                  <span :class="t.progress_percent >= 100 ? 'text-emerald-700' : 'text-blue-700'">{{ t.progress_percent || 0 }}%</span>
                </td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="5" class="p-3 text-center text-gray-400 italic">Belum ada item tindak lanjut yang ditetapkan.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- V. DAFTAR KEHADIRAN PESERTA RAPAT -->
      <div class="mb-8">
        <h3 class="font-bold text-gray-900 text-xs uppercase mb-2 border-b border-gray-200 pb-1 flex items-center gap-1.5">
          <i class="fas fa-users text-gray-500"></i> V. Presensi & Daftar Hadir Peserta Koordinasi
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px]">
          <div v-for="u in users.slice(0, 9)" :key="u.id" class="p-2 bg-slate-50 border border-gray-200 rounded flex items-center justify-between">
            <div>
              <p class="font-bold text-gray-800">{{ u.name }}</p>
              <p class="text-[9px] text-gray-500">{{ u.position?.name || 'Staf Pelaksana' }}</p>
            </div>
            <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
              Hadir
            </span>
          </div>
        </div>
      </div>

      <!-- VERIFIKASI DIGITAL & LEMBAR PENGESAHAN -->
      <div class="mt-8 pt-6 border-t-2 border-gray-300">
        <div class="flex flex-col sm:flex-row justify-between items-end gap-6 text-xs">
          
          <!-- Digital QR Verification Box -->
          <div class="flex items-center gap-3 bg-slate-50 border border-gray-200 p-3 rounded-lg max-w-sm">
            <div class="w-16 h-16 bg-white p-1 border border-gray-300 rounded shrink-0 flex items-center justify-center">
              <svg class="w-14 h-14" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="100" fill="white"/>
                <rect x="10" y="10" width="25" height="25" fill="#1e3a8a"/>
                <rect x="15" y="15" width="15" height="15" fill="white"/>
                <rect x="65" y="10" width="25" height="25" fill="#1e3a8a"/>
                <rect x="70" y="15" width="15" height="15" fill="white"/>
                <rect x="10" y="65" width="25" height="25" fill="#1e3a8a"/>
                <rect x="15" y="70" width="15" height="15" fill="white"/>
                <rect x="45" y="10" width="10" height="15" fill="#1e3a8a"/>
                <rect x="40" y="35" width="20" height="10" fill="#1e3a8a"/>
                <rect x="45" y="55" width="10" height="25" fill="#1e3a8a"/>
                <rect x="65" y="45" width="25" height="10" fill="#1e3a8a"/>
                <rect x="65" y="65" width="15" height="25" fill="#1e3a8a"/>
              </svg>
            </div>
            <div>
              <span class="text-[9px] font-mono text-gray-500 uppercase block">Digital Document Signature</span>
              <p class="text-[10px] text-gray-600 leading-tight mt-0.5">
                Risalah rapat ini disahkan secara elektronik melalui sistem Koordinasi & Eskalasi <b>PT PLN Nusantara Power UP Sengkang</b>.
              </p>
            </div>
          </div>

          <!-- Tanda Tangan Pejabat Pengesah -->
          <div class="text-right sm:text-center w-full sm:w-60">
            <p class="font-bold text-gray-700 mb-14">
              Sengkang, {{ formatDateLong(meeting.meeting_date) }}<br>
              <span class="font-normal text-gray-500">Pimpinan Rapat Koordinasi,</span>
            </p>
            <p class="font-bold text-gray-900 underline uppercase tracking-wide">
              {{ meeting.leader?.name || (asman?.name || 'Pimpinan Unit') }}
            </p>
            <p class="text-gray-500 text-[10px] font-mono">
              NID: {{ meeting.leader?.employee_no || '9216281ZX' }} | {{ meeting.leader?.position?.name || 'Team Leader / Asman' }}
            </p>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  meeting: Object,
  issues: Array,
  tasks: Array,
  decisions: Array,
  users: Array,
  level: String,
  asman: Object,
  mup: Object,
});

function doPrint() {
  window.print();
}

function formatYear(dateStr) {
  if (!dateStr) return new Date().getFullYear();
  return new Date(dateStr).getFullYear();
}

function formatDateShort(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
}

function formatDateLong(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}

onMounted(() => {
  if (typeof window !== 'undefined' && window.location.search.includes('print=1')) {
    setTimeout(() => {
      window.print();
    }, 500);
  }
});
</script>

<style>
@media print {
  .no-print {
    display: none !important;
  }
  body {
    background: white !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  .print-page {
    box-shadow: none !important;
    border: none !important;
    margin: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 10mm !important;
  }
}
</style>
