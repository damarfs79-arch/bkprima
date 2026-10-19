<template>
  <div class="min-h-screen bg-slate-50 font-sans text-gray-800 flex flex-col selection:bg-blue-600 selection:text-white">
    
    <!-- ============================================================ -->
    <!-- 1. TOP NAVBAR / HEADER (LANDING PAGE STYLE)                   -->
    <!-- ============================================================ -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200/80 shadow-2xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
        
        <!-- Brand Logo -->
        <router-link to="/tamu" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center p-1.5 shadow-2xs group-hover:scale-105 transition">
            <img src="/logo-smk.png" alt="Logo SMK" class="w-full h-full object-contain" />
          </div>
          <div>
            <div class="flex items-center gap-1.5">
              <span class="bg-blue-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-md leading-none tracking-wider">BK</span>
              <span class="text-xl font-black text-gray-900 tracking-tight">Prima</span>
              <span class="ml-1 text-[10px] font-extrabold uppercase px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full">
                Portal Tamu
              </span>
            </div>
            <p class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">SMK PGRI 05 JEMBER</p>
          </div>
        </router-link>

        <!-- Navigation Links (Desktop) -->
        <nav class="hidden md:flex items-center gap-1 text-xs font-bold text-gray-600">
          <a href="#hero" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-gray-50 transition">Beranda</a>
          <a href="#cek-pelanggaran" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-gray-50 transition flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
            <span>Cek Poin & Pelanggaran</span>
          </a>
          <a href="#eksplor-kelas" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-gray-50 transition">Data Kelas</a>
          <a href="#tata-tertib" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-gray-50 transition">Tata Tertib</a>
          <a href="#layanan-bk" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-gray-50 transition">Kontak BK</a>
        </nav>

        <!-- Right Action: Guru BK Login Button -->
        <div class="flex items-center gap-2">
          <router-link
            to="/login"
            class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs flex items-center gap-2 transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            <span class="hidden sm:inline">Masuk Guru BK</span>
            <span class="sm:hidden">Guru BK</span>
          </router-link>
        </div>

      </div>
    </header>

    <!-- ============================================================ -->
    <!-- 2. HERO SECTION                                              -->
    <!-- ============================================================ -->
    <section id="hero" class="relative overflow-hidden bg-gradient-to-b from-blue-700 via-blue-800 to-indigo-900 text-white pt-12 pb-16 sm:py-20">
      
      <!-- Ambient Glows -->
      <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute bottom-0 left-10 w-80 h-80 bg-teal-400/15 rounded-full blur-3xl pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center flex flex-col items-center">
        
        <!-- Pill Badge: Role Tamu -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-bold text-blue-100 mb-6 shadow-xs animate-bounce-subtle">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span>Akses Terbuka untuk Siswa &amp; Orang Tua / Wali Murid</span>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight max-w-4xl">
          Portal Transparansi Presensi &amp; Kedisiplinan Siswa
        </h1>

        <!-- Subtitle -->
        <p class="mt-5 text-sm sm:text-lg text-blue-100/90 max-w-2xl font-normal leading-relaxed">
          Ketahui kehadiran anak di sekolah, pantau rekap kelas, dan cek akumulasi poin sanksi secara langsung, transparan, dan terpercaya di SMK PGRI 05 Jember.
        </p>

        <!-- Fast CTA Buttons -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4 w-full max-w-md">
          <a
            href="#cek-pelanggaran"
            class="flex-1 min-w-[200px] px-6 py-3.5 bg-emerald-500 hover:bg-emerald-400 active:scale-98 text-white font-extrabold rounded-2xl shadow-lg shadow-emerald-900/30 text-xs sm:text-sm flex items-center justify-center gap-2 transition"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Cek Poin &amp; Pelanggaran</span>
          </a>

          <a
            href="#eksplor-kelas"
            class="flex-1 min-w-[180px] px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl border border-white/20 backdrop-blur-md text-xs sm:text-sm flex items-center justify-center gap-2 transition"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Lihat Daftar Kelas</span>
          </a>
        </div>

        <!-- Metric Highlight Bar -->
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 w-full max-w-4xl text-left">
          <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl">
            <span class="text-xs text-blue-200 font-semibold block">Total Kelas Aktif</span>
            <span class="text-2xl sm:text-3xl font-black mt-1 block">36 Rombel</span>
            <span class="text-[11px] text-blue-300">Tingkat X, XI, XII</span>
          </div>

          <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl">
            <span class="text-xs text-blue-200 font-semibold block">Populasi Siswa</span>
            <span class="text-2xl sm:text-3xl font-black mt-1 block">1.285</span>
            <span class="text-[11px] text-blue-300">7 Konsentrasi Keahlian</span>
          </div>

          <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl">
            <span class="text-xs text-blue-200 font-semibold block">Rata-rata Presensi</span>
            <span class="text-2xl sm:text-3xl font-black mt-1 block text-emerald-300">94.8%</span>
            <span class="text-[11px] text-emerald-200">Kategori Sangat Baik</span>
          </div>

          <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl">
            <span class="text-xs text-blue-200 font-semibold block">Sistem Pembinaan</span>
            <span class="text-2xl sm:text-3xl font-black mt-1 block text-amber-300">Humanis</span>
            <span class="text-[11px] text-amber-200">Pendampingan BK &amp; Wali</span>
          </div>
        </div>

      </div>
    </section>

    <!-- ============================================================ -->
    <!-- 3. SECTION: CEK POIN & PELANGGARAN SISWA MANDIRI            -->
    <!-- ============================================================ -->
    <section id="cek-pelanggaran" class="py-14 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
      <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="px-3 py-1 bg-rose-50 text-rose-600 border border-rose-100 rounded-full text-xs font-extrabold uppercase tracking-wider">
          Pencarian Data Siswa
        </span>
        <h2 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight mt-2">
          Cek Poin &amp; Rekap Pelanggaran Siswa
        </h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-2">
          Masukkan <strong>Nama Siswa</strong> atau <strong>NISN</strong> untuk mengecek status kepatuhan tata tertib, akumulasi poin, dan riwayat pembinaan konseling.
        </p>
      </div>

      <!-- Search Box Card -->
      <div class="max-w-2xl mx-auto bg-white rounded-3xl p-5 sm:p-7 border border-gray-200/80 shadow-md">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Ketik Nama Siswa atau NISN (contoh: Rizky atau 22231084)..."
            class="w-full bg-gray-50 border border-gray-200 rounded-2xl pl-12 pr-28 py-3.5 text-xs sm:text-sm text-gray-900 font-semibold placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
          />
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              type="button"
              class="text-xs font-bold text-gray-400 hover:text-gray-700 bg-gray-200 hover:bg-gray-300 px-2.5 py-1.5 rounded-xl transition cursor-pointer"
            >
              Hapus
            </button>
            <button
              @click="executeSearch"
              type="button"
              class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer"
            >
              Cari
            </button>
          </div>
        </div>

        <!-- Quick Demo Chips -->
        <div class="mt-4 flex flex-wrap items-center gap-1.5 text-xs">
          <span class="text-gray-400 font-medium">Contoh Cepat:</span>
          <button
            v-for="chip in demoChips"
            :key="chip.nisn"
            @click="selectStudentByNisn(chip.nisn)"
            type="button"
            class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold transition cursor-pointer border border-blue-100 text-[11px]"
          >
            {{ chip.name }} ({{ chip.class }})
          </button>
        </div>
      </div>

      <!-- Result Card: When Student Found -->
      <div v-if="selectedStudent" class="max-w-4xl mx-auto mt-8 animate-in fade-in duration-200">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-lg overflow-hidden">
          
          <!-- Card Header & Profile Banner -->
          <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 text-white relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
              
              <div class="flex items-center gap-4">
                <img
                  :src="selectedStudent.avatar"
                  :alt="selectedStudent.name"
                  class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-white/20 shadow-md"
                />
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="text-lg sm:text-2xl font-black text-white tracking-tight">{{ selectedStudent.name }}</h3>
                    <span
                      class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border"
                      :class="statusBadgeClass(selectedStudent.points)"
                    >
                      {{ statusLabel(selectedStudent.points) }}
                    </span>
                  </div>
                  <p class="text-xs sm:text-sm text-blue-200 font-medium mt-0.5">
                    NISN: <span class="font-bold text-white">{{ selectedStudent.nisn }}</span> • Kelas: <span class="font-bold text-white">{{ selectedStudent.class }}</span>
                  </p>
                  <p class="text-xs text-slate-300 mt-1 flex items-center gap-1.5">
                    <span>Wali Kelas: {{ selectedStudent.waliKelas }}</span>
                    <span>&bull;</span>
                    <span>Guru BK: {{ selectedStudent.guruBK }}</span>
                  </p>
                </div>
              </div>

              <!-- Contact BK via WhatsApp Button for Parents -->
              <button
                @click="openWhatsApp(selectedStudent)"
                class="self-start sm:self-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-md transition cursor-pointer shrink-0"
              >
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.22.771 1.394.858.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Konsultasi Wali Murid</span>
              </button>

            </div>
          </div>

          <!-- Points Gauge & Progress Bar -->
          <div class="p-6 sm:p-8 bg-slate-50 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
              <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">AKUMULASI POIN PELANGGARAN</span>
                <div class="flex items-baseline gap-2 mt-0.5">
                  <span
                    class="text-4xl sm:text-5xl font-black tracking-tight"
                    :class="pointsColorClass(selectedStudent.points)"
                  >
                    {{ selectedStudent.points }}
                  </span>
                  <span class="text-xs sm:text-sm font-semibold text-gray-400">/ 100 Poin Maksimal Batas Sanksi</span>
                </div>
              </div>

              <!-- Status Tag -->
              <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-2xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase block">Kategori Tindakan</span>
                <span class="text-xs font-black" :class="pointsColorClass(selectedStudent.points)">
                  {{ actionCategory(selectedStudent.points) }}
                </span>
              </div>
            </div>

            <!-- Visual Progress Bar -->
            <div class="relative w-full h-3.5 bg-gray-200 rounded-full overflow-hidden p-0.5 mt-4">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="progressBarClass(selectedStudent.points)"
                :style="{ width: `${Math.min(100, Math.max(5, selectedStudent.points))}%` }"
              ></div>
            </div>

            <div class="flex justify-between text-[10px] font-bold text-gray-400 mt-2 px-1">
              <span>0 (Sangat Tertib)</span>
              <span>25 (Peringatan I)</span>
              <span>50 (Peringatan II)</span>
              <span>75 (Panggilan Ortu)</span>
              <span>100 (Sanksi Tegas)</span>
            </div>
          </div>

          <!-- Timeline: Riwayat Pelanggaran & Kasus -->
          <div class="p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="font-black text-gray-900 text-sm sm:text-base flex items-center gap-2">
                <span>Riwayat Catatan Pelanggaran</span>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                  {{ selectedStudent.violations.length }} Kasus
                </span>
              </h4>
              <span class="text-xs text-gray-400">Data Semester Genap 2024/2026</span>
            </div>

            <!-- If Empty Violations (Good student) -->
            <div v-if="selectedStudent.violations.length === 0" class="p-6 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
              <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h5 class="text-sm font-bold text-emerald-900 mt-2">Siswa Teladan • Bebas Pelanggaran</h5>
              <p class="text-xs text-emerald-700 mt-1 max-w-md mx-auto">
                Ananda {{ selectedStudent.name }} memiliki catatan disiplin sempurna tanpa poin pelanggaran. Pertahankan prestasi dan kedisiplinan ini!
              </p>
            </div>

            <!-- Violations Table / List -->
            <div v-else class="space-y-3">
              <div
                v-for="(v, vIdx) in selectedStudent.violations"
                :key="vIdx"
                class="p-4 rounded-2xl border border-gray-100 bg-white hover:border-blue-200 transition shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3"
              >
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center font-black text-xs shrink-0 mt-0.5">
                    +{{ v.points }}
                  </div>
                  <div>
                    <h5 class="text-xs sm:text-sm font-bold text-gray-900">{{ v.title }}</h5>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ v.note }}</p>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-[11px] text-gray-400">
                      <span>Tgl: {{ v.date }}</span>
                      <span>&bull;</span>
                      <span>Waktu: {{ v.time }}</span>
                      <span>&bull;</span>
                      <span>Pelapor: {{ v.reporter }}</span>
                    </div>
                  </div>
                </div>

                <!-- Status Badge -->
                <div class="self-start sm:self-center shrink-0">
                  <span
                    class="px-3 py-1 rounded-full text-[11px] font-bold border"
                    :class="v.status === 'Selesai Pembinaan' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
                  >
                    {{ v.status }}
                  </span>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

      <!-- If Search yields no result -->
      <div v-else-if="searchQuery && !selectedStudent" class="max-w-2xl mx-auto mt-6 p-8 bg-white rounded-3xl border border-gray-200 text-center">
        <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-500 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <h4 class="text-sm font-bold text-gray-900 mt-2">Siswa Tidak Ditemukan</h4>
        <p class="text-xs text-gray-500 mt-1">
          Tidak ditemukan siswa dengan kata kunci "{{ searchQuery }}". Pastikan penulisan nama lengkap atau nomor NISN sudah tepat.
        </p>
      </div>
    </section>

    <!-- ============================================================ -->
    <!-- 4. SECTION: EKSPLORASI KELAS & ROMBEL (SEMUA KELAS)           -->
    <!-- ============================================================ -->
    <section id="eksplor-kelas" class="py-14 sm:py-20 bg-white border-y border-gray-200/80 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
          <div>
            <span class="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-full text-xs font-extrabold uppercase tracking-wider">
              Direktori Rombel
            </span>
            <h2 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight mt-2">
              Eksplorasi Kelas &amp; Rombel Siswa
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
              Lihat susunan rombongan belajar, wali kelas pendamping, dan tingkat presensi per kelas.
            </p>
          </div>

          <!-- Tingkat Level Selector -->
          <div class="flex bg-gray-100 p-1 rounded-2xl self-start md:self-auto text-xs font-bold">
            <button
              v-for="lvl in ['Semua Tingkat', 'Kelas 10 (X)', 'Kelas 11 (XI)', 'Kelas 12 (XII)']"
              :key="lvl"
              @click="selectedLevelFilter = lvl"
              class="px-3.5 py-2 rounded-xl transition cursor-pointer"
              :class="selectedLevelFilter === lvl ? 'bg-white text-blue-600 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
            >
              {{ lvl }}
            </button>
          </div>
        </div>

        <!-- Jurusan Tabs Filter -->
        <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-hide">
          <button
            v-for="jur in ['Semua Jurusan', 'RPL', 'TSM', 'DKV', 'TKJ', 'AKL', 'OTKP']"
            :key="jur"
            @click="selectedJurusanFilter = jur"
            class="px-4 py-2 rounded-full text-xs font-bold shrink-0 transition cursor-pointer border"
            :class="selectedJurusanFilter === jur ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'"
          >
            {{ jur }}
          </button>
        </div>

        <!-- Class Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <div
            v-for="rom in filteredClasses"
            :key="rom.id"
            class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between group"
          >
            <div>
              <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                  {{ rom.jurusan }}
                </span>
                <span
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                  :class="rom.attendanceRate >= 95 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
                >
                  Presensi {{ rom.attendanceRate }}%
                </span>
              </div>

              <h3 class="text-lg font-black text-gray-900 group-hover:text-blue-600 transition">
                {{ rom.name }}
              </h3>

              <div class="mt-3 space-y-1.5 text-xs text-gray-500">
                <div class="flex items-center gap-1.5">
                  <span class="text-gray-400">Wali Kelas:</span>
                  <span class="font-bold text-gray-800 truncate">{{ rom.waliKelas }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="text-gray-400">Guru BK:</span>
                  <span class="font-medium text-gray-700 truncate">{{ rom.guruBK }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="text-gray-400">Jumlah Siswa:</span>
                  <span class="font-bold text-gray-800">{{ rom.studentCount }} Siswa</span>
                </div>
              </div>
            </div>

            <!-- Open Modal Button -->
            <button
              @click="openClassDetail(rom)"
              class="mt-5 w-full py-2.5 px-4 bg-gray-50 hover:bg-blue-600 hover:text-white text-gray-700 font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition border border-gray-200 hover:border-blue-600 cursor-pointer"
            >
              <span>Lihat Rekap Kelas</span>
              <span>&rarr;</span>
            </button>
          </div>
        </div>

      </div>
    </section>

    <!-- ============================================================ -->
    <!-- 5. SECTION: BUKU PANDUAN TATA TERTIB & BOBOT POIN            -->
    <!-- ============================================================ -->
    <section id="tata-tertib" class="py-14 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
      <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-extrabold uppercase tracking-wider">
          Transparansi Regulasi
        </span>
        <h2 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight mt-2">
          Pedoman Tata Tertib &amp; Bobot Poin Sanksi
        </h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-2">
          Setiap siswa SMK PGRI 05 Jember dibina untuk berkarakter unggul, disiplin, dan bertanggung jawab. Berikut adalah bobot poin sebagai rujukan transparansi bagi wali murid.
        </p>
      </div>

      <!-- Categories Tab -->
      <div class="flex justify-center mb-8">
        <div class="inline-flex bg-gray-100 p-1.5 rounded-2xl overflow-x-auto max-w-full text-xs font-bold">
          <button
            v-for="cat in ruleCategories"
            :key="cat.id"
            @click="activeRuleCat = cat.id"
            class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0"
            :class="activeRuleCat === cat.id ? 'bg-white text-blue-600 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>

      <!-- Rules Table Card -->
      <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-200 uppercase text-[11px]">
              <tr>
                <th class="py-3.5 px-5">Jenis Pelanggaran</th>
                <th class="py-3.5 px-4 text-center">Bobot Poin</th>
                <th class="py-3.5 px-5">Tindakan Pembinaan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(rule, rIdx) in activeRules" :key="rIdx" class="hover:bg-slate-50/70 transition">
                <td class="py-3.5 px-5 font-bold text-gray-800">
                  {{ rule.title }}
                </td>
                <td class="py-3.5 px-4 text-center">
                  <span class="px-2.5 py-1 bg-rose-50 text-rose-600 font-black rounded-lg border border-rose-100">
                    +{{ rule.points }}
                  </span>
                </td>
                <td class="py-3.5 px-5 text-gray-600 text-xs">
                  {{ rule.action }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ============================================================ -->
    <!-- 6. SECTION: LAYANAN KONTAK & KONSULTASI BK                   -->
    <!-- ============================================================ -->
    <section id="layanan-bk" class="py-14 sm:py-20 bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white px-4 sm:px-6 lg:px-8">
      <div class="max-w-5xl mx-auto rounded-3xl bg-white/5 border border-white/10 p-6 sm:p-10 backdrop-blur-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
          <div>
            <span class="px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-full text-xs font-extrabold uppercase tracking-wider">
              Layanan Bimbingan Konseling
            </span>
            <h3 class="text-2xl sm:text-3xl font-black mt-3 tracking-tight">
              Butuh Konsultasi atau Klarifikasi Catatan Siswa?
            </h3>
            <p class="text-xs sm:text-sm text-slate-300 mt-3 leading-relaxed">
              Guru Bimbingan dan Konseling SMK PGRI 05 Jember siap melayani orang tua/wali murid untuk berdiskusi, memberikan solusi, dan merancang pendampingan terbaik bagi putra/putri Anda.
            </p>

            <div class="mt-6 space-y-3 text-xs sm:text-sm">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-blue-600/30 border border-blue-400/30 flex items-center justify-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </span>
                <span>Ruang BK Gedung A Lt. 1, SMK PGRI 05 Jember</span>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-blue-600/30 border border-blue-400/30 flex items-center justify-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </span>
                <span>Senin – Jumat: 07.00 – 15.30 WIB</span>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-blue-600/30 border border-blue-400/30 flex items-center justify-center text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </span>
                <span>Hotline BK: +62 812-3456-7890 (Ibu Nurul H.)</span>
              </div>
            </div>
          </div>

          <!-- Quick Consultation Request Form -->
          <div class="bg-white/10 p-6 rounded-2xl border border-white/15">
            <h4 class="text-sm font-bold text-white mb-3">Ajukan Janji Temu Konseling Online</h4>
            <div class="space-y-3">
              <div>
                <label class="text-[11px] font-bold text-slate-300 block mb-1">Nama Orang Tua / Siswa</label>
                <input
                  v-model="consultForm.name"
                  type="text"
                  placeholder="Contoh: Bpk. Haryanto"
                  class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="text-[11px] font-bold text-slate-300 block mb-1">Nama Siswa &amp; Kelas</label>
                <input
                  v-model="consultForm.student"
                  type="text"
                  placeholder="Contoh: Dimas Arya Pratama (X RPL 1)"
                  class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="text-[11px] font-bold text-slate-300 block mb-1">Topik Konsultasi</label>
                <select
                  v-model="consultForm.topic"
                  class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option>Klarifikasi Catatan Pelanggaran / Poin</option>
                  <option>Konseling Kehadiran &amp; Motivasi Belajar</option>
                  <option>Permohonan Dispensasi Izin Khusus</option>
                  <option>Konsultasi Minat &amp; Karir Lanjutan</option>
                </select>
              </div>

              <button
                @click="sendConsultRequest"
                class="w-full mt-2 py-3 bg-blue-600 hover:bg-blue-500 font-bold text-xs rounded-xl transition cursor-pointer shadow-md"
              >
                Kirim Permohonan Konseling
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================ -->
    <!-- 7. FOOTER                                                    -->
    <!-- ============================================================ -->
    <footer class="bg-slate-950 text-slate-400 py-8 border-t border-slate-800 text-xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <img src="/logo-smk.png" alt="Logo" class="w-6 h-6 object-contain" />
          <span class="font-bold text-slate-200">SMK PGRI 05 JEMBER</span>
          <span>•</span>
          <span>BK Prima Portal Tamu</span>
        </div>
        <p class="text-center sm:text-right text-[11px]">
          &copy; 2026 BK Prima • Sistem Presensi Digital &amp; Kedisiplinan Konseling Sekolah.
        </p>
      </div>
    </footer>

    <!-- ============================================================ -->
    <!-- 8. MODAL DETAIL KELAS (ROMBEL INSPECTOR)                     -->
    <!-- ============================================================ -->
    <div
      v-if="activeClassModal"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-150"
      @click.self="activeClassModal = null"
    >
      <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 border border-gray-200 shadow-2xl relative max-h-[90vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-gray-100">
          <div>
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                {{ activeClassModal.jurusan }}
              </span>
              <h3 class="text-xl font-black text-gray-900">{{ activeClassModal.name }}</h3>
            </div>
            <p class="text-xs text-gray-500 mt-1">
              Wali Kelas: <strong>{{ activeClassModal.waliKelas }}</strong> • BK: <strong>{{ activeClassModal.guruBK }}</strong>
            </p>
          </div>
          <button
            @click="activeClassModal = null"
            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Class Quick Stats -->
        <div class="grid grid-cols-3 gap-3 py-4 border-b border-gray-100">
          <div class="bg-gray-50 p-3 rounded-2xl text-center">
            <span class="text-[10px] font-bold text-gray-400 uppercase">Total Siswa</span>
            <span class="text-lg font-black text-gray-800 block">{{ activeClassModal.studentCount }}</span>
          </div>
          <div class="bg-emerald-50 p-3 rounded-2xl text-center">
            <span class="text-[10px] font-bold text-emerald-600 uppercase">Presensi Rata-rata</span>
            <span class="text-lg font-black text-emerald-600 block">{{ activeClassModal.attendanceRate }}%</span>
          </div>
          <div class="bg-blue-50 p-3 rounded-2xl text-center">
            <span class="text-[10px] font-bold text-blue-600 uppercase">Kedisiplinan</span>
            <span class="text-xs font-black text-blue-700 block mt-1">Sangat Baik</span>
          </div>
        </div>

        <!-- Student List in Class -->
        <div class="flex-1 overflow-y-auto py-4 space-y-2">
          <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Daftar Siswa Kelas Ini:</h4>
          
          <div
            v-for="(st, idx) in activeClassModal.students"
            :key="idx"
            class="flex items-center justify-between p-3 rounded-xl bg-gray-50/80 hover:bg-blue-50 transition border border-gray-100 text-xs"
          >
            <div class="flex items-center gap-3">
              <span class="w-6 text-gray-400 font-bold text-center">{{ idx + 1 }}</span>
              <div>
                <span class="font-bold text-gray-900 block">{{ st.name }}</span>
                <span class="text-[11px] text-gray-400">NISN: {{ st.nisn }}</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <span
                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                :class="st.points === 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
              >
                {{ st.points === 0 ? '0 Poin (Tertib)' : `${st.points} Poin Sanksi` }}
              </span>
              <button
                @click="checkFromModal(st.nisn)"
                class="px-2.5 py-1 bg-white hover:bg-blue-600 hover:text-white border border-gray-200 rounded-lg text-[11px] font-bold transition cursor-pointer"
              >
                Cek Rincian
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="pt-4 border-t border-gray-100 flex justify-end">
          <button
            @click="activeClassModal = null"
            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition cursor-pointer"
          >
            Tutup
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  searchedStudent: {
    type: Object,
    default: null
  },
  nisnQuery: {
    type: String,
    default: ''
  }
})

const searchQuery = ref('')
const selectedLevelFilter = ref('Semua Tingkat')
const selectedJurusanFilter = ref('Semua Jurusan')
const activeRuleCat = ref('terlambat')
const activeClassModal = ref(null)

onMounted(() => {
  if (props.nisnQuery) {
    searchQuery.value = props.nisnQuery
  }
  if (props.searchedStudent) {
    const s = props.searchedStudent
    const formatted = {
      name: s.name,
      nisn: s.nisn,
      class: s.class_name,
      jurusan: s.major || 'RPL',
      points: s.total_violation_points || 0,
      waliKelas: 'Bpk. Guru Wali Kelas',
      guruBK: 'Dra. Hj. Nurul Hidayati',
      avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
      violations: (s.violations || []).map(v => ({
        title: v.violation_name || v.category,
        note: v.notes || 'Tercatat dalam rekaman kedisiplinan BK',
        date: v.date,
        time: v.time ? `${v.time} WIB` : '07:30 WIB',
        points: v.points,
        reporter: v.reporter || 'Guru Piket / BK',
        status: v.status || 'Dalam Pembinaan'
      }))
    }
    studentsDb.unshift(formatted)
    searchQuery.value = s.nisn
  }
})

const executeSearch = () => {
  if (!searchQuery.value) return
  const q = searchQuery.value.trim()
  if (/^\d+$/.test(q)) {
    router.get('/tamu', { nisn: q }, { preserveState: true })
  }
}

const consultForm = ref({
  name: '',
  student: '',
  topic: 'Klarifikasi Catatan Pelanggaran / Poin'
})

// Demo Quick Chips
const demoChips = [
  { name: 'Muhammad Rizky', class: 'XI RPL 2', nisn: '22231084' },
  { name: 'Dimas Arya', class: 'X RPL 1', nisn: '00729183' },
  { name: 'Rania Zahra', class: 'X RPL 1', nisn: '00729185' },
  { name: 'Ahmad Fauzi (Teladan)', class: 'X RPL 1', nisn: '00729182' }
]

// Mock Students Database
const studentsDb = [
  {
    name: 'Muhammad Rizky Pratama',
    nisn: '22231084',
    class: 'XI RPL 2',
    jurusan: 'RPL',
    points: 35,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        title: 'Meninggalkan Kelas Tanpa Izin Pada Jam Pelajaran',
        note: 'Ditemukan di area kantin belakang saat jam Produktif RPL berlangsung.',
        date: '10 Okt 2024',
        time: '10.15 WIB',
        points: 20,
        reporter: 'Bpk. Hendra Gunawan (Piket)',
        status: 'Dalam Pembinaan'
      },
      {
        title: 'Terlambat Masuk Sekolah Lebih Dari 20 Menit',
        note: 'Tiba di gerbang sekolah pukul 07.24 WIB karena ban sepeda motor bocor.',
        date: '24 Okt 2024',
        time: '07.24 WIB',
        points: 10,
        reporter: 'Tim Satgas Disiplin Siswa',
        status: 'Selesai Pembinaan'
      },
      {
        title: 'Atribut Seragam Tidak Lengkap (Tanpa Dasi & Sabuk)',
        note: 'Pemeriksaan saat apel pagi hari Senin.',
        date: '18 Okt 2024',
        time: '06.55 WIB',
        points: 5,
        reporter: 'Ibu Anita Rahayu, M.Pd',
        status: 'Selesai Pembinaan'
      }
    ]
  },
  {
    name: 'Dimas Arya Pratama',
    nisn: '00729183',
    class: 'X RPL 1',
    jurusan: 'RPL',
    points: 15,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        title: 'Terlambat Masuk Sekolah 24 Menit',
        note: 'Tiba pukul 07.24 WIB. Dilakukan pembinaan piket dan pembersihan perpustakaan.',
        date: '24 Okt 2024',
        time: '07.24 WIB',
        points: 10,
        reporter: 'Guru Piket',
        status: 'Selesai Pembinaan'
      },
      {
        title: 'Sepatu Tidak Sesuai Ketentuan (Sepatu Putih)',
        note: 'Hari Senin wajib mengenakan sepatu hitam bertali.',
        date: '12 Okt 2024',
        time: '07.00 WIB',
        points: 5,
        reporter: 'Tim Kedisiplinan',
        status: 'Selesai Pembinaan'
      }
    ]
  },
  {
    name: 'Rania Zahra Putri',
    nisn: '00729185',
    class: 'X RPL 1',
    jurusan: 'RPL',
    points: 10,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        title: 'Terlambat Masuk Jam Pelajaran Pertama (15 Menit)',
        note: 'Keterlambatan tiba di sekolah pukul 07.15 WIB.',
        date: '24 Okt 2024',
        time: '07.15 WIB',
        points: 10,
        reporter: 'Guru Piket',
        status: 'Selesai Pembinaan'
      }
    ]
  },
  {
    name: 'Ahmad Fauzi',
    nisn: '00729182',
    class: 'X RPL 1',
    jurusan: 'RPL',
    points: 0,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Fathir Rizky Maulana',
    nisn: '00729184',
    class: 'X RPL 1',
    jurusan: 'RPL',
    points: 0,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Siti Nurhaliza',
    nisn: '00729186',
    class: 'X RPL 1',
    jurusan: 'RPL',
    points: 0,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
    violations: []
  }
]

