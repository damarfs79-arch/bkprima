<template>
  <AppLayout active-tab="pemanggilan" show-share>
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- ============================================================ -->
      <!-- 1. HEADER SECTION                                            -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wider text-rose-600 mb-1">
            Layanan Advokasi &amp; Penanganan Kasus Siswa
          </p>
          <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
            Pemanggilan Orang Tua Siswa
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Penerbitan Surat Panggilan (SP-1, SP-2, SP-3), notifikasi WhatsApp wali murid, dan berita acara konseling mediasi.
          </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
          <button
            @click="openNewSummonsModal"
            class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold px-4 py-2 rounded-lg text-xs transition cursor-pointer shrink-0"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Terbitkan Surat Panggilan Baru</span>
          </button>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 2. KPI METRICS CARDS (4 STATUS SUMMONS)                       -->
      <!-- ============================================================ -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Panggilan -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Surat</span>
            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </span>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-slate-900">{{ summonsList.length }}</div>
          <p class="text-xs text-slate-500 font-medium">Semester Ganjil 2024/2025</p>
        </div>

        <!-- Menunggu Kehadiran -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Menunggu Kehadiran</span>
            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-slate-900">{{ stats.waiting }}</div>
          <p class="text-xs text-amber-700 font-medium">Jadwal sidang minggu ini</p>
        </div>

        <!-- Mediasi Selesai -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mediasi Selesai</span>
            <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-slate-900">{{ stats.resolved }}</div>
          <p class="text-xs text-emerald-700 font-medium">Ada berita acara komitmen</p>
        </div>

        <!-- SP-3 Konferensi Kasus -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-1">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-rose-600">SP-3 / Sidang Pleno</span>
            <span class="w-6 h-6 rounded-md bg-rose-50 text-rose-600 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </span>
          </div>
          <div class="text-2xl sm:text-3xl font-bold text-slate-900">{{ stats.sp3 }}</div>
          <p class="text-xs text-rose-600 font-medium">Kasus berat / butuh waka</p>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- 3. MAIN TABLE & FILTER PANEL                                 -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Filter Toolbar -->
        <div class="p-4 sm:p-5 bg-white border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
          
          <!-- Category Pills -->
          <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide py-1">
            <button
              v-for="cat in filterCategories"
              :key="cat.key"
              @click="activeCategory = cat.key"
              class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer whitespace-nowrap"
              :class="activeCategory === cat.key ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200'"
            >
              {{ cat.label }}
            </button>
          </div>

          <!-- Search & Grade Filter -->
          <div class="flex items-center gap-2">
            <div class="relative w-full sm:w-64">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari siswa, NISN, no surat..."
                class="w-full bg-white border border-slate-300 rounded-lg pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              />
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </span>
            </div>

            <select
              v-model="selectedClassFilter"
              class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer"
            >
              <option value="ALL">Semua Kelas</option>
              <option value="X">Kelas 10 (X)</option>
              <option value="XI">Kelas 11 (XI)</option>
              <option value="XII">Kelas 12 (XII)</option>
            </select>
          </div>

        </div>

        <!-- Table of Summons -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
              <tr>
                <th class="py-3 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                <th class="py-3 px-4 text-[11px] uppercase tracking-wider">NO SURAT RESMI</th>
                <th class="py-3 px-4 text-[11px] uppercase tracking-wider">SISWA &amp; KELAS</th>
                <th class="py-3 px-4 text-[11px] uppercase tracking-wider">KASUS &amp; ALASAN</th>
                <th class="py-3 px-3 text-center text-[11px] uppercase tracking-wider">TAHAP</th>
                <th class="py-3 px-4 text-[11px] uppercase tracking-wider">JADWAL MENGHADAP</th>
                <th class="py-3 px-4 text-[11px] uppercase tracking-wider">WALI MURID &amp; WA</th>
                <th class="py-3 px-3 text-center text-[11px] uppercase tracking-wider">STATUS</th>
                <th class="py-3 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-for="(item, idx) in filteredSummons" :key="item.id" class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                <td class="py-3 px-4 font-mono font-medium text-slate-700">
                  <div class="font-bold text-slate-900">{{ item.letterNumber }}</div>
                  <div class="text-[10px] text-slate-400">Diterbitkan: {{ item.issueDate }}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ item.studentName }}</div>
                  <div class="text-[11px] text-slate-500 font-mono">
                    NISN: {{ item.nisn }} &bull; <span class="font-bold text-blue-700">{{ item.className }}</span>
                  </div>
                </td>
                <td class="py-3 px-4 max-w-xs">
                  <div class="font-bold text-slate-800 truncate">{{ item.caseTitle }}</div>
                  <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                    <span class="font-semibold text-[11px]" :class="item.points >= 75 ? 'text-rose-700' : 'text-amber-700'">
                      +{{ item.points }} Poin
                    </span>
                    <span>&bull;</span>
                    <span class="truncate">{{ item.caseDescription }}</span>
                  </div>
                </td>
                <td class="py-3 px-3 text-center">
                  <span
                    class="text-xs font-semibold"
                    :class="{
                      'text-amber-700': item.stage === 'SP-1',
                      'text-orange-700': item.stage === 'SP-2',
                      'text-rose-700': item.stage === 'SP-3'
                    }"
                  >
                    {{ item.stage }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-800">{{ item.meetingDate }}</div>
                  <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ item.meetingTime }} di {{ item.room }}</span>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-800">{{ item.parentName }}</div>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[11px] font-mono text-slate-500">{{ item.parentPhone }}</span>
                    <button
                      @click="sendWhatsApp(item)"
                      class="px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-md font-bold text-[10px] border border-emerald-200 cursor-pointer flex items-center gap-1"
                      title="Kirim Undangan WhatsApp"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                      </svg>
                      <span>WA</span>
                    </button>
                  </div>
                </td>
                <td class="py-3 px-3 text-center">
                  <span
                    class="text-xs font-semibold"
                    :class="{
                      'text-blue-700': item.status === 'Menunggu Kehadiran',
                      'text-emerald-700': item.status === 'Mediasi Selesai',
                      'text-rose-700': item.status === 'Mangkir / Panggil Ulang'
                    }"
                  >
                    {{ item.status }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button
                      @click="previewLetter(item)"
                      class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold border border-blue-200 flex items-center gap-1 cursor-pointer transition"
                      title="Lihat &amp; Cetak Surat Dinas"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <span>Surat</span>
                    </button>
                    <button
                      @click="openMediationModal(item)"
                      class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 flex items-center gap-1 cursor-pointer transition"
                      title="Catat Berita Acara Mediasi"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      <span>Notula</span>
                    </button>
                    <button
                      @click="deleteSummons(item)"
                      class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200 cursor-pointer transition"
                      title="Hapus Surat Panggilan"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredSummons.length === 0">
                <td colspan="9" class="py-12 text-center text-slate-400 text-xs">
                  Tidak ada surat panggilan orang tua yang sesuai dengan filter.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>

      <!-- ============================================================ -->
      <!-- MODAL 1: FORM TERBITKAN SURAT PANGGILAN BARU                 -->
      <!-- ============================================================ -->
      <div v-if="showNewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 animate-in fade-in">
        <div class="bg-white rounded-xl max-w-xl w-full border border-slate-200 shadow-lg overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh] flex flex-col">
          
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-black text-slate-900 text-base">Terbitkan Surat Panggilan Orang Tua</h3>
              <p class="text-xs text-slate-500">Penerbitan surat dinas resmi BK untuk siswa bermasalah</p>
            </div>
            <button @click="showNewModal = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitNewSummons" class="p-5 overflow-y-auto space-y-4 flex-1 text-xs">
            
            <!-- Pilih Siswa -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Pilih Siswa Bermasalah *</label>
              <select
                v-model="newSummons.studentId"
                @change="onSelectStudentForSummons"
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              >
                <option value="" disabled>-- Pilih siswa dari daftar kasus disiplin --</option>
                <option v-for="s in problematicStudents" :key="s.id" :value="s.id">
                  {{ s.name }} ({{ s.className }}) &bull; Akumulasi: {{ s.points }} Poin
                </option>
              </select>
            </div>

            <!-- Tahap Surat & No Surat -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahap Panggilan (SP) *</label>
                <input
                  v-model="newSummons.stage"
                  type="text"
                  placeholder="Contoh: SP-1, SP-2, Panggilan 1..."
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block font-semibold text-slate-700 mb-1">No. Surat Resmi</label>
                <input
                  v-model="newSummons.letterNumber"
                  type="text"
                  placeholder="Nomor surat resmi..."
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                />
              </div>
            </div>

            <!-- Alasan Pemanggilan -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Alasan / Kasus Pelanggaran *</label>
              <input
                v-model="newSummons.caseTitle"
                type="text"
                placeholder="Contoh: Alpa 5 hari berturut-turut &amp; merokok di lingkungan sekolah"
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              />
            </div>

            <!-- Jadwal Menghadap -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Hari &amp; Tanggal *</label>
                <input
                  v-model="newSummons.meetingDate"
                  type="text"
                  placeholder="Senin, 28 Okt 2024"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Waktu / Pukul *</label>
                <input
                  v-model="newSummons.meetingTime"
                  type="text"
                  placeholder="08.30 WIB"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Ruangan Pertemuan</label>
                <select
                  v-model="newSummons.room"
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                >
                  <option>Ruang BK Gedung A</option>
                  <option>Ruang Sidang Tatib</option>
                  <option>Ruang Kepala Sekolah</option>
                </select>
              </div>
            </div>

            <!-- Wali Murid & No WA -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Orang Tua / Wali *</label>
                <input
                  v-model="newSummons.parentName"
                  type="text"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp Wali *</label>
                <input
                  v-model="newSummons.parentPhone"
                  type="text"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                />
              </div>
            </div>

            <!-- Guru BK Penanggung Jawab -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Guru BK Penanggung Jawab</label>
              <input
                v-model="newSummons.counselor"
                type="text"
                readonly
                class="w-full bg-slate-100 border border-slate-200 rounded-xl p-2.5 text-slate-600 font-medium cursor-not-allowed"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showNewModal = false"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md shadow-rose-600/20 cursor-pointer flex items-center gap-1.5"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Terbitkan &amp; Simpan</span>
              </button>
            </div>

          </form>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 2: PRATINJAU & CETAK SURAT DINAS RESMI (KOP SEKOLAH)   -->
      <!-- ============================================================ -->
      <div v-if="showLetterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 animate-in fade-in">
        <div class="bg-white rounded-xl max-w-2xl w-full border border-slate-200 shadow-lg overflow-hidden animate-in zoom-in-95 duration-200 max-h-[92vh] flex flex-col">
          
          <!-- Modal Top Toolbar -->
          <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 no-print">
            <div class="flex items-center gap-2">
              <span class="font-bold text-slate-900 text-xs sm:text-sm">Pratinjau Surat Panggilan Resmi (Format Dinas)</span>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="printLetter"
                class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Simpan PDF</span>
              </button>
              <button @click="showLetterModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Official Paper Letter Preview -->
          <div class="p-8 overflow-y-auto bg-white text-slate-900 font-serif text-xs leading-relaxed space-y-4 select-text">
            
            <!-- KOP SURAT RESMI -->
            <div class="text-center pb-3 border-b-2 border-slate-900 relative">
              <div class="flex items-center justify-center gap-4">
                <img src="/logo-smk.png" alt="Logo SMK" class="w-14 h-14 object-contain" />
                <div>
                  <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700">YAYASAN PEMBINA LEMBAGA PENDIDIKAN PGRI JEMBER</h4>
                  <h2 class="font-black text-base sm:text-lg text-slate-950 uppercase tracking-tight">SMK PGRI 05 JEMBER</h2>
                  <p class="text-[10px] text-slate-600 font-sans">
                    Jl. Krakatau No. 58, Kec. Kencong, Kab. Jember, Jawa Timur 68167 &bull; Telp: (0336) 321456
                  </p>
                  <p class="text-[10px] text-slate-500 font-sans">
                    Website: smkpgri05jember.sch.id &bull; Pos-el: bk@smkpgri05jember.sch.id
                  </p>
                </div>
              </div>
            </div>

            <!-- Nomor & Perihal -->
            <div class="flex justify-between items-start pt-2 font-sans text-xs">
              <table class="text-xs">
                <tr>
                  <td class="font-bold pr-2">Nomor</td>
                  <td>: {{ selectedLetter.letterNumber }}</td>
                </tr>
                <tr>
                  <td class="font-bold pr-2">Lampiran</td>
                  <td>: - (Satu Berkas Rekap Kedisiplinan)</td>
                </tr>
                <tr>
                  <td class="font-bold pr-2">Perihal</td>
                  <td class="font-bold underline">: Panggilan Orang Tua / Wali Murid ({{ selectedLetter.stage }})</td>
                </tr>
              </table>
              <div class="text-right text-xs">
                Jember, {{ selectedLetter.issueDate }}
              </div>
            </div>

            <!-- Kepada Yth -->
            <div class="font-sans text-xs pt-2">
              <p>Kepada Yth.</p>
              <p class="font-bold">Bapak / Ibu Orang Tua / Wali Murid dari {{ selectedLetter.studentName }}</p>
              <p>di Tempat</p>
            </div>

            <!-- Isi Surat -->
            <div class="space-y-2.5 font-sans text-xs text-justify pt-1">
              <p>Dengan hormat,</p>
              <p>
                Sehubungan dengan pembinaan kedisiplinan dan evaluasi perkembangan pembelajaran siswa di SMK PGRI 05 JEMBER, kami dari pihak Bimbingan &amp; Konseling (BK) bersama Tim Kesiswaan mengundang Bapak/Ibu untuk hadir di sekolah guna membicarakan tindak lanjut atas:
              </p>
              
              <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs font-sans space-y-1 my-2">
                <div><strong>Nama Siswa:</strong> {{ selectedLetter.studentName }}</div>
                <div><strong>NISN / Kelas:</strong> {{ selectedLetter.nisn }} / {{ selectedLetter.className }}</div>
                <div><strong>Alasan Panggilan:</strong> {{ selectedLetter.caseTitle }} (Akumulasi {{ selectedLetter.points }} Poin Pelanggaran)</div>
              </div>

              <p>Pertemuan konsultasi dan penandatanganan kesepakatan bersama akan dilaksanakan pada:</p>

              <table class="font-sans text-xs ml-4 my-2">
                <tr>
                  <td class="font-bold pr-4 py-0.5">Hari / Tanggal</td>
                  <td>: {{ selectedLetter.meetingDate }}</td>
                </tr>
                <tr>
                  <td class="font-bold pr-4 py-0.5">Waktu / Pukul</td>
                  <td>: {{ selectedLetter.meetingTime }}</td>
                </tr>
                <tr>
                  <td class="font-bold pr-4 py-0.5">Tempat</td>
                  <td>: {{ selectedLetter.room }} SMK PGRI 05 JEMBER</td>
                </tr>
                <tr>
                  <td class="font-bold pr-4 py-0.5">Menghadap</td>
                  <td>: {{ selectedLetter.counselor || 'Guru BK' }} &amp; Wali Kelas</td>
                </tr>
              </table>

              <p>
                Mengingat pentingnya koordinasi ini demi masa depan dan kelanjutan studi ananda, kehadiran Bapak/Ibu selaku orang tua/wali sangat kami harapkan tepat pada waktu yang ditentukan.
              </p>

              <p>Demikian surat panggilan ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
            </div>

            <!-- Tanda Tangan -->
            <div class="pt-6 font-sans text-xs grid grid-cols-2 text-center gap-4">
              <div>
                <p>Mengetahui,</p>
                <p class="font-bold">Kepala SMK PGRI 05 JEMBER</p>
                <div class="h-16 flex items-center justify-center text-slate-300 italic font-mono text-[10px]">
                  [ Tanda Tangan &amp; Stempel Resmi ]
                </div>
                <p class="font-bold underline">Drs. H. Saiful Bahri, M.Pd</p>
                <p class="text-[10px] text-slate-500">NIP. 19680215 199403 1 003</p>
              </div>

              <div>
                <p>Koordinator Bimbingan &amp; Konseling,</p>
                <p class="font-bold">Guru BK Penanggung Jawab</p>
                <div class="h-16 flex items-center justify-center text-slate-300 italic font-mono text-[10px]">
                  [ Tanda Tangan Digital ]
                </div>
                <p class="font-bold underline">{{ selectedLetter.counselor || 'Guru BK' }}</p>
                <p class="text-[10px] text-slate-500">Guru Pembimbing BK</p>
              </div>
            </div>

          </div>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 3: CATAT BERITA ACARA MEDIASI                          -->
      <!-- ============================================================ -->
      <div v-if="showMediationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 animate-in fade-in">
        <div class="bg-white rounded-xl max-w-lg w-full border border-slate-200 shadow-lg overflow-hidden animate-in zoom-in-95 duration-200">
          
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-black text-slate-900 text-base">Berita Acara Mediasi Kasus</h3>
              <p class="text-xs text-slate-500">Catat komitmen kehadiran wali murid &amp; perjanjian siswa</p>
            </div>
            <button @click="showMediationModal = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveMediationNote" class="p-5 space-y-4 text-xs">
            
            <div class="bg-blue-50/70 p-3 rounded-xl border border-blue-100">
              <div class="font-bold text-slate-900 text-xs">{{ activeMediationItem.studentName }} ({{ activeMediationItem.className }})</div>
              <div class="text-[11px] text-slate-500 mt-0.5">No Surat: {{ activeMediationItem.letterNumber }} &bull; {{ activeMediationItem.stage }}</div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Status Kehadiran &amp; Mediasi *</label>
              <select
                v-model="activeMediationItem.status"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              >
                <option value="Menunggu Kehadiran">Menunggu Kehadiran</option>
                <option value="Mediasi Selesai">Mediasi Selesai (Tandatangan Surat Perjanjian)</option>
                <option value="Mangkir / Panggil Ulang">Mangkir / Tidak Hadir (Panggil Ulang)</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Butir Kesepakatan / Catatan Mediasi Guru BK</label>
              <textarea
                v-model="mediationNote"
                rows="4"
                placeholder="Tuliskan hasil pembicaraan dengan orang tua, komitmen siswa, dan sanksi edukatif yang disepakati bersama..."
                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              ></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showMediationModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer"
              >
                Tutup
              </button>
              <button
                type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/20 cursor-pointer flex items-center gap-1.5"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Berita Acara</span>
              </button>
            </div>

          </form>

        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  students: {
    type: Array,
    default: () => []
  },
  dbSummons: {
    type: Array,
    default: () => []
  }
})

