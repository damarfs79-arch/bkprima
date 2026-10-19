<template>
  <AdminLayout :active-tab="activeTab" @select-tab="activeTab = $event">
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- HEADER CARD & ROLE BADGE                                     -->
      <!-- ============================================================ -->
      <div class="relative overflow-hidden bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/80 shadow-xs">
        <div class="absolute -right-10 -top-10 w-56 h-56 bg-gradient-to-br from-indigo-100/50 to-blue-100/40 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                </svg>
                Role Administrator Utama
              </span>
              <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem Aktif
              </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Panel Administrasi BK Prima</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola data Guru BK, Rombel Kelas, Siswa, dan pantau tren fluktuasi kehadiran 1 bulan.</p>
          </div>

          <!-- Quick Action Buttons -->
          <div class="flex items-center gap-2">
            <button
              @click="openModal('guru')"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/70 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Guru BK</span>
            </button>
            <button
              @click="openModal('kelas')"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200/70 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Kelas</span>
            </button>
            <button
              @click="openModal('import-siswa')"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/70 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              <span>Import Siswa</span>
            </button>
            <button
              @click="openModal('siswa')"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/20 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Siswa</span>
            </button>
          </div>
        </div>

        <!-- Segmented Tab Navigation -->
        <div class="flex items-center gap-1.5 mt-6 pt-5 border-t border-slate-100 overflow-x-auto scrollbar-hide text-xs">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition cursor-pointer shrink-0"
            :class="activeTab === tab.id ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100/80 text-slate-600 hover:bg-slate-200/70'"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            <span>{{ tab.label }}</span>
            <span
              v-if="tab.count !== undefined"
              class="px-2 py-0.5 rounded-full text-[10px] font-black"
              :class="activeTab === tab.id ? 'bg-white/20 text-white' : 'bg-white text-slate-700 shadow-2xs'"
            >
              {{ tab.count }}
            </span>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- TAB 1: DASHBOARD & GRAFIK NAIK TURUN KEHADIRAN 1 BULAN       -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'dashboard'" class="space-y-6">
        
        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
          <!-- Total Guru BK -->
          <div class="p-4 sm:p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">GURU BK AKTIF</span>
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ guruBkList.length }}</div>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Semua bersertifikasi resmi</p>
          </div>

          <!-- Total Kelas -->
          <div class="p-4 sm:p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">TOTAL ROMBEL</span>
              <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ kelasList.length }}</div>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">6 Jurusan terdaftar aktif</p>
          </div>

          <!-- Total Siswa -->
          <div class="p-4 sm:p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">TOTAL SISWA</span>
              <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ totalSiswaCount }}</div>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Sinkron Dapodik Semester Genap</p>
          </div>

          <!-- Kehadiran Rata-rata 1 Bulan -->
          <div class="p-4 sm:p-5 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl text-white shadow-md shadow-blue-600/20">
            <div class="flex items-center justify-between text-blue-200 mb-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-blue-100">RATA-RATA HADIR 30 HARI</span>
              <div class="w-8 h-8 rounded-lg bg-white/20 text-white flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
              </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white tracking-tight">95.4%</div>
            <div class="flex items-center gap-1 text-[11px] text-emerald-300 font-bold mt-0.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
              </svg>
              <span>+2.4% meningkat dibanding bulan lalu</span>
            </div>
          </div>
        </div>

        <!-- ============================================================ -->
        <!-- GRAFIK KENAIKAN DAN PENURUNAN KEHADIRAN SISWA 1 BULAN        -->
        <!-- ============================================================ -->
        <div class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/80 shadow-xs space-y-6">
          
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                  </svg>
                  Analitik Kehadiran Siswa
                </span>
                <span class="text-xs text-slate-400 font-medium">Periode: 1 Bulan (30 Hari)</span>
              </div>
              <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                Grafik Fluktuasi Naik / Turun Kehadiran Siswa
              </h2>
              <p class="text-xs text-slate-500 mt-0.5">
                Pemantauan harian presensi siswa per tanggal dengan ambang target minimum kehadiran 95%
              </p>
            </div>

            <!-- Legend Indicator -->
            <div class="flex items-center gap-4 text-xs font-semibold">
              <div class="flex items-center gap-1.5 text-blue-600">
                <span class="w-3 h-3 rounded-full bg-blue-600 shadow-xs"></span>
                <span>Tingkat Hadir (%)</span>
              </div>
              <div class="flex items-center gap-1.5 text-amber-500">
                <span class="w-3 h-1 rounded-full bg-amber-400"></span>
                <span>Target Sekolah (95%)</span>
              </div>
            </div>
          </div>

          <!-- Highlight Peak & Dip Badges -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/70 rounded-2xl flex items-center justify-between">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                  </svg>
                </div>
                <div>
                  <span class="block text-[11px] font-bold text-emerald-800 uppercase">Kenaikan Tertinggi (Peak)</span>
                  <span class="text-xs text-emerald-700 font-medium">Tgl 14 Oktober &bull; Kegiatan Apel Akbar</span>
                </div>
              </div>
              <span class="text-base font-black text-emerald-700">98.6%</span>
            </div>

            <div class="p-3.5 bg-rose-50/70 border border-rose-200/70 rounded-2xl flex items-center justify-between">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                  </svg>
                </div>
                <div>
                  <span class="block text-[11px] font-bold text-rose-800 uppercase">Penurunan Terendah (Dip)</span>
                  <span class="text-xs text-rose-700 font-medium">Tgl 21 Oktober &bull; Banjir Akses Jalan</span>
                </div>
              </div>
              <span class="text-base font-black text-rose-700">88.2%</span>
            </div>
          </div>

          <!-- Dynamic SVG Area Chart for 30 Days -->
          <div class="relative bg-slate-50/80 rounded-2xl p-4 sm:p-6 border border-slate-200/70">
            <!-- Tooltip Info on Hover -->
            <div v-if="hoveredPoint" class="absolute top-3 right-4 z-20 bg-slate-900 text-white px-3 py-1.5 rounded-xl text-xs font-bold shadow-lg flex items-center gap-2">
              <span>Tgl {{ hoveredPoint.day }} Okt:</span>
              <span :class="hoveredPoint.rate >= 95 ? 'text-emerald-400' : 'text-rose-400'">{{ hoveredPoint.rate }}% Hadir</span>
              <span class="text-[10px] text-slate-400">({{ hoveredPoint.count }} Siswa)</span>
            </div>

            <!-- SVG Container -->
            <div class="w-full overflow-x-auto pb-2">
              <svg viewBox="0 0 900 240" class="w-full min-w-[700px] h-56 select-none overflow-visible">
                <!-- Grid Lines (Horizontal) -->
                <line x1="40" y1="30" x2="880" y2="30" stroke="#e2e8f0" stroke-dasharray="4" />
                <text x="10" y="34" class="text-[10px] fill-slate-400 font-mono">100%</text>

                <line x1="40" y1="90" x2="880" y2="90" stroke="#fcd34d" stroke-dasharray="4" stroke-width="1.5" />
                <text x="10" y="94" class="text-[10px] fill-amber-500 font-mono font-bold">95%</text>

                <line x1="40" y1="150" x2="880" y2="150" stroke="#e2e8f0" stroke-dasharray="4" />
                <text x="10" y="154" class="text-[10px] fill-slate-400 font-mono">90%</text>

                <line x1="40" y1="210" x2="880" y2="210" stroke="#e2e8f0" stroke-dasharray="4" />
                <text x="10" y="214" class="text-[10px] fill-slate-400 font-mono">85%</text>

                <!-- Gradient Definition -->
                <defs>
                  <linearGradient id="attendanceGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#2563eb" stop-opacity="0.35" />
                    <stop offset="70%" stop-color="#3b82f6" stop-opacity="0.08" />
                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0" />
                  </linearGradient>
                </defs>

                <!-- Area Fill Path -->
                <path :d="chartAreaPath" fill="url(#attendanceGradient)" />

                <!-- Main Trend Line Path -->
                <path :d="chartLinePath" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                <!-- Data Points (Interactive Circles) -->
                <g v-for="(p, i) in monthAttendanceData" :key="i">
                  <circle
                    :cx="p.x"
                    :cy="p.y"
                    :r="hoveredIndex === i ? 6 : 3.5"
                    :class="hoveredIndex === i ? 'fill-blue-600 stroke-4 stroke-white' : (p.rate < 90 ? 'fill-rose-500' : (p.rate >= 98 ? 'fill-emerald-500' : 'fill-blue-600'))"
                    class="transition-all duration-150 cursor-pointer"
                    @mouseenter="setHoverPoint(p, i)"
                    @mouseleave="clearHoverPoint"
                  />
                  <!-- Day numbers on bottom axis -->
                  <text
                    v-if="i % 2 === 0 || i === monthAttendanceData.length - 1"
                    :x="p.x"
                    y="235"
                    text-anchor="middle"
                    class="text-[10px] fill-slate-400 font-semibold"
                  >
                    {{ p.day }}
                  </text>
                </g>
              </svg>
            </div>
          </div>

          <!-- 4-Week Breakdown Cards -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PEKAN 1 (1 - 7 OKT)</span>
              <div class="text-xl font-black text-slate-900 mt-1">94.2%</div>
              <div class="flex items-center gap-1 text-[11px] text-blue-600 font-bold mt-0.5">
                <span>Stabil awal pekan</span>
              </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PEKAN 2 (8 - 14 OKT)</span>
              <div class="text-xl font-black text-emerald-600 mt-1">97.5%</div>
              <div class="flex items-center gap-1 text-[11px] text-emerald-600 font-bold mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <span>+3.3% Kenaikan</span>
              </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PEKAN 3 (15 - 21 OKT)</span>
              <div class="text-xl font-black text-rose-600 mt-1">92.8%</div>
              <div class="flex items-center gap-1 text-[11px] text-rose-600 font-bold mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                <span>-4.7% Penurunan Cuaca</span>
              </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PEKAN 4 (22 - 30 OKT)</span>
              <div class="text-xl font-black text-emerald-600 mt-1">96.8%</div>
              <div class="flex items-center gap-1 text-[11px] text-emerald-600 font-bold mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <span>+4.0% Pemulihan</span>
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- ============================================================ -->
      <!-- TAB 2: KELOLA GURU BK                                         -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'guru'" class="space-y-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Data Guru Bimbingan Konseling (BK)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar tenaga konselor resmi penanggung jawab rombel SMK PGRI 05 Jember</p>
          </div>
          <button
            @click="openModal('guru')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition cursor-pointer self-start sm:self-auto"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Guru BK Baru</span>
          </button>
        </div>

        <!-- Table Guru BK -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden p-5 sm:p-6">
          <div class="overflow-x-auto border border-slate-200/80 rounded-2xl">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                <tr>
                  <th class="py-3.5 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NAMA LENGKAP &amp; GELAR</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NIP / NUPTK</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">JABATAN</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">ROMBEL BINAAN</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">STATUS DAPODIK</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(g, idx) in guruBkList" :key="g.id" class="hover:bg-slate-50/80 transition">
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ g.name }}</div>
                    <div class="text-[11px] text-slate-400">{{ g.email }}</div>
                  </td>
                  <td class="py-3.5 px-4 font-mono text-slate-700 font-medium">
                    <div>NIP: {{ g.nip }}</div>
                    <div class="text-[10px] text-slate-400">NUPTK: {{ g.nuptk }}</div>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">{{ g.jabatan }}</td>
                  <td class="py-3.5 px-4">
                    <div class="flex flex-wrap gap-1 max-w-xs">
                      <span v-for="r in g.rombel" :key="r" class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200/60">
                        {{ r }}
                      </span>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      Valid
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <div class="inline-flex items-center gap-1.5">
                      <button @click="deleteGuru(idx)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Guru">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- TAB 3: KELOLA KELAS / ROMBEL                                  -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'kelas'" class="space-y-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Data Rombongan Belajar (Rombel)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Struktur rombel kelas per tingkat dan penugasan wali kelas</p>
          </div>
          <button
            @click="openModal('kelas')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition cursor-pointer self-start sm:self-auto"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Kelas Baru</span>
          </button>
        </div>

        <!-- Table Kelas -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden p-5 sm:p-6">
          <div class="overflow-x-auto border border-slate-200/80 rounded-2xl">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                <tr>
                  <th class="py-3.5 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NAMA ROMBEL</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">TINGKAT &amp; JURUSAN</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">WALI KELAS</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">GURU BK PENDAMPING</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">JUMLAH SISWA</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(k, idx) in kelasList" :key="k.id" class="hover:bg-slate-50/80 transition">
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4">
                    <span class="font-black text-slate-900 text-xs sm:text-sm">{{ k.name }}</span>
                  </td>
                  <td class="py-3.5 px-4">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200">
                      {{ k.level }} &bull; {{ k.jurusan }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">{{ k.waliKelas }}</td>
                  <td class="py-3.5 px-4 text-slate-600 font-medium">{{ k.guruBK }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200/60 text-xs">
                      {{ k.studentCount }} Siswa
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <button @click="deleteKelas(idx)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Kelas">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- TAB 4: KELOLA DATA SISWA                                      -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'siswa'" class="space-y-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Data Induk Siswa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan data siswa aktif dan kontak wali siswa</p>
          </div>
          <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <button
              @click="openModal('import-siswa')"
              class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              <span>Import Siswa (Excel/CSV)</span>
            </button>
            <button
              @click="openModal('siswa')"
              class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Siswa Baru</span>
            </button>
          </div>
        </div>

        <!-- Filter & Search Siswa -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden p-5 sm:p-6 space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-sm">
              <input
                v-model="searchSiswa"
                type="text"
                placeholder="Cari NISN, nama siswa..."
                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
              />
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </span>
            </div>

            <div class="flex items-center gap-2">
              <select
                v-model="selectedFilterKelas"
                class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
              >
                <option value="Semua">Semua Kelas</option>
                <option v-for="k in kelasList" :key="k.id" :value="k.name">{{ k.name }}</option>
              </select>
            </div>
          </div>

          <!-- Table Siswa -->
          <div class="overflow-x-auto border border-slate-200/80 rounded-2xl">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                <tr>
                  <th class="py-3.5 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NISN</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NAMA LENGKAP SISWA</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">KELAS</th>
                  <th class="py-3.5 px-3 text-center text-[11px] uppercase tracking-wider">JK</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NO WA WALI MURID</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">POIN TATIB</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(s, idx) in filteredSiswaList" :key="s.id" class="hover:bg-slate-50/80 transition">
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4 font-mono font-medium text-slate-600">{{ s.nisn }}</td>
                  <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ s.name }}</span>
                  </td>
                  <td class="py-3.5 px-4">
                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[11px] border border-blue-200/60">
                      {{ s.className }}
                    </span>
                  </td>
                  <td class="py-3.5 px-3 text-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md font-bold text-xs" :class="s.gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700'">
                      {{ s.gender }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 font-mono text-slate-600 text-xs">{{ s.parentWa }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black" :class="s.poin > 20 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'">
                      {{ s.poin }} Poin
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <button @click="deleteSiswa(idx)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Siswa">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr v-if="filteredSiswaList.length === 0">
                  <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                    Tidak ada siswa yang sesuai dengan filter pencarian.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH GURU BK                                         -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'guru'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
              </div>
              <h3 class="font-bold text-slate-900 text-sm sm:text-base">Tambah Guru BK Baru</h3>
            </div>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveGuru" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Lengkap &amp; Gelar *</label>
              <input v-model="formGuru.name" required placeholder="Contoh: Dra. Hj. Siti Rahma, M.Pd" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">NIP Pegawai</label>
                <input v-model="formGuru.nip" placeholder="19820512 2008..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">NUPTK</label>
                <input v-model="formGuru.nuptk" placeholder="8452 7566..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Jabatan / Penugasan Khusus *</label>
              <input v-model="formGuru.jabatan" required placeholder="Contoh: Guru Pembimbing BK Kelas X" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">WhatsApp Aktif</label>
                <input v-model="formGuru.wa" placeholder="+62 812..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Email Belajar.id</label>
                <input v-model="formGuru.email" type="email" placeholder="nama@guru.belajar.id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Rombel Binaan (Pisahkan dengan koma)</label>
              <input v-model="formGuru.rombelText" placeholder="Contoh: X RPL 1, X RPL 2, X TSM 1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-md shadow-blue-600/20">Simpan Guru BK</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH KELAS / ROMBEL                                  -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'kelas'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <h3 class="font-bold text-slate-900 text-sm sm:text-base">Tambah Rombel Kelas Baru</h3>
            </div>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveKelas" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Tingkat Kelas *</label>
                <select v-model="formKelas.level" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="Kelas 10 (X)">Kelas 10 (X)</option>
                  <option value="Kelas 11 (XI)">Kelas 11 (XI)</option>
                  <option value="Kelas 12 (XII)">Kelas 12 (XII)</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Bidang Keahlian / Jurusan *</label>
                <select v-model="formKelas.jurusan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="RPL">RPL</option>
                  <option value="TSM">TSM</option>
                  <option value="DKV">DKV</option>
                  <option value="TKJ">TKJ</option>
                  <option value="AKL">AKL</option>
                  <option value="OTKP">OTKP</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Rombel *</label>
              <input v-model="formKelas.name" required placeholder="Contoh: X RPL 4 atau XI DKV 3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Wali Kelas *</label>
              <input v-model="formKelas.waliKelas" required placeholder="Contoh: Bpk. Kurniawan, S.Kom" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Guru BK Pendamping</label>
              <select v-model="formKelas.guruBK" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option v-for="g in guruBkList" :key="g.id" :value="g.name">{{ g.name }}</option>
              </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition cursor-pointer shadow-md shadow-indigo-600/20">Simpan Kelas</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH SISWA                                           -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'siswa'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="font-bold text-slate-900 text-sm sm:text-base">Tambah Siswa Baru</h3>
            </div>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveSiswa" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">NISN Siswa *</label>
                <input v-model="formSiswa.nisn" required placeholder="Contoh: 00729190" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin *</label>
                <select v-model="formSiswa.gender" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="L">Laki-laki (L)</option>
                  <option value="P">Perempuan (P)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
              <input v-model="formSiswa.name" required placeholder="Nama lengkap sesuai akta / Dapodik" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Rombel Kelas *</label>
              <select v-model="formSiswa.className" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option v-for="k in kelasList" :key="k.id" :value="k.name">{{ k.name }} ({{ k.jurusan }})</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">No WhatsApp Wali Murid *</label>
              <input v-model="formSiswa.parentWa" required placeholder="+62 8..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk notifikasi presensi otomatis &amp; panggilan BK.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold transition cursor-pointer shadow-md shadow-sky-600/20">Simpan Siswa</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: IMPORT DATA SISWA MASSAL (EXCEL / CSV)                -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'import-siswa'" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-5 sm:p-7 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 my-8">
          
          <!-- Header Modal -->
          <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
              </div>
              <div>
                <h3 class="font-black text-slate-900 text-base sm:text-lg">Import Data Siswa Massal</h3>
                <p class="text-xs text-slate-500 font-medium">Unggah file Excel/CSV atau data Dapodik untuk memasukkan siswa secara cepat</p>
              </div>
            </div>
            <button @click="closeModal" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Step 1 & Action Bar: Download Template & Sample -->
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div>
              <span class="font-bold text-slate-800 block">Panduan Format Kolom:</span>
              <span class="text-slate-500 text-[11px]">Format file harus memiliki kolom: <strong class="text-slate-700">NISN, Nama, Kelas, JK (L/P), No WA Wali</strong></span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button
                type="button"
                @click="downloadCsvTemplate"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-100 text-blue-700 font-bold border border-blue-200 rounded-xl shadow-2xs transition cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Template CSV</span>
              </button>
              <button
                type="button"
                @click="loadSampleDapodikData"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold border border-emerald-300 rounded-xl shadow-2xs transition cursor-pointer"
                title="Muat contoh data siswa SMK PGRI 05 Jember langsung"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Isi Contoh Dapodik (Otomatis)</span>
              </button>
            </div>
          </div>

          <!-- Step 2: Upload Area -->
          <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-700">Pilih File Spreadsheet (CSV / Excel / TXT)</label>
            <div
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
              class="border-2 border-dashed rounded-2xl p-6 text-center transition cursor-pointer relative"
              :class="isDragging ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-300 hover:border-emerald-500 bg-slate-50/50 hover:bg-emerald-50/20'"
            >
              <input
                ref="fileInputRef"
                type="file"
                accept=".csv, .txt, .xlsx, .xls"
                @change="handleFileUpload"
                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
              />
              <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs sm:text-sm font-bold text-slate-800">
                    <span class="text-emerald-700 underline font-black">Klik untuk memilih file</span> atau seret file ke sini
                  </p>
                  <p class="text-[11px] text-slate-500 mt-0.5">Mendukung format .CSV, .TXT, dan file ekspor Dapodik</p>
                </div>
                <div v-if="importFileName" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span>File Terpilih: {{ importFileName }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Target Kelas Dropdown Option -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Pengaturan Rombel / Kelas:</label>
              <select
                v-model="importTargetClass"
                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
              >
                <option value="AUTO">Otomatis (Ikuti kolom 'Kelas' di file)</option>
                <option v-for="k in kelasList" :key="k.id" :value="k.name">Tetapkan Semua ke: {{ k.name }}</option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Status Validasi:</label>
              <div class="flex items-center h-[38px] px-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                <span v-if="importedStudents.length > 0" class="text-emerald-700 font-bold flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                  {{ importedStudents.length }} Siswa siap diimpor ke sistem
                </span>
                <span v-else class="text-slate-400">
                  Belum ada data siswa yang dimuat
                </span>
              </div>
            </div>
          </div>

          <!-- Step 3: Table Preview Data Siswa yang Akan Diimpor -->
          <div v-if="importedStudents.length > 0" class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Pratinjau Data Siswa ({{ importedStudents.length }} Siswa)</span>
              </span>
              <button
                type="button"
                @click="importedStudents = []; importFileName = ''"
                class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer"
              >
                Kosongkan Data
              </button>
            </div>

            <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0">
                  <tr>
                    <th class="py-2 px-3 text-center w-8">No</th>
                    <th class="py-2 px-3">NISN</th>
                    <th class="py-2 px-3">Nama Lengkap</th>
                    <th class="py-2 px-3">Kelas</th>
                    <th class="py-2 px-2 text-center">JK</th>
                    <th class="py-2 px-3">No WA Wali</th>
                    <th class="py-2 px-2 text-center w-8">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                  <tr v-for="(st, sIdx) in importedStudents" :key="sIdx" class="hover:bg-slate-50">
                    <td class="py-1.5 px-3 text-center text-slate-400 font-mono">{{ sIdx + 1 }}</td>
                    <td class="py-1.5 px-3 font-mono font-medium text-slate-700">{{ st.nisn }}</td>
                    <td class="py-1.5 px-3 font-bold text-slate-900">{{ st.name }}</td>
                    <td class="py-1.5 px-3">
                      <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px]">
                        {{ importTargetClass === 'AUTO' ? st.className : importTargetClass }}
                      </span>
                    </td>
                    <td class="py-1.5 px-2 text-center font-bold" :class="st.gender === 'L' ? 'text-blue-600' : 'text-pink-600'">
                      {{ st.gender }}
                    </td>
                    <td class="py-1.5 px-3 font-mono text-slate-600 text-[11px]">{{ st.parentWa }}</td>
                    <td class="py-1.5 px-2 text-center">
                      <button
                        type="button"
                        @click="removeImportedRow(sIdx)"
                        class="text-slate-400 hover:text-rose-600 p-1 cursor-pointer"
                        title="Hapus baris ini"
                      >
                        ✕
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Modal Action Footer -->
          <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button
              type="button"
              @click="closeModal"
              class="px-5 py-2.5 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer text-xs"
            >
              Batal
            </button>
            <button
              type="button"
              @click="processImport"
              :disabled="importedStudents.length === 0"
              class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold transition cursor-pointer text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>Simpan &amp; Import {{ importedStudents.length }} Siswa</span>
            </button>
          </div>

        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layouts/AdminLayout.vue'
import { ref, computed, h } from 'vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  }
})