// Mock Classes Database
const classesDb = [
  {
    id: 1,
    name: 'X RPL 1',
    level: 'Kelas 10 (X)',
    jurusan: 'RPL',
    studentCount: 36,
    attendanceRate: 94.4,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    students: [
      { name: 'Ahmad Fauzi', nisn: '00729182', points: 0 },
      { name: 'Dimas Arya Pratama', nisn: '00729183', points: 15 },
      { name: 'Fathir Rizky Maulana', nisn: '00729184', points: 0 },
      { name: 'Rania Zahra Putri', nisn: '00729185', points: 10 },
      { name: 'Siti Nurhaliza', nisn: '00729186', points: 0 },
      { name: 'Yusuf Maulana Al-Farizi', nisn: '00729187', points: 5 },
      { name: 'Zulfikar Ali Akbar', nisn: '00729188', points: 0 }
    ]
  },
  {
    id: 2,
    name: 'X RPL 2',
    level: 'Kelas 10 (X)',
    jurusan: 'RPL',
    studentCount: 36,
    attendanceRate: 97.2,
    waliKelas: 'Ibu Anita Rahayu, M.Pd',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    students: [
      { name: 'Bima Satria', nisn: '00729190', points: 0 },
      { name: 'Citra Dewi Permata', nisn: '00729191', points: 5 },
      { name: 'Doni Firmansyah', nisn: '00729192', points: 0 }
    ]
  },
  {
    id: 3,
    name: 'X DKV 1',
    level: 'Kelas 10 (X)',
    jurusan: 'DKV',
    studentCount: 36,
    attendanceRate: 95.8,
    waliKelas: 'Ibu Rina Melati, S.Pd',
    guruBK: 'Bpk. Danang Prasetyo, S.Pd',
    students: [
      { name: 'Alif Kurniawan', nisn: '00729201', points: 0 },
      { name: 'Bella Anggraini', nisn: '00729202', points: 10 }
    ]
  },
  {
    id: 4,
    name: 'X TSM 1',
    level: 'Kelas 10 (X)',
    jurusan: 'TSM',
    studentCount: 34,
    attendanceRate: 92.5,
    waliKelas: 'Bpk. Joko Susilo, S.T',
    guruBK: 'Bpk. Danang Prasetyo, S.Pd',
    students: [
      { name: 'Aditya Pratama', nisn: '00729210', points: 15 },
      { name: 'Bagus Sadewo', nisn: '00729211', points: 5 }
    ]
  },
  {
    id: 5,
    name: 'XI RPL 1',
    level: 'Kelas 11 (XI)',
    jurusan: 'RPL',
    studentCount: 36,
    attendanceRate: 96.5,
    waliKelas: 'Bpk. Fajar Ramadhan, M.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    students: [
      { name: 'Farhan Maulana', nisn: '22231070', points: 0 },
      { name: 'Galih Ramadhan', nisn: '22231071', points: 0 }
    ]
  },
  {
    id: 6,
    name: 'XI RPL 2',
    level: 'Kelas 11 (XI)',
    jurusan: 'RPL',
    studentCount: 36,
    attendanceRate: 93.8,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    students: [
      { name: 'Muhammad Rizky Pratama', nisn: '22231084', points: 35 },
      { name: 'Naufal Ghani', nisn: '22231085', points: 5 }
    ]
  },
  {
    id: 7,
    name: 'XI DKV 1',
    level: 'Kelas 11 (XI)',
    jurusan: 'DKV',
    studentCount: 35,
    attendanceRate: 95.0,
    waliKelas: 'Ibu Maya Sartika, S.Sn',
    guruBK: 'Ibu Rina Melati, S.Pd',
    students: [
      { name: 'Kayla Salsabila', nisn: '22231101', points: 0 }
    ]
  },
  {
    id: 8,
    name: 'XII RPL 1',
    level: 'Kelas 12 (XII)',
    jurusan: 'RPL',
    studentCount: 34,
    attendanceRate: 98.4,
    waliKelas: 'Bpk. Budi Santoso, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    students: [
      { name: 'Reza Pahlevi', nisn: '21221001', points: 0 },
      { name: 'Tiara Andini', nisn: '21221002', points: 0 }
    ]
  }
]