const activeCategory = ref('ALL')
const selectedClassFilter = ref('ALL')
const searchQuery = ref('')

const filterCategories = [
  { key: 'ALL', label: 'Semua Surat' },
  { key: 'WAITING', label: 'Menunggu Kehadiran' },
  { key: 'SP-1', label: 'SP-1 (Peringatan Awal)' },
  { key: 'SP-2', label: 'SP-2 (Peringatan Keras)' },
  { key: 'SP-3', label: 'SP-3 (Konferensi Kasus)' },
  { key: 'RESOLVED', label: 'Selesai Mediasi' }
]

// Summons Data Source
const summonsList = ref([])

const loadDbSummons = () => {
  if (props.dbSummons && props.dbSummons.length > 0) {
    summonsList.value = props.dbSummons.map(s => {
      const cls = s.student?.classroom 
        ? `${s.student.classroom.tingkat} ${s.student.classroom.jurusan} ${s.student.classroom.rombel}`
        : (s.student?.class_name || (s.student?.tingkat ? `${s.student.tingkat} ${s.student.jurusan} ${s.student.rombel}` : (s.class_name || '-')))
      const grade = s.student?.tingkat || (cls.startsWith('XII') ? 'XII' : (cls.startsWith('XI') ? 'XI' : 'X'))
      return {
        id: s.id,
        letterNumber: s.letter_number || '-',
        issueDate: s.created_at ? new Date(s.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Hari Ini',
        studentName: s.student?.name || s.student_name || '-',
        nisn: s.student?.nis || s.student?.nisn || s.nis || '-',
        className: cls,
        gradeLevel: grade,
        caseTitle: s.case_title,
        caseDescription: s.case_description || `Poin akumulasi ${s.points || 0} poin.`,
        points: s.points || 0,
        stage: s.stage || 'SP-1',
        meetingDate: s.meeting_date || '-',
        meetingTime: s.meeting_time || '-',
        room: s.room || 'Ruang BK',
        parentName: s.parent_name || 'Orang Tua / Wali',
        parentPhone: s.parent_phone || '-',
        status: s.status || 'Menunggu Kehadiran',
        counselor: s.counselor_name || 'Guru BK'
      }
    })
  } else {
    summonsList.value = []
  }
}

onMounted(() => {
  loadDbSummons()
})

watch(() => props.dbSummons, () => {
  loadDbSummons()
}, { deep: true })

// Problematic Students Pool for creating new summons
const problematicStudents = computed(() => {
  if (props.students && props.students.length > 0) {
    return props.students.map(s => {
      const cls = s.classroom 
        ? `${s.classroom.tingkat} ${s.classroom.jurusan} ${s.classroom.rombel}` 
        : (s.class_name || (s.tingkat ? `${s.tingkat} ${s.jurusan} ${s.rombel}` : '-'))
      return {
        id: s.id,
        name: s.name,
        nisn: s.nis || s.nisn || '',
        className: cls,
        points: s.total_violation_points || 0,
        parent: s.parent_name || ('Wali dari ' + s.name),
        phone: s.parent_phone || '-'
      }
    })
  }
  return []
})

const stats = computed(() => {
  const waiting = summonsList.value.filter(s => s.status === 'Menunggu Kehadiran').length
  const resolved = summonsList.value.filter(s => s.status === 'Mediasi Selesai').length
  const sp3 = summonsList.value.filter(s => s.stage === 'SP-3').length
  return { waiting, resolved, sp3 }
})

const filteredSummons = computed(() => {
  return summonsList.value.filter(item => {
    // Filter Category
    if (activeCategory.value === 'WAITING' && item.status !== 'Menunggu Kehadiran') return false
    if (activeCategory.value === 'RESOLVED' && item.status !== 'Mediasi Selesai') return false
    if (activeCategory.value === 'SP-1' && item.stage !== 'SP-1') return false
    if (activeCategory.value === 'SP-2' && item.stage !== 'SP-2') return false
    if (activeCategory.value === 'SP-3' && item.stage !== 'SP-3') return false

    // Filter Grade Level
    if (selectedClassFilter.value !== 'ALL' && item.gradeLevel !== selectedClassFilter.value) return false

    // Search query
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const matchName = item.studentName.toLowerCase().includes(q)
      const matchNisn = item.nisn.includes(q)
      const matchLetter = item.letterNumber.toLowerCase().includes(q)
      const matchParent = item.parentName.toLowerCase().includes(q)
      if (!matchName && !matchNisn && !matchLetter && !matchParent) return false
    }

    return true
  })
})

