<template>
  <AppLayout active-tab="pelanggaran" show-share>
    <div class="max-w-5xl mx-auto space-y-4 sm:space-y-5">

      <!-- ============================================================ -->
      <!-- HEADER RINGKAS: JUDUL + TOMBOL AKSI                         -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl px-5 py-4 sm:px-6 sm:py-5 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <p class="text-[11px] font-semibold uppercase tracking-widest text-rose-600 mb-0.5">Kedisiplinan &amp; Penanganan Kasus BK</p>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight leading-snug">
              Catatan Pelanggaran Siswa
            </h1>
            <p class="text-xs text-slate-500 mt-0.5 hidden sm:block">
              Rekap sanksi tata tertib, akumulasi poin &amp; surat panggilan orang tua
            </p>
          </div>

          <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
            <button
              @click="openNewSummonsModal"
              class="inline-flex items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold px-3.5 py-2 rounded-lg text-xs transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span>Panggil Ortu (SP)</span>
            </button>
            <router-link
              to="/verifikasi"
              class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-xs transition cursor-pointer shadow-xs"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Catat Pelanggaran</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- CARD 2: MASTER TABBED CONTAINER (PELANGGARAN VS PANGGILAN)    -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Header Mode View Switcher Bar -->
        <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
          
          <!-- Master Tabs Switcher -->
          <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg border border-slate-200 self-start sm:self-auto">
            <button
              @click="activeMainTab = 'pelanggaran'"
              class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-md text-xs font-semibold transition cursor-pointer"
              :class="activeMainTab === 'pelanggaran' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Daftar Rekaman Pelanggaran</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black" :class="activeMainTab === 'pelanggaran' ? 'bg-blue-100 text-blue-700' : 'bg-slate-200 text-slate-600'">
                {{ violations.length }}
              </span>
            </button>

            <button
              @click="activeMainTab = 'panggilan'"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="activeMainTab === 'panggilan' ? 'bg-white text-rose-600 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span>Surat Pemanggilan Orang Tua</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black" :class="activeMainTab === 'panggilan' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600'">
                {{ summonsList.length }} SP
              </span>
            </button>
          </div>

          <!-- Search Input -->
          <div class="relative w-full sm:w-72">
            <input
              v-model="searchQuery"
              type="text"
              :placeholder="activeMainTab === 'pelanggaran' ? 'Cari siswa, kelas, sanksi...' : 'Cari no surat, nama, wali...'"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
            />
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
          </div>

        </div>

        <!-- ============================================================ -->
        <!-- TAB VIEW 1: TABEL CATATAN PELANGGARAN                        -->
        <!-- ============================================================ -->
        <div v-if="activeMainTab === 'pelanggaran'">
          <!-- Filter Chips (Kategori / Status) & Pengurutan Otomatis Poin -->
          <div class="px-5 sm:px-6 py-3 bg-slate-50/60 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 overflow-x-auto">
              <span class="text-slate-400 font-bold shrink-0 text-[11px] uppercase tracking-wider">Status:</span>
              <button
                v-for="st in statusFilters"
                :key="st"
                @click="selectedStatus = st"
                class="px-3 py-1 rounded-lg font-bold text-xs transition cursor-pointer shrink-0"
                :class="selectedStatus === st ? 'bg-blue-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
              >
                {{ st }}
              </button>
            </div>

  
          </div>

          <div class="p-5 sm:p-6">
            <div class="overflow-x-auto border border-slate-200/80 rounded-2xl shadow-2xs">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                  <tr>
                    <th class="py-3.5 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">TANGGAL &amp; WAKTU</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">SISWA TERLAPOR</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">JENIS PELANGGARAN</th>
                    <th 
                      @click="toggleSortByPoints"
                      class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider cursor-pointer hover:bg-slate-100 transition select-none"
                      title="Klik untuk mengubah urutan poin terbanyak / terendah"
                    >
                      <div class="inline-flex items-center justify-center gap-1">
                        <span>POIN SANKSI</span>
                        <svg v-if="sortBy === 'points-desc'" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg v-else-if="sortBy === 'points-asc'" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                        </svg>
                      </div>
                    </th>
                    <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">STATUS KASUS</th>
                    <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                  <tr v-for="(item, idx) in filteredViolations" :key="item.id" class="hover:bg-slate-50/80 transition">
                    <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                    <td class="py-3.5 px-4">
                      <div class="font-bold text-slate-800">{{ item.date }}</div>
                      <div class="text-[11px] text-slate-400">{{ item.time }}</div>
                    </td>
                    <td class="py-3.5 px-4">
                      <div class="font-bold text-slate-900 text-xs">{{ item.studentName }}</div>
                      <div class="text-[11px] text-slate-500 mt-0.5">
                        NISN: {{ item.nisn }} &bull; {{ item.className }}
                      </div>
                    </td>
                    <td class="py-3.5 px-4">
                      <div class="font-semibold text-slate-800">{{ item.category }}</div>
                      <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ item.note }}</div>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                      <span 
                        class="px-2.5 py-1 rounded-full text-xs font-black inline-block shadow-2xs"
                        :class="item.points >= 25 ? 'bg-rose-100 text-rose-700 border border-rose-200' : (item.points >= 15 ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-blue-100 text-blue-700 border border-blue-200')"
                      >
                        +{{ item.points }} Poin
                      </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                      <span 
                        v-if="item.status === 'Panggilan Ortu'" 
                        class="px-3 py-1 bg-rose-50 text-rose-700 font-bold rounded-full border border-rose-200 text-[11px]"
                      >
                        Panggilan Ortu
                      </span>
                      <span 
                        v-else-if="item.status === 'Dalam Pembinaan'" 
                        class="px-3 py-1 bg-amber-50 text-amber-700 font-bold rounded-full border border-amber-200 text-[11px]"
                      >
                        Dalam Pembinaan
                      </span>
                      <span 
                        v-else 
                        class="px-3 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full border border-emerald-200 text-[11px]"
                      >
                        Selesai Dibina
                      </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                      <div class="flex items-center justify-center gap-1.5">
                        <router-link
                          :to="'/pelanggaran/detail?id=' + item.id"
                          class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-lg border border-blue-200 transition cursor-pointer"
                          title="Lihat Detail Pelanggaran"
                        >
                          <span>Detail</span>
                        </router-link>

                        <!-- Tombol Panggil Ortu jika kasus berat / Panggilan Ortu -->
                        <button
                          v-if="item.status === 'Panggilan Ortu' || item.points >= 25"
                          @click="prepareSummonsForStudent(item)"
                          class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-lg border border-rose-200 transition cursor-pointer"
                          title="Terbitkan Surat Panggilan Orang Tua"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                          </svg>
                          <span>Panggil Ortu</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredViolations.length === 0">
                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                      Tidak ada catatan pelanggaran yang sesuai dengan pencarian.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Bottom Summary -->
            <div class="flex justify-between items-center gap-3 pt-4 border-t border-slate-100 mt-4">
              <p class="text-xs text-slate-500 font-medium">
                Menampilkan <span class="font-bold text-slate-800">{{ filteredViolations.length }}</span> dari {{ violations.length }} catatan pelanggaran
              </p>
            </div>
          </div>
        </div>

        <!-- ============================================================ -->
        <!-- TAB VIEW 2: TABEL SURAT PEMANGGILAN ORANG TUA (SP)           -->
        <!-- ============================================================ -->
        <div v-else>
          <!-- Filter Chips SP Stage -->
          <div class="px-5 sm:px-6 py-3 bg-slate-50/60 border-b border-slate-100 flex items-center gap-2 overflow-x-auto text-xs">
            <span class="text-slate-400 font-bold shrink-0 text-[11px] uppercase tracking-wider">Tahap SP:</span>
            <button
              v-for="fl in ['ALL', 'SP-1', 'SP-2', 'SP-3', 'WAITING', 'RESOLVED']"
              :key="fl"
              @click="summonsFilter = fl"
              class="px-3 py-1 rounded-lg font-bold text-xs transition cursor-pointer shrink-0"
              :class="summonsFilter === fl ? 'bg-rose-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
            >
              {{ fl === 'ALL' ? 'Semua Surat' : (fl === 'WAITING' ? 'Menunggu Kehadiran' : (fl === 'RESOLVED' ? 'Mediasi Selesai' : fl)) }}
            </button>
          </div>

          <div class="p-5 sm:p-6">
            <div class="overflow-x-auto border border-slate-200/80 rounded-2xl shadow-2xs">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                  <tr>
                    <th class="py-3.5 px-4 text-center w-12 text-[11px] uppercase tracking-wider">NO</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">NO SURAT DINAS</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">SISWA &amp; ROMBEL</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">KASUS &amp; ALASAN</th>
                    <th class="py-3.5 px-3 text-center text-[11px] uppercase tracking-wider">TAHAP SP</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">JADWAL MENGHADAP</th>
                    <th class="py-3.5 px-4 text-[11px] uppercase tracking-wider">WALI MURID &amp; WA</th>
                    <th class="py-3.5 px-3 text-center text-[11px] uppercase tracking-wider">STATUS</th>
                    <th class="py-3.5 px-4 text-center text-[11px] uppercase tracking-wider">AKSI</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                  <tr v-for="(item, idx) in filteredSummons" :key="item.id" class="hover:bg-slate-50/80 transition">
                    <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                    <td class="py-3.5 px-4 font-mono font-medium text-slate-700">
                      <div class="font-bold text-slate-900">{{ item.letterNumber }}</div>
                      <div class="text-[10px] text-slate-400">Tgl: {{ item.issueDate }}</div>
                    </td>
                    <td class="py-3.5 px-4">
                      <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ item.studentName }}</div>
                      <div class="text-[11px] text-slate-500 font-mono">
                        NISN: {{ item.nisn }} &bull; <span class="font-bold text-blue-700">{{ item.className }}</span>
                      </div>
                    </td>
                    <td class="py-3.5 px-4 max-w-xs">
                      <div class="font-bold text-slate-800 truncate">{{ item.caseTitle }}</div>
                      <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                        <span class="px-1.5 py-0.2 rounded font-black text-[10px] bg-rose-100 text-rose-700">
                          +{{ item.points }} Poin
                        </span>
                        <span class="truncate">{{ item.caseDescription }}</span>
                      </div>
                    </td>
                    <td class="py-3.5 px-3 text-center">
                      <span
                        class="px-2.5 py-1 rounded-full text-[10px] font-black border"
                        :class="{
                          'bg-amber-50 text-amber-700 border-amber-200': item.stage === 'SP-1',
                          'bg-orange-50 text-orange-700 border-orange-200': item.stage === 'SP-2',
                          'bg-rose-50 text-rose-700 border-rose-300': item.stage === 'SP-3'
                        }"
                      >
                        {{ item.stage }}
                      </span>
                    </td>
                    <td class="py-3.5 px-4">
                      <div class="font-bold text-slate-800">{{ item.meetingDate }}</div>
                      <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ item.meetingTime }} ({{ item.room }})</span>
                      </div>
                    </td>
                    <td class="py-3.5 px-4">
                      <div class="font-semibold text-slate-800">{{ item.parentName }}</div>
                      <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[11px] font-mono text-slate-500">{{ item.parentPhone }}</span>
                        <button
                          @click="sendWhatsAppToParent(item)"
                          class="px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-md font-bold text-[10px] border border-emerald-200 cursor-pointer flex items-center gap-0.5"
                          title="Kirim Undangan WhatsApp"
                        >
                          <span>WA</span>
                        </button>
                      </div>
                    </td>
                    <td class="py-3.5 px-3 text-center">
                      <span
                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                        :class="{
                          'bg-blue-50 text-blue-700 border-blue-200': item.status === 'Menunggu Kehadiran',
                          'bg-emerald-50 text-emerald-700 border-emerald-200': item.status === 'Mediasi Selesai',
                          'bg-rose-50 text-rose-700 border-rose-200': item.status === 'Mangkir / Panggil Ulang'
                        }"
                      >
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                      <div class="flex items-center justify-center gap-1.5">
                        <button
                          @click="previewSummonsLetter(item)"
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
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredSummons.length === 0">
                    <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                      Tidak ada surat pemanggilan orang tua yang cocok dengan filter.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex justify-between items-center pt-5 border-t border-slate-100 mt-5">
              <p class="text-xs text-slate-500 font-medium">
                Total <span class="font-bold text-slate-800">{{ filteredSummons.length }}</span> surat pemanggilan orang tua terbit
              </p>
              <button 
                @click="openNewSummonsModal"
                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition cursor-pointer flex items-center gap-1.5 shadow-xs"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Terbitkan Surat SP Baru</span>
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- ============================================================ -->
      <!-- MODAL 1: FORM TERBITKAN SURAT PANGGILAN BARU                 -->
      <!-- ============================================================ -->
      <div v-if="showNewSummonsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
        <div class="bg-white rounded-3xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200 max-h-[90vh] flex flex-col">
          
          <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
              <h3 class="font-black text-slate-900 text-base">Terbitkan Surat Panggilan Orang Tua</h3>
              <p class="text-xs text-slate-500">Penerbitan surat dinas resmi BK untuk siswa bermasalah</p>
            </div>
            <button @click="showNewSummonsModal = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl cursor-pointer">
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
                <option v-for="s in candidateStudents" :key="s.id" :value="s.id">
                  {{ s.name }} ({{ s.className }}) &bull; Akumulasi: {{ s.points }} Poin
                </option>
              </select>
            </div>

            <!-- Tahap Surat & No Surat -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Tahap Panggilan (SP) *</label>
                <select
                  v-model="newSummons.stage"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                >
                  <option value="SP-1">SP-1 (Klarifikasi &amp; Peringatan Awal)</option>
                  <option value="SP-2">SP-2 (Peringatan Tertulis Keras)</option>
                  <option value="SP-3">SP-3 (Konferensi Kasus Pleno / Skorsing)</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1">No. Surat Resmi</label>
                <input
                  v-model="newSummons.letterNumber"
                  type="text"
                  readonly
                  class="w-full bg-slate-100 border border-slate-200 rounded-xl p-2.5 font-mono text-slate-600 cursor-not-allowed"
                />
              </div>
            </div>

            <!-- Alasan Pemanggilan -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Alasan / Kasus Pelanggaran *</label>
              <input
                v-model="newSummons.caseTitle"
                type="text"
                placeholder="Contoh: Alpa 5 hari berturut-turut &amp; merokok di area sekolah"
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
                @click="showNewSummonsModal = false"
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
      <div v-if="showLetterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
        <div class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200 max-h-[92vh] flex flex-col">
          
          <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 no-print">
            <div class="flex items-center gap-2">
              <span class="font-bold text-slate-900 text-xs sm:text-sm">Pratinjau Surat Panggilan Resmi Dinas</span>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="printLetter"
                class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / PDF</span>
              </button>
              <button @click="showLetterModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

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
                  <td>: - (Satu Berkas Rekapitulasi Pelanggaran)</td>
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

            <div class="font-sans text-xs pt-2">
              <p>Kepada Yth.</p>
              <p class="font-bold">Bapak / Ibu Orang Tua / Wali Murid dari {{ selectedLetter.studentName }}</p>
              <p>di Tempat</p>
            </div>

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
                  <td>: Dra. Hj. Nurul Hidayati, M.Pd (Koord. Guru BK) &amp; Wali Kelas</td>
                </tr>
              </table>

              <p>
                Mengingat pentingnya koordinasi ini demi masa depan dan kelanjutan studi ananda, kehadiran Bapak/Ibu selaku orang tua/wali sangat kami harapkan tepat pada waktu yang ditentukan.
              </p>

              <p>Demikian surat panggilan ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
            </div>

            <div class="pt-6 font-sans text-xs grid grid-cols-2 text-center gap-4">
              <div>
                <p>Mengetahui,</p>
                <p class="font-bold">Kepala SMK PGRI 05 JEMBER</p>
                <div class="h-16 flex items-center justify-center text-slate-300 italic font-mono text-[10px]">
                  [ Tanda Tangan &amp; Stempel ]
                </div>
                <p class="font-bold underline">Drs. H. Saiful Bahri, M.M</p>
                <p class="text-[10px] text-slate-500">NIP. 19680815 199403 1 008</p>
              </div>

              <div>
                <p>Koordinator Bimbingan &amp; Konseling,</p>
                <p class="font-bold">Guru BK Penanggung Jawab</p>
                <div class="h-16 flex items-center justify-center text-slate-300 italic font-mono text-[10px]">
                  [ Tanda Tangan Digital ]
                </div>
                <p class="font-bold underline">Dra. Hj. Nurul Hidayati, M.Pd</p>
                <p class="text-[10px] text-slate-500">NIP. 19710420 199802 2 003</p>
              </div>
            </div>

          </div>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 3: CATAT BERITA ACARA MEDIASI                          -->
      <!-- ============================================================ -->
      <div v-if="showMediationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
        <div class="bg-white rounded-3xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
          
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

      <!-- ============================================================ -->
      <!-- MODAL 4: PREVIEW FOTO BUKTI PELANGGARAN                      -->
      <!-- ============================================================ -->
      <div 
        v-if="previewPhotoItem" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs animate-in fade-in" 
        @click.self="previewPhotoItem = null"
      >
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl text-slate-100 flex flex-col">
          <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-950">
            <div>
              <p class="text-xs sm:text-sm font-bold text-white">{{ previewPhotoItem.studentName }} ({{ previewPhotoItem.className }})</p>
              <p class="text-[10px] text-slate-400">{{ previewPhotoItem.category }} &bull; {{ previewPhotoItem.date }} ({{ previewPhotoItem.time }})</p>
            </div>
            <button @click="previewPhotoItem = null" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="bg-black p-3 flex items-center justify-center max-h-[70vh] overflow-hidden">
            <img :src="previewPhotoItem.photoUrl" alt="Bukti Foto Pelanggaran" class="max-h-[65vh] w-auto max-w-full object-contain mx-auto rounded-lg">
          </div>
          <div class="p-3.5 bg-slate-950 border-t border-slate-800 flex items-center justify-between text-xs">
            <span class="text-[11px] text-slate-400">Bukti Foto Resmi Tersimpan di Sistem BK</span>
            <router-link :to="'/pelanggaran/detail?id=' + previewPhotoItem.id" class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition">
              Buka Detail Kasus Lengkap &rarr;
            </router-link>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const props = defineProps({
  dbViolations: {
    type: Array,
    default: () => []
  },
  dbStudents: {
    type: Array,
    default: () => []
  },
  initialTab: {
    type: String,
    default: 'daftar'
  }
})