// Rules Categories and items
const ruleCategories = [
  { id: 'terlambat', name: 'Keterlambatan & Presensi' },
  { id: 'seragam', name: 'Seragam & Kerapian' },
  { id: 'perilaku', name: 'Sikap & Karakter' },
  { id: 'berat', name: 'Larangan Khusus' }
]

const rulesDb = {
  terlambat: [
    { title: 'Terlambat hadir di sekolah (1 – 15 Menit)', points: 5, action: 'Peringatan Lisan & Pengarahan Guru Piket' },
    { title: 'Terlambat hadir di sekolah (> 15 Menit)', points: 10, action: 'Tugas Pembinaan Edukatif di Lingkungan Sekolah' },
    { title: 'Meninggalkan jam pelajaran tanpa izin (Bolos Jam)', points: 15, action: 'Konseling Khusus Guru BK & Teguran Tertulis' },
    { title: 'Tidak masuk tanpa keterangan (Alpa) > 3 Hari Berturut', points: 25, action: 'Surat Panggilan Orang Tua / Wali Murid Tahap I' }
  ],
  seragam: [
    { title: 'Seragam tidak lengkap atribut (Dasi, Logo, Sabuk)', points: 5, action: 'Teguran & Peminjaman Atribut Cadangan BK' },
    { title: 'Memakai sepatu dan kaos kaki tidak sesuai aturan hari', points: 5, action: 'Peringatan & Catatan Disiplin' },
    { title: 'Rambut tidak rapi bagi putra / mewarnai rambut', points: 10, action: 'Batas Waktu Perapian 2 Hari Kerja' }
  ],
  perilaku: [
    { title: 'Bermain game / gawai saat jam KBM tanpa instruksi guru', points: 10, action: 'Penyimpanan Gawai Sementara & Pengambilan oleh Ortu' },
    { title: 'Tidak mematuhi instruksi guru / bersikap kurang sopan', points: 15, action: 'Konseling Pembinaan Sikap & Permintaan Maaf' },
    { title: 'Merusak fasilitas sekolah atau sarana kelas', points: 25, action: 'Mengganti Kerusakan & Pembinaan Khusus' }
  ],
  berat: [
    { title: 'Membawa / merokok / rokok elektrik (vape) di sekolah', points: 30, action: 'Panggilan Orang Tua & Surat Peringatan I' },
    { title: 'Terlibat perkelahian / tawuran antar pelajar', points: 50, action: 'Surat Peringatan Keras & Skorsing Edukatif' },
    { title: 'Membawa sajam atau narkotika', points: 100, action: 'Penyerahan Kembali kepada Orang Tua / Pihak Berwajib' }
  ]
}