// Modals State
const showNewModal = ref(false)
const showLetterModal = ref(false)
const showMediationModal = ref(false)

const selectedLetter = ref(summonsList.value[0])
const activeMediationItem = ref(summonsList.value[0])
const mediationNote = ref('')

const newSummons = ref({
  studentId: '',
  studentName: '',
  nisn: '',
  className: '',
  gradeLevel: 'X',
  stage: 'SP-1',
  letterNumber: '',
  caseTitle: '',
  points: 0,
  meetingDate: '',
  meetingTime: '08.30 WIB',
  room: 'Ruang BK',
  parentName: '',
  parentPhone: '',
  counselor: 'Guru BK'
})

const openNewSummonsModal = () => {
  showNewModal.value = true
}

const onSelectStudentForSummons = () => {
  const selected = problematicStudents.find(s => s.id === Number(newSummons.value.studentId))
  if (selected) {
    newSummons.value.studentName = selected.name
    newSummons.value.nisn = selected.nisn
    newSummons.value.className = selected.className
    newSummons.value.gradeLevel = selected.className.startsWith('X ') ? 'X' : (selected.className.startsWith('XI ') ? 'XI' : 'XII')
    newSummons.value.points = selected.points
    newSummons.value.parentName = selected.parent
    newSummons.value.parentPhone = selected.phone
  }
}

