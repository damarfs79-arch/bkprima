<template>
  <Head title="BK Prima - Layanan Informasi Kehadiran & Kedisiplinan Siswa" />
  <div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- ============================================================ -->
    <!-- 1. DESKTOP SIDEBAR (Minimalist White, Clean Borders)         -->
    <!-- ============================================================ -->
    <aside class="hidden lg:flex w-64 xl:w-72 flex-col bg-white border-r border-slate-200 min-h-screen sticky top-0 h-screen shrink-0 z-30 select-none">
      
      <!-- Brand Logo Header (Foto 1) -->
      <div class="p-4 sm:p-5 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <img
            src="/logo-bkprima.png"
            alt="BK Prima - Sistem Informasi &amp; Konseling SMK PGRI 5 Jember"
            class="h-10 sm:h-11 w-auto max-w-full object-contain"
          />
        </div>
      </div>

      <!-- Navigation Menu List -->
      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <p class="px-3 text-xs font-semibold text-slate-400 mb-2">Menu Portal</p>
        
        <button
          v-for="(item, idx) in navSections"
          :key="item.id"
          @click="scrollToSection(item.id, idx)"
          class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium transition text-left cursor-pointer"
          :class="activeNavIndex === idx ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
        >
          <div
            class="transition"
            :class="activeNavIndex === idx ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700'"
          >
            <component :is="item.icon" class="w-4 h-4" />
          </div>
          <span class="flex-1">{{ item.label }}</span>
          <span
            v-if="item.id === 'section-pelanggaran'"
            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200"
          >
            {{ violatingStudents.length }}
          </span>
        </button>
      </nav>

      <!-- Info Operasional BK di Sidebar -->
      <div class="mx-3 mb-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Layanan Tatap Muka</span>
        </div>
        <p class="text-[11px] text-slate-500 mt-1 leading-snug">
          Senin – Jumat (07.00 – 15.30 WIB)<br />Ruang BK Gedung Utama Lt. 1
        </p>
      </div>

      <!-- Sidebar Footer (Akses Guru BK) -->
      <div class="p-4 border-t border-slate-100 space-y-2">
        <div class="px-3 py-2 bg-slate-50 rounded-xl text-[11px] text-slate-500 font-medium">
          Portal Informasi Publik &amp; Wali Murid
        </div>
        <router-link
          to="/login"
          class="flex items-center justify-center gap-2 w-full py-2.5 px-3 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
          </svg>
          <span>Masuk Guru BK</span>
        </router-link>
      </div>

    </aside>

    <!-- ============================================================ -->
    <!-- 2. MOBILE TOP BAR                                            -->
    <!-- ============================================================ -->
    <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 sticky top-0 z-30 flex items-center justify-between">
      
      <!-- Left: Hamburger & Brand -->
      <div class="flex items-center gap-2.5">
        <button
          @click="mobileDrawerOpen = true"
          class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex flex-col items-center justify-center gap-1 transition active:scale-95 cursor-pointer"
          title="Buka Menu"
        >
          <span class="w-4 h-[2px] bg-slate-700 rounded-full"></span>
          <span class="w-4 h-[2px] bg-slate-700 rounded-full"></span>
          <span class="w-4 h-[2px] bg-slate-700 rounded-full"></span>
        </button>

        <div class="flex items-center">
          <img src="/logo-bkprima.png" alt="BK Prima SMK PGRI 5 Jember" class="h-8 w-auto object-contain" />
        </div>
      </div>

      <!-- Right: Link Login Guru BK -->
      <router-link
        to="/login"
        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg flex items-center gap-1.5 transition"
      >
        <span>Guru BK</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        </svg>
      </router-link>

    </div>

    <!-- ============================================================ -->
    <!-- 3. MOBILE DRAWER OVERLAY                                     -->
    <!-- ============================================================ -->
    <div
      v-if="mobileDrawerOpen"
      class="lg:hidden fixed inset-0 z-50 bg-slate-900/50 flex"
      @click.self="mobileDrawerOpen = false"
    >
      <div class="w-72 bg-white h-full flex flex-col p-5 shadow-xl animate-in slide-in-from-left duration-200">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <img src="/logo-smk.png" alt="Logo" class="w-7 h-7 object-contain" />
            <div>
              <h4 class="font-bold text-slate-900 text-sm">Portal Informasi Siswa</h4>
              <p class="text-[10px] text-slate-400 font-semibold uppercase">SMK PGRI 05 JEMBER</p>
            </div>
          </div>
          <button
            @click="mobileDrawerOpen = false"
            class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <nav class="flex-1 py-4 space-y-1">
          <button
            v-for="(item, idx) in navSections"
            :key="item.id"
            @click="scrollToSection(item.id, idx); mobileDrawerOpen = false"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-left transition cursor-pointer"
            :class="activeNavIndex === idx ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50'"
          >
            <component :is="item.icon" class="w-4 h-4" />
            <span class="flex-1">{{ item.label }}</span>
          </button>
        </nav>

        <div class="pt-3 border-t border-slate-100">
          <router-link
            to="/login"
            class="flex items-center justify-center gap-2 w-full py-2.5 bg-slate-800 text-white font-bold text-xs rounded-xl"
          >
            <span>Masuk Guru BK</span>
          </router-link>
        </div>

      </div>
    </div>

    <!-- ============================================================ -->
    <!-- 4. MAIN CONTENT AREA                                         -->
    <!-- ============================================================ -->
    <div class="flex-1 flex flex-col min-w-0 pb-28 lg:pb-12 overflow-x-hidden">

      <!-- ============================================================ -->
      <!-- A. HERO SECTION: 2 COLUMNS (BALANCED NATURAL LAYOUT)         -->
      <!-- ============================================================ -->
      <section id="section-beranda" class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="max-w-6xl mx-auto">
          
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- LEFT COLUMN: Headline, Deskripsi Santun, & Form Pencarian Terpadu -->
            <div class="lg:col-span-7 flex flex-col justify-center">
              
              <!-- Eyebrow Text -->
              <p class="text-xs font-semibold text-blue-700 mb-2">
                Layanan Informasi Siswa Resmi &bull; SMK PGRI 05 Jember
              </p>

              <!-- Judul Utama -->
              <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 tracking-tight leading-tight">
                Layanan Informasi Kehadiran &amp; Kedisiplinan Siswa
              </h1>

              <!-- Deskripsi Ramah untuk Wali Murid -->
              <p class="mt-3.5 text-xs sm:text-sm lg:text-base text-slate-600 leading-relaxed max-w-xl font-normal">
                Portal terpadu bagi orang tua/wali murid dan siswa SMK PGRI 05 Jember untuk memantau catatan kehadiran harian, kepatuhan tata tertib, serta transparansi dokumentasi pembinaan secara terpercaya.
              </p>

              <!-- Form Pencarian NISN / Nama Siswa Terpadu -->
              <div class="mt-6 bg-slate-50 p-2 sm:p-2.5 rounded-2xl border border-slate-200 shadow-xs max-w-xl">
                <div class="flex items-center gap-2">
                  <div class="relative flex-1">
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Ketik Nama Siswa atau NISN..."
                      class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-12 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-900 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                      @keyup.enter="executeSearch"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <button
                      v-if="searchQuery"
                      @click="searchQuery = ''"
                      type="button"
                      class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-semibold p-1"
                      title="Hapus teks"
                    >
                      Batal
                    </button>
                  </div>

                  <button
                    @click="executeSearch"
                    type="button"
                    class="interactive-btn px-4 sm:px-5 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-xs sm:text-sm rounded-xl shadow-xs hover:shadow transition-all cursor-pointer shrink-0"
                  >
                    Cari Siswa
                  </button>
                </div>

                <!-- Akses Cepat & Contoh Siswa -->
                <div class="mt-2.5 px-1.5 flex flex-wrap items-center gap-1.5 text-xs">
                  <span class="text-slate-400 text-[11px] font-medium">Contoh siswa:</span>
                  <button
                    v-for="chip in demoChips"
                    :key="chip.nisn"
                    @click="selectStudentByNisn(chip.nisn)"
                    type="button"
                    class="interactive-badge px-2.5 py-1 rounded-lg bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-slate-600 font-medium text-[11px] border border-slate-200 cursor-pointer"
                  >
                    {{ chip.name }}
                  </button>
                </div>
              </div>

              <!-- Tombol Shortcut Langsung ke Siswa Melanggar -->
              <div class="mt-4 flex flex-wrap items-center gap-2">
                <button
                  @click="scrollToSection('section-pelanggaran', 1)"
                  type="button"
                  class="interactive-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 text-xs font-medium border border-rose-200 hover:border-rose-300 cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <span>Lihat Daftar Siswa Melanggar ({{ violatingStudents.length }} Siswa)</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
              </div>

            </div>

            <!-- RIGHT COLUMN: Foto Asli Gedung & Ringkasan Siswa -->
            <div class="lg:col-span-5">
              <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 shadow-xs">
                
                <div class="relative rounded-xl overflow-hidden aspect-[16/10] bg-slate-200 shadow-2xs">
                  <img
                    src="/school-campus.jpg"
                    alt="Aktivitas Lingkungan Kampus SMK PGRI 05 Jember"
                    class="w-full h-full object-cover"
                  />
                  
                  <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex items-end p-3.5">
                    <div class="text-white">
                      <p class="text-xs font-bold text-white leading-tight">Kampus SMK PGRI 05 Jember</p>
                      <p class="text-[11px] text-slate-200 mt-0.5">Pendampingan Karakter &amp; Layanan Bimbingan Konseling Siswa</p>
                    </div>
                  </div>
                </div>

                <!-- 2 Kartu Info Ringkas di Bawah Foto -->
                <div class="grid grid-cols-2 gap-2 mt-3">
                  <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                    <div class="flex items-center gap-1.5 text-blue-600 mb-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <span class="text-xs font-medium text-slate-500">Rombel</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-800">36 Kelas Aktif</p>
                    <p class="text-[11px] text-slate-500">7 Jurusan Keahlian</p>
                  </div>

                  <button
                    @click="scrollToSection('section-pelanggaran', 1)"
                    type="button"
                    class="bg-white hover:bg-rose-50/60 p-2.5 rounded-xl border border-slate-200/80 hover:border-rose-200 text-left transition cursor-pointer"
                  >
                    <div class="flex items-center gap-1.5 text-rose-600 mb-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                      <span class="text-xs font-medium text-slate-500">Kedisiplinan</span>
                    </div>
                    <p class="text-xs font-semibold text-rose-700">{{ violatingStudents.length }} Siswa Melanggar</p>
                    <p class="text-[11px] text-slate-500">Buka Daftar &rarr;</p>
                  </button>
                </div>

              </div>
            </div>

          </div>

        </div>
      </section>

      <!-- ============================================================ -->
      <!-- B. SECTION: DAFTAR SISWA MELANGGAR (CEK CEPAT & NYAMAN)      -->
      <!-- ============================================================ -->
      <section id="section-pelanggaran" class="bg-slate-50/75 border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="max-w-6xl mx-auto">
          
          <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
              <p class="text-xs font-semibold text-rose-600 mb-1">
                Transparansi Pembinaan &bull; Kedisiplinan Siswa
              </p>
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
                Daftar Siswa dengan Catatan Pelanggaran
              </h2>
              <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Data siswa yang memiliki akumulasi poin pelanggaran tata tertib dan memerlukan pendampingan bersama orang tua.
              </p>
            </div>

            <!-- Quick Counter Summary -->
            <div class="flex items-center gap-2 self-start md:self-auto flex-wrap">
              <span class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-700 font-medium shadow-2xs">
                <span>{{ violatingStudents.length }} Siswa Tercatat</span>
              </span>
              <span class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-700 font-medium shadow-2xs">
                <span>{{ violatingStudentsWithPhotosCount }} Ada Bukti Foto</span>
              </span>
            </div>
          </div>

          <!-- Controls: Tabs, Level Filter, Search -->
          <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200 mb-6 space-y-3">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
              <!-- Filter Tabs -->
              <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <button
                  type="button"
                  @click="violationFilterTab = 'semua'"
                  class="interactive-badge px-3 py-1.5 rounded-lg font-medium cursor-pointer"
                  :class="violationFilterTab === 'semua' ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                >
                  Semua ({{ violatingStudents.length }})
                </button>
                <button
                  type="button"
                  @click="violationFilterTab = 'perhatian'"
                  class="interactive-badge px-3 py-1.5 rounded-lg font-medium cursor-pointer"
                  :class="violationFilterTab === 'perhatian' ? 'bg-rose-600 text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                >
                  Poin &ge; 20 ({{ violatingStudentsHighPointsCount }})
                </button>
                <button
                  type="button"
                  @click="violationFilterTab = 'pembinaan'"
                  class="interactive-badge px-3 py-1.5 rounded-lg font-medium cursor-pointer"
                  :class="violationFilterTab === 'pembinaan' ? 'bg-amber-600 text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                >
                  Dalam Pembinaan
                </button>
                <button
                  type="button"
                  @click="violationFilterTab = 'selesai'"
                  class="interactive-badge px-3 py-1.5 rounded-lg font-medium cursor-pointer"
                  :class="violationFilterTab === 'selesai' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                >
                  Selesai Pembinaan
                </button>
                <button
                  type="button"
                  @click="violationFilterTab = 'foto'"
                  class="interactive-badge px-3 py-1.5 rounded-lg font-medium cursor-pointer"
                  :class="violationFilterTab === 'foto' ? 'bg-blue-600 text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                >
                  Ada Bukti Foto
                </button>
              </div>

              <!-- Filter Tingkat Kelas -->
              <div class="flex items-center gap-2">
                <select
                  v-model="violationLevelFilter"
                  class="bg-white hover:border-slate-300 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-700 focus:outline-none focus:border-slate-400 cursor-pointer"
                >
                  <option value="Semua Tingkat">Semua Tingkat</option>
                  <option value="Kelas 10">Kelas 10 (X)</option>
                  <option value="Kelas 11">Kelas 11 (XI)</option>
                  <option value="Kelas 12">Kelas 12 (XII)</option>
                </select>
              </div>
            </div>

            <!-- Search input within violations -->
            <div class="relative">
              <input
                v-model="violationSearchQuery"
                type="text"
                placeholder="Cari nama siswa, NISN, atau kelas siswa yang melanggar..."
                class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-8 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition"
              />
              <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Grid of Violating Students Cards -->
          <div v-if="filteredViolatingStudents.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="st in filteredViolatingStudents"
              :key="st.nisn"
              class="interactive-card bg-white rounded-xl border border-slate-200 hover:border-slate-300 p-4 sm:p-5 flex flex-col justify-between shadow-2xs group"
            >
              <div>
                <!-- Header Card: Avatar, Name, Points Badge -->
                <div class="flex items-start justify-between gap-3 mb-3">
                  <div class="flex items-center gap-3">
                    <img
                      :src="st.avatar"
                      :alt="st.name"
                      class="w-11 h-11 rounded-lg object-cover border border-slate-200 shrink-0"
                    />
                    <div>
                      <h3 class="font-semibold text-slate-900 text-sm leading-snug line-clamp-1">{{ st.name }}</h3>
                      <p class="text-xs text-slate-500 font-mono mt-0.5">NISN: {{ st.nisn }} &bull; {{ st.class }}</p>
                    </div>
                  </div>

                  <!-- Poin Badge -->
                  <span
                    class="text-xs font-semibold shrink-0"
                    :class="st.points >= 20 ? 'text-rose-600' : 'text-amber-600'"
                  >
                    {{ st.points }} Poin
                  </span>
                </div>

                <!-- Info Wali & BK -->
                <div class="text-[11px] text-slate-500 space-y-0.5 py-2 border-y border-slate-100 mb-3">
                  <div class="flex justify-between">
                    <span>Wali Kelas:</span>
                    <span class="text-slate-700 font-medium">{{ st.waliKelas }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Guru BK:</span>
                    <span class="text-slate-700 font-medium">{{ st.guruBK }}</span>
                  </div>
                </div>

                <!-- Pelanggaran Terakhir / Terberat -->
                <div class="space-y-1.5 mb-4">
                  <span class="text-[11px] font-medium text-slate-500 block">Catatan Pelanggaran Terakhir:</span>
                  <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                    <div class="flex items-center justify-between gap-2">
                      <span class="text-xs font-medium text-slate-800 line-clamp-1">
                        {{ st.violations && st.violations.length > 0 ? st.violations[0].title : 'Pelanggaran Tata Tertib' }}
                      </span>
                      <span
                        v-if="st.violations && st.violations.length > 0"
                        class="text-[10px] font-semibold px-1.5 py-0.2 rounded"
                        :class="st.violations[0].status === 'Selesai Pembinaan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                      >
                        {{ st.violations[0].status }}
                      </span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1">
                      <span>{{ st.violations && st.violations.length > 0 ? st.violations[0].date : '-' }}</span>
                      <span v-if="st.violations && st.violations.some(v => v.photoUrl)" class="inline-flex items-center gap-1 text-blue-600 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        <span>Foto Ada</span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tombol Aksi Buka Detail -->
              <div class="flex items-center gap-2 pt-1">
                <button
                  type="button"
                  @click="openViolatingStudentDetail(st)"
                  class="interactive-btn flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 active:bg-black text-white rounded-lg text-xs font-medium text-center cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <span>Lihat Riwayat &amp; Foto</span>
                </button>
                <button
                  type="button"
                  @click="openConsultationForStudent(st)"
                  class="interactive-btn p-2 bg-white hover:bg-emerald-50 active:bg-emerald-100 text-slate-600 hover:text-emerald-700 rounded-lg border border-slate-200 hover:border-emerald-200 cursor-pointer"
                  title="Konsultasikan Siswa ke BK"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                </button>
              </div>

            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-white rounded-xl border border-slate-200 p-8 text-center max-w-md mx-auto">
            <div class="w-10 h-10 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-xs font-semibold text-slate-800">Tidak ada data siswa yang cocok</p>
            <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau ganti filter kategori.</p>
          </div>

        </div>
      </section>

      <!-- ============================================================ -->
      <!-- C. SECTION: HASIL PENCARIAN & RIWAYAT FOTO SISWA             -->
      <!-- ============================================================ -->
      <section id="section-cari" class="px-4 sm:px-6 lg:px-8 py-8 sm:py-12 max-w-6xl mx-auto w-full">
        
        <!-- Result Card: Siswa Ditemukan -->
        <div v-if="selectedStudent" class="max-w-3xl mx-auto animate-in fade-in duration-200">
          <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            
            <!-- Profil Siswa -->
            <div class="p-5 sm:p-6 bg-slate-900 text-white">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                
                <div class="flex items-center gap-4">
                  <img
                    :src="selectedStudent.avatar"
                    :alt="selectedStudent.name"
                    class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl object-cover border border-slate-700 shrink-0"
                  />
                  <div>
                    <div class="flex items-center gap-2 flex-wrap">
                      <h3 class="text-base sm:text-xl font-bold text-white">{{ selectedStudent.name }}</h3>
                      <span
                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                        :class="statusBadgeClass(selectedStudent.points)"
                      >
                        {{ statusLabel(selectedStudent.points) }}
                      </span>
                    </div>
                    <p class="text-xs text-slate-300 font-medium mt-1">
                      NISN: <span class="font-bold text-white">{{ selectedStudent.nisn }}</span> • Kelas: <span class="font-bold text-white">{{ selectedStudent.class }}</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">
                      Wali Kelas: <span class="text-slate-200">{{ selectedStudent.waliKelas }}</span> • Guru BK: <span class="text-slate-200">{{ selectedStudent.guruBK }}</span>
                    </p>
                  </div>
                </div>

                <!-- Kontak Guru BK -->
                <button
                  @click="openWhatsApp(selectedStudent)"
                  class="self-start sm:self-auto px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs flex items-center gap-2 transition cursor-pointer shrink-0"
                >
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.22.771 1.394.858.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                  <span>Konsultasi BK</span>
                </button>

              </div>
            </div>

            <!-- Data Rombongan Belajar (Kelas) Info Strip -->
            <div class="px-5 sm:px-6 py-2.5 bg-slate-800 text-xs border-t border-slate-700/80 flex flex-wrap items-center justify-between gap-2.5 text-slate-300">
              <div class="flex items-center gap-1.5">
                <span class="text-slate-400">Rombel:</span>
                <span class="font-bold text-white">{{ selectedStudent.class }} ({{ selectedStudent.jurusanFull || selectedStudent.jurusan }})</span>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-slate-400">Wali Kelas: <strong class="text-white">{{ selectedStudent.waliKelas }}</strong></span>
                <span class="hidden sm:inline text-slate-600">•</span>
                <span class="text-slate-400">Guru BK: <strong class="text-white">{{ selectedStudent.guruBK }}</strong></span>
              </div>
            </div>

            <!-- Rekap Poin Kedisiplinan -->
            <div class="p-5 bg-slate-50 border-b border-slate-200">
              <div class="flex items-center justify-between mb-2">
                <div>
                  <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Akumulasi Poin Pelanggaran</span>
                  <div class="flex items-baseline gap-2 mt-0.5">
                    <span class="text-3xl sm:text-4xl font-extrabold" :class="pointsColorClass(selectedStudent.points)">
                      {{ selectedStudent.points }}
                    </span>
                    <span class="text-xs text-slate-400">/ 100 Poin Batas Pembinaan Intensif</span>
                  </div>
                </div>

                <div class="bg-white p-2.5 rounded-xl border border-slate-200 text-right">
                  <span class="text-[10px] font-semibold text-slate-400 uppercase block">Status Pembinaan</span>
                  <span class="text-xs font-bold" :class="pointsColorClass(selectedStudent.points)">
                    {{ actionCategory(selectedStudent.points) }}
                  </span>
                </div>
              </div>

              <!-- Bar Progres Poin -->
              <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden mt-2">
                <div
                  class="h-full rounded-full transition-all duration-300"
                  :class="progressBarClass(selectedStudent.points)"
                  :style="{ width: `${Math.min(100, Math.max(3, selectedStudent.points))}%` }"
                ></div>
              </div>
            </div>

            <!-- Daftar Riwayat Pelanggaran & Foto Kejadian -->
            <div class="p-5 sm:p-6 space-y-4">
              <div class="flex items-center justify-between">
                <h4 class="font-bold text-slate-900 text-sm">
                  Catatan Pembinaan &amp; Dokumentasi
                </h4>
                <span class="text-xs text-slate-500">
                  Total {{ selectedStudent.violations.length }} Kejadian
                </span>
              </div>

              <!-- Jika Siswa Teladan Bebas Pelanggaran -->
              <div v-if="selectedStudent.violations.length === 0" class="p-5 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                <div class="w-9 h-9 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <h5 class="text-xs font-bold text-emerald-900 mt-2">Catatan Disiplin Baik</h5>
                <p class="text-xs text-emerald-700 mt-0.5">
                  Ananda {{ selectedStudent.name }} tidak memiliki catatan pelanggaran. Terima kasih atas kedisiplinan yang baik.
                </p>
              </div>

              <!-- Daftar Item Pelanggaran -->
              <div v-else class="space-y-3">
                <div
                  v-for="(v, vIdx) in selectedStudent.violations"
                  :key="vIdx"
                  class="p-4 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition"
                >
                  <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    
                    <div class="flex items-start gap-3 flex-1">
                      <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                        +{{ v.points }}
                      </div>
                      <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                          <h5 class="text-xs sm:text-sm font-bold text-slate-900">{{ v.title }}</h5>
                          <span
                            class="px-2 py-0.5 rounded text-[10px] font-semibold"
                            :class="v.status === 'Selesai Pembinaan' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
                          >
                            {{ v.status }}
                          </span>
                        </div>

                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ v.note }}</p>

                        <!-- Informasi Tanggal & Pelapor (Ikon SVG) -->
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-[11px] text-slate-400">
                          <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ v.date }}</span>
                          </span>
                          <span>•</span>
                          <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ v.time }}</span>
                          </span>
                          <span>•</span>
                          <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Petugas: {{ v.reporter }}</span>
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Lampiran Bukti Foto -->
                    <div v-if="v.photoUrl" class="sm:w-32 shrink-0 mt-2 sm:mt-0">
                      <div
                        @click="openPhotoPreview(v)"
                        class="group relative rounded-xl overflow-hidden border border-slate-200 bg-slate-100 cursor-pointer hover:border-blue-400 transition"
                      >
                        <img
                          :src="v.photoUrl"
                          alt="Dokumentasi Pelanggaran"
                          class="w-full h-20 object-cover group-hover:scale-105 transition duration-300"
                        />
                        <div class="absolute inset-0 bg-slate-900/20 group-hover:bg-slate-900/10 transition flex items-end p-1">
                          <span class="bg-slate-900 text-white text-[9px] font-bold px-1.5 py-0.5 rounded flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Lihat Foto</span>
                          </span>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

            </div>

          </div>
        </div>

        <!-- Jika Data Tidak Ditemukan -->
        <div v-else-if="searchQuery && !selectedStudent" class="max-w-md mx-auto mt-4 p-5 bg-white rounded-2xl border border-slate-200 text-center">
          <p class="text-xs font-bold text-slate-800">Siswa tidak ditemukan</p>
          <p class="text-xs text-slate-500 mt-0.5">
            Pastikan penulisan nama atau nomor NISN "{{ searchQuery }}" sudah sesuai.
          </p>
        </div>

      </section>

      <!-- ============================================================ -->
      <!-- C. SECTION: DATA KELAS & WALI KELAS                          -->
      <!-- ============================================================ -->
      <section id="section-kelas" class="bg-white border-y border-slate-200 px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="max-w-6xl mx-auto">
          
          <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-blue-700 mb-1">
                Rombongan Belajar
              </p>
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
                Daftar Kelas &amp; Wali Kelas
              </h2>
              <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pilih kelas untuk melihat daftar siswa, wali kelas pendamping, dan tingkat kehadiran.
              </p>
            </div>

            <!-- Filter Tingkat -->
            <div class="flex bg-slate-100 p-1 rounded-xl text-xs font-semibold self-start md:self-auto">
              <button
                v-for="lvl in ['Semua Tingkat', 'Kelas 10 (X)', 'Kelas 11 (XI)', 'Kelas 12 (XII)']"
                :key="lvl"
                @click="selectedLevelFilter = lvl"
                class="px-3 py-1.5 rounded-lg transition cursor-pointer"
                :class="selectedLevelFilter === lvl ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
              >
                {{ lvl }}
              </button>
            </div>
          </div>

          <!-- Filter Jurusan -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-3 mb-6 scrollbar-hide text-xs font-semibold">
            <button
              v-for="jur in ['Semua Jurusan', 'RPL', 'TSM', 'DKV', 'TKJ', 'AKL', 'OTKP']"
              :key="jur"
              @click="selectedJurusanFilter = jur"
              class="px-3.5 py-1.5 rounded-lg shrink-0 transition cursor-pointer"
              :class="selectedJurusanFilter === jur ? 'bg-slate-800 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
            >
              {{ jur }}
            </button>
          </div>

          <!-- Grid Kartu Kelas -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div
              v-for="rom in filteredClasses"
              :key="rom.id"
              class="bg-slate-50 rounded-2xl p-4 border border-slate-200 hover:border-slate-300 transition flex flex-col justify-between"
            >
              <div>
                <!-- Tag Jurusan & Persentase Polos Tanpa Border -->
                <div class="flex items-center justify-between gap-2 mb-2">
                  <span class="text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                    {{ rom.jurusan }}
                  </span>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">
                    Presensi {{ rom.attendanceRate }}%
                  </span>
                </div>

                <h3 class="text-base font-bold text-slate-900 mt-1">
                  {{ rom.name }}
                </h3>

                <div class="mt-2.5 space-y-1 text-xs text-slate-500">
                  <div>
                    <span class="text-slate-400">Wali Kelas: </span>
                    <strong class="text-slate-700">{{ rom.waliKelas }}</strong>
                  </div>
                  <div>
                    <span class="text-slate-400">Guru BK: </span>
                    <span class="text-slate-600">{{ rom.guruBK }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400">Jumlah Siswa: </span>
                    <span class="text-slate-700 font-medium">{{ rom.studentCount }} Orang</span>
                  </div>
                </div>
              </div>

              <!-- Tombol Buka Detail Kelas -->
              <button
                @click="openClassDetail(rom)"
                class="mt-4 w-full py-2 px-3 bg-white hover:bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs flex items-center justify-center gap-1.5 transition border border-slate-200 cursor-pointer"
              >
                <span>Lihat Siswa Kelas</span>
                <span>&rarr;</span>
              </button>
            </div>
          </div>

        </div>
      </section>

      <!-- ============================================================ -->
      <!-- D. SECTION: TATA TERTIB & PEDOMAN POIN SANKSI                -->
      <!-- ============================================================ -->
      <section id="section-tata-tertib" class="px-4 sm:px-6 lg:px-8 py-10 sm:py-14 max-w-6xl mx-auto w-full">
        
        <div class="max-w-xl mx-auto mb-6 text-center">
          <p class="text-xs font-bold uppercase tracking-wider text-blue-700 mb-1">
            Pedoman Kedisiplinan
          </p>
          <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mt-2">
            Tata Tertib &amp; Bobot Poin Pelanggaran
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
            Panduan rujukan aturan sekolah bagi siswa dan orang tua untuk menciptakan lingkungan belajar yang tertib.
          </p>
        </div>

        <!-- Kategori Aturan -->
        <div class="flex justify-center mb-5">
          <div class="inline-flex bg-slate-100 p-1 rounded-xl text-xs font-semibold overflow-x-auto max-w-full">
            <button
              v-for="cat in ruleCategories"
              :key="cat.id"
              @click="activeRuleCat = cat.id"
              class="px-3.5 py-1.5 rounded-lg transition cursor-pointer shrink-0"
              :class="activeRuleCat === cat.id ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
            >
              {{ cat.name }}
            </button>
          </div>
        </div>

        <!-- Tabel Aturan -->
        <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
              <thead class="bg-slate-50/50 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                  <th class="py-3 px-4">Jenis Pelanggaran</th>
                  <th class="py-3 px-3 text-center">Poin</th>
                  <th class="py-3 px-4">Tindakan Pembinaan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(rule, rIdx) in activeRules" :key="rIdx" class="hover:bg-slate-50/60 transition">
                  <td class="py-3 px-4 font-semibold text-slate-800">
                    {{ rule.title }}
                  </td>
                  <td class="py-3 px-3 text-center">
                    <span class="text-xs font-semibold text-rose-600 font-mono">
                      +{{ rule.points }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-slate-600 text-xs">
                    {{ rule.action }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- ============================================================ -->
      <!-- E. SECTION: KONTAK & LAYANAN GURU BK                         -->
      <!-- ============================================================ -->
      <section id="section-kontak" class="bg-slate-900 text-white px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="max-w-5xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-blue-400 mb-1.5">
                Layanan Konseling
              </p>
              <h3 class="text-xl sm:text-2xl font-bold mt-2.5 tracking-tight text-white">
                Hubungi Guru Bimbingan Konseling
              </h3>
              <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                Ruang BK siap membantu Bapak/Ibu wali murid berdiskusi dan mencari solusi terbaik untuk pendampingan ananda di sekolah.
              </p>

              <!-- Kontak Info Bersih dengan Ikon SVG -->
              <div class="mt-5 space-y-2.5 text-xs text-slate-300">
                <div class="flex items-center gap-2.5">
                  <span class="w-7 h-7 rounded-lg bg-slate-800 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </span>
                  <span>Ruang BK Gedung Utama Lt. 1, SMK PGRI 05 Jember</span>
                </div>
                <div class="flex items-center gap-2.5">
                  <span class="w-7 h-7 rounded-lg bg-slate-800 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </span>
                  <span>Jam Pelayanan: Senin – Jumat (07.00 – 15.30 WIB)</span>
                </div>
                <div class="flex items-center gap-2.5">
                  <span class="w-7 h-7 rounded-lg bg-slate-800 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </span>
                  <span>Telepon BK: +62 812-3456-7890 (Dra. Hj. Nurul H.)</span>
                </div>
              </div>
            </div>

            <!-- Form Pesan Konsultasi Singkat -->
            <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700">
              <h4 class="text-xs sm:text-sm font-bold text-white mb-3">Tinggalkan Pesan Konsultasi</h4>
              
              <!-- Pesan Konfirmasi Pengiriman Berhasil (Clean SVG, Zero Emojis) -->
              <div
                v-if="consultSuccessMessage"
                class="mb-3.5 p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-start gap-2.5 animate-in fade-in"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <div class="flex-1">
                  <p class="font-bold text-white">Pesan Diteruskan ke Admin BK</p>
                  <p class="text-[11px] text-emerald-200/90 mt-0.5 leading-relaxed">{{ consultSuccessMessage }}</p>
                </div>
              </div>

              <div class="space-y-2.5">
                <div>
                  <label class="text-[11px] font-semibold text-slate-300 block mb-1">Nama Orang Tua / Wali *</label>
                  <input
                    v-model="consultForm.name"
                    type="text"
                    placeholder="Contoh: Bpk. Haryanto"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label class="text-[11px] font-semibold text-slate-300 block mb-1">Nama Siswa &amp; Kelas *</label>
                  <input
                    v-model="consultForm.student"
                    type="text"
                    placeholder="Contoh: Muhammad Rizky Pratama (XI RPL 2)"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label class="text-[11px] font-semibold text-slate-300 block mb-1">Nomor WhatsApp / HP Wali Murid (Opsional)</label>
                  <input
                    v-model="consultForm.phone"
                    type="text"
                    placeholder="Contoh: 081234567890"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 font-mono"
                  />
                </div>
                <div>
                  <label class="text-[11px] font-semibold text-slate-300 block mb-1">Topik Konsultasi</label>
                  <input
                    v-model="consultForm.topic"
                    type="text"
                    placeholder="Contoh: Klarifikasi catatan kehadiran / izin khusus..."
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                </div>

                <button
                  @click="sendConsultRequest"
                  :disabled="isSubmittingConsult"
                  class="w-full mt-2 py-2.5 bg-blue-600 hover:bg-blue-500 disabled:opacity-60 font-bold text-xs rounded-xl transition cursor-pointer text-white flex items-center justify-center gap-2"
                >
                  <svg v-if="isSubmittingConsult" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                  </svg>
                  <span>{{ isSubmittingConsult ? 'Mengirim ke Admin...' : 'Kirim Pesan' }}</span>
                </button>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- ============================================================ -->
      <!-- F. FOOTER                                                    -->
      <!-- ============================================================ -->
      <footer class="bg-white text-slate-500 py-6 px-4 sm:px-6 lg:px-8 text-xs border-t border-slate-200">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
          <div class="flex items-center gap-2">
            <img src="/logo-smk.png" alt="Logo SMK" class="w-5 h-5 object-contain" />
            <span class="font-bold text-slate-800">SMK PGRI 05 JEMBER</span>
            <span>•</span>
            <span>Layanan BK Prima</span>
          </div>
          <p class="text-[11px] text-slate-400">
            Sistem Informasi Kehadiran &amp; Pembinaan Kedisiplinan Siswa.
          </p>
        </div>
      </footer>

    </div>

    <!-- ============================================================ -->
    <!-- 5. FLOATING MOBILE BOTTOM NAVIGATION DOCK (POP-UP SLIDING)   -->
    <!-- ============================================================ -->
    <nav class="lg:hidden fixed bottom-4 left-4 right-4 z-40 max-w-sm mx-auto">
      <div class="bg-blue-600 rounded-3xl shadow-xl shadow-blue-600/35 h-[68px] px-1 flex items-center relative select-none">
        
        <!-- Sliding Elevated Animated Indicator -->
        <div 
          class="absolute top-0 bottom-0 left-0 w-1/5 flex items-center justify-center pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.34,1.35,0.64,1)] z-10"
          :style="{
            transform: `translateX(${activeBottomIndex * 100}%)`
          }"
        >
          <div class="w-[58px] h-[58px] rounded-full bg-white flex items-center justify-center shadow-xl shadow-blue-950/35 border-[3px] border-white -translate-y-4 transition-transform duration-200">
            <div class="w-[46px] h-[46px] rounded-full bg-blue-600 flex flex-col items-center justify-center shadow-inner">
              <component :is="bottomNavTabs[activeBottomIndex].icon" class="w-4 h-4 text-white" />
              <span class="text-[7.5px] font-bold text-white leading-none mt-0.5 tracking-tight px-0.5">
                {{ bottomNavTabs[activeBottomIndex].label }}
              </span>
            </div>
          </div>
        </div>

        <!-- 4 Tab Buttons (Beranda, Cari, Kelas, Tata Tertib) -->
        <button
          v-for="(item, idx) in bottomNavTabs"
          :key="item.key"
          @click="handleBottomTabClick(item, idx)"
          class="flex-1 flex flex-col items-center justify-center relative z-20 h-full py-1 cursor-pointer select-none text-center"
        >
          <div 
            class="flex flex-col items-center justify-center py-1 transition-all duration-200"
            :class="activeBottomIndex === idx ? 'opacity-0 scale-75' : 'opacity-85 hover:opacity-100'"
          >
            <component :is="item.icon" class="w-5 h-5 text-white" />
            <span class="text-[10px] font-semibold text-white mt-1 leading-tight">{{ item.label }}</span>
          </div>
        </button>

      </div>
    </nav>

    <!-- ============================================================ -->
    <!-- 6. MODAL PREVIEW FOTO BUKTI PELANGGARAN                      -->
    <!-- ============================================================ -->
    <div
      v-if="activePhotoModal"
      class="fixed inset-0 z-[60] bg-slate-900/80 flex items-center justify-center p-4 animate-in fade-in duration-150 backdrop-blur-xs"
      @click.self="activePhotoModal = null"
    >
      <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 flex flex-col">
        
        <!-- Modal Photo Header -->
        <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-rose-400 font-mono">
              +{{ activePhotoModal.points }} Poin
            </span>
            <div>
              <h4 class="text-xs sm:text-sm font-bold text-white line-clamp-1">{{ activePhotoModal.title }}</h4>
              <p class="text-[10px] text-slate-400">Lampiran Bukti Guru BK</p>
            </div>
          </div>

          <button
            @click="activePhotoModal = null"
            class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Photo Display -->
        <div class="relative bg-slate-950 flex items-center justify-center max-h-[60vh] overflow-hidden">
          <img
            :src="activePhotoModal.photoUrl"
            :alt="activePhotoModal.title"
            class="w-full max-h-[50vh] object-contain"
          />
          <div class="absolute bottom-2 left-2 right-2 bg-slate-900/90 px-3 py-1.5 rounded-lg text-white text-[11px] flex items-center justify-between">
            <span class="flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ activePhotoModal.photoLocation || 'Lingkungan SMK PGRI 05' }}</span>
            </span>
            <span class="text-emerald-300 font-bold">Terverifikasi</span>
          </div>
        </div>

        <!-- Meta Details -->
        <div class="p-4 bg-slate-50 space-y-2 text-xs">
          <div class="flex justify-between text-slate-600">
            <span>Waktu:</span>
            <strong class="text-slate-900">{{ activePhotoModal.date }} • {{ activePhotoModal.time }}</strong>
          </div>
          <div class="flex justify-between text-slate-600">
            <span>Petugas Pelapor:</span>
            <strong class="text-slate-900">{{ activePhotoModal.reporter }}</strong>
          </div>
          <div class="bg-white p-3 rounded-xl border border-slate-200 mt-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Catatan Guru:</span>
            <p class="text-slate-700 text-xs leading-relaxed">
              {{ activePhotoModal.note }}
            </p>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 bg-white border-t border-slate-200 flex justify-end">
          <button
            @click="activePhotoModal = null"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer"
          >
            Tutup
          </button>
        </div>

      </div>
    </div>

    <!-- ============================================================ -->
    <!-- 7. MODAL DETAIL KELAS (ROMBEL INSPECTOR)                     -->
    <!-- ============================================================ -->
    <div
      v-if="activeClassModal"
      class="fixed inset-0 z-50 bg-slate-900/70 flex items-center justify-center p-4 animate-in fade-in duration-150"
      @click.self="activeClassModal = null"
    >
      <div class="bg-white rounded-2xl max-w-xl w-full p-5 sm:p-6 border border-slate-200 shadow-2xl relative max-h-[85vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between gap-4 pb-3 border-b border-slate-100">
          <div>
            <div class="flex items-center gap-2">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">
                {{ activeClassModal.jurusan }}
              </span>
              <h3 class="text-lg font-bold text-slate-900">{{ activeClassModal.name }}</h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Wali Kelas: <strong>{{ activeClassModal.waliKelas }}</strong> • BK: <strong>{{ activeClassModal.guruBK }}</strong>
            </p>
          </div>
          <button
            @click="activeClassModal = null"
            class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Class Quick Stats -->
        <div class="grid grid-cols-3 gap-2.5 py-3 border-b border-slate-100 text-center">
          <div class="bg-slate-50 p-2.5 rounded-xl">
            <span class="text-[10px] font-semibold text-slate-400 uppercase">Siswa</span>
            <span class="text-base font-bold text-slate-800 block">{{ activeClassModal.studentCount }} Orang</span>
          </div>
          <div class="bg-emerald-50 p-2.5 rounded-xl">
            <span class="text-[10px] font-semibold text-emerald-700 uppercase">Kehadiran</span>
            <span class="text-base font-bold text-emerald-700 block">{{ activeClassModal.attendanceRate }}%</span>
          </div>
          <div class="bg-blue-50 p-2.5 rounded-xl">
            <span class="text-[10px] font-semibold text-blue-700 uppercase">Kedisiplinan</span>
            <span class="text-xs font-bold text-blue-800 block mt-0.5">Baik</span>
          </div>
        </div>

        <!-- Student List in Class -->
        <div class="flex-1 overflow-y-auto py-3 space-y-2">
          <div class="flex items-center justify-between mb-2">
            <h4 class="text-xs font-semibold text-slate-600">Daftar Siswa di Kelas:</h4>
            <button
              type="button"
              @click="classOnlyViolations = !classOnlyViolations"
              class="text-xs px-2.5 py-1 rounded-lg border transition font-medium cursor-pointer"
              :class="classOnlyViolations ? 'bg-rose-50 text-rose-700 border-rose-200 font-semibold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
            >
              {{ classOnlyViolations ? 'Menampilkan Siswa Melanggar' : 'Filter Siswa Melanggar' }}
            </button>
          </div>
          
          <div
            v-for="(st, idx) in filteredClassStudents"
            :key="idx"
            class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 transition border border-slate-100 text-xs"
          >
            <div class="flex items-center gap-2.5">
              <span class="w-5 text-slate-400 font-medium text-center text-[11px]">{{ idx + 1 }}</span>
              <div>
                <span class="font-semibold text-slate-900 block leading-tight">{{ st.name }}</span>
                <span class="text-[10px] text-slate-400 font-mono">NISN: {{ st.nisn }}</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <span
                class="px-2 py-0.5 rounded text-[11px] font-medium"
                :class="st.points === 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700 border border-rose-200'"
              >
                {{ st.points === 0 ? '0 Poin' : `+${st.points} Poin` }}
              </span>
              <button
                @click="openStudentDetailFromClass(st, activeClassModal)"
                class="px-2.5 py-1 bg-white hover:bg-slate-800 hover:text-white border border-slate-200 rounded-lg text-xs font-medium transition cursor-pointer"
              >
                Lihat Catatan
              </button>
            </div>
          </div>

          <div v-if="filteredClassStudents.length === 0" class="py-6 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-slate-100">
            Tidak ada siswa dengan catatan pelanggaran di kelas ini.
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="pt-3 border-t border-slate-100 flex justify-end">
          <button
            @click="activeClassModal = null"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer"
          >
            Tutup
          </button>
        </div>

      </div>
    </div>

    <!-- ============================================================ -->
    <!-- 8. MODAL DETAIL CATATAN SISWA, KELAS & FOTO PELANGGARAN      -->
    <!-- ============================================================ -->
    <div
      v-if="activeStudentDetailModal"
      class="fixed inset-0 z-50 bg-slate-900/75 flex items-center justify-center p-3 sm:p-5 animate-in fade-in duration-150 backdrop-blur-xs"
      @click.self="closeStudentDetailModal"
    >
      <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl relative max-h-[90vh] flex flex-col overflow-hidden">
        
        <!-- Modal Header / Bar Aksi -->
        <div class="p-4 sm:p-5 bg-slate-900 text-white flex items-center justify-between gap-3 border-b border-slate-800">
          <div class="flex items-center gap-2.5">
            <button
              v-if="previousClassModal"
              @click="backToClassModal"
              class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
              title="Kembali ke Daftar Siswa Kelas"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              <span class="hidden sm:inline">Kembali ke {{ previousClassModal.name }}</span>
              <span class="sm:hidden">Kembali</span>
            </button>
            <div>
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Rapor Disiplin &amp; Catatan Siswa</span>
              <h3 class="text-sm sm:text-base font-bold text-white leading-tight">Rekam Jejak Kedisiplinan</h3>
            </div>
          </div>

          <div class="flex items-center gap-1.5">
            <button
              @click="closeStudentDetailModal"
              class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center cursor-pointer transition"
              title="Tutup"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-slate-50/50">

          <!-- 1. DATA KELAS SISWA (LENGKAP & JELAS) -->
          <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
              <div class="flex items-center gap-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Data Rombongan Belajar (Kelas)</h4>
              </div>
              <span class="text-xs font-bold text-slate-700">
                {{ activeStudentDetailModal.class }}
              </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <span class="text-[10px] text-slate-400 block font-semibold uppercase">Kelas</span>
                <strong class="text-slate-800 block mt-0.5">{{ activeStudentDetailModal.class }}</strong>
              </div>
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <span class="text-[10px] text-slate-400 block font-semibold uppercase">Jurusan Keahlian</span>
                <strong class="text-slate-800 block mt-0.5">{{ activeStudentDetailModal.jurusanFull || activeStudentDetailModal.jurusan }}</strong>
              </div>
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <span class="text-[10px] text-slate-400 block font-semibold uppercase">Wali Kelas</span>
                <strong class="text-slate-800 block mt-0.5 truncate" :title="activeStudentDetailModal.waliKelas">{{ activeStudentDetailModal.waliKelas }}</strong>
              </div>
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <span class="text-[10px] text-slate-400 block font-semibold uppercase">Guru BK Pendamping</span>
                <strong class="text-slate-800 block mt-0.5 truncate" :title="activeStudentDetailModal.guruBK">{{ activeStudentDetailModal.guruBK }}</strong>
              </div>
            </div>
          </div>

          <!-- 2. IDENTITAS SISWA & RINGKASAN POIN -->
          <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3.5">
                <img
                  :src="activeStudentDetailModal.avatar"
                  :alt="activeStudentDetailModal.name"
                  class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0"
                />
                <div>
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="text-base font-bold text-slate-900">{{ activeStudentDetailModal.name }}</h4>
                    <span
                      class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                      :class="activeStudentDetailModal.points === 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                    >
                      {{ statusLabel(activeStudentDetailModal.points) }}
                    </span>
                  </div>
                  <p class="text-xs text-slate-500 mt-0.5">
                    NISN: <strong class="text-slate-800">{{ activeStudentDetailModal.nisn }}</strong> • Status: {{ actionCategory(activeStudentDetailModal.points) }}
                  </p>
                </div>
              </div>

              <!-- Total Poin Box -->
              <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 self-start sm:self-auto">
                <div>
                  <span class="text-[10px] font-semibold text-slate-400 uppercase block">Total Poin</span>
                  <span class="text-2xl font-extrabold block leading-tight" :class="pointsColorClass(activeStudentDetailModal.points)">
                    {{ activeStudentDetailModal.points }}
                  </span>
                </div>
                <div class="text-[10px] text-slate-400 border-l border-slate-200 pl-3">
                  <span>Batas SP: 100 Poin</span>
                  <span class="block text-slate-500 font-medium">SMK PGRI 05 Jember</span>
                </div>
              </div>
            </div>

            <!-- Progres bar visual -->
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mt-4">
              <div
                class="h-full rounded-full transition-all duration-300"
                :class="progressBarClass(activeStudentDetailModal.points)"
                :style="{ width: `${Math.min(100, Math.max(activeStudentDetailModal.points > 0 ? 5 : 0, activeStudentDetailModal.points))}%` }"
              ></div>
            </div>
          </div>

          <!-- 3. CATATAN PELANGGARAN SISWA & BUKTI FOTO -->
          <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                  Catatan Pelanggaran &amp; Bukti Foto Dokumentasi
                </h4>
              </div>
              <span class="text-xs text-slate-500">
                {{ activeStudentDetailModal.violations.length }} Catatan Tercatat
              </span>
            </div>

            <!-- Jika tidak ada pelanggaran (0 poin) -->
            <div
              v-if="activeStudentDetailModal.violations.length === 0"
              class="p-6 rounded-xl bg-emerald-50/70 border border-emerald-200 text-center"
            >
              <div class="w-10 h-10 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <h5 class="text-sm font-bold text-emerald-900">Catatan Disiplin Bersih</h5>
              <p class="text-xs text-emerald-700 mt-1 max-w-md mx-auto">
                Ananda {{ activeStudentDetailModal.name }} memiliki catatan kedisiplinan yang sangat baik dan tidak memiliki poin pelanggaran di kelas ini.
              </p>
            </div>

            <!-- List Pelanggaran dengan Foto Jelas -->
            <div v-else class="space-y-4 pt-1">
              <div
                v-for="(v, vIdx) in activeStudentDetailModal.violations"
                :key="vIdx"
                class="p-4 rounded-xl border border-slate-200 bg-slate-50/40 hover:bg-white hover:border-slate-300 transition"
              >
                <!-- Header Pelanggaran -->
                <div class="flex items-start justify-between gap-3 mb-2">
                  <div class="flex items-start gap-2.5">
                    <span class="text-xs font-bold text-rose-600 shrink-0 mt-0.5">
                      +{{ v.points }} Poin
                    </span>
                    <div>
                      <h5 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">{{ v.title }}</h5>
                      <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                        <span>{{ v.date }} • {{ v.time }}</span>
                        <span>•</span>
                        <span>Petugas: {{ v.reporter }}</span>
                      </div>
                    </div>
                  </div>

                  <span
                    class="text-xs font-medium shrink-0"
                    :class="v.status === 'Selesai Pembinaan' ? 'text-emerald-700' : 'text-amber-700'"
                  >
                    {{ v.status }}
                  </span>
                </div>

                <!-- Catatan Keterangan Guru BK -->
                <div class="bg-white p-3 rounded-xl border border-slate-200/80 my-2.5 text-xs text-slate-700 leading-relaxed">
                  <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Catatan Penanganan:</span>
                  {{ v.note }}
                </div>

                <!-- BUKTI FOTO DOKUMENTASI (JIKA ADA FOTONYA) -->
                <div v-if="v.hasPhoto && v.photoUrl" class="mt-3 pt-3 border-t border-slate-200/80">
                  <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-1.5 text-slate-700 text-xs font-bold">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span>Lampiran Foto Bukti Kejadian</span>
                    </div>
                    <span class="text-xs font-medium text-emerald-700 flex items-center gap-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Bukti Terverifikasi</span>
                    </span>
                  </div>

                  <!-- Foto Container -->
                  <div
                    @click="openPhotoPreview(v)"
                    class="group relative rounded-xl overflow-hidden border border-slate-200 bg-slate-100 cursor-pointer hover:border-blue-400 transition"
                  >
                    <div class="aspect-[16/9] w-full overflow-hidden bg-slate-900">
                      <img
                        :src="v.photoUrl"
                        :alt="v.title"
                        class="w-full h-full object-cover group-hover:scale-102 transition duration-300"
                      />
                    </div>

                    <!-- Overlay Bar -->
                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent p-2.5 sm:p-3 flex items-center justify-between text-white text-[11px]">
                      <span class="flex items-center gap-1.5 text-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ v.photoLocation || 'Lingkungan SMK PGRI 05' }}</span>
                      </span>

                      <span class="px-2 py-0.5 rounded bg-white/20 hover:bg-white/30 text-white font-semibold text-[10px] flex items-center gap-1 backdrop-blur-xs transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                        <span>Klik Perbesar</span>
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Jika Pelanggaran Tidak Ada Lampiran Foto -->
                <div v-else class="mt-2 text-[11px] text-slate-400 flex items-center gap-1.5 italic">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Tidak ada lampiran foto dokumentasi untuk butir pelanggaran ini.</span>
                </div>

              </div>
            </div>

          </div>

        </div>

        <!-- Modal Footer Actions -->
        <div class="p-3.5 sm:p-4 bg-white border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2.5">
          <div class="text-[11px] text-slate-500 text-center sm:text-left">
            Layanan BK Prima • SMK PGRI 05 Jember
          </div>

          <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button
              v-if="previousClassModal"
              @click="backToClassModal"
              class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition cursor-pointer"
            >
              Kembali ke Kelas
            </button>
            <button
              @click="openWhatsApp(activeStudentDetailModal)"
              class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs flex items-center gap-1.5 transition cursor-pointer"
            >
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.22.771 1.394.858.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
              </svg>
              <span>Konsultasi Guru BK</span>
            </button>
            <button
              @click="closeStudentDetailModal"
              class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-xs transition cursor-pointer"
            >
              Tutup
            </button>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, h } from 'vue'
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

// Navigation & Modals State
const activeNavIndex = ref(0)
const activeBottomIndex = ref(0)
const mobileDrawerOpen = ref(false)
const activeClassModal = ref(null)
const activePhotoModal = ref(null)
const activeStudentDetailModal = ref(null)
const previousClassModal = ref(null)

const searchQuery = ref('')
const selectedLevelFilter = ref('Semua Tingkat')
const selectedJurusanFilter = ref('Semua Jurusan')
const activeRuleCat = ref('terlambat')

// Icons as pure clean SVGs (Zero emojis)
const IconHome = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': 2 }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
])

