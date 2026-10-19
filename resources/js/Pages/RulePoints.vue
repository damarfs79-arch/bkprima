<template>
  <AppLayout active-tab="panduan" show-back>
    <div class="max-w-4xl mx-auto space-y-6">
      
      <!-- Top Subheader -->
      <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-blue-600 text-xs font-bold uppercase tracking-wider mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Bantuan & Panduan
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Tata Tertib & Poin Kedisiplinan</h1>
          <p class="text-xs text-gray-500 mt-1 max-w-xl leading-relaxed">
            Daftar standar akumulasi poin pelanggaran dan pembinaan kedisiplinan siswa SMK PGRI 05 JEMBER. Gunakan pedoman ini untuk menetapkan sanksi edukatif.
          </p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
          <span class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-100 whitespace-nowrap">
            10 Kategori Sanksi
          </span>
        </div>
      </div>

      <!-- Main Content Grid (Responsive: 1 col on mobile, 2 col on large desktop) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Table Column (2 Cols on lg) -->
        <div class="lg:col-span-2 space-y-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
              <h2 class="font-bold text-gray-900 text-sm sm:text-base flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Tabel Akumulasi Poin Sanksi
              </h2>
              <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">10 Pelanggaran</span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-blue-600 text-white font-bold">
                  <tr>
                    <th class="py-3 px-4 text-center w-12">No</th>
                    <th class="py-3 px-4">Pelanggaran</th>
                    <th class="py-3 px-3 text-center">Poin</th>
                    <th class="py-3 px-4">Tindakan / Sanksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="item in violations" :key="item.no" class="hover:bg-slate-50 transition">
                    <td class="py-3 px-4 text-center font-bold text-gray-400">{{ item.no }}</td>
                    <td class="py-3 px-4 font-semibold text-gray-900 whitespace-normal min-w-[200px]">{{ item.name }}</td>
                    <td class="py-3 px-3 text-center">
                      <span class="inline-block px-2 py-0.5 rounded-md font-extrabold text-xs" :class="getPoinColor(item.poin)">
                        +{{ item.poin }}
                      </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600 font-medium whitespace-normal">{{ item.tindakan }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Tingkatan Sanksi Sidebar (1 Col on lg) -->
        <div class="space-y-4">
          <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <h3 class="font-bold text-gray-900 text-sm mb-1 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              Tingkatan Sanksi & Akumulasi
            </h3>
            <p class="text-[11px] text-gray-400 mb-4">Skala tindakan penanganan berdasarkan akumulasi poin siswa</p>

            <div class="space-y-3">
              <div v-for="tier in tiers" :key="tier.range" class="p-3 rounded-xl border transition flex items-start gap-3" :class="tier.bgClass">
                <span class="px-2 py-1 rounded-md text-[11px] font-black tracking-tight whitespace-nowrap shadow-2xs" :class="tier.badgeClass">
                  {{ tier.range }}
                </span>
                <p class="text-xs font-semibold text-gray-800 leading-snug">{{ tier.desc }}</p>
              </div>
            </div>

            <!-- Quick Action: Input Pelanggaran -->
            <div class="mt-6 pt-5 border-t border-gray-100">
              <router-link to="/verifikasi" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Pelanggaran Baru
              </router-link>
            </div>
          </div>
        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'

const violations = [
  { no: 1, name: 'Terlambat masuk sekolah', poin: 5, tindakan: 'Teguran lisan' },
  { no: 2, name: 'Tidak memakai seragam lengkap', poin: 10, tindakan: 'Teguran tertulis' },
  { no: 3, name: 'Membolos/tidak hadir tanpa keterangan', poin: 15, tindakan: 'Panggilan ortu' },
  { no: 4, name: 'Merokok di lingkungan sekolah', poin: 25, tindakan: 'Skorsing 3 hari' },
  { no: 5, name: 'Berkelahi di sekolah', poin: 30, tindakan: 'Skorsing 5 hari' },
  { no: 6, name: 'Membawa HP saat jam pelajaran', poin: 10, tindakan: 'HP disita 1 minggu' },
  { no: 7, name: 'Merusak fasilitas sekolah', poin: 20, tindakan: 'Ganti rugi + teguran' },
  { no: 8, name: 'Bullying / perundungan', poin: 35, tindakan: 'Skorsing 1 minggu' },
  { no: 9, name: 'Mencontek saat ujian', poin: 15, tindakan: 'Nilai dibatalkan' },
  { no: 10, name: 'Membawa senjata tajam', poin: 50, tindakan: 'Dikeluarkan dari sekolah' }
]

const tiers = [
  {
    range: '0–25 Poin',
    desc: 'Peringatan & Pembinaan oleh Wali Kelas / Guru BK',
    bgClass: 'bg-emerald-50/50 border-emerald-100',
    badgeClass: 'bg-emerald-100 text-emerald-800'
  },
  {
    range: '26–50 Poin',
    desc: 'Panggilan Orang Tua & Penandatanganan Surat Perjanjian',
    bgClass: 'bg-amber-50/50 border-amber-100',
    badgeClass: 'bg-amber-100 text-amber-800'
  },
  {
    range: '51–75 Poin',
    desc: 'Skorsing Akademik & Pembinaan Khusus dengan Kepala Sekolah',
    bgClass: 'bg-rose-50/50 border-rose-100',
    badgeClass: 'bg-rose-100 text-rose-800'
  },
  {
    range: '76–100 Poin',
    desc: 'Dikeluarkan / Dikembalikan Resmi ke Orang Tua Siswa',
    bgClass: 'bg-purple-50/50 border-purple-100',
    badgeClass: 'bg-purple-100 text-purple-800'
  }
]

const getPoinColor = (poin) => {
  if (poin >= 35) return 'bg-red-100 text-red-700'
  if (poin >= 20) return 'bg-orange-100 text-orange-700'
  return 'bg-blue-100 text-blue-700'
}
</script>
