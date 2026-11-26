<template>
  <AppLayout active-tab="verifikasi" show-back>
    <div class="max-w-2xl mx-auto space-y-5">

      <!-- Toast Notification -->
      <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showSuccessToast" class="fixed top-20 right-4 sm:right-8 z-50 bg-white border border-emerald-200 rounded-xl shadow-lg p-3.5 flex items-center gap-3 text-xs max-w-sm">
          <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <p class="font-bold text-slate-900">Data Tersimpan</p>
            <p class="text-slate-500 text-[11px]">Catatan pelanggaran berhasil disimpan.</p>
          </div>
        </div>
      </transition>

      <!-- Main Form Container -->
      <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-xs space-y-5">
        
        <!-- Header -->
        <div class="border-b border-slate-200 pb-4">
          <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Input Pelanggaran</h1>
        </div>

        <!-- Server Validation Error Alert Banner -->
        <div v-if="Object.keys(serverErrors).length > 0" class="p-3.5 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-800 space-y-1">
          <div class="font-bold flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Terjadi kesalahan pengisian formulir:</span>
          </div>
          <ul class="list-disc list-inside text-[11px] text-rose-700 space-y-0.5">
            <li v-for="(err, key) in serverErrors" :key="key">{{ Array.isArray(err) ? err[0] : err }}</li>
          </ul>
        </div>

        <!-- Identitas Siswa -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <label class="text-xs sm:text-sm font-semibold text-slate-800">
              Identitas Siswa
            </label>
            <button
              type="button"
              @click="isManualInput = !isManualInput"
              class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 underline transition cursor-pointer"
            >
              {{ isManualInput ? 'Pilih dari Database' : 'Input Manual' }}
            </button>
          </div>

          <!-- Mode 1: Pilih Siswa dari Database (Default) -->
          <div v-if="!isManualInput" class="space-y-3">
            
            <!-- Filter & Selection Row -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
              <!-- Filter Kelas -->
              <div class="sm:col-span-4">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kelas</label>
                <div class="relative">
                  <select
                    v-model="selectedClassFilter"
                    class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 transition cursor-pointer appearance-none pr-8"
                  >
                    <option value="Semua">Semua Kelas</option>
                    <option v-for="cls in availableClasses" :key="cls" :value="cls">Kelas {{ cls }}</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Pilih Siswa Terdaftar -->
              <div class="sm:col-span-8">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                  Siswa
                </label>
                <div class="relative">
                  <select
                    v-model="selectedStudentId"
                    @change="onStudentSelectChange"
                    class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 transition cursor-pointer appearance-none pr-8"
                  >
                    <option :value="null">Pilih siswa...</option>
                    <option v-for="s in filteredStudentsByClass" :key="s.id" :value="s.id">
                      {{ s.name }} &bull; {{ s.class_name }} (NISN: {{ s.nis || s.nisn }})
                    </option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Kartu Siswa Terpilih (Selected Student Confirmation Box) -->
            <div 
              v-if="selectedStudentObj" 
              class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between gap-3 text-xs"
            >
              <div>
                <h4 class="font-bold text-slate-900 text-xs">{{ selectedStudentObj.name }}</h4>
                <p class="text-slate-500 text-[11px] mt-0.5">
                  NISN: {{ selectedStudentObj.nis || selectedStudentObj.nisn }} &bull; Kelas: {{ selectedStudentObj.class_name }} &bull; Poin: {{ selectedStudentObj.total_violation_points || 0 }}
                </p>
              </div>

              <button
                type="button"
                @click="resetStudentSelection"
                class="px-2 py-1 rounded text-slate-500 hover:text-slate-800 hover:bg-slate-200 text-[11px] font-medium transition cursor-pointer"
              >
                Ganti
              </button>
            </div>

          </div>

          <!-- Mode 2: Input Manual -->
          <div v-else class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                <input
                  v-model="form.studentName"
                  type="text"
                  placeholder="Ketik nama lengkap siswa..."
                  class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition"
                />
              </div>
              <div class="sm:col-span-1">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kelas</label>
                <input
                  v-model="form.studentClass"
                  type="text"
                  placeholder="Contoh: X RPL 1"
                  class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Pelanggaran -->
        <div class="space-y-2.5 pt-2 border-t border-slate-100">
          <label class="block text-xs sm:text-sm font-semibold text-slate-800">
            Pelanggaran
          </label>

          <!-- Input Kategori Pelanggaran -->
          <input
            v-model="form.category"
            type="text"
            placeholder="Ketik jenis pelanggaran..."
            class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition"
          />

          <!-- Quick Category Tags -->
          <div class="flex flex-wrap items-center gap-1.5 text-xs">
            <button
              v-for="cat in commonCategories"
              :key="cat"
              type="button"
              @click="form.category = cat"
              class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition cursor-pointer"
            >
              {{ cat }}
            </button>
          </div>

          <!-- Description Textarea -->
          <div>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Keterangan / kronologi singkat..."
              class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg p-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition leading-relaxed"
            ></textarea>
          </div>
        </div>

        <!-- Poin Pelanggaran (Opsional) -->
        <div class="space-y-2.5 pt-2 border-t border-slate-100">
          <div class="flex items-center justify-between">
            <label class="text-xs sm:text-sm font-semibold text-slate-800">
              Poin (Opsional)
            </label>
            <span class="text-[11px] text-slate-500 font-medium">
              {{ (form.points === null || form.points === '') ? 'Kasus Pembinaan (0 Poin)' : `+${form.points} Poin` }}
            </span>
          </div>

          <div>
            <input
              v-model="form.points"
              type="number"
              min="0"
              max="100"
              placeholder="0 (kosongkan jika pembinaan)"
              class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-400 transition"
            />
          </div>

          <!-- Quick Points Selector -->
          <div class="flex flex-wrap items-center gap-1.5 text-xs">
            <button
              type="button"
              @click="form.points = null"
              class="px-2 py-0.5 rounded-md border text-xs font-medium transition cursor-pointer"
              :class="(form.points === null || form.points === '') ? 'bg-blue-50 border-blue-300 text-blue-700 font-bold' : 'bg-slate-100 border-slate-200 text-slate-600 hover:bg-slate-200'"
            >
              Tanpa Poin
            </button>
            <button
              v-for="pt in [5, 10, 15, 20, 25, 30, 50]"
              :key="pt"
              type="button"
              @click="form.points = pt"
              class="px-2 py-0.5 rounded-md border text-xs font-medium transition cursor-pointer"
              :class="form.points === pt ? 'bg-blue-50 border-blue-300 text-blue-700 font-bold' : 'bg-slate-100 border-slate-200 text-slate-600 hover:bg-slate-200'"
            >
              +{{ pt }}
            </button>
          </div>
        </div>

        <!-- Bukti Foto -->
        <div class="space-y-2.5 pt-2 border-t border-slate-100">
          <div class="flex items-center justify-between">
            <label class="text-xs sm:text-sm font-semibold text-slate-800">
              Bukti Foto (Opsional)
            </label>
            <span 
              v-if="form.photoUrl" 
              class="text-xs font-medium text-emerald-700"
            >
              Foto Terlampir
            </span>
          </div>

          <!-- Photo State: If Photo Exists -->
          <div 
            v-if="form.photoUrl"
            class="relative rounded-lg overflow-hidden border border-slate-200 bg-slate-900 group"
          >
            <img
              :src="form.photoUrl"
              alt="Bukti Foto"
              class="w-full max-h-56 object-contain bg-slate-950 mx-auto"
            />
            <div class="absolute inset-x-0 bottom-0 bg-slate-950/80 p-2.5 flex items-center justify-between">
              <span class="text-white text-xs font-medium">Foto Terlampir</span>
              <button
                type="button"
                @click="removePhoto"
                class="px-2 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-semibold transition cursor-pointer"
              >
                Hapus
              </button>
            </div>
          </div>

          <!-- Photo State: Empty Placeholder Compact -->
          <div 
            v-else
            class="rounded-lg border border-dashed border-slate-300 bg-slate-50/50 p-4 text-center flex items-center justify-center gap-2 text-slate-500 text-xs"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Belum ada bukti foto</span>
          </div>

          <!-- Upload & Camera Actions -->
          <div class="grid grid-cols-2 gap-2.5 pt-1">
            <button
              type="button"
              @click="openCameraModal"
              class="py-2 px-3 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium text-xs flex items-center justify-center gap-1.5 border border-slate-200 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ form.photoUrl ? 'Foto Ulang' : 'Kamera' }}</span>
            </button>
            <button
              type="button"
              @click="triggerUpload"
              class="py-2 px-3 rounded-lg bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs flex items-center justify-center gap-1.5 border border-slate-200 transition cursor-pointer shadow-2xs"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Galeri</span>
            </button>
            <input 
              type="file" 
              ref="fileUpload" 
              @change="onPhotoChange" 
              class="hidden" 
              accept="image/*" 
            />
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-3 border-t border-slate-200">
          <button
            type="button"
            :disabled="isSubmitting"
            @click="handleSubmit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-xs sm:text-sm flex items-center justify-center gap-2 transition cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed shadow-2xs"
          >
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-else>{{ isSubmitting ? 'Menyimpan...' : 'Simpan Pelanggaran' }}</span>
          </button>
        </div>

      </div>

    </div>

    <!-- ========================================================= -->
    <!-- CAMERA CAPTURE MODAL                                      -->
    <!-- ========================================================= -->
    <div 
      v-if="isCameraModalOpen" 
      class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 animate-fadeIn"
      @click.self="closeCameraModal"
    >
      <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl flex flex-col text-slate-100">
        
        <!-- Modal Top Bar -->
        <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between bg-slate-950">
          <div class="flex items-center gap-2">
            <span class="text-xs sm:text-sm font-bold text-white tracking-wide">Kamera Bukti Pelanggaran</span>
          </div>
          <button 
            type="button" 
            @click="closeCameraModal" 
            class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Camera Viewfinder / Preview Area -->
        <div class="relative bg-black aspect-4/3 w-full flex items-center justify-center overflow-hidden">
          
          <!-- Live Video Stream -->
          <video
            v-show="!snapshotPreview && !cameraError"
            ref="videoElement"
            autoplay
            playsinline
            muted
            class="w-full h-full object-cover"
          ></video>

          <!-- Snapshot Review Preview -->
          <img
            v-if="snapshotPreview"
            :src="snapshotPreview"
            alt="Hasil Foto"
            class="w-full h-full object-cover"
          />

          <!-- Viewfinder Corner Guides (when streaming) -->
          <div v-if="!snapshotPreview && !cameraError" class="absolute inset-4 pointer-events-none">
            <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-white/70"></div>
            <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-white/70"></div>
            <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-white/70"></div>
            <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-white/70"></div>
          </div>

          <!-- Camera Loading Indicator -->
          <div v-if="isCameraStarting" class="absolute inset-0 bg-slate-950/80 flex flex-col items-center justify-center gap-2">
            <svg class="animate-spin h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-xs text-slate-300">Menghubungkan ke kamera...</span>
          </div>

          <!-- Camera Error Fallback -->
          <div v-if="cameraError" class="p-6 text-center max-w-sm">
            <div class="w-10 h-10 rounded-full bg-rose-500/10 text-rose-400 flex items-center justify-center mx-auto mb-2.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <p class="text-xs text-rose-300 font-semibold mb-1">Akses Kamera Terkendala</p>
            <p class="text-[11px] text-slate-400 mb-4 leading-relaxed">{{ cameraError }}</p>
            <button
              type="button"
              @click="closeCameraModal(); triggerUpload()"
              class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition cursor-pointer"
            >
              Pilih dari File Perangkat
            </button>
          </div>

        </div>

        <!-- Hidden Canvas for Snapshot Capture -->
        <canvas ref="canvasElement" class="hidden"></canvas>

        <!-- Modal Bottom Actions -->
        <div class="px-5 py-4 bg-slate-950 border-t border-slate-800 flex items-center justify-between">
          
          <!-- State 1: Streaming View -->
          <template v-if="!snapshotPreview && !cameraError">
            <button
              type="button"
              @click="toggleCameraFacing"
              class="p-2.5 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition cursor-pointer"
              title="Ganti Kamera Depan/Belakang"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>

            <!-- Big Shutter Button -->
            <button
              type="button"
              @click="takeSnapshot"
              class="w-14 h-14 rounded-full border-4 border-white bg-blue-600 hover:bg-blue-500 active:scale-95 transition shadow-lg flex items-center justify-center cursor-pointer"
              title="Ambil Foto"
            >
              <div class="w-5 h-5 rounded-full bg-white"></div>
            </button>

            <button
              type="button"
              @click="closeCameraModal"
              class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-white transition cursor-pointer"
            >
              Batal
            </button>
          </template>

          <!-- State 2: Review Snapshot -->
          <template v-if="snapshotPreview">
            <button
              type="button"
              @click="retakeSnapshot"
              class="px-3.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>Ulangi</span>
            </button>

            <button
              type="button"
              @click="useCapturedSnapshot"
              class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 transition cursor-pointer shadow-md"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>Gunakan Foto Ini</span>
            </button>
          </template>

        </div>

      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
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
const serverErrors = ref({})
const isSubmitting = ref(false)
const fileUpload = ref(null)
const photoFile = ref(null)

