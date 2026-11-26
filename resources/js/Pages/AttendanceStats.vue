<template>
  <AppLayout active-tab="statistik" show-share>
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- 1. HEADER SECTION & FILTER WAKTU                              -->
      <!-- ============================================================ -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
          <h1 class="text-xl font-bold text-slate-900 tracking-tight">Statistik Kehadiran</h1>
          <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi presensi dan pemantauan disiplin siswa</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs">
          <!-- Search Input -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari siswa / NISN..."
              class="bg-white hover:bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition w-36 sm:w-44"
            />
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Grade Filter -->
          <div class="relative">
            <select
              v-model="selectedLevel"
              class="bg-white hover:bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-7 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
            >
              <option value="semua">Semua Tingkat</option>
              <option v-for="lvl in levelOptions" :key="lvl" :value="lvl">Kelas {{ lvl }}</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Major Filter -->
          <div class="relative">
            <select
              v-model="selectedMajor"
              class="bg-white hover:bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-7 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
            >
              <option value="semua">Semua Jurusan</option>
              <option v-for="mj in majorOptions" :key="mj" :value="mj">{{ mj }}</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <button
            @click="downloadReport"
            type="button"
            class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg inline-flex items-center gap-1.5 transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Unduh</span>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 2. METRIC CARDS (4 KARTU RINGKAS)                            -->
      <!-- ============================================================ -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Hadir -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">Hadir</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-slate-900">{{ metrics.hadirRate }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.hadirCount }} data</span>
          </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-amber-600 mb-1">Terlambat</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-amber-600">{{ metrics.terlambatRate }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.terlambatCount }} data</span>
          </div>
        </div>

        <!-- Izin & Sakit -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">Izin / Sakit</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-slate-900">{{ (Number(metrics.izinRate) + Number(metrics.sakitRate)).toFixed(0) }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.izinCount + metrics.sakitCount }} data</span>
          </div>
        </div>

        <!-- Alpa -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-rose-600 mb-1">Alpa</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-rose-600">{{ metrics.alphaRate }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.alphaCount }} data</span>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 3. DAFTAR STATISTIK SISWA & PEMANGGILAN                      -->
      <!-- ============================================================ -->
      <div id="daftar-alpa-kritis" class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Table Header Toolbar -->
        <div class="px-4 py-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <h3 class="text-sm font-semibold text-slate-900">
              Evaluasi &amp; Statistik Presensi Siswa
            </h3>

            <!-- Tab Switcher -->
            <div class="inline-flex bg-slate-100 p-0.5 rounded-lg border border-slate-200 text-xs">
              <button
                @click="activeTab = 'atensi'"
                type="button"
                class="px-2.5 py-1 rounded-md transition cursor-pointer"
                :class="activeTab === 'atensi' ? 'bg-white text-slate-900 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
              >
                Atensi BK ({{ atRiskList.length }})
              </button>
              <button
                @click="activeTab = 'semua'"
                type="button"
                class="px-2.5 py-1 rounded-md transition cursor-pointer"
                :class="activeTab === 'semua' ? 'bg-white text-slate-900 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
              >
                Semua Siswa ({{ displayedStudents.length }})
              </button>
              <button
                @click="activeTab = 'kritis'"
                type="button"
                class="px-2.5 py-1 rounded-md transition cursor-pointer"
                :class="activeTab === 'kritis' ? 'bg-white text-rose-700 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
              >
                Butuh SP ({{ criticalSummonsList.length }})
              </button>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Total: {{ displayedStudents.length }} siswa</span>
            <Link
              href="/pemanggilan"
              class="text-xs text-slate-600 hover:text-slate-900 font-medium transition inline-flex items-center gap-1"
            >
              <span>Arsip SP</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50/50 border-b border-slate-100">
              <tr>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Siswa</th>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Kelas</th>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Rekap Presensi</th>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Status Kedisiplinan</th>
                <th class="py-2.5 px-4 text-right text-[11px] font-medium text-slate-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="student in displayedStudents" :key="student.id" class="hover:bg-slate-50/60 transition">
                <!-- Siswa (Nama & NIS) -->
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900 text-xs">{{ student.name }}</div>
                  <div class="text-[11px] text-slate-400">NISN: {{ student.nis || student.nisn || '-' }}</div>
                </td>

                <!-- Kelas -->
                <td class="py-3 px-4 text-slate-600 font-medium">
                  {{ student.class_name }}
                </td>

                <!-- Rekap Presensi (H, T, S, I, A) -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2">
                    <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[11px]">
                      H: {{ student.hadir_count }}
                    </span>
                    <span v-if="student.terlambat_count > 0" class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-mono text-[11px]">
                      T: {{ student.terlambat_count }}
                    </span>
                    <span v-if="student.sakit_count > 0" class="inline-block px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-mono text-[11px]">
                      S: {{ student.sakit_count }}
                    </span>
                    <span v-if="student.izin_count > 0" class="inline-block px-1.5 py-0.5 rounded bg-cyan-50 text-cyan-700 font-mono text-[11px]">
                      I: {{ student.izin_count }}
                    </span>
                    <span v-if="student.alpha_count > 0" class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold font-mono text-[11px]">
                      A: {{ student.alpha_count }}
                    </span>
                    <span class="text-slate-400 text-[11px]">({{ student.rate }}%)</span>
                  </div>
                </td>

                <!-- Status Kedisiplinan -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-1.5">
                    <span
                      class="px-2 py-0.5 rounded text-[11px] font-medium"
                      :class="{
                        'bg-rose-50 text-rose-700 border border-rose-200': student.badge_type === 'critical',
                        'bg-amber-50 text-amber-700 border border-amber-200': student.badge_type === 'warning',
                        'bg-slate-100 text-slate-700 border border-slate-200': student.badge_type === 'attention',
                        'bg-emerald-50 text-emerald-700 border border-emerald-200': student.badge_type === 'good'
                      }"
                    >
                      {{ student.risk_level }}
                    </span>
                    <span v-if="student.violation_points > 0" class="text-[11px] text-slate-400">
                      {{ student.violation_points }} Poin
                    </span>
                  </div>
                </td>

                <!-- Aksi -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-1.5">
                    <button
                      @click="openQuickSummonsModal(student)"
                      class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded-md text-xs font-medium cursor-pointer transition"
                    >
                      Panggil Ortu
                    </button>
                    <button
                      v-if="student.parent_phone && student.parent_phone !== '-'"
                      @click="sendWhatsAppWarning(student)"
                      class="px-2 py-1 bg-white hover:bg-slate-50 text-slate-700 rounded-md text-xs font-medium border border-slate-200 cursor-pointer transition"
                      title="Kirim pesan WhatsApp ke Orang Tua"
                    >
                      WA
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="displayedStudents.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                  Tidak ditemukan data siswa yang sesuai dengan filter.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL: TERBITKAN SURAT PANGGILAN CEPAT (SP-1 / SP-2)         -->
      <!-- ============================================================ -->
      <div v-if="showSummonsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden">
          
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-bold text-slate-900 text-base">Terbitkan Surat Panggilan Orang Tua</h3>
              <p class="text-xs text-slate-500">Tindak lanjut presensi dan pembinaan siswa</p>
            </div>
            <button @click="showSummonsModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitSummons" class="p-5 space-y-4 text-xs">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
              <div class="font-bold text-slate-900 text-sm">{{ selectedStudent.name }}</div>
              <div class="text-xs text-slate-600 font-mono">
                NISN: {{ selectedStudent.nis || selectedStudent.nisn || '-' }} &bull; Kelas: {{ selectedStudent.class_name }}
              </div>
              <div class="text-[11px] text-slate-500">
                Catatan: {{ selectedStudent.alpha_count > 0 ? selectedStudent.alpha_count + ' Hari Alpa' : (selectedStudent.terlambat_count > 0 ? selectedStudent.terlambat_count + ' Kali Terlambat' : 'Koordinasi Pembinaan BK') }}
                <span v-if="selectedStudent.violation_points > 0"> &bull; {{ selectedStudent.violation_points }} Poin Pelanggaran</span>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahap Panggilan *</label>
                <input
                  v-model="summonsForm.stage"
                  type="text"
                  placeholder="Contoh: SP-1, SP-2..."
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Resmi *</label>
                <input
                  v-model="summonsForm.letterNumber"
                  type="text"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Alasan Pemanggilan *</label>
              <input
                v-model="summonsForm.caseTitle"
                type="text"
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
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
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Waktu Pertemuan *</label>
                <input
                  v-model="summonsForm.meetingTime"
                  type="text"
                  placeholder="Contoh: 08.30 WIB"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Orang Tua / Wali</label>
                <input
                  v-model="summonsForm.parentName"
                  type="text"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">No. WhatsApp Wali</label>
                <input
                  v-model="summonsForm.parentPhone"
                  type="text"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Tempat / Ruangan</label>
              <input
                v-model="summonsForm.room"
                type="text"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <button
                type="button"
                @click="goToFullSummonsPage"
                class="text-xs font-semibold text-slate-700 hover:underline cursor-pointer inline-flex items-center gap-1"
              >
                <span>Buka di Modul Pemanggilan Penuh</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
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
                  class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg cursor-pointer inline-flex items-center gap-1.5 shadow-xs"
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
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  monthlyStats: {
    type: Object,
    default: () => ({})
  },
  summaryMetrics: {
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
  },
  allStudents: {
    type: Array,
    default: () => []
  },
  availableMajors: {
    type: Array,
    default: () => []
  },
  availableLevels: {
    type: Array,
    default: () => []
  }
})