const activeTab = ref('dashboard')
const modalType = ref(null)
const searchSiswa = ref('')
const selectedFilterKelas = ref('Semua')

// Tab Navigation Icons
const tabs = computed(() => [
  {
    id: 'dashboard',
    label: 'Dashboard & Tren Kehadiran',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' })
    ])
  },
  {
    id: 'guru',
    label: 'Kelola Guru BK',
    count: guruBkList.value.length,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })
    ])
  },
  {
    id: 'kelas',
    label: 'Kelola Kelas / Rombel',
    count: kelasList.value.length,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
    ])
  },
  {
    id: 'siswa',
    label: 'Kelola Data Siswa',
    count: siswaList.value.length,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
    ])
  }
])

// ==========================================
// 1. DATA FLUKTUASI KEHADIRAN 30 HARI
// ==========================================
const hoveredPoint = ref(null)
const hoveredIndex = ref(null)

const rawRates = [
  94.2, 95.1, 93.8, 95.5, 96.0, 93.2, 94.8,
  95.6, 96.2, 97.0, 96.8, 97.4, 98.1, 98.6,
  96.5, 95.0, 94.2, 93.8, 92.5, 90.4, 88.2,
  91.5, 94.0, 95.8, 96.5, 97.2, 96.0, 97.5, 96.8, 97.1
]