// Active Mode: 'pelanggaran' | 'panggilan'
const activeMainTab = ref('pelanggaran')
const previewPhotoItem = ref(null)
const sortBy = ref('points-desc') // 'points-desc' (Poin Terbanyak Auto) | 'date-desc' (Tanggal Terbaru) | 'points-asc'

const toggleSortByPoints = () => {
  if (sortBy.value === 'points-desc') {
    sortBy.value = 'points-asc'
  } else {
    sortBy.value = 'points-desc'
  }
}

const openPhotoPreview = (item) => {
  previewPhotoItem.value = item
}

onMounted(() => {
  if (route.query.tab === 'panggilan' || props.initialTab === 'panggilan') {
    activeMainTab.value = 'panggilan'
  }
  if (props.dbViolations && props.dbViolations.length > 0) {
    // Jika ada data DB, HANYA tampilkan data DB (tidak merge dengan dummy)
    violations.value = props.dbViolations.map((v) => ({
      id: v.id,
      date: v.date || new Date().toLocaleDateString('id-ID'),
      time: v.time ? `${v.time} WIB` : '07:30 WIB',
      studentName: v.student_name,
      nisn: v.student?.nisn || '-',
      className: v.student_class || '-',
      category: v.category,
      note: v.notes || v.violation_name,
      photoUrl: v.photo_url || null,
      points: Number(v.points) || 0,
      totalStudentPoints: Number(v.student?.total_violation_points) || Number(v.points) || 0,
      status: Number(v.points) >= 25 ? 'Panggilan Ortu' : (Number(v.points) >= 10 ? 'Dalam Pembinaan' : 'Selesai Dibina')
    }))
  }
})