const IconSearch = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': 2 }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' })
])

const IconClass = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': 2 }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
])

const IconRules = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': 2 }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
])

const IconAlert = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': 2 }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })
])

const IconPhone = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': 2 }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z' })
])

// Navigation Items for Desktop Sidebar
const navSections = [
  { id: 'section-beranda', label: 'Beranda', icon: IconHome },
  { id: 'section-pelanggaran', label: 'Siswa Melanggar', icon: IconAlert },
  { id: 'section-cari', label: 'Pencarian Siswa', icon: IconSearch },
  { id: 'section-kelas', label: 'Daftar Kelas', icon: IconClass },
  { id: 'section-tata-tertib', label: 'Tata Tertib', icon: IconRules },
  { id: 'section-kontak', label: 'Kontak Guru BK', icon: IconPhone }
]

// 4 Bottom Navigation Tabs (Floating Animated Elevated Bar)
const bottomNavTabs = [
  { key: 'beranda', id: 'section-beranda', label: 'Beranda', icon: IconHome },
  { key: 'pelanggaran', id: 'section-pelanggaran', label: 'Melanggar', icon: IconAlert },
  { key: 'cari', id: 'section-cari', label: 'Cari', icon: IconSearch },
  { key: 'kelas', id: 'section-kelas', label: 'Kelas', icon: IconClass },
  { key: 'tata-tertib', id: 'section-tata-tertib', label: 'Aturan', icon: IconRules }
]