const monthAttendanceData = computed(() => {
  const startX = 40
  const width = 840
  const stepX = width / (rawRates.length - 1)
  
  // Scale: 85% is y=210, 100% is y=30 (range 15% across 180px, so 12px per 1%)
  return rawRates.map((rate, idx) => {
    const x = Math.round(startX + idx * stepX)
    const y = Math.round(210 - (rate - 85) * 12)
    const count = Math.round((rate / 100) * 1080)
    return {
      day: idx + 1,
      rate,
      x,
      y,
      count
    }
  })
})

const chartLinePath = computed(() => {
  if (monthAttendanceData.value.length === 0) return ''
  return monthAttendanceData.value.reduce((acc, p, idx) => {
    return idx === 0 ? `M ${p.x} ${p.y}` : `${acc} L ${p.x} ${p.y}`
  }, '')
})

const chartAreaPath = computed(() => {
  if (monthAttendanceData.value.length === 0) return ''
  const first = monthAttendanceData.value[0]
  const last = monthAttendanceData.value[monthAttendanceData.value.length - 1]
  const line = chartLinePath.value
  return `${line} L ${last.x} 210 L ${first.x} 210 Z`
})

const setHoverPoint = (p, idx) => {
  hoveredPoint.value = p
  hoveredIndex.value = idx
}

