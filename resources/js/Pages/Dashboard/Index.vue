<template>
  <AppLayout title="Dashboard">
    <!-- ==================== HEADER BANNER & SIKLUS FILTER ==================== -->
    <div class="bg-[#0f2744] text-white p-5 rounded-2xl shadow-md border border-slate-700 relative overflow-hidden mb-6">
      <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="bg-blue-600/80 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
              {{ roleName }} Workspace
            </span>
            <span class="text-[11px] text-emerald-400 font-medium flex items-center gap-1">
              <i class="fas fa-circle text-emerald-400 text-[8px] animate-pulse"></i> UP Sengkang Monitoring Real-Time
            </span>
          </div>
          <h2 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">
            DASHBOARD MONITORING & OPERASIONAL {{ roleName }}
          </h2>
          <p class="text-xs text-sky-200 mt-1 font-medium">
            Monitoring terpadu tugas staf, evaluasi isu seksi, dan progres pekerjaan mingguan
          </p>
        </div>
        
        <!-- Siklus Koordinasi Selector -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 bg-slate-800/80 p-2.5 rounded-xl border border-slate-600/60 shrink-0">
          <div class="flex items-center gap-1.5 text-xs text-slate-300 font-medium mr-1">
            <i class="far fa-calendar-alt text-sky-400"></i>
            <span>Siklus:</span>
            <span class="bg-blue-500/20 text-sky-300 font-mono font-bold px-2 py-0.5 rounded text-[11px] border border-sky-400/30">
              W{{ manajemenTugas?.minggu_berjalan }}
            </span>
          </div>
          <div class="flex items-center gap-1">
            <button 
              type="button" 
              @click="setSiklus('semua')" 
              :class="siklusClass('semua')"
            >
              Semua
            </button>
            <button 
              type="button" 
              @click="setSiklus('N')" 
              :class="siklusClass('N')"
            >
              Minggu N
            </button>
            <button 
              type="button" 
              @click="setSiklus('N-1')" 
              :class="siklusClass('N-1')"
            >
              Minggu N-1
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== 5 CARD KPI ==================== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
      <!-- Card 1: Total Pekerjaan -->
      <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
        <p class="text-gray-500 text-xs font-medium mb-1">Total Pekerjaan</p>
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ kpiData?.total_pekerjaan || 0 }}</div>
        <p class="text-emerald-600 text-[11px] font-bold flex items-center gap-1">
          <i class="fas fa-tasks"></i> Terdata
        </p>
      </div>

      <!-- Card 2: On Progress -->
      <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
        <p class="text-gray-500 text-xs font-medium mb-1">On Progress</p>
        <div class="text-3xl font-bold text-blue-600 mb-1">{{ kpiData?.on_progress || 0 }}</div>
        <p class="text-gray-400 text-[11px] font-medium">Dalam pengerjaan</p>
      </div>

      <!-- Card 3: Selesai -->
      <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
        <p class="text-gray-500 text-xs font-medium mb-1">Selesai</p>
        <div class="text-3xl font-bold text-emerald-600 mb-1">{{ kpiData?.selesai || 0 }}</div>
        <p class="text-emerald-600 text-[11px] font-bold flex items-center gap-1">
          <i class="fas fa-check-circle"></i> Tuntas
        </p>
      </div>

      <!-- Card 4: Terlambat -->
      <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
        <p class="text-gray-500 text-xs font-medium mb-1">Terlambat (Overdue)</p>
        <div class="text-3xl font-bold text-rose-600 mb-1">{{ kpiData?.terlambat || 0 }}</div>
        <p class="text-rose-500 text-[11px] font-bold flex items-center gap-1">
          <i class="fas fa-exclamation-circle"></i> Butuh evaluasi
        </p>
      </div>

      <!-- Card 5: Issue Saya -->
      <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
        <p class="text-gray-500 text-xs font-medium mb-1">Issue Terkait</p>
        <div class="text-3xl font-bold text-amber-600 mb-1">{{ kpiData?.issue_saya || 0 }}</div>
        <p class="text-amber-600 text-[11px] font-bold flex items-center gap-1">
          <i class="fas fa-level-up-alt"></i> Aktif
        </p>
      </div>
    </div>

    <!-- ==================== RINGKASAN STATUS & PRIORITAS ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      
      <!-- Status Pekerjaan Breakdown -->
      <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
        <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
          <i class="fas fa-chart-pie text-blue-600"></i> Status Pekerjaan
        </h3>
        
        <div class="space-y-4 my-auto">
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span class="text-emerald-700">Selesai ({{ kpiData?.selesai || 0 }})</span>
              <span>{{ percent(kpiData?.selesai, kpiData?.total_pekerjaan) }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
              <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" :style="{ width: percent(kpiData?.selesai, kpiData?.total_pekerjaan) + '%' }"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span class="text-blue-700">Dalam Pengerjaan ({{ kpiData?.on_progress || 0 }})</span>
              <span>{{ percent(kpiData?.on_progress, kpiData?.total_pekerjaan) }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
              <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" :style="{ width: percent(kpiData?.on_progress, kpiData?.total_pekerjaan) + '%' }"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span class="text-rose-700">Terlambat ({{ kpiData?.terlambat || 0 }})</span>
              <span>{{ percent(kpiData?.terlambat, kpiData?.total_pekerjaan) }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
              <div class="bg-rose-500 h-2.5 rounded-full transition-all duration-500" :style="{ width: percent(kpiData?.terlambat, kpiData?.total_pekerjaan) + '%' }"></div>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-[11px] text-gray-500">
          <span>Total: <strong>{{ kpiData?.total_pekerjaan || 0 }} Pekerjaan</strong></span>
          <Link href="/pekerjaan" class="text-blue-600 font-semibold hover:underline">Lihat Semua &rarr;</Link>
        </div>
      </div>

      <!-- Prioritas Issue Breakdown -->
      <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
        <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
          <i class="fas fa-signal text-amber-500"></i> Tingkat Prioritas Isu
        </h3>

        <div class="space-y-4 my-auto">
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span class="text-rose-700">Tinggi / Kritis ({{ kpiData?.chart_priority?.high || 0 }})</span>
              <span class="text-rose-600 font-bold">Prioritas 1</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
              <div class="bg-rose-600 h-2.5 rounded-full" :style="{ width: percent(kpiData?.chart_priority?.high, (kpiData?.chart_priority?.high || 0) + (kpiData?.chart_priority?.medium || 0) + (kpiData?.chart_priority?.low || 0)) + '%' }"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span class="text-amber-700">Sedang ({{ kpiData?.chart_priority?.medium || 0 }})</span>
              <span class="text-amber-600 font-bold">Prioritas 2</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
              <div class="bg-amber-500 h-2.5 rounded-full" :style="{ width: percent(kpiData?.chart_priority?.medium, (kpiData?.chart_priority?.high || 0) + (kpiData?.chart_priority?.medium || 0) + (kpiData?.chart_priority?.low || 0)) + '%' }"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span class="text-emerald-700">Rendah ({{ kpiData?.chart_priority?.low || 0 }})</span>
              <span class="text-emerald-600 font-bold">Prioritas 3</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
              <div class="bg-emerald-500 h-2.5 rounded-full" :style="{ width: percent(kpiData?.chart_priority?.low, (kpiData?.chart_priority?.high || 0) + (kpiData?.chart_priority?.medium || 0) + (kpiData?.chart_priority?.low || 0)) + '%' }"></div>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-[11px] text-gray-500">
          <span>Eskalasi Terkelola</span>
          <Link href="/issues" class="text-blue-600 font-semibold hover:underline">Detail Isu &rarr;</Link>
        </div>
      </div>

      <!-- Quick Action Shortcuts -->
      <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
        <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
          <i class="fas fa-bolt text-yellow-500"></i> Aksi Cepat & Navigasi
        </h3>

        <div class="space-y-2.5">
          <Link href="/pekerjaan" class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:border-blue-300 hover:bg-blue-50/50 transition">
            <div class="flex items-center gap-2.5">
              <span class="w-7 h-7 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                <i class="fas fa-plus"></i>
              </span>
              <span class="text-xs font-bold text-gray-700">Kelola Pekerjaan / Task</span>
            </div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
          </Link>

          <Link href="/issues" class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:border-amber-300 hover:bg-amber-50/50 transition">
            <div class="flex items-center gap-2.5">
              <span class="w-7 h-7 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">
                <i class="fas fa-bullhorn"></i>
              </span>
              <span class="text-xs font-bold text-gray-700">Lapor Isu & Kendala</span>
            </div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
          </Link>

          <Link href="/rapat" class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/50 transition">
            <div class="flex items-center gap-2.5">
              <span class="w-7 h-7 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                <i class="far fa-calendar-check"></i>
              </span>
              <span class="text-xs font-bold text-gray-700">Rapat & Workspace Mingguan</span>
            </div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
          </Link>
        </div>

        <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400 flex items-center gap-1.5">
          <i class="fas fa-info-circle text-blue-500"></i>
          <span>Data tersinkronisasi otomatis dengan server</span>
        </div>
      </div>

    </div>

    <!-- ==================== TABEL SPESIFIK ROLE ==================== -->

    <!-- 1. JIKA ROLE STAFF: TAMPILKAN DAFTAR TUGAS SAYA -->
    <div v-if="roleName === 'STAFF'" class="bg-white rounded-xl border border-gray-100 shadow-xs mb-6 overflow-hidden">
      <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-bold text-sm text-gray-800">Daftar Pekerjaan Saya</h3>
          <p class="text-xs text-gray-400">Pekerjaan operasional yang ditugaskan kepada Anda</p>
        </div>
        <Link href="/pekerjaan" class="text-xs font-bold text-blue-600 hover:underline">
          Buka Modul Task &rarr;
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
            <tr>
              <th class="px-6 py-3">Judul Pekerjaan</th>
              <th class="px-6 py-3">Tenggat Waktu</th>
              <th class="px-6 py-3">Progres</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="t in myTasks" :key="t.id" class="hover:bg-slate-50/50">
              <td class="px-6 py-3 font-bold text-gray-800">{{ t.title }}</td>
              <td class="px-6 py-3 font-mono text-gray-600">{{ t.due_date }}</td>
              <td class="px-6 py-3 w-40">
                <div class="flex items-center gap-2">
                  <div class="w-24 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" :style="{ width: t.progress_percent + '%' }"></div>
                  </div>
                  <span class="font-bold text-[11px]">{{ t.progress_percent }}%</span>
                </div>
              </td>
              <td class="px-6 py-3">
                <span :class="statusBadge(t.calculated_status || t.status)">
                  {{ t.calculated_status || t.status }}
                </span>
              </td>
              <td class="px-6 py-3 text-center">
                <Link href="/pekerjaan" class="text-blue-600 hover:underline font-bold text-[11px]">
                  Perbarui Progres
                </Link>
              </td>
            </tr>
            <tr v-if="!myTasks || myTasks.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada penugasan pekerjaan untuk Anda.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. JIKA ROLE TL: DAFTAR ISU PERLU DITINJAU TL -->
    <div v-else-if="roleName === 'TL'" class="bg-white rounded-xl border border-gray-100 shadow-xs mb-6 overflow-hidden">
      <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-bold text-sm text-gray-800 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Isu Menunggu Keputusan TL
          </h3>
          <p class="text-xs text-gray-400">Kendala staf seksi yang memerlukan keputusan teknis atau eskalasi</p>
        </div>
        <Link href="/issues" class="text-xs font-bold text-blue-600 hover:underline">
          Lihat Semua Isu &rarr;
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
            <tr>
              <th class="px-6 py-3">No. Isu</th>
              <th class="px-6 py-3">Judul Isu</th>
              <th class="px-6 py-3">Pelapor</th>
              <th class="px-6 py-3">Prioritas</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-center">Tindakan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="iss in issuesUnderReview" :key="iss.id" class="hover:bg-slate-50/50">
              <td class="px-6 py-3 font-mono font-bold text-blue-600">#{{ iss.issue_no }}</td>
              <td class="px-6 py-3 font-bold text-gray-800">{{ iss.title }}</td>
              <td class="px-6 py-3 text-gray-600">{{ iss.reporter?.name || '-' }}</td>
              <td class="px-6 py-3">
                <span :class="priorityBadge(iss.priority)">{{ iss.priority }}</span>
              </td>
              <td class="px-6 py-3">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  {{ iss.status }}
                </span>
              </td>
              <td class="px-6 py-3 text-center">
                <Link :href="`/issues/${iss.id}`" class="px-2.5 py-1 bg-blue-600 text-white rounded text-[11px] font-bold shadow-xs hover:bg-blue-700 transition">
                  Tinjau & Putuskan
                </Link>
              </td>
            </tr>
            <tr v-if="!issuesUnderReview || issuesUnderReview.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-gray-400">Tidak ada isu aktif yang menunggu keputusan TL saat ini.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. JIKA ROLE ASMAN: DAFTAR ESKALASI MASUK KE ASMAN -->
    <div v-else-if="roleName === 'ASMAN'" class="bg-white rounded-xl border border-gray-100 shadow-xs mb-6 overflow-hidden">
      <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-bold text-sm text-gray-800 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Eskalasi Masuk ke Asisten Manajer
          </h3>
          <p class="text-xs text-gray-400">Isu teknis berbiaya / lintas seksi yang dinaikkan oleh TL</p>
        </div>
        <Link href="/issues" class="text-xs font-bold text-amber-600 hover:underline">
          Kelola Eskalasi &rarr;
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
            <tr>
              <th class="px-6 py-3">No. Isu</th>
              <th class="px-6 py-3">Judul Isu</th>
              <th class="px-6 py-3">Pelapor Asal</th>
              <th class="px-6 py-3">Prioritas</th>
              <th class="px-6 py-3 text-center">Aksi ASMAN</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="esc in escalatedIssues" :key="esc.id" class="hover:bg-slate-50/50">
              <td class="px-6 py-3 font-mono font-bold text-amber-600">#{{ esc.issue_no }}</td>
              <td class="px-6 py-3 font-bold text-gray-800">{{ esc.title }}</td>
              <td class="px-6 py-3 text-gray-600">{{ esc.reporter?.name || '-' }}</td>
              <td class="px-6 py-3">
                <span :class="priorityBadge(esc.priority)">{{ esc.priority }}</span>
              </td>
              <td class="px-6 py-3 text-center">
                <Link :href="`/issues/${esc.id}`" class="px-2.5 py-1 bg-amber-600 text-white rounded text-[11px] font-bold shadow-xs hover:bg-amber-700 transition">
                  Beri Putusan ASMAN
                </Link>
              </td>
            </tr>
            <tr v-if="!escalatedIssues || escalatedIssues.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-400">Tidak ada eskalasi pending untuk tingkat Asisten Manajer.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 4. JIKA ROLE MUP: DAFTAR KENDALA KRITIS UNIT -->
    <div v-else-if="roleName === 'MUP'" class="bg-white rounded-xl border border-gray-100 shadow-xs mb-6 overflow-hidden">
      <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-bold text-sm text-gray-800 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span> Kendala Kritis & Strategis Unit
          </h3>
          <p class="text-xs text-gray-400">Isu dengan dampak operasi tinggi pada keandalan sistem PLTGU Sengkang</p>
        </div>
        <Link href="/issues" class="text-xs font-bold text-purple-600 hover:underline">
          Semua Isu Unit &rarr;
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
            <tr>
              <th class="px-6 py-3">No. Isu</th>
              <th class="px-6 py-3">Judul Isu</th>
              <th class="px-6 py-3">Level Saat Ini</th>
              <th class="px-6 py-3">Prioritas</th>
              <th class="px-6 py-3 text-center">Arahan MUP</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="crit in criticalIssues" :key="crit.id" class="hover:bg-slate-50/50">
              <td class="px-6 py-3 font-mono font-bold text-purple-600">#{{ crit.issue_no }}</td>
              <td class="px-6 py-3 font-bold text-gray-800">{{ crit.title }}</td>
              <td class="px-6 py-3 font-bold text-slate-700">{{ crit.current_level }}</td>
              <td class="px-6 py-3">
                <span :class="priorityBadge(crit.priority)">{{ crit.priority }}</span>
              </td>
              <td class="px-6 py-3 text-center">
                <Link :href="`/issues/${crit.id}`" class="px-2.5 py-1 bg-purple-700 text-white rounded text-[11px] font-bold shadow-xs hover:bg-purple-800 transition">
                  Beri Putusan MUP
                </Link>
              </td>
            </tr>
            <tr v-if="!criticalIssues || criticalIssues.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-400">Tidak ada kendala kritis pada status unit saat ini.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ==================== MONITORING TUGAS TIM ==================== -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
      <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-bold text-sm text-gray-800">Aktivitas Pekerjaan Terbaru</h3>
          <p class="text-xs text-gray-400">Monitoring progres pekerjaan seluruh personil di unit UP Sengkang</p>
        </div>
        <Link href="/pekerjaan" class="text-xs font-bold text-blue-600 hover:underline">
          Buka Manajemen Task &rarr;
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
            <tr>
              <th class="px-6 py-3">Judul Pekerjaan</th>
              <th class="px-6 py-3">PIC / Pelaksana</th>
              <th class="px-6 py-3">Tenggat Waktu</th>
              <th class="px-6 py-3">Progres</th>
              <th class="px-6 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="task in tasks" :key="task.id" class="hover:bg-slate-50/50">
              <td class="px-6 py-3 font-bold text-gray-800">{{ task.title }}</td>
              <td class="px-6 py-3 text-gray-600 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 shrink-0">
                  <img :src="task.assignee?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(task.assignee?.name || 'User')}&size=60`" class="w-full h-full object-cover" />
                </div>
                <span>{{ task.assignee?.name || '-' }}</span>
              </td>
              <td class="px-6 py-3 font-mono text-gray-600">{{ task.due_date }}</td>
              <td class="px-6 py-3 w-36">
                <div class="flex items-center gap-2">
                  <div class="w-20 bg-gray-200 rounded-full h-1.5">
                    <div class="bg-blue-600 h-1.5 rounded-full" :style="{ width: task.progress_percent + '%' }"></div>
                  </div>
                  <span class="font-bold text-[10px]">{{ task.progress_percent }}%</span>
                </div>
              </td>
              <td class="px-6 py-3">
                <span :class="statusBadge(task.calculated_status || task.status)">
                  {{ task.calculated_status || task.status }}
                </span>
              </td>
            </tr>
            <tr v-if="!tasks || tasks.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat pekerjaan dalam sistem.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  roleName: String,
  kpiData: Object,
  manajemenTugas: Object,
  tasks: Array,
  issuesUnderReview: Array,
  escalatedIssues: Array,
  criticalIssues: Array,
  myTasks: Array,
  myTaskStats: Object,
});

function setSiklus(val) {
  router.get('/dashboard', { siklus: val }, { preserveState: true, preserveScroll: true });
}

function siklusClass(val) {
  const active = (props.manajemenTugas?.siklus_aktif || 'semua') === val;
  if (active) {
    return 'px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-xs transition cursor-pointer';
  }
  return 'px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-700/60 hover:bg-slate-700 text-slate-300 transition cursor-pointer';
}

function percent(count, total) {
  if (!total || total <= 0) return 0;
  return Math.round(((count || 0) / total) * 100);
}

function statusBadge(status) {
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

function priorityBadge(priority) {
  const p = String(priority || '').toUpperCase();
  if (p === 'CRITICAL' || p === 'HIGH') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300';
  }
  if (p === 'MEDIUM') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
}
</script>