const period = ref('bulan')
const selectedLevel = ref('semua')
const selectedMajor = ref('semua')
const searchQuery = ref('')
const activeTab = ref('atensi') // 'atensi', 'semua', 'kritis'

const showSummonsModal = ref(false)
const selectedStudent = ref({})

const getDefaultMeetingDate = () => {
  const d = new Date()
  d.setDate(d.getDate() + 2)
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' })
}

const summonsForm = ref({
  stage: 'SP-1',
  letterNumber: '',
  caseTitle: '',
  meetingDate: getDefaultMeetingDate(),
  meetingTime: '08.30 WIB',
  room: 'Ruang BK Gedung A',
  parentName: '',
  parentPhone: '',
  counselor: 'Guru BK'
})

const metrics = computed(() => {
  return {
    totalPresensi: props.summaryMetrics?.totalPresensi || 0,
    hadirCount: props.summaryMetrics?.hadirCount || 0,
    terlambatCount: props.summaryMetrics?.terlambatCount || 0,
    sakitCount: props.summaryMetrics?.sakitCount || 0,
    izinCount: props.summaryMetrics?.izinCount || 0,
    alphaCount: props.summaryMetrics?.alphaCount || 0,
    hadirRate: props.summaryMetrics?.hadirRate !== undefined ? props.summaryMetrics.hadirRate : 0,
    terlambatRate: props.summaryMetrics?.terlambatRate !== undefined ? props.summaryMetrics.terlambatRate : 0,
    sakitRate: props.summaryMetrics?.sakitRate !== undefined ? props.summaryMetrics.sakitRate : 0,
    izinRate: props.summaryMetrics?.izinRate !== undefined ? props.summaryMetrics.izinRate : 0,
    alphaRate: props.summaryMetrics?.alphaRate !== undefined ? props.summaryMetrics.alphaRate : 0,
  }
})