const handleBottomTabClick = (tab, idx) => {
  activeBottomIndex.value = idx
  activeNavIndex.value = idx
  scrollToSection(tab.id, idx)
}

const scrollToSection = (sectionId, idx) => {
  if (idx !== undefined) {
    activeNavIndex.value = idx
    if (idx < bottomNavTabs.length) {
      activeBottomIndex.value = idx
    }
  }
  const el = document.getElementById(sectionId)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

// Quick demo chips
const demoChips = [
  { name: 'Muhammad Rizky', class: 'XI RPL 2', nisn: '22231084' },
  { name: 'Dimas Arya', class: 'X RPL 1', nisn: '00729183' },
  { name: 'Rania Zahra', class: 'X RPL 1', nisn: '00729185' },
  { name: 'Ahmad Fauzi', class: 'X RPL 1', nisn: '00729182' }
]

// Jurusan Helper
const getJurusanFullName = (code) => {
  const map = {
    RPL: 'Rekayasa Perangkat Lunak',
    DKV: 'Desain Komunikasi Visual',
    TSM: 'Teknik Sepeda Motor',
    TKRO: 'Teknik Kendaraan Ringan Otomotif',
    AKL: 'Akuntansi & Keuangan Lembaga',
    OTKP: 'Otomatisasi & Tata Kelola Perkantoran',
    BDP: 'Bisnis Daring & Pemasaran'
  }
  return map[code] || code
}

// Comprehensive Students Database with Photo Evidence
const studentsDb = [
  {
    name: 'Muhammad Rizky Pratama',
    nisn: '22231084',
    class: 'XI RPL 2',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 35,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 93.8,
    avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 1,
        title: 'Meninggalkan Kelas Saat Jam Pelajaran',
        note: 'Siswa berada di luar lingkungan kelas saat jam pelajaran produktif berlangsung.',
        date: '10 Okt 2024',
        time: '10.15 WIB',
        points: 20,
        reporter: 'Bpk. Hendra Gunawan (Piket)',
        status: 'Dalam Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Area Belakang Gedung Praktik'
      },
      {
        id: 2,
        title: 'Terlambat Masuk Sekolah Lebih Dari 20 Menit',
        note: 'Tiba di gerbang sekolah pukul 07.24 WIB karena kendala sepeda motor.',
        date: '24 Okt 2024',
        time: '07.24 WIB',
        points: 10,
        reporter: 'Petugas Piket Gerbang',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Pintu Gerbang Utama'
      },
      {
        id: 3,
        title: 'Atribut Seragam Tidak Lengkap',
        note: 'Tidak memakai dasi dan sabuk saat apel pagi hari Senin.',
        date: '18 Okt 2024',
        time: '06.55 WIB',
        points: 5,
        reporter: 'Ibu Anita Rahayu, M.Pd',
        status: 'Selesai Pembinaan',
        hasPhoto: false,
        photoUrl: null
      }
    ]
  },
  {
    name: 'Dimas Arya Pratama',
    nisn: '00729183',
    class: 'X RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 15,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 94.4,
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 4,
        title: 'Terlambat Masuk Sekolah',
        note: 'Tiba pukul 07.24 WIB. Dilakukan pendampingan piket dan pembinaan tugas ringan.',
        date: '24 Okt 2024',
        time: '07.24 WIB',
        points: 10,
        reporter: 'Guru Piket Gerbang',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Pos Keamanan Gerbang Utama'
      },
      {
        id: 5,
        title: 'Sepatu Tidak Sesuai Ketentuan',
        note: 'Mengenakan sepatu putih pada hari Senin tanpa surat keterangan izin.',
        date: '12 Okt 2024',
        time: '07.00 WIB',
        points: 5,
        reporter: 'Tim Kedisiplinan',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Halaman Depan Sekolah'
      }
    ]
  },
  {
    name: 'Rania Zahra Putri',
    nisn: '00729185',
    class: 'X RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 10,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 94.4,
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 6,
        title: 'Terlambat Masuk Jam Pertama',
        note: 'Keterlambatan tiba di sekolah pukul 07.15 WIB pada jam mata pelajaran pertama.',
        date: '24 Okt 2024',
        time: '07.15 WIB',
        points: 10,
        reporter: 'Guru Piket',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Koridor Kelas X Gedung A'
      }
    ]
  },
  {
    name: 'Ahmad Fauzi',
    nisn: '00729182',
    class: 'X RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 94.4,
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Fathir Rizky Maulana',
    nisn: '00729184',
    class: 'X RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 94.4,
    avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Siti Nurhaliza',
    nisn: '00729186',
    class: 'X RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 94.4,
    avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Yusuf Maulana Al-Farizi',
    nisn: '00729187',
    class: 'X RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 5,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 94.4,
    avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 7,
        title: 'Kelengkapan Seragam (Dasi & Sabuk Sekolah)',
        note: 'Tidak mengenakan dasi identitas sekolah saat pelaksanaan apel pagi hari Senin.',
        date: '15 Okt 2024',
        time: '06.50 WIB',
        points: 5,
        reporter: 'Ibu Anita Rahayu, M.Pd',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Area Lapangan Upacara'
      }
    ]
  },
  {
    name: 'Zulfikar Ali Akbar',
    nisn: '00729188',
    class: 'X RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 94.4,
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Bima Satria',
    nisn: '00729190',
    class: 'X RPL 2',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Ibu Anita Rahayu, M.Pd',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 97.2,
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Citra Dewi Permata',
    nisn: '00729191',
    class: 'X RPL 2',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 5,
    waliKelas: 'Ibu Anita Rahayu, M.Pd',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 97.2,
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 8,
        title: 'Atribut Kaos Kaki Tidak Sesuai Ketentuan',
        note: 'Kaos kaki tidak berlogo resmi sekolah saat pemeriksaan kerapian hari Selasa.',
        date: '14 Okt 2024',
        time: '07.10 WIB',
        points: 5,
        reporter: 'Bpk. Kurniawan, S.Kom',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Lobi Utama Gedung Pembelajaran'
      }
    ]
  },
  {
    name: 'Doni Firmansyah',
    nisn: '00729192',
    class: 'X RPL 2',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Ibu Anita Rahayu, M.Pd',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 97.2,
    avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Alif Kurniawan',
    nisn: '00729201',
    class: 'X DKV 1',
    jurusan: 'DKV',
    jurusanFull: 'Desain Komunikasi Visual',
    points: 0,
    waliKelas: 'Ibu Rina Melati, S.Pd',
    guruBK: 'Bpk. Danang Prasetyo, S.Pd',
    attendanceRate: 95.8,
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Bella Anggraini',
    nisn: '00729202',
    class: 'X DKV 1',
    jurusan: 'DKV',
    jurusanFull: 'Desain Komunikasi Visual',
    points: 10,
    waliKelas: 'Ibu Rina Melati, S.Pd',
    guruBK: 'Bpk. Danang Prasetyo, S.Pd',
    attendanceRate: 95.8,
    avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 9,
        title: 'Keterlambatan Hadir Jam Pembelajaran Pertama',
        note: 'Tiba di sekolah terlambat 20 menit saat jam pelajaran dasar sketsa dan ilustrasi.',
        date: '19 Okt 2024',
        time: '07.20 WIB',
        points: 10,
        reporter: 'Bpk. Joko Susilo, S.T',
        status: 'Dalam Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Gerbang Barat SMK PGRI 05'
      }
    ]
  },
  {
    name: 'Aditya Pratama',
    nisn: '00729210',
    class: 'X TSM 1',
    jurusan: 'TSM',
    jurusanFull: 'Teknik Sepeda Motor',
    points: 15,
    waliKelas: 'Bpk. Joko Susilo, S.T',
    guruBK: 'Bpk. Danang Prasetyo, S.Pd',
    attendanceRate: 92.5,
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 10,
        title: 'Parkir Sepeda Motor di Luar Area Resmi Siswa',
        note: 'Memarkirkan sepeda motor di jalur evakuasi depan bengkel praktik otomotif.',
        date: '21 Okt 2024',
        time: '07.15 WIB',
        points: 10,
        reporter: 'Petugas Keamanan Sekolah',
        status: 'Dalam Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Area Parkir Depan Bengkel TSM'
      },
      {
        id: 11,
        title: 'Tidak Mengenakan Wearpack Sesuai Jadwal Praktik',
        note: 'Tidak membawa pakaian kerja bengkel saat jam praktik chasis motor.',
        date: '16 Okt 2024',
        time: '08.30 WIB',
        points: 5,
        reporter: 'Instruktur Praktik TSM',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Bengkel Praktik Otomotif'
      }
    ]
  },
  {
    name: 'Bagus Sadewo',
    nisn: '00729211',
    class: 'X TSM 1',
    jurusan: 'TSM',
    jurusanFull: 'Teknik Sepeda Motor',
    points: 5,
    waliKelas: 'Bpk. Joko Susilo, S.T',
    guruBK: 'Bpk. Danang Prasetyo, S.Pd',
    attendanceRate: 92.5,
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 12,
        title: 'Kelengkapan Atribut Sabuk Sekolah',
        note: 'Tidak mengenakan sabuk sekolah resmi saat pemeriksaan rutin.',
        date: '18 Okt 2024',
        time: '07.05 WIB',
        points: 5,
        reporter: 'Guru Piket',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Pintu Masuk Ruang Teori'
      }
    ]
  },
  {
    name: 'Farhan Maulana',
    nisn: '22231070',
    class: 'XI RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Fajar Ramadhan, M.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 96.5,
    avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Galih Ramadhan',
    nisn: '22231071',
    class: 'XI RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Fajar Ramadhan, M.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 96.5,
    avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Naufal Ghani',
    nisn: '22231085',
    class: 'XI RPL 2',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 5,
    waliKelas: 'Bpk. Kurniawan, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 93.8,
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
    violations: [
      {
        id: 13,
        title: 'Terlambat Masuk Jam Pelajaran Setelah Istirahat',
        note: 'Terlambat kembali ke laboratorium komputer setelah jam istirahat pertama.',
        date: '17 Okt 2024',
        time: '09.45 WIB',
        points: 5,
        reporter: 'Guru Mata Pelajaran',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Area Tangga Menuju Lab RPL'
      }
    ]
  },
  {
    name: 'Kayla Salsabila',
    nisn: '22231101',
    class: 'XI DKV 1',
    jurusan: 'DKV',
    jurusanFull: 'Desain Komunikasi Visual',
    points: 0,
    waliKelas: 'Ibu Maya Sartika, S.Sn',
    guruBK: 'Ibu Rina Melati, S.Pd',
    attendanceRate: 95.0,
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Reza Pahlevi',
    nisn: '21221001',
    class: 'XII RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Budi Santoso, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 98.4,
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
    violations: []
  },
  {
    name: 'Tiara Andini',
    nisn: '21221002',
    class: 'XII RPL 1',
    jurusan: 'RPL',
    jurusanFull: 'Rekayasa Perangkat Lunak',
    points: 0,
    waliKelas: 'Bpk. Budi Santoso, S.Kom',
    guruBK: 'Dra. Hj. Nurul Hidayati',
    attendanceRate: 98.4,
    avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=150&q=80',
    violations: []
  }
]

