<template>
  <AppLayout active-tab="verifikasi" show-back>
    <div class="max-w-3xl mx-auto space-y-6">

      <!-- Toast Notification -->
      <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showSuccessToast" class="fixed top-20 right-4 sm:right-8 z-50 bg-white border border-emerald-200 rounded-2xl shadow-xl p-4 flex items-center gap-3 text-xs max-w-sm">
          <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <p class="font-bold text-gray-900">Data Berhasil Disimpan</p>
            <p class="text-gray-500 text-[11px]">Catatan pelanggaran dan sanksi siswa berhasil dicatat.</p>
          </div>
        </div>
      </transition>

      <!-- Main Form Container -->
      <div class="bg-white rounded-3xl p-5 sm:p-8 border border-gray-100 shadow-sm space-y-6">
        
        <!-- Header -->
        <div class="border-b border-gray-100 pb-5">
          <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Verifikasi & Pelaporan</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Form Input Pelanggaran</h1>
          <p class="text-xs text-gray-500 mt-1 leading-relaxed">
            Isi informasi nama siswa, rincian pelanggaran, dan lampirkan bukti foto untuk rekaman kedisiplinan BK.
          </p>
        </div>

        <!-- 1. Nama Siswa Terlapor -->
        <div class="space-y-2">
          <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-800">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center">1</span>
            <span>Nama Siswa Terlapor</span>
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="form.studentName"
              type="text"
              placeholder="Ketik nama lengkap siswa (KLS, Kejuruan)..."
              class="w-full bg-blue-50/40 border border-blue-200/80 rounded-2xl pl-10 pr-4 py-3 text-xs sm:text-sm font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
            />
          </div>
          <!-- Quick Suggestion Tags -->
          <div class="flex flex-wrap items-center gap-1.5 pt-1 text-[11px]">
            <span class="text-gray-400">Saran cepat:</span>
            <button
              v-for="sug in studentSuggestions"
              :key="sug"
              type="button"
              @click="form.studentName = sug"
              class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 text-gray-600 font-medium transition cursor-pointer"
            >
              {{ sug }}
            </button>
          </div>
        </div>

        <!-- 2. Pelanggaran yang Dibuat Siswa -->
        <div class="space-y-3 pt-2">
          <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-800">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center">2</span>
            <span>Pelanggaran yang Dibuat Siswa</span>
          </label>

          <!-- Input Kategori Pelanggaran (Menggantikan Select) -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <input
              v-model="form.category"
              type="text"
              placeholder="Ketik jenis atau kategori pelanggaran siswa..."
              class="w-full bg-blue-50/40 border border-blue-200/80 rounded-2xl pl-10 pr-4 py-3 text-xs sm:text-sm font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
            />
          </div>

          <!-- Description Textarea -->
          <div>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Tuliskan kronologi dan alasan siswa..."
              class="w-full bg-blue-50/30 border border-blue-200/60 rounded-2xl p-4 text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition leading-relaxed"
            ></textarea>
          </div>
        </div>

        <!-- 3. Bukti Foto Pelanggaran -->
        <div class="space-y-3 pt-2">
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-800">
              <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center">3</span>
              <span>Bukti Foto Pelanggaran</span>
            </label>
            <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 font-bold text-[11px] rounded-full border border-emerald-200 flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              Foto Terlampir
            </span>
          </div>

          <!-- Image Preview Card -->
          <div class="relative rounded-2xl overflow-hidden border-2 border-dashed border-gray-200 bg-gray-50 group">
            <img
              :src="form.photoUrl"
              alt="Bukti Foto Pelanggaran"
              class="w-full max-h-72 object-cover object-center"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end p-4">
              <div class="text-white text-xs">
                <p class="font-bold">Bukti: Siswa tambal ban di Jl. PB Sudirman</p>
                <p class="text-[10px] text-gray-200">Diunggah: 24 Okt 2024, 07:22 WIB • GeoTag Valid</p>
              </div>
            </div>
          </div>

          <!-- Upload Actions -->
          <div class="grid grid-cols-2 gap-3 pt-1">
            <button
              type="button"
              @click="triggerUpload"
              class="py-3 px-4 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center gap-2 border border-blue-200 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>Ambil Foto Baru</span>
            </button>
            <button
              type="button"
              @click="triggerUpload"
              class="py-3 px-4 rounded-xl bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs flex items-center justify-center gap-2 border border-gray-200 transition cursor-pointer shadow-2xs"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Pilih dari Galeri</span>
            </button>
            <input type="file" ref="fileUpload" @change="onPhotoChange" class="hidden" accept="image/*" />
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-gray-100">
          <button
            type="button"
            :disabled="isSubmitting"
            @click="handleSubmit"
            class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-bold py-4 rounded-2xl shadow-md hover:shadow-lg text-xs sm:text-sm flex items-center justify-center gap-2 transition cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed"
          >
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            <span>{{ isSubmitting ? 'Menyimpan ke Sistem BK Prima...' : 'Simpan Data Pelanggaran' }}</span>
          </button>
        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useRoute } from 'vue-router'

const route = useRoute()
const props = defineProps({
  studentsList: {
    type: Array,
    default: () => []
  }
})

const showSuccessToast = ref(false)
const isSubmitting = ref(false)
const fileUpload = ref(null)

const studentSuggestions = [
  'Muhammad Rizky Pratama (XI RPL 2)',
  'Dimas Arya Pratama (X RPL 1)',
  'Rania Zahra Putri (X RPL 1)',
  'Fathir Rizky Maulana (X RPL 1)'
]

const form = ref({
  studentName: 'Muhammad Rizky Pratama (XI RPL 2)',
  category: 'Terlambat Masuk',
  description: 'Siswa datang pukul 07:18 WIB (terlambat 18 menit dari batas gerbang ditutup). Alasan ban motor bocor saat perjalanan menuju sekolah.',
  photoUrl: 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
})

onMounted(() => {
  if (route.query && route.query.nama) {
    const cls = route.query.kelas ? ` (${route.query.kelas})` : ''
    form.value.studentName = `${route.query.nama}${cls}`
  }
})

const triggerUpload = () => {
  fileUpload.value?.click()
}

const onPhotoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (event) => {
      form.value.photoUrl = event.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleSubmit = () => {
  if (!form.value.studentName) {
    alert('Mohon isi nama siswa terlapor')
    return
  }
  if (!form.value.category) {
    alert('Mohon isi jenis atau kategori pelanggaran')
    return
  }

  isSubmitting.value = true
  router.post('/pelanggaran', {
    student_name: form.value.studentName,
    category: form.value.category,
    violation_name: form.value.category,
    notes: form.value.description,
    points: 10,
    date: new Date().toISOString().slice(0, 10),
    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
    reporter: 'Guru Piket & BK',
    penalty_action: 'Teguran lisan & pencatatan kedisiplinan',
  }, {
    onSuccess: () => {
      showSuccessToast.value = true
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}
</script>
