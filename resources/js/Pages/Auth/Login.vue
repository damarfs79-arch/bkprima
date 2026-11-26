<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-center items-center p-4 sm:p-6 font-sans">
    <div class="w-full max-w-[400px] my-auto">
      
      <!-- Brand Header -->
      <div class="text-center mb-6">
        <div class="flex justify-center mb-3">
          <img
            src="/logo-bkprima.png"
            alt="Logo BK Prima SMK PGRI 5 Jember"
            class="h-12 w-auto object-contain"
          />
        </div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
          Masuk ke Akun
        </h1>
        <p class="text-xs text-slate-500 mt-1">
          SMK PGRI 05 Jember
        </p>
      </div>

      <!-- Login Card -->
      <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-7 shadow-xs">
        
        <!-- Error Alert -->
        <div
          v-if="form.errors.email || form.errors.identifier || form.errors.password"
          class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2.5"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <div>
            <div class="font-semibold">Gagal Masuk</div>
            <p class="mt-0.5 leading-relaxed">
              {{ form.errors.email || form.errors.identifier || form.errors.password }}
            </p>
          </div>
        </div>

        <form @submit.prevent="submitLogin" class="space-y-4">
          
          <!-- Field: Email -->
          <div>
            <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">
              Email
            </label>
            <div class="relative">
              <input
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                required
                placeholder="nama@bkprima.sch.id"
                :class="[
                  'w-full bg-white border rounded-lg px-3.5 py-2 text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 transition',
                  (form.errors.email || form.errors.identifier) ? 'border-rose-300 focus:border-rose-500' : 'border-slate-200'
                ]"
              />
            </div>
          </div>

          <!-- Field: Password -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="password" class="block text-xs font-medium text-slate-700">
                Kata Sandi
              </label>
              <button
                type="button"
                @click="openForgotModal = true"
                class="text-xs text-slate-500 hover:text-slate-800 transition cursor-pointer"
              >
                Lupa sandi?
              </button>
            </div>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                placeholder="Masukkan kata sandi..."
                :class="[
                  'w-full bg-white border rounded-lg px-3.5 py-2 pr-10 text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 transition',
                  form.errors.password ? 'border-rose-300 focus:border-rose-500' : 'border-slate-200'
                ]"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 focus:outline-none transition cursor-pointer"
                :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
              >
                <!-- Eye Open Icon -->
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <!-- Eye Slash Icon -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center pt-0.5">
            <label class="flex items-center gap-2 cursor-pointer select-none">
              <input
                v-model="form.remember"
                type="checkbox"
                class="w-3.5 h-3.5 text-slate-900 bg-white border-slate-300 rounded focus:ring-slate-900/20 focus:ring-1 cursor-pointer"
              />
              <span class="text-xs text-slate-600">Ingat sesi login</span>
            </label>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-xs sm:text-sm font-medium py-2.5 px-4 rounded-lg transition duration-150 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed shadow-xs"
          >
            <svg v-if="form.processing" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="!form.processing">Masuk</span>
            <span v-else>Memverifikasi Akun...</span>
          </button>
        </form>

      </div>

      <!-- Footer Info -->
      <div class="text-center mt-6 text-xs text-slate-400 space-y-0.5">
        <p>Hak Cipta &copy; 2026 SMK PGRI 05 Jember</p>
        <p class="text-[11px] text-slate-400">Sistem Informasi Bimbingan Konseling &amp; Presensi Siswa</p>
      </div>
    </div>

    <!-- Modal: Bantuan Lupa Sandi -->
    <div
      v-if="openForgotModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
      @click.self="openForgotModal = false"
    >
      <div class="bg-white rounded-xl max-w-sm w-full p-5 sm:p-6 shadow-xl border border-slate-200 text-left">
        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h3 class="text-sm font-bold text-slate-900">Bantuan Lupa Kata Sandi</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          Pemulihan akun dapat dilakukan melalui Admin BK atau Koordinator BK.
        </p>

        <div class="mt-4 p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs space-y-1">
          <div class="font-medium text-slate-700">Prosedur Pemulihan:</div>
          <p class="text-slate-500 text-[11px]">1. Hubungi Admin BK dengan menyertakan alamat email resmi.</p>
          <p class="text-slate-500 text-[11px]">2. Petugas akan mereset kredensial masuk akun Anda.</p>
        </div>

        <div class="mt-5">
          <button
            type="button"
            @click="openForgotModal = false"
            class="w-full bg-slate-900 text-white py-2 rounded-lg text-xs font-medium hover:bg-slate-800 transition cursor-pointer"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const showPassword = ref(false)
const openForgotModal = ref(false)

const submitLogin = () => {
  form.email = form.email ? form.email.trim() : ''
  form.password = form.password ? form.password.trim() : ''

  form.post('/login', {
    preserveScroll: true,
    onFinish: () => {
      form.reset('password')
    },
  })
}
</script>