// Camera State
const isCameraModalOpen = ref(false)
const isCameraStarting = ref(false)
const videoElement = ref(null)
const canvasElement = ref(null)
const mediaStream = ref(null)
const snapshotPreview = ref(null)
const cameraError = ref('')
const facingMode = ref('environment') // 'environment' (back) or 'user' (front)

// Categories for quick selection
const commonCategories = [
  'Terlambat Masuk',
  'Atribut Tidak Lengkap',
  'Keluar Lingkungan Sekolah',
  'Merokok / Vape',
  'Pelanggaran Ketertiban'
]

// Student Selection State (Reuse database students)
const selectedClassFilter = ref('Semua')
const selectedStudentId = ref(null)
const isManualInput = ref(false)

const availableClasses = computed(() => {
  if (!props.studentsList || props.studentsList.length === 0) return []
  const classes = new Set(props.studentsList.map(s => s.class_name).filter(Boolean))
  return Array.from(classes).sort()
})

const filteredStudentsByClass = computed(() => {
  if (!props.studentsList) return []
  if (selectedClassFilter.value === 'Semua') {
    return props.studentsList
  }
  return props.studentsList.filter(s => s.class_name === selectedClassFilter.value)
})

const selectedStudentObj = computed(() => {
  if (!selectedStudentId.value || !props.studentsList) return null
  return props.studentsList.find(s => s.id === Number(selectedStudentId.value)) || null
})