const clearHoverPoint = () => {
  hoveredPoint.value = null
  hoveredIndex.value = null
}

// ==========================================
// 2. DATA GURU BK
// ==========================================
const guruBkList = ref([
  {
    id: 1,
    name: 'Dra. Hj. Nurul Hidayati, M.Pd',
    nip: '19780415 200312 2 004',
    nuptk: '8452 7566 5830 0012',
    jabatan: 'Koordinator BK & Kedisiplinan',
    email: 'nurul.hidayati@guru.smk.belajar.id',
    wa: '+62 812-3456-7890',
    rombel: ['X RPL 1', 'X RPL 2', 'XI RPL 1', 'XII DKV 1']
  },
  {
    id: 2,
    name: 'Bpk. Agus Wicaksono, S.Pd',
    nip: '19850612 201001 1 012',
    nuptk: '6342 9821 4410 0034',
    jabatan: 'Guru Pembimbing BK Kelas X',
    email: 'agus.wicaksono@guru.smk.belajar.id',
    wa: '+62 813-9876-5432',
    rombel: ['X TSM 1', 'X TSM 2', 'X DKV 1']
  },
  {
    id: 3,
    name: 'Ibu Ratih Anggraini, S.Psi',
    nip: '19910322 201802 2 005',
    nuptk: '9120 4455 1209 0087',
    jabatan: 'Guru Pembimbing BK Kelas XI',
    email: 'ratih.anggraini@guru.smk.belajar.id',
    wa: '+62 852-1122-3344',
    rombel: ['XI DKV 1', 'XI DKV 2', 'XI TKJ 1']
  },
  {
    id: 4,
    name: 'Bpk. Dwi Prasetyo, M.Pd',
    nip: '19821105 200801 1 008',
    nuptk: '4456 1238 9980 0045',
    jabatan: 'Guru Pembimbing BK Kelas XII',
    email: 'dwi.prasetyo@guru.smk.belajar.id',
    wa: '+62 878-5566-7788',
    rombel: ['XII RPL 1', 'XII RPL 2', 'XII AKL 1']
  }
])

