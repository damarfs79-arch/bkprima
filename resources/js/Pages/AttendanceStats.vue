<template>
  <AppLayout active-tab="statistik" show-share>
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- 1. HEADER SECTION & FILTER WAKTU                              -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
              <span class="text-blue-700 font-bold uppercase tracking-wider text-[11px]">Portal Guru BK</span>
              <span>&bull;</span>
              <span>HadirMu Presensi</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
              Statistik Kehadiran Siswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
              Rekapitulasi persentase kehadiran siswa SMK PGRI 05 Jember dan pemantauan kedisiplinan.
            </p>
          </div>

          <!-- Toggle Period (1 Minggu vs 1 Bulan vs 12 Bulan) -->
          <div class="flex bg-slate-100 p-1 rounded-lg self-start sm:self-auto overflow-x-auto max-w-full border border-slate-200">
            <button
              @click="period = 'minggu'"
              class="px-3 py-1.5 rounded-md text-xs font-semibold transition cursor-pointer shrink-0"
              :class="period === 'minggu' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
            >
              1 Minggu
            </button>
            <button
              @click="period = 'bulan'"
              class="px-3 py-1.5 rounded-md text-xs font-semibold transition cursor-pointer shrink-0"
              :class="period === 'bulan' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
            >
              1 Bulan
            </button>
            <button
              @click="period = 'tahun'"
              class="px-3 py-1.5 rounded-md text-xs font-semibold transition cursor-pointer shrink-0"
              :class="period === 'tahun' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
            >
              12 Bulan (Komparasi)
            </button>
          </div>
        </div>

        <!-- Integrated Filters Strip -->
        <div class="pt-4 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3.5">
          <!-- Month Filter -->
          <div class="relative">
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Periode Bulan</label>
            <div class="relative">
              <select class="w-full bg-white border border-slate-300 rounded-lg pl-9 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer transition">
                <option>Februari 2026</option>
                <option>Januari 2026</option>
                <option>Desember 2025</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Grade Filter -->
          <div class="relative">
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tingkat Kelas</label>
            <div class="relative">
              <select class="w-full bg-white border border-slate-300 rounded-lg pl-9 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer transition">
                <option>Semua Kelas</option>
                <option>Kelas 10</option>
                <option>Kelas 11</option>
                <option>Kelas 12</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Major Filter -->
          <div class="relative">
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jurusan / Keahlian</label>
            <div class="relative">
              <select class="w-full bg-white border border-slate-300 rounded-lg pl-9 pr-8 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer transition">
                <option>Semua Jurusan</option>
                <option>Rekayasa Perangkat Lunak (RPL)</option>
                <option>Teknik Sepeda Motor (TSM)</option>
                <option>Desain Komunikasi Visual (DKV)</option>
                <option>Teknik Komputer Jaringan (TKJ)</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 2. EARLY WARNING ALERT BANNER (ALPA >= 3 HARI MINGGU INI)     -->
      <!-- ============================================================ -->
      <div v-if="atRiskStudents.length > 0" class="bg-rose-50 border border-rose-200 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
          <div class="w-10 h-10 rounded-lg bg-rose-100 border border-rose-200 text-rose-700 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h2 class="text-sm font-bold text-rose-900">
                Peringatan Dini: Terdeteksi {{ atRiskStudents.length }} Siswa Alpa &ge; 3 Hari dalam 1 Minggu
              </h2>
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-200/80 text-rose-800">
                Perlu Tindakan Guru BK
              </span>
            </div>
            <p class="text-xs text-rose-700 mt-0.5">
              Sesuai SOP kedisiplinan sekolah, siswa dengan 3 kali alpa dalam seminggu wajib diterbitkan Surat Panggilan Orang Tua (SP-1).
            </p>
          </div>
        </div>

        <a
          href="#daftar-alpa-kritis"
          class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-rose-700 hover:bg-rose-800 text-white font-semibold rounded-lg text-xs transition cursor-pointer shrink-0"
        >
          <span>Tindak Lanjuti Siswa</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </a>
      </div>

      <!-- ============================================================ -->
      <!-- 3. HERO CARD: RATA-RATA TINGKAT KEHADIRAN SEKOLAH            -->
      <!-- ============================================================ -->
      <div class="bg-slate-900 rounded-xl p-6 sm:p-7 text-white shadow-xs relative overflow-hidden">
        <div class="relative z-10 space-y-3.5">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
              <span class="p-2 bg-white/10 rounded-lg text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </span>
              <h2 class="text-xs font-semibold tracking-wider uppercase text-slate-300">Rata-rata Tingkat Kehadiran Sekolah</h2>
            </div>
            <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 rounded-full text-xs font-semibold flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
              </svg>
              +2.4% vs Bulan Lalu
            </span>
          </div>

          <div class="flex items-baseline gap-3.5 flex-wrap">
            <span class="text-4xl sm:text-5xl font-bold tracking-tight text-white">94.8%</span>
            <span class="text-xs sm:text-sm text-slate-300 bg-white/10 px-2.5 py-1 rounded-md font-medium">~1.218 hadir dari 1.285 siswa / hari</span>
          </div>

          <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-3xl font-medium">
            Total populasi 1.285 siswa di 36 rombel kelas. Rata-rata kehadiran berada pada kategori <strong class="text-white font-bold">Sangat Baik</strong> (Target Standar Disiplin 90%).
          </p>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 4. 4 GRID METRIC CARDS                                       -->
      <!-- ============================================================ -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Hadir -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siswa Hadir</span>
            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-bold text-slate-900">94.8%</span>
            <span class="text-xs font-medium text-slate-500">1.218 siswa</span>
          </div>
          <p class="text-xs font-medium text-emerald-600 pt-1">
            Tepat Waktu &amp; Tertib
          </p>
        </div>

        <!-- Sakit -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sakit (S)</span>
            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-bold text-slate-900">2.3%</span>
            <span class="text-xs font-medium text-slate-500">~30 siswa</span>
          </div>
          <p class="text-xs font-medium text-slate-600 pt-1">
            Surat Dokter Terverifikasi
          </p>
        </div>

        <!-- Izin -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Izin (I)</span>
            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-bold text-slate-900">1.8%</span>
            <span class="text-xs font-medium text-slate-500">~23 siswa</span>
          </div>
          <p class="text-xs font-medium text-slate-600 pt-1">
            Dispensasi &amp; Urusan Keluarga
          </p>
        </div>

        <!-- Alpa / Bolos -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-rose-600">Alpa / Bolos (A)</span>
            <span class="w-6 h-6 rounded-md bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-bold text-slate-900">1.1%</span>
            <span class="text-xs font-medium text-rose-600">~14 siswa</span>
          </div>
          <p class="text-xs font-medium text-rose-600 pt-1">
            {{ atRiskStudents.length }} Siswa Butuh Pemanggilan
          </p>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 5. FITUR KHUSUS: DAFTAR SISWA BUTUH PEMANGGILAN (ALPA >= 3)  -->
      <!-- ============================================================ -->
      <div id="daftar-alpa-kritis" class="bg-white rounded-xl border border-rose-200 shadow-xs overflow-hidden">
        
        <!-- Table Header Toolbar -->
        <div class="p-4 sm:p-5 bg-rose-50/50 border-b border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="flex items-center gap-2">
              <h3 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">
                Daftar Siswa Butuh Pemanggilan Orang Tua (Alpa &ge; 3 Kali Minggu Ini)
              </h3>
              <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                {{ atRiskStudents.length }} Siswa
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Data akumulasi alpa dalam 7 hari terakhir yang membutuhkan penerbitan Surat Panggilan (SP).
            </p>
          </div>

          <Link
            href="/pemanggilan"
            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition cursor-pointer self-start sm:self-auto"
          >
            <span>Arsip Surat Panggilan</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </Link>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50/50 border-b border-slate-200">
              <tr>
                <th class="py-3 px-4 text-center w-12 text-[11px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama &amp; NISN Siswa</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kelas</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Akumulasi Alpa</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Riwayat Tanggal Alpa</th>
                <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kontak Orang Tua / Wali</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tindakan BK</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi Pemanggilan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="(student, index) in atRiskStudents" :key="student.id" class="hover:bg-rose-50/40 transition">
                <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ index + 1 }}</td>
                
                <!-- Nama & NISN -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ student.name }}</div>
                  <div class="text-xs text-slate-500 font-normal mt-0.5">NISN: {{ student.nisn }}</div>
                </td>

                <!-- Kelas -->
                <td class="py-3.5 px-4 text-center">
                  <span class="text-xs font-medium text-slate-600">
                    {{ student.class_name }}
                  </span>
                </td>

                <!-- Akumulasi Alpa -->
                <td class="py-3.5 px-4 text-center">
                  <span class="text-xs font-semibold text-rose-600">
                    {{ student.alpha_count }} Kali Alpa
                  </span>
                </td>

                <!-- Riwayat Tanggal -->
                <td class="py-3.5 px-4">
                  <div class="text-xs text-slate-700 font-medium">
                    {{ student.dates && student.dates.length ? student.dates.join(', ') : '3 hari berturut-turut' }}
                  </div>
                  <div class="text-[10px] text-rose-600 font-semibold mt-0.5">Tanpa Surat / Keterangan</div>
                </td>

                <!-- Kontak Wali -->
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-slate-800 text-xs">{{ student.parent_name }}</div>
                  <div class="text-xs text-slate-500 font-normal mt-0.5">{{ student.parent_phone }}</div>
                </td>

                <!-- Rekomendasi Tindakan -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="text-xs font-medium"
                    :class="student.alpha_count >= 4 ? 'text-rose-700' : 'text-amber-700'"
                  >
                    {{ student.risk_level || 'Peringatan (SP-1)' }}
                  </span>
                </td>

                <!-- Aksi Pemanggilan -->
                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <!-- Tombol Terbitkan Surat Panggilan (Modal Langsung) -->
                    <button
                      @click="openQuickSummonsModal(student)"
                      class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-md text-xs font-semibold inline-flex items-center gap-1 cursor-pointer transition shadow-2xs"
                      title="Terbitkan Surat Panggilan Orang Tua Sekarang"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      <span>Panggil Ortu</span>
                    </button>

                    <!-- Tombol Kirim WA Peringatan -->
                    <button
                      v-if="student.parent_phone"
                      @click="sendWhatsAppWarning(student)"
                      class="px-2 py-1 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 rounded-md text-xs font-semibold border border-slate-200 inline-flex items-center gap-1 cursor-pointer transition"
                      title="Kirim Notifikasi Peringatan via WhatsApp"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                      </svg>
                      <span>WA</span>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="atRiskStudents.length === 0">
                <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                  Tidak ada siswa yang terdeteksi alpa &ge; 3 kali pada minggu ini. Kedisiplinan terjaga dengan baik.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-3 bg-slate-50 border-t border-slate-200 text-slate-500 text-[11px] flex items-center justify-between">
          <span>* Berdasarkan SK Kesiswaan: Alpa 3 hari dalam seminggu diterbitkan SP-1, alpa &ge; 4 hari diterbitkan SP-2 / Sidang BK.</span>
          <Link href="/pemanggilan" class="text-blue-600 hover:underline font-semibold">
            Buka Modul Pemanggilan Orang Tua &rarr;
          </Link>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 6. LINE CHART COMPONENT                                      -->
      <!-- ============================================================ -->
      <DimensionComparisonLineChart />

      <!-- ============================================================ -->
      <!-- 7. DOWNLOAD / EXPORT BANNER                                  -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Ekspor Laporan Statistik &amp; Rekap Alpa</h4>
            <p class="text-xs text-slate-500">Unduh data perbandingan presensi bulanan lengkap dengan daftar siswa butuh pemanggilan.</p>
          </div>
        </div>

        <button
          @click="downloadReport"
          class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs sm:text-sm flex items-center justify-center gap-2 transition cursor-pointer shrink-0"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>Unduh Rekap Statistik</span>
        </button>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TERBITKAN SURAT PANGGILAN CEPAT (SP-1 / SP-2)         -->
      <!-- ============================================================ -->
      <div v-if="showSummonsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden">
          
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-bold text-slate-900 text-base">Terbitkan Surat Panggilan Orang Tua</h3>
              <p class="text-xs text-slate-500">Tindak lanjut presensi siswa alpa &ge; 3 kali</p>
            </div>
            <button @click="showSummonsModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitSummons" class="p-5 space-y-4 text-xs">
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl space-y-1">
              <div class="font-bold text-rose-900 text-sm">{{ selectedStudent.name }}</div>
              <div class="text-xs text-rose-700 font-mono">
                NISN: {{ selectedStudent.nisn }} &bull; Kelas: {{ selectedStudent.class_name }}
              </div>
              <div class="text-[11px] text-rose-600 font-semibold">
                Akumulasi Pelanggaran: {{ selectedStudent.alpha_count }} Hari Alpa (Tanpa Keterangan)
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahap Panggilan *</label>
                <select
                  v-model="summonsForm.stage"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                >
                  <option value="SP-1">SP-1 (Peringatan Awal)</option>
                  <option value="SP-2">SP-2 (Peringatan Keras)</option>
                  <option value="SP-3">SP-3 (Konferensi Kasus)</option>
                </select>
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Resmi *</label>
                <input
                  v-model="summonsForm.letterNumber"
                  type="text"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Alasan Pemanggilan *</label>
              <input
                v-model="summonsForm.caseTitle"
                type="text"
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Hari &amp; Tanggal Menghadap *</label>
                <input
                  v-model="summonsForm.meetingDate"
                  type="text"
                  placeholder="Contoh: Rabu, 04 Nov 2026"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Waktu Pertemuan *</label>
                <input
                  v-model="summonsForm.meetingTime"
                  type="text"
                  placeholder="Contoh: 08.30 WIB"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Orang Tua / Wali</label>
                <input
                  v-model="summonsForm.parentName"
                  type="text"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">No. WhatsApp Wali</label>
                <input
                  v-model="summonsForm.parentPhone"
                  type="text"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Tempat / Ruangan</label>
              <input
                v-model="summonsForm.room"
                type="text"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <button
                type="button"
                @click="goToFullSummonsPage"
                class="text-xs font-semibold text-rose-700 hover:underline cursor-pointer"
              >
                Buka di Modul Pemanggilan Penuh &rarr;
              </button>

              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="showSummonsModal = false"
                  class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-lg cursor-pointer inline-flex items-center gap-1.5 shadow-xs"
                >
                  <span>Terbitkan &amp; Kirim Surat</span>
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import DimensionComparisonLineChart from '../Components/DimensionComparisonLineChart.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  monthlyStats: {
    type: Object,
    default: () => ({})
  },
  totalStudents: {
    type: Number,
    default: 0
  },
  atRiskStudents: {
    type: Array,
    default: () => []
  }
})