const searchQuery = ref('')
const selectedStatus = ref('Semua')
const summonsFilter = ref('ALL')

const statusFilters = ['Semua', 'Dalam Pembinaan', 'Panggilan Ortu', 'Selesai Dibina']

// 1. VIOLATIONS DATA
const violations = ref([
  {
    id: 6,
    date: '10 Okt 2024',
    time: '10:30 WIB',
    studentName: 'Zulfikar Ali Akbar',
    nisn: '22231077',
    className: 'XI TSM 1',
    category: 'Berkelahi di Area Parkir Luar',
    note: 'Perselisihan antarsiswa di luar gerbang sekolah.',
    points: 30,
    totalStudentPoints: 85,
    status: 'Panggilan Ortu'
  },
  {
    id: 4,
    date: '17 Okt 2024',
    time: '13:00 WIB',
    studentName: 'Bagus Setiawan',
    nisn: '22231044',
    className: 'X RPL 1',
    category: 'Merokok di Lingkungan Sekolah',
    note: 'Kedapatan merokok di belakang kantin sekolah.',
    points: 25,
    totalStudentPoints: 35,
    status: 'Panggilan Ortu'
  },
  {
    id: 5,
    date: '14 Okt 2024',
    time: '08:00 WIB',
    studentName: 'Fajar Nugroho',
    nisn: '22231018',
    className: 'XII TKJ 1',
    category: 'Membolos / Tidak Masuk Tanpa Izin',
    note: 'Tidak hadir tanpa surat izin selama 3 hari berturut-turut.',
    points: 15,
    totalStudentPoints: 50,
    status: 'Panggilan Ortu'
  },
  {
    id: 3,
    date: '19 Okt 2024',
    time: '11:15 WIB',
    studentName: 'Rania Zahra Putri',
    nisn: '22231105',
    className: 'X DKV 1',
    category: 'Membawa HP saat Jam Pelajaran',
    note: 'Bermain game di kelas saat jam Bahasa Indonesia.',
    points: 10,
    totalStudentPoints: 10,
    status: 'Dalam Pembinaan'
  },
  {
    id: 1,
    date: '24 Okt 2024',
    time: '07:24 WIB',
    studentName: 'Dimas Arya Pratama',
    nisn: '22231063',
    className: 'X RPL 1',
    category: 'Terlambat Masuk Gerbang',
    note: 'Bangun kesiangan, tiba di sekolah 07:24 WIB.',
    points: 5,
    totalStudentPoints: 5,
    status: 'Selesai Dibina'
  },
  {
    id: 2,
    date: '21 Okt 2024',
    time: '09:45 WIB',
    studentName: 'Andi Saputra',
    nisn: '22231092',
    className: 'XI TSM 1',
    category: 'Seragam Tidak Rapi / Baju Dikeluarkan',
    note: 'Teguran lisan oleh petugas piket.',
    points: 5,
    totalStudentPoints: 5,
    status: 'Selesai Dibina'
  },
  {
    id: 7,
    date: '08 Okt 2024',
    time: '07:35 WIB',
    studentName: 'Cantika Dewi Lestari',
    nisn: '22231088',
    className: 'X RPL 1',
    category: 'Terlambat Masuk Gerbang',
    note: 'Terlambat 15 menit, diarahkan membersihkan koridor.',
    points: 5,
    totalStudentPoints: 5,
    status: 'Selesai Dibina'
  }
])

