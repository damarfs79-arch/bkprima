<template>
  <AppLayout active-tab="statistik" show-share>
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- 1. HEADER SECTION & FILTER WAKTU                              -->
      <!-- ============================================================ -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
          <h1 class="text-xl font-bold text-slate-900 tracking-tight">Statistik Kehadiran</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs">
          <!-- Month Filter -->
          <div class="relative">
            <select class="bg-white hover:bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-7 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition">
              <option>Februari 2026</option>
              <option>Januari 2026</option>
              <option>Desember 2025</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Grade Filter -->
          <div class="relative">
            <select class="bg-white hover:bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-7 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition">
              <option>Semua Tingkat</option>
              <option>Kelas 10</option>
              <option>Kelas 11</option>
              <option>Kelas 12</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Major Filter -->
          <div class="relative">
            <select class="bg-white hover:bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-7 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-slate-400 appearance-none cursor-pointer transition">
              <option>Semua Jurusan</option>
              <option>RPL</option>
              <option>TSM</option>
              <option>DKV</option>
              <option>TKJ</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Toggle Period -->
          <div class="flex bg-slate-100 p-0.5 rounded-lg border border-slate-200">
            <button
              @click="period = 'minggu'"
              class="px-2.5 py-1 rounded-md text-xs transition cursor-pointer"
              :class="period === 'minggu' ? 'bg-white text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            >
              1 Minggu
            </button>
            <button
              @click="period = 'bulan'"
              class="px-2.5 py-1 rounded-md text-xs transition cursor-pointer"
              :class="period === 'bulan' ? 'bg-white text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            >
              1 Bulan
            </button>
            <button
              @click="period = 'tahun'"
              class="px-2.5 py-1 rounded-md text-xs transition cursor-pointer"
              :class="period === 'tahun' ? 'bg-white text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            >
              1 Tahun
            </button>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 2. METRIC CARDS                                              -->
      <!-- ============================================================ -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <!-- Hadir -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">Hadir</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-slate-900">{{ metrics.hadirRate }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.hadirCount }} catatan</span>
          </div>
        </div>

        <!-- Sakit -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">Sakit</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-slate-900">{{ metrics.sakitRate }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.sakitCount }} catatan</span>
          </div>
        </div>

        <!-- Izin -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-slate-500 mb-1">Izin</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-slate-900">{{ metrics.izinRate }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.izinCount }} catatan</span>
          </div>
        </div>

        <!-- Alpa -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
          <div class="text-xs font-medium text-rose-600 mb-1">Alpa</div>
          <div class="flex items-baseline gap-1.5">
            <span class="text-2xl font-bold text-rose-600">{{ metrics.alphaRate }}%</span>
            <span class="text-xs text-slate-400">{{ metrics.alphaCount }} catatan</span>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 3. DAFTAR SISWA BUTUH PEMANGGILAN                            -->
      <!-- ============================================================ -->
      <div id="daftar-alpa-kritis" class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Table Header Toolbar -->
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <h3 class="text-sm font-semibold text-slate-900">
              Siswa Butuh Pemanggilan (Alpa &ge; 3)
            </h3>
            <span class="text-xs text-slate-500">
              ({{ atRiskStudents.length }})
            </span>
          </div>

          <Link
            href="/pemanggilan"
            class="text-xs text-slate-500 hover:text-slate-800 transition"
          >
            Arsip SP &rarr;
          </Link>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50/50 border-b border-slate-100">
              <tr>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Siswa</th>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Kelas</th>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Alpa</th>
                <th class="py-2.5 px-4 text-[11px] font-medium text-slate-500">Kontak Wali</th>
                <th class="py-2.5 px-4 text-right text-[11px] font-medium text-slate-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="student in atRiskStudents" :key="student.id" class="hover:bg-slate-50/60 transition">
                <!-- Siswa (Nama & NIS) -->
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900 text-xs">{{ student.name }}</div>
                  <div class="text-[11px] text-slate-400">NIS: {{ student.nis || student.nisn }}</div>
                </td>

                <!-- Kelas -->
                <td class="py-3 px-4 text-slate-600">
                  {{ student.class_name }}
                </td>

                <!-- Alpa -->
                <td class="py-3 px-4 font-semibold text-rose-600">
                  {{ student.alpha_count }} hari
                </td>

                <!-- Kontak Wali -->
                <td class="py-3 px-4">
                  <div class="text-slate-800 font-medium">{{ student.parent_name }}</div>
                  <div class="text-[11px] text-slate-400">{{ student.parent_phone }}</div>
                </td>

                <!-- Aksi -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-1.5">
                    <button
                      @click="openQuickSummonsModal(student)"
                      class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-md text-xs font-medium cursor-pointer transition"
                    >
                      Panggil Ortu
                    </button>
                    <button
                      v-if="student.parent_phone"
                      @click="sendWhatsAppWarning(student)"
                      class="px-2 py-1 bg-white hover:bg-slate-50 text-slate-700 rounded-md text-xs font-medium border border-slate-200 cursor-pointer transition"
                    >
                      WA
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="atRiskStudents.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                  Tidak ada siswa yang butuh pemanggilan minggu ini.
                </td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 6. LINE CHART COMPONENT                                      -->
      <!-- ============================================================ -->
      <DimensionComparisonLineChart />

      <!-- ============================================================ -->
      <!-- 7. DOWNLOAD / EXPORT BANNER                                  -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
        <div>
          <div class="font-semibold text-slate-900 text-xs sm:text-sm">Ekspor Laporan Statistik</div>
          <div class="text-slate-400 text-[11px] mt-0.5">Unduh rekap presensi bulanan dan daftar alpa</div>
        </div>

        <button
          @click="downloadReport"
          class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg inline-flex items-center gap-1.5 transition cursor-pointer shrink-0"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>Unduh Laporan</span>
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
                NIS: {{ selectedStudent.nis || selectedStudent.nisn }} &bull; Kelas: {{ selectedStudent.class_name }}
              </div>
              <div class="text-[11px] text-rose-600 font-semibold">
                Akumulasi Pelanggaran: {{ selectedStudent.alpha_count }} Hari Alpa (Tanpa Keterangan)
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahap Panggilan *</label>
                <input
                  v-model="summonsForm.stage"
                  type="text"
                  placeholder="Contoh: SP-1, SP-2, Panggilan 1..."
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                />
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
                class="text-xs font-semibold text-rose-700 hover:underline cursor-pointer inline-flex items-center gap-1"
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
  }
})

const metrics = computed(() => {
  return {
    totalPresensi: props.summaryMetrics?.totalPresensi || 0,
    hadirCount: props.summaryMetrics?.hadirCount || 0,
    sakitCount: props.summaryMetrics?.sakitCount || 0,
    izinCount: props.summaryMetrics?.izinCount || 0,
    alphaCount: props.summaryMetrics?.alphaCount || 0,
    hadirRate: props.summaryMetrics?.hadirRate !== undefined ? props.summaryMetrics.hadirRate : 0,
    sakitRate: props.summaryMetrics?.sakitRate !== undefined ? props.summaryMetrics.sakitRate : 0,
    izinRate: props.summaryMetrics?.izinRate !== undefined ? props.summaryMetrics.izinRate : 0,
    alphaRate: props.summaryMetrics?.alphaRate !== undefined ? props.summaryMetrics.alphaRate : 0,
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