const activeRules = computed(() => {
  return rulesDb[activeRuleCat.value] || []
})

// Search Student Calculation
const selectedStudent = computed(() => {
  if (!searchQuery.value) return null
  const q = searchQuery.value.trim().toLowerCase()
  return studentsDb.find(s => s.name.toLowerCase().includes(q) || s.nisn.includes(q)) || null
})

// Filtered Classes calculation
const filteredClasses = computed(() => {
  return classesDb.filter(c => {
    const matchLevel = selectedLevelFilter.value === 'Semua Tingkat' || c.level === selectedLevelFilter.value
    const matchJurusan = selectedJurusanFilter.value === 'Semua Jurusan' || c.jurusan === selectedJurusanFilter.value
    return matchLevel && matchJurusan
  })
})

const selectStudentByNisn = (nisn) => {
  searchQuery.value = nisn
  const el = document.getElementById('cek-pelanggaran')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}

const checkFromModal = (nisn) => {
  activeClassModal.value = null
  selectStudentByNisn(nisn)
}

const openClassDetail = (rombel) => {
  activeClassModal.value = rombel
}

const statusBadgeClass = (points) => {
  if (points === 0) return 'bg-emerald-500/20 text-emerald-300 border-emerald-400/40'
  if (points <= 20) return 'bg-blue-500/20 text-blue-300 border-blue-400/40'
  if (points <= 40) return 'bg-amber-500/20 text-amber-300 border-amber-400/40'
  return 'bg-rose-500/20 text-rose-300 border-rose-400/40'
}