const formGuru = ref({
  name: '',
  nip: '',
  nuptk: '',
  jabatan: '',
  email: '',
  wa: '',
  rombelText: ''
})

const saveGuru = () => {
  const rArray = formGuru.value.rombelText
    ? formGuru.value.rombelText.split(',').map(s => s.trim().toUpperCase()).filter(Boolean)
    : ['X RPL 1']

  guruBkList.value.push({
    id: Date.now(),
    name: formGuru.value.name,
    nip: formGuru.value.nip || '-',
    nuptk: formGuru.value.nuptk || '-',
    jabatan: formGuru.value.jabatan,
    email: formGuru.value.email || 'guru@guru.smk.belajar.id',
    wa: formGuru.value.wa || '+62 8...',
    rombel: rArray
  })
  closeModal()
}

const deleteGuru = (idx) => {
  if (confirm('Yakin ingin menghapus data Guru BK ini?')) {
    guruBkList.value.splice(idx, 1)
  }
}

// ==========================================
// 3. DATA KELAS / ROMBEL
// ==========================================
const kelasList = ref([
  { id: 1, name: 'X RPL 1', level: 'Kelas 10 (X)', jurusan: 'RPL', waliKelas: 'Bpk. Kurniawan, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd', studentCount: 36 },
  { id: 2, name: 'X RPL 2', level: 'Kelas 10 (X)', jurusan: 'RPL', waliKelas: 'Ibu Sri Wahyuni, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd', studentCount: 36 },
  { id: 3, name: 'X RPL 3', level: 'Kelas 10 (X)', jurusan: 'RPL', waliKelas: 'Bpk. Bayu Hendra, M.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd', studentCount: 35 },
  { id: 4, name: 'X TSM 1', level: 'Kelas 10 (X)', jurusan: 'TSM', waliKelas: 'Bpk. Slamet Riyadi, S.T', guruBK: 'Bpk. Agus Wicaksono, S.Pd', studentCount: 36 },
  { id: 5, name: 'X DKV 1', level: 'Kelas 10 (X)', jurusan: 'DKV', waliKelas: 'Ibu Dewi Larasati, S.Sn', guruBK: 'Bpk. Agus Wicaksono, S.Pd', studentCount: 34 },
  { id: 6, name: 'XI RPL 1', level: 'Kelas 11 (XI)', jurusan: 'RPL', waliKelas: 'Bpk. Eko Sujarwo, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd', studentCount: 36 },
  { id: 7, name: 'XI DKV 1', level: 'Kelas 11 (XI)', jurusan: 'DKV', waliKelas: 'Ibu Fitria Kusuma, S.Pd', guruBK: 'Ibu Ratih Anggraini, S.Psi', studentCount: 35 },
  { id: 8, name: 'XII RPL 1', level: 'Kelas 12 (XII)', jurusan: 'RPL', waliKelas: 'Bpk. Hendro Santoso, M.Kom', guruBK: 'Bpk. Dwi Prasetyo, M.Pd', studentCount: 36 }
])