const period = ref('tahun')
const showSummonsModal = ref(false)
const selectedStudent = ref({})

const summonsForm = ref({
  stage: 'SP-1',
  letterNumber: '',
  caseTitle: '',
  meetingDate: 'Rabu, 04 Nov 2026',
  meetingTime: '08.30 WIB',
  room: 'Ruang BK Gedung A',
  parentName: '',
  parentPhone: '',
  counselor: 'Dra. Hj. Nurul Hidayati, M.Pd'
})

const openQuickSummonsModal = (student) => {
  selectedStudent.value = student
  const randomNum = Math.floor(100 + Math.random() * 900)
  summonsForm.value = {
    stage: student.alpha_count >= 4 ? 'SP-2' : 'SP-1',
    letterNumber: `421.5/${randomNum}/BK/SMK-PGRI5/XI/2026`,
    caseTitle: `Tidak Hadir Tanpa Keterangan (Alpa) Selama ${student.alpha_count} Hari dalam 1 Minggu`,
    meetingDate: 'Rabu, 04 Nov 2026',
    meetingTime: '08.30 WIB',
    room: 'Ruang BK Gedung A',
    parentName: student.parent_name || `Bpk/Ibu ${student.name}`,
    parentPhone: student.parent_phone || '081234567890',
    counselor: 'Dra. Hj. Nurul Hidayati, M.Pd'
  }
  showSummonsModal.value = true
}