const statusLabel = (points) => {
  if (points === 0) return 'Disiplin Teladan'
  if (points <= 20) return 'Status Baik'
  if (points <= 40) return 'Perhatian BK'
  return 'Peringatan Keras'
}

const pointsColorClass = (points) => {
  if (points === 0) return 'text-emerald-600'
  if (points <= 20) return 'text-blue-600'
  if (points <= 40) return 'text-amber-600'
  return 'text-rose-600'
}

const actionCategory = (points) => {
  if (points === 0) return 'Pertahankan Kedisiplinan'
  if (points <= 20) return 'Pembinaan Ringan Wali Kelas'
  if (points <= 40) return 'Peringatan Tahap 1 • Panggilan Ortu'
  return 'Tindakan Disiplin Serius'
}

const progressBarClass = (points) => {
  if (points <= 20) return 'bg-emerald-500'
  if (points <= 40) return 'bg-amber-500'
  return 'bg-rose-600'
}

const openWhatsApp = (student) => {
  const text = encodeURIComponent(`Halo Ibu Guru BK SMK PGRI 05 Jember, saya wali murid dari ananda ${student.name} (${student.class}, NISN: ${student.nisn}). Saya ingin berkonsultasi mengenai catatan perkembangan dan kedisiplinan anak saya. Terima kasih.`)
  window.open(`https://wa.me/6281234567890?text=${text}`, '_blank')
}

const sendConsultRequest = () => {
  if (!consultForm.value.name || !consultForm.value.student) {
    alert('Mohon isi nama orang tua dan nama siswa terlebih dahulu.')
    return
  }
  alert(`Terima kasih Bapak/Ibu ${consultForm.value.name}. Permohonan konsultasi untuk ananda ${consultForm.value.student} mengenai "${consultForm.value.topic}" telah diteruskan ke Ruang BK. Guru BK akan menghubungi Anda dalam waktu 1x24 jam kerja.`)
  consultForm.value.name = ''
  consultForm.value.student = ''
}
</script>

<style scoped>
@keyframes bounceSubtle {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-4px);
  }
}
.animate-bounce-subtle {
  animation: bounceSubtle 3s infinite ease-in-out;
}
</style>
