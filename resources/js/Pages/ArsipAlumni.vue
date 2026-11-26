<template>
  <AppLayout active-tab="alumni">
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- 1. HEADER SECTION & EXPORT / SUMMARY ACTION                  -->
      <!-- ============================================================ -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
            Arsip Siswa Lulus
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            Basis data riwayat alumni dan arsip rekam jejak kedisiplinan SMK PGRI 5 Jember
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="printSummary"
            class="interactive-btn inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-lg text-xs font-semibold shadow-2xs transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Rekap</span>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 2. KPI METRIC SUMMARY CARDS                                  -->
      <!-- ============================================================ -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">Total Alumni Terdata</div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ kpi.totalAlumni }}</span>
            <span class="text-xs text-slate-500">alumni</span>
          </div>
          <div class="text-[11px] text-slate-400 mt-1">Tersimpan dalam basis data arsip</div>
        </div>

        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">
            {{ filters.tahun === 'ALL' || !filters.tahun ? 'Tahun Lulus Terpilih (Semua)' : 'Tahun Lulus ' + filters.tahun }}
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-bold text-blue-600">{{ kpi.selectedYearCount }}</span>
            <span class="text-xs text-slate-500">siswa</span>
          </div>
          <div class="text-[11px] text-slate-400 mt-1">Sesuai filter kriteria aktif</div>
        </div>

        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">Alumni Tanpa Catatan Pelanggaran</div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl sm:text-3xl font-bold text-emerald-600">{{ kpi.cleanAlumniCount }}</span>
            <span class="text-xs text-slate-500">alumni (0 poin)</span>
          </div>
          <div class="text-[11px] text-emerald-600 mt-1">Memenuhi kualifikasi rekomendasi SKKB</div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 3. FILTER STRIP CARD                                         -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
          <!-- Search Input -->
          <div class="relative sm:col-span-2 lg:col-span-1">
            <input
              v-model="searchQuery"
              @input="handleSearchDebounced"
              type="text"
              placeholder="Cari nama atau NISN alumni..."
              class="w-full bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Graduation Year Filter -->
          <div class="relative">
            <select
              v-model="selectedYear"
              @change="applyFilter"
              class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
            >
              <option value="ALL">Semua Tahun Lulus</option>
              <option v-for="y in options.years" :key="y" :value="y">Lulus {{ y }}</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Major Filter -->
          <div class="relative">
            <select
              v-model="selectedMajor"
              @change="applyFilter"
              class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
            >
              <option value="ALL">Semua Jurusan</option>
              <option v-for="m in options.majors" :key="m" :value="m">{{ m }}</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Rombel Filter -->
          <div class="relative">
            <select
              v-model="selectedRombel"
              @change="applyFilter"
              class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition"
            >
              <option value="ALL">Semua Rombel</option>
              <option v-for="r in options.rombels" :key="r" :value="r">Rombel {{ r }}</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 4. READ-ONLY DATA TABLE OF GRADUATED STUDENTS                -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50/75 border-b border-slate-200">
              <tr>
                <th class="py-3 px-4 text-center w-12 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">No</th>
                <th class="py-3 px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Nama &amp; NISN Alumni</th>
                <th class="py-3 px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Kelas Terakhir</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tahun Lulus</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Poin Akhir</th>
                <th class="py-3 px-4 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Riwayat SP</th>
                <th class="py-3 px-4 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="(student, index) in alumni.data" :key="student.id" class="hover:bg-slate-50/60 transition">
                <td class="py-3.5 px-4 text-center font-medium text-slate-400">
                  {{ (alumni.current_page - 1) * alumni.per_page + index + 1 }}
                </td>

                <!-- Nama & NIS -->
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ student.name }}</div>
                  <div class="text-[11px] text-slate-400 font-mono mt-0.5">NISN: {{ student.nis || student.nisn }}</div>
                </td>

                <!-- Kelas Terakhir -->
                <td class="py-3.5 px-4">
                  <span class="text-xs font-medium text-slate-700">
                    {{ student.last_class_name || student.class_name || '-' }}
                  </span>
                </td>

                <!-- Tahun Lulus -->
                <td class="py-3.5 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ student.graduation_year || '-' }}
                  </span>
                </td>

                <!-- Total Poin Akhir -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold"
                    :class="student.total_violation_points === 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (student.total_violation_points >= 26 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200')"
                  >
                    {{ student.total_violation_points }} Poin
                  </span>
                </td>

                <!-- Riwayat SP -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="text-xs font-medium"
                    :class="student.total_violation_points === 0 ? 'text-emerald-600' : (student.total_violation_points >= 51 ? 'text-rose-600 font-semibold' : 'text-slate-600')"
                  >
                    {{ getSpStatusLabel(student.total_violation_points) }}
                  </span>
                </td>

                <!-- Aksi -->
                <td class="py-3.5 px-4 text-right">
                  <button
                    type="button"
                    @click="openTimeline(student)"
                    class="interactive-btn inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-medium transition cursor-pointer"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Lihat Rekam Jejak</span>
                  </button>
                </td>
              </tr>

              <tr v-if="alumni.data.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                  Tidak ada data arsip siswa lulus yang sesuai dengan kriteria filter pencarian.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="alumni.total > alumni.per_page" class="p-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
          <div>
            Menampilkan {{ (alumni.current_page - 1) * alumni.per_page + 1 }} - {{ Math.min(alumni.current_page * alumni.per_page, alumni.total) }} dari {{ alumni.total }} alumni
          </div>
          <div class="flex items-center gap-1">
            <template v-for="(link, i) in alumni.links" :key="i">
              <button
                v-if="link.url"
                type="button"
                @click="goToPage(link.url)"
                class="px-2.5 py-1 rounded border text-xs transition cursor-pointer"
                :class="link.active ? 'bg-slate-900 text-white border-slate-900 font-semibold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200'"
                v-html="link.label"
              ></button>
              <span v-else class="px-2 py-1 text-slate-300 text-xs" v-html="link.label"></span>
            </template>
          </div>
        </div>
      </div>

    </div>

    <!-- ============================================================ -->
    <!-- 5. SLIDE-OVER / MODAL: REKAM JEJAK ALUMNI (SKKB REFERENCE)    -->
    <!-- ============================================================ -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/75 shrink-0">
          <div>
            <h3 class="font-bold text-slate-900 text-base">Rekam Jejak Alumni &amp; Referensi SKKB</h3>
            <p class="text-xs text-slate-500 mt-0.5">Riwayat kedisiplinan dan pembinaan selama bersekolah</p>
          </div>
          <button @click="showModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 space-y-4 overflow-y-auto text-xs flex-1">
          <!-- Student Identity Summary Card -->
          <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <div class="text-sm font-bold text-slate-900">{{ activeStudent?.name }}</div>
              <div class="text-xs text-slate-500 font-mono mt-0.5">
                NISN: {{ activeStudent?.nis || activeStudent?.nisn }} &bull; Kelas Terakhir: {{ activeStudent?.last_class_name || activeStudent?.class_name }}
              </div>
              <div class="text-[11px] text-slate-500 mt-1">
                Tahun Kelulusan: <strong class="text-slate-800">{{ activeStudent?.graduation_year || '-' }}</strong> &bull; Jurusan: {{ activeStudent?.major }}
              </div>
            </div>

            <div class="text-left sm:text-right shrink-0">
              <div class="text-[11px] text-slate-400">Akumulasi Poin Sanksi</div>
              <div
                class="text-lg font-bold"
                :class="activeStudent?.total_violation_points === 0 ? 'text-emerald-600' : 'text-rose-600'"
              >
                {{ activeStudent?.total_violation_points }} Poin
              </div>
            </div>
          </div>

          <!-- SKKB Feasibility Status Banner -->
          <div
            v-if="activeStudent?.total_violation_points === 0"
            class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3 text-emerald-800"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <div class="font-bold text-xs">Rekomendasi SKKB: Sangat Baik (Berkelakuan Baik)</div>
              <div class="text-[11px] text-emerald-700 mt-0.5">
                Siswa tidak memiliki catatan pelanggaran tata tertib selama menempuh masa studi. Memenuhi syarat penerbitan Surat Keterangan Berkelakuan Baik resmi sekolah.
              </div>
            </div>
          </div>
          <div
            v-else
            class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3 text-amber-800"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
              <div class="font-bold text-xs">Catatan Pembinaan Tercatat: {{ activeStudent?.total_violation_points }} Poin</div>
              <div class="text-[11px] text-amber-700 mt-0.5">
                Alumni memiliki riwayat penanganan kedisiplinan. Periksa catatan pembinaan di bawah sebelum menerbitkan rekomendasi kelakuan baik.
              </div>
            </div>
          </div>

          <!-- Timeline List of Violations -->
          <div class="space-y-2">
            <h4 class="font-semibold text-slate-800 text-xs">Riwayat Kasus &amp; Pembinaan Historis</h4>

            <div v-if="loadingTimeline" class="py-8 text-center text-slate-400">
              Memuat data riwayat rekam jejak...
            </div>

            <div v-else-if="timelineList.length === 0" class="p-6 text-center text-slate-400 bg-slate-50 border border-dashed border-slate-200 rounded-xl">
              Bersih &bull; Tidak ada riwayat kasus atau sanksi yang tercatat.
            </div>

            <div v-else class="space-y-2.5">
              <div
                v-for="(item, idx) in timelineList"
                :key="item.id || idx"
                class="p-3.5 bg-white border border-slate-200 rounded-xl space-y-1.5"
              >
                <div class="flex items-center justify-between text-xs">
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-slate-900">{{ item.violation_name }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded font-medium bg-slate-100 text-slate-600 border border-slate-200">
                      {{ item.class_snapshot || 'Kelas Tidak Diketahui' }}
                    </span>
                  </div>
                  <span
                    class="font-bold text-xs"
                    :class="item.points ? 'text-rose-600' : 'text-slate-500'"
                  >
                    {{ item.points ? '+' + item.points + ' Poin' : 'Tanpa Poin' }}
                  </span>
                </div>

                <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-2">
                  <span>Tanggal: {{ item.date_formatted }} ({{ item.time }})</span>
                  <span>&bull;</span>
                  <span>Petugas Pelapor: {{ item.reporter }}</span>
                </div>

                <div v-if="item.penalty_action" class="text-[11px] text-slate-700 bg-slate-50 p-2 rounded-lg border border-slate-100">
                  <span class="font-medium text-slate-800">Tindakan Pembinaan:</span> {{ item.penalty_action }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between shrink-0">
          <button
            type="button"
            @click="copySkkbSummary"
            class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-medium cursor-pointer transition"
          >
            Salin Ringkasan SKKB
          </button>
          <button
            type="button"
            @click="showModal = false"
            class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-medium cursor-pointer transition"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  alumni: {
    type: Object,
    default: () => ({ data: [], current_page: 1, per_page: 15, total: 0, links: [] })
  },
  filters: {
    type: Object,
    default: () => ({ search: '', tahun: 'ALL', jurusan: 'ALL', rombel: 'ALL' })
  },
  options: {
    type: Object,
    default: () => ({ years: [], majors: [], rombels: [] })
  },
  kpi: {
    type: Object,
    default: () => ({ totalAlumni: 0, selectedYearCount: 0, cleanAlumniCount: 0 })
  }
})