const onStudentSelectChange = () => {
  const student = selectedStudentObj.value
  if (student) {
    form.value.studentId = student.id
    form.value.studentName = student.name
    form.value.studentClass = student.class_name
  } else {
    resetStudentSelection()
  }
}

const resetStudentSelection = () => {
  selectedStudentId.value = null
  form.value.studentId = null
  form.value.studentName = ''
  form.value.studentClass = ''
}

// Clean Form with NO DUMMY DATA
const form = ref({
  studentId: null,
  studentName: '',
  studentClass: '',
  category: '',
  description: '',
  points: null,
  photoUrl: null,
  photoTimestamp: ''
})

onMounted(() => {
  if (route.query) {
    if (route.query.student_id && props.studentsList) {
      const found = props.studentsList.find(s => s.id === Number(route.query.student_id))
      if (found) {
        selectedStudentId.value = found.id
        onStudentSelectChange()
        return
      }
    }
    if (route.query.nama && props.studentsList) {
      const found = props.studentsList.find(s => s.name.toLowerCase() === route.query.nama.toLowerCase())
      if (found) {
        selectedStudentId.value = found.id
        onStudentSelectChange()
        return
      }
      form.value.studentName = route.query.nama
      if (route.query.kelas) form.value.studentClass = route.query.kelas
      isManualInput.value = true
    }
  }
})