const majorOptions = computed(() => {
  return props.availableMajors || []
})

const levelOptions = computed(() => {
  if (props.availableLevels && props.availableLevels.length > 0) {
    return props.availableLevels
  }
  return ['10', '11', '12']
})

// Base dataset: allStudents (fallback to atRiskStudents if not provided)
const rawStudentsList = computed(() => {
  if (props.allStudents && props.allStudents.length > 0) {
    return props.allStudents
  }
  return props.atRiskStudents || []
})

// Siswa yang butuh atensi khusus
const atRiskList = computed(() => {
  return rawStudentsList.value.filter(s => {
    return (s.alpha_count > 0) || (s.terlambat_count > 0) || (s.violation_points > 0) || (s.badge_type !== 'good')
  })
})

// Siswa butuh panggilan SP
const criticalSummonsList = computed(() => {
  return rawStudentsList.value.filter(s => {
    return (s.alpha_count >= 3) || (s.violation_points >= 50) || (s.badge_type === 'critical')
  })
})

// Siswa yang ditampilkan berdasarkan tab dan filter
const displayedStudents = computed(() => {
  let list = rawStudentsList.value

  if (activeTab.value === 'atensi') {
    list = atRiskList.value
  } else if (activeTab.value === 'kritis') {
    list = criticalSummonsList.value
  }

  // Filter Tingkat
  if (selectedLevel.value !== 'semua') {
    list = list.filter(s => {
      const lvl = String(s.level || '')
      const className = String(s.class_name || '')
      if (selectedLevel.value === '10') return lvl === '10' || className.startsWith('X ') || className.startsWith('10 ')
      if (selectedLevel.value === '11') return lvl === '11' || className.startsWith('XI ') || className.startsWith('11 ')
      if (selectedLevel.value === '12') return lvl === '12' || className.startsWith('XII ') || className.startsWith('12 ')
      return true
    })
  }

  // Filter Jurusan
  if (selectedMajor.value !== 'semua') {
    list = list.filter(s => {
      const mj = String(s.major || '').toLowerCase()
      const className = String(s.class_name || '').toLowerCase()
      const target = selectedMajor.value.toLowerCase()
      return mj === target || className.includes(target)
    })
  }

  // Filter Pencarian
  if (searchQuery.value && searchQuery.value.trim() !== '') {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(s => {
      const name = String(s.name || '').toLowerCase()
      const nis = String(s.nis || s.nisn || '').toLowerCase()
      const className = String(s.class_name || '').toLowerCase()
      return name.includes(q) || nis.includes(q) || className.includes(q)
    })
  }

  return list
})

