<template>
  <div class="min-h-screen bg-slate-50 font-sans pb-10">
    
    <!-- Header -->
    <header class="bg-white border-b border-gray-100 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
      <div class="flex items-center gap-3">
        <button @click="goBack" class="p-1.5 rounded-full hover:bg-gray-100 text-gray-700 -ml-1.5 cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </button>
        <div>
          <h1 class="text-base font-bold text-gray-900 leading-tight">Detail Pelanggaran</h1>
          <p class="text-[10px] font-bold text-gray-500 tracking-wider">BIMBINGAN & KONSELING</p>
        </div>
      </div>
      <div class="flex items-center gap-2 text-gray-600">
        <button @click="printPage" class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Cetak Berkas">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
        </button>
        <button @click="sharePage" class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Bagikan Link Detail">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
          </svg>
        </button>
      </div>
    </header>

    <main class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">
      
      <!-- Student Profile -->
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <div class="flex gap-4 items-start mb-3">
          <img src="https://i.pravatar.cc/150?img=11" alt="Profile" class="w-14 h-14 rounded-full object-cover border-2 border-gray-100">
          <div>
            <h2 class="font-bold text-gray-900 text-lg leading-tight">Muhammad Rizky Pratama</h2>
            <p class="text-xs text-gray-500 mt-1">NISN: 22231084 • XI RPL 2</p>
            <div class="flex items-center gap-1 text-[11px] text-gray-500 mt-0.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
              Wali Kelas: Bpk. Kurniawan, S.Kom
            </div>
          </div>
        </div>
        <div class="bg-red-50 text-red-600 px-3 py-2 rounded-xl text-xs font-bold border border-red-100 flex items-center justify-center gap-1.5 shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          Perhatian Khusus • Panggilan Orang Tua Tahap II
        </div>
      </div>

      <!-- Akumulasi Poin -->
      <div class="bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100 shadow-sm relative overflow-hidden">
        <h3 class="text-[10px] font-bold text-gray-500 mb-1 tracking-wider">AKUMULASI POIN<br>PELANGGARAN</h3>
        <div class="flex justify-between items-end mb-3">
          <div class="flex items-baseline gap-1">
            <span class="text-5xl font-black text-red-600 tracking-tighter">35</span>
            <span class="text-sm font-medium text-gray-500">/ 100 Poin Maks</span>
          </div>
          <div class="bg-red-100 text-red-700 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-red-200">
            Level Peringatan<br>2
          </div>
        </div>

        <!-- Slider -->
        <div class="relative mt-5 mb-6">
          <div class="h-2.5 w-full bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-blue-400 via-amber-400 to-red-500 rounded-full" style="width: 35%"></div>
          </div>
          <!-- Points Markers -->
          <div class="absolute w-full flex justify-between text-[10px] font-bold text-gray-500 mt-2 -px-2">
            <div class="text-center w-10 -ml-4">
              <div>0</div>
              <div class="font-normal">(Aman)</div>
            </div>
            <div class="text-center text-amber-600">
              <div>Ambang SP 1</div>
              <div>(25)</div>
            </div>
            <div class="text-center text-red-600">
              <div>Ambang SP 2</div>
              <div>(50)</div>
            </div>
            <div class="text-center w-10 -mr-2">
              <div>100</div>
              <div class="font-normal">(DO)</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kasus Hari Ini -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex gap-3">
          <div class="mt-1 text-blue-600 bg-blue-50 p-2 rounded-lg h-8 w-8 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="flex-1">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-bold text-gray-900 text-lg">Kasus Hari Ini</h3>
                <p class="text-xs text-gray-500 mt-0.5">Senin, 24 Februari 2025 • 07.38 WIB</p>
              </div>
              <div class="text-right">
                <span class="inline-block bg-red-100 text-red-600 text-xs font-bold px-2.5 py-1 rounded-full mb-1">+15 Poin</span>
                <p class="text-[10px] font-medium text-red-500 leading-tight">Terlambat 38<br>mnt</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="p-4 space-y-4">
          <div class="flex gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <h4 class="text-sm font-bold text-gray-900">Terlambat Masuk Sekolah (>30 Menit)</h4>
              <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Gerbang ditutup pukul 07.00 WIB. Siswa tercatat hadir 07.38 WIB.</p>
            </div>
          </div>
          <div class="flex gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <div>
              <h4 class="text-sm font-bold text-gray-900">Atribut Seragam Tidak Lengkap</h4>
              <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Tidak mengenakan dasi seragam kejuruan resmi hari Senin.</p>
            </div>
          </div>
          
          <div class="flex items-center gap-2 text-xs text-gray-600 pt-2 border-t border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Pencatat Piket: Ibu Sulastri, S.Pd (Gerbang Depan)
          </div>

          <div class="bg-gray-50 p-3 rounded-xl border border-gray-200">
            <h4 class="text-[10px] font-bold text-gray-500 mb-1">KETERANGAN ALASAN SISWA:</h4>
            <p class="text-sm text-gray-700 italic">"Ban motor bocor terkena paku di Jl. Veteran dekat pom bensin, antre di tambal ban dari jam 06.50 WIB."</p>
          </div>
        </div>
      </div>

      <!-- Lampiran Bukti Kejadian -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
        <div class="flex justify-between items-center mb-3">
          <div class="flex items-center gap-2 text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3 class="font-bold text-gray-900">Lampiran Bukti Kejadian</h3>
          </div>
          <button class="text-xs font-bold text-blue-600 flex items-center gap-1">
            Resolusi Penuh
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
          </button>
        </div>

        <div class="relative rounded-xl overflow-hidden mb-4 border border-gray-200">
          <img src="https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Bukti Kejadian" class="w-full h-48 object-cover">
          <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            Bengkel Pak Adi • 07.12 WIB
          </div>
          <div class="absolute bottom-2 left-2 text-white text-[9px] drop-shadow-md">
            Koordinat: -7.7829, 110.3671
          </div>
          <div class="absolute bottom-2 right-2 bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded font-bold">
            Verifikasi GPS
          </div>
        </div>

        <div class="bg-red-50 rounded-xl p-3 border border-red-100">
          <div class="flex justify-between items-start mb-2">
            <div class="flex gap-2 items-center text-red-600 font-bold text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
              Verifikasi BK: Ditolak Sebagai Alasan Sah
            </div>
            <div class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded text-center leading-tight">
              Poin Tetap<br>Masuk
            </div>
          </div>
          <p class="text-xs text-red-900 leading-relaxed font-medium">
            Catatan Guru BK (Dra. Hj. Nurhidayati): "Siswa tercatat sudah 4 kali dalam bulan ini menggunakan alasan ban bocor pada hari Senin. Diwajibkan hadir bersama orang tua untuk pembinaan rute & kesiapan transportasi."
          </p>
        </div>
      </div>

      <!-- Riwayat Pelanggaran -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
        <div class="flex justify-between items-center mb-4">
          <div class="flex items-center gap-2 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="font-bold text-gray-900">Riwayat Pelanggaran Siswa</h3>
          </div>
          <div class="bg-blue-50 text-blue-800 text-[10px] font-bold px-2 py-1 rounded text-center leading-tight">
            Total: 4 Kasus<br>(Februari)
          </div>
        </div>

        <div class="relative border-l-2 border-gray-100 ml-3 mt-4 space-y-6 pb-2">
          <!-- Item 1 -->
          <div class="relative pl-6">
            <div class="absolute -left-1.5 top-1.5 w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow-sm"></div>
            <div class="flex justify-between items-start mb-1">
              <h4 class="font-bold text-sm text-gray-900">Jumat, 21 Feb 2025 (07.35 WIB)</h4>
              <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-red-100">+10 Poin</span>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed mb-2">Terlambat 35 menit. Alasan: Bangun kesiangan setelah lembur tugas.</p>
            <div class="flex items-start gap-1.5 text-amber-700 text-xs font-medium bg-amber-50 p-2 rounded-lg border border-amber-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Sanksi: Teguran Lisan & Pembinaan Guru Piket
            </div>
          </div>
          
          <!-- Item 2 -->
          <div class="relative pl-6">
            <div class="absolute -left-1.5 top-1.5 w-3 h-3 bg-amber-500 rounded-full border-2 border-white shadow-sm"></div>
            <div class="flex justify-between items-start mb-1">
              <h4 class="font-bold text-sm text-gray-900">Senin, 17 Feb 2025 (07.22 WIB)</h4>
              <span class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-100">+5 Poin</span>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed mb-2">Terlambat 22 menit. Alasan: Macet perlintasan kereta api.</p>
            <div class="flex items-start gap-1.5 text-amber-700 text-xs font-medium bg-amber-50 p-2 rounded-lg border border-amber-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Sanksi: Bersih Lingkungan Sekolah (Area Masjid)
            </div>
          </div>

          <!-- Item 3 -->
          <div class="relative pl-6">
            <div class="absolute -left-1.5 top-1.5 w-3 h-3 bg-amber-500 rounded-full border-2 border-white shadow-sm"></div>
            <div class="flex justify-between items-start mb-1">
              <h4 class="font-bold text-sm text-gray-900">Senin, 10 Feb 2025 (07.18 WIB)</h4>
              <span class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-100">+5 Poin</span>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed mb-2">Terlambat 18 menit. Alasan: Ban bocor di Jl. Magelang.</p>
            <div class="flex items-start gap-1.5 text-amber-700 text-xs font-medium bg-amber-50 p-2 rounded-lg border border-amber-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Sanksi: Janji Tertulis Buku Tata Tertib Siswa
            </div>
          </div>
        </div>
      </div>

      <!-- Tindak Lanjut -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mt-2">
        <div class="flex gap-3 mb-4">
          <div class="bg-red-50 p-2 rounded-lg h-9 w-9 flex items-center justify-center text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <h3 class="font-bold text-gray-900">Tindak Lanjut Konseling Guru BK</h3>
            <p class="text-xs text-gray-500 leading-tight mt-0.5">Kewajiban penanganan disiplin sesuai Panduan Tatib SMK</p>
          </div>
        </div>

        <div class="space-y-3">
          <router-link to="/pemanggilan" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-md flex items-center justify-center gap-2 cursor-pointer transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Terbitkan Surat Panggilan Orang Tua (SP-2)</span>
          </router-link>
          
          <div class="flex gap-3">
            <button 
              type="button" 
              @click="scheduleCounseling" 
              class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-3 rounded-xl border border-blue-100 flex items-center justify-center gap-2 text-sm shadow-sm transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Jadwal Konseling</span>
            </button>
            <button 
              type="button" 
              @click="sendWhatsApp" 
              class="flex-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold py-3 rounded-xl border border-emerald-100 flex items-center justify-center gap-2 text-sm shadow-sm transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <span>WA Orang Tua</span>
            </button>
          </div>

          <button 
            type="button" 
            @click="openTindakanModal" 
            class="w-full bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 font-bold py-3 rounded-xl flex items-center justify-center gap-2 text-sm shadow-sm transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Input Tindakan / Catatan Pembinaan Khusus</span>
          </button>
        </div>
      </div>
      
      <p class="text-center text-[10px] text-gray-400 mt-6 px-4">
        Sistem Kedisiplinan & Kehadiran Terpadu HadirMu • Dokumen Digital Resmi Sekolah
      </p>

    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  violation: {
    type: Object,
    default: null
  }
})