const submitSummons = () => {
  // Arahkan ke modul pemanggilan dengan query parameter agar tersimpan di riwayat pemanggilan
  showSummonsModal.value = false
  router.visit('/pemanggilan', {
    data: {
      nama: selectedStudent.value.name,
      nisn: selectedStudent.value.nisn,
      kelas: selectedStudent.value.class_name,
      alasan: summonsForm.value.caseTitle,
      stage: summonsForm.value.stage,
      wali: summonsForm.value.parentName,
      phone: summonsForm.value.parentPhone
    }
  })
}

const goToFullSummonsPage = () => {
  showSummonsModal.value = false
  router.visit('/pemanggilan', {
    data: {
      nama: selectedStudent.value.name,
      nisn: selectedStudent.value.nisn,
      kelas: selectedStudent.value.class_name,
      alasan: summonsForm.value.caseTitle,
      stage: summonsForm.value.stage,
      wali: summonsForm.value.parentName,
      phone: summonsForm.value.parentPhone
    }
  })
}

const sendWhatsAppWarning = (student) => {
  if (!student.parent_phone) return
  const phone = student.parent_phone.replace(/[^0-9]/g, '')
  const text = encodeURIComponent(
    `Yth. Bapak/Ibu ${student.parent_name} (Wali dari ${student.name} - ${student.class_name}), kami dari Tim Bimbingan & Konseling SMK PGRI 05 JEMBER menginformasikan bahwa ananda telah tercatat ALPA (tanpa keterangan) sebanyak ${student.alpha_count} kali dalam 1 minggu terakhir. Kami memohon kehadiran Bapak/Ibu di Ruang BK pada hari Rabu, 04 Nov 2026 pukul 08.30 WIB untuk koordinasi pembinaan. Terima kasih atas kerja samanya.`
  )
  window.open(`https://wa.me/${phone.startsWith('0') ? '62' + phone.slice(1) : phone}?text=${text}`, '_blank')
}

const downloadReport = () => {
  window.print()
}
</script>