const filteredViolations = computed(() => {
  const result = violations.value.filter(item => {
    const matchesStatus = selectedStatus.value === 'Semua' || item.status === selectedStatus.value
    if (!matchesStatus) return false

    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const matchName = item.studentName.toLowerCase().includes(q)
      const matchNisn = item.nisn.includes(q)
      const matchClass = item.className.toLowerCase().includes(q)
      const matchCat = item.category.toLowerCase().includes(q)
      if (!matchName && !matchNisn && !matchClass && !matchCat) return false
    }
    return true
  })

  // Pengurutan otomatis: Poin pelanggaran paling banyak diletakkan di atas sendiri
  return [...result].sort((a, b) => {
    if (sortBy.value === 'points-desc') {
      const pointDiff = (Number(b.points) || 0) - (Number(a.points) || 0)
      if (pointDiff !== 0) return pointDiff
      const totalDiff = (Number(b.totalStudentPoints) || 0) - (Number(a.totalStudentPoints) || 0)
      if (totalDiff !== 0) return totalDiff
      return (Number(b.id) || 0) - (Number(a.id) || 0)
    } else if (sortBy.value === 'points-asc') {
      const pointDiff = (Number(a.points) || 0) - (Number(b.points) || 0)
      if (pointDiff !== 0) return pointDiff
      return (Number(b.id) || 0) - (Number(a.id) || 0)
    } else {
      return (Number(b.id) || 0) - (Number(a.id) || 0)
    }
  })
})