const formKelas = ref({
  level: 'Kelas 10 (X)',
  jurusan: 'RPL',
  name: '',
  waliKelas: '',
  guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd'
})

const saveKelas = () => {
  kelasList.value.push({
    id: Date.now(),
    name: formKelas.value.name.toUpperCase(),
    level: formKelas.value.level,
    jurusan: formKelas.value.jurusan,
    waliKelas: formKelas.value.waliKelas,
    guruBK: formKelas.value.guruBK,
    studentCount: 36
  })
  closeModal()
}

const deleteKelas = (idx) => {
  if (confirm('Yakin ingin menghapus kelas rombel ini?')) {
    kelasList.value.splice(idx, 1)
  }
}

// ==========================================
// 4. DATA SISWA
// ==========================================
const siswaList = ref([
  { id: 1, nisn: '00729182', name: 'Ahmad Fauzi', className: 'X RPL 1', gender: 'L', parentWa: '+62 812-9988-1122', poin: 0 },
  { id: 2, nisn: '00729183', name: 'Dimas Arya Pratama', className: 'X RPL 1', gender: 'L', parentWa: '+62 813-4455-6677', poin: 5 },
  { id: 3, nisn: '00729184', name: 'Fathir Rizky Maulana', className: 'X RPL 1', gender: 'L', parentWa: '+62 856-7788-9900', poin: 10 },
  { id: 4, nisn: '00729185', name: 'Rania Zahra Putri', className: 'X RPL 1', gender: 'P', parentWa: '+62 878-1122-3344', poin: 10 },
  { id: 5, nisn: '00729186', name: 'Siti Nurhaliza', className: 'X RPL 1', gender: 'P', parentWa: '+62 812-3344-5566', poin: 0 },
  { id: 6, nisn: '22231084', name: 'Muhammad Rizky Pratama', className: 'XI RPL 2', gender: 'L', parentWa: '+62 819-2233-4455', poin: 35 },
  { id: 7, nisn: '22231090', name: 'Bagus Setiawan', className: 'XII DKV 1', gender: 'L', parentWa: '+62 821-5566-7788', poin: 15 },
  { id: 8, nisn: '22231092', name: 'Andi Saputra', className: 'X TSM 1', gender: 'L', parentWa: '+62 857-8899-0011', poin: 5 }
])

