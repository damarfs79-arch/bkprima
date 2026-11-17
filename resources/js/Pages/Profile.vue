<template>
  <AppLayout active-tab="profil" show-back>
    <div class="max-w-4xl mx-auto space-y-5">

      <!-- Toast Notification -->
      <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showToast" class="fixed top-20 right-4 sm:right-8 z-50 bg-white border border-slate-200 rounded-xl shadow-lg p-4 flex items-center gap-3 text-xs max-w-xs">
          <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div>
            <p class="font-semibold text-slate-800">Profil berhasil disimpan</p>
            <p class="text-slate-500 text-[11px] mt-0.5">Data Guru BK telah diperbarui.</p>
          </div>
        </div>
      </transition>

      <!-- Page Header -->
      <div class="bg-white rounded-xl px-5 py-4 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-0.5">Manajemen Akun</p>
          <h1 class="text-lg font-bold text-slate-900">Profil Guru BK</h1>
          <p class="text-xs text-slate-500 mt-0.5">Pembaruan biodata, kontak kedinasan &amp; rombel binaan</p>
        </div>
        <button
          @click="saveProfile"
          :disabled="isSaving"
          class="self-start sm:self-auto inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white font-semibold px-4 py-2 rounded-lg text-xs transition cursor-pointer active:scale-95"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
        </button>
      </div>

      <!-- Avatar / Identity Card -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-6 bg-slate-50 border-b border-slate-100"></div>

        <div class="px-6 pb-6 flex flex-col sm:flex-row sm:items-end gap-4 mt-4">
          <!-- Avatar -->
          <div class="relative shrink-0">
            <img
              :src="avatarPreview"
              alt="Foto Guru BK"
              class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border-4 border-white shadow-md ring-1 ring-slate-200"
            />
            <button
              @click="triggerFileInput"
              class="absolute -bottom-1 -right-1 p-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 transition cursor-pointer"
              title="Ganti Foto Profil"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
            <input type="file" ref="fileInput" @change="onFileChange" class="hidden" accept="image/*" />
          </div>

          <!-- Identity text -->
          <div class="pb-1 sm:pb-2">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ form.name }}</h3>
            <p class="text-xs text-slate-500 mt-0.5">{{ form.jabatan || 'Guru Bimbingan Konseling' }}</p>
            <p class="text-xs text-slate-400">SMK PGRI 05 JEMBER</p>
            <p class="text-[11px] text-slate-400 mt-2">Format JPG/PNG, maks. 2MB &bull; Disarankan foto resmi berseragam dinas.</p>
          </div>
        </div>
      </div>

      <!-- Form Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <!-- Left: Data Kepegawaian -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
          <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <p class="text-xs font-bold text-slate-800 leading-tight">Informasi Pokok Pendidik</p>
              <p class="text-[11px] text-slate-400">Data resmi identitas kepegawaian</p>
            </div>
          </div>

          <!-- Nama -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap &amp; Gelar</label>
            <div class="relative">
              <input
                v-model="form.name"
                type="text"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 pr-9 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 focus:bg-white transition"
              />
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </span>
            </div>
          </div>

          <!-- NIP -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="text-xs font-semibold text-slate-700">NIP</label>
              <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Terkunci
              </span>
            </div>
            <input
              :value="form.nip"
              type="text"
              class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2.5 text-xs font-mono text-slate-500 cursor-not-allowed select-none"
              readonly
            />
            <p class="text-[10px] text-slate-400 mt-1">Hubungi Operator Sekolah / Dapodik jika perlu mengubah NIP.</p>
          </div>

          <!-- NUPTK -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">NUPTK</label>
            <input
              v-model="form.nuptk"
              type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 focus:bg-white transition"
            />
          </div>

          <!-- Jabatan -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jabatan / Penugasan</label>
            <input
              v-model="form.jabatan"
              type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 focus:bg-white transition"
            />
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-5">

          <!-- Kontak Card -->
          <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <p class="text-xs font-bold text-slate-800 leading-tight">Kontak &amp; Panggilan BK</p>
                <p class="text-[11px] text-slate-400">Komunikasi wali siswa &amp; kedinasan</p>
              </div>
            </div>

            <!-- WhatsApp -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp</label>
              <input
                v-model="form.wa"
                type="text"
                placeholder="+62 8xx-xxxx-xxxx"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 focus:bg-white transition"
              />
              <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk panggilan &amp; surat pemanggilan wali murid.</p>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Kedinasan (Belajar.id)</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 focus:bg-white transition"
              />
            </div>
          </div>

          <!-- Rombel Card -->
          <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012-2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-bold text-slate-800 leading-tight">Rombongan Belajar</p>
                  <p class="text-[11px] text-slate-400">Kelas binaan &amp; pendampingan BK</p>
                </div>
              </div>
              <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">{{ form.rombel.length }} kelas</span>
            </div>

            <!-- Rombel Tags -->
            <div class="flex flex-wrap gap-1.5 pt-1">
              <span
                v-for="(cls, idx) in form.rombel"
                :key="idx"
                class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-medium text-xs rounded-lg transition"
              >
                <span>{{ cls }}</span>
                <button
                  @click="removeRombel(idx)"
                  class="text-slate-400 hover:text-red-500 transition cursor-pointer ml-0.5"
                  title="Hapus"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </span>

              <!-- Tambah Rombel inline -->
              <div v-if="showRombelInput" class="inline-flex items-center gap-1">
                <input
                  v-model="newRombelInput"
                  @keydown.enter="confirmAddRombel"
                  @keydown.escape="showRombelInput = false; newRombelInput = ''"
                  ref="rombelInputRef"
                  type="text"
                  placeholder="cth: X RPL 1"
                  class="w-28 bg-white border border-slate-300 rounded-lg px-2 py-1 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition"
                />
                <button
                  @click="confirmAddRombel"
                  class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg transition cursor-pointer"
                  title="Tambahkan"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
                <button
                  @click="showRombelInput = false; newRombelInput = ''"
                  class="p-1 text-slate-400 hover:bg-slate-100 rounded-lg transition cursor-pointer"
                  title="Batal"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>

              <button
                v-else
                @click="openRombelInput"
                class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-50 border border-slate-200 border-dashed text-slate-500 font-medium text-xs rounded-lg transition cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kelas
              </button>
            </div>
          </div>

        </div>
      </div>

      <!-- Footer Actions -->
      <div class="flex flex-col sm:flex-row gap-2.5 pt-1 pb-6">
        <button
          @click="saveProfile"
          :disabled="isSaving"
          class="flex-1 bg-slate-800 hover:bg-slate-700 disabled:opacity-60 text-white font-semibold py-3 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition cursor-pointer active:scale-[0.99]"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
        </button>
        <button
          @click="router.visit('/absensi')"
          class="px-6 bg-white hover:bg-slate-50 text-slate-600 font-semibold py-3 rounded-xl border border-slate-200 text-xs sm:text-sm transition cursor-pointer flex items-center justify-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Kembali</span>
        </button>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  profileUser: {
    type: Object,
    default: null
  }
})