// 2. PARENT SUMMONS (SP) DATA
const summonsList = ref([
  {
    id: 3,
    letterNumber: '421.5/074/BK/SMK-PGRI5/X/2024',
    issueDate: '18 Okt 2024',
    studentName: 'Zulfikar Ali Akbar',
    nisn: '22231077',
    className: 'XI TSM 1',
    caseTitle: 'Perkelahian Antar Siswa & Merusak Meja Ruang Kelas',
    caseDescription: 'Akumulasi 85 poin, diperlukan sidang pleno bersama orang tua.',
    points: 85,
    stage: 'SP-3',
    meetingDate: 'Senin, 21 Okt 2024',
    meetingTime: '10.00 WIB',
    room: 'Ruang Sidang Tatib',
    parentName: 'Bpk. H. Akbar Maulana',
    parentPhone: '+62 813-7721-0099',
    status: 'Mediasi Selesai',
    counselor: 'Bpk. Agus Wicaksono, S.Pd'
  },
  {
    id: 2,
    letterNumber: '421.5/079/BK/SMK-PGRI5/X/2024',
    issueDate: '21 Okt 2024',
    studentName: 'Fajar Nugroho',
    nisn: '22231018',
    className: 'XII TKJ 1',
    caseTitle: 'Membolos 4 Hari Berturut-turut Tanpa Surat',
    caseDescription: 'Tidak ada kabar dari wali murid, surat peringatan keras diterbitkan.',
    points: 50,
    stage: 'SP-2',
    meetingDate: 'Jumat, 25 Okt 2024',
    meetingTime: '08.30 WIB',
    room: 'Ruang BK Gedung A',
    parentName: 'Bpk. Joko Nugroho',
    parentPhone: '+62 852-1100-3322',
    status: 'Menunggu Kehadiran',
    counselor: 'Bpk. Dwi Prasetyo, M.Pd'
  },
  {
    id: 1,
    letterNumber: '421.5/081/BK/SMK-PGRI5/X/2024',
    issueDate: '23 Okt 2024',
    studentName: 'Bagus Setiawan',
    nisn: '22231044',
    className: 'X RPL 1',
    caseTitle: 'Merokok di Lingkungan Sekolah & Membawa Korek',
    caseDescription: 'Akumulasi sanksi mencapai 35 poin, membutuhkan mediasi orang tua.',
    points: 35,
    stage: 'SP-1',
    meetingDate: 'Kamis, 24 Okt 2024',
    meetingTime: '09.00 WIB',
    room: 'Ruang BK Gedung A',
    parentName: 'Ibu Siti Khotimah',
    parentPhone: '+62 821-9923-4100',
    status: 'Menunggu Kehadiran',
    counselor: 'Dra. Hj. Nurul Hidayati, M.Pd'
  }
])

