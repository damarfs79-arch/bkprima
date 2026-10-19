<template>
  <AppLayout active-tab="piket" show-share>
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Semester Subheader Bar & Clock -->
      <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2 text-gray-700 font-semibold">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Semester Genap 2026/2027 • Pekan ke-8</span>
        </div>
        <div class="flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 rounded-xl font-mono font-bold">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ currentTime }} WIB</span>
        </div>
      </div>

      <!-- Horizontal Calendar Week Selector -->
      <div class="grid grid-cols-6 gap-2 sm:gap-3">
        <button
          v-for="day in weekDays"
          :key="day.date"
          @click="selectedDay = day.date"
          class="flex flex-col items-center py-2.5 sm:py-3 rounded-2xl border transition text-xs cursor-pointer shadow-2xs"
          :class="selectedDay === day.date ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-md scale-102' : 'bg-white text-gray-600 border-gray-100 hover:border-gray-300 hover:bg-gray-50'"
        >
          <span class="text-[10px] tracking-wider uppercase opacity-80">{{ day.day }}</span>
          <span class="text-base sm:text-lg font-black my-0.5">{{ day.date }}</span>
          <span class="w-1.5 h-1.5 rounded-full" :class="selectedDay === day.date ? 'bg-white' : (day.hasDuty ? 'bg-blue-500' : 'bg-transparent')"></span>
        </button>
      </div>

      <!-- Hero Card: Tugas Jaga Anda Hari Ini -->
      <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
        <!-- Background glow -->
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/20 rounded-full blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-3">
          <div class="space-y-3">
            <div class="flex items-center gap-2">
            </div>

            <div>
              <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">Dra. Hj. Nurul Hidayati, M.Pd</h2>
              <p class="text-xs text-blue-200">Koordinator Bimbingan Konseling & Disiplin</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/10 flex items-start gap-3 max-w-lg">
              <div class="p-2 bg-blue-500/30 rounded-xl text-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="text-xs">
                <p class="text-[10px] uppercase font-bold text-blue-300 tracking-wider">POS PENJAGAAN</p>
                <p class="font-bold text-white text-sm">Gerbang Utama</p>
                <p class="text-blue-200 text-[11px]">Pintu Masuk Siswa & Parkir Kendaraan Roda 2</p>
                <div class="flex items-center gap-2 mt-2">
                  <span class="text-xs font-bold text-amber-300">⏰ 06.30 – 07.30 WIB</span>
                  <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md text-white font-medium">Piket Pagi Masuk</span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-300 bg-emerald-950/40 border border-emerald-500/30 px-3.5 py-2 rounded-xl max-w-md">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span>Sudah Presensi Piket (06.24 WIB • GPS Valid)</span>
            </div>
          </div>

          <!-- Quick Action CTA -->
          <div class="flex flex-col gap-3 self-stretch md:self-center">
            <router-link
              to="/verifikasi"
              class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg transition text-xs sm:text-sm flex items-center justify-center gap-2 text-center cursor-pointer border border-blue-300/30"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              <span>+ Catat Siswa Terlambat / Tertibkan</span>
            </router-link>
            <router-link
              to="/panduan"
              class="bg-white/10 hover:bg-white/20 text-blue-100 font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition text-center"
            >
              <span>Buku Pedoman Sanksi & Poin</span>
              <span>&rarr;</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- 3 Summary Metric Cards -->
      <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex flex-col items-center text-center">
          <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <span class="text-2xl font-black text-gray-900">6</span>
          <span class="text-[11px] font-bold text-gray-700">Guru Terjadwal</span>
          <span class="text-[10px] text-emerald-600 font-semibold mt-0.5">4 Hadir • 1 Izin</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex flex-col items-center text-center">
          <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            </svg>
          </div>
          <span class="text-2xl font-black text-gray-900">3</span>
          <span class="text-[11px] font-bold text-gray-700">Titik Pos Jaga</span>
          <span class="text-[10px] text-emerald-600 font-semibold mt-0.5">100% Terisi</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex flex-col items-center text-center">
          <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <span class="text-2xl font-black text-rose-600">18</span>
          <span class="text-[11px] font-bold text-gray-700">Siswa Ditertibkan</span>
          <span class="text-[10px] text-gray-500 font-medium mt-0.5">Sanksi Edukatif</span>
        </div>
      </div>

      <!-- Section: Penugasan Pos Hari Ini (2-column layout on Desktop) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Pos 1, 2, 3 List (2 Cols on lg) -->
        <div class="lg:col-span-2 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm sm:text-base font-black text-gray-900">Penugasan Pos Hari Ini</h3>
              <p class="text-xs text-gray-400">Senin Pagi, 24 Februari 2025</p>
            </div>
            <button class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 bg-white px-3 py-1.5 rounded-xl border border-gray-100 shadow-2xs cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>Refresh</span>
            </button>
          </div>

          <!-- Pos 1 -->
          <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
              <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center">1</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">Gerbang Depan & Masuk Siswa</h4>
                  <p class="text-[11px] text-gray-400">Pintu gerbang utama & jalur pejalan kaki</p>
                </div>
              </div>
              <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-xs rounded-full border border-emerald-100">
                Lengkap (3/3)
              </span>
            </div>

            <div class="space-y-2">
              <div class="flex items-center justify-between p-2.5 rounded-xl bg-blue-50/50 border border-blue-100/60">
                <div class="flex items-center gap-3">
                  <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-blue-200" />
                  <div>
                    <p class="font-bold text-gray-900 text-xs flex items-center gap-1.5">
                      Dra. Hj. Nurul Hidayati, M.Pd
                      <span class="text-[9px] bg-blue-600 text-white font-extrabold px-1.5 py-0.2 rounded-md">Anda</span>
                    </p>
                    <p class="text-[10px] text-gray-500">Koord. Guru BK</p>
                  </div>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-600 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  06.24 WIB
                </span>
              </div>

              <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                  <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200" />
                  <div>
                    <p class="font-bold text-gray-900 text-xs">Bpk. Bambang Soeprapto, S.Pd</p>
                    <p class="text-[10px] text-gray-500">PKS Kesiswaan & Ketertiban</p>
                  </div>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-600 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  06.28 WIB
                </span>
              </div>

              <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                  <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200" />
                  <div>
                    <p class="font-bold text-gray-900 text-xs">Ibu Sulastri, S.Pd</p>
                    <p class="text-[10px] text-gray-500">Guru Piket Harian</p>
                  </div>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-600 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  06.15 WIB
                </span>
              </div>
            </div>

            <div class="flex gap-2 pt-2">
              <button class="flex-1 py-2 px-3 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-100 hover:bg-blue-100 transition flex items-center justify-center gap-1.5 cursor-pointer">
                <span> WhatsApp Grup Pos 1</span>
              </button>
              <button class="p-2 bg-gray-50 text-gray-600 text-xs font-bold rounded-xl border border-gray-200 hover:bg-gray-100 transition cursor-pointer" title="Lokasi Pos">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Pos 2 -->
          <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
              <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center">2</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">Lobi Utama & Tangga Gedung A/B</h4>
                  <p class="text-[11px] text-gray-400">Pemeriksaan atribut seragam & ketertiban koridor</p>
                </div>
              </div>
              <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-full border border-blue-100">
                2 Petugas
              </span>
            </div>

            <div class="space-y-2">
              <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center">KW</div>
                  <div>
                    <p class="font-bold text-gray-900 text-xs">Bpk. Kurniawan, S.Kom</p>
                    <p class="text-[10px] text-gray-500">Wali Kelas XI MIPA 2</p>
                  </div>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-600 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  06.32 WIB
                </span>
              </div>

              <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50/50 border border-amber-100">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold text-xs flex items-center justify-center">RW</div>
                  <div>
                    <p class="font-bold text-gray-900 text-xs">Ibu Rina Wardani, M.Pd</p>
                    <p class="text-[10px] text-amber-600 font-medium">Digantikan Bpk. Fajar Ramadhan, S.Pd</p>
                  </div>
                </div>
                <button class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-lg text-[10px] font-bold transition cursor-pointer flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                  Tukar Jadwal
                </button>
              </div>
            </div>
          </div>

          <!-- Pos 3 -->
          <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
              <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center">3</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">Gerbang Barat & Parkir Motor</h4>
                  <p class="text-[11px] text-gray-400">Penertiban kelengkapan helm & knalpot standar</p>
                </div>
              </div>
              <span class="px-2.5 py-1 bg-orange-50 text-orange-700 font-bold text-xs rounded-full border border-orange-100">
                Menunggu
              </span>
            </div>

            <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">HW</div>
                <div>
                  <p class="font-bold text-gray-900 text-xs">Bpk. Hendra Wijaya, S.Pd</p>
                  <p class="text-[10px] text-gray-500">Guru PJOK & Pembina OSIS</p>
                </div>
              </div>
              <span class="text-xs font-semibold text-amber-600 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Menuju Pos
              </span>
            </div>
          </div>
        </div>

        <!-- Sidebar: SOP Piket & Quick Reports (1 Col on lg) -->
        <div class="space-y-6">

          <!-- Manajemen & Laporan Piket -->
          <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-3">
            <h4 class="font-bold text-gray-900 text-sm">Manajemen & Laporan Piket</h4>
            
            <button class="w-full p-3 rounded-xl bg-gray-50 hover:bg-blue-50/50 border border-gray-100 text-left transition flex items-center justify-between group cursor-pointer">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                </div>
                <div>
                  <p class="font-bold text-xs text-gray-900 group-hover:text-blue-600">Ajukan Tukar Jadwal Piket</p>
                  <p class="text-[10px] text-gray-400">Delegasi tugas jaga dengan rekan guru</p>
                </div>
              </div>
              <span class="text-gray-400 group-hover:text-blue-600">&rarr;</span>
            </button>

            <button class="w-full p-3 rounded-xl bg-gray-50 hover:bg-emerald-50/50 border border-gray-100 text-left transition flex items-center justify-between group cursor-pointer">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <p class="font-bold text-xs text-gray-900 group-hover:text-emerald-600">Jurnal & Berita Acara Piket</p>
                  <p class="text-[10px] text-gray-400">Rekap catatan & ekspor PDF laporan</p>
                </div>
              </div>
              <span class="text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
              </span>
            </button>
          </div>

          <!-- Panduan Standar SOP Piket Pagi -->
          <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-3">
            <div class="flex items-center gap-2 text-gray-900 font-bold text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Panduan Standar SOP Piket Pagi</span>
            </div>
            <p class="text-[11px] text-gray-500 leading-relaxed">Harap laksanakan pengawasan gerbang sesuai ketentuan Tata Tertib Disiplin Sekolah:</p>

            <div class="space-y-2.5 text-xs text-gray-700">
              <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-gray-100">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                <p class="leading-relaxed"><strong>Pintu Gerbang Ditutup Tepat 07.00 WIB.</strong> Seluruh siswa yang hadir setelah bel wajib diarahkan ke barisan pembinaan gerbang depan.</p>
              </div>

              <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-gray-100">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                <p class="leading-relaxed"><strong>Pemeriksaan Atribut:</strong> Cek dasi, kelengkapan ikat pinggang, kaos kaki putih logo sekolah, dan kerapian rambut siswa putra.</p>
              </div>

              <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-gray-100">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                <p class="leading-relaxed"><strong>Input Langsung:</strong> Scan kartu pelajar siswa atau ketik nama/NISN melalui menu 'Catat Siswa Terlambat' untuk pembaruan data real-time ke wali murid.</p>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, onMounted, onUnmounted } from 'vue'

const selectedDay = ref('24')
const currentTime = ref('07:15:20')

const weekDays = [
  { day: 'SEN', date: '24', hasDuty: true },
  { day: 'SEL', date: '25', hasDuty: false },
  { day: 'RAB', date: '26', hasDuty: true },
  { day: 'KAM', date: '27', hasDuty: false },
  { day: 'JUM', date: '28', hasDuty: true },
  { day: 'SAB', date: '01', hasDuty: false },
]

let intervalId = null

onMounted(() => {
  const updateTime = () => {
    const now = new Date()
    currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
  }
  updateTime()
  intervalId = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>