// Open Camera Modal & Start Stream
const openCameraModal = async () => {
  isCameraModalOpen.value = true
  snapshotPreview.value = null
  cameraError.value = ''
  isCameraStarting.value = true

  await nextTick()
  await startCameraStream()
}

// Start User Media
const startCameraStream = async () => {
  stopCameraStream()
  isCameraStarting.value = true
  cameraError.value = ''

  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    cameraError.value = 'Browser atau perangkat ini tidak mengizinkan akses kamera langsung. Silakan gunakan tombol galeri.'
    isCameraStarting.value = false
    return
  }

  try {
    const constraints = {
      video: {
        facingMode: facingMode.value,
        width: { ideal: 1280 },
        height: { ideal: 720 }
      },
      audio: false
    }

    let stream = null
    try {
      stream = await navigator.mediaDevices.getUserMedia(constraints)
    } catch (err) {
      // Fallback without facingMode constraint if device rejects specific mode
      stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false })
    }

    mediaStream.value = stream
    if (videoElement.value) {
      videoElement.value.srcObject = stream
      await videoElement.value.play().catch(() => {})
    }
  } catch (err) {
    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
      cameraError.value = 'Izin kamera ditolak oleh browser. Berikan izin kamera pada pengaturan situs atau unggah berkas dari galeri.'
    } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
      cameraError.value = 'Perangkat kamera tidak ditemukan pada komputer/ponsel ini.'
    } else {
      cameraError.value = `Gagal mengaktifkan kamera (${err.message || 'Error'}). Silakan unggah foto dari berkas galeri.`
    }
  } finally {
    isCameraStarting.value = false
  }
}

// Toggle Front/Back Camera
const toggleCameraFacing = async () => {
  facingMode.value = facingMode.value === 'environment' ? 'user' : 'environment'
  await startCameraStream()
}