const candidateStudents = computed(() => {
  if (props.dbStudents && props.dbStudents.length > 0) {
    return [...props.dbStudents]
      .sort((a, b) => (Number(b.total_violation_points) || 0) - (Number(a.total_violation_points) || 0))
      .map(s => ({
        id: s.id,
        name: s.name,
        nisn: s.nisn,
        className: s.class_name,
        points: Number(s.total_violation_points) || 0,
        parent: s.parent_name || `Bpk/Ibu Wali ${s.name.split(' ')[0]}`,
        phone: s.parent_phone || '+62 812-3456-7890'
      }))
  }

  return [
    { id: 103, name: 'Zulfikar Ali Akbar', nisn: '22231077', className: 'XI TSM 1', points: 85, parent: 'Bpk. H. Akbar Maulana', phone: '+62 813-7721-0099' },
    { id: 102, name: 'Fajar Nugroho', nisn: '22231018', className: 'XII TKJ 1', points: 50, parent: 'Bpk. Joko Nugroho', phone: '+62 852-1100-3322' },
    { id: 104, name: 'Danendra Putra', nisn: '22231093', className: 'X RPL 2', points: 40, parent: 'Bpk. Danendra Sr', phone: '+62 812-3301-4491' },
    { id: 101, name: 'Bagus Setiawan', nisn: '22231044', className: 'X RPL 1', points: 35, parent: 'Ibu Siti Khotimah', phone: '+62 821-9923-4100' }
  ]
})

