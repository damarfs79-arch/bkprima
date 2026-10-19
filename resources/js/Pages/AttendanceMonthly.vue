<template>
  <AppLayout activeTab="absensi" :showBack="true">
    <Head title="Rekapitulasi Presensi Bulanan Siswa" />

    <!-- Container Utama -->
    <div class="space-y-6">
      
      <!-- ========================================== -->
      <!-- 1. HEADER & CONTROLS (Screen Only)         -->
      <!-- ========================================== -->
      <div class="print:hidden bg-white rounded-3xl p-6 border border-gray-100 shadow-sm transition">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                Laporan Dinas & Wali Kelas
              </span>
              <span class="text-xs font-semibold text-gray-400">• {{ monthName }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
              Rekapitulasi Presensi Bulanan
            </h1>
            <p class="text-xs text-gray-500 mt-1">
              Matriks absensi harian akumulatif per bulan (1-31) siap cetak format resmi A4 Landscape.
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2 flex-wrap">
            <Link
              href="/absensi"
              class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-slate-50 transition flex items-center gap-1.5 cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Presensi Harian
            </Link>

            <button
              type="button"
              @click="printDocument"
              class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition flex items-center gap-2 cursor-pointer active:scale-95"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Cetak / Print Rekap A4
            </button>
          </div>
        </div>

        <!-- Filter Bar -->
        <div class="mt-6 pt-5 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Filter Kelas -->
          <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pilih Rombel / Kelas</label>
            <div class="relative">
              <select
                v-model="currentClass"
                @change="applyFilter"
                class="w-full bg-slate-50 border border-gray-200 text-gray-800 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer appearance-none"
              >
                <option v-for="cls in classes" :key="cls" :value="cls">{{ cls }}</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Filter Bulan -->
          <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Bulan & Tahun</label>
            <input
              type="month"
              v-model="currentMonth"
              @change="applyFilter"
              class="w-full bg-slate-50 border border-gray-200 text-gray-800 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer"
            />
          </div>

          <!-- Quick Stat 1 -->
          <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-3 flex items-center justify-between">
            <div>
              <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Total Siswa</p>
              <p class="text-xl font-black text-gray-900 mt-0.5">{{ classStats.totalStudents }} Siswa</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-blue-600/10 text-blue-600 flex items-center justify-center font-bold text-xs">
              {{ selectedClass }}
            </div>
          </div>

          <!-- Quick Stat 2 -->
          <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-3 flex items-center justify-between">
            <div>
              <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Rata-rata Hadir</p>
              <p class="text-xl font-black text-emerald-600 mt-0.5">{{ classStats.avgPercentage }}%</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-600/10 text-emerald-600 flex items-center justify-center font-bold text-xs">
              {{ classStats.effectiveDays }} Hari
            </div>
          </div>
        </div>

        <!-- Legend / Keterangan Kode -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-3 flex-wrap text-xs text-gray-600">
          <span class="font-bold text-gray-400 text-[11px] uppercase tracking-wider">Keterangan:</span>
          <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-700 font-black text-[10px] flex items-center justify-center">H</span>
            <span class="text-gray-600 font-medium">Hadir</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-700 font-black text-[10px] flex items-center justify-center">T</span>
            <span class="text-gray-600 font-medium">Terlambat</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 font-black text-[10px] flex items-center justify-center">S</span>
            <span class="text-gray-600 font-medium">Sakit</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-md bg-purple-100 text-purple-700 font-black text-[10px] flex items-center justify-center">I</span>
            <span class="text-gray-600 font-medium">Izin</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-md bg-rose-100 text-rose-700 font-black text-[10px] flex items-center justify-center">A</span>
            <span class="text-gray-600 font-medium">Alpa</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-md bg-slate-200 text-slate-500 font-black text-[10px] flex items-center justify-center">L</span>
            <span class="text-gray-400 font-medium">Libur Akhir Pekan</span>
          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- 2. KOP SURAT RESMI (Print View Only)       -->
      <!-- ========================================== -->
      <div class="hidden print:block mb-4">
        <div class="flex items-center justify-between border-b-4 border-double border-gray-900 pb-3">
          <div class="w-20 h-20 shrink-0 flex items-center justify-center">
            <img src="/logo-smk.png" alt="Logo SMK" class="w-20 h-20 object-contain" />
          </div>
          <div class="text-center flex-1 px-4">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-800">Yayasan Pembina Lembaga Pendidikan PGRI Jawa Timur</p>
            <h2 class="text-xl font-black text-gray-900 tracking-wide uppercase">SMK PGRI 05 JEMBER</h2>
            <p class="text-[10px] text-gray-600 font-medium">NPSN: 20523821 | Status Akreditasi: A (Unggul)</p>
            <p class="text-[10px] text-gray-600 font-medium">{{ schoolInfo.address }}</p>
            <p class="text-[10px] text-gray-600 font-medium">Email: info@smkpgri05jember.sch.id | Website: www.smkpgri05jember.sch.id</p>
          </div>
          <div class="w-20 h-20 shrink-0"></div>
        </div>

        <div class="text-center my-3">
          <h3 class="text-base font-black uppercase text-gray-900 underline underline-offset-4">
            REKAPITULASI PRESENSI KEHADIRAN SISWA BULANAN
          </h3>
          <p class="text-xs font-bold text-gray-700 mt-1">
            BULAN: <span class="uppercase">{{ monthName }}</span> | ROMBEL: {{ selectedClass }} | TAHUN AJARAN 2024/2025
          </p>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- 3. MATRIX TABLE (Screen & Print)           -->
      <!-- ========================================== -->
      <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden print:border-none print:shadow-none print:rounded-none">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-[11px] print:text-[9.5px]">
            <thead>
              <!-- Baris Header 1: Kategori Kolom -->
              <tr class="bg-slate-100 text-gray-700 font-extrabold uppercase border-b border-gray-200 print:bg-gray-100">
                <th rowspan="2" class="py-2.5 px-2 text-center border-r border-gray-200 w-8">No</th>
                <th rowspan="2" class="py-2.5 px-2 border-r border-gray-200 w-24">NISN</th>
                <th rowspan="2" class="py-2.5 px-3 border-r border-gray-200 min-w-[180px] print:min-w-[150px]">Nama Lengkap Siswa</th>
                <th rowspan="2" class="py-2.5 px-1.5 text-center border-r border-gray-200 w-8">L/P</th>
                
                <!-- Kolom Tanggal Matriks (1 s/d 31) -->
                <th :colspan="days.length" class="py-1 px-1 text-center border-r border-gray-200 bg-blue-50/70 text-blue-800 print:bg-gray-200">
                  Tanggal Presensi Bulan {{ monthName }}
                </th>

                <!-- Kolom Akumulasi Rekap -->
                <th colspan="5" class="py-1 px-1 text-center border-r border-gray-200 bg-amber-50/70 text-amber-900 print:bg-gray-200">
                  Rekapitulasi
                </th>
                <th rowspan="2" class="py-2.5 px-2 text-center bg-emerald-50 text-emerald-900 print:bg-gray-100 w-12 font-black">%</th>
              </tr>

              <!-- Baris Header 2: Tanggal 1 s.d 31 & Kode H/T/S/I/A -->
              <tr class="bg-slate-50 text-gray-600 font-bold border-b border-gray-200 text-center text-[10px] print:text-[8.5px]">
                <!-- Number of days -->
                <th
                  v-for="d in days"
                  :key="'th-day-' + d.day"
                  class="py-1 px-0.5 border-r border-gray-200 min-w-[20px] print:min-w-[16px]"
                  :class="d.isWeekend ? 'bg-slate-200 text-rose-600 font-black' : ''"
                  :title="d.dayName + ', ' + d.fullDate"
                >
                  <div>{{ d.day }}</div>
                  <div class="text-[7.5px] uppercase font-semibold text-gray-400 print:hidden">{{ d.dayName.slice(0, 1) }}</div>
                </th>

                <!-- Rekap Titles -->
                <th class="py-1 px-1.5 border-r border-gray-200 text-emerald-700 bg-emerald-50/40 w-7">H</th>
                <th class="py-1 px-1.5 border-r border-gray-200 text-amber-700 bg-amber-50/40 w-7">T</th>
                <th class="py-1 px-1.5 border-r border-gray-200 text-blue-700 bg-blue-50/40 w-7">S</th>
                <th class="py-1 px-1.5 border-r border-gray-200 text-purple-700 bg-purple-50/40 w-7">I</th>
                <th class="py-1 px-1.5 border-r border-gray-200 text-rose-700 bg-rose-50/40 w-7">A</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 font-medium">
              <tr
                v-for="(st, idx) in students"
                :key="st.id"
                class="hover:bg-blue-50/40 transition print:hover:bg-transparent"
                :class="idx % 2 === 1 ? 'bg-slate-50/40' : 'bg-white'"
              >
                <!-- No -->
                <td class="py-1.5 px-2 text-center text-gray-500 font-bold border-r border-gray-200">{{ idx + 1 }}</td>
                <!-- NISN -->
                <td class="py-1.5 px-2 text-gray-600 font-mono text-[10px] border-r border-gray-200">{{ st.nisn }}</td>
                <!-- Nama Siswa -->
                <td class="py-1.5 px-3 text-gray-900 font-bold border-r border-gray-200 whitespace-nowrap">
                  {{ st.name }}
                </td>
                <!-- L/P -->
                <td class="py-1.5 px-1.5 text-center text-gray-600 border-r border-gray-200">{{ st.gender }}</td>

                <!-- Daily Status Cells -->
                <td
                  v-for="d in days"
                  :key="'cell-' + st.id + '-' + d.day"
                  class="py-1 px-0.5 text-center border-r border-gray-200 text-[10px] print:text-[8px] font-black"
                  :class="getCellBg(st.daily[d.day]?.code, d.isWeekend)"
                >
                  <span>{{ st.daily[d.day]?.code || '-' }}</span>
                </td>

                <!-- Summary Cells -->
                <td class="py-1.5 px-1 text-center font-bold text-emerald-700 bg-emerald-50/20 border-r border-gray-200">
                  {{ st.summary.hadir }}
                </td>
                <td class="py-1.5 px-1 text-center font-bold text-amber-700 bg-amber-50/20 border-r border-gray-200">
                  {{ st.summary.terlambat }}
                </td>
                <td class="py-1.5 px-1 text-center font-bold text-blue-700 bg-blue-50/20 border-r border-gray-200">
                  {{ st.summary.sakit }}
                </td>
                <td class="py-1.5 px-1 text-center font-bold text-purple-700 bg-purple-50/20 border-r border-gray-200">
                  {{ st.summary.izin }}
                </td>
                <td class="py-1.5 px-1 text-center font-bold text-rose-700 bg-rose-50/20 border-r border-gray-200">
                  {{ st.summary.alpa }}
                </td>

                <!-- Percentage -->
                <td
                  class="py-1.5 px-2 text-center font-black"
                  :class="st.summary.percentage >= 90 ? 'text-emerald-600 bg-emerald-50/40' : (st.summary.percentage >= 75 ? 'text-amber-600 bg-amber-50/40' : 'text-rose-600 bg-rose-50/40')"
                >
                  {{ st.summary.percentage }}%
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="students.length === 0">
                <td :colspan="days.length + 10" class="py-10 text-center text-gray-400 font-bold">
                  Belum ada data siswa atau presensi untuk kelas {{ selectedClass }} pada bulan {{ monthName }}.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- 4. LEMBAR TANDA TANGAN (Print View Only)   -->
      <!-- ========================================== -->
      <div class="hidden print:block mt-8 pt-4 text-xs font-medium text-gray-800">
        <div class="flex justify-between items-start text-center px-6">
          <!-- Kolom Guru BK -->
          <div class="w-64">
            <p>Mengetahui,</p>
            <p class="font-bold">Guru Bimbingan Konseling</p>
            <div class="h-20"></div>
            <p class="font-black underline">{{ schoolInfo.guruBk }}</p>
            <p class="text-[10px]">NIP. 19740512 200212 2 003</p>
          </div>

          <!-- Kolom Kepala Sekolah -->
          <div class="w-64">
            <p>Menyetujui,</p>
            <p class="font-bold">Kepala SMK PGRI 05 Jember</p>
            <div class="h-20"></div>
            <p class="font-black underline">{{ schoolInfo.kepalaSekolah }}</p>
            <p class="text-[10px]">NIP. 19680315 199403 1 008</p>
          </div>

          <!-- Kolom Wali Kelas -->
          <div class="w-64">
            <p>Jember, {{ printDate }}</p>
            <p class="font-bold">Wali Kelas {{ selectedClass }}</p>
            <div class="h-20"></div>
            <p class="font-black underline">{{ schoolInfo.waliKelas }}</p>
            <p class="text-[10px]">NIP. - / GTT</p>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

const props = defineProps({
  students: {
    type: Array,
    default: () => []
  },
  days: {
    type: Array,
    default: () => []
  },
  classes: {
    type: Array,
    default: () => []
  },
  selectedClass: {
    type: String,
    default: 'X RPL 1'
  },
  selectedMonth: {
    type: String,
    default: ''
  },
  monthName: {
    type: String,
    default: ''
  },
  classStats: {
    type: Object,
    default: () => ({ totalStudents: 0, effectiveDays: 0, avgPercentage: 0 })
  },
  schoolInfo: {
    type: Object,
    default: () => ({})
  }
})

const currentClass = ref(props.selectedClass)
const currentMonth = ref(props.selectedMonth)

const printDate = new Date().toLocaleDateString('id-ID', {
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})

const applyFilter = () => {
  router.get('/absensi/bulanan', {
    class_name: currentClass.value,
    month: currentMonth.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const printDocument = () => {
  window.print()
}

const getCellBg = (code, isWeekend) => {
  if (isWeekend) return 'bg-slate-100 text-slate-400 font-normal'
  switch (code) {
    case 'H': return 'text-emerald-700 bg-emerald-50/40'
    case 'T': return 'text-amber-800 bg-amber-100/60 font-bold'
    case 'S': return 'text-blue-700 bg-blue-100/60 font-bold'
    case 'I': return 'text-purple-700 bg-purple-100/60 font-bold'
    case 'A': return 'text-rose-700 bg-rose-100 font-black'
    case 'L': return 'bg-slate-100 text-slate-400 font-normal'
    default: return 'text-gray-400'
  }
}
</script>

<style>
@media print {
  @page {
    size: A4 landscape;
    margin: 8mm 8mm 8mm 8mm;
  }
  body {
    background-color: #ffffff !important;
    font-size: 9.5px;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
}
</style>
