<template>
  <AppLayout active-tab="panduan" show-back>
    <div class="max-w-6xl mx-auto space-y-6">
      
      <!-- Top Subheader -->
      <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 mb-1">
            Bantuan &amp; Panduan
          </p>
          <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Master Tata Tertib &amp; Skema Pemutihan Poin</h1>
          <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
            Standar akumulasi poin pelanggaran, sanksi edukatif, serta mekanisme pemutihan poin apresiasi siswa SMK PGRI 05 Jember.
          </p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
          <button
            @click="openDeductionModal()"
            type="button"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Catat Pemutihan Poin</span>
          </button>
          <button
            @click="openRuleModal()"
            type="button"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Aturan</span>
          </button>
        </div>
      </div>

      <!-- Main Tabs Switcher -->
      <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 self-start text-xs font-medium w-full sm:w-auto">
        <button
          type="button"
          @click="activeMainTab = 'pelanggaran'"
          class="flex-1 sm:flex-none px-4 py-2 rounded-lg transition cursor-pointer text-center"
          :class="activeMainTab === 'pelanggaran' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Katalog Pelanggaran ({{ violationRules.length }})
        </button>
        <button
          type="button"
          @click="activeMainTab = 'apresiasi'"
          class="flex-1 sm:flex-none px-4 py-2 rounded-lg transition cursor-pointer text-center"
          :class="activeMainTab === 'apresiasi' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Apresiasi &amp; Pemutihan ({{ appreciationRules.length }})
        </button>
        <button
          type="button"
          @click="activeMainTab = 'riwayat_pemutihan'"
          class="flex-1 sm:flex-none px-4 py-2 rounded-lg transition cursor-pointer text-center"
          :class="activeMainTab === 'riwayat_pemutihan' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Log Pemutihan Siswa ({{ props.deductions.length }})
        </button>
      </div>

      <!-- TAB 1 & 2: KATALOG ATURAN (PELANGGARAN ATAU APRESIASI) -->
      <div v-if="activeMainTab === 'pelanggaran' || activeMainTab === 'apresiasi'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Table Column -->
        <div class="lg:col-span-2 space-y-4">
          <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            
            <!-- Toolbar: Category filter & Search -->
            <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
              <div class="flex items-center gap-1.5 flex-wrap">
                <button
                  type="button"
                  @click="selectedCategory = 'Semua'"
                  class="px-2.5 py-1 text-xs rounded-md transition cursor-pointer"
                  :class="selectedCategory === 'Semua' ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100'"
                >
                  Semua
                </button>
                <button
                  v-for="cat in availableCategories"
                  :key="cat"
                  type="button"
                  @click="selectedCategory = cat"
                  class="px-2.5 py-1 text-xs rounded-md transition cursor-pointer"
                  :class="selectedCategory === cat ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100'"
                >
                  {{ cat }}
                </button>
              </div>

              <div class="relative w-full sm:w-56">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Cari nama atau sanksi..."
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 transition"
                />
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50/50 border-b border-slate-200">
                  <tr>
                    <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-12 text-center">No</th>
                    <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kategori / Kode</th>
                    <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama Ketentuan</th>
                    <th class="py-3 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-center">Poin</th>
                    <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tindakan / Konsekuensi</th>
                    <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-if="filteredRules.length === 0">
                    <td colspan="6" class="py-8 text-center text-slate-500">
                      Tidak ada aturan yang sesuai dengan filter.
                    </td>
                  </tr>
                  <tr v-for="(item, idx) in filteredRules" :key="item.id" class="hover:bg-slate-50/80 transition">
                    <td class="py-3 px-4 text-center font-mono text-slate-400 font-medium">{{ idx + 1 }}</td>
                    <td class="py-3 px-4">
                      <div class="font-semibold text-slate-800">{{ item.category }}</div>
                      <div class="font-mono text-[10px] text-slate-400 mt-0.5">{{ item.code || '-' }}</div>
                    </td>
                    <td class="py-3 px-4 font-medium text-slate-900 whitespace-normal min-w-[220px]">
                      <div>{{ item.name }}</div>
                      <div v-if="item.description" class="text-xs text-slate-500 font-normal mt-0.5">{{ item.description }}</div>
                    </td>
                    <td class="py-3 px-3 text-center">
                      <span
                        class="font-mono text-xs font-semibold"
                        :class="item.type === 'pelanggaran' ? 'text-rose-600' : 'text-emerald-600'"
                      >
                        {{ item.type === 'pelanggaran' ? '+' : '-' }}{{ item.points }}
                      </span>
                    </td>
                    <td class="py-3 px-4 text-slate-600 whitespace-normal min-w-[180px]">
                      {{ item.sanction || '-' }}
                    </td>
                    <td class="py-3 px-4 text-right">
                      <div class="inline-flex items-center gap-2">
                        <button
                          type="button"
                          @click="editRule(item)"
                          class="text-xs font-medium text-slate-600 hover:text-slate-900 cursor-pointer"
                        >
                          Edit
                        </button>
                        <span class="text-slate-300">&bull;</span>
                        <button
                          type="button"
                          @click="deleteRule(item.id)"
                          class="text-xs font-medium text-rose-600 hover:text-rose-800 cursor-pointer"
                        >
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>

        <!-- Tingkatan Sanksi Sidebar -->
        <div class="space-y-4">
          <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <h3 class="font-bold text-slate-900 text-sm mb-1 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              Tingkatan Sanksi &amp; Akumulasi
            </h3>
            <p class="text-xs text-slate-400 mb-4">Skala tindakan penanganan berdasarkan akumulasi poin siswa</p>

            <div class="space-y-3">
              <div v-for="tier in tiers" :key="tier.range" class="p-3 rounded-lg border transition flex items-start gap-3" :class="tier.bgClass">
                <span class="font-mono text-xs font-semibold text-slate-800 shrink-0">
                  {{ tier.range }}
                </span>
                <p class="text-xs text-slate-700 leading-snug">{{ tier.desc }}</p>
              </div>
            </div>

            <!-- Panduan Pemutihan Box -->
            <div class="mt-5 p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
              <span class="font-bold text-slate-800 block">Mekanisme Pemutihan Poin:</span>
              <p class="text-slate-600 leading-relaxed">
                Siswa yang memiliki catatan sanksi dapat mengurangi akumulasi poin aktif dengan melaksanakan tugas pembinaan disiplin atau memperoleh prestasi sekolah yang diverifikasi oleh Guru BK.
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- TAB 3: LOG PEMUTIHAN POIN SISWA -->
      <div v-else-if="activeMainTab === 'riwayat_pemutihan'" class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
          
          <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <div>
              <h2 class="font-bold text-slate-900 text-sm sm:text-base">Daftar Pemutihan &amp; Apresiasi Poin Siswa</h2>
              <p class="text-xs text-slate-500 mt-0.5">Catatan pengurangan poin siswa melalui tugas pembinaan atau prestasi.</p>
            </div>
            <button
              @click="openDeductionModal()"
              type="button"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition cursor-pointer self-start sm:self-auto"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Pemutihan</span>
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50/50 border-b border-slate-200">
                <tr>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-12 text-center">No</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nama Siswa &amp; Kelas</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Bentuk Pemutihan / Kegiatan</th>
                  <th class="py-3 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-center">Pengurangan</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tanggal &amp; Pengesah</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Catatan</th>
                  <th class="py-3 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="props.deductions.length === 0">
                  <td colspan="7" class="py-8 text-center text-slate-500">
                    Belum ada riwayat pemutihan atau apresiasi poin tercatat.
                  </td>
                </tr>
                <tr v-for="(item, idx) in props.deductions" :key="item.id" class="hover:bg-slate-50/80 transition">
                  <td class="py-3 px-4 text-center font-mono text-slate-400 font-medium">{{ idx + 1 }}</td>
                  <td class="py-3 px-4">
                    <div class="font-semibold text-slate-900">{{ item.student?.name || 'Siswa' }}</div>
                    <div class="text-xs text-slate-500 font-normal mt-0.5">
                      {{ item.student?.class_name || '-' }} &bull; NISN: {{ item.student?.nisn || '-' }}
                    </div>
                  </td>
                  <td class="py-3 px-4 font-medium text-slate-800 whitespace-normal min-w-[200px]">
                    <div>{{ item.title }}</div>
                    <div class="text-[10px] text-slate-500 uppercase tracking-wide mt-0.5">{{ formatType(item.type) }}</div>
                  </td>
                  <td class="py-3 px-3 text-center">
                    <span class="font-mono text-xs font-semibold text-emerald-600">
                      -{{ item.points_deducted }} Poin
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <div class="text-slate-800 font-medium">{{ item.date }}</div>
                    <div class="text-xs text-slate-500 font-normal mt-0.5">{{ item.officer || '-' }}</div>
                  </td>
                  <td class="py-3 px-4 text-slate-600 whitespace-normal min-w-[180px]">
                    {{ item.notes || '-' }}
                  </td>
                  <td class="py-3 px-4 text-right">
                    <button
                      type="button"
                      @click="deleteDeduction(item.id)"
                      class="text-xs font-medium text-rose-600 hover:text-rose-800 cursor-pointer"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 1: TAMBAH / EDIT ATURAN TATA TERTIB                     -->
      <!-- ============================================================ -->
      <div v-if="showRuleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col">
          <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-900 text-sm sm:text-base">
              {{ editingRuleId ? 'Edit Aturan Tata Tertib' : 'Tambah Aturan Tata Tertib Baru' }}
            </h3>
            <button @click="closeRuleModal" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitRuleForm" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto max-h-[80vh]">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tipe Ketentuan</label>
                <select
                  v-model="ruleForm.type"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
                >
                  <option value="pelanggaran">Sanksi Pelanggaran</option>
                  <option value="apresiasi">Apresiasi / Pemutihan</option>
                </select>
              </div>
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Kategori</label>
                <input
                  v-model="ruleForm.category"
                  type="text"
                  placeholder="Ringan / Sedang / Berat / Prestasi"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
                />
              </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Aturan</label>
                <input
                  v-model="ruleForm.code"
                  type="text"
                  placeholder="PL-01"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600 font-mono"
                />
              </div>
              <div class="col-span-2">
                <label class="block font-semibold text-slate-700 mb-1">Bobot Poin</label>
                <input
                  v-model.number="ruleForm.points"
                  type="number"
                  min="1"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600 font-mono"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Ketentuan / Pelanggaran</label>
              <input
                v-model="ruleForm.name"
                type="text"
                placeholder="Contoh: Terlambat masuk sekolah..."
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Tindakan / Sanksi / Konsekuensi</label>
              <input
                v-model="ruleForm.sanction"
                type="text"
                placeholder="Contoh: Teguran lisan, skorsing 3 hari, atau pengurangan poin..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Deskripsi Tambahan (Opsional)</label>
              <textarea
                v-model="ruleForm.description"
                rows="2"
                placeholder="Keterangan lebih lanjut mengenai pasal atau tata cara pembinaan..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
              ></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="closeRuleModal"
                class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="ruleForm.processing"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition cursor-pointer disabled:opacity-50"
              >
                {{ ruleForm.processing ? 'Menyimpan...' : (editingRuleId ? 'Perbarui Aturan' : 'Simpan Aturan') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ============================================================ -->
      <!-- MODAL 2: CATAT PEMUTIHAN / APRESIASI POIN SISWA               -->
      <!-- ============================================================ -->
      <div v-if="showDeductionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col">
          <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-900 text-sm sm:text-base">Catat Pemutihan Poin Siswa</h3>
            <button @click="showDeductionModal = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitDeductionForm" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto max-h-[80vh]">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa</label>
              <select
                v-model="deductionForm.student_id"
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
              >
                <option value="" disabled>-- Pilih Siswa Penerima Pemutihan --</option>
                <option v-for="st in props.students" :key="st.id" :value="st.id">
                  {{ st.name }} ({{ st.class_name }}) - Poin Aktif: {{ st.total_violation_points }}
                </option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Bentuk Pembinaan / Prestasi</label>
                <select
                  v-model="deductionForm.type"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
                >
                  <option value="tugas_pembinaan">Tugas Pembinaan</option>
                  <option value="prestasi">Prestasi Lomba</option>
                  <option value="konseling_tuntas">Konseling Tuntas</option>
                  <option value="lainnya">Lainnya</option>
                </select>
              </div>
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Poin yang Dikurangkan</label>
                <input
                  v-model.number="deductionForm.points_deducted"
                  type="number"
                  min="1"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600 font-mono"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Judul Kegiatan / Bentuk Tugas</label>
              <input
                v-model="deductionForm.title"
                type="text"
                placeholder="Contoh: Bakti Sosial Kebersihan Perpustakaan..."
                required
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Tanggal Pelaksanaan</label>
                <input
                  v-model="deductionForm.date"
                  type="date"
                  required
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
                />
              </div>
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Guru BK / Petugas Pengesah</label>
                <input
                  v-model="deductionForm.officer"
                  type="text"
                  placeholder="Nama Guru BK"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Catatan Evaluasi Pembinaan</label>
              <textarea
                v-model="deductionForm.notes"
                rows="2"
                placeholder="Siswa telah menyelesaikan tugas pembinaan dengan tertib..."
                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600"
              ></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showDeductionModal = false"
                class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="deductionForm.processing"
                class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition cursor-pointer disabled:opacity-50"
              >
                {{ deductionForm.processing ? 'Menyimpan...' : 'Simpan Pemutihan' }}
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

const props = defineProps({
  rules: {
    type: Array,
    default: () => []
  },
  deductions: {
    type: Array,
    default: () => []
  },
  students: {
    type: Array,
    default: () => []
  },
  activeTab: {
    type: String,
    default: 'pelanggaran'
  }
})

const activeMainTab = ref(props.activeTab || 'pelanggaran')
const selectedCategory = ref('Semua')
const searchQuery = ref('')

const showRuleModal = ref(false)
const editingRuleId = ref(null)

const ruleForm = useForm({
  type: 'pelanggaran',
  category: 'Ringan',
  code: '',
  name: '',
  points: 5,
  sanction: '',
  description: ''
})

const showDeductionModal = ref(false)
const deductionForm = useForm({
  student_id: '',
  rule_id: null,
  title: '',
  type: 'tugas_pembinaan',
  points_deducted: 10,
  date: new Date().toISOString().split('T')[0],
  officer: 'Guru BK SMK PGRI 05 Jember',
  notes: ''
})

const violationRules = computed(() => {
  return props.rules.filter(r => r.type === 'pelanggaran')
})

const appreciationRules = computed(() => {
  return props.rules.filter(r => r.type === 'apresiasi')
})

const currentRulesList = computed(() => {
  return activeMainTab.value === 'pelanggaran' ? violationRules.value : appreciationRules.value
})

const availableCategories = computed(() => {
  const cats = new Set(currentRulesList.value.map(r => r.category).filter(Boolean))
  return Array.from(cats)
})

const filteredRules = computed(() => {
  return currentRulesList.value.filter(item => {
    const matchCat = selectedCategory.value === 'Semua' || item.category === selectedCategory.value
    const q = searchQuery.value.toLowerCase().trim()
    const matchQuery = !q ||
      item.name?.toLowerCase().includes(q) ||
      item.sanction?.toLowerCase().includes(q) ||
      item.code?.toLowerCase().includes(q)
    return matchCat && matchQuery
  })
})

const tiers = [
  {
    range: '0–25 Poin',
    desc: 'Peringatan & Pembinaan edukatif oleh Wali Kelas / Guru BK',
    bgClass: 'bg-emerald-50/40 border-emerald-100'
  },
  {
    range: '26–50 Poin',
    desc: 'Penerbitan Surat Panggilan Ortu (SP-1) & Perjanjian Tertulis',
    bgClass: 'bg-amber-50/40 border-amber-100'
  },
  {
    range: '51–75 Poin',
    desc: 'Surat Panggilan Ortu (SP-2) & Skorsing Pembinaan Khusus',
    bgClass: 'bg-rose-50/40 border-rose-100'
  },
  {
    range: '76–100 Poin',
    desc: 'Konferensi Kasus (SP-3) & Dikembalikan Resmi ke Orang Tua',
    bgClass: 'bg-purple-50/40 border-purple-100'
  }
]

const formatType = (type) => {
  switch (type) {
    case 'tugas_pembinaan': return 'Tugas Pembinaan'
    case 'prestasi': return 'Prestasi Akademik / Non-Akademik'
    case 'konseling_tuntas': return 'Konseling Tuntas'
    default: return 'Lainnya'
  }
}

const openRuleModal = () => {
  editingRuleId.value = null
  ruleForm.reset()
  ruleForm.type = activeMainTab.value === 'apresiasi' ? 'apresiasi' : 'pelanggaran'
  ruleForm.category = ruleForm.type === 'pelanggaran' ? 'Ringan' : 'Pemutihan'
  showRuleModal.value = true
}

const editRule = (item) => {
  editingRuleId.value = item.id
  ruleForm.type = item.type
  ruleForm.category = item.category
  ruleForm.code = item.code || ''
  ruleForm.name = item.name
  ruleForm.points = item.points
  ruleForm.sanction = item.sanction || ''
  ruleForm.description = item.description || ''
  showRuleModal.value = true
}

const closeRuleModal = () => {
  showRuleModal.value = false
  editingRuleId.value = null
  ruleForm.reset()
}

const submitRuleForm = () => {
  if (editingRuleId.value) {
    ruleForm.put(`/panduan/aturan/${editingRuleId.value}`, {
      preserveScroll: true,
      onSuccess: () => closeRuleModal()
    })
  } else {
    ruleForm.post('/panduan/aturan', {
      preserveScroll: true,
      onSuccess: () => closeRuleModal()
    })
  }
}

const deleteRule = (id) => {
  if (confirm('Yakin ingin menghapus aturan tata tertib ini?')) {
    router.delete(`/panduan/aturan/${id}`, { preserveScroll: true })
  }
}

const openDeductionModal = () => {
  deductionForm.reset()
  deductionForm.date = new Date().toISOString().split('T')[0]
  deductionForm.officer = 'Guru BK SMK PGRI 05 Jember'
  showDeductionModal.value = true
}

const submitDeductionForm = () => {
  deductionForm.post('/panduan/pemutihan', {
    preserveScroll: true,
    onSuccess: () => {
      showDeductionModal.value = false
      deductionForm.reset()
    }
  })
}

const deleteDeduction = (id) => {
  if (confirm('Hapus catatan pemutihan poin ini? Poin siswa akan dikalkulasi ulang.')) {
    router.delete(`/panduan/pemutihan/${id}`, { preserveScroll: true })
  }
}
</script>
