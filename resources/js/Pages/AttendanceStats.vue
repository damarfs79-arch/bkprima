<template>
  <AppLayout active-tab="statistik" show-share>
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header Title & Period Filter (Ukuran Besar & Jelas Dibaca Guru) -->
      <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <p class="text-xs font-black text-blue-700 tracking-wider uppercase mb-1 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-blue-600"></span>
              PORTAL GURU BK • HADIRMU PRESENSI
            </p>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <span>Statistik Kehadiran Siswa</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 font-medium mt-1">
              Rekapitulasi lengkap persentase kehadiran siswa SMK PGRI 05 JEMBER untuk pemantauan kedisiplinan.
            </p>
          </div>

          <!-- Toggle Period (1 Minggu vs 1 Bulan vs 12 Bulan) -->
          <div class="flex bg-slate-100 p-1.5 rounded-2xl self-start sm:self-auto overflow-x-auto max-w-full shadow-inner border border-slate-200">
            <button
              @click="period = 'minggu'"
              class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition cursor-pointer shrink-0"
              :class="period === 'minggu' ? 'bg-white text-blue-700 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900'"
            >
              1 Minggu
            </button>
            <button
              @click="period = 'bulan'"
              class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition cursor-pointer shrink-0"
              :class="period === 'bulan' ? 'bg-white text-blue-700 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900'"
            >
              1 Bulan
            </button>
            <button
              @click="period = 'tahun'"
              class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition cursor-pointer shrink-0"
              :class="period === 'tahun' ? 'bg-white text-blue-700 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900'"
            >
              12 Bulan (Komparasi)
            </button>
          </div>
        </div>

        <!-- Integrated Filters Strip (Label Jelas & Dropdown Nyaman Dipilih) -->
        <div class="pt-4 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3.5">
          <!-- Month Filter -->
          <div class="relative">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Periode Bulan</label>
            <div class="relative">
              <select class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm font-black text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600 focus:bg-white appearance-none cursor-pointer transition">
                <option>Februari 2026</option>
                <option>Januari 2026</option>
                <option>Desember 2025</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Grade Filter -->
          <div class="relative">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tingkat Kelas</label>
            <div class="relative">
              <select class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm font-black text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600 focus:bg-white appearance-none cursor-pointer transition">
                <option>Semua Tingkat (Kelas 10, 11, 12)</option>
                <option>Kelas 10 (X)</option>
                <option>Kelas 11 (XI)</option>
                <option>Kelas 12 (XII)</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Major Filter -->
          <div class="relative">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jurusan / Keahlian</label>
            <div class="relative">
              <select class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm font-black text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600 focus:bg-white appearance-none cursor-pointer transition">
                <option>Semua Jurusan</option>
                <option>Rekayasa Perangkat Lunak (RPL)</option>
                <option>Teknik Sepeda Motor (TSM)</option>
                <option>Desain Komunikasi Visual (DKV)</option>
                <option>Teknik Komputer Jaringan (TKJ)</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hero Card: Rata-rata Tingkat Kehadiran Sekolah (Huruf Besar & Sangat Kontras) -->
      <div class="bg-gradient-to-r from-blue-800 via-blue-700 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 space-y-3.5">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
              <span class="p-2 bg-white/20 rounded-xl text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </span>
              <h2 class="text-sm sm:text-base font-black tracking-wide uppercase text-blue-100">RATA-RATA TINGKAT KEHADIRAN SEKOLAH</h2>
            </div>
            <span class="px-3.5 py-1.5 bg-emerald-500/30 text-emerald-200 border border-emerald-400/40 rounded-full text-xs sm:text-sm font-black flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
              </svg>
              +2.4% vs Bulan Lalu
            </span>
          </div>

          <div class="flex flex-baseline items-baseline gap-3.5">
            <span class="text-5xl sm:text-6xl font-black tracking-tight text-white">94.8%</span>
            <span class="text-sm sm:text-base text-blue-200 font-bold bg-white/10 px-3 py-1 rounded-xl">~1.218 hadir dari 1.285 siswa / hari</span>
          </div>

          <p class="text-sm sm:text-base text-blue-100 leading-relaxed max-w-3xl font-medium">
            Total populasi 1.285 siswa di 36 rombel kelas. Rata-rata kehadiran berada pada kategori <strong class="text-white underline decoration-emerald-400 underline-offset-4 font-black">Sangat Baik</strong> (Melampaui Target Standar Disiplin 90%).
          </p>
        </div>
      </div>

      <!-- 4 Grid Metric Cards (Siswa Hadir, Sakit, Izin, Alpa/Bolos) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Hadir -->
        <div class="bg-white rounded-3xl p-5 border-2 border-emerald-200 shadow-xs space-y-1 relative overflow-hidden">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-black text-slate-800">Siswa Hadir</span>
            <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl sm:text-4xl font-black text-slate-900">94.8%</span>
            <span class="text-xs sm:text-sm font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">1.218 siswa</span>
          </div>
          <p class="text-xs sm:text-sm font-bold text-emerald-700 flex items-center gap-1.5 pt-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            Tepat Waktu &amp; Tertib
          </p>
        </div>

        <!-- Sakit -->
        <div class="bg-white rounded-3xl p-5 border-2 border-amber-200 shadow-xs space-y-1 relative overflow-hidden">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-black text-slate-800">Sakit (S)</span>
            <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-black text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl sm:text-4xl font-black text-slate-900">2.3%</span>
            <span class="text-xs sm:text-sm font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">~30 siswa</span>
          </div>
          <p class="text-xs sm:text-sm font-bold text-amber-700 flex items-center gap-1.5 pt-1.5">
            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
            Surat Dokter Terverifikasi
          </p>
        </div>

        <!-- Izin -->
        <div class="bg-white rounded-3xl p-5 border-2 border-blue-200 shadow-xs space-y-1 relative overflow-hidden">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-black text-slate-800">Izin (I)</span>
            <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-black text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl sm:text-4xl font-black text-slate-900">1.8%</span>
            <span class="text-xs sm:text-sm font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">~23 siswa</span>
          </div>
          <p class="text-xs sm:text-sm font-bold text-blue-700 flex items-center gap-1.5 pt-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            Dispensasi &amp; Keperluan Keluarga
          </p>
        </div>

        <!-- Alpa / Bolos -->
        <div class="bg-white rounded-3xl p-5 border-2 border-rose-300 shadow-xs space-y-1 relative overflow-hidden">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-black text-rose-700">Alpa / Bolos (A)</span>
            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-black text-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl sm:text-4xl font-black text-rose-700">1.1%</span>
            <span class="text-xs sm:text-sm font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">~14 siswa</span>
          </div>
          <p class="text-xs sm:text-sm font-black text-rose-700 flex items-center gap-1.5 pt-1.5">
            <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
            Perlu Tindak Lanjut Guru BK
          </p>
        </div>
      </div>

      <!-- Section: Redesigned Line Chart Component for Teachers -->
      <DimensionComparisonLineChart />

      <!-- Download Action & Export Banner (NO EMOJIS - Huruf Jelas & Tombol Besar) -->
      <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
          <div class="w-13 h-13 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h4 class="text-base sm:text-lg font-black text-slate-900">Ekspor Laporan Statistik Komparasi</h4>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">Unduh data perbandingan presensi bulanan lengkap dengan grafik komparasi resmi.</p>
          </div>
        </div>

        <button
          @click="downloadReport"
          class="w-full sm:w-auto px-7 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl shadow-md text-sm sm:text-base flex items-center justify-center gap-2.5 transition cursor-pointer shrink-0"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>Unduh Rekap Statistik (PDF/Excel)</span>
        </button>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import DimensionComparisonLineChart from '../Components/DimensionComparisonLineChart.vue'
import { ref } from 'vue'

const period = ref('tahun')

const downloadReport = () => {
  alert('Rekap statistik komparasi format PDF/Excel berhasil diunduh!')
}
</script>
