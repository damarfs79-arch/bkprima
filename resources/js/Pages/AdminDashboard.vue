<template>
  <AdminLayout :active-tab="activeTab" @select-tab="activeTab = $event">
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- HEADER CARD                                                  -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-lg p-5 sm:p-6 border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Panel Administrasi BK Prima</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola data Guru BK, Rombel Kelas, Siswa, dan pantau tren fluktuasi kehadiran 1 bulan.</p>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- TAB 1: DASHBOARD & GRAFIK NAIK TURUN KEHADIRAN 1 BULAN       -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'dashboard'" class="space-y-6">
        
        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
          <!-- Total Guru BK -->
          <div class="interactive-card p-4 sm:p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 group cursor-default">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-xs font-medium text-slate-500">Guru BK Aktif</span>
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center transition-transform duration-200 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ guruBkList.length }}</div>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Semua bersertifikasi resmi</p>
          </div>

          <!-- Total Kelas -->
          <div class="interactive-card p-4 sm:p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 group cursor-default">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-xs font-medium text-slate-500">Total Rombel</span>
              <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center transition-transform duration-200 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ kelasList.length }}</div>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">6 Jurusan terdaftar aktif</p>
          </div>

          <!-- Total Siswa -->
          <div class="interactive-card p-4 sm:p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 group cursor-default">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-xs font-medium text-slate-500">Total Siswa</span>
              <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center transition-transform duration-200 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ totalSiswaCount }}</div>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Sinkron Dapodik Semester Genap</p>
          </div>

          <!-- Kehadiran Rata-rata 1 Bulan (Card Bulat Mini Dinamis) -->
          <div class="interactive-card p-4 sm:p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 relative overflow-hidden group cursor-default">
            <div class="flex items-center justify-between text-slate-400 mb-2">
              <span class="text-xs font-medium text-slate-500">Presensi {{ currentMonthData.shortName }}</span>
              <div
                class="w-8 h-8 rounded-lg flex items-center justify-center transition-transform duration-200 group-hover:scale-110"
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
                <div class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ currentCohortData.hadir.rate }}%</div>
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
                <span class="absolute text-[10px] font-bold text-slate-800">{{ Math.round(currentCohortData.hadir.rate) }}%</span>
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
                <div class="text-xs font-medium text-slate-500 mb-1 flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                  </svg>
                  <span>Statistika Presensi Siswa &bull; Akumulasi 1 Bulan Penuh</span>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
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

            <!-- Banner Ringkasan Presensi Aktual Database -->
            <div
              class="p-4 rounded-lg border flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition"
              :class="currentCohortData.hadir.rate >= 90 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-800'"
            >
              <div class="flex items-center gap-3">
                <div
                  class="w-9 h-9 rounded-lg text-white flex items-center justify-center shrink-0"
                  :class="currentCohortData.hadir.rate >= 90 ? 'bg-emerald-600' : 'bg-slate-600'"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold">
                      Ringkasan Presensi Aktual Database:
                    </span>
                    <span
                      class="text-xs font-bold px-2 py-0.5 rounded"
                      :class="currentCohortData.hadir.rate >= 90 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'"
                    >
                      {{ currentCohortData.hadir.rate }}% Hadir
                    </span>
                  </div>
                  <p class="text-xs mt-0.5" :class="currentCohortData.hadir.rate >= 90 ? 'text-emerald-800' : 'text-slate-600'">
                    <template v-if="(props.attendanceStats?.totalPresensi || 0) > 0">
                      Tercatat <strong>{{ currentCohortData.hadir.count }}</strong> data hadir dari total <strong>{{ props.attendanceStats.totalPresensi }}</strong> catatan presensi di database.
                    </template>
                    <template v-else>
                      Belum ada data presensi yang tercatat di database.
                    </template>
                  </p>
                </div>
              </div>
              <div
                class="shrink-0 text-xs font-semibold self-start sm:self-auto"
                :class="currentCohortData.hadir.rate >= 90 ? 'text-emerald-700' : 'text-slate-500'"
              >
                {{ (props.attendanceStats?.totalPresensi || 0) === 0 ? 'Data Kosong' : (currentCohortData.hadir.rate >= 90 ? 'Target Kehadiran Terpenuhi' : 'Perlu Evaluasi Presensi') }}
              </div>
            </div>

            <!-- TAMPILAN 1: DIAGRAM LINGKARAN BULAT PENUH (SOLID PIE CHART SEPERTI FOTO 1) -->
            <div v-if="chartViewMode === 'bulat'" class="space-y-6">
              
              <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- KOLOM KIRI: DIAGRAM LINGKARAN PENUH SEPERTI FOTO 1 -->
                <div class="lg:col-span-5 flex flex-col items-center justify-center p-5 sm:p-6 bg-slate-50 rounded-lg border border-slate-200 relative">
                  
                  <div class="w-full flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-bold text-slate-700">Diagram Lingkaran Penuh</span>
                    <span class="text-xs text-slate-500 font-mono">1 Bulan &bull; 100%</span>
                  </div>

                  <!-- SVG Solid Pie Chart -->
                  <div class="relative w-72 h-72 sm:w-80 sm:h-80 flex items-center justify-center my-2 select-none">
                    <svg viewBox="0 0 380 380" class="w-full h-full overflow-visible">
                      
                      <!-- Empty State when no attendance records -->
                      <g v-if="pieSlices.length === 0">
                        <circle cx="190" cy="190" r="160" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2" />
                        <text x="190" y="175" text-anchor="middle" class="text-sm font-semibold fill-slate-500">
                          Belum Ada Data Presensi
                        </text>
                        <text x="190" y="205" text-anchor="middle" class="text-2xl font-bold fill-slate-700">
                          0%
                        </text>
                        <text x="190" y="228" text-anchor="middle" class="text-xs fill-slate-400">
                          0 Siswa Tercatat
                        </text>
                      </g>

                      <!-- Slices (Irisan Pie) -->
                      <g v-else>
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
                              class="text-sm sm:text-base font-bold fill-white"
                              style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);"
                            >
                              {{ slice.label }}
                            </text>
                            <text
                              :x="slice.labelX"
                              :y="slice.labelY + 20"
                              text-anchor="middle"
                              class="text-3xl sm:text-4xl font-bold fill-white"
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
                      <span class="w-2.5 h-2.5 rounded-xs shrink-0" :style="{ backgroundColor: hoveredPieSlice.color }"></span>
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
                        <span class="w-2.5 h-2.5 rounded-xs shrink-0" :style="{ backgroundColor: slice.color }"></span>
                        <span class="font-bold text-slate-800 text-[11px]">{{ slice.label }}</span>
                      </div>
                      <div class="font-bold text-slate-900 text-xs mt-0.5">{{ slice.rate }}%</div>
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
                          <span class="w-3 h-3 rounded-xs shrink-0" :style="{ backgroundColor: slice.color }"></span>
                          <span class="font-bold text-slate-800 text-xs sm:text-sm">{{ slice.label }}</span>
                        </div>
                        <div class="text-right">
                          <span class="text-base sm:text-lg font-bold text-slate-900">{{ slice.rate }}%</span>
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
                        <span class="text-xs font-semibold text-slate-800">
                          Analisis Komparasi 1 Bulan: Hadir vs Sakit / Izin / Alpha
                        </span>
                      </div>
                      <span class="text-xs font-medium text-slate-500">
                        Rasio {{ Math.round(currentCohortData.hadir.rate / Math.max(0.1, 100 - currentCohortData.hadir.rate)) }} : 1
                      </span>
                    </div>

                    <!-- 2-Box Metric Comparison -->
                    <div class="grid grid-cols-2 gap-3">
                      <div class="p-3.5 bg-white rounded-lg border border-slate-200">
                        <span class="text-[11px] font-medium text-slate-500 block">Total Siswa Hadir</span>
                        <div class="text-2xl sm:text-3xl font-bold text-blue-700 tracking-tight mt-0.5">
                          {{ currentCohortData.hadir.rate }}%
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                          {{ currentCohortData.hadir.count }} dari total {{ currentCohortData.totalSiswa }} siswa
                        </p>
                      </div>

                      <div class="p-3.5 bg-white rounded-lg border border-slate-200">
                        <span class="text-[11px] font-medium text-slate-500 block">Total Ketidakhadiran (S+I+A)</span>
                        <div class="text-2xl sm:text-3xl font-bold text-rose-600 tracking-tight mt-0.5">
                          {{ (100 - currentCohortData.hadir.rate).toFixed(1) }}%
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                          {{ totalAbsenCount }} siswa (Sakit, Izin, dan Alpha)
                        </p>
                      </div>
                    </div>

                    <!-- Explanatory Text answering user's question -->
                    <p v-if="currentCohortData.totalPresensi > 0" class="text-xs text-slate-600 leading-relaxed">
                      Berdasarkan diagram bulat akumulasi data presensi aktual, persentase siswa hadir tercatat sebesar <strong>{{ currentCohortData.hadir.rate }}%</strong> ({{ currentCohortData.hadir.count }} siswa). Siswa Alpha tanpa keterangan tercatat sebesar <strong>{{ currentCohortData.alpha.rate }}%</strong> ({{ currentCohortData.alpha.count }} siswa).
                    </p>
                    <p v-else class="text-xs text-slate-500 leading-relaxed italic">
                      Belum ada catatan presensi siswa yang tersimpan di sistem. Data statistik akan terakumulasi otomatis setelah presensi harian siswa dimulai.
                    </p>

                    <!-- Target Status Chip -->
                    <div class="p-3 bg-white rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                      <div>
                        <span class="text-slate-700 font-medium">Standar Target Kehadiran Sekolah: <strong>95.0%</strong></span>
                      </div>
                      <span
                        class="text-xs font-semibold"
                        :class="currentCohortData.totalPresensi === 0 ? 'text-slate-500' : (currentCohortData.hadir.rate >= 95 ? 'text-emerald-700' : 'text-amber-700')"
                      >
                        {{ currentCohortData.totalPresensi === 0 ? 'Belum Ada Data' : (currentCohortData.hadir.rate >= 95 ? 'Terpenuhi (+' + (currentCohortData.hadir.rate - 95).toFixed(1) + '%)' : 'Di Bawah Standar (' + (currentCohortData.hadir.rate - 95).toFixed(1) + '%)') }}
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
                  <span class="text-xs font-medium text-slate-500 self-start sm:self-auto">
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
                      <span :class="selectedMonth === m.id ? 'text-blue-900 font-bold' : 'text-slate-700'">{{ m.shortName }}</span>
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

                    <div class="text-lg font-bold text-slate-900 tracking-tight">{{ m.hadirRate }}%</div>
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
                      <span class="block text-xs font-semibold text-emerald-800">Kenaikan Tertinggi</span>
                      <span class="text-xs text-emerald-700 font-medium">Tgl 14 Oktober &bull; Kegiatan Apel Akbar</span>
                    </div>
                  </div>
                  <span class="text-base font-bold text-emerald-700">98.6%</span>
                </div>

                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-lg flex items-center justify-between">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center shrink-0">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                      </svg>
                    </div>
                    <div>
                      <span class="block text-xs font-semibold text-rose-800">Penurunan Terendah</span>
                      <span class="text-xs text-rose-700 font-medium">Tgl 21 Oktober &bull; Banjir Akses Jalan</span>
                    </div>
                  </div>
                  <span class="text-base font-bold text-rose-700">88.2%</span>
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
              <span class="text-xs font-medium text-slate-500 self-start sm:self-auto">
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
                  <span class="absolute text-xs font-bold text-slate-900">{{ j.rate }}%</span>
                </div>

                <div class="font-bold text-slate-900 text-sm mt-1">{{ j.name }}</div>
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
                  <span class="absolute text-[11px] font-bold text-slate-900">{{ w.rate }}%</span>
                </div>

                <div class="min-w-0">
                  <span class="text-xs font-medium text-slate-500 block">{{ w.week }}</span>
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
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Data Guru Bimbingan Konseling (BK)</h2>
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
              <thead class="bg-slate-50/50 border-b border-slate-200">
                <tr>
                  <th class="py-3 px-4 text-center w-12 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama Lengkap &amp; Email</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Jabatan</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No. WhatsApp</th>
                  <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status Akun</th>
                  <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(g, idx) in guruBkList" :key="g.id" class="hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ g.name }}</div>
                    <div class="text-xs text-slate-500 font-normal mt-0.5">{{ g.email }}</div>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">{{ g.jabatan }}</td>
                  <td class="py-3.5 px-4 font-mono text-slate-600">{{ g.wa || '-' }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="text-xs font-medium text-emerald-700">
                      Akun Aktif
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <div class="inline-flex items-center gap-1.5">
                      <button @click="editGuru(g)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 active:scale-90 rounded-md transition-all duration-150 cursor-pointer" title="Edit Guru BK">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button @click="confirmDeleteGuru(g)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 active:scale-90 rounded-md transition-all duration-150 cursor-pointer" title="Hapus Guru">
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
      <!-- TAB 2B: KELOLA DATA JURUSAN                                  -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'jurusan'" class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
          <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-sm font-bold text-slate-900">Master Program Keahlian / Jurusan</h2>
              <p class="text-xs text-slate-500 mt-0.5">
                Tambah, ubah, atau hapus daftar jurusan yang digunakan pada rombel kelas dan filter presensi.
              </p>
            </div>
            <button
              type="button"
              @click="cancelEditJurusan(); openModal('jurusan')"
              class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition cursor-pointer shrink-0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Jurusan</span>
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                  <th class="py-3 px-4 w-12">No</th>
                  <th class="py-3 px-4 w-28">Kode</th>
                  <th class="py-3 px-4">Nama Lengkap Jurusan</th>
                  <th class="py-3 px-4">Keterangan</th>
                  <th class="py-3 px-4 text-right w-36">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="jurusanList.length === 0">
                  <td colspan="5" class="py-8 text-center text-slate-400">
                    Belum ada jurusan terdaftar. Klik "Tambah Jurusan" untuk menambahkan.
                  </td>
                </tr>
                <tr v-for="(j, idx) in jurusanList" :key="j.id" class="hover:bg-slate-50/70 transition">
                  <td class="py-3 px-4 text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3 px-4 font-bold text-slate-900">{{ j.code }}</td>
                  <td class="py-3 px-4 font-semibold text-slate-800">{{ j.name }}</td>
                  <td class="py-3 px-4 text-slate-500">{{ j.description || '-' }}</td>
                  <td class="py-3 px-4 text-right">
                    <div class="inline-flex items-center gap-1.5">
                      <button
                        type="button"
                        @click="editJurusanItem(j); openModal('jurusan')"
                        class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition cursor-pointer"
                      >
                        Edit
                      </button>
                      <button
                        type="button"
                        @click="confirmDeleteJurusan(j)"
                        class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold transition cursor-pointer"
                      >
                        Hapus
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
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Data Rombongan Belajar (Rombel)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Struktur rombel kelas per tingkat dan penugasan wali kelas</p>
          </div>
          <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">
            <button
              @click="openModal('jurusan')"
              class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300 font-semibold text-xs transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Jurusan</span>
            </button>
            <button
              @click="openModal('kelas')"
              class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Kelas Baru</span>
            </button>
          </div>
        </div>

        <!-- Table Kelas -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden p-5 sm:p-6 space-y-4">
          <!-- Filter Bar Kelas: Search + Dropdown Kelas 10, 11, 12 -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-sm">
              <input
                v-model="searchKelas"
                type="text"
                placeholder="Cari rombel, wali kelas, guru BK..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              />
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
              <div class="flex items-center gap-1.5">
                <label class="text-xs font-medium text-slate-500">Tingkat:</label>
                <select
                  v-model="selectedFilterLevelKelas"
                  class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                >
                  <option value="Semua">Semua Tingkat</option>
                  <option value="10">Kelas 10 (X)</option>
                  <option value="11">Kelas 11 (XI)</option>
                  <option value="12">Kelas 12 (XII)</option>
                </select>
              </div>

              <div v-if="jurusanList.length > 0" class="flex items-center gap-1.5">
                <label class="text-xs font-medium text-slate-500">Jurusan:</label>
                <select
                  v-model="selectedFilterJurusanKelas"
                  class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                >
                  <option value="Semua">Semua Jurusan</option>
                  <option v-for="jur in jurusanList" :key="jur.id" :value="jur.code">{{ jur.code }}</option>
                </select>
              </div>

              <div class="text-xs text-slate-500 pl-1 font-medium">
                <span>({{ filteredKelasList.length }} rombel)</span>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50/50 border-b border-slate-200">
                <tr>
                  <th class="py-3 px-4 text-center w-12 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama Rombel</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kelas</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Wali Kelas</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Guru BK Pendamping</th>
                  <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Jumlah Siswa</th>
                  <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-if="filteredKelasList.length === 0">
                  <td colspan="7" class="py-8 text-center text-slate-400">
                    Tidak ada rombel kelas yang sesuai dengan filter tingkat atau pencarian.
                  </td>
                </tr>
                <tr v-for="(k, idx) in filteredKelasList" :key="k.id" class="hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ k.name }}</span>
                  </td>
                  <td class="py-3.5 px-4">
                    <span class="text-xs text-slate-600 font-medium">
                      {{ formatKelasLabel(k.level, k.jurusan) }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">{{ k.waliKelas }}</td>
                  <td class="py-3.5 px-4 text-slate-600 font-medium">{{ k.guruBK }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="text-xs text-slate-700 font-medium">
                      {{ k.studentCount }} Siswa
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <div class="inline-flex items-center justify-center gap-1.5">
                      <button @click="editKelas(k)" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 active:scale-90 rounded-md transition-all duration-150 cursor-pointer" title="Edit Rombel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button @click="confirmDeleteKelas(k)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 active:scale-90 rounded-md transition-all duration-150 cursor-pointer" title="Hapus Kelas">
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
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Data Induk Siswa</h2>
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
              <thead class="bg-slate-50/50 border-b border-slate-200">
                <tr>
                  <th class="py-3 px-4 text-center w-12 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">NISN</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama Lengkap Siswa</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kelas</th>
                  <th class="py-3 px-3 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">JK</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No. WhatsApp Wali Murid</th>
                  <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Poin Tatib</th>
                  <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(s, idx) in filteredSiswaList" :key="s.id" class="hover:bg-slate-50 transition">
                  <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4 font-mono font-normal text-xs text-slate-500">{{ s.nisn }}</td>
                  <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ s.name }}</span>
                  </td>
                  <td class="py-3.5 px-4">
                    <span class="text-xs font-medium text-slate-700">
                      {{ s.className }}
                    </span>
                  </td>
                  <td class="py-3.5 px-3 text-center">
                    <span class="text-xs font-medium text-slate-600">
                      {{ s.gender }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 font-mono text-slate-600 text-xs">{{ s.parentWa }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="text-xs font-medium" :class="s.poin > 20 ? 'text-rose-600 font-semibold' : 'text-slate-600'">
                      {{ s.poin }} Poin
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <div class="inline-flex items-center justify-center gap-1.5">
                      <button
                        @click="editSiswa(s)"
                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 active:scale-90 rounded-md transition-all duration-150 cursor-pointer"
                        title="Edit Data Siswa"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button
                        @click="confirmDeleteSiswa(s)"
                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 active:scale-90 rounded-md transition-all duration-150 cursor-pointer"
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
                  <td colspan="8" class="py-10 text-center">
                    <div class="flex flex-col items-center justify-center gap-2">
                      <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                      </div>
                      <p class="text-xs font-semibold text-slate-700">
                        {{ selectedFilterKelas !== 'Semua' ? `Belum ada data siswa untuk rombel ${selectedFilterKelas}` : 'Tidak ada data siswa yang cocok dengan pencarian' }}
                      </p>
                      <div class="flex items-center gap-2 mt-1">
                        <button
                          v-if="selectedFilterKelas !== 'Semua'"
                          @click="selectedFilterKelas = 'Semua'"
                          class="text-xs font-medium text-slate-500 hover:text-slate-800 underline cursor-pointer"
                        >
                          Lihat Semua Kelas
                        </button>
                        <span v-if="selectedFilterKelas !== 'Semua'" class="text-slate-300">•</span>
                        <button
                          @click="openModal('import-siswa')"
                          class="text-xs font-bold text-blue-600 hover:text-blue-700 underline cursor-pointer"
                        >
                          Import Siswa untuk {{ selectedFilterKelas !== 'Semua' ? selectedFilterKelas : 'Rombel Ini' }}
                        </button>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>



      <!-- ============================================================ -->
      <!-- TAB 4B: KELOLA TINDAKAN / SANKSI PEMBINAAN                   -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'sanksi'" class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
          <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-sm font-bold text-slate-900">Master Tindakan &amp; Sanksi Pembinaan</h2>
              <p class="text-xs text-slate-500 mt-0.5">
                Daftar pilihan tindakan/sanksi yang tampil di aplikasi Android Guru BK saat mencatat pelanggaran siswa.
              </p>
            </div>
            <button
              type="button"
              @click="openSanksiModal()"
              class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition cursor-pointer shrink-0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Tindakan</span>
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                  <th class="py-3 px-4 w-16">Urutan</th>
                  <th class="py-3 px-4">Nama Tindakan / Sanksi</th>
                  <th class="py-3 px-4">Keterangan</th>
                  <th class="py-3 px-4 w-24">Status</th>
                  <th class="py-3 px-4 text-right w-32">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="sanksiList.length === 0">
                  <td colspan="5" class="py-8 text-center text-slate-400">
                    Belum ada data tindakan / sanksi. Klik "Tambah Tindakan" untuk menambahkan.
                  </td>
                </tr>
                <tr v-for="s in sanksiList" :key="s.id" class="hover:bg-slate-50/70 transition">
                  <td class="py-3 px-4 font-mono text-slate-500">{{ s.sort_order }}</td>
                  <td class="py-3 px-4 font-semibold text-slate-800">{{ s.name }}</td>
                  <td class="py-3 px-4 text-slate-500">{{ s.description || '-' }}</td>
                  <td class="py-3 px-4">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold"
                      :class="s.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                    >
                      {{ s.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right">
                    <div class="inline-flex items-center gap-1.5">
                      <button
                        type="button"
                        @click="openSanksiModal(s)"
                        class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition cursor-pointer"
                      >
                        Edit
                      </button>
                      <button
                        type="button"
                        @click="confirmDeleteSanksi(s)"
                        class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold transition cursor-pointer"
                      >
                        Hapus
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
      <!-- TAB 4C: KELOLA ARSIP ALUMNI (SISWA LULUS)                    -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'arsip'" class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
          <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-sm font-bold text-slate-900">Kelola Arsip Siswa Lulus (Alumni)</h2>
              <p class="text-xs text-slate-500 mt-0.5">
                Luluskan siswa aktif, tambah data alumni, edit tahun lulus, atau pulihkan kembali ke siswa aktif.
              </p>
            </div>
            <button
              type="button"
              @click="openAlumniModal()"
              class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition cursor-pointer shrink-0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah / Luluskan Siswa</span>
            </button>
          </div>

          <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-3">
            <input
              v-model="searchAlumni"
              type="text"
              placeholder="Cari NISN atau nama alumni..."
              class="flex-1 bg-white border border-slate-200 rounded-lg px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                  <th class="py-3 px-4 w-12">No</th>
                  <th class="py-3 px-4">NISN</th>
                  <th class="py-3 px-4">Nama Alumni</th>
                  <th class="py-3 px-4">Kelas Terakhir</th>
                  <th class="py-3 px-4">Tahun Lulus</th>
                  <th class="py-3 px-4">Poin Akhir</th>
                  <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="filteredAlumni.length === 0">
                  <td colspan="7" class="py-8 text-center text-slate-400">
                    Belum ada data arsip siswa lulus. Klik "Tambah / Luluskan Siswa" untuk menambahkan.
                  </td>
                </tr>
                <tr v-for="(a, idx) in filteredAlumni" :key="a.id" class="hover:bg-slate-50/70 transition">
                  <td class="py-3 px-4 text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3 px-4 font-mono text-slate-600">{{ a.nisn || a.nis }}</td>
                  <td class="py-3 px-4 font-semibold text-slate-800">{{ a.name }}</td>
                  <td class="py-3 px-4 text-slate-600">{{ a.lastClassName || '-' }}</td>
                  <td class="py-3 px-4 font-semibold text-blue-700">{{ a.graduationYear || '-' }}</td>
                  <td class="py-3 px-4">{{ a.poin || 0 }} Poin</td>
                  <td class="py-3 px-4 text-right">
                    <div class="inline-flex items-center gap-1.5">
                      <button
                        type="button"
                        @click="openAlumniModal(a)"
                        class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition cursor-pointer"
                      >
                        Edit
                      </button>
                      <button
                        type="button"
                        @click="restoreAlumni(a)"
                        class="px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition cursor-pointer"
                      >
                        Pulihkan
                      </button>
                      <button
                        type="button"
                        @click="confirmDeleteAlumni(a)"
                        class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold transition cursor-pointer"
                      >
                        Hapus
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
      <!-- TAB 5: PENGATURAN SEKOLAH, KEPALA SEKOLAH & SEMESTER          -->
      <!-- ============================================================ -->
      <div v-if="activeTab === 'pengaturan'" class="space-y-6 animate-in fade-in duration-150">
        
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-2xs">
          <div class="border-b border-slate-100 pb-4 mb-6">
            <h2 class="text-base font-bold text-slate-900">Pengaturan Lembaga &amp; Dokumen Cetak</h2>
            <p class="text-xs text-slate-500 mt-1">
              Atur identitas kepala sekolah, periode semester aktif, dan informasi kop resmi untuk pencetakan dokumen rekap presensi.
            </p>
          </div>

          <form @submit.prevent="saveSettings" class="space-y-6 text-xs max-w-2xl">
            <!-- Section 1: Kepala Sekolah -->
            <div class="space-y-3">
              <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider text-blue-700">1. Pejabat Penandatangan (Kepala Sekolah)</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Nama Kepala Sekolah *</label>
                  <input
                    v-model="formSettings.kepala_sekolah"
                    required
                    type="text"
                    placeholder="Contoh: Drs. H. Saiful Bahri, M.Pd"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                  />
                  <p class="text-[11px] text-slate-400 mt-1">Nama ini dicantumkan pada kolom 'Menyetujui' di lembar cetak rekap presensi dan dokumen resmi.</p>
                </div>
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">NIP Kepala Sekolah</label>
                  <input
                    v-model="formSettings.nip_kepala_sekolah"
                    type="text"
                    placeholder="Contoh: 19680315 199403 1 008"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                  />
                </div>
              </div>
            </div>

            <!-- Section 2: Periode Akademik & Semester -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
              <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider text-blue-700">2. Periode Akademik &amp; Tampilan Web</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Label Semester Aktif (Sidebar Web) *</label>
                  <input
                    v-model="formSettings.semester"
                    required
                    type="text"
                    placeholder="Contoh: Semester Genap 24/25"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                  />
                  <p class="text-[11px] text-slate-400 mt-1">Ditampilkan pada bagian bawah menu sidebar web seluruh pengguna.</p>
                </div>
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Tahun Ajaran *</label>
                  <input
                    v-model="formSettings.tahun_ajaran"
                    required
                    type="text"
                    placeholder="Contoh: 2024/2025"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                  />
                  <p class="text-[11px] text-slate-400 mt-1">Dicantumkan pada kop judul rekapitulasi presensi bulanan.</p>
                </div>
              </div>
            </div>

            <!-- Section 3: Identitas Lembaga -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
              <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider text-blue-700">3. Identitas Lembaga (Kop Surat)</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Nama Sekolah</label>
                  <input
                    v-model="formSettings.school_name"
                    type="text"
                    placeholder="Contoh: SMK PGRI 05 JEMBER"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                  />
                </div>
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Alamat Lembaga</label>
                  <input
                    v-model="formSettings.school_address"
                    type="text"
                    placeholder="Alamat sekolah..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                  />
                </div>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
              <button
                type="submit"
                :disabled="isSavingSettings"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-semibold rounded-lg cursor-pointer transition shadow-2xs inline-flex items-center gap-1.5"
              >
                <svg v-if="isSavingSettings" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isSavingSettings ? 'Menyimpan...' : 'Simpan Pengaturan' }}</span>
              </button>
            </div>
          </form>
        </div>

      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH GURU BK                                         -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'guru'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 shadow-lg border border-slate-200 space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-base">Tambah Guru BK</h3>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveGuru" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
              <input
                v-model="formGuru.name"
                required
                placeholder="Masukkan nama lengkap beserta gelar"
                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Email *</label>
                <input
                  v-model="formGuru.email"
                  type="email"
                  required
                  placeholder="nama@bkprima.sch.id"
                  class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <div class="relative">
                  <input
                    :type="showGuruPassword ? 'text' : 'password'"
                    v-model="formGuru.password"
                    placeholder="Default: 123456"
                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 pr-9 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 font-mono text-xs"
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
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Jabatan</label>
                <input
                  v-model="formGuru.jabatan"
                  placeholder="Guru Pembimbing BK"
                  class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">No. WhatsApp</label>
                <input
                  v-model="formGuru.wa"
                  placeholder="081234567890"
                  class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                />
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2 rounded-lg border border-slate-200 font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                Batal
              </button>
              <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition cursor-pointer shadow-xs">
                Simpan Guru BK
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH KELAS / ROMBEL                                  -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'kelas'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-base">Tambah Rombel Kelas Baru</h3>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveKelas" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Tingkat Kelas *</label>
                <select v-model="formKelas.level" @change="autoGenerateClassName" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option value="Kelas 10 (X)">Kelas 10 (X)</option>
                  <option value="Kelas 11 (XI)">Kelas 11 (XI)</option>
                  <option value="Kelas 12 (XII)">Kelas 12 (XII)</option>
                </select>
              </div>
              <div>
                <div class="flex items-center justify-between mb-1">
                  <label class="block font-bold text-slate-700">Jurusan *</label>
                  <button type="button" @click="openModal('jurusan')" class="text-[11px] text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
                    + Kelola Jurusan
                  </button>
                </div>
                <select
                  v-model="formKelas.jurusan_id"
                  @change="onJurusanSelectChange"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                >
                  <option value="" disabled>-- Pilih Jurusan --</option>
                  <option v-for="j in jurusanList" :key="j.id" :value="j.id">
                    {{ j.code }} - {{ j.name }}
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Rombel *</label>
                <input
                  v-model="formKelas.name"
                  required
                  placeholder="X RPL 1"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Wali Kelas *</label>
                <input
                  v-model="formKelas.waliKelas"
                  required
                  placeholder="Nama wali kelas"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white"
                />
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block font-bold text-slate-700">Guru BK Pendamping</label>
                <button type="button" @click="openModal('guru')" class="text-[11px] text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
                  + Tambah Guru BK Baru
                </button>
              </div>
              <select v-model="formKelas.guruBkId" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option value="">-- Belum Ditentukan --</option>
                <option v-for="g in guruBkList" :key="g.id" :value="g.id">{{ g.name }}</option>
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
      <!-- MODAL: TAMBAH / KELOLA JURUSAN                                -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'jurusan'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                  {{ editingJurusan ? 'Edit Jurusan / Bidang Keahlian' : 'Tambah Jurusan / Bidang Keahlian' }}
                </h3>
                <p class="text-[11px] text-slate-500 font-normal">
                  {{ editingJurusan ? 'Perbarui informasi program keahlian' : 'Daftarkan program keahlian baru untuk pengelompokan rombel kelas' }}
                </p>
              </div>
            </div>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Alert Error Validasi -->
          <div v-if="errorMessageJurusan" class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center justify-between">
            <span class="font-medium">{{ errorMessageJurusan }}</span>
            <button type="button" @click="errorMessageJurusan = ''" class="text-rose-500 hover:text-rose-700 ml-2 p-1 rounded-md transition cursor-pointer" title="Tutup">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveJurusan" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Kode Jurusan *</label>
                <input
                  v-model="formJurusan.code"
                  required
                  placeholder="Contoh: RPL"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white"
                />
              </div>
              <div class="col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Jurusan *</label>
                <input
                  v-model="formJurusan.name"
                  required
                  placeholder="Contoh: Rekayasa Perangkat Lunak"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white"
                />
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Deskripsi Singkat (Opsional)</label>
              <textarea
                v-model="formJurusan.description"
                rows="2"
                placeholder="Fokus bidang keahlian, teknologi yang dipelajari, dsb..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white"
              ></textarea>
            </div>

            <!-- List Jurusan yang Tersedia -->
            <div class="pt-2 border-t border-slate-100">
              <span class="text-[11px] font-bold uppercase text-slate-500 block mb-1.5">Jurusan Terdaftar Saat Ini ({{ jurusanList.length }}):</span>
              <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto p-1 bg-slate-50/50 rounded-lg border border-slate-100">
                <div
                  v-for="j in jurusanList"
                  :key="j.code"
                  class="inline-flex items-center gap-1.5 pl-2.5 pr-1 py-1 rounded-md text-[11px] font-medium border transition"
                  :class="editingJurusan && editingJurusan.id === j.id ? 'bg-blue-50 text-blue-900 border-blue-300 ring-1 ring-blue-300' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300'"
                >
                  <strong class="font-bold text-slate-900">{{ j.code }}</strong>
                  <span class="text-slate-500 text-[10px] truncate max-w-[140px]">&bull; {{ j.name }}</span>
                  <div class="inline-flex items-center gap-0.5 ml-1">
                    <button
                      type="button"
                      @click="editJurusanItem(j)"
                      class="p-1 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition cursor-pointer"
                      title="Edit Jurusan"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>
                    <button
                      type="button"
                      @click="confirmDeleteJurusan(j)"
                      class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition cursor-pointer"
                      title="Hapus Jurusan"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 flex-wrap">
              <button
                v-if="editingJurusan"
                type="button"
                @click="cancelEditJurusan"
                class="px-3.5 py-2 rounded-lg border border-slate-200 font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
              >
                Batal Edit
              </button>
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                Batal
              </button>
              <button
                type="submit"
                :disabled="isSubmittingJurusan"
                class="px-4 py-2.5 rounded-lg bg-slate-700 hover:bg-slate-800 text-white font-bold transition cursor-pointer shadow-sm disabled:opacity-50"
              >
                {{ isSubmittingJurusan ? 'Menyimpan...' : (editingJurusan ? 'Perbarui Jurusan' : 'Simpan Jurusan') }}
              </button>
              <button
                v-if="!editingJurusan"
                type="button"
                @click="saveJurusan(true)"
                :disabled="isSubmittingJurusan"
                class="px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm disabled:opacity-50 inline-flex items-center gap-1.5"
              >
                <span>Simpan &amp; Lanjut Buat Rombel</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH / EDIT TINDAKAN / SANKSI                       -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'sanksi'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 animate-in zoom-in-95 duration-150">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">
              {{ editingSanksi ? 'Edit Tindakan / Sanksi' : 'Tambah Tindakan / Sanksi Baru' }}
            </h3>
            <button type="button" @click="closeModal" class="text-slate-400 hover:text-slate-600 cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveSanksi" class="mt-4 space-y-3 text-xs">
            <div v-if="errorMessageSanksi" class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 font-medium">
              {{ errorMessageSanksi }}
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Tindakan / Sanksi *</label>
              <input
                v-model="formSanksi.name"
                type="text"
                required
                placeholder="Contoh: Pembinaan Lisan & Pengarahan BK"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
              <input
                v-model="formSanksi.description"
                type="text"
                placeholder="Penjelasan singkat pelaksanaan sanksi"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Urutan Tampil</label>
                <input
                  v-model.number="formSanksi.sort_order"
                  type="number"
                  min="0"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div class="flex items-end pb-2">
                <label class="inline-flex items-center gap-2 cursor-pointer font-semibold text-slate-700">
                  <input v-model="formSanksi.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                  <span>Aktif di Aplikasi</span>
                </label>
              </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
              <button type="button" @click="closeModal" class="px-4 py-2 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 cursor-pointer">
                Batal
              </button>
              <button
                type="submit"
                :disabled="isSavingSanksi"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold cursor-pointer disabled:opacity-50"
              >
                {{ isSavingSanksi ? 'Menyimpan...' : (editingSanksi ? 'Perbarui' : 'Simpan') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TAMBAH / EDIT ARSIP ALUMNI                            -->
      <!-- ============================================================ -->
      <div v-if="modalType === 'arsip'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 animate-in zoom-in-95 duration-150">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">
              {{ editingAlumni ? 'Edit Data Arsip Alumni' : 'Tambah / Luluskan Siswa ke Arsip' }}
            </h3>
            <button type="button" @click="closeModal" class="text-slate-400 hover:text-slate-600 cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveAlumni" class="mt-4 space-y-3 text-xs">
            <div v-if="errorMessageAlumni" class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 font-medium">
              {{ errorMessageAlumni }}
            </div>

            <div v-if="!editingAlumni" class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-lg">
              <button
                type="button"
                @click="alumniInputMode = 'angkatan'"
                class="py-1.5 rounded-md font-semibold transition cursor-pointer"
                :class="alumniInputMode === 'angkatan' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-600'"
              >
                Luluskan 1 Angkatan
              </button>
              <button
                type="button"
                @click="alumniInputMode = 'manual'"
                class="py-1.5 rounded-md font-semibold transition cursor-pointer"
                :class="alumniInputMode === 'manual' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-600'"
              >
                Input Alumni Baru
              </button>
            </div>

            <div v-if="!editingAlumni && alumniInputMode === 'angkatan'">
              <label class="block font-bold text-slate-700 mb-1">Pilih Angkatan *</label>
              <select
                v-model="formAlumni.level"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
              >
                <option value="">-- Pilih Angkatan --</option>
                <option value="XII">Angkatan Kelas XII ({{ angkatanCounts.XII }} siswa aktif)</option>
                <option value="XI">Angkatan Kelas XI ({{ angkatanCounts.XI }} siswa aktif)</option>
                <option value="X">Angkatan Kelas X ({{ angkatanCounts.X }} siswa aktif)</option>
              </select>
            </div>

            <template v-else>
              <div>
                <label class="block font-bold text-slate-700 mb-1">NISN Alumni *</label>
                <input
                  v-model="formAlumni.nisn"
                  type="text"
                  required
                  placeholder="Contoh: 0061234561"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Alumni *</label>
                <input
                  v-model="formAlumni.name"
                  type="text"
                  required
                  placeholder="Nama lengkap siswa"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Kelas Terakhir *</label>
                <input
                  v-model="formAlumni.last_class_name"
                  type="text"
                  required
                  placeholder="Contoh: XII RPL 1"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </template>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Tahun Lulus *</label>
              <input
                v-model.number="formAlumni.graduation_year"
                type="number"
                min="2000"
                max="2100"
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
              <button type="button" @click="closeModal" class="px-4 py-2 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 cursor-pointer">
                Batal
              </button>
              <button
                type="submit"
                :disabled="isSavingAlumni"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold cursor-pointer disabled:opacity-50"
              >
                {{ isSavingAlumni ? 'Menyimpan...' : (editingAlumni ? 'Perbarui' : 'Simpan') }}
              </button>
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
        <div class="bg-white rounded-lg max-w-md w-full p-5 sm:p-6 shadow-lg border border-slate-200 space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-base">Edit Guru BK</h3>
            <button @click="closeModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="updateGuru" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
              <input
                v-model="formEditGuru.name"
                required
                placeholder="Masukkan nama lengkap beserta gelar"
                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Email *</label>
                <input
                  v-model="formEditGuru.email"
                  type="email"
                  required
                  placeholder="nama@bkprima.sch.id"
                  class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <div class="relative">
                  <input
                    :type="showEditGuruPassword ? 'text' : 'password'"
                    v-model="formEditGuru.password"
                    placeholder="Kosongkan jika tetap"
                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 pr-9 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 font-mono text-xs"
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
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Jabatan</label>
                <input
                  v-model="formEditGuru.jabatan"
                  placeholder="Guru Pembimbing BK"
                  class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">No. WhatsApp</label>
                <input
                  v-model="formEditGuru.wa"
                  placeholder="081234567890"
                  class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                />
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2 rounded-lg border border-slate-200 font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                Batal
              </button>
              <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition cursor-pointer shadow-xs">
                Simpan Perubahan
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
            <h3 class="font-bold text-slate-900 text-base">Edit Rombel Kelas</h3>
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
                <select v-model="formEditKelas.jurusan_id" @change="onEditJurusanSelectChange" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                  <option v-for="j in jurusanList" :key="j.id" :value="j.id">{{ j.code }} - {{ j.name }}</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Rombel *</label>
                <input v-model="formEditKelas.name" required placeholder="X RPL 1" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white uppercase" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Kapasitas Siswa</label>
                <input v-model.number="formEditKelas.studentCount" type="number" min="1" max="50" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Nama Wali Kelas *</label>
              <input v-model="formEditKelas.waliKelas" required placeholder="Nama wali kelas" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Guru BK Pendamping</label>
              <select v-model="formEditKelas.guruBkId" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option value="">-- Belum Ditentukan --</option>
                <option v-for="g in guruBkList" :key="g.id" :value="g.id">{{ g.name }}</option>
              </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
              <button type="button" @click="closeModal" class="px-4 py-2.5 rounded-lg border border-slate-200 font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">Batal</button>
              <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition cursor-pointer shadow-sm">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL CARD: KONFIRMASI TINDAKAN (HAPUS / SETUJUI)            -->
      <!-- ============================================================ -->
      <div
        v-if="confirmModal.show"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/50 animate-in fade-in duration-150"
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
              <span v-if="confirmModal.targetInfo.tag" class="text-xs text-slate-500 font-medium">
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
                :disabled="isUploadingFile"
              />
              <div v-if="!isUploadingFile" class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs sm:text-sm font-bold text-slate-800">
                    <span class="text-blue-700 underline font-semibold">Klik untuk memilih file</span> atau seret file ke sini
                  </p>
                  <p class="text-[11px] text-slate-500 mt-0.5">Mendukung format .XLSX (Excel), .CSV, .TXT, dan file ekspor Dapodik</p>
                </div>
                <div v-if="importFileName" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span>File Terpilih: {{ importFileName }}</span>
                </div>
              </div>
              <div v-else class="flex flex-col items-center justify-center gap-2 py-4 pointer-events-none">
                <div class="w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xs font-bold text-blue-700">Membaca dan memvalidasi file spreadsheet...</p>
              </div>
            </div>
            <div v-if="uploadError" class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
              {{ uploadError }}
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
                <span v-if="importedStudents.length > 0" class="text-emerald-700 font-bold">
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
              <span class="text-xs font-bold text-slate-800">
                Pratinjau Data Siswa ({{ importedStudents.length }} Siswa)
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
                      <span class="text-xs text-slate-600 font-medium">
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
              :disabled="importedStudents.length === 0 || isSubmittingImport"
              class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold transition cursor-pointer text-xs shadow-sm flex items-center gap-1.5"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>{{ isSubmittingImport ? 'Mengimpor Data...' : `Simpan & Import ${importedStudents.length} Siswa` }}</span>
            </button>
          </div>

        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layouts/AdminLayout.vue'
import { ref, computed, onMounted, watch, h } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  attendanceStats: {
    type: Object,
    default: () => ({
      totalPresensi: 0,
      hadirCount: 0,
      sakitCount: 0,
      izinCount: 0,
      alphaCount: 0,
      hadirRate: 0,
      sakitRate: 0,
      izinRate: 0,
      alphaRate: 0,
    })
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
  },
  initialKelasList: {
    type: Array,
    default: () => []
  },
  initialSiswaList: {
    type: Array,
    default: () => []
  },
  initialJurusanList: {
    type: Array,
    default: () => []
  },
  initialSanksiList: {
    type: Array,
    default: () => []
  },
  initialAlumniList: {
    type: Array,
    default: () => []
  },
  settings: {
    type: Object,
    default: () => ({})
  }
})

const formatKelasLabel = (level, jurusan) => {
  const raw = String(level || '').toUpperCase()
  let num = ''
  if (raw.includes('12') || raw.includes('XII')) num = '12'
  else if (raw.includes('11') || raw.includes('XI')) num = '11'
  else if (raw.includes('10') || raw.includes('X')) num = '10'
  else num = String(level || '').trim()

  const jur = String(jurusan || '').trim()
  return [num, jur].filter(Boolean).join(' ')
}

const getSavedTab = () => {
  if (typeof window !== 'undefined') {
    const params = new URLSearchParams(window.location.search)
    const tabParam = params.get('tab')
    if (tabParam && ['dashboard', 'guru', 'jurusan', 'kelas', 'siswa', 'sanksi', 'arsip', 'pengaturan'].includes(tabParam)) {
      return tabParam
    }
    const saved = localStorage.getItem('bkprima_admin_active_tab')
    if (saved && ['dashboard', 'guru', 'jurusan', 'kelas', 'siswa', 'sanksi', 'arsip', 'pengaturan'].includes(saved)) {
      return saved
    }
  }
  return 'dashboard'
}

const activeTab = ref(getSavedTab())

watch(activeTab, (val) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('bkprima_admin_active_tab', val)
  }
})
const modalType = ref(null)
const searchSiswa = ref('')
const selectedFilterKelas = ref('Semua')
const searchKelas = ref('')
const selectedFilterLevelKelas = ref('Semua')
const selectedFilterJurusanKelas = ref('Semua')

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
    id: 'guru',
    label: 'Kelola Guru BK',
    count: guruBkList.value.length,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })
    ])
  },
  {
    id: 'jurusan',
    label: 'Kelola Jurusan',
    count: jurusanList.value.length,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
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
  },
  {
    id: 'sanksi',
    label: 'Kelola Tindakan / Sanksi',
    count: sanksiList.value.length,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })
    ])
  },
  {
    id: 'arsip',
    label: 'Kelola Arsip Alumni',
    count: alumniList.value.length,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 14l9-5-9-5-9 5 9 5z' }),
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' })
    ])
  },
  {
    id: 'pengaturan',
    label: 'Pengaturan Sekolah',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
    ])
  }
])