// Classes Database
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
  { id: 'terlambat', name: 'Keterlambatan & Kehadiran' },
  { id: 'seragam', name: 'Seragam & Kerapian' },
  { id: 'perilaku', name: 'Sikap & Etika' },
  { id: 'berat', name: 'Larangan Khusus' }
]

const rulesDb = {
  terlambat: [
    { title: 'Terlambat hadir di sekolah (1 – 15 Menit)', points: 5, action: 'Peringatan Lisan & Pengarahan Guru Piket' },
    { title: 'Terlambat hadir di sekolah (> 15 Menit)', points: 10, action: 'Tugas Edukatif di Lingkungan Sekolah' },
    { title: 'Meninggalkan jam pelajaran tanpa izin', points: 15, action: 'Konseling Khusus Guru BK & Teguran Tertulis' },
    { title: 'Tidak masuk tanpa keterangan > 3 Hari Berturut', points: 25, action: 'Panggilan Orang Tua / Wali Murid' }
  ],
  seragam: [
    { title: 'Seragam tidak lengkap atribut (Dasi, Logo, Sabuk)', points: 5, action: 'Teguran & Pemakaian Atribut Cadangan BK' },
    { title: 'Sepatu dan kaos kaki tidak sesuai aturan hari', points: 5, action: 'Peringatan & Catatan Disiplin' },
    { title: 'Rambut tidak rapi bagi putra / mewarnai rambut', points: 10, action: 'Batas Waktu Perapian 2 Hari' }
  ],
  perilaku: [
    { title: 'Bermain game / gawai saat jam KBM tanpa izin guru', points: 10, action: 'Penyimpanan Gawai Sementara' },
    { title: 'Tidak mematuhi instruksi guru / bersikap kurang sopan', points: 15, action: 'Konseling Pembinaan Sikap' },
    { title: 'Merusak fasilitas sekolah atau kelas', points: 25, action: 'Mengganti Kerusakan & Pembinaan Khusus' }
  ],
  berat: [
    { title: 'Membawa / merokok / rokok elektrik di lingkungan sekolah', points: 30, action: 'Panggilan Orang Tua & Surat Peringatan' },
    { title: 'Terlibat perkelahian / tawuran', points: 50, action: 'Panggilan Orang Tua Keras & Skorsing Edukatif' },
    { title: 'Membawa sajam atau narkotika', points: 100, action: 'Penyerahan Kembali kepada Orang Tua / Pihak Terkait' }
  ]
}