const photoUrl = 'https://images.unsplash.com/photo-1590240974866-9eb10a17ebbb?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'

const studentName = computed(() => props.violation?.student_name || 'Muhammad Rizky Pratama')
const studentClass = computed(() => props.violation?.student_class || 'XI RPL 2')
const studentPoints = computed(() => props.violation?.points || 35)

const goBack = () => {
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/pelanggaran')
  }
}

const printPage = () => {
  window.print()
}

const sharePage = () => {
  if (navigator.share) {
    navigator.share({
      title: `Detail Pelanggaran - ${studentName.value}`,
      url: window.location.href
    }).catch(() => {})
  } else {
    alert('Tautan halaman detail pelanggaran berhasil disalin ke clipboard!')
  }
}

const viewFullImage = () => {
  window.open(photoUrl, '_blank')
}

const scheduleCounseling = () => {
  const dateStr = prompt('Masukkan tanggal dan waktu konseling siswa:', 'Rabu, 26 Feb 2025 pukul 09.00 WIB')
  if (dateStr && dateStr.trim()) {
    alert(`Jadwal konseling berhasil ditetapkan pada ${dateStr} di Ruang Konseling BK.`)
  }
}

const sendWhatsApp = () => {
  const text = encodeURIComponent(
    `Yth. Bapak/Ibu Wali dari ${studentName.value} (${studentClass.value}), kami menginformasikan catatan kedisiplinan siswa di sekolah. Mohon konfirmasi untuk koordinasi dengan Guru BK. Terima kasih.`
  )
  window.open(`https://wa.me/6281234567890?text=${text}`, '_blank')
}

const openTindakanModal = () => {
  const note = prompt('Tuliskan catatan tindakan pembinaan khusus:', 'Siswa telah diberikan pengarahan kedisiplinan dan menandatangani komitmen tertulis.')
  if (note && note.trim()) {
    alert('Catatan tindakan pembinaan khusus berhasil disimpan ke rekam jejak siswa.')
  }
}
</script>
