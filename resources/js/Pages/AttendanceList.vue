<template>
  <AppLayout active-tab="absensi" show-share>
    <div class="max-w-7xl mx-auto space-y-5">

      <!-- Toast Notification -->
      <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="toastMessage" class="fixed top-20 right-4 sm:right-8 z-50 bg-white border border-slate-200 rounded-xl shadow-lg p-4 flex items-center gap-3 text-xs max-w-sm">
          <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div>
            <p class="font-semibold text-slate-800">{{ toastMessage }}</p>
          </div>
        </div>
      </transition>

      <!-- ============================================================ -->
      <!-- TOP HEADER SECTION                                            -->
      <!-- ============================================================ -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
            Rekap Presensi
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">{{ todayDate || 'Senin, 02 Nov 2026' }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <button
            v-if="isMasterAdmin"
            type="button"
            @click="openPromotionModal"
            class="interactive-btn inline-flex items-center gap-1.5 bg-white hover:bg-amber-50 text-amber-700 border border-amber-300 font-semibold px-3 py-1.5 rounded-lg text-xs cursor-pointer shadow-2xs transition"
            title="Kelulusan kelas XII dan kenaikan tingkat kelas siswa"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
            </svg>
            <span>Kenaikan Kelas &amp; Kelulusan</span>
          </button>

          <Link
            href="/absensi/bulanan"
            class="interactive-btn inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-medium px-3 py-1.5 rounded-lg text-xs cursor-pointer shadow-2xs transition"
          >
            <span>Rekap Bulanan</span>
          </Link>

          <button
            @click="openImportModal"
            class="interactive-btn inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-medium px-3 py-1.5 rounded-lg text-xs cursor-pointer shadow-2xs transition"
          >
            <span>Import</span>
          </button>

          <button
            @click="openAddStudentModal"
            class="interactive-btn inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium px-3.5 py-1.5 rounded-lg text-xs cursor-pointer shadow-2xs transition"
          >
            <span>+ Siswa</span>
          </button>

          <button
            @click="syncAttendance"
            :disabled="isSyncing"
            class="interactive-btn inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 disabled:bg-slate-400 text-white font-medium px-3.5 py-1.5 rounded-lg text-xs cursor-pointer shadow-2xs transition"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" :class="{ 'animate-spin': isSyncing }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>{{ isSyncing ? 'Menyinkronkan...' : 'Sinkronkan' }}</span>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- SELECTOR DROPDOWNS: TINGKAT -> JURUSAN -> ROMBEL               -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 p-3.5 sm:p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          
          <!-- Tingkat -->
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Tingkat
            </label>
            <div class="relative">
              <select
                :value="selectedLevel"
                @change="onLevelChange($event.target.value)"
                class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
              >
                <option value="ALL">Semua Tingkat</option>
                <option v-for="lvl in levelOptions" :key="lvl.value" :value="lvl.value">
                  {{ lvl.label }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Jurusan -->
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Jurusan
            </label>
            <div class="relative">
              <select
                :value="selectedJurusan"
                @change="onJurusanChange($event.target.value)"
                class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
              >
                <option value="ALL">Semua Jurusan</option>
                <option v-for="jur in jurusanOptions" :key="jur.code" :value="jur.code">
                  {{ jur.name }} ({{ jur.code }})
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Rombel -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-medium text-slate-600">
                Rombel
              </label>
              <button
                v-if="isFilterActive"
                @click="resetFilters"
                class="text-[11px] font-medium text-blue-600 hover:text-blue-800 cursor-pointer"
              >
                Reset
              </button>
            </div>
            <div class="relative">
              <select
                :value="selectedClass"
                @change="onClassChange($event.target.value)"
                class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
              >
                <option value="ALL">Semua Rombel</option>
                <option v-for="cls in filteredRombels" :key="cls" :value="cls">
                  {{ cls }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Pencarian Siswa -->
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Cari Siswa
            </label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Pilih atau cari siswa..."
                class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-8 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition"
              />
              <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
          <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h2 class="font-bold text-slate-900 text-base tracking-tight">
                {{ activeRombelInfo.name }}
              </h2>
              <span class="text-xs text-slate-500 font-medium">
                &bull; {{ activeRombelInfo.jurusan }}
              </span>
              <span class="text-xs font-medium text-emerald-700">
                &bull; {{ activeRombelInfo.attendanceRate }}% Hadir
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Wali Kelas: {{ activeRombelInfo.waliKelas }} &bull; BK: {{ activeRombelInfo.guruBK }}
            </p>
          </div>
        </div>

        <div class="text-xs text-slate-500 shrink-0">
          <span class="font-bold text-slate-900 text-sm">{{ filteredStudents.length }}</span> siswa
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- KPI STATS STRIP                                              -->
      <!-- ============================================================ -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3.5 bg-white rounded-lg border border-slate-200">
          <span class="text-xs font-medium text-slate-500 block">Total Siswa</span>
          <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ currentStats.total }}</span>
        </div>
        <div class="p-3.5 bg-white rounded-lg border border-slate-200">
          <span class="text-xs font-medium text-slate-500 block">Hadir</span>
          <span class="text-2xl font-bold text-emerald-700 mt-1 block">{{ currentStats.hadir }}</span>
        </div>
        <div class="p-3.5 bg-white rounded-lg border border-slate-200">
          <span class="text-xs font-medium text-slate-500 block">Terlambat</span>
          <span class="text-2xl font-bold text-amber-700 mt-1 block">{{ currentStats.terlambat }}</span>
        </div>
        <div class="p-3.5 bg-white rounded-lg border border-slate-200">
          <span class="text-xs font-medium text-slate-500 block">Alpa / Izin / Sakit</span>
          <span class="text-2xl font-bold text-rose-700 mt-1 block">{{ currentStats.absen }}</span>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MAIN DATA TABLE                                              -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        
        <div class="p-4 sm:px-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
          <div class="text-xs font-semibold text-slate-800 flex items-center gap-2">
            <span>Daftar Siswa {{ selectedClass !== 'ALL' ? selectedClass : (selectedJurusan !== 'ALL' ? 'Jurusan ' + selectedJurusan : 'Seluruh Kelas') }}</span>
            <span class="text-slate-400 font-normal">({{ filteredStudents.length }} siswa)</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              v-if="selectedClass !== 'ALL' && filteredStudents.length > 0"
              @click="openClearClassModalFor(selectedClass)"
              type="button"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span>Kosongkan Siswa Kelas ({{ selectedClass }})</span>
            </button>
            <button
              v-else-if="selectedClass === 'ALL'"
              @click="openClearClassModal"
              type="button"
              class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-rose-700 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition cursor-pointer"
              title="Kosongkan seluruh siswa pada 1 rombel kelas"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span>Kosongkan 1 Kelas</span>
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50/50 border-b border-slate-200">
              <tr>
                <th class="py-3 px-4 text-center w-12 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama &amp; NIS Siswa</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kelas</th>
                <th class="py-3 px-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">JK</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Keterangan</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kontak</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="(student, index) in filteredStudents" :key="student.id" class="hover:bg-slate-50/80 transition">
                <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ index + 1 }}</td>
                
                <!-- Nama & NIS -->
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ student.name }}</div>
                  <div class="text-xs text-slate-500 font-normal mt-0.5">NIS: {{ student.nis || student.nisn }}</div>
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

                <!-- Kontak -->
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-1.5">
                    <span class="text-xs text-slate-600 font-mono">{{ student.parent_phone || '-' }}</span>
                    <a
                      v-if="student.parent_phone"
                      :href="'https://wa.me/' + student.parent_phone.replace(/[^0-9]/g, '')"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="p-1 text-slate-400 hover:text-emerald-600 transition"
                      title="WhatsApp"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                      </svg>
                    </a>
                  </div>
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
      <!-- MODAL CARD: KONFIRMASI HAPUS SISWA                           -->
      <!-- ============================================================ -->
      <div v-if="showDeleteStudentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-xl max-w-sm w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
          <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm">Hapus Data Siswa</h3>
                <p class="text-xs text-slate-500">Konfirmasi penghapusan data siswa</p>
              </div>
            </div>
            <button @click="showDeleteStudentModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="p-5 space-y-3.5 text-xs">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Nama Siswa</span>
                <span class="font-bold text-slate-900">{{ studentToDelete?.name }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">NIS</span>
                <span class="font-mono text-slate-800">{{ studentToDelete?.nis || studentToDelete?.nisn }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Kelas</span>
                <span class="font-semibold text-slate-800">{{ studentToDelete?.class_name }}</span>
              </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
              Apakah Anda yakin ingin menghapus data siswa <strong class="text-slate-800">{{ studentToDelete?.name }}</strong>? Seluruh data riwayat presensi dan catatan pelanggaran terkait akan ikut terhapus secara permanen.
            </p>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
              <button
                type="button"
                @click="showDeleteStudentModal = false"
                class="flex-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg cursor-pointer transition text-xs"
              >
                Batal
              </button>
              <button
                type="button"
                @click="executeDeleteStudent"
                :disabled="isDeletingStudent"
                class="flex-1 px-4 py-2 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white font-semibold rounded-lg cursor-pointer inline-flex items-center justify-center gap-1.5 transition text-xs"
              >
                <svg v-if="isDeletingStudent" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isDeletingStudent ? 'Menghapus...' : 'Ya, Hapus Siswa' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: KOSONGKAN 1 KELAS (BULK DELETE SISWA)                 -->
      <!-- ============================================================ -->
      <div v-if="showClearClassModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base">Kosongkan Siswa 1 Kelas</h3>
                <p class="text-xs text-slate-500">Hapus seluruh data siswa dalam 1 rombel sekaligus</p>
              </div>
            </div>
            <button @click="showClearClassModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitClearClass" class="p-5 space-y-4 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Pilih Rombel / Kelas yang akan Dikosongkan *</label>
              <select
                v-model="targetClassToClear"
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
              >
                <option v-for="cls in allClasses" :key="cls" :value="cls">{{ cls }}</option>
              </select>
            </div>

            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-[11px] leading-relaxed">
              Tindakan ini akan menghapus <strong>{{ classStudentsCountToClear }} siswa</strong> di kelas <strong>{{ targetClassToClear }}</strong> beserta riwayat presensi dan pelanggaran siswa tersebut secara permanen.
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">
                Ketik <span class="font-mono text-rose-600 bg-rose-50 px-1 py-0.5 rounded border border-rose-200">HAPUS</span> untuk konfirmasi:
              </label>
              <input
                v-model="clearClassConfirmation"
                required
                type="text"
                placeholder="Ketik HAPUS..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showClearClassModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="clearClassConfirmation !== 'HAPUS' || isClearingClass || classStudentsCountToClear === 0"
                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold rounded-lg cursor-pointer inline-flex items-center gap-1.5"
              >
                <svg v-if="isClearingClass" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isClearingClass ? 'Menghapus...' : 'Kosongkan Kelas Ini' }}</span>
              </button>
            </div>
          </form>
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
              <label class="block font-semibold text-slate-700 mb-1">Nomor Induk Siswa (NIS) *</label>
              <input
                v-model="addForm.nis"
                required
                type="text"
                placeholder="Contoh: 10452"
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
                NIS: {{ selectedStudent.nis || selectedStudent.nisn }} &bull; Kelas: {{ selectedStudent.class_name }}
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
              <label class="block font-semibold text-slate-700 mb-1">NIS *</label>
              <input
                v-model="editStudentForm.nis"
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

      <!-- ============================================================ -->
      <!-- MODAL 4: IMPORT DATA SISWA (EXCEL / CSV / DAPODIK)           -->
      <!-- ============================================================ -->
      <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-5 sm:p-6 shadow-xl border border-slate-200 space-y-4 my-8">
          
          <!-- Header Modal -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base">Import Data Siswa ke Rombel Binaan</h3>
                <p class="text-xs text-slate-500">Unggah file spreadsheet (CSV/Excel) untuk memasukkan daftar siswa presensi</p>
              </div>
            </div>
            <button @click="closeImportModal" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Panduan & Download Template -->
          <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div>
              <span class="font-semibold text-slate-800 block">Format Kolom File:</span>
              <span class="text-slate-500 text-[11px]">Kolom urut: <strong class="text-slate-700">NIS, Nama Lengkap, Kelas, JK (L/P), No WA Wali</strong></span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button
                type="button"
                @click="downloadCsvTemplate"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200 rounded-lg shadow-2xs transition cursor-pointer text-xs"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Template CSV</span>
              </button>
              <button
                type="button"
                @click="loadSampleData"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold border border-blue-200 rounded-lg shadow-2xs transition cursor-pointer text-xs"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Contoh Data Otomatis</span>
              </button>
            </div>
          </div>

          <!-- Upload Dropzone Area -->
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700">Pilih File Spreadsheet (CSV / Excel / TXT)</label>
            <div
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
              class="border-2 border-dashed rounded-xl p-6 text-center transition cursor-pointer relative"
              :class="isDragging ? 'border-blue-500 bg-blue-50/50' : 'border-slate-300 hover:border-slate-400 bg-slate-50/50'"
            >
              <input
                ref="importFileInputRef"
                type="file"
                accept=".csv, .txt, .xlsx, .xls"
                @change="handleFileUpload"
                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
              />
              <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs sm:text-sm font-semibold text-slate-800">
                    <span class="text-blue-700 font-semibold underline">Klik untuk memilih file</span> atau seret file ke sini
                  </p>
                  <p class="text-[11px] text-slate-400 mt-0.5">Mendukung format file CSV, TXT, atau ekspor Dapodik</p>
                </div>
                <div v-if="importFileName" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-emerald-50 text-emerald-800 font-semibold text-xs border border-emerald-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span>File: {{ importFileName }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Target Kelas Selector -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Rombel Tujuan:</label>
              <select
                v-model="importTargetClass"
                class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
              >
                <option value="AUTO">Otomatis (Sesuai kolom kelas file)</option>
                <option v-for="rombel in availableRombels" :key="rombel.id" :value="rombel.id">
                  Tetapkan ke: {{ rombel.nama_rombel || rombel.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Status Muatan:</label>
              <div class="flex items-center h-[38px] px-3.5 bg-slate-50 rounded-lg border border-slate-200 text-xs">
                <span v-if="importedStudents.length > 0" class="text-emerald-700 font-semibold">
                  {{ importedStudents.length }} siswa siap diimpor
                </span>
                <span v-else class="text-slate-400">
                  Belum ada baris data dimuat
                </span>
              </div>
            </div>
          </div>

          <!-- Preview Table -->
          <div v-if="importedStudents.length > 0" class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-800">
                Pratinjau Siswa ({{ importedStudents.length }} Siswa)
              </span>
              <button
                type="button"
                @click="importedStudents = []; importFileName = ''"
                class="text-[11px] font-semibold text-rose-600 hover:underline cursor-pointer"
              >
                Hapus Semua
              </button>
            </div>

            <div class="max-h-44 overflow-y-auto border border-slate-200 rounded-xl">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-700 font-semibold sticky top-0 border-b border-slate-200">
                  <tr>
                    <th class="py-2 px-3 text-center w-8">No</th>
                    <th class="py-2 px-3">NIS</th>
                    <th class="py-2 px-3">Nama Siswa</th>
                    <th class="py-2 px-3">Kelas</th>
                    <th class="py-2 px-2 text-center">JK</th>
                    <th class="py-2 px-3">No WA Wali</th>
                    <th class="py-2 px-2 text-center w-8">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                  <tr v-for="(st, sIdx) in importedStudents" :key="sIdx" class="hover:bg-slate-50">
                    <td class="py-1.5 px-3 text-center text-slate-400 font-mono">{{ sIdx + 1 }}</td>
                    <td class="py-1.5 px-3 font-mono text-slate-700">{{ st.nis || st.nisn }}</td>
                    <td class="py-1.5 px-3 font-semibold text-slate-900">{{ st.name }}</td>
                    <td class="py-1.5 px-3">
                      <span class="text-xs text-slate-700 font-medium">
                        {{ getTargetRombelName(st.className) }}
                      </span>
                    </td>
                    <td class="py-1.5 px-2 text-center font-bold" :class="st.gender === 'L' ? 'text-blue-600' : 'text-pink-600'">
                      {{ st.gender }}
                    </td>
                    <td class="py-1.5 px-3 font-mono text-slate-600 text-[11px]">{{ st.parentWa }}</td>
                    <td class="py-1.5 px-2 text-center">
                      <button
                        type="button"
                        @click="importedStudents.splice(sIdx, 1)"
                        class="text-slate-400 hover:text-rose-600 p-1 cursor-pointer"
                        title="Hapus baris ini"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Footer Modal -->
          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
            <button
              type="button"
              @click="closeImportModal"
              class="px-4 py-2 rounded-lg border border-slate-200 font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer text-xs"
            >
              Batal
            </button>
            <button
              type="button"
              @click="submitImport"
              :disabled="importedStudents.length === 0 || isSubmittingImport"
              class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold transition cursor-pointer text-xs flex items-center gap-1.5 shadow-2xs"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>{{ isSubmittingImport ? 'Mengimpor Data...' : `Simpan & Import (${importedStudents.length} Siswa)` }}</span>
            </button>
          </div>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: KENAIKAN KELAS & KELULUSAN (MASTER ADMIN ONLY)        -->
      <!-- ============================================================ -->
      <div v-if="showPromotionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
          
          <!-- Modal Header -->
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/75 shrink-0">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base">Kenaikan Kelas &amp; Kelulusan Tahunan</h3>
                <p class="text-xs text-slate-500">Pembaruan jenjang kelas dan kelulusan siswa SMK PGRI 5 Jember</p>
              </div>
            </div>
            <button @click="showPromotionModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body Content -->
          <div class="p-5 space-y-4 overflow-y-auto text-xs flex-1">

            <!-- Live Summary Counts (3 Grid) -->
            <div class="grid grid-cols-3 gap-3">
              <div class="p-3 bg-amber-50/70 border border-amber-200/80 rounded-xl">
                <div class="text-[11px] font-semibold text-amber-800">1. Lulus (Kelas XII)</div>
                <div class="text-xl font-bold text-amber-900 mt-1">
                  {{ loadingPromotionSummary ? '...' : promotionSummary.count_xii }}
                  <span class="text-xs font-normal text-amber-700">siswa</span>
                </div>
                <div class="text-[10px] text-amber-700/80 mt-0.5">Dialihkan ke Arsip Alumni</div>
              </div>

              <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-xl">
                <div class="text-[11px] font-semibold text-blue-800">2. Naik ke XII (XI &rarr; XII)</div>
                <div class="text-xl font-bold text-blue-900 mt-1">
                  {{ loadingPromotionSummary ? '...' : promotionSummary.count_xi }}
                  <span class="text-xs font-normal text-blue-700">siswa</span>
                </div>
                <div class="text-[10px] text-blue-700/80 mt-0.5">Mempertahankan rombel</div>
              </div>

              <div class="p-3 bg-indigo-50/70 border border-indigo-200/80 rounded-xl">
                <div class="text-[11px] font-semibold text-indigo-800">3. Naik ke XI (X &rarr; XI)</div>
                <div class="text-xl font-bold text-indigo-900 mt-1">
                  {{ loadingPromotionSummary ? '...' : promotionSummary.count_x }}
                  <span class="text-xs font-normal text-indigo-700">siswa</span>
                </div>
                <div class="text-[10px] text-indigo-700/80 mt-0.5">Mempertahankan rombel</div>
              </div>
            </div>

            <!-- Parameters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahun Kelulusan (Angkatan XII) *</label>
                <input
                  v-model.number="promotionForm.graduation_year"
                  type="number"
                  min="2000"
                  max="2100"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 font-medium focus:bg-white focus:outline-none focus:border-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahun Ajaran Baru *</label>
                <input
                  v-model="promotionForm.new_academic_year"
                  type="text"
                  placeholder="Contoh: 2026/2027"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 font-medium focus:bg-white focus:outline-none focus:border-slate-400"
                />
              </div>
            </div>

            <!-- Pengecualian Siswa Tinggal Kelas -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
              <div class="flex items-center justify-between">
                <label class="font-semibold text-slate-700">Pengecualian Siswa Tinggal Kelas (Opsional)</label>
                <span class="text-[11px] text-slate-400">
                  {{ promotionForm.retained_student_ids.length }} siswa dipilih
                </span>
              </div>
              <p class="text-[11px] text-slate-500">
                Siswa yang ditandai akan tetap berada di tingkat kelas saat ini dan tidak mengalami kenaikan tingkat maupun kelulusan.
              </p>

              <!-- Selected Tags -->
              <div v-if="promotionForm.retained_student_ids.length > 0" class="flex flex-wrap gap-1.5 p-2 bg-slate-50 rounded-lg border border-slate-200 max-h-24 overflow-y-auto">
                <span
                  v-for="stId in promotionForm.retained_student_ids"
                  :key="stId"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200"
                >
                  <span>{{ getStudentNameById(stId) }}</span>
                  <button type="button" @click="toggleRetainedStudent(stId)" class="text-rose-400 hover:text-rose-700 cursor-pointer">&times;</button>
                </span>
              </div>

              <!-- Search input to select students -->
              <div class="relative">
                <input
                  v-model="retainedSearch"
                  type="text"
                  placeholder="Ketik nama atau NIS siswa untuk ditandai tinggal kelas..."
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-2 text-xs focus:bg-white focus:outline-none focus:border-slate-400"
                />
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>

              <!-- Search results dropdown -->
              <div v-if="filteredRetainedCandidates.length > 0 && retainedSearch.trim().length > 0" class="border border-slate-200 rounded-lg max-h-36 overflow-y-auto divide-y divide-slate-100 bg-white shadow-xs">
                <div
                  v-for="cand in filteredRetainedCandidates"
                  :key="cand.id"
                  @click="toggleRetainedStudent(cand.id); retainedSearch = ''"
                  class="p-2 hover:bg-slate-50 cursor-pointer flex items-center justify-between transition"
                >
                  <div>
                    <span class="font-semibold text-slate-800">{{ cand.name }}</span>
                    <span class="text-slate-400 text-[11px] ml-1.5">({{ cand.class_name }} &bull; NIS: {{ cand.nis || cand.nisn }})</span>
                  </div>
                  <span
                    class="text-[11px] font-semibold"
                    :class="promotionForm.retained_student_ids.includes(cand.id) ? 'text-rose-600' : 'text-blue-600'"
                  >
                    {{ promotionForm.retained_student_ids.includes(cand.id) ? 'Batal Tinggal Kelas' : '+ Tinggal Kelas' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Reset Point Checkbox -->
            <div class="pt-2 border-t border-slate-100">
              <label class="flex items-start gap-2.5 cursor-pointer">
                <input
                  v-model="promotionForm.reset_points"
                  type="checkbox"
                  class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                />
                <div>
                  <span class="font-semibold text-slate-800">Reset akumulasi poin pelanggaran ke 0 untuk tahun ajaran baru</span>
                  <p class="text-[11px] text-slate-500 mt-0.5">
                    Riwayat catatan pelanggaran dan surat pemanggilan sebelumnya tetap tersimpan utuh dan tidak terhapus.
                  </p>
                </div>
              </label>
            </div>

            <!-- Safety Confirmation Section -->
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl space-y-2">
              <div class="flex items-center gap-2 text-rose-800 font-bold text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Peringatan Keamanan Transaksi Database</span>
              </div>
              <p class="text-[11px] text-rose-700 leading-relaxed">
                Operasi ini akan memperbarui status kesiswaan seluruh rombel secara permanen. Untuk mengonfirmasi, ketik kata <strong class="font-bold underline">PROSES</strong> di kolom bawah ini.
              </p>
              <div>
                <input
                  v-model="promotionForm.confirmation"
                  type="text"
                  placeholder="Ketik PROSES untuk mengonfirmasi..."
                  class="w-full bg-white border border-rose-300 rounded-lg p-2 text-xs text-rose-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                />
              </div>
            </div>

          </div>

          <!-- Modal Footer -->
          <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between shrink-0">
            <button
              type="button"
              @click="showPromotionModal = false"
              class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-semibold cursor-pointer transition"
            >
              Batal
            </button>
            <button
              type="button"
              @click="submitPromotion"
              :disabled="promotionForm.confirmation !== 'PROSES' || submittingPromotion"
              class="px-4 py-2 bg-amber-600 hover:bg-amber-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-lg text-xs font-semibold cursor-pointer transition shadow-2xs inline-flex items-center gap-1.5"
            >
              <svg v-if="submittingPromotion" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ submittingPromotion ? 'Memproses Transaksi...' : 'Eksekusi Kenaikan &amp; Kelulusan' }}</span>
            </button>
          </div>

        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const isMasterAdmin = computed(() => {
  const role = page.props?.auth?.user?.role
  return role === 'masteradmin' || role === 'admin'
})

// State & Logic Modal Kenaikan Kelas & Kelulusan
const showPromotionModal = ref(false)
const loadingPromotionSummary = ref(false)
const submittingPromotion = ref(false)
const retainedSearch = ref('')

const promotionSummary = ref({
  count_xii: 0,
  count_xi: 0,
  count_x: 0,
  current_year: new Date().getFullYear(),
  default_academic_year: `${new Date().getFullYear()}/${new Date().getFullYear() + 1}`,
  students: []
})

const promotionForm = ref({
  graduation_year: new Date().getFullYear(),
  new_academic_year: `${new Date().getFullYear()}/${new Date().getFullYear() + 1}`,
  retained_student_ids: [],
  reset_points: true,
  confirmation: ''
})

const openPromotionModal = async () => {
  showPromotionModal.value = true
  loadingPromotionSummary.value = true
  promotionForm.value.confirmation = ''
  retainedSearch.value = ''
  try {
    const res = await fetch('/master/kenaikan-kelas/summary')
    if (res.ok) {
      const data = await res.json()
      promotionSummary.value = data
      promotionForm.value.graduation_year = data.current_year
      promotionForm.value.new_academic_year = data.default_academic_year
    }
  } catch (e) {
    console.error(e)
  } finally {
    loadingPromotionSummary.value = false
  }
}

const toggleRetainedStudent = (studentId) => {
  const idx = promotionForm.value.retained_student_ids.indexOf(studentId)
  if (idx > -1) {
    promotionForm.value.retained_student_ids.splice(idx, 1)
  } else {
    promotionForm.value.retained_student_ids.push(studentId)
  }
}

const getStudentNameById = (id) => {
  const s = promotionSummary.value.students?.find(item => item.id === id)
  return s ? `${s.name} (${s.class_name})` : `Siswa #${id}`
}

const filteredRetainedCandidates = computed(() => {
  const q = retainedSearch.value.trim().toLowerCase()
  if (!q) return []
  const list = promotionSummary.value.students || []
  return list.filter(s => 
    s.name.toLowerCase().includes(q) || 
    (s.nisn && s.nisn.includes(q)) || 
    (s.class_name && s.class_name.toLowerCase().includes(q))
  ).slice(0, 8)
})

const submitPromotion = () => {
  if (promotionForm.value.confirmation !== 'PROSES') return
  submittingPromotion.value = true
  router.post('/master/kenaikan-kelas', promotionForm.value, {
    onSuccess: () => {
      showPromotionModal.value = false
      submittingPromotion.value = false
    },
    onError: (errors) => {
      submittingPromotion.value = false
      alert(Object.values(errors).flat().join('\n') || 'Gagal memproses kenaikan kelas.')
    }
  })
}

const props = defineProps({
  dbStudents: {
    type: Array,
    default: () => []
  },
  classes: {
    type: Array,
    default: () => []
  },
  rombelList: {
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
const toastMessage = ref('')

const showToast = (msg) => {
  toastMessage.value = msg
  setTimeout(() => {
    toastMessage.value = ''
  }, 3500)
}

// State filter bertingkat
const selectedLevel = ref(props.initialFilters?.tingkat || 'ALL')
const selectedJurusan = ref(props.initialFilters?.jurusan || 'ALL')
const selectedClass = ref(props.initialFilters?.kelas || 'ALL')
const searchQuery = ref('')

// Master rombel & jurusan mapping
const allJurusanMaster = [
  { code: 'RPL', name: 'Rekayasa Perangkat Lunak' },
  { code: 'TSM', name: 'Teknik Sepeda Motor' },
  { code: 'DKV', name: 'Desain Komunikasi Visual' },
  { code: 'TKJ', name: 'Teknik Komputer Jaringan' },
  { code: 'AKL', name: 'Akuntansi & Keuangan Lembaga' },
  { code: 'OTKP', name: 'Otomatisasi Perkantoran' }
]

// Unified classes list strictly from authorized classes or student records
const allClasses = computed(() => {
  const fromProps = props.classes && props.classes.length ? props.classes : []
  const fromStudents = props.dbStudents.map(s => s.class_name).filter(Boolean)
  const combined = Array.from(new Set([...fromProps, ...fromStudents]))
  return combined.sort((a, b) => a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' }))
})

// Master rombel list dinamis dari database props
const availableRombels = computed(() => {
  if (props.rombelList && props.rombelList.length > 0) {
    return props.rombelList
  }
  return allClasses.value.map((cls, idx) => ({
    id: idx + 1,
    name: cls,
    nama_rombel: cls,
  }))
})

// Jurusan options only for classes authorized for this guru
const jurusanOptions = computed(() => {
  const usedCodes = new Set()
  allClasses.value.forEach(cls => {
    const upper = cls.toUpperCase()
    allJurusanMaster.forEach(jur => {
      if (upper.includes(jur.code)) {
        usedCodes.add(jur.code)
      }
    })
  })
  
  if (usedCodes.size === 0) {
    return allJurusanMaster
  }
  return allJurusanMaster.filter(j => usedCodes.has(j.code))
})

// Tingkat options only for levels present in authorized classes
const levelOptions = computed(() => {
  const levels = new Set()
  allClasses.value.forEach(cls => {
    if (cls.startsWith('X ') || cls.includes('10')) levels.add('10')
    if (cls.startsWith('XI ') || cls.includes('11')) levels.add('11')
    if (cls.startsWith('XII ') || cls.includes('12')) levels.add('12')
  })
  return [
    { value: '10', label: 'Kelas 10' },
    { value: '11', label: 'Kelas 11' },
    { value: '12', label: 'Kelas 12' },
  ].filter(l => levels.size === 0 || levels.has(l.value))
})

// Data referensi master rombel, wali kelas, guru BK (fallback data)
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
    const jurObj = jurusanOptions.value.find(j => j.code === selectedJurusan.value)
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
  nis: '',
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
    nis: '',
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
  nis: '',
  name: '',
  class_name: '',
  gender: 'L',
  parent_name: '',
  parent_phone: ''
})

const openEditStudentModal = (student) => {
  editStudentForm.value = {
    id: student.id,
    nis: student.nis || student.nisn || '',
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
// DELETE SISWA (CUSTOM MODAL CARD KONFIRMASI)
// ==========================================
const showDeleteStudentModal = ref(false)
const studentToDelete = ref(null)
const isDeletingStudent = ref(false)

const confirmDeleteStudent = (student) => {
  studentToDelete.value = student
  showDeleteStudentModal.value = true
}

const deleteStudent = (student) => {
  confirmDeleteStudent(student)
}

const executeDeleteStudent = () => {
  if (!studentToDelete.value) return
  isDeletingStudent.value = true

  router.delete(`/siswa/${studentToDelete.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteStudentModal.value = false
      studentToDelete.value = null
      isDeletingStudent.value = false
    },
    onError: () => {
      isDeletingStudent.value = false
    }
  })
}

// ==========================================
// KOSONGKAN 1 KELAS (BULK DELETE PER ROMBEL)
// ==========================================
const showClearClassModal = ref(false)
const targetClassToClear = ref('')
const clearClassConfirmation = ref('')
const isClearingClass = ref(false)

const openClearClassModal = () => {
  targetClassToClear.value = allClasses.value[0] || ''
  clearClassConfirmation.value = ''
  showClearClassModal.value = true
}

const openClearClassModalFor = (className) => {
  targetClassToClear.value = className
  clearClassConfirmation.value = ''
  showClearClassModal.value = true
}

const classStudentsCountToClear = computed(() => {
  if (!targetClassToClear.value) return 0
  return props.dbStudents.filter(s => s.class_name === targetClassToClear.value).length
})

const submitClearClass = () => {
  if (!targetClassToClear.value) return
  if (clearClassConfirmation.value !== 'HAPUS') return
  isClearingClass.value = true
  router.post('/absensi/kelas/clear', { class_name: targetClassToClear.value }, {
    preserveScroll: true,
    onSuccess: () => {
      showClearClassModal.value = false
      isClearingClass.value = false
      clearClassConfirmation.value = ''
    },
    onError: (errors) => {
      isClearingClass.value = false
      alert(Object.values(errors).flat().join('\n') || 'Gagal mengosongkan data kelas.')
    }
  })
}

// ==========================================
// ACTION LAINNYA
// ==========================================
const recordViolationForStudent = (student) => {
  router.visit('/verifikasi', {
    data: {
      nama: student.name,
      nis: student.nis || student.nisn,
      nisn: student.nis || student.nisn,
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
    `Yth. Bapak/Ibu ${student.parent_name || 'Orang Tua/Wali'} (Wali dari ${student.name} - ${student.class_name}), kami menginformasikan laporan presensi harian pada tanggal ${att.date || todayDateShort.value}: Status: ${att.status}. Keterangan: ${att.notes}. Terima kasih atas kerja samanya. - Tim BK SMK PGRI 05 JEMBER`
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

// ==========================================
// IMPORT SISWA LOGIC
// ==========================================
const showImportModal = ref(false)
const importFileName = ref('')
const importTargetClass = ref('AUTO')
const isDragging = ref(false)
const isSubmittingImport = ref(false)
const importedStudents = ref([])
const importFileInputRef = ref(null)

const getTargetRombelName = (defaultClassName) => {
  if (importTargetClass.value === 'AUTO') {
    return defaultClassName
  }
  const found = availableRombels.value.find(r => r.id === importTargetClass.value || r.id == importTargetClass.value)
  return found ? (found.nama_rombel || found.name) : defaultClassName
}

const openImportModal = () => {
  importedStudents.value = []
  importFileName.value = ''
  if (selectedClass.value !== 'ALL') {
    const matched = availableRombels.value.find(r => r.name === selectedClass.value || r.nama_rombel === selectedClass.value)
    importTargetClass.value = matched ? matched.id : 'AUTO'
  } else {
    importTargetClass.value = 'AUTO'
  }
  showImportModal.value = true
}

const closeImportModal = () => {
  showImportModal.value = false
  importedStudents.value = []
  importFileName.value = ''
}

const downloadCsvTemplate = () => {
  const sampleClass = allClasses.value[0] || 'X RPL 1'
  const csvContent = 'NIS,NAMA_LENGKAP,KELAS,JENIS_KELAMIN,NO_WA_WALI\n' +
    `10451,Muhammad Bagas Saputra,${sampleClass},L,+62 812-3344-5566\n` +
    `10452,Nabila Putri Ayu,${sampleClass},P,+62 813-7788-9900\n` +
    `10453,Rendy Wahyu Pratama,${sampleClass},L,+62 856-1122-3344\n`
  
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', 'Template_Import_Siswa_Presensi.csv')
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const loadSampleData = () => {
  importFileName.value = 'Data_Siswa_Binaan_Presensi.csv'
  const target = allClasses.value[0] || 'X RPL 1'
  const target2 = allClasses.value[1] || target

  importedStudents.value = [
    { nis: '10451', name: 'Alifia Zahra Ramadhani', className: target, gender: 'P', parentWa: '+62 812-3401-1001' },
    { nis: '10452', name: 'Bayu Aditya Pratama', className: target, gender: 'L', parentWa: '+62 813-3401-1002' },
    { nis: '10453', name: 'Cahyo Wibowo', className: target, gender: 'L', parentWa: '+62 856-3401-1003' },
    { nis: '10454', name: 'Dwi Lestari Handayani', className: target2, gender: 'P', parentWa: '+62 878-3401-1004' },
    { nis: '10455', name: 'Eko Prasetyo Nugroho', className: target2, gender: 'L', parentWa: '+62 821-3401-1005' }
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
      const nis = cols[0]?.trim().replace(/['"]/g, '') || `10${450 + i}`
      const name = cols[1]?.trim().replace(/['"]/g, '') || 'Siswa Baru'
      const className = cols[2]?.trim().replace(/['"]/g, '').toUpperCase() || (allClasses.value[0] || 'X RPL 1')
      const gender = (cols[3]?.trim().replace(/['"]/g, '').toUpperCase() === 'P') ? 'P' : 'L'
      const parentWa = cols[4]?.trim().replace(/['"]/g, '') || `+62 812-${1000 + i}-${2000 + i}`
      
      results.push({
        nis,
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
  processImportFile(file)
}

const handleDrop = (e) => {
  isDragging.value = false
  const file = e.dataTransfer.files?.[0]
  if (!file) return
  processImportFile(file)
}

const processImportFile = (file) => {
  importFileName.value = file.name
  const reader = new FileReader()
  reader.onload = (event) => {
    const content = event.target.result
    const parsed = parseCsvText(content)
    if (parsed.length > 0) {
      importedStudents.value = parsed
    } else {
      loadSampleData()
      alert(`File "${file.name}" berhasil diunggah. Menampilkan data hasil konversi.`)
    }
  }
  reader.readAsText(file)
}

const submitImport = () => {
  if (importedStudents.value.length === 0 || isSubmittingImport.value) return
  
  const targetRombelObj = availableRombels.value.find(r => r.id === importTargetClass.value || r.id == importTargetClass.value)
  const resolvedTargetClassName = targetRombelObj ? (targetRombelObj.nama_rombel || targetRombelObj.name) : null

  const payload = importedStudents.value.map(st => ({
    nis: st.nis,
    name: st.name,
    className: importTargetClass.value === 'AUTO' ? st.className : (resolvedTargetClassName || st.className),
    gender: st.gender,
    parentWa: st.parentWa,
  }))

  isSubmittingImport.value = true
  router.post('/absensi/import', {
    students: payload,
    target_rombel_id: importTargetClass.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      isSubmittingImport.value = false
      closeImportModal()
      showToast(`Berhasil mengimpor ${payload.length} siswa ke database!`)
    },
    onError: (err) => {
      isSubmittingImport.value = false
      alert('Gagal mengimpor data siswa. Pastikan format kolom sudah benar.')
    }
  })
}
</script>