const openQuickSummonsModal = (student) => {
  selectedStudent.value = student
  const randomNum = Math.floor(100 + Math.random() * 900)
  summonsForm.value = {
    stage: student.alpha_count >= 4 ? 'SP-2' : 'SP-1',
    letterNumber: `421.5/${randomNum}/BK/SMK-PGRI5/${new Date().getFullYear()}`,
    caseTitle: `Tidak Hadir Tanpa Keterangan (Alpa) Selama ${student.alpha_count} Hari dalam 1 Minggu`,
    meetingDate: getDefaultMeetingDate(),
    meetingTime: '08.30 WIB',
    room: 'Ruang BK Gedung A',
    parentName: student.parent_name || `Bpk/Ibu ${student.name}`,
    parentPhone: student.parent_phone || '081234567890',
    counselor: 'Guru BK'
  }
  showSummonsModal.value = true
}

const isSubmittingSummons = ref(false)

const submitSummons = () => {
  if (!selectedStudent.value?.id) return
  isSubmittingSummons.value = true

  router.post('/pelanggaran/panggilan', {
    student_id: selectedStudent.value.id,
    stage: summonsForm.value.stage,
    case_title: summonsForm.value.caseTitle,
    meeting_date: summonsForm.value.meetingDate,
    meeting_time: summonsForm.value.meetingTime,
    room: summonsForm.value.room,
    parent_name: summonsForm.value.parentName,
    parent_phone: summonsForm.value.parentPhone,
    counselor: summonsForm.value.counselor
  }, {
    preserveScroll: true,
    onSuccess: () => {
      isSubmittingSummons.value = false
      showSummonsModal.value = false
      router.visit('/pemanggilan')
    },
    onError: () => {
      isSubmittingSummons.value = false
      alert('Gagal menerbitkan surat panggilan.')
    }
  })
}

const goToFullSummonsPage = () => {
  showSummonsModal.value = false
  router.visit('/pemanggilan')
}

const sendWhatsAppWarning = (student) => {
  if (!student.parent_phone || student.parent_phone === '-') {
    alert('Nomor telepon orang tua belum terdata untuk siswa ini.')
    return
  }
  const phone = student.parent_phone.replace(/[^0-9]/g, '')
  const parent = student.parent_name || 'Orang Tua / Wali'
  const text = encodeURIComponent(
    `Yth. Bapak/Ibu ${parent} (Wali dari ${student.name} - ${student.class_name}), kami dari Tim Bimbingan & Konseling SMK PGRI 05 JEMBER menginformasikan bahwa ananda telah tercatat ALPA (tanpa keterangan) sebanyak ${student.alpha_count} kali. Mohon kesediaan Bapak/Ibu untuk berkoordinasi dengan pihak sekolah di Ruang BK demi pembinaan ananda. Terima kasih atas perhatian dan kerja samanya.`
  )
  window.open(`https://wa.me/${phone.startsWith('0') ? '62' + phone.slice(1) : phone}?text=${text}`, '_blank')
}

const downloadReport = () => {
  window.print()
}
</script>