const showToast = ref(false)
const isSaving = ref(false)
const fileInput = ref(null)
const rombelInputRef = ref(null)
const showRombelInput = ref(false)
const newRombelInput = ref('')

const DEFAULT_AVATAR = 'https://ui-avatars.com/api/?name=Guru+BK&background=e2e8f0&color=475569&bold=true&size=200'

const avatarPreview = ref(DEFAULT_AVATAR)

const form = ref({
  name: '',
  nip: '',
  nuptk: '',
  jabatan: '',
  wa: '',
  email: '',
  rombel: []
})

onMounted(() => {
  if (props.profileUser) {
    form.value.name    = props.profileUser.name    || ''
    form.value.email   = props.profileUser.email   || ''
    form.value.wa      = props.profileUser.phone   || ''
    form.value.nip     = props.profileUser.nip     || ''
    form.value.nuptk   = props.profileUser.nuptk   || ''
    form.value.jabatan = props.profileUser.jabatan || ''
    form.value.rombel  = Array.isArray(props.profileUser.rombel)
      ? [...props.profileUser.rombel]
      : []

    if (form.value.name) {
      avatarPreview.value = `https://ui-avatars.com/api/?name=${encodeURIComponent(form.value.name)}&background=e2e8f0&color=475569&bold=true&size=200`
    }
  }
})

const removeRombel = (idx) => {
  form.value.rombel.splice(idx, 1)
}

const openRombelInput = () => {
  showRombelInput.value = true
  nextTick(() => rombelInputRef.value?.focus())
}

const confirmAddRombel = () => {
  const val = newRombelInput.value.trim().toUpperCase()
  if (val && !form.value.rombel.includes(val)) {
    form.value.rombel.push(val)
  }
  newRombelInput.value = ''
  showRombelInput.value = false
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
    alert('Ukuran file melebihi batas 2MB. Pilih foto yang lebih kecil.')
    return
  }
  const reader = new FileReader()
  reader.onload = (event) => {
    avatarPreview.value = event.target.result
  }
  reader.readAsDataURL(file)
}

const saveProfile = () => {
  isSaving.value = true
  router.post('/profil', {
    name:    form.value.name,
    email:   form.value.email,
    phone:   form.value.wa,
    nuptk:   form.value.nuptk,
    jabatan: form.value.jabatan,
    rombel:  form.value.rombel,
  }, {
    onSuccess: () => {
      showToast.value = true
      setTimeout(() => { showToast.value = false }, 3500)
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      if (firstError) alert(firstError)
    },
    onFinish: () => {
      isSaving.value = false
    }
  })
}
</script>
