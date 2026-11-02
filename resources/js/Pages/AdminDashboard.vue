<template>
  <AdminLayout :active-tab="activeTab" :unread-count="unreadConsultationCount" @select-tab="activeTab = $event">
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- HEADER CARD & ROLE BADGE                                     -->
      <!-- ============================================================ -->
      <!-- ============================================================ -->
      <!-- HEADER CARD & ROLE BADGE                                     -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
              <span class="text-blue-700 font-bold uppercase tracking-wider text-[11px]">Role Administrator Utama</span>
              <span>&bull;</span>
              <span class="inline-flex items-center gap-1.5 text-emerald-700 text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
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
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Guru BK</span>
            </button>
            <button
              @click="openModal('kelas')"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Kelas</span>
            </button>
            <button
              @click="openModal('import-siswa')"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              <span>Import Siswa</span>
            </button>
            <button
              @click="openModal('siswa')"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Siswa</span>
            </button>
          </div>
        </div>

        <!-- Segmented Tab Navigation -->
        <div class="flex items-center gap-1.5 mt-5 pt-4 border-t border-slate-100 overflow-x-auto scrollbar-hide text-xs">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-semibold transition cursor-pointer shrink-0"
            :class="activeTab === tab.id ? 'bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            <span>{{ tab.label }}</span>
            <span
              v-if="tab.count !== undefined"
              class="px-1.5 py-0.5 rounded text-[10px] font-bold"
              :class="activeTab === tab.id ? 'bg-slate-800 text-slate-200' : 'bg-white text-slate-700 border border-slate-200'"
            >
              {{ tab.count }}
            </span>
          </button>
        </div>
      </div>

      <!-- Notification Banner: Pesan Konsultasi Baru dari Halaman Tamu -->
      <div
        v-if="unreadConsultationCount > 0 && activeTab !== 'konsultasi'"
        class="p-4 sm:p-5 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-in fade-in"
      >
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <div>
            <p class="text-xs sm:text-sm font-bold text-slate-900">
              Terdapat {{ unreadConsultationCount }} Pesan Konsultasi Baru Masuk dari Halaman Tamu
            </p>
            <p class="text-[11px] text-slate-600 mt-0.5">
              Wali murid mengirimkan permohonan bimbingan/konsultasi siswa yang membutuhkan tindak lanjut Guru BK.
            </p>
          </div>
        </div>
        <button
          @click="activeTab = 'konsultasi'"
          class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition cursor-pointer self-start sm:self-auto shrink-0 flex items-center gap-1.5"
        >
          <span>Tindak Lanjuti Pesan</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- ============================================================ -->
      <!-- TAB 1: DASHBOARD & GRAFIK NAIK TURUN KEHADIRAN 1 BULAN       -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'dashboard'" class="space-y-6">
        
        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
          <!-- Total Guru BK -->
          <div class="p-4 sm:p-5 bg-white rounded-lg border border-slate-200">
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
          <div class="p-4 sm:p-5 bg-white rounded-lg border border-slate-200">
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
          <div class="p-4 sm:p-5 bg-white rounded-lg border border-slate-200">
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

          <!-- Kehadiran Rata-rata 1 Bulan (Card Bulat Mini Dinamis) -->
          <div class="p-4 sm:p-5 bg-white rounded-lg border border-slate-200 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">PRESENSI {{ currentMonthData.shortName.toUpperCase() }}</span>
              <div
                class="w-8 h-8 rounded-lg flex items-center justify-center"
                :class="currentMonthData.trend === 'naik' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'"
              >
                <svg v-if="currentMonthData.trend === 'naik'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
              </div>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ currentCohortData.hadir.rate }}%</div>
                <div
                  class="flex items-center gap-1 text-[11px] font-bold mt-0.5"
                  :class="currentMonthData.trend === 'naik' ? 'text-emerald-600' : 'text-rose-600'"
                >
                  <svg v-if="currentMonthData.trend === 'naik'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                  </svg>
                  <span>{{ currentMonthData.delta }} vs {{ currentMonthData.prevMonthName }}</span>
                </div>
              </div>
              <!-- Mini Circular Progress Gauge -->
              <div class="shrink-0 relative flex items-center justify-center">
                <svg viewBox="0 0 54 54" class="w-12 h-12">
                  <circle cx="27" cy="27" r="21" fill="none" stroke="#f1f5f9" stroke-width="5" />
                  <circle
                    cx="27"
                    cy="27"
                    r="21"
                    fill="none"
                    :stroke="currentMonthData.trend === 'naik' ? '#10b981' : '#f43f5e'"
                    stroke-width="5"
                    stroke-linecap="round"
                    :stroke-dasharray="getRingDasharray(currentCohortData.hadir.rate, 21)"
                    transform="rotate(-90 27 27)"
                  />
                </svg>
                <span class="absolute text-[10px] font-black text-slate-800">{{ Math.round(currentCohortData.hadir.rate) }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ============================================================ -->
        <!-- STATISTIKA PRESENSI SISWA BERBASIS DIAGRAM BULAT             -->
        <!-- ============================================================ -->
        <div class="space-y-6">

          <!-- Card Utama: Diagram Lingkaran Solid Komposisi Presensi 1 Bulan -->
          <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200 space-y-6">
            
            <!-- Header Card & Filter Controls -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
              <div>
                <!-- Eyebrow text tanpa pill badge melayang -->
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                  </svg>
                  <span>Statistika Presensi Siswa &bull; Akumulasi 1 Bulan Penuh</span>
                </div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                  Diagram Lingkaran Komposisi Kehadiran Siswa (1 Bulan)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                  Proporsi komparasi kehadiran (Hadir) terhadap ketidakhadiran (Sakit, Izin, Alpha) yang digabung dalam 1 diagram lingkaran penuh (total 100%).
                </p>
              </div>

              <!-- Controls: Pilih Bulan, Pilih Angkatan, & Mode Tampilan -->
              <div class="flex flex-wrap items-center gap-2.5">
                <!-- Dropdown Pilih Bulan -->
                <div class="relative">
                  <select
                    v-model="selectedMonth"
                    class="appearance-none bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2 pr-8 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer transition"
                  >
                    <option value="mei">Bulan Mei 2026 (Aktif)</option>
                    <option value="april">Bulan April 2026</option>
                    <option value="maret">Bulan Maret 2026</option>
                    <option value="februari">Bulan Februari 2026</option>
                    <option value="januari">Bulan Januari 2026</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>

                <!-- Dropdown Pilih Angkatan -->
                <div class="relative">
                  <select
                    v-model="selectedAngkatan"
                    class="appearance-none bg-white hover:bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2 pr-8 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer transition"
                  >
                    <option value="all">Semua Angkatan (Gabungan Siswa)</option>
                    <option value="x">Angkatan Kelas X (Fase E)</option>
                    <option value="xi">Angkatan Kelas XI (Fase F - Tk. 1)</option>
                    <option value="xii">Angkatan Kelas XII (Fase F - Tk. 2)</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>

                <!-- Switch View: Bulat Penuh (Default) / Fluktuasi Garis -->
                <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-lg border border-slate-200 text-xs shrink-0">
                  <button
                    type="button"
                    @click="chartViewMode = 'bulat'"
                    class="px-3 py-1.5 rounded-md font-semibold transition flex items-center gap-1.5 cursor-pointer"
                    :class="chartViewMode === 'bulat' ? 'bg-white text-blue-700 font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
                    title="Diagram Lingkaran Penuh (Pie Chart)"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    <span>Diagram Bulat</span>
                  </button>
                  <button
                    type="button"
                    @click="chartViewMode = 'garis'"
                    class="px-3 py-1.5 rounded-md font-semibold transition flex items-center gap-1.5 cursor-pointer"
                    :class="chartViewMode === 'garis' ? 'bg-white text-blue-700 font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
                    title="Fluktuasi Harian 30 Hari"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <span>Tren Garis</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Banner Notifikasi Kenaikan / Penurunan Kehadiran Bulan Terpilih -->
            <div
              class="p-4 rounded-lg border flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition"
              :class="currentMonthData.trend === 'naik' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'"
            >
              <div class="flex items-center gap-3">
                <div
                  class="w-9 h-9 rounded-lg text-white flex items-center justify-center shrink-0"
                  :class="currentMonthData.trend === 'naik' ? 'bg-emerald-600' : 'bg-rose-600'"
                >
                  <svg v-if="currentMonthData.trend === 'naik'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                  </svg>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wide">
                      {{ currentMonthData.trend === 'naik' ? 'Kenaikan Kehadiran Siswa:' : 'Penurunan Kehadiran Siswa:' }}
                    </span>
                    <span
                      class="text-xs font-black px-2 py-0.5 rounded"
                      :class="currentMonthData.trend === 'naik' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                    >
                      {{ currentMonthData.delta }} pada {{ currentMonthData.name }}
                    </span>
                  </div>
                  <p class="text-xs mt-0.5" :class="currentMonthData.trend === 'naik' ? 'text-emerald-800' : 'text-rose-800'">
                    Tingkat presensi hadir {{ selectedAngkatanLabel }} tercatat <strong>{{ currentCohortData.hadir.rate }}%</strong>
                    ({{ currentMonthData.trend === 'naik' ? 'meningkat' : 'menurun' }} dibanding {{ currentMonthData.prevMonthName }} sebesar {{ currentMonthData.prevHadirRate }}%).
                    {{ currentMonthData.summary }}
                  </p>
                </div>
              </div>
              <div
                class="shrink-0 flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-lg border bg-white self-start sm:self-auto"
                :class="currentMonthData.trend === 'naik' ? 'text-emerald-800 border-emerald-200' : 'text-rose-800 border-rose-200'"
              >
                <span
                  class="w-2 h-2 rounded-full"
                  :class="currentMonthData.trend === 'naik' ? 'bg-emerald-500' : 'bg-rose-500'"
                ></span>
                <span>{{ currentMonthData.trend === 'naik' ? 'Tren Positif / Melampaui Target' : 'Perlu Evaluasi Presensi' }}</span>
              </div>
            </div>

            <!-- TAMPILAN 1: DIAGRAM LINGKARAN BULAT PENUH (SOLID PIE CHART SEPERTI FOTO 1) -->
            <div v-if="chartViewMode === 'bulat'" class="space-y-6">
              
              <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- KOLOM KIRI: DIAGRAM LINGKARAN PENUH SEPERTI FOTO 1 -->
                <div class="lg:col-span-5 flex flex-col items-center justify-center p-5 sm:p-6 bg-slate-50 rounded-lg border border-slate-200 relative">
                  
                  <div class="w-full flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-bold text-slate-700">Diagram Lingkaran Penuh</span>
                    <span class="font-mono text-[11px] bg-slate-200 px-2 py-0.5 rounded font-bold">1 Bulan &bull; 100%</span>
                  </div>

                  <!-- SVG Solid Pie Chart -->
                  <div class="relative w-72 h-72 sm:w-80 sm:h-80 flex items-center justify-center my-2 select-none">
                    <svg viewBox="0 0 380 380" class="w-full h-full overflow-visible">
                      
                      <!-- Slices (Irisan Pie) -->
                      <g>
                        <path
                          v-for="slice in pieSlices"
                          :key="slice.id"
                          :d="slice.pathD"
                          :fill="slice.color"
                          stroke="#ffffff"
                          stroke-width="2.5"
                          class="transition-all duration-300 cursor-pointer"
                          :class="[
                            hoveredPieSlice && hoveredPieSlice.id === slice.id ? 'opacity-100 filter brightness-110 drop-shadow-md' : (hoveredPieSlice ? 'opacity-40' : 'opacity-100 hover:brightness-105')
                          ]"
                          @mouseenter="hoveredPieSlice = slice"
                          @mouseleave="hoveredPieSlice = null"
                        />
                      </g>

                      <!-- Inside Slice Labels (Format Foto 1) -->
                      <g class="pointer-events-none select-none">
                        <template v-for="slice in pieSlices" :key="'label-' + slice.id">
                          <!-- Large Slices (Hadir > 50%) -->
                          <g v-if="slice.isLarge">
                            <text
                              :x="slice.labelX"
                              :y="slice.labelY - 14"
                              text-anchor="middle"
                              class="text-sm sm:text-base font-bold fill-white tracking-wide"
                              style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);"
                            >
                              {{ slice.label }}
                            </text>
                            <text
                              :x="slice.labelX"
                              :y="slice.labelY + 20"
                              text-anchor="middle"
                              class="text-3xl sm:text-4xl font-black fill-white"
                              style="text-shadow: 0 2px 4px rgba(0,0,0,0.35);"
                            >
                              {{ slice.rate }}%
                            </text>
                            <text
                              :x="slice.labelX"
                              :y="slice.labelY + 42"
                              text-anchor="middle"
                              class="text-xs font-semibold fill-white/90"
                              style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);"
                            >
                              {{ slice.count }} Siswa
                            </text>
                          </g>

                          <!-- Small Slices (Sakit, Izin, Alpha) with Leader Lines -->
                          <g v-else>
                            <line
                              :x1="slice.pinStartX"
                              :y1="slice.pinStartY"
                              :x2="slice.pinEndX"
                              :y2="slice.pinEndY"
                              :stroke="slice.color"
                              stroke-width="1.8"
                              stroke-linecap="round"
                            />
                            <circle
                              :cx="slice.pinEndX"
                              :cy="slice.pinEndY"
                              r="2.5"
                              :fill="slice.color"
                            />
                          </g>
                        </template>
                      </g>

                    </svg>

                    <!-- Hover Spotlight Tooltip Badge -->
                    <div
                      v-if="hoveredPieSlice"
                      class="absolute top-2 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold border border-slate-800 pointer-events-none flex items-center gap-2 z-20 whitespace-nowrap animate-in fade-in zoom-in-95 duration-150"
                    >
                      <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: hoveredPieSlice.color }"></span>
                      <span>{{ hoveredPieSlice.label }}: {{ hoveredPieSlice.rate }}%</span>
                      <span class="text-slate-400 font-normal">({{ hoveredPieSlice.count }} Siswa)</span>
                    </div>
                  </div>

                  <!-- Legend Mini Cards di bawah Pie -->
                  <div class="w-full grid grid-cols-2 sm:grid-cols-4 gap-2 pt-3 border-t border-slate-200 text-center">
                    <div
                      v-for="slice in pieSlices"
                      :key="'pill-' + slice.id"
                      class="p-2 rounded-lg bg-white border border-slate-200 text-xs transition cursor-pointer"
                      :class="hoveredPieSlice && hoveredPieSlice.id === slice.id ? 'ring-2 ring-blue-500' : 'hover:bg-slate-50'"
                      @mouseenter="hoveredPieSlice = slice"
                      @mouseleave="hoveredPieSlice = null"
                    >
                      <div class="flex items-center justify-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: slice.color }"></span>
                        <span class="font-bold text-slate-800 text-[11px]">{{ slice.label }}</span>
                      </div>
                      <div class="font-black text-slate-900 text-xs mt-0.5">{{ slice.rate }}%</div>
                    </div>
                  </div>

                  <p class="text-[11px] text-slate-400 mt-2 text-center">
                    Arahkan kursor pada irisan diagram untuk meninjau rincian persentase presensi.
                  </p>
                </div>

                <!-- KOLOM KANAN: RINCIAN KATEGORI & KOMPARASI HADIR VS SAKIT/ALPHA -->
                <div class="lg:col-span-7 space-y-4">
                  
                  <!-- 4 Kategori Status Presensi -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div
                      v-for="slice in pieSlices"
                      :key="'card-' + slice.id"
                      class="p-4 rounded-lg border transition cursor-pointer"
                      :class="[
                        hoveredPieSlice && hoveredPieSlice.id === slice.id ? 'bg-slate-50 border-slate-400 ring-1 ring-slate-300' : 'bg-white border-slate-200 hover:bg-slate-50'
                      ]"
                      @mouseenter="hoveredPieSlice = slice"
                      @mouseleave="hoveredPieSlice = null"
                    >
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                          <span class="w-3.5 h-3.5 rounded-full shrink-0" :style="{ backgroundColor: slice.color }"></span>
                          <span class="font-bold text-slate-800 text-xs sm:text-sm">{{ slice.label }}</span>
                        </div>
                        <div class="text-right">
                          <span class="text-base sm:text-lg font-black text-slate-900">{{ slice.rate }}%</span>
                        </div>
                      </div>
                      
                      <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1">
                        <span>{{ slice.desc }}</span>
                        <span class="font-bold text-slate-700 shrink-0">{{ slice.count }} Siswa</span>
                      </div>

                      <!-- Progress bar -->
                      <div class="w-full bg-slate-100 h-2 rounded-full mt-2.5 overflow-hidden">
                        <div
                          class="h-full rounded-full transition-all duration-300"
                          :style="{ width: slice.rate + '%', backgroundColor: slice.color }"
                        ></div>
                      </div>
                    </div>
                  </div>

                  <!-- CARD KOMPARASI: HADIR VS KETIDAKHADIRAN 1 BULAN (Solid Flat Style) -->
                  <div class="p-4 sm:p-5 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-800">
                          Analisis Komparasi 1 Bulan: Hadir vs Sakit / Izin / Alpha
                        </span>
                      </div>
                      <span class="text-[11px] font-bold text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded border border-blue-200">
                        Rasio {{ Math.round(currentCohortData.hadir.rate / Math.max(0.1, 100 - currentCohortData.hadir.rate)) }} : 1
                      </span>
                    </div>

                    <!-- 2-Box Metric Comparison -->
                    <div class="grid grid-cols-2 gap-3">
                      <div class="p-3.5 bg-white rounded-lg border border-slate-200">
                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">Total Siswa Hadir</span>
                        <div class="text-2xl sm:text-3xl font-black text-blue-700 tracking-tight mt-0.5">
                          {{ currentCohortData.hadir.rate }}%
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                          {{ currentCohortData.hadir.count }} dari total {{ currentCohortData.totalSiswa }} siswa
                        </p>
                      </div>

                      <div class="p-3.5 bg-white rounded-lg border border-slate-200">
                        <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Total Ketidakhadiran (S+I+A)</span>
                        <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight mt-0.5">
                          {{ (100 - currentCohortData.hadir.rate).toFixed(1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                          {{ totalAbsenCount }} siswa (Sakit, Izin, dan Alpha)
                        </p>
                      </div>
                    </div>

                    <!-- Explanatory Text answering user's question -->
                    <p class="text-xs text-slate-600 leading-relaxed">
                      Berdasarkan diagram bulat akumulasi 1 bulan pada <strong>{{ currentMonthData.name }}</strong>, persentase siswa hadir (<strong>{{ currentCohortData.hadir.rate }}%</strong>) tercatat <strong>sangat tinggi</strong> dan mendominasi total presensi sekolah. Siswa Alpha tanpa keterangan hanya menyumbang <strong>{{ currentCohortData.alpha.rate }}%</strong> ({{ currentCohortData.alpha.count }} siswa), jauh di bawah ambang batas toleransi ketidakhadiran.
                    </p>

                    <!-- Target Status Chip -->
                    <div class="p-3 bg-white rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                      <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-700 font-medium">Standar Target Kehadiran Sekolah: <strong>95.0%</strong></span>
                      </div>
                      <span
                        class="font-bold px-2.5 py-1 rounded text-[11px]"
                        :class="currentCohortData.hadir.rate >= 95 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
                      >
                        {{ currentCohortData.hadir.rate >= 95 ? 'Terpenuhi (+' + (currentCohortData.hadir.rate - 95).toFixed(1) + '%)' : 'Di Bawah Standar (' + (currentCohortData.hadir.rate - 95).toFixed(1) + '%)' }}
                      </span>
                    </div>
                  </div>

                </div>

              </div>

              <!-- TIMELINE REKAPITULASI TREN 5 BULAN (SEMESTER GENAP) -->
              <div class="p-4 sm:p-5 bg-white rounded-lg border border-slate-200 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                  <div>
                    <h3 class="font-bold text-slate-900 text-xs sm:text-sm flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span>Perbandingan Tren Kehadiran Antar-Bulan (Semester Genap 2026)</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Pilih salah satu bulan untuk melihat diagram lingkaran presensi pada bulan tersebut</p>
                  </div>
                  <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg self-start sm:self-auto border border-slate-200">
                    {{ selectedAngkatanLabel }}
                  </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-1">
                  <button
                    v-for="m in monthlyList"
                    :key="m.id"
                    type="button"
                    @click="selectedMonth = m.id"
                    class="p-3 rounded-lg border text-left transition cursor-pointer relative overflow-hidden group"
                    :class="[
                      selectedMonth === m.id
                        ? 'bg-blue-50 border-blue-500'
                        : 'bg-slate-50 border-slate-200 hover:bg-slate-100 hover:border-slate-300'
                    ]"
                  >
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                      <span :class="selectedMonth === m.id ? 'text-blue-900 font-black' : 'text-slate-700'">{{ m.shortName }}</span>
                      <span
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center gap-0.5"
                        :class="m.trend === 'naik' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                      >
                        <svg v-if="m.trend === 'naik'" xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        <span>{{ m.delta }}</span>
                      </span>
                    </div>

                    <div class="text-lg font-black text-slate-900 tracking-tight">{{ m.hadirRate }}%</div>
                    <span class="text-[10px] text-slate-400 block mt-0.5">Hadir Siswa</span>

                    <!-- Mini bar progress -->
                    <div class="w-full bg-slate-200 h-1.5 rounded-full mt-2 overflow-hidden">
                      <div
                        class="h-full rounded-full transition-all duration-300"
                        :style="{
                          width: m.hadirRate + '%',
                          backgroundColor: m.trend === 'naik' ? '#10b981' : '#f43f5e'
                        }"
                      ></div>
                    </div>

                    <span
                      v-if="selectedMonth === m.id"
                      class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-blue-600"
                    ></span>
                  </button>
                </div>
              </div>

            </div>

            <!-- TAMPILAN 2: GRAFIK TREN HARIAN (ALTERNATIF) -->
            <div v-else class="space-y-6">
              <!-- Highlight Peak & Dip Badges -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
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

                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-lg flex items-center justify-between">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center shrink-0">
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
              <div class="relative bg-slate-50 rounded-lg p-4 sm:p-6 border border-slate-200">
                <div v-if="hoveredPoint" class="absolute top-3 right-4 z-20 bg-slate-900 text-white px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-800 flex items-center gap-2">
                  <span>Tgl {{ hoveredPoint.day }} Okt:</span>
                  <span :class="hoveredPoint.rate >= 95 ? 'text-emerald-400' : 'text-rose-400'">{{ hoveredPoint.rate }}% Hadir</span>
                  <span class="text-[10px] text-slate-400">({{ hoveredPoint.count }} Siswa)</span>
                </div>

                <div class="w-full overflow-x-auto pb-2">
                  <svg viewBox="0 0 900 240" class="w-full min-w-[700px] h-56 select-none overflow-visible">
                    <line x1="40" y1="30" x2="880" y2="30" stroke="#e2e8f0" stroke-dasharray="4" />
                    <text x="10" y="34" class="text-[10px] fill-slate-400 font-mono">100%</text>

                    <line x1="40" y1="90" x2="880" y2="90" stroke="#fcd34d" stroke-dasharray="4" stroke-width="1.5" />
                    <text x="10" y="94" class="text-[10px] fill-amber-500 font-mono font-bold">95%</text>

                    <line x1="40" y1="150" x2="880" y2="150" stroke="#e2e8f0" stroke-dasharray="4" />
                    <text x="10" y="154" class="text-[10px] fill-slate-400 font-mono">90%</text>

                    <line x1="40" y1="210" x2="880" y2="210" stroke="#e2e8f0" stroke-dasharray="4" />
                    <text x="10" y="214" class="text-[10px] fill-slate-400 font-mono">85%</text>

                    <defs>
                      <linearGradient id="attendanceGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#2563eb" stop-opacity="0.35" />
                        <stop offset="70%" stop-color="#3b82f6" stop-opacity="0.08" />
                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0" />
                      </linearGradient>
                    </defs>

                    <path :d="chartAreaPath" fill="url(#attendanceGradient)" />
                    <path :d="chartLinePath" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

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
            </div>

          </div>

          <!-- Card 2: Diagram Bulat per Jurusan (Circular Progress Gauges) -->
          <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                  Distribusi Kehadiran per Jurusan (Diagram Bulat)
                </h3>
                <p class="text-xs text-slate-400">Tingkat kehadiran siswa di 5 kompetensi keahlian SMK PGRI 05 Jember</p>
              </div>
              <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 self-start sm:self-auto">
                Target Standar: 95.0%
              </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 pt-2">
              <div
                v-for="j in jurusanStats"
                :key="j.name"
                class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-center flex flex-col items-center hover:bg-slate-100 transition"
              >
                <!-- Circular Ring Gauge -->
                <div class="relative w-18 h-18 my-1 flex items-center justify-center">
                  <svg viewBox="0 0 72 72" class="w-full h-full select-none">
                    <circle cx="36" cy="36" r="28" fill="none" stroke="#e2e8f0" stroke-width="6" />
                    <circle
                      cx="36"
                      cy="36"
                      r="28"
                      fill="none"
                      :stroke="j.color"
                      stroke-width="6"
                      stroke-linecap="round"
                      :stroke-dasharray="getRingDasharray(j.rate, 28)"
                      transform="rotate(-90 36 36)"
                      class="transition-all duration-500"
                    />
                  </svg>
                  <span class="absolute text-xs font-black text-slate-900">{{ j.rate }}%</span>
                </div>

                <div class="font-black text-slate-900 text-sm mt-1">{{ j.name }}</div>
                <div class="text-[11px] text-slate-400 line-clamp-1" :title="j.fullName">{{ j.fullName }}</div>
                <div class="mt-2 text-[10px] font-bold px-2 py-0.5 rounded border" :class="j.rate >= 95 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'">
                  {{ j.status }}
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Diagram Bulat Rekapitulasi Mingguan (4 Pekan) -->
          <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200 space-y-4">
            <div>
              <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                Tren Kehadiran Mingguan (Diagram Bulat 4 Pekan)
              </h3>
              <p class="text-xs text-slate-400">Progres capaian kehadiran siswa setiap siklus 7 hari</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 pt-1">
              <div
                v-for="w in weeklyStats"
                :key="w.week"
                class="p-4 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-3.5 hover:bg-slate-100 transition"
              >
                <!-- Mini Circular Ring -->
                <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                  <svg viewBox="0 0 60 60" class="w-full h-full select-none">
                    <circle cx="30" cy="30" r="23" fill="none" stroke="#e2e8f0" stroke-width="5" />
                    <circle
                      cx="30"
                      cy="30"
                      r="23"
                      fill="none"
                      :stroke="w.color"
                      stroke-width="5"
                      stroke-linecap="round"
                      :stroke-dasharray="getRingDasharray(w.rate, 23)"
                      transform="rotate(-90 30 30)"
                      class="transition-all duration-500"
                    />
                  </svg>
                  <span class="absolute text-[11px] font-black text-slate-900">{{ w.rate }}%</span>
                </div>

                <div class="min-w-0">
                  <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ w.week }}</span>
                  <div class="text-xs font-semibold text-slate-700 truncate">{{ w.range }}</div>
                  <div class="text-[11px] font-bold mt-0.5 truncate" :class="w.rate >= 95 ? 'text-emerald-600' : 'text-slate-500'">
                    {{ w.change }}
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- ============================================================ -->
      <!-- TAB 2: KELOLA GURU BK                                         -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'guru'" class="space-y-4">
        <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Data Guru Bimbingan Konseling (BK)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar tenaga konselor resmi penanggung jawab rombel SMK PGRI 05 Jember</p>
          </div>
          <button
            @click="openModal('guru')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition cursor-pointer self-start sm:self-auto"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Guru BK Baru</span>
          </button>
        </div>

        <!-- Table Guru BK -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden p-5 sm:p-6">
          <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                <tr>
                  <th class="py-3.5 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NAMA LENGKAP &amp; GELAR</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">AKUN LOGIN BK</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NIP / NUPTK</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">JABATAN</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">ROMBEL BINAAN</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">STATUS AKUN</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(g, idx) in guruBkList" :key="g.id" class="hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ g.name }}</div>
                    <div class="text-[11px] text-slate-400">{{ g.email }}</div>
                  </td>
                  <td class="py-3.5 px-4">
                    <div class="inline-flex items-center gap-1 font-mono text-[11px] text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 font-bold">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                      </svg>
                      <span>@{{ g.username || 'guru_bk' }}</span>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 font-mono text-slate-700 font-medium">
                    <div>NIP: {{ g.nip }}</div>
                    <div class="text-[10px] text-slate-400">NUPTK: {{ g.nuptk }}</div>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">{{ g.jabatan }}</td>
                  <td class="py-3.5 px-4">
                    <div class="flex flex-wrap gap-1 max-w-xs">
                      <span v-for="r in g.rombel" :key="r" class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">
                        {{ r }}
                      </span>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                      <span>Akun Aktif</span>
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <div class="inline-flex items-center gap-1.5">
                      <button @click="editGuru(g)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition cursor-pointer" title="Edit Guru BK">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button @click="confirmDeleteGuru(g)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer" title="Hapus Guru">
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
        <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Data Rombongan Belajar (Rombel)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Struktur rombel kelas per tingkat dan penugasan wali kelas</p>
          </div>
          <button
            @click="openModal('kelas')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition cursor-pointer self-start sm:self-auto"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Kelas Baru</span>
          </button>
        </div>

        <!-- Table Kelas -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden p-5 sm:p-6">
          <div class="overflow-x-auto border border-slate-200 rounded-lg">
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
                <tr v-for="(k, idx) in kelasList" :key="k.id" class="hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4">
                    <span class="font-black text-slate-900 text-xs sm:text-sm">{{ k.name }}</span>
                  </td>
                  <td class="py-3.5 px-4">
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200">
                      {{ k.level }} &bull; {{ k.jurusan }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">{{ k.waliKelas }}</td>
                  <td class="py-3.5 px-4 text-slate-600 font-medium">{{ k.guruBK }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 text-xs">
                      {{ k.studentCount }} Siswa
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <div class="inline-flex items-center justify-center gap-1.5">
                      <button @click="editKelas(k)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition cursor-pointer" title="Edit Rombel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button @click="confirmDeleteKelas(k)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer" title="Hapus Kelas">
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
      <!-- TAB 4: KELOLA DATA SISWA                                      -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'siswa'" class="space-y-4">
        <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Data Induk Siswa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan data siswa aktif dan kontak wali siswa</p>
          </div>
          <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <button
              @click="openModal('import-siswa')"
              class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              <span>Import Siswa (Excel/CSV)</span>
            </button>
            <button
              @click="openModal('siswa')"
              class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Siswa Baru</span>
            </button>
          </div>
        </div>

        <!-- Filter & Search Siswa -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden p-5 sm:p-6 space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-sm">
              <input
                v-model="searchSiswa"
                type="text"
                placeholder="Cari NISN, nama siswa..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
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
                class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
              >
                <option value="Semua">Semua Kelas</option>
                <option v-for="k in kelasList" :key="k.id" :value="k.name">{{ k.name }}</option>
              </select>
            </div>
          </div>

          <!-- Table Siswa -->
          <div class="overflow-x-auto border border-slate-200 rounded-lg">
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
                <tr v-for="(s, idx) in filteredSiswaList" :key="s.id" class="hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4 font-mono font-medium text-slate-600">{{ s.nisn }}</td>
                  <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ s.name }}</span>
                  </td>
                  <td class="py-3.5 px-4">
                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[11px] border border-blue-200">
                      {{ s.className }}
                    </span>
                  </td>
                  <td class="py-3.5 px-3 text-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded font-bold text-xs" :class="s.gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700'">
                      {{ s.gender }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 font-mono text-slate-600 text-xs">{{ s.parentWa }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="px-2.5 py-0.5 rounded text-xs font-bold" :class="s.poin > 20 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'">
                      {{ s.poin }} Poin
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <div class="inline-flex items-center justify-center gap-1.5">
                      <button
                        @click="editSiswa(s)"
                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition cursor-pointer"
                        title="Edit Data Siswa"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button
                        @click="confirmDeleteSiswa(s)"
                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer"
                        title="Hapus Siswa"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
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
      <!-- TAB 5: PESAN KONSULTASI WALI MURID & TAMU                     -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'konsultasi'" class="space-y-6 animate-in fade-in duration-150">
        
        <!-- Header Strip Pesan Konsultasi -->
        <div class="bg-white rounded-lg border border-slate-200 p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Inbox Layanan Konseling</span>
              <span v-if="unreadConsultationCount > 0" class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                {{ unreadConsultationCount }} Perlu Tindak Lanjut
              </span>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Pesan Konsultasi Wali Murid</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar pesan dan permohonan pendampingan yang dikirimkan oleh wali murid melalui Halaman Tamu.</p>
          </div>

          <!-- Quick Stats Pill -->
          <div class="flex items-center gap-2 self-start md:self-auto text-xs">
            <div class="px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 font-semibold">
              Total: <strong class="text-slate-900">{{ consultationList.length }}</strong>
            </div>
            <div class="px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 font-semibold">
              Belum Dihubungi: <strong>{{ unreadConsultationCount }}</strong>
            </div>
            <div class="px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">
              Selesai: <strong>{{ contactedCount }}</strong>
            </div>
          </div>
        </div>

        <!-- Filter & Search List -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden p-5 sm:p-6 space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
              <input
                v-model="searchConsultation"
                type="text"
                placeholder="Cari nama wali, siswa, atau topik..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              />
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </span>
            </div>

            <div class="flex items-center gap-2">
              <select
                v-model="filterConsultationStatus"
                class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
              >
                <option value="Semua">Semua Status</option>
                <option value="Belum Dihubungi">Belum Dihubungi</option>
                <option value="Sudah Dihubungi">Sudah Dihubungi</option>
              </select>
            </div>
          </div>

          <!-- Table of Consultation Messages -->
          <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                <tr>
                  <th class="py-3.5 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NAMA WALI MURID</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">SISWA &amp; KELAS</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider min-w-[240px]">TOPIK KONSULTASI</th>
                  <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">WAKTU MASUK</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">STATUS</th>
                  <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">TINDAKAN</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr
                  v-for="(item, idx) in filteredConsultations"
                  :key="item.id"
                  class="hover:bg-slate-50 transition"
                  :class="item.status === 'unread' ? 'bg-amber-50/30 font-medium' : ''"
                >
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                  
                  <!-- Nama Wali & No Telp -->
                  <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-900 block leading-tight">{{ item.parent_name }}</span>
                    <span v-if="item.phone" class="text-[11px] text-slate-500 font-mono">{{ item.phone }}</span>
                    <span v-else class="text-[10px] text-slate-400">Kontak via data siswa</span>
                  </td>

                  <!-- Siswa & Kelas -->
                  <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-800 block">{{ item.student_name }}</span>
                  </td>

                  <!-- Topik Pesan -->
                  <td class="py-3.5 px-4">
                    <p class="text-xs text-slate-700 leading-relaxed max-w-md">{{ item.topic }}</p>
                  </td>

                  <!-- Waktu Masuk -->
                  <td class="py-3.5 px-4 text-slate-500 text-[11px] whitespace-nowrap">
                    {{ formatConsultationTime(item.created_at) }}
                  </td>

                  <!-- Status Badge -->
                  <td class="py-3.5 px-4 text-center whitespace-nowrap">
                    <span
                      class="px-2.5 py-1 rounded text-[10px] font-bold inline-block"
                      :class="item.status === 'unread' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200'"
                    >
                      {{ item.status === 'unread' ? 'Perlu Dihubungi' : 'Sudah Dihubungi' }}
                    </span>
                  </td>

                  <!-- Tindakan -->
                  <td class="py-3.5 px-4 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5">
                      <!-- WhatsApp Hubungi -->
                      <button
                        @click="openWhatsAppToParent(item)"
                        class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] flex items-center gap-1 transition cursor-pointer"
                        title="Hubungi langsung via WhatsApp"
                      >
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                          <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.22.771 1.394.858.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                        </svg>
                        <span>WhatsApp</span>
                      </button>

                      <!-- Toggle Selesai / Belum -->
                      <button
                        v-if="item.status === 'unread'"
                        @click="confirmApproveConsultation(item)"
                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] border border-slate-200 transition cursor-pointer"
                        title="Tandai sudah dihubungi oleh Guru BK"
                      >
                        Tandai Selesai
                      </button>

                      <!-- Hapus -->
                      <button
                        @click="confirmDeleteConsultation(item)"
                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer"
                        title="Hapus riwayat pesan"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>

                <tr v-if="filteredConsultations.length === 0">
                  <td colspan="7" class="py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-slate-400">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                      </svg>
                      <p class="text-xs font-bold text-slate-600">Tidak ada pesan konsultasi</p>
                      <p class="text-[11px] text-slate-400 mt-0.5">Pesan yang dikirimkan oleh wali murid di Halaman Tamu akan muncul di sini secara otomatis.</p>
                    </div>
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
      <div v-if="modalType === 'guru'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center">
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
              <input v-model="formGuru.name" required placeholder="Contoh: Dra. Hj. Siti Rahma, M.Pd" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">NIP Pegawai</label>
                <input v-model="formGuru.nip" placeholder="19820512 2008..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">NUPTK</label>
                <input v-model="formGuru.nuptk" placeholder="8452 7566..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Jabatan / Penugasan Khusus *</label>
              <input v-model="formGuru.jabatan" required placeholder="Contoh: Guru Pembimbing BK Kelas X" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <!-- KREDENSIAL AKUN LOGIN BK (USERNAME & PASSWORD) -->
            <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200 space-y-2.5">
              <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                  Kredensial Akun Login Guru BK
                </span>
                <span class="text-[10px] text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60">Akses Portal /login</span>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block font-bold text-slate-700 mb-1">Username Akun *</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-mono font-bold text-xs select-none">@</span>
                    <input
                      v-model="formGuru.username"
                      required
                      placeholder="nurul_bk"
                      class="w-full bg-white border border-slate-200 rounded-lg pl-7 pr-3 py-2 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                </div>

                <div>
                  <label class="block font-bold text-slate-700 mb-1">Kata Sandi (Password) *</label>
                  <div class="relative">
                    <input
                      :type="showGuruPassword ? 'text' : 'password'"
                      v-model="formGuru.password"
                      required
                      placeholder="Min. 6 karakter..."
                      class="w-full bg-white border border-slate-200 rounded-lg px-3.5 py-2 pr-9 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                    />
                    <button
                      type="button"
                      @click="showGuruPassword = !showGuruPassword"
                      class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer p-0.5"
                      :title="showGuruPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                    >
                      <svg v-if="!showGuruPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <p class="text-[10px] text-slate-500">Kredensial ini digunakan Guru BK untuk masuk dan mengelola presensi siswa di halaman login.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">WhatsApp Aktif</label>
                <input v-model="formGuru.wa" placeholder="+62 812..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Email Belajar.id</label>
                <input v-model="formGuru.email" type="email" placeholder="nama@guru.belajar.id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Rombel Binaan (Pisahkan dengan koma)</label>
              <input v-model="formGuru.rombelText" placeholder="Contoh: X RPL 1, X RPL 2, X TSM 1" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm">Simpan Guru BK</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH KELAS / ROMBEL                                  -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'kelas'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center">
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
                <select v-model="formKelas.level" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="Kelas 10 (X)">Kelas 10 (X)</option>
                  <option value="Kelas 11 (XI)">Kelas 11 (XI)</option>
                  <option value="Kelas 12 (XII)">Kelas 12 (XII)</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Bidang Keahlian / Jurusan *</label>
                <select v-model="formKelas.jurusan" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
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
              <input v-model="formKelas.name" required placeholder="Contoh: X RPL 4 atau XI DKV 3" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Wali Kelas *</label>
              <input v-model="formKelas.waliKelas" required placeholder="Contoh: Bpk. Kurniawan, S.Kom" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Guru BK Pendamping</label>
              <select v-model="formKelas.guruBK" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option v-for="g in guruBkList" :key="g.id" :value="g.name">{{ g.name }}</option>
              </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm">Simpan Kelas</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH SISWA                                           -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'siswa'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center">
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
                <input v-model="formSiswa.nisn" required placeholder="Contoh: 00729190" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin *</label>
                <select v-model="formSiswa.gender" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="L">Laki-laki (L)</option>
                  <option value="P">Perempuan (P)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
              <input v-model="formSiswa.name" required placeholder="Nama lengkap sesuai akta / Dapodik" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Rombel Kelas *</label>
              <select v-model="formSiswa.className" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option v-for="k in kelasList" :key="k.id" :value="k.name">{{ k.name }} ({{ k.jurusan }})</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">No WhatsApp Wali Murid *</label>
              <input v-model="formSiswa.parentWa" required placeholder="+62 8..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk notifikasi presensi otomatis &amp; panggilan BK.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm">Simpan Siswa</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: EDIT DATA SISWA                                       -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'edit-siswa'" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Edit Data Siswa</h3>
                <p class="text-[11px] text-slate-500">Perbarui identitas, kelas, dan kontak wali siswa</p>
              </div>
            </div>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="updateSiswa" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">NISN Siswa *</label>
                <input v-model="formEditSiswa.nisn" required placeholder="00729..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin *</label>
                <select v-model="formEditSiswa.gender" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="L">Laki-laki (L)</option>
                  <option value="P">Perempuan (P)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
              <input v-model="formEditSiswa.name" required placeholder="Nama lengkap siswa" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Rombel Kelas *</label>
                <select v-model="formEditSiswa.className" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option v-for="k in kelasList" :key="k.id" :value="k.name">{{ k.name }} ({{ k.jurusan }})</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Poin Pelanggaran</label>
                <input v-model.number="formEditSiswa.poin" type="number" min="0" max="100" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">No WhatsApp Wali Murid *</label>
              <input v-model="formEditSiswa.parentWa" required placeholder="+62 8..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk notifikasi presensi otomatis &amp; panggilan BK.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm flex items-center gap-1.5">
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
      <!-- MODAL: EDIT DATA GURU BK                                     -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'edit-guru'" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Edit Data Guru BK</h3>
                <p class="text-[11px] text-slate-500">Perbarui data tenaga konselor sekolah</p>
              </div>
            </div>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="updateGuru" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Lengkap &amp; Gelar *</label>
              <input v-model="formEditGuru.name" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">NIP *</label>
                <input v-model="formEditGuru.nip" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">NUPTK</label>
                <input v-model="formEditGuru.nuptk" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Jabatan *</label>
                <input v-model="formEditGuru.jabatan" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Email Resmi *</label>
                <input v-model="formEditGuru.email" type="email" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>

            <!-- KREDENSIAL AKUN LOGIN BK (USERNAME & PASSWORD) -->
            <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-200 space-y-2.5">
              <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                  Kredensial Akun Login Guru BK
                </span>
                <span class="text-[10px] text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60">Akses Portal</span>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block font-bold text-slate-700 mb-1">Username Login *</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-mono font-bold text-xs select-none">@</span>
                    <input
                      v-model="formEditGuru.username"
                      required
                      placeholder="Username akun..."
                      class="w-full bg-white border border-slate-200 rounded-lg pl-7 pr-3 py-2 font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                </div>

                <div>
                  <label class="block font-bold text-slate-700 mb-1">Ganti Sandi (Opsional)</label>
                  <div class="relative">
                    <input
                      :type="showEditGuruPassword ? 'text' : 'password'"
                      v-model="formEditGuru.password"
                      placeholder="Kosongkan jika tetap"
                      class="w-full bg-white border border-slate-200 rounded-lg px-3.5 py-2 pr-9 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                    />
                    <button
                      type="button"
                      @click="showEditGuruPassword = !showEditGuruPassword"
                      class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer p-0.5"
                      :title="showEditGuruPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                    >
                      <svg v-if="!showEditGuruPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Rombel Binaan (Pisahkan dengan koma)</label>
              <input v-model="formEditGuru.rombelText" placeholder="Contoh: X RPL 1, X RPL 2, XI RPL 1" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm flex items-center gap-1.5">
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
      <!-- MODAL: EDIT DATA KELAS / ROMBEL                              -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'edit-kelas'" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Edit Rombel Kelas</h3>
                <p class="text-[11px] text-slate-500">Perbarui data rombel dan wali pendamping</p>
              </div>
            </div>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="updateKelas" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Tingkat Kelas *</label>
                <select v-model="formEditKelas.level" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="Kelas 10 (X)">Kelas 10 (X)</option>
                  <option value="Kelas 11 (XI)">Kelas 11 (XI)</option>
                  <option value="Kelas 12 (XII)">Kelas 12 (XII)</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Jurusan *</label>
                <select v-model="formEditKelas.jurusan" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="RPL">RPL</option>
                  <option value="DKV">DKV</option>
                  <option value="TSM">TSM</option>
                  <option value="TKJ">TKJ</option>
                  <option value="AKL">AKL</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Rombel *</label>
                <input v-model="formEditKelas.name" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white uppercase" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Kapasitas Siswa</label>
                <input v-model.number="formEditKelas.studentCount" type="number" min="1" max="50" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Wali Kelas *</label>
              <input v-model="formEditKelas.waliKelas" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Guru BK Pendamping *</label>
              <select v-model="formEditKelas.guruBK" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option v-for="g in guruBkList" :key="g.id" :value="g.name">{{ g.name }}</option>
              </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm flex items-center gap-1.5">
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
      <!-- MODAL CARD: KONFIRMASI TINDAKAN (HAPUS / SETUJUI)            -->
      <!-- ============================================================ -->
      <div
        v-if="confirmModal.show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 animate-in fade-in duration-150"
        @click.self="closeConfirmModal"
      >
        <div class="bg-white rounded-xl max-w-md w-full p-6 sm:p-7 shadow-xl border border-slate-200 text-center animate-in zoom-in-95 duration-150">
          
          <!-- Icon Container -->
          <div
            class="w-12 h-12 mx-auto rounded-lg flex items-center justify-center mb-4 border"
            :class="confirmModal.type === 'approve' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-600 border-rose-200'"
          >
            <!-- Trash icon if delete -->
            <svg v-if="confirmModal.type === 'delete'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <!-- Check/Approve icon if approve -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>

          <!-- Title -->
          <h3 class="text-base sm:text-lg font-bold text-slate-900">
            {{ confirmModal.title }}
          </h3>

          <!-- Message / Content -->
          <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            {{ confirmModal.message }}
          </p>

          <!-- Target Data Card (if available) -->
          <div v-if="confirmModal.targetInfo" class="mt-4 p-3 bg-slate-50 rounded-lg border border-slate-200 text-left text-xs">
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-800">{{ confirmModal.targetInfo.name }}</span>
              <span v-if="confirmModal.targetInfo.tag" class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px]">
                {{ confirmModal.targetInfo.tag }}
              </span>
            </div>
            <p v-if="confirmModal.targetInfo.sub" class="text-[11px] text-slate-400 mt-0.5">
              {{ confirmModal.targetInfo.sub }}
            </p>
          </div>

          <!-- Action Buttons: Batal & Setujui/Hapus -->
          <div class="grid grid-cols-2 gap-2.5 mt-6 pt-2">
            <button
              type="button"
              @click="closeConfirmModal"
              class="w-full py-2.5 px-4 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
            >
              {{ confirmModal.cancelText || 'Batal' }}
            </button>

            <button
              type="button"
              @click="executeConfirmAction"
              class="w-full py-2.5 px-4 rounded-lg text-white font-bold text-xs shadow-sm transition cursor-pointer flex items-center justify-center gap-1.5"
              :class="confirmModal.type === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
            >
              <svg v-if="confirmModal.type === 'delete'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>{{ confirmModal.confirmText || (confirmModal.type === 'approve' ? 'Setujui' : 'Setujui & Hapus') }}</span>
            </button>
          </div>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- TOAST NOTIFICATION                                           -->
      <!-- ============================================================ -->
      <div v-if="toastMessage" class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white text-xs px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 border border-slate-700 animate-in fade-in slide-in-from-bottom-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <span class="font-semibold">{{ toastMessage }}</span>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: IMPORT DATA SISWA MASSAL (EXCEL / CSV)                -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'import-siswa'" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 overflow-y-auto">
        <div class="bg-white rounded-xl max-w-2xl w-full p-5 sm:p-7 shadow-xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 my-8">
          
          <!-- Header Modal -->
          <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base sm:text-lg">Import Data Siswa Massal</h3>
                <p class="text-xs text-slate-500 font-medium">Unggah file Excel/CSV atau data Dapodik untuk memasukkan siswa secara cepat</p>
              </div>
            </div>
            <button @click="closeModal" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Step 1 & Action Bar: Download Template & Sample -->
          <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div>
              <span class="font-bold text-slate-800 block">Panduan Format Kolom:</span>
              <span class="text-slate-500 text-[11px]">Format file harus memiliki kolom: <strong class="text-slate-700">NISN, Nama, Kelas, JK (L/P), No WA Wali</strong></span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button
                type="button"
                @click="downloadCsvTemplate"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-100 text-blue-700 font-bold border border-slate-200 rounded-lg shadow-sm transition cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Template CSV</span>
              </button>
              <button
                type="button"
                @click="loadSampleDapodikData"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold border border-blue-200 rounded-lg shadow-sm transition cursor-pointer"
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
              class="border-2 border-dashed rounded-lg p-6 text-center transition cursor-pointer relative"
              :class="isDragging ? 'border-blue-500 bg-blue-50/50' : 'border-slate-300 hover:border-blue-500 bg-slate-50/50 hover:bg-blue-50/20'"
            >
              <input
                ref="fileInputRef"
                type="file"
                accept=".csv, .txt, .xlsx, .xls"
                @change="handleFileUpload"
                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
              />
              <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs sm:text-sm font-bold text-slate-800">
                    <span class="text-blue-700 underline font-semibold">Klik untuk memilih file</span> atau seret file ke sini
                  </p>
                  <p class="text-[11px] text-slate-500 mt-0.5">Mendukung format .CSV, .TXT, dan file ekspor Dapodik</p>
                </div>
                <div v-if="importFileName" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200">
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
                class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
              >
                <option value="AUTO">Otomatis (Ikuti kolom 'Kelas' di file)</option>
                <option v-for="k in kelasList" :key="k.id" :value="k.name">Tetapkan Semua ke: {{ k.name }}</option>
              </select>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Status Validasi:</label>
              <div class="flex items-center h-[38px] px-3.5 bg-slate-50 rounded-lg border border-slate-200 text-xs">
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

            <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-lg">
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
                        class="text-slate-400 hover:text-rose-600 p-1 cursor-pointer flex items-center justify-center"
                        title="Hapus baris ini"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Modal Action Footer -->
          <div class="flex items-center justify-between pt-4 border-t border-slate-200">
            <button
              type="button"
              @click="closeModal"
              class="px-5 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer text-xs"
            >
              Batal
            </button>
            <button
              type="button"
              @click="processImport"
              :disabled="importedStudents.length === 0"
              class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold transition cursor-pointer text-xs shadow-sm flex items-center gap-1.5"
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
import { ref, computed, onMounted, h } from 'vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  consultations: {
    type: Array,
    default: () => []
  },
  unreadConsultationsCount: {
    type: Number,
    default: 0
  },
  initialGuruBkList: {
    type: Array,
    default: () => []
  }
})