const isSubmittingSummons = ref(false)

const submitNewSummons = () => {
  if (!newSummons.value.studentId) {
    alert('Silakan pilih siswa terlebih dahulu.')
    return
  }
  isSubmittingSummons.value = true

  router.post('/pelanggaran/panggilan', {
    student_id: newSummons.value.studentId,
    stage: newSummons.value.stage,
    letter_number: newSummons.value.letterNumber || undefined,
    case_title: newSummons.value.caseTitle || `Akumulasi ${newSummons.value.points} poin pelanggaran tata tertib sekolah`,
    points: newSummons.value.points,
    meeting_date: newSummons.value.meetingDate || 'Menyesuaikan Jadwal',
    meeting_time: newSummons.value.meetingTime || '08.30 WIB',
    room: newSummons.value.room || 'Ruang BK',
    parent_name: newSummons.value.parentName,
    parent_phone: newSummons.value.parentPhone,
    counselor: newSummons.value.counselor
  }, {
    preserveScroll: true,
    onSuccess: () => {
      isSubmittingSummons.value = false
      showNewModal.value = false
    },
    onError: () => {
      isSubmittingSummons.value = false
      alert('Gagal menerbitkan surat panggilan. Silakan periksa kembali formulir.')
    }
  })
}

const previewLetter = (item) => {
  selectedLetter.value = item
  showLetterModal.value = true
}