const filteredSummons = computed(() => {
  const result = summonsList.value.filter(item => {
    if (summonsFilter.value === 'WAITING' && item.status !== 'Menunggu Kehadiran') return false
    if (summonsFilter.value === 'RESOLVED' && item.status !== 'Mediasi Selesai') return false
    if (summonsFilter.value === 'SP-1' && item.stage !== 'SP-1') return false
    if (summonsFilter.value === 'SP-2' && item.stage !== 'SP-2') return false
    if (summonsFilter.value === 'SP-3' && item.stage !== 'SP-3') return false

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

  // Urutkan akumulasi poin sanksi tertinggi di atas
  return [...result].sort((a, b) => (Number(b.points) || 0) - (Number(a.points) || 0))
})

// Modals State
const showNewSummonsModal = ref(false)
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
  stage: 'SP-1',
  letterNumber: `421.5/0${82 + summonsList.value.length}/BK/SMK-PGRI5/X/2024`,
  caseTitle: '',
  points: 35,
  meetingDate: 'Senin, 28 Okt 2024',
  meetingTime: '08.30 WIB',
  room: 'Ruang BK Gedung A',
  parentName: '',
  parentPhone: '',
  counselor: 'Dra. Hj. Nurul Hidayati, M.Pd'
})

const openNewSummonsModal = () => {
  newSummons.value.letterNumber = `421.5/0${82 + summonsList.value.length}/BK/SMK-PGRI5/X/2024`
  showNewSummonsModal.value = true
}

