<template>
  <AppLayout active-tab="absensi" show-share>
    <div class="max-w-6xl mx-auto">

      <!-- ============================================================ -->
      <!-- UNIFIED MASTER CONTAINER (SATU KESATUAN TATA LETAK RAPI)      -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        
        <!-- 1. TOP HEADER SECTION -->
        <div class="p-5 sm:p-6 bg-white border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/70">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                </svg>
                Portal Presensi Terpadu Siswa
              </span>
              <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Kamis, 24 Okt 2024
              </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
              Rekap Presensi Harian Siswa
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
              SMK PGRI 05 JEMBER &bull; Terhubung langsung ke mesin fingerprint HadirMu
            </p>
          </div>

          <div class="flex items-center gap-2 self-start md:self-auto flex-wrap">
            <Link
              href="/absensi/bulanan"
              class="inline-flex items-center justify-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold px-3.5 py-2.5 rounded-xl text-xs transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Rekapan Bulanan (A4)</span>
            </Link>

            <button
              @click="syncAttendance"
              class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-blue-600/20 transition cursor-pointer shrink-0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>Sinkronkan Mesin HadirMu</span>
            </button>
          </div>
        </div>

        <!-- 2. INTEGRATED FILTER & NAVIGATION TOOLBAR -->
        <div class="bg-slate-50/90 border-b border-slate-200/80 p-4 sm:p-5">
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            
            <!-- Filter 1: Tingkat Kelas Tabs -->
            <div class="sm:col-span-5 space-y-1.5">
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                1. Tingkat Kelas
              </label>
              <div class="grid grid-cols-3 gap-1 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
                <button
                  v-for="lvl in levels"
                  :key="lvl.id"
                  @click="onSelectLevel(lvl.id)"
                  class="py-2 text-center text-xs font-bold rounded-lg transition-all duration-150 cursor-pointer"
                  :class="selectedLevel === lvl.id ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                >
                  {{ lvl.label }}
                </button>
              </div>
            </div>

            <!-- Filter 2: Dropdown Jurusan -->
            <div class="sm:col-span-4 space-y-1.5">
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                2. Bidang Keahlian / Jurusan
              </label>
              <div class="relative">
                <select
                  v-model="selectedJurusan"
                  @change="onJurusanChange"
                  class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none shadow-2xs transition cursor-pointer"
                >
                  <option value="ALL">Semua Jurusan</option>
                  <option v-for="jur in jurusans" :key="jur.code" :value="jur.code">
                    {{ jur.name }} ({{ jur.code }})
                  </option>
                </select>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Filter 3: Dropdown Rombel Langsung -->
            <div class="sm:col-span-3 space-y-1.5">
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                3. Rombel Terpilih
              </label>
              <div class="relative">
                <select
                  :value="currentRombel.id"
                  @change="onDropdownRombelChange"
                  class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none shadow-2xs transition cursor-pointer"
                >
                  <option v-for="r in availableRombels" :key="r.id" :value="r.id">
                    {{ r.name }} ({{ r.count }} Siswa)
                  </option>
                </select>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

          </div>

          <!-- Quick Class Selection Badges -->
          <div class="mt-3 pt-3 border-t border-slate-200/60 flex items-center gap-2 overflow-x-auto scrollbar-hide">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              Pilih Cepat:
            </span>
            <button
              v-for="rom in availableRombels"
              :key="rom.id"
              @click="selectRombel(rom)"
              class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer shrink-0 border"
              :class="currentRombel.name === rom.name ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-200'"
            >
              {{ rom.name }}
            </button>
          </div>
        </div>

        <!-- 3. ACTIVE ROMBEL INFO & VIEW SWITCHER BAR -->
        <div class="p-5 sm:p-6 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 shadow-2xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h2 class="font-black text-slate-900 text-lg sm:text-xl tracking-tight">
                  {{ currentRombel.name }}
                </h2>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-blue-100 text-blue-700">
                  {{ currentRombel.jurusan }}
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  94.4% Hadir Hari Ini
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-1 font-medium">
                Wali Kelas: <span class="font-bold text-slate-800">{{ currentRombel.waliKelas }}</span> &bull; Guru BK: <span class="text-slate-700 font-semibold">{{ currentRombel.guruBK }}</span>
              </p>
            </div>
          </div>

          <!-- Switcher Mode: [Rekap Presensi Harian] vs [Data Siswa Kelas] -->
          <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-2xl border border-slate-200/70 self-start sm:self-auto">
            <button
              @click="viewMode = 'presensi'"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="viewMode === 'presensi' ? 'bg-white text-blue-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
              <span>Presensi Harian</span>
            </button>
            <button
              @click="viewMode = 'datasiswa'"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="viewMode === 'datasiswa' ? 'bg-white text-blue-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span>Data Siswa Kelas</span>
            </button>
          </div>
        </div>

        <!-- 4. INTEGRATED KPI 4 COUNTERS STRIP (When Presensi view is active) -->
        <div v-if="viewMode === 'presensi'" class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 sm:p-5 bg-slate-50/60 border-b border-slate-100">
          <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl border border-slate-200/80 shadow-2xs text-center">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">TOTAL SISWA</span>
            <span class="text-xl font-black text-slate-900 my-0.5">{{ currentRombel.count }}</span>
            <span class="text-[11px] font-medium text-slate-400">Terdaftar Aktif</span>
          </div>
          <div class="flex flex-col items-center justify-center p-3 bg-emerald-50/80 rounded-xl border border-emerald-200/80 shadow-2xs text-center">
            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider">HADIR</span>
            <span class="text-xl font-black text-emerald-700 my-0.5">{{ rombelStats.hadir }}</span>
            <span class="text-[11px] font-medium text-emerald-600">Tepat Waktu</span>
          </div>
          <div class="flex flex-col items-center justify-center p-3 bg-amber-50/80 rounded-xl border border-amber-200/80 shadow-2xs text-center">
            <span class="text-[10px] font-black text-amber-700 uppercase tracking-wider">TERLAMBAT</span>
            <span class="text-xl font-black text-amber-700 my-0.5">{{ rombelStats.telat }}</span>
            <span class="text-[11px] font-medium text-amber-600">Tercatat Hadir</span>
          </div>
          <div class="flex flex-col items-center justify-center p-3 bg-rose-50/80 rounded-xl border border-rose-200/80 shadow-2xs text-center">
            <span class="text-[10px] font-black text-rose-700 uppercase tracking-wider">ALPA / IZIN / SAKIT</span>
            <span class="text-xl font-black text-rose-700 my-0.5">{{ rombelStats.alpa + rombelStats.izin + rombelStats.sakit }}</span>
            <span class="text-[11px] font-medium text-rose-600">Ket. Khusus</span>
          </div>
        </div>

        <!-- 5. SEARCH & LIST HEADER TOOLBAR -->
        <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
          <div class="flex items-center gap-2 text-slate-800 text-xs sm:text-sm font-bold">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            <span>Daftar Siswa {{ currentRombel.name }}</span>
            <span class="text-slate-400 font-normal text-xs">({{ filteredStudents.length }} dari {{ currentRombel.count }} siswa)</span>
          </div>

          <!-- Search Input -->
          <div class="relative w-full sm:w-72">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama siswa atau NISN..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition"
            />
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
          </div>
        </div>

        <!-- 6. DATA TABLES -->
        <!-- VIEW 1: TABEL PRESENSI KEHADIRAN -->
        <div v-if="viewMode === 'presensi'" class="px-4 sm:px-6 pb-6">
          <div class="overflow-x-auto border border-slate-200/80 rounded-2xl shadow-2xs">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                <tr>
                  <th class="py-3 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                  <th class="py-3 px-4 text-[11px] uppercase tracking-wider">NAMA LENGKAP SISWA</th>
                  <th class="py-3 px-3 text-center text-[11px] uppercase tracking-wider">JK</th>
                  <th class="py-3 px-4 text-center text-[11px] uppercase tracking-wider">TGL &amp; SCAN HADIRMU</th>
                  <th class="py-3 px-4 text-center text-[11px] uppercase tracking-wider">STATUS KEHADIRAN</th>
                  <th class="py-3 px-4 text-[11px] uppercase tracking-wider">KETERANGAN MASUK</th>
                  <th class="py-3 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="student in filteredStudents" :key="student.no" class="hover:bg-slate-50/80 transition">
                  <td class="py-3 px-4 text-center font-bold text-slate-400">{{ student.no }}</td>
                  <td class="py-3 px-4">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ student.name }}</div>
                    <div class="text-[11px] text-slate-400 font-mono">NISN: {{ student.nisn }}</div>
                  </td>
                  <td class="py-3 px-3 text-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md font-bold text-xs" :class="student.gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700'">
                      {{ student.gender }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="font-medium text-slate-700">{{ student.date }}</div>
                    <div class="text-[11px] text-slate-400 font-mono flex items-center justify-center gap-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ student.scanTime }}</span>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <span v-if="student.status === 'Hadir'" class="px-3 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded-full border border-emerald-200 text-xs">
                      Hadir
                    </span>
                    <span v-else-if="student.status === 'Terlambat'" class="px-3 py-0.5 bg-amber-50 text-amber-700 font-bold rounded-full border border-amber-200 text-xs">
                      Terlambat
                    </span>
                    <span v-else-if="student.status === 'Sakit'" class="px-3 py-0.5 bg-blue-50 text-blue-700 font-bold rounded-full border border-blue-200 text-xs">
                      Sakit
                    </span>
                    <span v-else-if="student.status === 'Izin'" class="px-3 py-0.5 bg-purple-50 text-purple-700 font-bold rounded-full border border-purple-200 text-xs">
                      Izin
                    </span>
                    <span v-else class="px-3 py-0.5 bg-rose-50 text-rose-700 font-bold rounded-full border border-rose-200 text-xs">
                      Alpa
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <span class="text-xs text-slate-600 font-medium">{{ student.note }}</span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                      <!-- Edit Presensi Status -->
                      <button
                        @click="openEditStatusModal(student)"
                        class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold border border-blue-200 flex items-center gap-1 cursor-pointer transition"
                        title="Ubah Status Kehadiran Siswa"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Ubah</span>
                      </button>

                      <!-- Catat Pelanggaran / Poin BK -->
                      <button
                        @click="recordViolationForStudent(student)"
                        class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-bold border border-amber-200 flex items-center gap-1 cursor-pointer transition"
                        title="Catat Poin Pelanggaran Kedisiplinan"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Poin</span>
                      </button>

                      <!-- WhatsApp Orang Tua -->
                      <button
                        @click="sendWhatsAppToParent(student)"
                        class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-200 flex items-center gap-1 cursor-pointer transition"
                        title="Kirim Notifikasi Presensi ke WhatsApp Orang Tua"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>WA</span>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredStudents.length === 0">
                  <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                    Tidak ada data siswa yang cocok dengan pencarian.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- VIEW 2: TABEL DATA INDUK SISWA KELAS -->
        <div v-else class="px-4 sm:px-6 pb-6">
          <div class="overflow-x-auto border border-slate-200/80 rounded-2xl shadow-2xs">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                <tr>
                  <th class="py-3 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                  <th class="py-3 px-4 text-[11px] uppercase tracking-wider">NISN</th>
                  <th class="py-3 px-4 text-[11px] uppercase tracking-wider">NAMA LENGKAP</th>
                  <th class="py-3 px-3 text-center text-[11px] uppercase tracking-wider">JK</th>
                  <th class="py-3 px-4 text-[11px] uppercase tracking-wider">TEMPAT, TGL LAHIR</th>
                  <th class="py-3 px-4 text-[11px] uppercase tracking-wider">NAMA WALI / ORTU</th>
                  <th class="py-3 px-4 text-[11px] uppercase tracking-wider">NO WA WALI</th>
                  <th class="py-3 px-3 text-center text-[11px] uppercase tracking-wider">STATUS</th>
                  <th class="py-3 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="student in filteredStudents" :key="student.no" class="hover:bg-slate-50/80 transition">
                  <td class="py-3 px-4 text-center font-bold text-slate-400">{{ student.no }}</td>
                  <td class="py-3 px-4 font-mono font-medium text-slate-700">{{ student.nisn }}</td>
                  <td class="py-3 px-4">
                    <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ student.name }}</span>
                  </td>
                  <td class="py-3 px-3 text-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md font-bold text-xs" :class="student.gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700'">
                      {{ student.gender }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-slate-600 font-medium">{{ student.birthInfo }}</td>
                  <td class="py-3 px-4 font-semibold text-slate-800">{{ student.parentName }}</td>
                  <td class="py-3 px-4 font-mono text-slate-600">{{ student.parentWa }}</td>
                  <td class="py-3 px-3 text-center">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      Aktif
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                      <!-- Detail Biodata Siswa -->
                      <button
                        @click="openStudentDetailModal(student)"
                        class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold border border-blue-200 flex items-center gap-1 cursor-pointer transition"
                        title="Lihat Biodata Lengkap Siswa"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Profil</span>
                      </button>

                      <!-- Hubungi Orang Tua (WhatsApp) -->
                      <button
                        @click="sendWhatsAppToParent(student)"
                        class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-200 flex items-center gap-1 cursor-pointer transition"
                        title="Kirim Pesan WhatsApp ke Orang Tua"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>WA</span>
                      </button>

                      <!-- Surat Panggilan Ortu -->
                      <router-link
                        to="/pemanggilan"
                        class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-bold border border-rose-200 flex items-center gap-1 cursor-pointer transition"
                        title="Terbitkan Surat Panggilan Orang Tua"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Panggil</span>
                      </router-link>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredStudents.length === 0">
                  <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                    Tidak ada siswa yang cocok dengan pencarian.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 7. FOOTER ACTION BAR -->
        <div class="p-4 sm:px-6 border-t border-slate-100 flex flex-col sm:flex-row gap-3 bg-slate-50/60">
          <button @click="exportExcel" class="flex-1 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-bold py-2.5 px-4 rounded-xl shadow-xs flex items-center justify-center gap-2 transition cursor-pointer text-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>{{ viewMode === 'presensi' ? 'Export Rekap Presensi (Excel)' : 'Export Data Siswa Kelas (Excel)' }}</span>
          </button>
          <button @click="printReport" class="px-5 bg-white hover:bg-slate-50 active:scale-98 text-slate-700 border border-slate-200 font-bold py-2.5 rounded-xl shadow-2xs flex items-center justify-center gap-2 transition cursor-pointer text-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Dokumen</span>
          </button>
        </div>

      </div>

      <!-- ============================================================ -->
      <!-- MODAL 1: UBAH STATUS PRESENSI SISWA                          -->
      <!-- ============================================================ -->
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
        <div class="bg-white rounded-3xl max-w-md w-full border border-slate-200 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
          
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-black text-slate-900 text-sm sm:text-base">Ubah Status Presensi Siswa</h3>
              <p class="text-[11px] text-slate-500">Penyesuaian presensi harian oleh Guru BK</p>
            </div>
            <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveAttendanceStatus" class="p-5 space-y-4 text-xs">
            
            <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-100">
              <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ editingStudent.name }}</div>
              <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                NISN: {{ editingStudent.nisn }} &bull; Rombel: {{ currentRombel.name }}
              </div>
            </div>

            <!-- Pilih Status Kehadiran -->
            <div>
              <label class="block font-bold text-slate-700 mb-1.5">Status Kehadiran *</label>
              <div class="grid grid-cols-3 gap-1.5">
                <button
                  type="button"
                  v-for="st in ['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpa']"
                  :key="st"
                  @click="editForm.status = st"
                  class="py-2 px-2 text-center text-xs font-bold rounded-xl border transition cursor-pointer"
                  :class="editForm.status === st ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'"
                >
                  {{ st }}
                </button>
              </div>
            </div>

            <!-- Jam Scan / Masuk -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Jam Scan HadirMu</label>
              <input
                v-model="editForm.scanTime"
                type="text"
                placeholder="Contoh: 06.45 WIB atau '-'"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <!-- Keterangan Alasan -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Keterangan / Alasan Tambahan</label>
              <input
                v-model="editForm.note"
                type="text"
                placeholder="Contoh: Surat keterangan dokter terlampir, izin dinas lomba, dll"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showEditModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/20 cursor-pointer flex items-center gap-1.5"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Perubahan</span>
              </button>
            </div>

          </form>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 2: DETAIL PROFIL & BUKU INDUK SISWA                     -->
      <!-- ============================================================ -->
      <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
        <div class="bg-white rounded-3xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
          
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-black text-slate-900 text-base">Biodata Lengkap Siswa</h3>
              <p class="text-xs text-slate-500">Buku Induk &amp; Kontak Orang Tua Siswa</p>
            </div>
            <button @click="showDetailModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="p-5 space-y-4 text-xs">
            <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
              <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 font-black text-lg flex items-center justify-center border border-blue-200">
                {{ selectedStudentDetail.name.split(' ').map(n => n[0]).slice(0, 2).join('') }}
              </div>
              <div>
                <h4 class="font-black text-slate-900 text-sm sm:text-base">{{ selectedStudentDetail.name }}</h4>
                <p class="text-xs text-slate-500 font-mono">NISN: {{ selectedStudentDetail.nisn }} &bull; Rombel: {{ currentRombel.name }}</p>
                <div class="flex items-center gap-2 mt-1">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Siswa Aktif
                  </span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ currentRombel.jurusan }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Detail Information Grid -->
            <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
              <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Kelamin</span>
                <span class="font-semibold text-slate-800">{{ selectedStudentDetail.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
              </div>
              <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tempat, Tgl Lahir</span>
                <span class="font-semibold text-slate-800">{{ selectedStudentDetail.birthInfo }}</span>
              </div>
              <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Wali Kelas</span>
                <span class="font-semibold text-slate-800">{{ currentRombel.waliKelas }}</span>
              </div>
              <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Guru BK Pembimbing</span>
                <span class="font-semibold text-slate-800">{{ currentRombel.guruBK }}</span>
              </div>
            </div>

            <!-- Orang Tua Section -->
            <div class="p-3.5 rounded-2xl border border-emerald-100 bg-emerald-50/40 space-y-2">
              <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Kontak Orang Tua / Wali Murid</span>
              </div>
              <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                  <span class="text-[10px] text-slate-500 block">Nama Wali:</span>
                  <span class="font-bold text-slate-800">{{ selectedStudentDetail.parentName }}</span>
                </div>
                <div>
                  <span class="text-[10px] text-slate-500 block">No. WhatsApp:</span>
                  <span class="font-mono font-bold text-emerald-700">{{ selectedStudentDetail.parentWa }}</span>
                </div>
              </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showDetailModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer"
              >
                Tutup
              </button>
              <button
                type="button"
                @click="sendWhatsAppToParent(selectedStudentDetail)"
                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md shadow-emerald-600/20 cursor-pointer flex items-center gap-1.5"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>Hubungi via WhatsApp</span>
              </button>
            </div>

          </div>

        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { router, Link } from '@inertiajs/vue3'

const vueRouter = useRouter()

const props = defineProps({
  dbStudents: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  todayDate: {
    type: String,
    default: ''
  }
})

const isSyncing = ref(false)
const selectedLevel = ref('10')
const selectedJurusan = ref('ALL')
const viewMode = ref('presensi')
const searchQuery = ref('')

const levels = [
  { id: '10', label: 'Kelas 10 (X)' },
  { id: '11', label: 'Kelas 11 (XI)' },
  { id: '12', label: 'Kelas 12 (XII)' }
]

const jurusans = [
  { code: 'RPL', name: 'Rekayasa Perangkat Lunak' },
  { code: 'TSM', name: 'Teknik Sepeda Motor' },
  { code: 'DKV', name: 'Desain Komunikasi Visual' },
  { code: 'TKJ', name: 'Teknik Komputer Jaringan' },
  { code: 'AKL', name: 'Akuntansi & Keuangan Lembaga' },
  { code: 'OTKP', name: 'Otomatisasi Perkantoran' }
]

const allRombels = [
  // Kelas 10
  { id: 1, level: '10', name: 'X RPL 1', jurusan: 'RPL', count: 36, waliKelas: 'Bpk. Kurniawan, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { id: 2, level: '10', name: 'X RPL 2', jurusan: 'RPL', count: 36, waliKelas: 'Ibu Sri Wahyuni, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { id: 3, level: '10', name: 'X RPL 3', jurusan: 'RPL', count: 35, waliKelas: 'Bpk. Bayu Hendra, M.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { id: 4, level: '10', name: 'X TSM 1', jurusan: 'TSM', count: 36, waliKelas: 'Bpk. Slamet Riyadi, S.T', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { id: 5, level: '10', name: 'X TSM 2', jurusan: 'TSM', count: 35, waliKelas: 'Bpk. Joko Susilo, S.Pd', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { id: 6, level: '10', name: 'X DKV 1', jurusan: 'DKV', count: 35, waliKelas: 'Ibu Dewi Larasati, S.Sn', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { id: 7, level: '10', name: 'X DKV 2', jurusan: 'DKV', count: 35, waliKelas: 'Bpk. Danang Prasetya, M.Sn', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { id: 8, level: '10', name: 'X TKJ 1', jurusan: 'TKJ', count: 36, waliKelas: 'Bpk. Ridwan Hakim, S.T', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { id: 9, level: '10', name: 'X AKL 1', jurusan: 'AKL', count: 36, waliKelas: 'Ibu Nur Aini, S.E', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },
  { id: 10, level: '10', name: 'X OTKP 1', jurusan: 'OTKP', count: 35, waliKelas: 'Ibu Endang Sulastri, S.Pd', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },

  // Kelas 11
  { id: 11, level: '11', name: 'XI RPL 1', jurusan: 'RPL', count: 36, waliKelas: 'Bpk. Eko Sujarwo, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { id: 12, level: '11', name: 'XI RPL 2', jurusan: 'RPL', count: 36, waliKelas: 'Ibu Rahmawati, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { id: 13, level: '11', name: 'XI TSM 1', jurusan: 'TSM', count: 35, waliKelas: 'Bpk. Bambang Irawan, S.T', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { id: 14, level: '11', name: 'XI DKV 1', jurusan: 'DKV', count: 36, waliKelas: 'Ibu Fitria Kusuma, S.Pd', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { id: 15, level: '11', name: 'XI TKJ 1', jurusan: 'TKJ', count: 35, waliKelas: 'Bpk. Arif Wibowo, S.Kom', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { id: 16, level: '11', name: 'XI AKL 1', jurusan: 'AKL', count: 36, waliKelas: 'Ibu Siti Aminah, S.E', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },

  // Kelas 12
  { id: 17, level: '12', name: 'XII RPL 1', jurusan: 'RPL', count: 36, waliKelas: 'Bpk. Hendro Santoso, M.Kom', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },
  { id: 18, level: '12', name: 'XII RPL 2', jurusan: 'RPL', count: 36, waliKelas: 'Ibu Maya Indah, S.Kom', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },
  { id: 19, level: '12', name: 'XII TSM 1', jurusan: 'TSM', count: 35, waliKelas: 'Bpk. Taufik Hidayat, S.T', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { id: 20, level: '12', name: 'XII DKV 1', jurusan: 'DKV', count: 36, waliKelas: 'Ibu Anisa Putri, S.Pd', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { id: 21, level: '12', name: 'XII TKJ 1', jurusan: 'TKJ', count: 36, waliKelas: 'Bpk. Ferry Anggara, S.Kom', guruBK: 'Ibu Ratih Anggraini, S.Psi' }
]

const availableRombels = computed(() => {
  return allRombels.filter(r => {
    const matchesLevel = r.level === selectedLevel.value
    const matchesJurusan = selectedJurusan.value === 'ALL' || r.jurusan === selectedJurusan.value
    return matchesLevel && matchesJurusan
  })
})

const currentRombel = ref(allRombels[0])

const onSelectLevel = (lvlId) => {
  selectedLevel.value = lvlId
  const match = allRombels.find(r => r.level === lvlId && (selectedJurusan.value === 'ALL' || r.jurusan === selectedJurusan.value))
  if (match) {
    currentRombel.value = match
  } else {
    const fallback = allRombels.find(r => r.level === lvlId)
    if (fallback) currentRombel.value = fallback
  }
}

const onJurusanChange = () => {
  const match = availableRombels.value[0]
  if (match) {
    currentRombel.value = match
  }
}

const selectRombel = (rom) => {
  currentRombel.value = rom
}

const onDropdownRombelChange = (e) => {
  const targetId = Number(e.target.value)
  const found = allRombels.find(r => r.id === targetId)
  if (found) {
    currentRombel.value = found
  }
}

// Student generator function
const generateStudents = (rombel) => {
  const count = rombel.count || 36
  const baseList = [
    { name: 'Ahmad Fauzi', gender: 'L', status: 'Hadir', scanTime: '06.42 WIB', note: 'Tepat Waktu' },
    { name: 'Dimas Arya Pratama', gender: 'L', status: 'Terlambat', scanTime: '07.24 WIB', note: 'Terlambat 24 Menit' },
    { name: 'Fathir Rizky Maulana', gender: 'L', status: 'Hadir', scanTime: '06.35 WIB', note: 'Tepat Waktu' },
    { name: 'Rania Zahra Putri', gender: 'P', status: 'Terlambat', scanTime: '07.15 WIB', note: 'Terlambat 15 Menit' },
    { name: 'Siti Nurhaliza', gender: 'P', status: 'Sakit', scanTime: '-', note: 'Surat Keterangan Dokter' },
    { name: 'Yusuf Maulana Al-Farizi', gender: 'L', status: 'Hadir', scanTime: '06.48 WIB', note: 'Tepat Waktu' },
    { name: 'Zulfikar Ali Akbar', gender: 'L', status: 'Izin', scanTime: '-', note: 'Dispensasi Kegiatan Sekolah' },
    { name: 'Bagus Setiawan', gender: 'L', status: 'Alpa', scanTime: '-', note: 'Tanpa Keterangan' },
    { name: 'Aditya Pratama', gender: 'L', status: 'Hadir', scanTime: '06.40 WIB', note: 'Tepat Waktu' },
    { name: 'Ananda Putri Maharani', gender: 'P', status: 'Hadir', scanTime: '06.38 WIB', note: 'Tepat Waktu' },
    { name: 'Bima Satria Wijaya', gender: 'L', status: 'Hadir', scanTime: '06.50 WIB', note: 'Tepat Waktu' },
    { name: 'Cantika Dewi Lestari', gender: 'P', status: 'Hadir', scanTime: '06.44 WIB', note: 'Tepat Waktu' },
    { name: 'Danendra Putra', gender: 'L', status: 'Hadir', scanTime: '06.30 WIB', note: 'Tepat Waktu' },
    { name: 'Dhea Rahmawati', gender: 'P', status: 'Hadir', scanTime: '06.52 WIB', note: 'Tepat Waktu' },
    { name: 'Fajar Nugroho', gender: 'L', status: 'Hadir', scanTime: '06.45 WIB', note: 'Tepat Waktu' },
    { name: 'Gilang Ramadhan', gender: 'L', status: 'Hadir', scanTime: '06.36 WIB', note: 'Tepat Waktu' },
    { name: 'Hana Alifah Zahra', gender: 'P', status: 'Hadir', scanTime: '06.41 WIB', note: 'Tepat Waktu' },
    { name: 'Irfan Maulana', gender: 'L', status: 'Hadir', scanTime: '06.47 WIB', note: 'Tepat Waktu' },
    { name: 'Jessica Aurelia', gender: 'P', status: 'Hadir', scanTime: '06.39 WIB', note: 'Tepat Waktu' },
    { name: 'Kevin Christian', gender: 'L', status: 'Hadir', scanTime: '06.43 WIB', note: 'Tepat Waktu' }
  ]

  const results = []
  for (let i = 1; i <= count; i++) {
    const template = baseList[(i - 1) % baseList.length]
    const nisn = '007' + (29180 + i + (rombel.id * 50))
    results.push({
      no: i,
      name: i <= baseList.length ? template.name : `${template.name} ${i}`,
      nisn: nisn,
      gender: template.gender,
      date: '24/10/24',
      scanTime: template.scanTime,
      status: template.status,
      note: template.note,
      birthInfo: `Jember, ${(i % 28) + 1} Mei 2008`,
      parentName: `Bpk. ${template.name.split(' ')[0]}`,
      parentWa: `+62 812-${(1000 + i * 37)}-${(2000 + i * 49)}`
    })
  }
  return results
}

// Stateful reactive student list
const studentsList = ref(generateStudents(currentRombel.value))

watch(currentRombel, (newRom) => {
  studentsList.value = generateStudents(newRom)
})

const filteredStudents = computed(() => {
  if (!searchQuery.value) return studentsList.value
  const q = searchQuery.value.toLowerCase()
  return studentsList.value.filter(s => s.name.toLowerCase().includes(q) || s.nisn.includes(q))
})

const rombelStats = computed(() => {
  const students = studentsList.value
  const hadir = students.filter(s => s.status === 'Hadir').length
  const telat = students.filter(s => s.status === 'Terlambat').length
  const sakit = students.filter(s => s.status === 'Sakit').length
  const izin = students.filter(s => s.status === 'Izin').length
  const alpa = students.filter(s => s.status === 'Alpa').length
  return { hadir, telat, sakit, izin, alpa }
})

// ==========================================
// ACTION MODALS STATE & METHODS
// ==========================================
const showEditModal = ref(false)
const showDetailModal = ref(false)

const editingStudent = ref({})
const editForm = ref({
  status: 'Hadir',
  scanTime: '',
  note: ''
})

const selectedStudentDetail = ref({})

// 1. Open Edit Status Modal
const openEditStatusModal = (student) => {
  editingStudent.value = student
  editForm.value = {
    status: student.status,
    scanTime: student.scanTime,
    note: student.note
  }
  showEditModal.value = true
}

// 2. Save Updated Attendance Status
const saveAttendanceStatus = () => {
  const idx = studentsList.value.findIndex(s => s.no === editingStudent.value.no)
  if (idx !== -1) {
    studentsList.value[idx].status = editForm.value.status
    studentsList.value[idx].scanTime = editForm.value.scanTime || '-'
    studentsList.value[idx].note = editForm.value.note || (editForm.value.status === 'Hadir' ? 'Tepat Waktu' : editForm.value.status)
  }
  showEditModal.value = false
  alert(`Status presensi siswa ${editingStudent.value.name} berhasil diperbarui menjadi "${editForm.value.status}"!`)
}

// 3. Open Student Detail Modal
const openStudentDetailModal = (student) => {
  selectedStudentDetail.value = student
  showDetailModal.value = true
}

// 4. Record Violation for Student (Direct to /verifikasi)
const recordViolationForStudent = (student) => {
  router.push({
    path: '/verifikasi',
    query: {
      nama: student.name,
      nisn: student.nisn,
      kelas: currentRombel.value.name
    }
  })
}

// 5. WhatsApp notification to parent
const sendWhatsAppToParent = (student) => {
  const text = encodeURIComponent(
    `Yth. Bapak/Ibu ${student.parentName} (Wali dari ${student.name} - ${currentRombel.value.name}), kami menginformasikan laporan presensi harian HadirMu pada tanggal 24 Okt 2024: Status: ${student.status} (Waktu: ${student.scanTime}). Keterangan: ${student.note}. Terima kasih atas perhatiannya. - Tim BK SMK PGRI 05 JEMBER`
  )
  const phone = student.parentWa.replace(/[^0-9]/g, '')
  window.open(`https://wa.me/${phone}?text=${text}`, '_blank')
}

const syncAttendance = () => {
  isSyncing.value = true
  router.post('/absensi/sync', {}, {
    onSuccess: () => {
      alert('Sinkronisasi data mesin fingerprint HadirMu berhasil diperbarui!')
    },
    onError: () => {
      alert('Gagal menyinkronkan data mesin fingerprint.')
    },
    onFinish: () => {
      isSyncing.value = false
    }
  })
}

const exportExcel = () => {
  alert(`Data ${viewMode.value === 'presensi' ? 'presensi' : 'siswa'} rombel ${currentRombel.value.name} berhasil diexport ke format Excel!`)
}

const printReport = () => {
  window.print()
}
</script>