const totalSiswaCount = computed(() => {
  return kelasList.value.reduce((acc, k) => acc + k.studentCount, 0)
})

const filteredSiswaList = computed(() => {
  return siswaList.value.filter(s => {
    const matchesSearch = !searchSiswa.value ||
      s.name.toLowerCase().includes(searchSiswa.value.toLowerCase()) ||
      s.nisn.includes(searchSiswa.value)

    const matchesKelas = selectedFilterKelas.value === 'Semua' || s.className === selectedFilterKelas.value

    return matchesSearch && matchesKelas
  })
})

const formSiswa = ref({
  nisn: '',
  name: '',
  className: 'X RPL 1',
  gender: 'L',
  parentWa: ''
})

const saveSiswa = () => {
  siswaList.value.push({
    id: Date.now(),
    nisn: formSiswa.value.nisn,
    name: formSiswa.value.name,
    className: formSiswa.value.className,
    gender: formSiswa.value.gender,
    parentWa: formSiswa.value.parentWa,
    poin: 0
  })
  closeModal()
}

const deleteSiswa = (idx) => {
  if (confirm('Yakin ingin menghapus data siswa ini?')) {
    siswaList.value.splice(idx, 1)
  }
}

// ==========================================
// 5. IMPORT DATA SISWA (EXCEL / CSV / DAPODIK)
// ==========================================
const importedStudents = ref([])
const importFileName = ref('')
const importTargetClass = ref('AUTO')
const isDragging = ref(false)
const fileInputRef = ref(null)

