<template>
  <AppLayout active-tab="profil" show-back>
    <div class="max-w-4xl mx-auto space-y-6">

      <!-- Toast Notification -->
      <transition 
        enter-active-class="transform ease-out duration-300 transition" 
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" 
        leave-active-class="transition ease-in duration-100" 
        leave-from-class="opacity-100" 
        leave-to-class="opacity-0"
      >
        <div v-if="showToast" class="fixed top-20 right-4 sm:right-8 z-50 bg-white/95 backdrop-blur-md border border-emerald-200/80 rounded-2xl shadow-2xl p-4 flex items-center gap-3 text-xs max-w-sm">
          <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <p class="font-bold text-gray-900">Profil Berhasil Disimpan</p>
            <p class="text-gray-500 text-[11px]">Data Guru BK HadirMu telah diperbarui di sistem.</p>
          </div>
        </div>
      </transition>

      <!-- Page Header Card with Soft Gradient Accent -->
      <div class="relative overflow-hidden bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-blue-100/40 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
              Tenaga Pendidik BK
            </span>
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Profil Guru BK</h1>
          <p class="text-xs text-slate-500 mt-0.5">Pembaruan Biodata, Kontak Kedinasan & Rombel Binaan</p>
        </div>
        <button 
          @click="saveProfile" 
          class="relative z-10 self-start sm:self-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs flex items-center gap-2 shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition-all cursor-pointer group active:scale-95"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          <span>Simpan Perubahan</span>
        </button>
      </div>

      <!-- Avatar Hero Section Card -->
      <div class="relative overflow-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <!-- Decorative Header Banner Pattern -->
        <div class="h-24 sm:h-28 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-600 relative overflow-hidden">
          <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
          <div class="absolute -bottom-10 right-10 w-40 h-40 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        </div>

        <div class="px-6 pb-6 pt-0 relative flex flex-col items-center text-center -mt-12 sm:-mt-14">
          <!-- Avatar Container with Ambient Glow & Camera Trigger -->
          <div class="relative mb-3 group">
            <div class="absolute -inset-1 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-400 opacity-60 blur-xs group-hover:opacity-100 transition duration-300"></div>
            <img
              :src="form.avatar"
              alt="Foto Guru BK"
              class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-white shadow-xl"
            />
            <button 
              @click="triggerFileInput" 
              class="absolute bottom-0 right-0 p-2.5 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all cursor-pointer ring-2 ring-white" 
              title="Ganti Foto Profil"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
            <input type="file" ref="fileInput" @change="onFileChange" class="hidden" accept="image/*" />
          </div>

          <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">{{ form.name }}</h3>
          <p class="text-xs text-slate-500 font-medium mb-3">{{ form.jabatan }} &bull; {{ form.sekolah }}</p>

          <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3.5 py-1.2 rounded-full text-xs font-bold border border-blue-200/60 mb-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Guru Pembimbing Terdaftar
          </div>
          <p class="text-[11px] text-slate-400 max-w-sm">Format JPG/PNG, maksimal 2MB. Disarankan foto resmi berseragam dinas sekolah.</p>
        </div>
      </div>

      <!-- Dapodik Verification Alert -->
      <div class="bg-gradient-to-r from-emerald-50/90 to-teal-50/70 border border-emerald-200/80 rounded-2xl p-4 sm:p-4.5 flex items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-sm shadow-emerald-500/20 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Terverifikasi Dapodik Kemdikbud</h4>
            <p class="text-[11px] text-slate-500">Sinkronisasi terakhir: 12 Jan 2025 &bull; Status Valid &amp; Aktif</p>
          </div>
        </div>
        <button 
          @click="syncDapodik"
          class="p-2 text-emerald-700 hover:text-emerald-800 hover:bg-emerald-100/70 rounded-xl transition cursor-pointer group" 
          title="Sinkronkan Ulang Dapodik"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-180 duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>

      <!-- Main Form 2-Column Responsive Layout -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Left Column: Informasi Pokok Pendidik -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
          <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 text-slate-900 font-bold text-sm">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <span class="block leading-tight">Informasi Pokok Pendidik</span>
              <span class="text-[11px] text-slate-400 font-normal">Data resmi identitas kepegawaian</span>
            </div>
          </div>

          <!-- Nama Lengkap & Gelar -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap &amp; Gelar</label>
            <div class="relative">
              <input 
                v-model="form.name" 
                type="text" 
                class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 pr-9 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
              />
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </span>
            </div>
          </div>

          <!-- NIP (Terkunci) -->
          <div>
            <div class="flex justify-between items-center mb-1.5">
              <label class="text-xs font-bold text-slate-700">Nomor Induk Pegawai (NIP)</label>
              <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 border border-slate-200/70 px-2 py-0.5 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Terkunci</span>
              </span>
            </div>
            <div class="relative">
              <input 
                :value="form.nip" 
                type="text" 
                class="w-full bg-slate-100/90 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-600 cursor-not-allowed select-none" 
                readonly 
              />
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Hubungi Operator Sekolah / Dapodik jika ingin mengubah NIP.</p>
          </div>

          <!-- NUPTK -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">NUPTK</label>
            <input 
              v-model="form.nuptk" 
              type="text" 
              class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
            />
          </div>

          <!-- Jabatan -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jabatan / Penugasan Khusus</label>
            <input 
              v-model="form.jabatan" 
              type="text" 
              class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
            />
          </div>

          <!-- Pangkalan Sekolah -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pangkalan Satuan Pendidikan</label>
            <input 
              v-model="form.sekolah" 
              type="text" 
              class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
            />
          </div>
        </div>

        <!-- Right Column: Kontak & Rombel -->
        <div class="space-y-6">

          <!-- Kontak Card -->
          <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div class="flex items-center gap-2.5 text-slate-900 font-bold text-sm">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <div>
                  <span class="block leading-tight">Kontak &amp; Panggilan BK</span>
                  <span class="text-[11px] text-slate-400 font-normal">Komunikasi wali siswa &amp; kedinasan</span>
                </div>
              </div>
              <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60 px-2 py-0.5 rounded-full">Responsif</span>
            </div>

            <!-- WhatsApp -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Wali Siswa &amp; Darurat *</label>
              <div class="relative">
                <input 
                  v-model="form.wa" 
                  type="text" 
                  class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
                />
              </div>
              <p class="text-[10px] text-slate-400 mt-1">Dipakai untuk automasi panggilan berkala &amp; surat panggilan orang tua.</p>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Kedinasan (Belajar.id)</label>
              <input 
                v-model="form.email" 
                type="email" 
                class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
              />
            </div>

            <!-- Jam Layanan -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Layanan Konseling Tatap Muka</label>
              <input 
                v-model="form.jamLayanan" 
                type="text" 
                class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
              />
            </div>
          </div>

          <!-- Rombel Binaan Card -->
          <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div class="flex items-center gap-2.5 text-slate-900 font-bold text-sm">
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012-2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <div>
                  <span class="block leading-tight">Rombongan Belajar (Rombel)</span>
                  <span class="text-[11px] text-slate-400 font-normal">Kelas binaan dan pendampingan</span>
                </div>
              </div>
              <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200/60 px-2.5 py-0.5 rounded-full">{{ form.rombel.length }} Kelas</span>
            </div>

            <!-- Badges with Remove -->
            <div class="flex flex-wrap gap-2 pt-1">
              <span
                v-for="(cls, idx) in form.rombel"
                :key="cls"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50/80 hover:bg-blue-100/80 border border-blue-200 text-blue-700 font-bold text-xs rounded-xl shadow-2xs transition"
              >
                <span>{{ cls }}</span>
                <button 
                  @click="removeRombel(idx)" 
                  class="text-blue-400 hover:text-red-500 hover:bg-red-50 p-0.5 rounded-md transition cursor-pointer" 
                  title="Hapus Rombel"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </span>
              <button 
                @click="addRombelPrompt" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition cursor-pointer active:scale-95"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kelas</span>
              </button>
            </div>

            <!-- Ruang Konseling -->
            <div class="pt-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Ruang Konseling &amp; Mediasi Utama</label>
              <div class="relative">
                <input 
                  v-model="form.ruangBK" 
                  type="text" 
                  class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition" 
                />
              </div>
            </div>
          </div>

          <!-- Keamanan Card -->
          <div 
            class="group bg-white rounded-2xl p-4.5 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-indigo-300 hover:bg-indigo-50/30 transition-all cursor-pointer" 
            @click="changePasswordAlert"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
              </div>
              <div>
                <h4 class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-indigo-600 transition-colors">Ubah Kata Sandi Akun</h4>
                <p class="text-[11px] text-slate-400">Terakhir diperbarui 45 hari lalu</p>
              </div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-indigo-100 text-slate-400 group-hover:text-indigo-600 flex items-center justify-center transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>

        </div>

      </div>

      <!-- Action Footer Buttons -->
      <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <button 
          @click="saveProfile" 
          class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-2xl shadow-md shadow-blue-600/20 text-xs sm:text-sm flex items-center justify-center gap-2 transition cursor-pointer active:scale-[0.99]"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
          <span>Simpan Perubahan</span>
        </button>
        <button 
          @click="$router.back()" 
          class="px-6 bg-white hover:bg-slate-100 text-slate-700 font-bold py-3.5 rounded-2xl border border-slate-200 text-xs sm:text-sm transition cursor-pointer flex items-center justify-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Batal</span>
        </button>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, onMounted } from 'vue'
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