const openMediationModal = (item) => {
  activeMediationItem.value = item
  mediationNote.value = `Telah menghadap orang tua (${item.parentName}) dari siswa ${item.studentName}. Orang tua sepakat untuk meningkatkan pengawasan di rumah serta menyetujui pembinaan siswa.`
  showMediationModal.value = true
}

const isSavingMediation = ref(false)

const saveMediationNote = () => {
  if (!activeMediationItem.value) return
  isSavingMediation.value = true

  router.patch(`/pelanggaran/panggilan/${activeMediationItem.value.id}`, {
    status: 'Mediasi Selesai',
    mediation_notes: mediationNote.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      isSavingMediation.value = false
      showMediationModal.value = false
    },
    onError: () => {
      isSavingMediation.value = false
      alert('Gagal menyimpan berita acara mediasi.')
    }
  })
}

const deleteSummons = (item) => {
  if (!confirm(`Hapus data surat panggilan ${item.letterNumber}?`)) return

  router.delete(`/pelanggaran/panggilan/${item.id}`, {
    preserveScroll: true,
    onError: () => {
      alert('Gagal menghapus surat panggilan.')
    }
  })
}

const sendWhatsApp = (item) => {
  const text = encodeURIComponent(
    `Yth. Bapak/Ibu ${item.parentName} (Wali dari ${item.studentName} - ${item.className}), kami dari Tim Bimbingan & Konseling SMK PGRI 05 JEMBER menyampaikan Surat Panggilan Resmi (${item.stage}) No: ${item.letterNumber}. Mohon kehadirannya pada ${item.meetingDate} pukul ${item.meetingTime} di ${item.room}. Terima kasih.`
  )
  const phone = item.parentPhone.replace(/[^0-9]/g, '')
  window.open(`https://wa.me/${phone}?text=${text}`, '_blank')
}

const printLetter = () => {
  window.print()
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const nama = urlParams.get('nama')
  if (nama) {
    newSummons.value.studentName = nama
    newSummons.value.nisn = urlParams.get('nisn') || ''
    newSummons.value.className = urlParams.get('kelas') || 'X RPL 1'
    newSummons.value.gradeLevel = newSummons.value.className.startsWith('X ') ? 'X' : (newSummons.value.className.startsWith('XI ') ? 'XI' : 'XII')
    newSummons.value.caseTitle = urlParams.get('alasan') || 'Alpa (Tanpa Keterangan) Selama 3 Hari dalam 1 Minggu'
    newSummons.value.points = Number(urlParams.get('poin')) || 30
    newSummons.value.stage = urlParams.get('stage') || 'SP-1'
    newSummons.value.parentName = urlParams.get('wali') || `Bpk/Ibu ${nama}`
    newSummons.value.parentPhone = urlParams.get('phone') || '+62 812-3456-7890'
    newSummons.value.letterNumber = `421.5/0${82 + summonsList.value.length}/BK/SMK-PGRI5/X/2026`
    showNewModal.value = true
  }
})
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