// ==========================================
// PENGATURAN SEKOLAH & SEMESTER
// ==========================================
const formSettings = ref({
  kepala_sekolah: props.settings?.kepala_sekolah || 'Drs. H. Saiful Bahri, M.Pd',
  nip_kepala_sekolah: props.settings?.nip_kepala_sekolah || '19680315 199403 1 008',
  semester: props.settings?.semester || 'Semester Genap 24/25',
  tahun_ajaran: props.settings?.tahun_ajaran || '2024/2025',
  school_name: props.settings?.school_name || 'SMK PGRI 05 JEMBER',
  school_address: props.settings?.school_address || 'Jl. Krakatau No. 54 Tanggul, Kab. Jember - Jawa Timur',
})

const isSavingSettings = ref(false)

const saveSettings = () => {
  isSavingSettings.value = true
  router.post('/admin/pengaturan', formSettings.value, {
    preserveScroll: true,
    onSuccess: () => {
      isSavingSettings.value = false
      showToast('Pengaturan sekolah, kepala sekolah, dan semester berhasil disimpan.')
    },
    onError: (errs) => {
      isSavingSettings.value = false
      alert(Object.values(errs)[0] || 'Gagal menyimpan pengaturan.')
    }
  })
}

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
        totalSiswa: 0,
        hadir: { rate: 95.8, count: 1035, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir mengikuti seluruh sesi KBM' },
        sakit: { rate: 2.1, count: 23, label: 'Sakit', color: '#0ea5e9', desc: 'Terkonfirmasi surat keterangan dokter / wali' },
        izin: { rate: 1.4, count: 15, label: 'Izin', color: '#f59e0b', desc: 'Izin tertulis keperluan keluarga / dinas' },
        alpha: { rate: 0.7, count: 7, label: 'Alpha', color: '#f43f5e', desc: 'Tidak ada pemberitahuan, dalam bimbingan BK' }
      },
      x: {
        totalSiswa: 0,
        hadir: { rate: 96.4, count: 347, label: 'Hadir', color: '#2563eb', desc: 'Disiplin tinggi adaptasi Fase E' },
        sakit: { rate: 1.9, count: 7, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi wali' },
        izin: { rate: 1.1, count: 4, label: 'Izin', color: '#f59e0b', desc: 'Izin keperluan keluarga' },
        alpha: { rate: 0.6, count: 2, label: 'Alpha', color: '#f43f5e', desc: 'Alpha tanpa keterangan' }
      },
      xi: {
        totalSiswa: 0,
        hadir: { rate: 95.2, count: 343, label: 'Hadir', color: '#2563eb', desc: 'Termasuk rombel simulasi PKL industri' },
        sakit: { rate: 2.3, count: 8, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi dokter' },
        izin: { rate: 1.7, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin persiapan magang' },
        alpha: { rate: 0.8, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha tanpa keterangan' }
      },
      xii: {
        totalSiswa: 0,
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
        totalSiswa: 0,
        hadir: { rate: 94.2, count: 1017, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM penuh' },
        sakit: { rate: 2.9, count: 31, label: 'Sakit', color: '#0ea5e9', desc: 'Flu musiman pasca cuaca ekstrem' },
        izin: { rate: 1.8, count: 20, label: 'Izin', color: '#f59e0b', desc: 'Izin mudik / acara keluarga' },
        alpha: { rate: 1.1, count: 12, label: 'Alpha', color: '#f43f5e', desc: 'Keterlambatan kembali ke sekolah' }
      },
      x: {
        totalSiswa: 0,
        hadir: { rate: 94.7, count: 341, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran awal pekan stabil' },
        sakit: { rate: 2.8, count: 10, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.6, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin keluarga' },
        alpha: { rate: 0.9, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 0,
        hadir: { rate: 93.6, count: 337, label: 'Hadir', color: '#2563eb', desc: 'Persiapan jadwal penempatan DU/DI' },
        sakit: { rate: 3.2, count: 12, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit berizin' },
        izin: { rate: 2.0, count: 7, label: 'Izin', color: '#f59e0b', desc: 'Izin kegiatan keluarga' },
        alpha: { rate: 1.2, count: 4, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 0,
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
        totalSiswa: 0,
        hadir: { rate: 95.3, count: 1029, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM penuh' },
        sakit: { rate: 2.3, count: 25, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.6, count: 17, label: 'Izin', color: '#f59e0b', desc: 'Izin resmi' },
        alpha: { rate: 0.8, count: 9, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      x: {
        totalSiswa: 0,
        hadir: { rate: 95.8, count: 345, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran disiplin' },
        sakit: { rate: 2.2, count: 8, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.4, count: 5, label: 'Izin', color: '#f59e0b', desc: 'Izin resmi' },
        alpha: { rate: 0.6, count: 2, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 0,
        hadir: { rate: 94.9, count: 342, label: 'Hadir', color: '#2563eb', desc: 'KBM berjalan efektif' },
        sakit: { rate: 2.5, count: 9, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.7, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin resmi' },
        alpha: { rate: 0.9, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 0,
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
        totalSiswa: 0,
        hadir: { rate: 93.8, count: 1013, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM' },
        sakit: { rate: 3.4, count: 37, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 1.9, count: 21, label: 'Izin', color: '#f59e0b', desc: 'Izin kendala cuaca' },
        alpha: { rate: 0.9, count: 9, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      x: {
        totalSiswa: 0,
        hadir: { rate: 94.4, count: 340, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran Fase E' },
        sakit: { rate: 3.1, count: 11, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 1.7, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 0.8, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 0,
        hadir: { rate: 93.2, count: 335, label: 'Hadir', color: '#2563eb', desc: 'Kehadiran Fase F' },
        sakit: { rate: 3.7, count: 13, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 2.1, count: 8, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 1.0, count: 4, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 0,
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
        totalSiswa: 0,
        hadir: { rate: 94.5, count: 1021, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir KBM awal semester' },
        sakit: { rate: 2.6, count: 28, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit terkonfirmasi' },
        izin: { rate: 2.0, count: 22, label: 'Izin', color: '#f59e0b', desc: 'Izin perpanjangan tiket' },
        alpha: { rate: 0.9, count: 9, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      x: {
        totalSiswa: 0,
        hadir: { rate: 95.0, count: 342, label: 'Hadir', color: '#2563eb', desc: 'Awal semester genap' },
        sakit: { rate: 2.4, count: 9, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 1.8, count: 6, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 0.8, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xi: {
        totalSiswa: 0,
        hadir: { rate: 94.1, count: 339, label: 'Hadir', color: '#2563eb', desc: 'Awal semester genap' },
        sakit: { rate: 2.7, count: 10, label: 'Sakit', color: '#0ea5e9', desc: 'Sakit' },
        izin: { rate: 2.2, count: 8, label: 'Izin', color: '#f59e0b', desc: 'Izin' },
        alpha: { rate: 1.0, count: 3, label: 'Alpha', color: '#f43f5e', desc: 'Alpha' }
      },
      xii: {
        totalSiswa: 0,
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
  const stats = props.attendanceStats || {}
  const totalPresensi = stats.totalPresensi || 0
  const hadirRate = stats.hadirRate !== undefined ? stats.hadirRate : 0
  const sakitRate = stats.sakitRate !== undefined ? stats.sakitRate : 0
  const izinRate = stats.izinRate !== undefined ? stats.izinRate : 0
  const alphaRate = stats.alphaRate !== undefined ? stats.alphaRate : 0

  const hadirCount = stats.hadirCount || 0
  const sakitCount = stats.sakitCount || 0
  const izinCount = stats.izinCount || 0
  const alphaCount = stats.alphaCount || 0

  return {
    totalSiswa: totalSiswaCount.value,
    totalPresensi,
    hadir: { rate: hadirRate, count: hadirCount, label: 'Hadir', color: '#2563eb', desc: 'Siswa hadir mengikuti KBM' },
    sakit: { rate: sakitRate, count: sakitCount, label: 'Sakit', color: '#0ea5e9', desc: 'Keterangan sakit dari dokter/wali' },
    izin: { rate: izinRate, count: izinCount, label: 'Izin', color: '#f59e0b', desc: 'Izin tertulis dinas/keluarga' },
    alpha: { rate: alphaRate, count: alphaCount, label: 'Alpha', color: '#f43f5e', desc: 'Tanpa keterangan presensi' }
  }
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
  return []
})

const pieSlices = computed(() => {
  const cohort = currentCohortData.value
  if (cohort.totalPresensi === 0) {
    return []
  }

  const items = [
    { id: 'hadir', ...cohort.hadir },
    { id: 'sakit', ...cohort.sakit },
    { id: 'izin', ...cohort.izin },
    { id: 'alpha', ...cohort.alpha }
  ].filter(it => it.rate > 0)

  if (items.length === 0) return []

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

const jurusanStats = computed(() => {
  if (jurusanList.value && jurusanList.value.length > 0) {
    const colors = ['#10b981', '#2563eb', '#6366f1', '#0d9488', '#f59e0b', '#ec4899']
    return jurusanList.value.map((j, idx) => {
      const count = siswaList.value.filter(s => {
        const cls = s.className ? s.className.toUpperCase() : ''
        return cls.includes(j.code.toUpperCase()) || (s.major && s.major.toUpperCase() === j.code.toUpperCase())
      }).length

      return {
        name: j.code,
        fullName: j.name,
        rate: count > 0 ? 96.0 : 0.0,
        target: 95.0,
        students: count,
        color: colors[idx % colors.length],
        status: count > 0 ? 'Aktif' : 'Belum Ada Siswa'
      }
    })
  }
  return []
})

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
    const count = Math.round((rate / 100) * totalSiswaCount.value)
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

const guruBkList = ref(props.initialGuruBkList ? [...props.initialGuruBkList] : [])

watch(() => props.initialGuruBkList, (newVal) => {
  if (newVal) {
    guruBkList.value = [...newVal]
  }
}, { deep: true })

const formGuru = ref({
  name: '',
  email: '',
  password: '',
  jabatan: '',
  wa: '',
  rombelText: ''
})

const isSubmittingGuru = ref(false)

const saveGuru = () => {
  if (!formGuru.value.name || !formGuru.value.name.trim()) return
  if (!formGuru.value.email || !formGuru.value.email.trim()) return

  const cleanName = formGuru.value.name.trim()
  const cleanEmail = formGuru.value.email.trim().toLowerCase()

  isSubmittingGuru.value = true
  router.post('/admin/guru', {
    name: cleanName,
    email: cleanEmail,
    password: formGuru.value.password || '123456',
    jabatan: formGuru.value.jabatan && formGuru.value.jabatan.trim() ? formGuru.value.jabatan.trim() : 'Guru BK',
    wa: formGuru.value.wa && formGuru.value.wa.trim() ? formGuru.value.wa.trim() : '-',
    rombelText: formGuru.value.rombelText && formGuru.value.rombelText.trim() ? formGuru.value.rombelText.trim() : '-'
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isSubmittingGuru.value = false
      showToast(`Akun Guru BK ${cleanName} (${cleanEmail}) berhasil dibuat!`)
      closeModal()
      formGuru.value = {
        name: '',
        email: '',
        password: '',
        jabatan: '',
        wa: '',
        rombelText: ''
      }
      showGuruPassword.value = false
    },
    onError: (errs) => {
      isSubmittingGuru.value = false
      alert(Object.values(errs)[0] || 'Gagal menyimpan data Guru BK.')
    }
  })
}

const formEditGuru = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  jabatan: '',
  wa: '',
  rombelText: ''
})

const editingGuru = ref(null)

const editGuru = (g) => {
  editingGuru.value = g
  formEditGuru.value = {
    id: g.id,
    name: g.name,
    email: g.email || '',
    password: '',
    jabatan: g.jabatan || 'Guru BK',
    wa: g.wa === '-' ? '' : (g.wa || ''),
    rombelText: Array.isArray(g.rombel) ? g.rombel.join(', ') : (g.rombel || '')
  }
  showEditGuruPassword.value = false
  modalType.value = 'edit-guru'
}

const isUpdatingGuru = ref(false)

const updateGuru = () => {
  if (!editingGuru.value) return

  const cleanEmail = formEditGuru.value.email ? formEditGuru.value.email.trim().toLowerCase() : editingGuru.value.email
  isUpdatingGuru.value = true

  router.put(`/admin/guru/${editingGuru.value.id}`, {
    name: formEditGuru.value.name,
    email: cleanEmail,
    password: formEditGuru.value.password || undefined,
    jabatan: formEditGuru.value.jabatan && formEditGuru.value.jabatan.trim() ? formEditGuru.value.jabatan.trim() : 'Guru BK',
    wa: formEditGuru.value.wa && formEditGuru.value.wa.trim() ? formEditGuru.value.wa.trim() : '-',
    rombelText: formEditGuru.value.rombelText && formEditGuru.value.rombelText.trim() ? formEditGuru.value.rombelText.trim() : '-'
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isUpdatingGuru.value = false
      showToast(`Data Guru BK ${formEditGuru.value.name} (${cleanEmail}) berhasil diperbarui.`)
      closeModal()
    },
    onError: (errs) => {
      isUpdatingGuru.value = false
      alert(Object.values(errs)[0] || 'Gagal memperbarui data Guru BK.')
    }
  })
}

const confirmDeleteGuru = (g) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Akun Guru BK',
    message: 'Apakah Anda yakin ingin menghapus akun konselor ini? Akun tidak lagi dapat digunakan untuk login ke portal sistem.',
    targetInfo: {
      name: `${g.name} (${g.email})`,
      tag: g.jabatan,
      sub: `Rombel: ${Array.isArray(g.rombel) ? g.rombel.join(', ') : (g.rombel || '-')}`
    },
    confirmText: 'Ya, Hapus Akun',
    cancelText: 'Batal',
    onConfirm: () => {
      router.delete(`/admin/guru/${g.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          if (page.props.flash?.error) {
            showToast(page.props.flash.error)
          } else {
            showToast(`Akun Guru BK ${g.name} berhasil dihapus.`)
          }
        },
        onError: () => {
          showToast('Gagal menghapus akun Guru BK.')
        }
      })
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
// 2.5. DATA JURUSAN / BIDANG KEAHLIAN
// ==========================================
const jurusanList = ref(props.initialJurusanList ? [...props.initialJurusanList] : [])

watch(() => props.initialJurusanList, (newVal) => {
  if (newVal && newVal.length > 0) {
    jurusanList.value = [...newVal]
  }
}, { deep: true, immediate: true })

const formJurusan = ref({
  code: '',
  name: '',
  description: ''
})

const editingJurusan = ref(null)
const isSubmittingJurusan = ref(false)
const errorMessageJurusan = ref('')

const editJurusanItem = (j) => {
  editingJurusan.value = j
  errorMessageJurusan.value = ''
  formJurusan.value = {
    code: j.code,
    name: j.name,
    description: j.description || ''
  }
}

const cancelEditJurusan = () => {
  editingJurusan.value = null
  errorMessageJurusan.value = ''
  formJurusan.value = { code: '', name: '', description: '' }
}

const saveJurusan = (andCreateClass = false) => {
  if (!formJurusan.value.code || !formJurusan.value.name) {
    errorMessageJurusan.value = 'Kode jurusan dan nama lengkap jurusan wajib diisi.'
    return
  }

  isSubmittingJurusan.value = true
  errorMessageJurusan.value = ''

  const savedCode = formJurusan.value.code.trim().toUpperCase()
  const isEdit = !!editingJurusan.value
  const targetUrl = isEdit ? `/admin/jurusan/${editingJurusan.value.id}` : '/admin/jurusan'
  const method = isEdit ? 'put' : 'post'

  router[method](targetUrl, {
    code: savedCode,
    name: formJurusan.value.name.trim(),
    description: formJurusan.value.description
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      isSubmittingJurusan.value = false
      if (page.props.flash?.error) {
        errorMessageJurusan.value = page.props.flash.error
        return
      }
      showToast(`Jurusan ${savedCode} berhasil ${isEdit ? 'diperbarui' : 'ditambahkan'}!`)
      closeModal()
      if (andCreateClass) {
        setTimeout(() => {
          openModal('kelas')
          const found = jurusanList.value.find(j => j.code === savedCode)
          if (found) {
            formKelas.value.jurusan_id = found.id
            formKelas.value.jurusan = found.code
          } else {
            formKelas.value.jurusan = savedCode
          }
          autoGenerateClassName()
        }, 120)
      }
    },
    onError: (errors) => {
      isSubmittingJurusan.value = false
      if (errors.code) {
        errorMessageJurusan.value = errors.code
      } else if (errors.name) {
        errorMessageJurusan.value = errors.name
      } else {
        errorMessageJurusan.value = 'Gagal menyimpan jurusan. Silakan periksa formulir.'
      }
    }
  })
}

// ==========================================
// CRUD TINDAKAN / SANKSI PEMBINAAN
// ==========================================
const sanksiList = ref(props.initialSanksiList ? [...props.initialSanksiList] : [])
watch(() => props.initialSanksiList, (newVal) => {
  if (newVal) sanksiList.value = [...newVal]
}, { deep: true })

const editingSanksi = ref(null)
const isSavingSanksi = ref(false)
const errorMessageSanksi = ref('')
const formSanksi = ref({
  name: '',
  description: '',
  sort_order: 1,
  is_active: true,
})

const openSanksiModal = (s = null) => {
  errorMessageSanksi.value = ''
  if (s) {
    editingSanksi.value = s
    formSanksi.value = {
      name: s.name || '',
      description: s.description || '',
      sort_order: s.sort_order ?? 1,
      is_active: s.is_active !== false,
    }
  } else {
    editingSanksi.value = null
    formSanksi.value = {
      name: '',
      description: '',
      sort_order: sanksiList.value.length + 1,
      is_active: true,
    }
  }
  modalType.value = 'sanksi'
}

const saveSanksi = () => {
  errorMessageSanksi.value = ''
  if (!formSanksi.value.name.trim()) {
    errorMessageSanksi.value = 'Nama tindakan/sanksi wajib diisi.'
    return
  }

  isSavingSanksi.value = true
  const isEdit = !!editingSanksi.value
  const url = isEdit ? `/admin/sanksi/${editingSanksi.value.id}` : '/admin/sanksi'
  const method = isEdit ? 'put' : 'post'

  router[method](url, formSanksi.value, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      isSavingSanksi.value = false
      modalType.value = null
      editingSanksi.value = null
      showToast(page.props.flash?.success || (isEdit ? 'Tindakan berhasil diperbarui.' : 'Tindakan berhasil ditambahkan.'))
    },
    onError: (errors) => {
      isSavingSanksi.value = false
      errorMessageSanksi.value = Object.values(errors)[0] || 'Gagal menyimpan tindakan.'
    }
  })
}

const confirmDeleteSanksi = (s) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Tindakan / Sanksi',
    message: `Apakah Anda yakin ingin menghapus tindakan "${s.name}"?`,
    targetInfo: {
      name: s.name,
      tag: `Urutan ${s.sort_order}`,
      sub: s.description || 'Tindakan Pembinaan BK'
    },
    confirmText: 'Ya, Hapus',
    cancelText: 'Batal',
    onConfirm: () => {
      router.delete(`/admin/sanksi/${s.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          showToast(page.props.flash?.success || 'Tindakan berhasil dihapus.')
        },
        onError: () => {
          showToast('Gagal menghapus tindakan.')
        }
      })
    }
  }
}

// ==========================================
// CRUD ARSIP ALUMNI (SISWA LULUS)
// ==========================================
const alumniList = ref(props.initialAlumniList ? [...props.initialAlumniList] : [])
watch(() => props.initialAlumniList, (newVal) => {
  if (newVal) alumniList.value = [...newVal]
}, { deep: true })

const searchAlumni = ref('')
const filteredAlumni = computed(() => {
  const q = searchAlumni.value.trim().toLowerCase()
  if (!q) return alumniList.value
  return alumniList.value.filter(a =>
    (a.name || '').toLowerCase().includes(q) ||
    (a.nisn || a.nis || '').toLowerCase().includes(q) ||
    (a.lastClassName || '').toLowerCase().includes(q)
  )
})

const activeSiswaOptions = computed(() => {
  return siswaList.value.filter(s => (s.status || 'aktif').toLowerCase() !== 'lulus')
})

const angkatanCounts = computed(() => {
  const counts = { X: 0, XI: 0, XII: 0 }
  activeSiswaOptions.value.forEach(s => {
    const cls = (s.className || '').trim().toUpperCase()
    if (cls.startsWith('XII ')) counts.XII++
    else if (cls.startsWith('XI ')) counts.XI++
    else if (cls.startsWith('X ')) counts.X++
  })
  return counts
})

const editingAlumni = ref(null)
const alumniInputMode = ref('angkatan')
const isSavingAlumni = ref(false)
const errorMessageAlumni = ref('')
const formAlumni = ref({
  level: 'XII',
  student_id: '',
  nisn: '',
  name: '',
  last_class_name: '',
  graduation_year: new Date().getFullYear(),
})

const openAlumniModal = (a = null) => {
  errorMessageAlumni.value = ''
  if (a) {
    editingAlumni.value = a
    alumniInputMode.value = 'manual'
    formAlumni.value = {
      level: '',
      student_id: '',
      nisn: a.nisn || a.nis || '',
      name: a.name || '',
      last_class_name: a.lastClassName || '',
      graduation_year: a.graduationYear || new Date().getFullYear(),
    }
  } else {
    editingAlumni.value = null
    alumniInputMode.value = 'angkatan'
    formAlumni.value = {
      level: 'XII',
      student_id: '',
      nisn: '',
      name: '',
      last_class_name: '',
      graduation_year: new Date().getFullYear(),
    }
  }
  modalType.value = 'arsip'
}

const saveAlumni = () => {
  errorMessageAlumni.value = ''
  if (!editingAlumni.value && alumniInputMode.value === 'angkatan' && !formAlumni.value.level) {
    errorMessageAlumni.value = 'Silakan pilih angkatan yang akan diluluskan.'
    return
  }

  isSavingAlumni.value = true
  const isEdit = !!editingAlumni.value
  const url = isEdit ? `/admin/arsip-alumni/${editingAlumni.value.id}` : '/admin/arsip-alumni'
  const method = isEdit ? 'put' : 'post'

  router[method](url, formAlumni.value, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      isSavingAlumni.value = false
      modalType.value = null
      editingAlumni.value = null
      showToast(page.props.flash?.success || (isEdit ? 'Data alumni berhasil diperbarui.' : 'Data alumni berhasil disimpan.'))
    },
    onError: (errors) => {
      isSavingAlumni.value = false
      errorMessageAlumni.value = Object.values(errors)[0] || 'Gagal menyimpan data alumni.'
    }
  })
}

const restoreAlumni = (a) => {
  router.put(`/admin/arsip-alumni/${a.id}`, { restore_active: true }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      showToast(page.props.flash?.success || 'Siswa dipulihkan ke daftar aktif.')
    },
    onError: () => {
      showToast('Gagal memulihkan siswa.')
    }
  })
}

const confirmDeleteAlumni = (a) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Data Arsip Alumni',
    message: `Apakah Anda yakin ingin menghapus data alumni "${a.name}"?`,
    targetInfo: {
      name: a.name,
      tag: `NISN: ${a.nisn || a.nis}`,
      sub: `${a.lastClassName || '-'} • Lulus ${a.graduationYear || '-'}`
    },
    confirmText: 'Ya, Hapus',
    cancelText: 'Batal',
    onConfirm: () => {
      router.delete(`/admin/arsip-alumni/${a.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          showToast(page.props.flash?.success || 'Data alumni berhasil dihapus.')
        },
        onError: () => {
          showToast('Gagal menghapus data alumni.')
        }
      })
    }
  }
}

const confirmDeleteJurusan = (j) => {
  confirmModal.value = {
    show: true,
    type: 'delete',
    title: 'Hapus Jurusan',
    message: `Apakah Anda yakin ingin menghapus data jurusan "${j.name}" (${j.code})?`,
    targetInfo: {
      name: j.name,
      tag: j.code,
      sub: j.description || 'Program Keahlian'
    },
    confirmText: 'Ya, Hapus Jurusan',
    cancelText: 'Batal',
    onConfirm: () => {
      router.delete(`/admin/jurusan/${j.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          if (page.props.flash?.error) {
            errorMessageJurusan.value = page.props.flash.error
            showToast(page.props.flash.error)
          } else {
            errorMessageJurusan.value = ''
            showToast(`Jurusan ${j.code} berhasil dihapus.`)
            if (editingJurusan.value && editingJurusan.value.id === j.id) {
              cancelEditJurusan()
            }
          }
        },
        onError: () => {
          showToast('Gagal menghapus jurusan.')
        }
      })
    }
  }
}

// ==========================================
// 3. DATA KELAS / ROMBEL
// ==========================================
const kelasList = ref(props.initialKelasList ? [...props.initialKelasList] : [])

watch(() => props.initialKelasList, (newVal) => {
  if (newVal && newVal.length > 0) {
    kelasList.value = [...newVal]
  }
}, { deep: true })

const getClassGradeNumber = (k) => {
  if (!k) return ''
  const name = (k.name || '').trim().toUpperCase()
  const level = (k.level || '').trim().toUpperCase()
  const combined = `${name} ${level}`

  if (name.startsWith('XII ') || combined.includes('12') || combined.includes('(XII)')) {
    return '12'
  }
  if (name.startsWith('XI ') || combined.includes('11') || combined.includes('(XI)')) {
    return '11'
  }
  if (name.startsWith('X ') || combined.includes('10') || combined.includes('(X)')) {
    return '10'
  }
  return ''
}

const filteredKelasList = computed(() => {
  return kelasList.value.filter(k => {
    // 1. Filter Tingkat Kelas (10, 11, 12)
    if (selectedFilterLevelKelas.value !== 'Semua') {
      const grade = getClassGradeNumber(k)
      if (grade !== selectedFilterLevelKelas.value) {
        return false
      }
    }

    // 2. Filter Jurusan
    if (selectedFilterJurusanKelas.value !== 'Semua') {
      const jur = (k.jurusan || '').toUpperCase()
      if (jur !== selectedFilterJurusanKelas.value.toUpperCase()) {
        return false
      }
    }

    // 3. Pencarian nama rombel, wali kelas, atau guru BK
    if (searchKelas.value && searchKelas.value.trim() !== '') {
      const q = searchKelas.value.toLowerCase().trim()
      const matchName = (k.name || '').toLowerCase().includes(q)
      const matchWali = (k.waliKelas || '').toLowerCase().includes(q)
      const matchBk = (k.guruBK || '').toLowerCase().includes(q)
      const matchJur = (k.jurusan || '').toLowerCase().includes(q)
      if (!matchName && !matchWali && !matchBk && !matchJur) {
        return false
      }
    }

    return true
  })
})

const formKelas = ref({
  level: 'Kelas 10 (X)',
  jurusan_id: '',
  jurusan: 'RPL',
  name: '',
  waliKelas: '',
  guruBkId: ''
})

const onJurusanSelectChange = () => {
  const selected = jurusanList.value.find(j => j.id === formKelas.value.jurusan_id)
  if (selected) {
    formKelas.value.jurusan = selected.code
  }
  autoGenerateClassName()
}

const autoGenerateClassName = () => {
  const levelText = formKelas.value.level || 'Kelas 10 (X)'
  const levelNum = levelText.includes('12') ? 'XII' : (levelText.includes('11') ? 'XI' : 'X')
  const jur = (formKelas.value.jurusan || 'RPL').trim().toUpperCase()
  const prefix = `${levelNum} ${jur}`

  const existingMatches = kelasList.value
    .filter(k => k.name && k.name.toUpperCase().startsWith(prefix))
    .map(k => {
      const parts = k.name.trim().split(/\s+/)
      const lastPart = parts[parts.length - 1]
      const num = parseInt(lastPart, 10)
      return isNaN(num) ? 0 : num
    })

  const nextNum = existingMatches.length > 0 ? Math.max(...existingMatches) + 1 : 1
  formKelas.value.name = `${prefix} ${nextNum}`
}

const saveKelas = () => {
  const selectedJurusan = jurusanList.value.find(j => j.id === formKelas.value.jurusan_id)
  const jurCode = selectedJurusan ? selectedJurusan.code : (formKelas.value.jurusan || 'RPL')
  const selectedGuru = guruBkList.value.find(g => g.id === formKelas.value.guruBkId)

  router.post('/admin/kelas', {
    name: formKelas.value.name,
    level: formKelas.value.level,
    jurusan_id: formKelas.value.jurusan_id || null,
    jurusan: jurCode,
    waliKelas: formKelas.value.waliKelas,
    guruBkId: formKelas.value.guruBkId || null,
    guruBK: selectedGuru ? selectedGuru.name : null,
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      closeModal()
      showToast(`Rombel ${formKelas.value.name.toUpperCase()} berhasil disimpan.`)
    }
  })
}

const formEditKelas = ref({
  id: null,
  level: 'Kelas 10 (X)',
  jurusan_id: '',
  jurusan: 'RPL',
  name: '',
  waliKelas: '',
  guruBkId: '',
  studentCount: 0
})

const editingKelas = ref(null)

const onEditJurusanSelectChange = () => {
  const selected = jurusanList.value.find(j => j.id === formEditKelas.value.jurusan_id)
  if (selected) {
    formEditKelas.value.jurusan = selected.code
  }
}

const editKelas = (k) => {
  editingKelas.value = k
  let jurId = k.jurusan_id
  if (!jurId && k.jurusan) {
    const match = jurusanList.value.find(j => j.code === k.jurusan)
    if (match) jurId = match.id
  }

  formEditKelas.value = {
    id: k.id,
    level: k.level,
    jurusan_id: jurId || '',
    jurusan: k.jurusan,
    name: k.name,
    waliKelas: k.waliKelas,
    guruBkId: k.guruBkId || '',
    studentCount: k.studentCount ?? 0
  }
  modalType.value = 'edit-kelas'
}

const updateKelas = () => {
  if (!editingKelas.value) return
  const selectedJurusan = jurusanList.value.find(j => j.id === formEditKelas.value.jurusan_id)
  const jurCode = selectedJurusan ? selectedJurusan.code : formEditKelas.value.jurusan
  const selectedGuru = guruBkList.value.find(g => g.id === formEditKelas.value.guruBkId)

  router.put(`/admin/kelas/${editingKelas.value.id}`, {
    name: formEditKelas.value.name,
    level: formEditKelas.value.level,
    jurusan_id: formEditKelas.value.jurusan_id || null,
    jurusan: jurCode,
    waliKelas: formEditKelas.value.waliKelas,
    guruBkId: formEditKelas.value.guruBkId || null,
    guruBK: selectedGuru ? selectedGuru.name : null,
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      closeModal()
      showToast(`Data rombel ${formEditKelas.value.name} berhasil diperbarui.`)
    }
  })
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
      router.delete(`/admin/kelas/${k.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          if (page.props.flash?.error) {
            showToast(page.props.flash.error)
          } else {
            showToast(`Rombel ${k.name} berhasil dihapus.`)
          }
        },
        onError: () => {
          showToast('Gagal menghapus rombel kelas.')
        }
      })
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
const siswaList = ref(props.initialSiswaList ? [...props.initialSiswaList] : [])

watch(() => props.initialSiswaList, (newVal) => {
  if (newVal) {
    siswaList.value = [...newVal]
  }
}, { deep: true })

const totalSiswaCount = computed(() => {
  if (props.stats && typeof props.stats.totalStudents === 'number') {
    return props.stats.totalStudents
  }
  return siswaList.value.length
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

const isSubmittingSiswa = ref(false)

const saveSiswa = () => {
  if (isSubmittingSiswa.value) return
  isSubmittingSiswa.value = true

  router.post('/admin/siswa', formSiswa.value, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isSubmittingSiswa.value = false
      selectedFilterKelas.value = formSiswa.value.className || 'Semua'
      closeModal()
      showToast(`Data siswa ${formSiswa.value.name} berhasil disimpan ke database.`)
    },
    onError: (errs) => {
      isSubmittingSiswa.value = false
      alert(Object.values(errs)[0] || 'Gagal menyimpan data siswa.')
    }
  })
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
  if (!editingSiswa.value || isSubmittingSiswa.value) return
  isSubmittingSiswa.value = true

  router.put('/admin/siswa/' + editingSiswa.value.id, formEditSiswa.value, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isSubmittingSiswa.value = false
      closeModal()
      showToast(`Data siswa ${formEditSiswa.value.name} berhasil diperbarui.`)
    },
    onError: (errs) => {
      isSubmittingSiswa.value = false
      alert(Object.values(errs)[0] || 'Gagal memperbarui data siswa.')
    }
  })
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
      sub: `NISN: ${s.nisn} • Poin Tatib: ${s.poin || 0} Poin`
    },
    confirmText: 'Ya, Hapus Siswa',
    cancelText: 'Batal',
    onConfirm: () => {
      router.delete('/admin/siswa/' + s.id, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          showToast(`Data siswa ${s.name} berhasil dihapus dari sistem.`)
        },
        onError: () => {
          alert('Gagal menghapus data siswa.')
        }
      })
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
const isUploadingFile = ref(false)
const uploadError = ref('')

const downloadCsvTemplate = () => {
  const sampleClass = selectedFilterKelas.value !== 'Semua' ? selectedFilterKelas.value : 'XI TKKR 2'
  const csvContent = 'NISN,NAMA_LENGKAP,KELAS,JENIS_KELAMIN,NO_WA_WALI\n' +
    `00729188,Muhammad Bagas Saputra,${sampleClass},L,+62 812-3344-5566\n` +
    `00729189,Nabila Putri Ayu,${sampleClass},P,+62 813-7788-9900\n` +
    `00729190,Rendy Wahyu Pratama,${sampleClass},L,+62 856-1122-3344\n` +
    `00729191,Salsabila Dewi Lestari,${sampleClass},P,+62 878-4455-6677\n` +
    `00729192,Yoga Dwi Kurniawan,${sampleClass},L,+62 821-9900-1122\n` +
    `00729193,Zaskia Gotik Maharani,${sampleClass},P,+62 852-3344-7788`
  
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', `Template_Import_Siswa_${sampleClass.replace(/\s+/g, '_')}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const loadSampleDapodikData = () => {
  const target = selectedFilterKelas.value !== 'Semua' ? selectedFilterKelas.value : 'XI TKKR 2'
  importFileName.value = `Data_Siswa_Dapodik_${target.replace(/\s+/g, '_')}.csv`
  importTargetClass.value = target
  importedStudents.value = [
    { nisn: '00829101', name: 'Alifia Zahra Ramadhani', className: target, gender: 'P', parentWa: '+62 812-3401-1001' },
    { nisn: '00829102', name: 'Bayu Aditya Pratama', className: target, gender: 'L', parentWa: '+62 813-3401-1002' },
    { nisn: '00829103', name: 'Cahyo Wibowo', className: target, gender: 'L', parentWa: '+62 856-3401-1003' },
    { nisn: '00829104', name: 'Dwi Lestari Handayani', className: target, gender: 'P', parentWa: '+62 878-3401-1004' },
    { nisn: '00829105', name: 'Eko Prasetyo Nugroho', className: target, gender: 'L', parentWa: '+62 821-3401-1005' },
    { nisn: '00829106', name: 'Fani Oktaviana', className: target, gender: 'P', parentWa: '+62 852-3401-1006' },
    { nisn: '00829107', name: 'Guruh Arya Perkasa', className: target, gender: 'L', parentWa: '+62 812-3401-1007' },
    { nisn: '00829108', name: 'Hendri Kurniawan', className: target, gender: 'L', parentWa: '+62 813-3401-1008' },
    { nisn: '00829109', name: 'Indah Permata Sari', className: target, gender: 'P', parentWa: '+62 856-3401-1009' },
    { nisn: '00829110', name: 'Jovan Ardiansyah', className: target, gender: 'L', parentWa: '+62 878-3401-1010' },
    { nisn: '00829111', name: 'Kania Putri Jelita', className: target, gender: 'P', parentWa: '+62 821-3401-1011' },
    { nisn: '00829112', name: 'Lukman Hakim', className: target, gender: 'L', parentWa: '+62 852-3401-1012' },
    { nisn: '00829113', name: 'Muhammad Faizal Akbar', className: target, gender: 'L', parentWa: '+62 812-3401-1013' },
    { nisn: '00829114', name: 'Nadia Safitri', className: target, gender: 'P', parentWa: '+62 813-3401-1014' },
    { nisn: '00829115', name: 'Oky Firmansyah', className: target, gender: 'L', parentWa: '+62 856-3401-1015' }
  ]
}

const parseCsvText = (text) => {
  const lines = text.split(/\r?\n/).map(l => l.trim()).filter(Boolean)
  if (lines.length <= 1) return []

  // Deteksi delimiter
  const firstLine = lines[0]
  let delimiter = ','
  const semicolons = (firstLine.match(/;/g) || []).length
  const commas = (firstLine.match(/,/g) || []).length
  const tabs = (firstLine.match(/\t/g) || []).length
  if (semicolons > commas && semicolons > tabs) delimiter = ';'
  else if (tabs > commas && tabs > semicolons) delimiter = '\t'

  const splitRow = (rowStr) => {
    const regex = new RegExp(`(?:^|${delimiter})(?:"([^"]*)"|([^${delimiter}]*))`, 'g')
    const matches = []
    let match
    while ((match = regex.exec(rowStr)) !== null) {
      const val = match[1] !== undefined ? match[1] : match[2]
      matches.push((val || '').trim())
      if (regex.lastIndex === rowStr.length && rowStr.endsWith(delimiter)) {
        matches.push('')
        break
      }
    }
    return matches.length > 0 ? matches : rowStr.split(delimiter).map(c => c.trim().replace(/^["']|["']$/g, ''))
  }

  const headerRow = splitRow(lines[0])
  const colMap = { nisn: -1, name: -1, className: -1, gender: -1, parentWa: -1 }

  headerRow.forEach((col, idx) => {
    const c = col.toLowerCase().replace(/[^a-z0-9]/g, '')
    if (['nisn', 'nis', 'noinduk'].includes(c)) colMap.nisn = idx
    else if (['nama', 'namasiswa', 'namalengkap', 'namapesertadidik', 'studentname'].includes(c)) colMap.name = idx
    else if (['kelas', 'rombel', 'namarombel', 'tingkat', 'classname'].includes(c)) colMap.className = idx
    else if (['jk', 'jeniskelamin', 'lp', 'gender', 'sex'].includes(c)) colMap.gender = idx
    else if (['nowa', 'nohp', 'nowawali', 'telepon', 'whatsapp', 'kontak', 'parentwa'].includes(c)) colMap.parentWa = idx
  })

  const hasRecognizedHeader = colMap.name !== -1 || colMap.nisn !== -1
  const startIndex = hasRecognizedHeader ? 1 : 0

  if (!hasRecognizedHeader) {
    colMap.nisn = 0
    colMap.name = 1
    colMap.className = 2
    colMap.gender = 3
    colMap.parentWa = 4
  }

  const results = []
  for (let i = startIndex; i < lines.length; i++) {
    const cols = splitRow(lines[i])
    if (cols.length < 2) continue

    const name = (colMap.name !== -1 && cols[colMap.name]) ? cols[colMap.name] : (cols[1] || '')
    if (!name || /^(mengetahui|kepala|wali|total|jumlah)/i.test(name)) continue

    const rawNisn = (colMap.nisn !== -1 && cols[colMap.nisn]) ? cols[colMap.nisn] : (cols[0] || '')
    const nisn = rawNisn.replace(/[^a-zA-Z0-9]/g, '') || `00829${1000 + i}`
    const rawClass = (colMap.className !== -1 && cols[colMap.className]) ? cols[colMap.className] : (cols[2] || '')
    const className = rawClass.trim().toUpperCase() || (selectedFilterKelas.value !== 'Semua' ? selectedFilterKelas.value : 'X RPL 1')
    const rawGender = (colMap.gender !== -1 && cols[colMap.gender]) ? cols[colMap.gender].toUpperCase() : (cols[3]?.toUpperCase() || 'L')
    const gender = (rawGender.startsWith('P') || rawGender.includes('WANITA') || rawGender.includes('PEREMPUAN')) ? 'P' : 'L'
    const parentWa = (colMap.parentWa !== -1 && cols[colMap.parentWa]) ? cols[colMap.parentWa] : (cols[4] || '')

    results.push({
      nis: nisn,
      nisn,
      name,
      className,
      gender,
      parentWa: parentWa || '+62 812-3344-5566'
    })
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

const processFile = async (file) => {
  importFileName.value = file.name
  uploadError.value = ''
  isUploadingFile.value = true

  const ext = file.name.split('.').pop().toLowerCase()

  // Untuk file CSV atau TXT, baca langsung melalui browser secara instan
  if (['csv', 'txt'].includes(ext)) {
    const reader = new FileReader()
    reader.onload = (e) => {
      try {
        const text = e.target.result
        const parsed = parseCsvText(text)
        if (parsed.length > 0) {
          importedStudents.value = parsed
          if (importTargetClass.value === 'AUTO' && parsed[0]?.className) {
            importTargetClass.value = parsed[0].className
          }
          showToast(`Berhasil membaca ${parsed.length} data siswa dari file ${file.name}.`)
        } else {
          uploadError.value = 'Tidak ada baris data siswa yang terbaca dari file CSV/TXT ini.'
        }
      } catch (err) {
        console.error(err)
        uploadError.value = 'Gagal memproses struktur file CSV.'
      } finally {
        isUploadingFile.value = false
      }
    }
    reader.onerror = () => {
      uploadError.value = 'Gagal membaca file dari penyimpanan lokal.'
      isUploadingFile.value = false
    }
    reader.readAsText(file)
    return
  }

  // Untuk Excel (.xlsx, .xls), proses melalui server preview
  try {
    const formData = new FormData()
    formData.append('file', file)
    if (importTargetClass.value && importTargetClass.value !== 'AUTO') {
      formData.append('targetClass', importTargetClass.value)
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    const response = await fetch('/admin/siswa/preview-import', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })

    const data = await response.json()
    if (response.ok && data.success) {
      if (data.students && data.students.length > 0) {
        importedStudents.value = data.students
        if (data.detectedClass && importTargetClass.value === 'AUTO') {
          importTargetClass.value = data.detectedClass
        }
        showToast(`Berhasil membaca ${data.students.length} data siswa dari file.`)
      } else {
        uploadError.value = 'Tidak ada baris siswa yang terdeteksi di dalam file spreadsheet.'
      }
    } else {
      uploadError.value = data.message || 'Gagal membaca isi file spreadsheet.'
    }
  } catch (err) {
    console.error('Import preview error:', err)
    uploadError.value = 'Terjadi kesalahan saat memproses file di server.'
  } finally {
    isUploadingFile.value = false
  }
}

const removeImportedRow = (index) => {
  importedStudents.value.splice(index, 1)
}

const isSubmittingImport = ref(false)

const processImport = () => {
  if (importedStudents.value.length === 0 || isSubmittingImport.value) return
  
  const payload = importedStudents.value.map(st => ({
    nisn: st.nisn,
    name: st.name,
    className: importTargetClass.value === 'AUTO' ? st.className : importTargetClass.value,
    gender: st.gender,
    parentWa: st.parentWa,
  }))

  const targetClassToFocus = importTargetClass.value !== 'AUTO' ? importTargetClass.value : (payload[0]?.className || 'Semua')

  isSubmittingImport.value = true
  router.post('/admin/siswa/import', {
    students: payload,
    targetClass: importTargetClass.value
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isSubmittingImport.value = false
      importedStudents.value = []
      importFileName.value = ''
      closeModal()
      activeTab.value = 'siswa'
      if (targetClassToFocus && targetClassToFocus !== 'AUTO') {
        selectedFilterKelas.value = targetClassToFocus
      }
      showToast(`Berhasil mengimpor ${payload.length} data siswa ke database BK Prima.`)
    },
    onError: () => {
      isSubmittingImport.value = false
      alert('Gagal mengimpor data siswa. Silakan periksa kembali data spreadsheet.')
    }
  })
}

// Modal Handlers
const openModal = (type) => {
  modalType.value = type
  if (type === 'guru') {
    formGuru.value = { name: '', nip: '', nuptk: '', jabatan: '', email: '', wa: '', rombelText: '' }
  } else if (type === 'kelas') {
    const defaultJur = jurusanList.value[0]
    formKelas.value = {
      level: 'Kelas 10 (X)',
      jurusan_id: defaultJur ? defaultJur.id : '',
      jurusan: defaultJur ? defaultJur.code : 'RPL',
      name: '',
      waliKelas: '',
      guruBkId: guruBkList.value[0]?.id || ''
    }
    autoGenerateClassName()
  } else if (type === 'jurusan') {
    formJurusan.value = { code: '', name: '', description: '' }
    editingJurusan.value = null
    errorMessageJurusan.value = ''
  } else if (type === 'siswa') {
    formSiswa.value = {
      nisn: '',
      name: '',
      className: (selectedFilterKelas.value !== 'Semua' ? selectedFilterKelas.value : (kelasList.value[0]?.name || 'X RPL 1')),
      gender: 'L',
      parentWa: ''
    }
  } else if (type === 'import-siswa') {
    importedStudents.value = []
    importFileName.value = ''
    uploadError.value = ''
    isUploadingFile.value = false
    importTargetClass.value = selectedFilterKelas.value !== 'Semua' ? selectedFilterKelas.value : 'AUTO'
  }
}

const closeModal = () => {
  modalType.value = null
  importedStudents.value = []
  importFileName.value = ''
  editingSiswa.value = null
  editingGuru.value = null
  editingKelas.value = null
  editingJurusan.value = null
  errorMessageJurusan.value = ''
  formJurusan.value = { code: '', name: '', description: '' }
}
</script>