const searchQuery = ref(props.filters.search || '')
const selectedYear = ref(props.filters.tahun || 'ALL')
const selectedMajor = ref(props.filters.jurusan || 'ALL')
const selectedRombel = ref(props.filters.rombel || 'ALL')

let searchTimer = null
const handleSearchDebounced = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    applyFilter()
  }, 350)
}

const applyFilter = () => {
  router.get('/arsip-lulus', {
    search: searchQuery.value || undefined,
    tahun: selectedYear.value !== 'ALL' ? selectedYear.value : undefined,
    jurusan: selectedMajor.value !== 'ALL' ? selectedMajor.value : undefined,
    rombel: selectedRombel.value !== 'ALL' ? selectedRombel.value : undefined,
  }, {
    preserveState: true,
    replace: true,
  })
}

const goToPage = (url) => {
  if (url) {
    router.visit(url, { preserveState: true })
  }
}

const getSpStatusLabel = (points) => {
  const p = Number(points) || 0
  if (p === 0) return 'Bersih (Tanpa Sanksi)'
  if (p >= 76) return 'Konferensi Kasus (SP-3)'
  if (p >= 51) return 'Panggilan Ortu (SP-2)'
  if (p >= 26) return 'Peringatan Resmi (SP-1)'
  return 'Teguran Pembinaan'
}