const form = ref({
  avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
  name: 'Dra. Hj. Nurul Hidayati, M.Pd',
  nip: '19780415 200312 2 004',
  nuptk: '8452 7566 5830 0012',
  jabatan: 'Koordinator Bimbingan Konseling & Kedisiplinan',
  sekolah: 'SMK PGRI 05 JEMBER',
  wa: '+62 812-3456-7890',
  email: 'nurul.hidayati@guru.smk.belajar.id',
  jamLayanan: '07.00 – 15.30 WIB (Hari Kerja)',
  ruangBK: 'Ruang BK Gedung A Lt. 1',
  rombel: ['X RPL 1', 'X RPL 2', 'XI RPL 1', 'XI RPL 2', 'XII DKV 1', 'XII DKV 2', 'XII DKV 3']
})

onMounted(() => {
  if (props.profileUser) {
    if (props.profileUser.name) form.value.name = props.profileUser.name
    if (props.profileUser.email) form.value.email = props.profileUser.email
    if (props.profileUser.phone) form.value.wa = props.profileUser.phone
    if (props.profileUser.nip) form.value.nip = props.profileUser.nip
  }
})

const removeRombel = (idx) => {
  form.value.rombel.splice(idx, 1)
}

const addRombelPrompt = () => {
  const newClass = prompt('Masukkan nama Rombel baru (contoh: X AKL 1):')
  if (newClass && newClass.trim()) {
    form.value.rombel.push(newClass.trim().toUpperCase())
  }
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (event) => {
      form.value.avatar = event.target.result
    }
    reader.readAsDataURL(file)
  }
}

const saveProfile = () => {
  isSaving.value = true
  router.post('/profil', {
    name: form.value.name,
    email: form.value.email,
    phone: form.value.wa
  }, {
    onSuccess: () => {
      showToast.value = true
      setTimeout(() => {
        showToast.value = false
      }, 3500)
    },
    onFinish: () => {
      isSaving.value = false
    }
  })
}

const syncDapodik = () => {
  alert('Data pendidik berhasil disinkronkan dengan basis data Dapodik Kemdikbud!')
}

const changePasswordAlert = () => {
  alert('Form pergantian kata sandi dikirimkan melalui email kedinasan Anda.')
}
</script>