const activeRules = computed(() => rulesDb[activeRuleCat.value] || [])

const generateViolationsForPoints = (points, studentName) => {
  if (points <= 0) return []
  if (points === 5) {
    return [
      {
        id: Date.now() + Math.random(),
        title: 'Atribut Seragam Sekolah Kurang Lengkap',
        note: `Siswa ${studentName} tercatat tidak mengenakan dasi/atribut resmi saat apel pagi. Telah diingatkan dan menandatangani kartu disiplin.`,
        date: '16 Okt 2024',
        time: '06.55 WIB',
        points: 5,
        reporter: 'Ibu Anita Rahayu, M.Pd',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Halaman Gedung Teori'
      }
    ]
  }
  if (points === 10) {
    return [
      {
        id: Date.now() + Math.random(),
        title: 'Terlambat Masuk Jam Pelajaran Pertama (>15 Menit)',
        note: `Siswa ${studentName} tiba di gerbang sekolah pukul 07.22 WIB saat KBM pertama berlangsung. Diberikan tugas edukatif oleh tim piket.`,
        date: '22 Okt 2024',
        time: '07.22 WIB',
        points: 10,
        reporter: 'Petugas Piket Gerbang',
        status: 'Dalam Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Pos Keamanan Gerbang Utama'
      }
    ]
  }
  if (points === 15) {
    return [
      {
        id: Date.now() + Math.random() + 1,
        title: 'Terlambat Masuk Sekolah Lebih Dari 20 Menit',
        note: `Tiba di sekolah pukul 07.25 WIB. Didampingi guru piket untuk pencatatan dan pengarahan kedisiplinan.`,
        date: '24 Okt 2024',
        time: '07.25 WIB',
        points: 10,
        reporter: 'Guru Piket Gerbang',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Pintu Gerbang Utama'
      },
      {
        id: Date.now() + Math.random() + 2,
        title: 'Sepatu / Kaos Kaki Tidak Sesuai Ketentuan',
        note: `Menggunakan sepatu warna tidak sesuai ketentuan tata tertib sekolah hari Senin.`,
        date: '14 Okt 2024',
        time: '07.00 WIB',
        points: 5,
        reporter: 'Tim Kedisiplinan',
        status: 'Selesai Pembinaan',
        hasPhoto: true,
        photoUrl: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
        photoLocation: 'Halaman Depan Sekolah'
      }
    ]
  }
  return [
    {
      id: Date.now() + Math.random() + 1,
      title: 'Meninggalkan Kelas Saat Jam Pelajaran Berlangsung',
      note: `Siswa berada di luar lingkungan kelas tanpa surat izin guru mata pelajaran saat jam produktif.`,
      date: '10 Okt 2024',
      time: '10.15 WIB',
      points: 20,
      reporter: 'Bpk. Hendra Gunawan (Piket)',
      status: 'Dalam Pembinaan',
      hasPhoto: true,
      photoUrl: 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=800&q=80',
      photoLocation: 'Area Belakang Gedung Praktik'
    },
    {
      id: Date.now() + Math.random() + 2,
      title: 'Terlambat Masuk Sekolah Lebih Dari 20 Menit',
      note: `Tiba di gerbang sekolah pukul 07.24 WIB karena kendala transportasi.`,
      date: '24 Okt 2024',
      time: '07.24 WIB',
      points: 10,
      reporter: 'Petugas Piket Gerbang',
      status: 'Selesai Pembinaan',
      hasPhoto: true,
      photoUrl: 'https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?auto=format&fit=crop&w=800&q=80',
      photoLocation: 'Pintu Gerbang Utama'
    },
    {
      id: Date.now() + Math.random() + 3,
      title: 'Atribut Seragam Tidak Lengkap',
      note: `Tidak memakai dasi dan sabuk resmi sekolah saat apel pagi.`,
      date: '18 Okt 2024',
      time: '06.55 WIB',
      points: 5,
      reporter: 'Ibu Anita Rahayu, M.Pd',
      status: 'Selesai Pembinaan',
      hasPhoto: false,
      photoUrl: null
    }
  ]
}