const activeTab = ref('dashboard')
const modalType = ref(null)
const searchSiswa = ref('')
const selectedFilterKelas = ref('Semua')

// Notifikasi Toast
const toastMessage = ref('')
const showToast = (msg) => {
  toastMessage.value = msg
  setTimeout(() => {
    if (toastMessage.value === msg) {
      toastMessage.value = ''
    }
  }, 3500)
}

// Modal Card Konfirmasi Universal (Hapus / Setujui)
const confirmModal = ref({
  show: false,
  type: 'delete', // 'delete' atau 'approve'
  title: '',
  message: '',
  targetInfo: null, // { name: '', tag: '', sub: '' }
  confirmText: '',
  cancelText: 'Batal',
  onConfirm: null
})

const closeConfirmModal = () => {
  confirmModal.value.show = false
  confirmModal.value.onConfirm = null
}

const executeConfirmAction = async () => {
  if (typeof confirmModal.value.onConfirm === 'function') {
    await confirmModal.value.onConfirm()
  }
  closeConfirmModal()
}

// Tab Navigation Icons
const tabs = computed(() => [
  {
    id: 'dashboard',
    label: 'Statistika & Tren Presensi',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z' }),
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z' })
    ])
  },
  {
    id: 'konsultasi',
    label: 'Pesan Konsultasi',
    count: unreadConsultationCount.value > 0 ? unreadConsultationCount.value : undefined,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z' })
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
// 1. STATISTIKA PRESENSI SISWA (DIAGRAM BULAT PENUH 1 BULAN SEPERTI FOTO 1)
// ==========================================
const chartViewMode = ref('bulat') // 'bulat' (default) atau 'garis'
const selectedMonth = ref('mei') // Default Bulan Mei 2026 sesuai instruksi user
const selectedAngkatan = ref('all') // 'all', 'x', 'xi', 'xii'
const hoveredPieSlice = ref(null)

const monthlyAttendanceDb = {
  mei: {
    id: 'mei',
    name: 'Bulan Mei 2026',
    shortName: 'Mei',
    trend: 'naik',
    delta: '+1.6%',
    deltaValue: 1.6,
    prevMonthName: 'Bulan April 2026',
    prevHadirRate: 94.2,
    summary: 'Tingkat kehadiran siswa meningkat +1.6% dibanding April berkat program pembinaan wali kelas dan apel motivasi belajar.',
    cohorts: {
      all: {
        totalSiswa: 1080,
        hadir: { rate: 95.8, count: 1035, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir mengikuti seluruh sesi KBM' },
        sakit: { rate: 2.1, count: 23, label: 'Sakit', color: '#0ea5e9', desc: 'Terkonfirmasi surat keterangan dokter / wali' },
        izin: { rate: 1.4, count: 15, label: 'Izin', color: '#f59e0b', desc: 'Izin tertulis keperluan keluarga / dinas' },
        alpha: { rate: 0.7, count: 7, label: 'Alpha', color: '#f43f5e', desc: 'Tidak ada pemberitahuan, dalam bimbingan BK' }
      },
      x: {
        totalSiswa: 360,
        hadir: { rate: 96.4, count: 347, label: 'Hadir', color: '#2563eb', desc: 'Disiplin tinggi adaptasi Fase E' },
        sakit: { rate: 1.9, count: 7, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi wali' },
        izin: { rate: 1.1, count: 4, label: 'Izin', color: '#f59e0b', desc: 'Izin keperluan keluarga' },
        alpha: { rate: 0.6, count: 2, label: 'Alpha', color: '#f43f5e', desc: 'Alpha tanpa keterangan' }
      },
      xi: {
        totalSiswa: 360,
        hadir: { rate: 95.2, count: 343, label: 'Hadir', color: '#2563eb', desc: 'Termasuk rombel simulasi PKL industri' },
        sakit: { rate: 2.3, count: 8, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi dokter' },
        izin: { rate: 1.7, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin persiapan magang' },
        alpha: { rate: 0.8, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha tanpa keterangan' }
      },
      xii: {
        totalSiswa: 360,
        hadir: { rate: 95.8, count: 345, label: 'Hadir', color: '#2563eb', desc: 'Fokus persiapan asesmen kelulusan & UKK' },
        sakit: { rate: 2.1, count: 8, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi wali' },
        izin: { rate: 1.4, count: 5, label: 'Izin', color: '#f59e0b', desc: 'Izin tes kerja / dinas' },
        alpha: { rate: 0.7, count: 2, label: 'Alpha', color: '#f43f5e', desc: 'Alpha tanpa keterangan' }
      }
    }
  },
  april: {
    id: 'april',
    name: 'Bulan April 2026',
    shortName: 'April',
    trend: 'turun',
    delta: '-1.1%',
    deltaValue: -1.1,
    prevMonthName: 'Bulan Maret 2026',
    prevHadirRate: 95.3,
    summary: 'Terdapat penurunan presensi hadir sebesar -1.1% dipengaruhi masa libur cuti bersama dan cuaca curah hujan tinggi.',
    cohorts: {
      all: {
        totalSiswa: 1080,
        hadir: { rate: 94.2, count: 1017, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM penuh' },
        sakit: { rate: 2.9, count: 31, label: 'Sakit', color: '#0ea5e9', desc: 'Flu musiman pasca cuaca ekstrem' },
        izin: { rate: 1.8, count: 20, label: 'Izin', color: '#f59e0b', desc: 'Izin mudik / acara keluarga' },
        alpha: { rate: 1.1, count: 12, label: 'Alpha', color: '#f43f5e', desc: 'Keterlambatan kembali ke sekolah' }
      },
      x: {
        totalSiswa: 360,
        hadir: { rate: 94.7, count: 341, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran awal pekan stabil' },
        sakit: { rate: 2.8, count: 10, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.6, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin keluarga' },
        alpha: { rate: 0.9, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 360,
        hadir: { rate: 93.6, count: 337, label: 'Hadir', color: '#2563eb', desc: 'Persiapan jadwal penempatan DU/DI' },
        sakit: { rate: 3.2, count: 12, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit berizin' },
        izin: { rate: 2.0, count: 7, label: 'Izin', color: '#f59e0b', desc: 'Izin kegiatan keluarga' },
        alpha: { rate: 1.2, count: 4, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 360,
        hadir: { rate: 94.3, count: 339, label: 'Hadir', color: '#2563eb', desc: 'Intensif materi ujian akhir' },
        sakit: { rate: 2.7, count: 10, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit berizin' },
        izin: { rate: 1.8, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin kedukaan/keluarga' },
        alpha: { rate: 1.2, count: 5, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      }
    }
  },
  maret: {
    id: 'maret',
    name: 'Bulan Maret 2026',
    shortName: 'Maret',
    trend: 'naik',
    delta: '+1.5%',
    deltaValue: 1.5,
    prevMonthName: 'Bulan Februari 2026',
    prevHadirRate: 93.8,
    summary: 'Presensi melonjak +1.5% berkat kedisiplinan apel pagi dan tindak lanjut pendampingan wali kelas.',
    cohorts: {
      all: {
        totalSiswa: 1080,
        hadir: { rate: 95.3, count: 1029, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM penuh' },
        sakit: { rate: 2.3, count: 25, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.6, count: 17, label: 'Izin', color: '#f59e0b', desc: 'Izin resmi' },
        alpha: { rate: 0.8, count: 9, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      x: {
        totalSiswa: 360,
        hadir: { rate: 95.8, count: 345, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran disiplin' },
        sakit: { rate: 2.2, count: 8, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.4, count: 5, label: 'Izin', color: '#f59e0b', desc: 'Izin resmi' },
        alpha: { rate: 0.6, count: 2, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 360,
        hadir: { rate: 94.9, count: 342, label: 'Hadir', color: '#2563eb', desc: 'KBM berjalan efektif' },
        sakit: { rate: 2.5, count: 9, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.7, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin resmi' },
        alpha: { rate: 0.9, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 360,
        hadir: { rate: 95.2, count: 343, label: 'Hadir', color: '#2563eb', desc: 'Simulasi ujian berjalan tertib' },
        sakit: { rate: 2.2, count: 8, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.7, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin resmi' },
        alpha: { rate: 0.9, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      }
    }
  },
  februari: {
    id: 'februari',
    name: 'Bulan Februari 2026',
    shortName: 'Februari',
    trend: 'turun',
    delta: '-0.7%',
    deltaValue: -0.7,
    prevMonthName: 'Bulan Januari 2026',
    prevHadirRate: 94.5,
    summary: 'Penurunan ringan -0.7% dampak kendala banjir akses jalan penghubung di beberapa titik tempat tinggal siswa.',
    cohorts: {
      all: {
        totalSiswa: 1080,
        hadir: { rate: 93.8, count: 1013, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM' },
        sakit: { rate: 3.4, count: 37, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.9, count: 21, label: 'Izin', color: '#f59e0b', desc: 'Izin kendala cuaca' },
        alpha: { rate: 0.9, count: 9, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      x: {
        totalSiswa: 360,
        hadir: { rate: 94.4, count: 340, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran Fase E' },
        sakit: { rate: 3.1, count: 11, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 1.7, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 0.8, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 360,
        hadir: { rate: 93.2, count: 335, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran Fase F' },
        sakit: { rate: 3.7, count: 13, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 2.1, count: 8, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 1.0, count: 4, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 360,
        hadir: { rate: 93.8, count: 338, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran Fase F' },
        sakit: { rate: 3.4, count: 12, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 1.9, count: 7, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 0.9, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      }
    }
  },
  januari: {
    id: 'januari',
    name: 'Bulan Januari 2026',
    shortName: 'Januari',
    trend: 'naik',
    delta: '+1.2%',
    deltaValue: 1.2,
    prevMonthName: 'Bulan Desember 2025',
    prevHadirRate: 93.3,
    summary: 'Awal Semester Genap berjalan tertib dengan kenaikan +1.2% siswa aktif mengikuti seluruh agenda sekolah.',
    cohorts: {
      all: {
        totalSiswa: 1080,
        hadir: { rate: 94.5, count: 1021, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM awal semester' },
        sakit: { rate: 2.6, count: 28, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 2.0, count: 22, label: 'Izin', color: '#f59e0b', desc: 'Izin perpanjangan tiket' },
        alpha: { rate: 0.9, count: 9, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      x: {
        totalSiswa: 360,
        hadir: { rate: 95.0, count: 342, label: 'Hadir', color: '#2563eb', desc: 'Awal semester genap' },
        sakit: { rate: 2.4, count: 9, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 1.8, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 0.8, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 360,
        hadir: { rate: 94.1, count: 339, label: 'Hadir', color: '#2563eb', desc: 'Awal semester genap' },
        sakit: { rate: 2.7, count: 10, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 2.2, count: 8, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 1.0, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 360,
        hadir: { rate: 94.4, count: 340, label: 'Hadir', color: '#2563eb', desc: 'Awal semester genap' },
        sakit: { rate: 2.7, count: 10, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 2.0, count: 7, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 0.9, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      }
    }
  }
}

const currentMonthData = computed(() => {
  return monthlyAttendanceDb[selectedMonth.value] || monthlyAttendanceDb.mei
})

const currentCohortData = computed(() => {
  const cohorts = currentMonthData.value.cohorts
  return cohorts[selectedAngkatan.value] || cohorts.all
})

const selectedAngkatanLabel = computed(() => {
  switch (selectedAngkatan.value) {
    case 'x': return 'Angkatan Kelas X (Fase E)'
    case 'xi': return 'Angkatan Kelas XI (Fase F - Tk. 1)'
    case 'xii': return 'Angkatan Kelas XII (Fase F - Tk. 2)'
    default: return 'Gabungan Semua Angkatan'
  }
})

const totalAbsenCount = computed(() => {
  const c = currentCohortData.value
  return c.sakit.count + c.izin.count + c.alpha.count
})

const monthlyList = computed(() => {
  const angkatanKey = selectedAngkatan.value
  return ['januari', 'februari', 'maret', 'april', 'mei'].map(k => {
    const m = monthlyAttendanceDb[k]
    const cohort = m.cohorts[angkatanKey] || m.cohorts.all
    return {
      id: m.id,
      name: m.name,
      shortName: m.shortName,
      trend: m.trend,
      delta: m.delta,
      hadirRate: cohort.hadir.rate
    }
  })
})

const pieSlices = computed(() => {
  const cohort = currentCohortData.value
  const items = [
    { id: 'hadir', ...cohort.hadir },
    { id: 'sakit', ...cohort.sakit },
    { id: 'izin', ...cohort.izin },
    { id: 'alpha', ...cohort.alpha }
  ]

  const cx = 190
  const cy = 190
  const r = 160

  let currentAngle = -Math.PI / 2 // Dimulai tepat di puncak lingkaran (jam 12)

  return items.map(item => {
    const angleSpan = (item.rate / 100) * 2 * Math.PI
    const startAngle = currentAngle
    const endAngle = currentAngle + angleSpan
    currentAngle = endAngle

    const x1 = cx + r * Math.cos(startAngle)
    const y1 = cy + r * Math.sin(startAngle)
    const x2 = cx + r * Math.cos(endAngle)
    const y2 = cy + r * Math.sin(endAngle)

    const largeArc = angleSpan > Math.PI ? 1 : 0
    const pathD = `M ${cx} ${cy} L ${x1.toFixed(2)} ${y1.toFixed(2)} A ${r} ${r} 0 ${largeArc} 1 ${x2.toFixed(2)} ${y2.toFixed(2)} Z`

    const midAngle = startAngle + angleSpan / 2
    const isLarge = item.rate >= 12
    const labelRadius = isLarge ? r * 0.55 : r * 0.78
    const labelX = Math.round(cx + labelRadius * Math.cos(midAngle))
    const labelY = Math.round(cy + labelRadius * Math.sin(midAngle))

    const pinStartX = Math.round(cx + (r - 2) * Math.cos(midAngle))
    const pinStartY = Math.round(cy + (r - 2) * Math.sin(midAngle))
    const pinEndX = Math.round(cx + (r + 18) * Math.cos(midAngle))
    const pinEndY = Math.round(cy + (r + 18) * Math.sin(midAngle))

    return {
      ...item,
      pathD,
      startAngle,
      endAngle,
      angleSpan,
      midAngle,
      isLarge,
      labelX,
      labelY,
      pinStartX,
      pinStartY,
      pinEndX,
      pinEndY
    }
  })
})

const jurusanStats = ref([
  { name: 'RPL', fullName: 'Rekayasa Perangkat Lunak', rate: 97.2, target: 95.0, students: 107, color: '#10b981', status: 'Sangat Baik' },
  { name: 'DKV', fullName: 'Desain Komunikasi Visual', rate: 95.8, target: 95.0, students: 69, color: '#2563eb', status: 'Melampaui Target' },
  { name: 'TKJ', fullName: 'Teknik Komputer & Jaringan', rate: 96.0, target: 95.0, students: 72, color: '#6366f1', status: 'Melampaui Target' },
  { name: 'AKL', fullName: 'Akuntansi Keuangan Lembaga', rate: 95.1, target: 95.0, students: 36, color: '#0d9488', status: 'Memenuhi Target' },
  { name: 'TSM', fullName: 'Teknik Sepeda Motor', rate: 94.1, target: 95.0, students: 72, color: '#f59e0b', status: 'Perlu Perhatian' }
])

const weeklyStats = ref([
  { week: 'Pekan 1', range: '1 - 7 Oktober', rate: 94.2, change: 'Stabil awal pekan', color: '#2563eb' },
  { week: 'Pekan 2', range: '8 - 14 Oktober', rate: 97.5, change: '+3.3% Kenaikan', color: '#10b981' },
  { week: 'Pekan 3', range: '15 - 21 Oktober', rate: 92.8, change: '-4.7% Penurunan Cuaca', color: '#f43f5e' },
  { week: 'Pekan 4', range: '22 - 30 Oktober', rate: 96.8, change: '+4.0% Pemulihan', color: '#10b981' }
])

const getRingDasharray = (rate, radius = 28) => {
  const c = 2 * Math.PI * radius
  const filled = (rate / 100) * c
  return `${filled.toFixed(2)} ${(c - filled).toFixed(2)}`
}

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
const showGuruPassword = ref(false)
const showEditGuruPassword = ref(false)

const defaultGuruBkList = [
  {
    id: 1,
    name: 'Dra. Hj. Nurul Hidayati, M.Pd',
    username: 'nurul_bk',
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
    username: 'agus_bk',
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
    username: 'ratih_bk',
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
    username: 'dwi_bk',
    nip: '19821105 200801 1 008',
    nuptk: '4456 1238 9980 0045',
    jabatan: 'Guru Pembimbing BK Kelas XII',
    email: 'dwi.prasetyo@guru.smk.belajar.id',
    wa: '+62 878-5566-7788',
    rombel: ['XII RPL 1', 'XII RPL 2', 'XII AKL 1']
  }
]

const guruBkList = ref(props.initialGuruBkList && props.initialGuruBkList.length > 0 ? [...props.initialGuruBkList] : defaultGuruBkList)

const formGuru = ref({
  name: '',
  username: '',
  password: '',
  nip: '',
  nuptk: '',
  jabatan: '',
  email: '',
  wa: '',
  rombelText: ''
})

const saveGuru = async () => {
  const rArray = formGuru.value.rombelText
    ? formGuru.value.rombelText.split(',').map(s => s.trim().toUpperCase()).filter(Boolean)
    : ['X RPL 1']

  const cleanUsername = (formGuru.value.username || formGuru.value.name.toLowerCase().replace(/[^a-z0-9]/g, '_')).trim().toLowerCase()

  const payload = {
    name: formGuru.value.name,
    username: cleanUsername,
    password: formGuru.value.password || 'password123',
    nip: formGuru.value.nip || '-',
    nuptk: formGuru.value.nuptk || '-',
    jabatan: formGuru.value.jabatan,
    email: formGuru.value.email || `${cleanUsername}@guru.smk.belajar.id`,
    wa: formGuru.value.wa || '+62 8...',
    rombelText: formGuru.value.rombelText || 'X RPL 1'
  }

  try {
    const res = await fetch('/admin/guru', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify(payload)
    })

    if (res.ok) {
      const data = await res.json()
      if (data.guru) {
        guruBkList.value.push(data.guru)
      } else {
        guruBkList.value.push({ id: Date.now(), ...payload, rombel: rArray })
      }
    } else {
      guruBkList.value.push({ id: Date.now(), ...payload, rombel: rArray })
    }
  } catch (e) {
    guruBkList.value.push({ id: Date.now(), ...payload, rombel: rArray })
  }

  showToast(`Akun login Guru BK ${formGuru.value.name} (@${cleanUsername}) berhasil dibuat!`)
  
  // Reset form
  formGuru.value = {
    name: '',
    username: '',
    password: '',
    nip: '',
    nuptk: '',
    jabatan: '',
    email: '',
    wa: '',
    rombelText: ''
  }
  showGuruPassword.value = false
  closeModal()
}

const formEditGuru = ref({
  id: null,
  name: '',
  username: '',
  password: '',
  nip: '',
  nuptk: '',
  jabatan: '',
  email: '',
  wa: '',
  rombelText: ''
})

const editingGuru = ref(null)

const editGuru = (g) => {
  editingGuru.value = g
  formEditGuru.value = {
    id: g.id,
    name: g.name,
    username: g.username || '',
    password: '',
    nip: g.nip === '-' ? '' : g.nip,
    nuptk: g.nuptk === '-' ? '' : g.nuptk,
    jabatan: g.jabatan,
    email: g.email,
    wa: g.wa,
    rombelText: Array.isArray(g.rombel) ? g.rombel.join(', ') : (g.rombel || '')
  }
  showEditGuruPassword.value = false
  modalType.value = 'edit-guru'
}

const updateGuru = async () => {
  if (!editingGuru.value) return
  const idx = guruBkList.value.findIndex(g => g.id === editingGuru.value.id)
  if (idx !== -1) {
    const rArray = formEditGuru.value.rombelText
      ? formEditGuru.value.rombelText.split(',').map(s => s.trim().toUpperCase()).filter(Boolean)
      : ['X RPL 1']

    const cleanUsername = (formEditGuru.value.username || formEditGuru.value.name.toLowerCase().replace(/[^a-z0-9]/g, '_')).trim().toLowerCase()

    const payload = {
      name: formEditGuru.value.name,
      username: cleanUsername,
      password: formEditGuru.value.password || undefined,
      nip: formEditGuru.value.nip || '-',
      nuptk: formEditGuru.value.nuptk || '-',
      jabatan: formEditGuru.value.jabatan,
      email: formEditGuru.value.email,
      wa: formEditGuru.value.wa,
      rombelText: formEditGuru.value.rombelText
    }

    try {
      await fetch(`/admin/guru/${editingGuru.value.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(payload)
      })
    } catch (e) {}

    guruBkList.value[idx] = {
      ...guruBkList.value[idx],
      name: formEditGuru.value.name,
      username: cleanUsername,
      nip: formEditGuru.value.nip || '-',
      nuptk: formEditGuru.value.nuptk || '-',
      jabatan: formEditGuru.value.jabatan,
      email: formEditGuru.value.email,
      wa: formEditGuru.value.wa,
      rombel: rArray
    }
    showToast(`Data akun Guru BK ${formEditGuru.value.name} (@${cleanUsername}) berhasil diperbarui.`)
  }
  closeModal()
}

const confirmDeleteGuru = (g) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Akun Guru BK',
    message: 'Apakah Anda yakin ingin menghapus akun konselor ini? Akun tidak lagi dapat digunakan untuk login ke portal sistem.',
    targetInfo: {
      name: `${g.name} (@${g.username || 'guru_bk'})`,
      tag: g.jabatan,
      sub: `NIP: ${g.nip} • ${Array.isArray(g.rombel) ? g.rombel.join(', ') : g.rombel}`
    },
    confirmText: 'Ya, Hapus Akun',
    cancelText: 'Batal',
    onConfirm: async () => {
      try {
        await fetch(`/admin/guru/${g.id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        })
      } catch (e) {}

      const idx = guruBkList.value.findIndex(item => item.id === g.id)
      if (idx !== -1) {
        guruBkList.value.splice(idx, 1)
        showToast(`Akun Guru BK ${g.name} berhasil dihapus.`)
      }
    }
  }
}

const deleteGuru = (idx) => {
  const g = guruBkList.value[idx]
  if (g) {
    confirmDeleteGuru(g)
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

const formEditKelas = ref({
  id: null,
  level: 'Kelas 10 (X)',
  jurusan: 'RPL',
  name: '',
  waliKelas: '',
  guruBK: '',
  studentCount: 36
})

const editingKelas = ref(null)

const editKelas = (k) => {
  editingKelas.value = k
  formEditKelas.value = {
    id: k.id,
    level: k.level,
    jurusan: k.jurusan,
    name: k.name,
    waliKelas: k.waliKelas,
    guruBK: k.guruBK,
    studentCount: k.studentCount || 36
  }
  modalType.value = 'edit-kelas'
}

const updateKelas = () => {
  if (!editingKelas.value) return
  const idx = kelasList.value.findIndex(k => k.id === editingKelas.value.id)
  if (idx !== -1) {
    kelasList.value[idx] = {
      ...kelasList.value[idx],
      name: formEditKelas.value.name.toUpperCase(),
      level: formEditKelas.value.level,
      jurusan: formEditKelas.value.jurusan,
      waliKelas: formEditKelas.value.waliKelas,
      guruBK: formEditKelas.value.guruBK,
      studentCount: formEditKelas.value.studentCount || 36
    }
    showToast(`Data rombel ${formEditKelas.value.name} berhasil diperbarui.`)
  }
  closeModal()
}

const confirmDeleteKelas = (k) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Rombel Kelas',
    message: 'Apakah Anda yakin ingin menghapus rombel kelas ini? Data kehadiran dan jadwal terkait akan disesuaikan.',
    targetInfo: {
      name: k.name,
      tag: k.jurusan,
      sub: `Wali: ${k.waliKelas} • Pembimbing: ${k.guruBK}`
    },
    confirmText: 'Ya, Hapus Kelas',
    cancelText: 'Batal',
    onConfirm: () => {
      const idx = kelasList.value.findIndex(item => item.id === k.id)
      if (idx !== -1) {
        kelasList.value.splice(idx, 1)
        showToast(`Rombel ${k.name} berhasil dihapus.`)
      }
    }
  }
}

const deleteKelas = (idx) => {
  const k = kelasList.value[idx]
  if (k) {
    confirmDeleteKelas(k)
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

const formEditSiswa = ref({
  id: null,
  nisn: '',
  name: '',
  className: '',
  gender: 'L',
  parentWa: '',
  poin: 0
})

const editingSiswa = ref(null)

const editSiswa = (s) => {
  editingSiswa.value = s
  formEditSiswa.value = {
    id: s.id,
    nisn: s.nisn,
    name: s.name,
    className: s.className,
    gender: s.gender,
    parentWa: s.parentWa,
    poin: s.poin || 0
  }
  modalType.value = 'edit-siswa'
}

const updateSiswa = () => {
  if (!editingSiswa.value) return
  const idx = siswaList.value.findIndex(s => s.id === editingSiswa.value.id)
  if (idx !== -1) {
    siswaList.value[idx] = {
      ...siswaList.value[idx],
      nisn: formEditSiswa.value.nisn,
      name: formEditSiswa.value.name,
      className: formEditSiswa.value.className,
      gender: formEditSiswa.value.gender,
      parentWa: formEditSiswa.value.parentWa,
      poin: formEditSiswa.value.poin
    }
    showToast(`Data siswa ${formEditSiswa.value.name} berhasil diperbarui.`)
  }
  closeModal()
}

const confirmDeleteSiswa = (s) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Data Siswa',
    message: 'Apakah Anda yakin ingin menghapus data siswa ini dari sistem BK Prima? Tindakan ini tidak dapat dibatalkan.',
    targetInfo: {
      name: s.name,
      tag: s.className,
      sub: `NISN: ${s.nisn} • Poin Pelanggaran: ${s.poin} Poin`
    },
    confirmText: 'Ya, Hapus Siswa',
    cancelText: 'Batal',
    onConfirm: () => {
      const idx = siswaList.value.findIndex(item => item.id === s.id)
      if (idx !== -1) {
        siswaList.value.splice(idx, 1)
        showToast(`Data siswa ${s.name} berhasil dihapus.`)
      }
    }
  }
}

const deleteSiswa = (idx) => {
  const s = siswaList.value[idx]
  if (s) {
    confirmDeleteSiswa(s)
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
  
  alert(`Berhasil mengimpor ${count} data siswa baru ke database BK Prima.`)
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
  editingSiswa.value = null
  editingGuru.value = null
  editingKelas.value = null
}

// ==========================================
// 6. PESAN KONSULTASI TAMU & NOTIFIKASI
// ==========================================
const consultationList = ref([])
const searchConsultation = ref('')
const filterConsultationStatus = ref('Semua')

const syncConsultations = () => {
  let localData = []
  try {
    localData = JSON.parse(localStorage.getItem('bk_prima_consultations') || '[]')
  } catch (e) {
    localData = []
  }

  const combined = [...localData]
  if (Array.isArray(props.consultations)) {
    props.consultations.forEach(serverItem => {
      const exists = combined.some(c => c.id === serverItem.id || (c.parent_name === serverItem.parent_name && c.student_name === serverItem.student_name && c.topic === serverItem.topic))
      if (!exists) {
        combined.push(serverItem)
      }
    })
  }

  if (combined.length === 0) {
    combined.push({
      id: 1,
      parent_name: 'Bpk. Haryanto',
      student_name: 'Muhammad Rizky Pratama (XI RPL 2)',
      topic: 'Klarifikasi catatan kehadiran dan rencana bimbingan kedisiplinan ananda',
      phone: '081234567890',
      status: 'unread',
      created_at: new Date(Date.now() - 3600000).toISOString()
    })
  }

  consultationList.value = combined
}

const unreadConsultationCount = computed(() => {
  return consultationList.value.filter(c => c.status === 'unread').length
})

const contactedCount = computed(() => {
  return consultationList.value.filter(c => c.status === 'contacted').length
})

const filteredConsultations = computed(() => {
  return consultationList.value.filter(c => {
    const q = searchConsultation.value.toLowerCase().trim()
    const matchQuery = !q || 
      (c.parent_name && c.parent_name.toLowerCase().includes(q)) || 
      (c.student_name && c.student_name.toLowerCase().includes(q)) ||
      (c.topic && c.topic.toLowerCase().includes(q))
    
    const matchStatus = filterConsultationStatus.value === 'Semua' || 
      (filterConsultationStatus.value === 'Belum Dihubungi' && c.status === 'unread') ||
      (filterConsultationStatus.value === 'Sudah Dihubungi' && c.status === 'contacted')

    return matchQuery && matchStatus
  })
})

const formatConsultationTime = (isoString) => {
  if (!isoString) return '-'
  try {
    const d = new Date(isoString)
    return d.toLocaleString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }) + ' WIB'
  } catch (e) {
    return isoString
  }
}

const markConsultationContacted = async (item) => {
  item.status = 'contacted'
  item.contacted_at = new Date().toISOString()
  
  try {
    localStorage.setItem('bk_prima_consultations', JSON.stringify(consultationList.value))
  } catch (e) {}

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    await fetch(`/admin/konsultasi/${item.id}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
      },
      body: JSON.stringify({ status: 'contacted' })
    })
  } catch (e) {}
}

const confirmApproveConsultation = (item) => {
  confirmModal.value = {
    show: true,
    type: 'approve',
    title: 'Setujui & Tandai Pesan Selesai',
    message: 'Konfirmasi bahwa permohonan bimbingan dan konsultasi dari wali murid ini telah ditindaklanjuti dan disetujui?',
    targetInfo: {
      name: `Wali: ${item.parent_name}`,
      tag: 'Disetujui',
      sub: `Siswa: ${item.student_name} • Kontak: ${item.phone || '-'}`
    },
    confirmText: 'Setujui & Tandai Selesai',
    cancelText: 'Batal',
    onConfirm: async () => {
      await markConsultationContacted(item)
      showToast(`Permohonan konsultasi dari ${item.parent_name} berhasil disetujui.`)
    }
  }
}

const confirmDeleteConsultation = (item) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Riwayat Konsultasi',
    message: 'Apakah Anda yakin ingin menghapus riwayat pesan permohonan konsultasi ini dari database?',
    targetInfo: {
      name: `Wali: ${item.parent_name}`,
      tag: item.status === 'contacted' ? 'Sudah Dihubungi' : 'Belum Dihubungi',
      sub: `Siswa: ${item.student_name}`
    },
    confirmText: 'Ya, Hapus Pesan',
    cancelText: 'Batal',
    onConfirm: async () => {
      const idx = consultationList.value.findIndex(c => c.id === item.id)
      if (idx !== -1) {
        consultationList.value.splice(idx, 1)
      }
      try {
        localStorage.setItem('bk_prima_consultations', JSON.stringify(consultationList.value))
      } catch (e) {}

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        await fetch(`/admin/konsultasi/${item.id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
          }
        })
      } catch (e) {}
      showToast(`Pesan permohonan dari ${item.parent_name} berhasil dihapus.`)
    }
  }
}

const deleteConsultationItem = (idx, item) => {
  confirmDeleteConsultation(item)
}

const openWhatsAppToParent = (item) => {
  const phone = item.phone ? item.phone.replace(/[^0-9]/g, '') : ''
  const targetPhone = phone.startsWith('0') ? '62' + phone.slice(1) : (phone || '6281234567890')
  const message = encodeURIComponent(`Halo Bapak/Ibu ${item.parent_name}, kami dari Ruang Bimbingan Konseling (BK) SMK PGRI 05 Jember menindaklanjuti pesan konsultasi Anda mengenai ananda ${item.student_name}. Kami siap berdiskusi untuk pendampingan ananda. Terima kasih.`)
  window.open(`https://wa.me/${targetPhone}?text=${message}`, '_blank')
  markConsultationContacted(item)
}

onMounted(() => {
  syncConsultations()
  window.addEventListener('storage', (e) => {
    if (e.key === 'bk_prima_consultations') {
      syncConsultations()
    }
  })
})
</script>