const prepareSummonsForStudent = (violation) => {
  newSummons.value.studentName = violation.studentName
  newSummons.value.nisn = violation.nisn
  newSummons.value.className = violation.className
  newSummons.value.caseTitle = violation.category + ' - ' + violation.note
  newSummons.value.points = violation.points
  newSummons.value.parentName = 'Bpk/Ibu Wali ' + violation.studentName.split(' ')[0]
  newSummons.value.parentPhone = '+62 812-3456-7890'
  newSummons.value.stage = violation.points >= 50 ? 'SP-2' : 'SP-1'
  newSummons.value.letterNumber = `421.5/0${82 + summonsList.value.length}/BK/SMK-PGRI5/X/2024`
  showNewSummonsModal.value = true
}

const onSelectStudentForSummons = () => {
  const selected = candidateStudents.value.find(s => s.id === Number(newSummons.value.studentId))
  if (selected) {
    newSummons.value.studentName = selected.name
    newSummons.value.nisn = selected.nisn
    newSummons.value.className = selected.className
    newSummons.value.points = selected.points
    newSummons.value.parentName = selected.parent
    newSummons.value.parentPhone = selected.phone
  }
}

const submitNewSummons = () => {
  summonsList.value.unshift({
    id: Date.now(),
    letterNumber: newSummons.value.letterNumber,
    issueDate: '24 Okt 2024',
    studentName: newSummons.value.studentName,
    nisn: newSummons.value.nisn,
    className: newSummons.value.className,
    caseTitle: newSummons.value.caseTitle,
    caseDescription: `Akumulasi ${newSummons.value.points} poin pelanggaran tata tertib sekolah.`,
    points: newSummons.value.points,
    stage: newSummons.value.stage,
    meetingDate: newSummons.value.meetingDate,
    meetingTime: newSummons.value.meetingTime,
    room: newSummons.value.room,
    parentName: newSummons.value.parentName,
    parentPhone: newSummons.value.parentPhone,
    status: 'Menunggu Kehadiran',
    counselor: newSummons.value.counselor
  })

  showNewSummonsModal.value = false
  activeMainTab.value = 'panggilan'
  alert(`Surat Panggilan ${newSummons.value.stage} untuk siswa ${newSummons.value.studentName} berhasil diterbitkan!`)
}

const previewSummonsLetter = (item) => {
  selectedLetter.value = item
  showLetterModal.value = true
}

const openMediationModal = (item) => {
  activeMediationItem.value = item
  mediationNote.value = `Telah hadir orang tua (${item.parentName}) dari siswa ${item.studentName}. Orang tua menyatakan komitmen membina anak di rumah dan menandatangani surat perjanjian disiplin.`
  showMediationModal.value = true
}

const saveMediationNote = () => {
  showMediationModal.value = false
  alert(`Berita acara mediasi surat ${activeMediationItem.value.letterNumber} berhasil disimpan!`)
}

const sendWhatsAppToParent = (item) => {
  const text = encodeURIComponent(
    `Yth. Bapak/Ibu ${item.parentName} (Wali dari ${item.studentName} - ${item.className}), kami dari Tim Bimbingan & Konseling SMK PGRI 05 JEMBER menyampaikan Surat Panggilan Resmi (${item.stage}) No: ${item.letterNumber}. Mohon kehadirannya pada ${item.meetingDate} pukul ${item.meetingTime} di ${item.room}. Terima kasih.`
  )
  const phone = item.parentPhone.replace(/[^0-9]/g, '')
  window.open(`https://wa.me/${phone}?text=${text}`, '_blank')
}

const printViolations = () => {
  window.print()
}

const printLetter = () => {
  window.print()
}
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