const resolveStudentData = (query, specificClass = null) => {
  if (!query) return null
  const q = String(query).trim().toLowerCase()

  // 1. Direct match in studentsDb
  let found = studentsDb.find(s => s.nisn.toLowerCase() === q || s.name.toLowerCase().includes(q))
  if (found) {
    if (specificClass) {
      found.class = specificClass.name
      found.jurusan = specificClass.jurusan
      found.waliKelas = specificClass.waliKelas
      found.guruBK = specificClass.guruBK
      found.attendanceRate = specificClass.attendanceRate
      found.jurusanFull = getJurusanFullName(specificClass.jurusan)
    } else if (!found.jurusanFull) {
      found.jurusanFull = getJurusanFullName(found.jurusan)
    }
    return found
  }

  // 2. Search through classesDb
  for (const c of classesDb) {
    const studentInClass = c.students.find(st => st.nisn.toLowerCase() === q || st.name.toLowerCase().includes(q))
    if (studentInClass) {
      const isFemale = /siti|dewi|zahra|rani|tiara|bella|kayla|citra|anita|putri|ayu/i.test(studentInClass.name)
      const generated = {
        name: studentInClass.name,
        nisn: studentInClass.nisn,
        class: c.name,
        jurusan: c.jurusan,
        jurusanFull: getJurusanFullName(c.jurusan),
        points: studentInClass.points,
        waliKelas: c.waliKelas,
        guruBK: c.guruBK,
        attendanceRate: c.attendanceRate,
        avatar: isFemale
          ? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80'
          : 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
        violations: generateViolationsForPoints(studentInClass.points, studentInClass.name)
      }
      studentsDb.push(generated)
      return generated
    }
  }

  // 3. Backend Searched Student prop fallback if matches
  if (props.searchedStudent && (props.searchedStudent.nisn.toLowerCase() === q || props.searchedStudent.name.toLowerCase().includes(q))) {
    const bs = props.searchedStudent
    return {
      name: bs.name,
      nisn: bs.nisn,
      class: bs.class_name,
      jurusan: bs.major,
      jurusanFull: getJurusanFullName(bs.major),
      points: bs.total_violation_points || 0,
      waliKelas: 'Guru Wali Kelas SMK PGRI 05',
      guruBK: 'Dra. Hj. Nurul Hidayati',
      attendanceRate: 95.0,
      avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
      violations: (bs.violations || []).map(v => ({
        id: v.id,
        title: v.violation_type || 'Pelanggaran Tata Tertib',
        note: v.description || 'Pencatatan kedisiplinan siswa.',
        date: v.violation_date || 'Hari ini',
        time: 'Jam Sekolah',
        points: v.points || 5,
        reporter: 'Petugas Piket BK',
        status: 'Tercatat',
        hasPhoto: !!v.evidence_photo,
        photoUrl: v.evidence_photo || null,
        photoLocation: 'Lingkungan Sekolah'
      }))
    }
  }

  return null
}

