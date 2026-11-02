<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50/40 to-slate-100 flex flex-col justify-center items-center p-4 sm:p-6 relative overflow-hidden font-sans select-none">
    
    <!-- Ambient Background Lighting -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-400/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-400/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-200/20 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Background Subtle Dot Grid -->
    <div class="absolute inset-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:20px_20px] opacity-40 pointer-events-none"></div>

    <!-- Main Container: Constrained to standard modern auth width to fix zoomed-in / oversized layout -->
    <div class="w-full max-w-[430px] relative z-10 py-6 sm:py-8 my-auto">
      
      <!-- Top Brand Header -->
      <div class="flex flex-col items-center text-center mb-4 sm:mb-5">
        <!-- Official Logo BK Prima (Foto 1) -->
        <div class="mb-2">
          <img src="/logo-bkprima.png" alt="BK Prima - Sistem Informasi &amp; Konseling SMK PGRI 5 Jember" class="h-14 sm:h-16 w-auto object-contain drop-shadow-xs" />
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
          Masuk Akun <span class="text-blue-600">Konseling</span>
        </h1>
        <p class="text-xs text-slate-500 mt-1 max-w-[320px] leading-relaxed">
          Sistem manajemen presensi &amp; buku catatan kedisiplinan siswa SMK PGRI 05 Jember
        </p>
      </div>

      <!-- Segmented Role Navigation Tabs -->
      <div class="bg-slate-200/80 p-1 rounded-xl mb-3.5 grid grid-cols-3 gap-1 text-xs font-semibold shadow-inner">
        <button
          type="button"
          @click="selectRole('guru')"
          class="py-1.5 px-1 text-center rounded-lg font-bold transition cursor-pointer"
          :class="currentRole === 'guru' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Guru BK
        </button>
        <button
          type="button"
          @click="selectRole('admin')"
          class="py-1.5 px-1 text-center rounded-lg font-bold transition cursor-pointer flex items-center justify-center gap-1"
          :class="currentRole === 'admin' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
          <span>Admin</span>
          <span class="text-[9px] px-1 py-0.2 rounded" :class="currentRole === 'admin' ? 'bg-indigo-800 text-white' : 'bg-slate-300 text-slate-700'">Sistem</span>
        </button>
        <router-link
          to="/tamu"
          class="py-1.5 px-1 text-center rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center justify-center gap-1 cursor-pointer font-medium"
        >
          <span>Tamu</span>
          <span class="text-[9px] px-1 py-0.2 bg-emerald-100 text-emerald-700 font-bold rounded">Publik</span>
        </router-link>
      </div>

      <!-- Auth Card -->
      <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 sm:p-6 shadow-xl shadow-slate-200/70 border border-slate-200/80">
        <form @submit.prevent="submitLogin" class="space-y-3.5">
          
          <!-- NIP Field -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                NIP / Akun Resmi Kepegawaian
              </label>
              <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Terverifikasi
              </span>
            </div>

            <div class="relative">
              <input
                v-model="nip"
                type="text"
                placeholder="Masukkan NIP atau nomor akun..."
                class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2 text-sm text-slate-900 font-medium tracking-wide focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
              />
              <div class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Terdaftar aktif di sistem SIM-Pegawai SMK PGRI 05
            </p>
          </div>

          <!-- Password Field -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Kata Sandi Portal
              </label>
            </div>

            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Masukkan kata sandi..."
                class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2 pr-10 text-sm text-slate-900 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition tracking-wider"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1 transition"
                :title="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
              >
                <!-- Eye Open Icon -->
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <!-- Eye Slash Icon -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between pt-0.5">
            <label class="flex items-center gap-2 cursor-pointer select-none">
              <input
                v-model="rememberMe"
                type="checkbox"
                class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
              />
              <span class="text-xs font-medium text-slate-600">Ingat sesi saya</span>
            </label>
            <button
              type="button"
              @click="openForgotModal = true"
              class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition"
            >
              Lupa Sandi?
            </button>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-blue-600/25 transition duration-150 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed mt-1"
          >
            <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="!isLoading">Masuk ke HadirMu BK</span>
            <span v-else>Memverifikasi Akun...</span>
          </button>
        </form>

        <!-- Modern Clean Divider -->
        <div class="relative my-4">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-wider text-slate-400">
            <span class="bg-white px-2.5">Akses Alternatif</span>
          </div>
        </div>

        <!-- Guest Access Portal Card (NO EMOJI - Pure SVG Icon) -->
        <router-link
          to="/tamu"
          class="group block p-2.5 rounded-xl bg-gradient-to-r from-teal-50/60 to-emerald-50/60 border border-teal-200/80 hover:border-teal-400 hover:shadow-sm transition"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-600/20">
                <!-- SVG Icon for Users / Parents & Students -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="text-left">
                <span class="text-xs font-bold text-slate-900 group-hover:text-teal-700 transition block leading-snug">
                  Portal Tamu Siswa &amp; Wali Murid
                </span>
                <span class="text-[10px] text-slate-500 block">
                  Cek rekap pelanggaran &amp; data presensi tanpa login
                </span>
              </div>
            </div>
            <div class="w-5 h-5 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center shrink-0 group-hover:translate-x-0.5 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </router-link>

        <!-- Help & Contact Admin IT -->
        <button
          type="button"
          @click="openContactModal = true"
          class="w-full mt-2.5 py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          Bantuan &amp; Kontak Admin IT Sekolah
        </button>
      </div>

      <!-- Footer Info -->
      <div class="text-center mt-5 text-[11px] text-slate-500 space-y-1">
        <div class="flex items-center justify-center gap-1.5 text-slate-500 font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span>Sistem Resmi BK Prima • Terenkripsi SSL/TLS</span>
        </div>
        <p class="text-[10px] text-slate-400">
          Hak Cipta &copy; 2026 SMK PGRI 05 Jember. Seluruh hak dilindungi.
        </p>
      </div>
    </div>

    <!-- Modal: Lupa Sandi -->
    <div
      v-if="openForgotModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
      @click.self="openForgotModal = false"
    >
      <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-left animate-fadeIn">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900">Pemulihan Kata Sandi</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          Untuk menjaga keamanan data konseling siswa, reset kata sandi guru BK dilakukan melalui verifikasi Koordinator BK atau Operator IT Sekolah.
        </p>

        <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-1.5">
          <div class="font-semibold text-slate-700">Langkah Pemulihan:</div>
          <p class="text-slate-600 text-[11px]">1. Siapkan identitas NIP resmi.</p>
          <p class="text-slate-600 text-[11px]">2. Hubungi Operator IT SMK PGRI 05 Jember.</p>
          <p class="text-slate-600 text-[11px]">3. Petugas akan merilis token reset sementara.</p>
        </div>

        <div class="mt-5 flex gap-2">
          <button
            type="button"
            @click="openForgotModal = false; openContactModal = true"
            class="flex-1 bg-blue-600 text-white py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition text-center"
          >
            Hubungi Operator
          </button>
          <button
            type="button"
            @click="openForgotModal = false"
            class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Kontak IT Admin -->
    <div
      v-if="openContactModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
      @click.self="openContactModal = false"
    >
      <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-left animate-fadeIn">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900">Pusat Bantuan &amp; Helpdesk IT</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          Tim IT SMK PGRI 05 Jember siap membantu sinkronisasi data presensi, akun guru, dan masalah akses portal.
        </p>

        <div class="mt-4 space-y-2">
          <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
            <div>
              <span class="block text-[10px] text-slate-400 font-bold uppercase">Helpdesk IT Sekolah</span>
              <span class="font-bold text-slate-800">Unit Lab Komputer &amp; Server</span>
            </div>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">Online</span>
          </div>
          <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
            <div>
              <span class="block text-[10px] text-slate-400 font-bold uppercase">Jam Layanan</span>
              <span class="font-medium text-slate-700">Senin - Sabtu (07.00 - 15.30 WIB)</span>
            </div>
          </div>
        </div>

        <button
          type="button"
          @click="openContactModal = false"
          class="w-full mt-5 bg-slate-900 text-white py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition"
        >
          Selesai
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const currentRole = ref('guru')
const nip = ref('198503152010012014')
const password = ref('BkSMKPGRI052026!')
const showPassword = ref(false)
const rememberMe = ref(true)
const isLoading = ref(false)
const openForgotModal = ref(false)
const openContactModal = ref(false)

const selectRole = (role) => {
  currentRole.value = role
  if (role === 'admin') {
    nip.value = 'admin_bkprima'
    password.value = 'admin2026!'
  } else {
    nip.value = '198503152010012014'
    password.value = 'BkSMKPGRI052026!'
  }
}

const submitLogin = () => {
  isLoading.value = true
  router.post('/login', {
    nip: nip.value,
    email: nip.value,
    password: password.value,
    role: currentRole.value,
    remember: rememberMe.value
  }, {
    onFinish: () => {
      isLoading.value = false
    }
  })
}
</script>
