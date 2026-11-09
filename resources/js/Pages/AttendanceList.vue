<template>
  <AppLayout active-tab="absensi" show-share>
    <div class="max-w-7xl mx-auto space-y-5">

      <!-- ============================================================ -->
      <!-- TOP HEADER SECTION                                            -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
            <span class="text-blue-700 font-semibold text-xs">Presensi Harian</span>
            <span>&bull;</span>
            <span>{{ todayDate || 'Senin, 02 Nov 2026' }}</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
            Rekap Presensi Harian Siswa
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            SMK PGRI 05 Jember &bull; Terhubung ke mesin presensi HadirMu
          </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
          <Link
            href="/absensi/bulanan"
            class="interactive-btn inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-medium px-3.5 py-2 rounded-lg text-xs cursor-pointer shadow-2xs"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Rekapan Bulanan (A4)</span>
          </Link>

          <button
            @click="openAddStudentModal"
            class="interactive-btn inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium px-3.5 py-2 rounded-lg text-xs cursor-pointer shadow-2xs"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Siswa</span>
          </button>

          <button
            @click="syncAttendance"
            :disabled="isSyncing"
            class="interactive-btn inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 disabled:bg-slate-400 text-white font-medium px-3.5 py-2 rounded-lg text-xs cursor-pointer shadow-2xs"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="{ 'animate-spin': isSyncing }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>{{ isSyncing ? 'Menyinkronkan...' : 'Sinkronkan HadirMu' }}</span>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- SELECTOR DROPDOWNS: TINGKAT -> JURUSAN -> ROMBEL               -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
        <div class="mb-3 flex items-center justify-between">
          <div class="text-xs font-semibold text-slate-800 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span>Filter Kelas &amp; Jurusan Siswa</span>
          </div>

          <button
            v-if="isFilterActive"
            @click="resetFilters"
            class="text-xs font-medium text-blue-600 hover:text-blue-800 inline-flex items-center gap-1 cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Tampilkan Semua Kelas</span>
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
          
          <!-- 1. Dropdown Tingkat Kelas -->
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">
              1. Tingkat Kelas
            </label>
            <div class="relative">
              <select
                :value="selectedLevel"
                @change="onLevelChange($event.target.value)"
                class="w-full bg-white hover:border-slate-300 border border-slate-200 rounded-lg pl-8 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
              >
                <option value="ALL">Semua Kelas</option>
                <option value="10">Kelas 10</option>
                <option value="11">Kelas 11</option>
                <option value="12">Kelas 12</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- 2. Dropdown Jurusan / Bidang Keahlian -->
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">
              2. Jurusan / Keahlian
            </label>
            <div class="relative">
              <select
                :value="selectedJurusan"
                @change="onJurusanChange($event.target.value)"
                class="w-full bg-white hover:border-slate-300 border border-slate-200 rounded-lg pl-8 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
              >
                <option value="ALL">Semua Jurusan</option>
                <option v-for="jur in jurusanOptions" :key="jur.code" :value="jur.code">
                  {{ jur.name }} ({{ jur.code }})
                </option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- 3. Dropdown Rombel Spesifik -->
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">
              3. Rombel Terpilih
            </label>
            <div class="relative">
              <select
                :value="selectedClass"
                @change="onClassChange($event.target.value)"
                class="w-full bg-white hover:border-slate-300 border border-slate-200 rounded-lg pl-8 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
              >
                <option value="ALL">Semua Rombel</option>
                <option v-for="cls in filteredRombels" :key="cls" :value="cls">
                  {{ cls }}
                </option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- 4. Pencarian Siswa -->
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">
              Pencarian Siswa
            </label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nama atau NISN..."
                class="w-full bg-white hover:border-slate-300 border border-slate-200 rounded-lg pl-8 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition"
              />
              <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- BANNER INFORMASI KELAS AKTIF (JIKA MEMILIH KELAS SPESIFIK)   -->
      <!-- ============================================================ -->
      <div v-if="activeRombelInfo" class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h2 class="font-bold text-slate-900 text-base sm:text-lg tracking-tight">
                {{ activeRombelInfo.name }}
              </h2>
              <span class="text-xs text-slate-500 font-medium">
                &bull; {{ activeRombelInfo.jurusan }}
              </span>
              <span class="text-xs font-medium text-emerald-700">
                &bull; {{ activeRombelInfo.attendanceRate }}% Hadir Hari Ini
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Wali Kelas: <span class="font-semibold text-slate-800">{{ activeRombelInfo.waliKelas }}</span> &bull; Guru BK: <span class="text-slate-700 font-medium">{{ activeRombelInfo.guruBK }}</span>
            </p>
          </div>
        </div>

        <div class="text-xs text-slate-500 shrink-0">
          <span class="font-bold text-slate-900 text-sm">{{ filteredStudents.length }}</span> siswa terdata di rombel ini
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- KPI STATS STRIP                                              -->
      <!-- ============================================================ -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="interactive-card p-4 bg-white rounded-lg border border-slate-200 hover:border-slate-300 cursor-default">
          <span class="text-xs font-medium text-slate-500 block">Total Siswa</span>
          <span class="text-2xl font-bold text-slate-900 my-1 block">{{ currentStats.total }}</span>
          <span class="text-xs text-slate-500">Terdaftar aktif</span>
        </div>
        <div class="interactive-card p-4 bg-white rounded-lg border border-slate-200 hover:border-slate-300 cursor-default">
          <span class="text-xs font-medium text-slate-500 block">Hadir</span>
          <span class="text-2xl font-bold text-emerald-700 my-1 block">{{ currentStats.hadir }}</span>
          <span class="text-xs text-emerald-600 font-medium">Tepat Waktu</span>
        </div>
        <div class="interactive-card p-4 bg-white rounded-lg border border-slate-200 hover:border-slate-300 cursor-default">
          <span class="text-xs font-medium text-slate-500 block">Terlambat</span>
          <span class="text-2xl font-bold text-amber-700 my-1 block">{{ currentStats.terlambat }}</span>
          <span class="text-xs text-amber-600 font-medium">Tercatat Hadir</span>
        </div>
        <div class="interactive-card p-4 bg-white rounded-lg border border-slate-200 hover:border-slate-300 cursor-default">
          <span class="text-xs font-medium text-slate-500 block">Alpa / Izin / Sakit</span>
          <span class="text-2xl font-bold text-rose-700 my-1 block">{{ currentStats.absen }}</span>
          <span class="text-xs text-slate-500">Keterangan Khusus</span>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MAIN DATA TABLE                                              -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        
        <div class="p-4 sm:px-6 border-b border-slate-200 flex items-center justify-between gap-3 bg-white">
          <div class="text-xs font-semibold text-slate-800 flex items-center gap-2">
            <span>Daftar Siswa {{ selectedClass !== 'ALL' ? selectedClass : (selectedJurusan !== 'ALL' ? 'Jurusan ' + selectedJurusan : 'Seluruh Kelas') }}</span>
            <span class="text-slate-400 font-normal">({{ filteredStudents.length }} siswa)</span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50/50 border-b border-slate-200">
              <tr>
                <th class="py-3 px-4 text-center w-12 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama &amp; NISN Siswa</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kelas</th>
                <th class="py-3 px-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">JK</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Jam Scan HadirMu</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status Kehadiran</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Keterangan</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kontak Orang Tua / Wali</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="(student, index) in filteredStudents" :key="student.id" class="hover:bg-slate-50/80 transition">
                <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ index + 1 }}</td>
                
                <!-- Nama & NISN -->
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ student.name }}</div>
                  <div class="text-xs text-slate-500 font-normal mt-0.5">NISN: {{ student.nisn }}</div>
                </td>

                <!-- Kelas -->
                <td class="py-3.5 px-4 text-center">
                  <span class="text-xs font-medium text-slate-700">
                    {{ student.class_name }}
                  </span>
                </td>

                <!-- JK -->
                <td class="py-3.5 px-3 text-center">
                  <span class="text-xs font-medium text-slate-700">
                    {{ student.gender }}
                  </span>
                </td>

                <!-- Jam Scan HadirMu -->
                <td class="py-3.5 px-4 text-center">
                  <div class="font-mono text-xs font-semibold text-slate-700">
                    {{ getStudentAttendance(student).check_in_time || '-' }}
                  </div>
                  <div class="text-xs text-slate-500 font-normal mt-0.5">
                    {{ getStudentAttendance(student).date || todayDateShort }}
                  </div>
                </td>

                <!-- Status Kehadiran -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    v-if="getStudentAttendance(student).status === 'Hadir'"
                    class="text-xs font-medium text-emerald-700"
                  >
                    Hadir
                  </span>
                  <span
                    v-else-if="getStudentAttendance(student).status === 'Terlambat'"
                    class="text-xs font-medium text-amber-700"
                  >
                    Terlambat
                  </span>
                  <span
                    v-else-if="getStudentAttendance(student).status === 'Sakit'"
                    class="text-xs font-medium text-sky-700"
                  >
                    Sakit
                  </span>
                  <span
                    v-else-if="getStudentAttendance(student).status === 'Izin'"
                    class="text-xs font-medium text-indigo-700"
                  >
                    Izin
                  </span>
                  <span
                    v-else
                    class="text-xs font-medium text-rose-700"
                  >
                    Alpa
                  </span>
                </td>

                <!-- Keterangan -->
                <td class="py-3.5 px-4">
                  <span class="text-xs text-slate-600">
                    {{ getStudentAttendance(student).notes || '-' }}
                  </span>
                </td>

                <!-- Kontak Orang Tua -->
                <td class="py-3.5 px-4">
                  <div class="font-medium text-slate-800 text-xs">{{ student.parent_name || '-' }}</div>
                  <div class="text-xs text-slate-500 font-normal mt-0.5">{{ student.parent_phone || '-' }}</div>
                </td>

                <!-- Aksi: Icon Group minimalis tanpa multiple bordered buttons -->
                <td class="py-3.5 px-4 text-center">
                  <div class="inline-flex items-center justify-center gap-1 text-slate-400">
                    <!-- Ubah Status Presensi -->
                    <button
                      @click="openEditStatusModal(student)"
                      class="p-1.5 hover:text-blue-600 hover:bg-slate-100 rounded-md transition cursor-pointer"
                      title="Ubah Status Presensi Hari Ini"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </button>

                    <!-- Edit Data Profil Siswa -->
                    <button
                      @click="openEditStudentModal(student)"
                      class="p-1.5 hover:text-slate-700 hover:bg-slate-100 rounded-md transition cursor-pointer"
                      title="Edit Data Siswa"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>

                    <!-- Input Poin Pelanggaran -->
                    <button
                      @click="recordViolationForStudent(student)"
                      class="p-1.5 hover:text-amber-600 hover:bg-slate-100 rounded-md transition cursor-pointer"
                      title="Catat Poin Pelanggaran Siswa"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                    </button>

                    <!-- Kirim WA Ortu -->
                    <button
                      v-if="student.parent_phone"
                      @click="sendWhatsAppToParent(student)"
                      class="p-1.5 hover:text-emerald-600 hover:bg-slate-100 rounded-md transition cursor-pointer"
                      title="Kirim Notifikasi Presensi via WhatsApp"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                      </svg>
                    </button>

                    <!-- Hapus Siswa -->
                    <button
                      @click="deleteStudent(student)"
                      class="p-1.5 hover:text-rose-600 hover:bg-slate-100 rounded-md transition cursor-pointer"
                      title="Hapus Data Siswa"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="filteredStudents.length === 0">
                <td colspan="9" class="py-12 text-center text-slate-400 text-xs">
                  Tidak ada data siswa yang cocok dengan filter atau pencarian saat ini.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- FOOTER BAR -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
          <div>
            Menampilkan <span class="font-bold text-slate-800">{{ filteredStudents.length }}</span> dari <span class="font-bold text-slate-800">{{ dbStudents.length }}</span> total siswa terdaftar di sistem.
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="printReport"
              class="px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg font-semibold inline-flex items-center gap-1.5 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              <span>Cetak Rekap</span>
            </button>
          </div>
        </div>

      </div>

      <!-- ============================================================ -->
      <!-- MODAL 1: TAMBAH SISWA BARU (CREATE)                          -->
      <!-- ============================================================ -->
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-bold text-slate-900 text-base">Tambah Siswa Baru</h3>
              <p class="text-xs text-slate-500">Pendaftaran data induk siswa ke database</p>
            </div>
            <button @click="showAddModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitAddStudent" class="p-5 space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nomor Induk Siswa Nasional (NISN) *</label>
              <input
                v-model="addForm.nisn"
                required
                type="text"
                placeholder="Contoh: 0071234567"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
              <input
                v-model="addForm.name"
                required
                type="text"
                placeholder="Masukkan nama lengkap siswa"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Kelas / Rombel *</label>
                <select
                  v-model="addForm.class_name"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                >
                  <option v-for="cls in allClasses" :key="cls" :value="cls">{{ cls }}</option>
                </select>
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin *</label>
                <select
                  v-model="addForm.gender"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                >
                  <option value="L">Laki-laki (L)</option>
                  <option value="P">Perempuan (P)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Orang Tua / Wali</label>
              <input
                v-model="addForm.parent_name"
                type="text"
                placeholder="Contoh: Bpk. Santoso"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp Orang Tua / Wali</label>
              <input
                v-model="addForm.parent_phone"
                type="text"
                placeholder="Contoh: 081234567890"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showAddModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold rounded-lg cursor-pointer inline-flex items-center gap-1.5"
              >
                <span>{{ isSubmitting ? 'Menyimpan...' : 'Simpan Siswa' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 2: UBAH STATUS PRESENSI                                -->
      <!-- ============================================================ -->
      <div v-if="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-bold text-slate-900 text-base">Ubah Status Presensi</h3>
              <p class="text-xs text-slate-500">Pembaruan presensi hari ini</p>
            </div>
            <button @click="showStatusModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitStatus" class="p-5 space-y-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
              <div class="font-bold text-slate-900 text-sm">{{ selectedStudent.name }}</div>
              <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                NISN: {{ selectedStudent.nisn }} &bull; Kelas: {{ selectedStudent.class_name }}
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1.5">Status Kehadiran *</label>
              <div class="grid grid-cols-5 gap-1.5">
                <button
                  type="button"
                  v-for="st in ['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpa']"
                  :key="st"
                  @click="statusForm.status = st"
                  class="py-2 text-center text-xs font-semibold rounded-lg border transition cursor-pointer"
                  :class="statusForm.status === st ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'"
                >
                  {{ st }}
                </button>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Jam Scan HadirMu</label>
              <input
                v-model="statusForm.check_in_time"
                type="text"
                placeholder="Contoh: 06:45 WIB"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Keterangan / Catatan</label>
              <input
                v-model="statusForm.notes"
                type="text"
                placeholder="Contoh: Tepat Waktu, Surat Dokter terlampir, dll."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showStatusModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold rounded-lg cursor-pointer"
              >
                <span>{{ isSubmitting ? 'Menyimpan...' : 'Simpan Presensi' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 3: EDIT PROFIL SISWA (UPDATE)                          -->
      <!-- ============================================================ -->
      <div v-if="showEditStudentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-bold text-slate-900 text-base">Edit Data Siswa</h3>
              <p class="text-xs text-slate-500">Perbarui identitas atau data wali siswa</p>
            </div>
            <button @click="showEditStudentModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitEditStudent" class="p-5 space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">NISN *</label>
              <input
                v-model="editStudentForm.nisn"
                required
                type="text"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
              <input
                v-model="editStudentForm.name"
                required
                type="text"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Kelas *</label>
                <select
                  v-model="editStudentForm.class_name"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                >
                  <option v-for="cls in allClasses" :key="cls" :value="cls">{{ cls }}</option>
                </select>
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin *</label>
                <select
                  v-model="editStudentForm.gender"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                >
                  <option value="L">Laki-laki (L)</option>
                  <option value="P">Perempuan (P)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Orang Tua / Wali</label>
              <input
                v-model="editStudentForm.parent_name"
                type="text"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp Orang Tua / Wali</label>
              <input
                v-model="editStudentForm.parent_phone"
                type="text"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showEditStudentModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold rounded-lg cursor-pointer"
              >
                <span>{{ isSubmitting ? 'Memperbarui...' : 'Simpan Perubahan' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  dbStudents: {
    type: Array,
    default: () => []
  },
  classes: {
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
  },
  initialFilters: {
    type: Object,
    default: () => ({
      tingkat: 'ALL',
      jurusan: 'ALL',
      kelas: 'ALL'
    })
  }
})

const isSyncing = ref(false)
const isSubmitting = ref(false)

// State filter bertingkat
const selectedLevel = ref(props.initialFilters?.tingkat || 'ALL')
const selectedJurusan = ref(props.initialFilters?.jurusan || 'ALL')
const selectedClass = ref(props.initialFilters?.kelas || 'ALL')
const searchQuery = ref('')

const jurusanOptions = [
  { code: 'RPL', name: 'Rekayasa Perangkat Lunak' },
  { code: 'TSM', name: 'Teknik Sepeda Motor' },
  { code: 'DKV', name: 'Desain Komunikasi Visual' },
  { code: 'TKJ', name: 'Teknik Komputer Jaringan' },
  { code: 'AKL', name: 'Akuntansi & Keuangan Lembaga' },
  { code: 'OTKP', name: 'Otomatisasi Perkantoran' }
]

// Data referensi master rombel, wali kelas, guru BK
const rombelDirectory = [
  // Kelas 10 (X)
  { level: '10', jurusan: 'RPL', name: 'X RPL 1', waliKelas: 'Bpk. Kurniawan, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { level: '10', jurusan: 'RPL', name: 'X RPL 2', waliKelas: 'Ibu Sri Wahyuni, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { level: '10', jurusan: 'RPL', name: 'X RPL 3', waliKelas: 'Bpk. Bayu Hendra, M.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { level: '10', jurusan: 'TSM', name: 'X TSM 1', waliKelas: 'Bpk. Slamet Riyadi, S.T', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { level: '10', jurusan: 'TSM', name: 'X TSM 2', waliKelas: 'Bpk. Joko Susilo, S.Pd', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { level: '10', jurusan: 'DKV', name: 'X DKV 1', waliKelas: 'Ibu Dewi Larasati, S.Sn', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { level: '10', jurusan: 'DKV', name: 'X DKV 2', waliKelas: 'Bpk. Danang Prasetya, M.Sn', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { level: '10', jurusan: 'TKJ', name: 'X TKJ 1', waliKelas: 'Bpk. Ridwan Hakim, S.T', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { level: '10', jurusan: 'AKL', name: 'X AKL 1', waliKelas: 'Ibu Nur Aini, S.E', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },
  { level: '10', jurusan: 'OTKP', name: 'X OTKP 1', waliKelas: 'Ibu Endang Sulastri, S.Pd', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },

  // Kelas 11 (XI)
  { level: '11', jurusan: 'RPL', name: 'XI RPL 1', waliKelas: 'Bpk. Eko Sujarwo, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { level: '11', jurusan: 'RPL', name: 'XI RPL 2', waliKelas: 'Ibu Rahmawati, S.Kom', guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd' },
  { level: '11', jurusan: 'TSM', name: 'XI TSM 1', waliKelas: 'Bpk. Bambang Irawan, S.T', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { level: '11', jurusan: 'DKV', name: 'XI DKV 1', waliKelas: 'Ibu Fitria Kusuma, S.Pd', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { level: '11', jurusan: 'TKJ', name: 'XI TKJ 1', waliKelas: 'Bpk. Arif Wibowo, S.Kom', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { level: '11', jurusan: 'AKL', name: 'XI AKL 1', waliKelas: 'Ibu Siti Aminah, S.E', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },

  // Kelas 12 (XII)
  { level: '12', jurusan: 'RPL', name: 'XII RPL 1', waliKelas: 'Bpk. Hendro Santoso, M.Kom', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },
  { level: '12', jurusan: 'RPL', name: 'XII RPL 2', waliKelas: 'Ibu Maya Indah, S.Kom', guruBK: 'Bpk. Dwi Prasetyo, M.Pd' },
  { level: '12', jurusan: 'TSM', name: 'XII TSM 1', waliKelas: 'Bpk. Taufik Hidayat, S.T', guruBK: 'Bpk. Agus Wicaksono, S.Pd' },
  { level: '12', jurusan: 'DKV', name: 'XII DKV 1', waliKelas: 'Ibu Anisa Putri, S.Pd', guruBK: 'Ibu Ratih Anggraini, S.Psi' },
  { level: '12', jurusan: 'TKJ', name: 'XII TKJ 1', waliKelas: 'Bpk. Ferry Anggara, S.Kom', guruBK: 'Ibu Ratih Anggraini, S.Psi' }
]

const todayDateShort = computed(() => {
  const d = new Date()
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = String(d.getFullYear()).slice(-2)
  return `${day}/${month}/${year}`
})

// Unified all classes list
const allClasses = computed(() => {
  const defaults = rombelDirectory.map(r => r.name)
  const fromProps = props.classes && props.classes.length ? props.classes : []
  const fromStudents = props.dbStudents.map(s => s.class_name).filter(Boolean)
  return Array.from(new Set([...fromProps, ...fromStudents, ...defaults]))
})

// Filtered rombels list according to selectedLevel & selectedJurusan
const filteredRombels = computed(() => {
  return allClasses.value.filter(cls => {
    // Level match
    if (selectedLevel.value !== 'ALL') {
      const prefix = selectedLevel.value === '10' ? 'X ' : (selectedLevel.value === '11' ? 'XI ' : 'XII ')
      if (!cls.startsWith(prefix) && !cls.includes(selectedLevel.value)) {
        return false
      }
    }

    // Jurusan match
    if (selectedJurusan.value !== 'ALL') {
      if (!cls.toUpperCase().includes(selectedJurusan.value)) {
        return false
      }
    }

    return true
  })
})

const isFilterActive = computed(() => {
  return selectedLevel.value !== 'ALL' || selectedJurusan.value !== 'ALL' || selectedClass.value !== 'ALL' || !!searchQuery.value
})

// Event Handlers for cascading dropdowns & navigation
const updateUrlAndNavigate = () => {
  router.get('/absensi', {
    tingkat: selectedLevel.value,
    jurusan: selectedJurusan.value,
    kelas: selectedClass.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const onLevelChange = (newLevel) => {
  selectedLevel.value = newLevel
  // If current selectedClass does not match newLevel, reset selectedClass
  if (selectedClass.value !== 'ALL' && !filteredRombels.value.includes(selectedClass.value)) {
    selectedClass.value = 'ALL'
  }
  updateUrlAndNavigate()
}

const onJurusanChange = (newJurusan) => {
  selectedJurusan.value = newJurusan
  // If current selectedClass does not match newJurusan, reset selectedClass
  if (selectedClass.value !== 'ALL' && !filteredRombels.value.includes(selectedClass.value)) {
    selectedClass.value = 'ALL'
  }
  updateUrlAndNavigate()
}

const onClassChange = (newClass) => {
  selectedClass.value = newClass

  // Auto-sync level and jurusan if a specific rombel is selected
  if (newClass !== 'ALL') {
    const found = rombelDirectory.find(r => r.name === newClass)
    if (found) {
      selectedLevel.value = found.level
      selectedJurusan.value = found.jurusan
    }
  }
  updateUrlAndNavigate()
}

const resetFilters = () => {
  selectedLevel.value = 'ALL'
  selectedJurusan.value = 'ALL'
  selectedClass.value = 'ALL'
  searchQuery.value = ''
  updateUrlAndNavigate()
}

// Active Rombel Card Banner info
const activeRombelInfo = computed(() => {
  if (selectedClass.value !== 'ALL') {
    const meta = rombelDirectory.find(r => r.name === selectedClass.value) || {
      name: selectedClass.value,
      jurusan: selectedJurusan.value !== 'ALL' ? selectedJurusan.value : 'Umum',
      waliKelas: 'Bpk/Ibu Wali Kelas',
      guruBK: 'Guru BK Pembimbing'
    }

    const students = filteredStudents.value
    const present = students.filter(s => getStudentAttendance(s).status === 'Hadir').length
    const rate = students.length > 0 ? Math.round((present / students.length) * 100) : 100

    return {
      ...meta,
      attendanceRate: rate
    }
  }

  if (selectedJurusan.value !== 'ALL' || selectedLevel.value !== 'ALL') {
    const jurObj = jurusanOptions.find(j => j.code === selectedJurusan.value)
    const levelLabel = selectedLevel.value !== 'ALL' ? `Kelas ${selectedLevel.value}` : 'Semua Tingkat'
    const jurLabel = jurObj ? jurObj.name : (selectedJurusan.value !== 'ALL' ? selectedJurusan.value : 'Semua Jurusan')

    const students = filteredStudents.value
    const present = students.filter(s => getStudentAttendance(s).status === 'Hadir').length
    const rate = students.length > 0 ? Math.round((present / students.length) * 100) : 100

    return {
      name: `${levelLabel} • ${jurLabel}`,
      jurusan: selectedJurusan.value !== 'ALL' ? selectedJurusan.value : 'Semua Jurusan',
      waliKelas: 'Koordinator Jurusan',
      guruBK: 'Dra. Hj. Nurul Hidayati, M.Pd',
      attendanceRate: rate
    }
  }

  return null
})

// Helper to get today's attendance record of student
const getStudentAttendance = (student) => {
  if (student.attendances && student.attendances.length > 0) {
    return student.attendances[0]
  }
  return {
    status: 'Hadir',
    check_in_time: '06:45 WIB',
    notes: 'Tepat Waktu',
    date: todayDateShort.value
  }
}

// Filter students based on level, jurusan, class, and search query
const filteredStudents = computed(() => {
  return props.dbStudents.filter(s => {
    // Level filter
    if (selectedLevel.value !== 'ALL') {
      const levelMatches = s.level === selectedLevel.value ||
        (selectedLevel.value === '10' && s.class_name.startsWith('X ')) ||
        (selectedLevel.value === '11' && s.class_name.startsWith('XI ')) ||
        (selectedLevel.value === '12' && s.class_name.startsWith('XII '))
      if (!levelMatches) return false
    }

    // Jurusan filter
    if (selectedJurusan.value !== 'ALL') {
      const jurusanMatches = (s.major && s.major.toUpperCase() === selectedJurusan.value) ||
        (s.class_name && s.class_name.toUpperCase().includes(selectedJurusan.value))
      if (!jurusanMatches) return false
    }

    // Class filter
    if (selectedClass.value !== 'ALL' && s.class_name !== selectedClass.value) {
      return false
    }

    // Search query filter
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const nameMatch = s.name.toLowerCase().includes(q)
      const nisnMatch = s.nisn ? s.nisn.toLowerCase().includes(q) : false
      const classMatch = s.class_name ? s.class_name.toLowerCase().includes(q) : false
      if (!nameMatch && !nisnMatch && !classMatch) return false
    }

    return true
  })
})

// Dynamic stats for filtered list
const currentStats = computed(() => {
  const list = filteredStudents.value
  let hadir = 0
  let terlambat = 0
  let absen = 0

  list.forEach(s => {
    const att = getStudentAttendance(s)
    if (att.status === 'Hadir') hadir++
    else if (att.status === 'Terlambat') terlambat++
    else absen++
  })

  return {
    total: list.length,
    hadir,
    terlambat,
    absen
  }
})

// ==========================================
// MODAL: TAMBAH SISWA
// ==========================================
const showAddModal = ref(false)
const addForm = ref({
  nisn: '',
  name: '',
  class_name: 'X RPL 1',
  gender: 'L',
  parent_name: '',
  parent_phone: ''
})

const openAddStudentModal = () => {
  let defaultClass = 'X RPL 1'
  if (selectedClass.value !== 'ALL') {
    defaultClass = selectedClass.value
  } else if (filteredRombels.value.length > 0) {
    defaultClass = filteredRombels.value[0]
  }

  addForm.value = {
    nisn: '',
    name: '',
    class_name: defaultClass,
    gender: 'L',
    parent_name: '',
    parent_phone: ''
  }
  showAddModal.value = true
}

const submitAddStudent = () => {
  isSubmitting.value = true
  router.post('/siswa', addForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      showAddModal.value = false
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

// ==========================================
// MODAL: UBAH STATUS PRESENSI
// ==========================================
const showStatusModal = ref(false)
const selectedStudent = ref({})
const statusForm = ref({
  student_id: null,
  status: 'Hadir',
  check_in_time: '',
  notes: ''
})

const openEditStatusModal = (student) => {
  selectedStudent.value = student
  const att = getStudentAttendance(student)
  statusForm.value = {
    student_id: student.id,
    status: att.status || 'Hadir',
    check_in_time: att.check_in_time || '06:45 WIB',
    notes: att.notes || 'Tepat Waktu'
  }
  showStatusModal.value = true
}

const submitStatus = () => {
  isSubmitting.value = true
  router.post('/absensi/status', statusForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      showStatusModal.value = false
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

// ==========================================
// MODAL: EDIT DATA SISWA
// ==========================================
const showEditStudentModal = ref(false)
const editStudentForm = ref({
  id: null,
  nisn: '',
  name: '',
  class_name: '',
  gender: 'L',
  parent_name: '',
  parent_phone: ''
})

const openEditStudentModal = (student) => {
  editStudentForm.value = {
    id: student.id,
    nisn: student.nisn || '',
    name: student.name || '',
    class_name: student.class_name || 'X RPL 1',
    gender: student.gender || 'L',
    parent_name: student.parent_name || '',
    parent_phone: student.parent_phone || ''
  }
  showEditStudentModal.value = true
}

const submitEditStudent = () => {
  isSubmitting.value = true
  router.put(`/siswa/${editStudentForm.value.id}`, editStudentForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      showEditStudentModal.value = false
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

// ==========================================
// DELETE SISWA (CRUD DELETE REAL DATABASE)
// ==========================================
const deleteStudent = (student) => {
  if (confirm(`Apakah Anda yakin ingin menghapus data siswa "${student.name}" (NISN: ${student.nisn})? Data presensi dan pelanggaran terkait akan ikut terhapus.`)) {
    router.delete(`/siswa/${student.id}`, {
      preserveScroll: true
    })
  }
}

// ==========================================
// ACTION LAINNYA
// ==========================================
const recordViolationForStudent = (student) => {
  router.visit('/verifikasi', {
    data: {
      nama: student.name,
      nisn: student.nisn,
      kelas: student.class_name,
      siswa_id: student.id
    }
  })
}

const sendWhatsAppToParent = (student) => {
  if (!student.parent_phone) return
  const att = getStudentAttendance(student)
  const phone = student.parent_phone.replace(/[^0-9]/g, '')
  const text = encodeURIComponent(
    `Yth. Bapak/Ibu ${student.parent_name || 'Orang Tua/Wali'} (Wali dari ${student.name} - ${student.class_name}), kami menginformasikan laporan presensi harian pada tanggal ${att.date || todayDateShort.value}: Status: ${att.status} (Waktu: ${att.check_in_time}). Keterangan: ${att.notes}. Terima kasih atas kerja samanya. - Tim BK SMK PGRI 05 JEMBER`
  )
  window.open(`https://wa.me/${phone.startsWith('0') ? '62' + phone.slice(1) : phone}?text=${text}`, '_blank')
}

const syncAttendance = () => {
  isSyncing.value = true
  router.post('/absensi/sync', {}, {
    preserveScroll: true,
    onFinish: () => {
      isSyncing.value = false
    }
  })
}

const printReport = () => {
  window.print()
}
</script>