const selectedStudent = computed(() => {
  if (!searchQuery.value) return null
  return resolveStudentData(searchQuery.value)
})

const filteredClasses = computed(() => {
  return classesDb.filter(c => {
    const matchLevel = selectedLevelFilter.value === 'Semua Tingkat' || c.level === selectedLevelFilter.value
    const matchJurusan = selectedJurusanFilter.value === 'Semua Jurusan' || c.jurusan === selectedJurusanFilter.value
    return matchLevel && matchJurusan
  })
})

// State & Filter Siswa Melanggar
const violationFilterTab = ref('semua')
const violationSearchQuery = ref('')
const violationLevelFilter = ref('Semua Tingkat')
const classOnlyViolations = ref(false)

const violatingStudents = computed(() => {
  const list = []
  const seenNisns = new Set()

  for (const s of studentsDb) {
    if ((s.points > 0 || (s.violations && s.violations.length > 0)) && !seenNisns.has(s.nisn)) {
      seenNisns.add(s.nisn)
      list.push(s)
    }
  }

  for (const c of classesDb) {
    for (const st of c.students) {
      if (st.points > 0 && !seenNisns.has(st.nisn)) {
        seenNisns.add(st.nisn)
        const fullStudent = resolveStudentData(st.nisn, c)
        if (fullStudent) {
          list.push(fullStudent)
        }
      }
    }
  }

  return list.sort((a, b) => b.points - a.points)
})