// Take Snapshot
const takeSnapshot = () => {
  const video = videoElement.value
  const canvas = canvasElement.value
  if (!video || !canvas) return

  const width = video.videoWidth || 640
  const height = video.videoHeight || 480

  canvas.width = width
  canvas.height = height

  const ctx = canvas.getContext('2d')
  ctx.drawImage(video, 0, 0, width, height)

  snapshotPreview.value = canvas.toDataURL('image/jpeg', 0.85)
}

// Retake Snapshot
const retakeSnapshot = () => {
  snapshotPreview.value = null
}

// Use Captured Snapshot
const useCapturedSnapshot = () => {
  if (snapshotPreview.value) {
    form.value.photoUrl = snapshotPreview.value
    photoFile.value = null
    const now = new Date()
    const dateFormatted = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
    const timeFormatted = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    form.value.photoTimestamp = `Diambil: ${dateFormatted}, ${timeFormatted} WIB • Validasi Kamera`
  }
  closeCameraModal()
}

// Stop Stream & Close Modal
const stopCameraStream = () => {
  if (mediaStream.value) {
    mediaStream.value.getTracks().forEach(track => track.stop())
    mediaStream.value = null
  }
}

const closeCameraModal = () => {
  stopCameraStream()
  snapshotPreview.value = null
  isCameraModalOpen.value = false
}

// Clean up camera when page unmounts
onBeforeUnmount(() => {
  stopCameraStream()
})

// Gallery File Input
const triggerUpload = () => {
  fileUpload.value?.click()
}

const onPhotoChange = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    if (file.size > 2097152) {
      alert('Ukuran berkas foto bukti maksimal adalah 2 MB (2048 KB).')
      if (e.target) e.target.value = ''
      return
    }
    photoFile.value = file
    const reader = new FileReader()
    reader.onload = (event) => {
      form.value.photoUrl = event.target.result
      const now = new Date()
      const dateFormatted = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
      const timeFormatted = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
      form.value.photoTimestamp = `Diunggah: ${dateFormatted}, ${timeFormatted} WIB • ${file.name}`
    }
    reader.readAsDataURL(file)
  }
  // reset input so same file can be selected again
  if (e.target) {
    e.target.value = ''
  }
}

// Remove Attached Photo
const removePhoto = () => {
  form.value.photoUrl = null
  form.value.photoTimestamp = ''
  photoFile.value = null
}

// Submit Form
const handleSubmit = () => {
  serverErrors.value = {}

  if (!form.value.studentName.trim()) {
    alert('Mohon pilih siswa terlapor dari database atau masukkan nama siswa.')
    return
  }
  if (!form.value.category.trim()) {
    alert('Mohon isi jenis atau kategori pelanggaran.')
    return
  }

  isSubmitting.value = true
  const pointsVal = (form.value.points !== null && form.value.points !== '' && !isNaN(Number(form.value.points))) 
    ? Number(form.value.points) 
    : null

  const payload = {
    student_name: form.value.studentName.trim(),
    category: form.value.category.trim(),
    violation_name: form.value.category.trim(),
    date: new Date().toISOString().slice(0, 10),
    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
    reporter: 'Guru Piket & BK',
    penalty_action: pointsVal === null ? 'Pembinaan & teguran lisan' : 'Teguran tertulis & pencatatan kedisiplinan',
  }

  if (form.value.studentId) {
    payload.student_id = form.value.studentId
  }
  if (form.value.studentClass) {
    payload.student_class = form.value.studentClass
  }
  if (form.value.description) {
    payload.notes = form.value.description
  }
  if (pointsVal !== null) {
    payload.points = pointsVal
    payload.poin = pointsVal
  }
  if (photoFile.value) {
    payload.foto_bukti = photoFile.value
  } else if (form.value.photoUrl && form.value.photoUrl.startsWith('data:image')) {
    payload.photo_url = form.value.photoUrl
  }

  router.post('/pelanggaran', payload, {
    forceFormData: true,
    onSuccess: () => {
      serverErrors.value = {}
      showSuccessToast.value = true
      resetStudentSelection()
      photoFile.value = null
      form.value = {
        studentId: null,
        studentName: '',
        studentClass: '',
        category: '',
        description: '',
        points: null,
        photoUrl: null,
        photoTimestamp: ''
      }
      setTimeout(() => {
        showSuccessToast.value = false
      }, 3000)
    },
    onError: (errors) => {
      serverErrors.value = errors || {}
      const firstError = Object.values(errors).flat()[0]
      alert(firstError || 'Gagal menyimpan catatan pelanggaran. Periksa kembali isian formulir.')
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}
</script>