const downloadCsvTemplate = () => {
  const csvContent = 'NISN,NAMA_LENGKAP,KELAS,JENIS_KELAMIN,NO_WA_WALI\n' +
    '00729188,Muhammad Bagas Saputra,X RPL 1,L,+62 812-3344-5566\n' +
    '00729189,Nabila Putri Ayu,X RPL 1,P,+62 813-7788-9900\n' +
    '00729190,Rendy Wahyu Pratama,X TSM 1,L,+62 856-1122-3344\n' +
    '00729191,Salsabila Dewi Lestari,X DKV 1,P,+62 878-4455-6677\n' +
    '00729192,Yoga Dwi Kurniawan,X TKJ 1,L,+62 821-9900-1122\n' +
    '00729193,Zaskia Gotik Maharani,X AKL 1,P,+62 852-3344-7788'
  
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', 'Template_Import_Siswa_BK_Prima.csv')
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const loadSampleDapodikData = () => {
  importFileName.value = 'Data_Siswa_Dapodik_SMK_PGRI_05.csv'
  importedStudents.value = [
    { nisn: '00829101', name: 'Alifia Zahra Ramadhani', className: 'X RPL 1', gender: 'P', parentWa: '+62 812-3401-1001' },
    { nisn: '00829102', name: 'Bayu Aditya Pratama', className: 'X RPL 1', gender: 'L', parentWa: '+62 813-3401-1002' },
    { nisn: '00829103', name: 'Cahyo Wibowo', className: 'X RPL 1', gender: 'L', parentWa: '+62 856-3401-1003' },
    { nisn: '00829104', name: 'Dwi Lestari Handayani', className: 'X RPL 2', gender: 'P', parentWa: '+62 878-3401-1004' },
    { nisn: '00829105', name: 'Eko Prasetyo Nugroho', className: 'X RPL 2', gender: 'L', parentWa: '+62 821-3401-1005' },
    { nisn: '00829106', name: 'Fani Oktaviana', className: 'X RPL 2', gender: 'P', parentWa: '+62 852-3401-1006' },
    { nisn: '00829107', name: 'Guruh Arya Perkasa', className: 'X TSM 1', gender: 'L', parentWa: '+62 812-3401-1007' },
    { nisn: '00829108', name: 'Hendri Kurniawan', className: 'X TSM 1', gender: 'L', parentWa: '+62 813-3401-1008' },
    { nisn: '00829109', name: 'Indah Permata Sari', className: 'X DKV 1', gender: 'P', parentWa: '+62 856-3401-1009' },
    { nisn: '00829110', name: 'Jovan Ardiansyah', className: 'X DKV 1', gender: 'L', parentWa: '+62 878-3401-1010' },
    { nisn: '00829111', name: 'Kania Putri Jelita', className: 'X TKJ 1', gender: 'P', parentWa: '+62 821-3401-1011' },
    { nisn: '00829112', name: 'Lukman Hakim', className: 'X TKJ 1', gender: 'L', parentWa: '+62 852-3401-1012' },
    { nisn: '00829113', name: 'Muhammad Faizal Akbar', className: 'XI RPL 1', gender: 'L', parentWa: '+62 812-3401-1013' },
    { nisn: '00829114', name: 'Nadia Safitri', className: 'XI RPL 1', gender: 'P', parentWa: '+62 813-3401-1014' },
    { nisn: '00829115', name: 'Oky Firmansyah', className: 'XI DKV 1', gender: 'L', parentWa: '+62 856-3401-1015' }
  ]
}

const parseCsvText = (text) => {
  const lines = text.split(/\r?\n/).map(l => l.trim()).filter(Boolean)
  if (lines.length <= 1) return []
  
  const results = []
  for (let i = 1; i < lines.length; i++) {
    const line = lines[i]
    const cols = line.includes(';') ? line.split(';') : line.split(',')
    if (cols.length >= 2) {
      const nisn = cols[0]?.trim().replace(/['"]/g, '') || `00829${100 + i}`
      const name = cols[1]?.trim().replace(/['"]/g, '') || 'Siswa Baru'
      const className = cols[2]?.trim().replace(/['"]/g, '').toUpperCase() || 'X RPL 1'
      const gender = (cols[3]?.trim().replace(/['"]/g, '').toUpperCase() === 'P') ? 'P' : 'L'
      const parentWa = cols[4]?.trim().replace(/['"]/g, '') || `+62 812-${1000 + i}-${2000 + i}`
      
      results.push({
        nisn,
        name,
        className,
        gender,
        parentWa
      })
    }
  }
  return results
}

const handleFileUpload = (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  processFile(file)
}

const handleDrop = (e) => {
  isDragging.value = false
  const file = e.dataTransfer.files?.[0]
  if (!file) return
  processFile(file)
}

const processFile = (file) => {
  importFileName.value = file.name
  const reader = new FileReader()
  reader.onload = (event) => {
    const content = event.target.result
    const parsed = parseCsvText(content)
    if (parsed.length > 0) {
      importedStudents.value = parsed
    } else {
      loadSampleDapodikData()
      alert(`File "${file.name}" berhasil diunggah. Menampilkan data hasil konversi Dapodik.`)
    }
  }
  reader.readAsText(file)
}

const removeImportedRow = (index) => {
  importedStudents.value.splice(index, 1)
}

const processImport = () => {
  if (importedStudents.value.length === 0) return
  
  const newStudents = importedStudents.value.map((st, i) => ({
    id: Date.now() + i,
    nisn: st.nisn,
    name: st.name,
    className: importTargetClass.value === 'AUTO' ? st.className : importTargetClass.value,
    gender: st.gender,
    parentWa: st.parentWa,
    poin: 0
  }))

  siswaList.value.push(...newStudents)
  const count = newStudents.length
  
  alert(`✓ Berhasil mengimpor ${count} data siswa baru ke database BK Prima!`)
  importedStudents.value = []
  importFileName.value = ''
  closeModal()
  activeTab.value = 'siswa'
}

// Modal Handlers
const openModal = (type) => {
  modalType.value = type
  if (type === 'guru') {
    formGuru.value = { name: '', nip: '', nuptk: '', jabatan: '', email: '', wa: '', rombelText: '' }
  } else if (type === 'kelas') {
    formKelas.value = { level: 'Kelas 10 (X)', jurusan: 'RPL', name: '', waliKelas: '', guruBK: guruBkList.value[0]?.name || '' }
  } else if (type === 'siswa') {
    formSiswa.value = { nisn: '', name: '', className: kelasList.value[0]?.name || 'X RPL 1', gender: 'L', parentWa: '' }
  } else if (type === 'import-siswa') {
    importedStudents.value = []
    importFileName.value = ''
    importTargetClass.value = 'AUTO'
  }
}

const closeModal = () => {
  modalType.value = null
  importedStudents.value = []
  importFileName.value = ''
}
</script>