const violatingStudentsHighPointsCount = computed(() => {
  return violatingStudents.value.filter(s => s.points >= 20).length
})

const violatingStudentsWithPhotosCount = computed(() => {
  return violatingStudents.value.filter(s => s.violations && s.violations.some(v => v.photoUrl)).length
})

const filteredViolatingStudents = computed(() => {
  return violatingStudents.value.filter(s => {
    if (violationFilterTab.value === 'perhatian' && s.points < 20) return false
    if (violationFilterTab.value === 'pembinaan') {
      const hasActive = s.violations && s.violations.some(v => v.status === 'Dalam Pembinaan')
      if (!hasActive) return false
    }
    if (violationFilterTab.value === 'selesai') {
      const allDone = s.violations && s.violations.every(v => v.status === 'Selesai Pembinaan')
      if (!allDone) return false
    }
    if (violationFilterTab.value === 'foto') {
      const hasPhoto = s.violations && s.violations.some(v => v.photoUrl)
      if (!hasPhoto) return false
    }

    if (violationLevelFilter.value !== 'Semua Tingkat') {
      if (violationLevelFilter.value.includes('10') && !s.class.startsWith('X ') && !s.class.startsWith('X-')) return false
      if (violationLevelFilter.value.includes('11') && !s.class.startsWith('XI ') && !s.class.startsWith('XI-')) return false
      if (violationLevelFilter.value.includes('12') && !s.class.startsWith('XII ') && !s.class.startsWith('XII-')) return false
    }

    if (violationSearchQuery.value) {
      const q = violationSearchQuery.value.toLowerCase().trim()
      const matchName = s.name.toLowerCase().includes(q)
      const matchNisn = s.nisn.toLowerCase().includes(q)
      const matchClass = s.class.toLowerCase().includes(q)
      if (!matchName && !matchNisn && !matchClass) return false
    }

    return true
  })
})

const openViolatingStudentDetail = (st) => {
  activeStudentDetailModal.value = st
  searchQuery.value = st.nisn
}

const openConsultationForStudent = (st) => {
  consultForm.student = `${st.name} (${st.class})`
  consultForm.topic = `Konsultasi catatan poin pelanggaran ananda (${st.points} Poin)`
  scrollToSection('section-kontak', 5)
}

const filteredClassStudents = computed(() => {
  if (!activeClassModal.value || !activeClassModal.value.students) return []
  if (!classOnlyViolations.value) return activeClassModal.value.students
  return activeClassModal.value.students.filter(s => s.points > 0)
})

const selectStudentByNisn = (nisn) => {
  searchQuery.value = nisn
  scrollToSection('section-cari', 2)
}

const openStudentDetailFromClass = (st, classObj) => {
  const student = resolveStudentData(st.nisn || st.name, classObj)
  previousClassModal.value = classObj
  activeClassModal.value = null
  activeStudentDetailModal.value = student
  searchQuery.value = student.nisn
}

const backToClassModal = () => {
  const prev = previousClassModal.value
  activeStudentDetailModal.value = null
  if (prev) {
    activeClassModal.value = prev
  }
}

const closeStudentDetailModal = () => {
  activeStudentDetailModal.value = null
  previousClassModal.value = null
}

const checkFromModal = (nisn) => {
  const st = resolveStudentData(nisn, activeClassModal.value)
  if (st && activeClassModal.value) {
    openStudentDetailFromClass(st, activeClassModal.value)
  } else {
    activeClassModal.value = null
    selectStudentByNisn(nisn)
  }
}

const openClassDetail = (rombel) => {
  classOnlyViolations.value = false
  activeClassModal.value = rombel
}

const openPhotoPreview = (violation) => {
  activePhotoModal.value = violation
}

const executeSearch = () => {
  if (!searchQuery.value) return
  const q = searchQuery.value.trim()
  if (/^\d+$/.test(q)) {
    router.get('/tamu', { nisn: q }, { preserveState: true })
  }
}

const statusBadgeClass = (points) => {
  if (points === 0) return 'bg-emerald-500/20 text-emerald-300'
  if (points <= 20) return 'bg-blue-500/20 text-blue-300'
  if (points <= 40) return 'bg-amber-500/20 text-amber-300'
  return 'bg-rose-500/20 text-rose-300'
}

const statusLabel = (points) => {
  if (points === 0) return 'Disiplin Baik'
  if (points <= 20) return 'Status Normal'
  if (points <= 40) return 'Perhatian BK'
  return 'Peringatan'
}

const pointsColorClass = (points) => {
  if (points === 0) return 'text-emerald-600'
  if (points <= 20) return 'text-blue-600'
  if (points <= 40) return 'text-amber-600'
  return 'text-rose-600'
}

const actionCategory = (points) => {
  if (points === 0) return 'Tertib & Disiplin'
  if (points <= 20) return 'Pembinaan Ringan Wali Kelas'
  if (points <= 40) return 'Peringatan Tahap 1 • Panggilan Ortu'
  return 'Tindakan Disiplin Khusus'
}

const progressBarClass = (points) => {
  if (points <= 20) return 'bg-emerald-500'
  if (points <= 40) return 'bg-amber-500'
  return 'bg-rose-600'
}

const openWhatsApp = (student) => {
  const text = encodeURIComponent(`Halo Bpk/Ibu Guru BK SMK PGRI 05 Jember, saya wali murid dari ${student.name} (${student.class}, NISN: ${student.nisn}). Saya ingin berkonsultasi mengenai perkembangan ananda. Terima kasih.`)
  window.open(`https://wa.me/6281234567890?text=${text}`, '_blank')
}

const consultForm = ref({
  name: '',
  student: '',
  topic: '',
  phone: ''
})
const isSubmittingConsult = ref(false)
const consultSuccessMessage = ref('')

const sendConsultRequest = async () => {
  if (!consultForm.value.name.trim() || !consultForm.value.student.trim()) {
    alert('Mohon lengkapi nama orang tua dan nama siswa terlebih dahulu.')
    return
  }

  isSubmittingConsult.value = true
  const parentName = consultForm.value.name.trim()
  const studentName = consultForm.value.student.trim()
  const topicText = consultForm.value.topic.trim() || 'Permohonan konsultasi perkembangan siswa'
  const phoneText = consultForm.value.phone.trim() || null

  const newEntry = {
    id: Date.now(),
    parent_name: parentName,
    student_name: studentName,
    topic: topicText,
    phone: phoneText,
    status: 'unread',
    created_at: new Date().toISOString()
  }

  // 1. Client-side LocalStorage sync (instant notification update in Admin Panel)
  try {
    const existing = JSON.parse(localStorage.getItem('bk_prima_consultations') || '[]')
    existing.unshift(newEntry)
    localStorage.setItem('bk_prima_consultations', JSON.stringify(existing))
  } catch (e) {
    console.error('LocalStorage error:', e)
  }

  // 2. Server-side API POST ke database backend
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    await fetch('/tamu/konsultasi', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
      },
      body: JSON.stringify({
        name: parentName,
        student: studentName,
        topic: topicText,
        phone: phoneText
      })
    })
  } catch (err) {
    console.error('Database sync error:', err)
  } finally {
    isSubmittingConsult.value = false
    consultSuccessMessage.value = `Pesan untuk ananda ${studentName} telah berhasil dikirim ke Ruang BK. Guru BK akan menindaklanjuti pesan Bapak/Ibu segera.`
    consultForm.value.name = ''
    consultForm.value.student = ''
    consultForm.value.topic = ''
    consultForm.value.phone = ''
  }
}

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
      avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
      violations: (s.violations || []).map(v => ({
        id: v.id,
        title: v.violation_name || v.category,
        note: v.notes || 'Tercatat dalam rekaman kedisiplinan BK',
        date: v.date,
        time: v.time ? `${v.time} WIB` : '07:30 WIB',
        points: v.points,
        reporter: v.reporter || 'Guru Piket / BK',
        status: v.status || 'Dalam Pembinaan',
        hasPhoto: !!v.photo_url,
        photoUrl: v.photo_url || null,
        photoLocation: 'Area Sekolah'
      }))
    }
    studentsDb.unshift(formatted)
    searchQuery.value = s.nisn
  }
})
</script>

<style scoped>
/* Scrollbar hide utility for smooth mobile tabs */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