// Modal Timeline Logic
const showModal = ref(false)
const activeStudent = ref(null)
const timelineList = ref([])
const loadingTimeline = ref(false)

const openTimeline = async (student) => {
  activeStudent.value = student
  showModal.value = true
  loadingTimeline.value = true
  timelineList.value = []

  try {
    const res = await fetch(`/arsip-lulus/${student.id}/timeline`)
    if (res.ok) {
      const data = await res.json()
      timelineList.value = data.timeline || []
      if (data.student) {
        activeStudent.value = { ...student, ...data.student }
      }
    }
  } catch (e) {
    timelineList.value = []
  } finally {
    loadingTimeline.value = false
  }
}

const copySkkbSummary = () => {
  if (!activeStudent.value) return
  const s = activeStudent.value
  const status = s.total_violation_points === 0 ? 'BERKELAKUAN BAIK (Tanpa Catatan Pelanggaran)' : `TERCATAT PEMBINAAN (${s.total_violation_points} Poin)`
  const text = `INFORMASI ARSIP ALUMNI SMK PGRI 05 JEMBER\nNama: ${s.name}\nNISN: ${s.nis || s.nisn}\nTahun Lulus: ${s.graduation_year || '-'}\nKelas Terakhir: ${s.last_class_name || s.class_name}\nStatus Kedisiplinan: ${status}\nTotal Poin Sanksi Akhir: ${s.total_violation_points} Poin.`
  navigator.clipboard?.writeText(text).then(() => {
    alert('Ringkasan data SKKB berhasil disalin ke clipboard.')
  })
}

const printSummary = () => {
  window.print()
}
</script>
