<template>
  <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-xs space-y-6">
    <!-- Header with Clear Title & Action Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-slate-200">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">
            Laporan Evaluasi Presensi Siswa
          </span>
          <span class="text-xs text-slate-400">•</span>
          <span class="text-xs text-slate-500 font-medium">Khusus Guru BK &amp; Wali Kelas</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
          Grafik Perkembangan Kehadiran Siswa
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
          Perbandingan kehadiran siswa per bulan antara tahun 2025 dan 2026.
        </p>
      </div>

      <!-- Quick Dataset & View Selectors -->
      <div class="flex flex-wrap items-center gap-2">
        <!-- Toggle Dataset -->
        <div class="bg-slate-100 p-1 rounded-lg flex text-xs font-semibold border border-slate-200">
          <button
            type="button"
            @click="activeDataset = 'kehadiran'"
            class="px-3 py-1.5 rounded-md transition cursor-pointer"
            :class="activeDataset === 'kehadiran' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
          >
            Persentase Hadir (%)
          </button>
          <button
            type="button"
            @click="activeDataset = 'kasus'"
            class="px-3 py-1.5 rounded-md transition cursor-pointer"
            :class="activeDataset === 'kasus' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
          >
            Kasus Ketidakhadiran
          </button>
        </div>

        <!-- Toggle Angka di Titik -->
        <button
          type="button"
          @click="showDataLabels = !showDataLabels"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition cursor-pointer flex items-center gap-1.5"
          :class="showDataLabels ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'"
          title="Tampilkan atau sembunyikan angka di atas setiap titik grafik"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          <span>{{ showDataLabels ? 'Label: Aktif' : 'Tampilkan Angka' }}</span>
        </button>

        <!-- Scale Toggle Button -->
        <button
          v-if="activeDataset === 'kehadiran'"
          type="button"
          @click="toggleScale"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-300 text-slate-700 hover:bg-slate-50 transition cursor-pointer flex items-center gap-1.5"
          :title="zoomScale ? 'Ganti ke skala penuh 0-100%' : 'Fokuskan rentang fluktuasi'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
          </svg>
          <span>{{ zoomScale ? 'Skala: 60% – 100%' : 'Skala: 0% – 100%' }}</span>
        </button>
      </div>
    </div>

    <!-- Quick Insight Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Card 1: Rata-rata 2026 -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-1">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">RATA-RATA 2026</span>
          <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ avg2026 }}%</span>
          <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
            </svg>
            <span>+{{ (avg2026 - avg2025).toFixed(1) }}%</span>
          </span>
        </div>
        <p class="text-xs text-slate-500 font-medium mt-1">Tahun Berjalan (Meningkat)</p>
      </div>

      <!-- Card 2: Rata-rata 2025 -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-1">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">RATA-RATA 2025</span>
          <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ avg2025 }}%</span>
          <span class="text-xs text-slate-500 font-medium">Tahun Lalu</span>
        </div>
        <p class="text-xs text-slate-500 font-medium mt-1">Sebagai Data Pembanding</p>
      </div>

      <!-- Card 3: Bulan Puncak -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-1">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">BULAN TERBAIK</span>
          <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ bestMonth.monthName }}</span>
          <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">{{ bestMonth.val2026 }}%</span>
        </div>
        <p class="text-xs text-slate-500 font-medium mt-1">Tingkat Disiplin Tertinggi</p>
      </div>

      <!-- Card 4: Titik Perhatian -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-1">
          <span class="text-xs font-semibold uppercase tracking-wider text-rose-600">PERLU ATENSI BK</span>
          <div class="w-6 h-6 rounded-md bg-rose-50 text-rose-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ lowestMonth.monthName }}</span>
          <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">{{ lowestMonth.val2026 }}%</span>
        </div>
        <p class="text-xs text-rose-600 font-medium mt-1">Masa Ujian / Pasca Kelulusan</p>
      </div>
    </div>

    <!-- Main Chart Container -->
    <div class="relative w-full bg-slate-50/80 rounded-3xl p-4 sm:p-6 border border-slate-200">
      
      <!-- Top Legend Bar Inside Chart Area (Ukuran Font Besar & Jelas) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-3 border-b border-slate-200 text-sm">
        <!-- Academic Period Notes -->
        <div class="flex items-center gap-2">
          <span class="font-black text-slate-800">Periode:</span>
          <span class="text-slate-700 font-bold">Januari s.d Desember (1 Tahun Penuh)</span>
          <span class="hidden md:inline-block px-2.5 py-0.5 bg-blue-100 text-blue-800 rounded-md text-xs font-black">SMK PGRI 05 Jember</span>
        </div>

        <!-- Interactive Legend Buttons (Tebal & Mudah Diklik) -->
        <div class="flex items-center gap-3">
          <!-- 2026 Button -->
          <button
            type="button"
            @click="toggleSeries('2026')"
            class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition cursor-pointer font-bold text-xs sm:text-sm"
            :class="(!isFiltered || activeSeries === '2026') ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-200 text-slate-600 hover:bg-slate-300'"
          >
            <span>2026 (Tahun Berjalan)</span>
          </button>

          <!-- 2025 Button -->
          <button
            type="button"
            @click="toggleSeries('2025')"
            class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition cursor-pointer font-bold text-xs sm:text-sm"
            :class="(!isFiltered || activeSeries === '2025') ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-200 text-slate-600 hover:bg-slate-300'"
          >
            <span>2025 (Tahun Lalu)</span>
          </button>

          <!-- Target Benchmark Legend -->
          <div v-if="activeDataset === 'kehadiran'" class="hidden sm:flex items-center gap-1.5 text-slate-700 text-xs sm:text-sm font-bold ml-1">
            <span class="w-5 h-1 border-t-2 border-dashed border-emerald-600"></span>
            <span>Target Disiplin (90%)</span>
          </div>
        </div>
      </div>

      <!-- Responsive SVG Graphic with High Legibility for Senior Teachers -->
      <div class="relative w-full overflow-x-auto select-none pt-2">
        <div class="min-w-[720px]">
          <svg
            viewBox="0 0 880 390"
            class="w-full h-auto overflow-visible"
            @mouseleave="hoverIndex = null"
          >
            <!-- Defs for Gradients and Shadow Filters -->
            <defs>
              <linearGradient id="modernBlueArea" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#2563eb" stop-opacity="0.28" />
                <stop offset="100%" stop-color="#2563eb" stop-opacity="0.02" />
              </linearGradient>
              <linearGradient id="modernAmberArea" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.2" />
                <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.01" />
              </linearGradient>
              <filter id="softGlow" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#1e3a8a" flood-opacity="0.25" />
              </filter>
            </defs>

            <!-- Y-Axis Vertical Label (Besar & Kontras) -->
            <text
              x="-185"
              y="22"
              transform="rotate(-90)"
              text-anchor="middle"
              class="text-[13px] font-black fill-slate-800 tracking-wider uppercase font-sans"
            >
              {{ currentConfig.yLabel }}
            </text>

            <!-- Horizontal Gridlines & Y-Axis Scale Values (Tebal & Terbaca Jelas) -->
            <g v-for="(tick, tIdx) in gridTicks" :key="'grid-' + tIdx">
              <line
                :x1="padding.left"
                :y1="getY(tick.val)"
                :x2="padding.left + chartWidth"
                :y2="getY(tick.val)"
                :stroke="tick.val === 90 && activeDataset === 'kehadiran' ? '#059669' : '#cbd5e1'"
                :stroke-width="tick.val === 90 && activeDataset === 'kehadiran' ? '2' : '1.2'"
                :stroke-dasharray="tick.val === 90 && activeDataset === 'kehadiran' ? '7 4' : '4 4'"
              />
              <text
                :x="padding.left - 14"
                :y="getY(tick.val) + 5"
                text-anchor="end"
                class="text-[13px] font-black font-sans"
                :class="tick.val === 90 && activeDataset === 'kehadiran' ? 'fill-emerald-800 text-[14px]' : 'fill-slate-800'"
              >
                {{ tick.label }}
              </text>
            </g>

            <!-- Target Line Badge on right side -->
            <g v-if="activeDataset === 'kehadiran'">
              <rect
                :x="padding.left + chartWidth - 110"
                :y="getY(90) - 12"
                width="110"
                height="24"
                rx="6"
                fill="#d1fae5"
                stroke="#059669"
                stroke-width="1.5"
              />
              <text
                :x="padding.left + chartWidth - 55"
                :y="getY(90) + 5"
                text-anchor="middle"
                class="text-[11px] font-black fill-emerald-900 font-sans"
              >
                Target Disiplin 90%
              </text>
            </g>

            <!-- Vertical Columns, Academic Semesters & Hover Triggers -->
            <g v-for="(item, idx) in currentData" :key="'col-' + idx">
              <!-- Vertical Hover Line -->
              <line
                v-if="hoverIndex === idx"
                :x1="getX(idx)"
                :y1="padding.top - 10"
                :x2="getX(idx)"
                :y2="getY(minY)"
                stroke="#2563eb"
                stroke-width="2.5"
                stroke-dasharray="4 4"
                opacity="0.9"
              />

              <!-- Transparent Mouse Trigger Column Area -->
              <rect
                :x="getX(idx) - (stepX / 2)"
                :y="padding.top - 10"
                :width="stepX"
                :height="chartHeight + 20"
                fill="transparent"
                class="cursor-pointer"
                @mouseenter="hoverIndex = idx"
              />

              <!-- X-Axis Month Name Label (Jan, Feb, Mar...) BESAR & TEBAL -->
              <text
                :x="getX(idx)"
                :y="getY(minY) + 24"
                text-anchor="middle"
                class="text-[14px] font-black font-sans transition-all"
                :class="hoverIndex === idx ? 'fill-blue-700 text-[16px]' : 'fill-slate-900'"
              >
                {{ item.shortName }}
              </text>

              <!-- Month Subtitle (Bln 01, Bln 02...) JELAS & MUDAH DIPAHAMI -->
              <text
                :x="getX(idx)"
                :y="getY(minY) + 40"
                text-anchor="middle"
                class="text-[11px] font-bold font-sans"
                :class="hoverIndex === idx ? 'fill-blue-600 font-black' : 'fill-slate-600'"
              >
                Bln {{ item.month < 10 ? '0' + item.month : item.month }}
              </text>
            </g>

            <!-- Series 1: Amber Line (2025 / Tahun Lalu) -->
            <g v-if="!isFiltered || activeSeries === '2025'">
              <!-- Area Fill -->
              <polygon
                :points="areaPointsAmber"
                fill="url(#modernAmberArea)"
              />
              <!-- Polyline Stroke (Tebal 3.5px) -->
              <polyline
                :points="linePointsAmber"
                fill="none"
                stroke="#d97706"
                stroke-width="3.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <!-- Data Points (Lingkaran Besar) -->
              <circle
                v-for="(item, idx) in currentData"
                :key="'pt-amber-' + idx"
                :cx="getX(idx)"
                :cy="getY(item.val2025)"
                :r="hoverIndex === idx ? 7 : 5"
                fill="#ffffff"
                stroke="#d97706"
                :stroke-width="hoverIndex === idx ? 3.5 : 2.5"
                class="transition-all duration-150 pointer-events-none shadow-sm"
              />
            </g>

            <!-- Series 2: Blue Line (2026 / Tahun Berjalan) -->
            <g v-if="!isFiltered || activeSeries === '2026'">
              <!-- Area Fill -->
              <polygon
                :points="areaPointsBlue"
                fill="url(#modernBlueArea)"
              />
              <!-- Polyline Stroke (Tebal 4.5px dengan bayangan) -->
              <polyline
                :points="linePointsBlue"
                fill="none"
                stroke="#2563eb"
                stroke-width="4.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                filter="url(#softGlow)"
              />
              <!-- Data Points (Lingkaran Besar 6px) -->
              <circle
                v-for="(item, idx) in currentData"
                :key="'pt-blue-' + idx"
                :cx="getX(idx)"
                :cy="getY(item.val2026)"
                :r="hoverIndex === idx ? 8.5 : 6"
                fill="#ffffff"
                stroke="#2563eb"
                :stroke-width="hoverIndex === idx ? 4 : 3"
                class="transition-all duration-150 pointer-events-none shadow-md"
              />

              <!-- Direct Data Labels on Points (Angka langsung terbaca tanpa perlu hover!) -->
              <g v-if="showDataLabels">
                <g v-for="(item, idx) in currentData" :key="'label-2026-' + idx">
                  <!-- Pill Background -->
                  <rect
                    :x="getX(idx) - 22"
                    :y="getY(item.val2026) - 28"
                    width="44"
                    height="19"
                    rx="5"
                    fill="#1e40af"
                    class="shadow-sm"
                  />
                  <!-- Text Value -->
                  <text
                    :x="getX(idx)"
                    :y="getY(item.val2026) - 14"
                    text-anchor="middle"
                    class="text-[10.5px] font-black fill-white font-sans"
                  >
                    {{ item.val2026 }}{{ activeDataset === 'kehadiran' ? '%' : '' }}
                  </text>
                </g>
              </g>
            </g>
          </svg>
        </div>
      </div>

      <!-- Detail Card on Hover (Contextual Academic Tooltip for Guru BK - Huruf Besar & Tegas) -->
      <div
        v-if="hoverIndex !== null && currentHoverItem"
        class="mt-4 p-4 sm:p-5 rounded-2xl bg-slate-900 text-white shadow-xl border border-slate-700 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4"
      >
        <div class="flex items-center gap-3.5">
          <div class="w-13 h-13 rounded-2xl bg-blue-600 border-2 border-blue-400 flex flex-col items-center justify-center font-black text-white shrink-0 shadow-md">
            <span class="text-sm uppercase">{{ currentHoverItem.shortName }}</span>
            <span class="text-[11px] text-blue-200">Bln {{ currentHoverItem.month }}</span>
          </div>
          <div>
            <div class="text-xs font-black text-blue-400 uppercase tracking-wider">
              RINCIAN EVALUASI PRESENSI
            </div>
            <div class="text-base sm:text-lg font-black text-white">
              Bulan {{ currentHoverItem.monthName }} (2026 vs 2025)
            </div>
            <div class="text-xs sm:text-sm text-slate-300 mt-0.5 font-medium">
              Catatan Sekolah: <strong class="text-white">{{ currentHoverItem.note }}</strong>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-6 text-sm">
          <!-- 2025 Value -->
          <div class="flex flex-col">
            <span class="text-amber-300 font-bold text-base flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-xs bg-amber-400"></span>
              2025: {{ formatValue(currentHoverItem.val2025) }}
            </span>
            <span class="text-xs text-slate-400 font-medium">Tahun Sebelumnya</span>
          </div>

          <!-- 2026 Value -->
          <div class="flex flex-col">
            <span class="text-blue-300 font-bold text-base flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-xs bg-blue-400"></span>
              2026: {{ formatValue(currentHoverItem.val2026) }}
            </span>
            <span class="text-xs text-slate-400 font-medium">Tahun Berjalan</span>
          </div>

          <!-- Delta / Difference -->
          <div class="flex flex-col border-l border-slate-700 pl-4">
            <span
              class="font-black flex items-center gap-1 text-sm sm:text-base"
              :class="currentHoverItem.val2026 >= currentHoverItem.val2025 ? 'text-emerald-400' : 'text-rose-400'"
            >
              <svg v-if="currentHoverItem.val2026 >= currentHoverItem.val2025" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v3.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
              </svg>
              <span>{{ currentHoverItem.val2026 >= currentHoverItem.val2025 ? 'Naik +' : 'Turun -' }} {{ Math.abs(currentHoverItem.val2026 - currentHoverItem.val2025).toFixed(1) }}{{ activeDataset === 'kehadiran' ? '%' : ' Kasus' }}</span>
            </span>
            <span class="text-xs text-slate-400 font-bold">Perubahan Disiplin</span>
          </div>

          <!-- Target Status -->
          <div v-if="activeDataset === 'kehadiran'" class="flex flex-col border-l border-slate-700 pl-4">
            <span
              class="font-black text-sm"
              :class="currentHoverItem.val2026 >= 90 ? 'text-emerald-300' : 'text-amber-300'"
            >
              {{ currentHoverItem.val2026 >= 90 ? 'Capai Target (≥90%)' : 'Di Bawah Target (<90%)' }}
            </span>
            <span class="text-xs text-slate-400 font-bold">Standar Disiplin</span>
          </div>
        </div>
      </div>

      <!-- Idle Hint if no point is hovered (Font Jelas) -->
      <div v-else class="mt-3 text-center text-xs sm:text-sm text-slate-600 font-medium py-1 flex items-center justify-center gap-1.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span><strong>Petunjuk:</strong> Arahkan kursor pada titik grafik untuk rincian otomatis, atau lihat tabel data bulanan di bawah.</span>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TABEL RINCIAN BULANAN (SANGAT MUDAH DIBACA OLEH GURU / ORANG TUA)         -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
          <h3 class="text-lg sm:text-xl font-bold text-slate-900">
            Tabel Rincian Kehadiran Siswa Per Bulan
          </h3>
          <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
            Daftar angka pasti tiap bulan, bisa dibaca langsung tanpa harus mengarahkan kursor mouse ke grafik.
          </p>
        </div>
        <button
          @click="showTable = !showTable"
          class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm rounded-xl cursor-pointer self-start sm:self-auto transition flex items-center gap-1.5"
        >
          <span>{{ showTable ? 'Sembunyikan Tabel' : 'Buka Tabel Lengkap' }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="showTable ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
      </div>

      <div v-if="showTable" class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
          <thead>
            <tr class="bg-slate-100 text-slate-800 text-xs sm:text-sm font-black border-b-2 border-slate-300">
              <th class="py-3 px-3.5 rounded-l-xl">Bulan</th>
              <th class="py-3 px-3.5 text-center">Tahun 2026 (Berjalan)</th>
              <th class="py-3 px-3.5 text-center">Tahun 2025 (Lalu)</th>
              <th class="py-3 px-3.5 text-center">Kenaikan / Penurunan</th>
              <th class="py-3 px-3.5 text-center">Status Disiplin (90%)</th>
              <th class="py-3 px-3.5 rounded-r-xl">Keterangan Kegiatan Sekolah</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
            <tr
              v-for="item in currentData"
              :key="'tbl-' + item.month"
              class="hover:bg-blue-50/60 transition"
              :class="item.val2026 >= 90 ? 'bg-white' : 'bg-amber-50/30'"
            >
              <td class="py-3 px-3.5 font-black text-slate-900">
                {{ item.month < 10 ? '0' + item.month : item.month }}. {{ item.monthName }}
              </td>
              <td class="py-3 px-3.5 text-center">
                <span class="inline-block px-3 py-1 rounded-lg bg-blue-100 text-blue-900 font-black text-sm border border-blue-300">
                  {{ formatValue(item.val2026) }}
                </span>
              </td>
              <td class="py-3 px-3.5 text-center">
                <span class="inline-block px-3 py-1 rounded-lg bg-amber-100 text-amber-900 font-bold text-sm border border-amber-300">
                  {{ formatValue(item.val2025) }}
                </span>
              </td>
              <td class="py-3 px-3.5 text-center font-black">
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs sm:text-sm font-black"
                  :class="item.val2026 >= item.val2025 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                >
                  <svg v-if="item.val2026 >= item.val2025" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v3.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ item.val2026 >= item.val2025 ? '+' : '-' }}{{ Math.abs(item.val2026 - item.val2025).toFixed(1) }}%</span>
                </span>
              </td>
              <td class="py-3 px-3.5 text-center">
                <span
                  v-if="activeDataset === 'kehadiran'"
                  class="inline-block px-2.5 py-1 rounded-full text-xs font-black border"
                  :class="item.val2026 >= 90 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300'"
                >
                  {{ item.val2026 >= 90 ? 'Memenuhi Target' : 'Perlu Pembinaan' }}
                </span>
                <span v-else class="text-xs font-bold text-slate-600">-</span>
              </td>
              <td class="py-3 px-3.5 text-xs sm:text-sm text-slate-700 font-medium">
                {{ item.note }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Practical Guidance for BK Teachers (NO EMOJIS, Professional Cards - Huruf Besar & Kontras) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
      <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-5 flex items-start gap-3.5 shadow-xs">
        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
          </svg>
        </div>
        <div>
          <h4 class="text-sm font-black text-blue-950">Puncak Kehadiran Tertinggi</h4>
          <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed">
            Bulan September &amp; November mencapai rekor tertinggi (<strong>97.4%</strong>) berkat pendampingan rutin Guru BK dan Wali Kelas.
          </p>
        </div>
      </div>

      <div class="bg-emerald-50 border-2 border-emerald-200 rounded-2xl p-5 flex items-start gap-3.5 shadow-xs">
        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
        </div>
        <div>
          <h4 class="text-sm font-black text-emerald-950">Efektivitas Pendampingan BK</h4>
          <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed">
            Garis biru tahun 2026 konsisten berada <strong>+3.1% di atas</strong> tahun 2025 berkat notifikasi presensi cepat kepada orang tua.
          </p>
        </div>
      </div>

      <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-5 flex items-start gap-3.5 shadow-xs">
        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </div>
        <div>
          <h4 class="text-sm font-black text-amber-950">Pola Penurunan Berkala</h4>
          <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed">
            Penurunan pada bulan Juni (<strong>83.1%</strong>) adalah siklus tahunan wajar pasca ujian semester dan persiapan kenaikan kelas.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeDataset = ref('kehadiran')
const hoverIndex = ref(null)
const isFiltered = ref(false)
const activeSeries = ref('all') // 'all', '2025', '2026'
const zoomScale = ref(true) // true: 60-100%, false: 0-100%
const showDataLabels = ref(true) // Tampilkan angka di atas setiap titik grafik secara bawaan
const showTable = ref(true) // Tampilkan tabel bulanan secara bawaan untuk memudahkan guru membaca data

// Chart Geometry (Ruang lebih lega untuk label angka & nama bulan)
const padding = { top: 45, bottom: 60, left: 70, right: 40 }
const totalWidth = 880
const totalHeight = 390
const chartWidth = totalWidth - padding.left - padding.right
const chartHeight = totalHeight - padding.top - padding.bottom

// Dataset 1: Kehadiran Siswa (%) - Realistis data sekolah SMK PGRI 05
const datasetKehadiran = [
  { month: 1, shortName: 'Jan', monthName: 'Januari', val2025: 86.5, val2026: 88.2, note: 'Awal Masuk Semester Genap' },
  { month: 2, shortName: 'Feb', monthName: 'Februari', val2025: 82.0, val2026: 85.4, note: 'Kegiatan Pembelajaran Efektif' },
  { month: 3, shortName: 'Mar', monthName: 'Maret', val2025: 89.1, val2026: 92.0, note: 'Ujian Tengah Semester (UTS)' },
  { month: 4, shortName: 'Apr', monthName: 'April', val2025: 84.5, val2026: 87.8, note: 'Pesantren Kilat & Libur Idul Fitri' },
  { month: 5, shortName: 'Mei', monthName: 'Mei', val2025: 93.0, val2026: 94.6, note: 'Praktik Kerja Industri & Uji Sertifikasi' },
  { month: 6, shortName: 'Jun', monthName: 'Juni', val2025: 78.4, val2026: 83.1, note: 'Penilaian Akhir Tahun (PAT) & Libur' },
  { month: 7, shortName: 'Jul', monthName: 'Juli', val2025: 94.2, val2026: 91.5, note: 'MPLS & Awal Tahun Ajaran Baru' },
  { month: 8, shortName: 'Agu', monthName: 'Agustus', val2025: 81.3, val2026: 89.0, note: 'Lomba HUT RI & Ekstrakurikuler' },
  { month: 9, shortName: 'Sep', monthName: 'September', val2025: 90.5, val2026: 96.8, note: 'PTS Ganjil & Penegakan Disiplin BK' },
  { month: 10, shortName: 'Okt', monthName: 'Oktober', val2025: 87.2, val2026: 90.4, note: 'Bulan Bahasa & Pembinaan Karakter' },
  { month: 11, shortName: 'Nov', monthName: 'November', val2025: 95.8, val2026: 97.4, note: 'Pemantapan Jelang Ujian Akhir' },
  { month: 12, shortName: 'Des', monthName: 'Desember', val2025: 85.0, val2026: 91.0, note: 'PAS Ganjil, Classmeeting & Rapor' }
]

// Dataset 2: Kasus Ketidakhadiran (Alpa/Sakit/Izin Tanpa Keterangan)
const datasetKasus = [
  { month: 1, shortName: 'Jan', monthName: 'Januari', val2025: 35, val2026: 28, note: 'Adaptasi awal semester' },
  { month: 2, shortName: 'Feb', monthName: 'Februari', val2025: 42, val2026: 34, note: 'Musim hujan & kondisi kesehatan' },
  { month: 3, shortName: 'Mar', monthName: 'Maret', val2025: 26, val2026: 18, note: 'Tertib selama UTS' },
  { month: 4, shortName: 'Apr', monthName: 'April', val2025: 38, val2026: 29, note: 'Izin mudik dan keluarga' },
  { month: 5, shortName: 'Mei', monthName: 'Mei', val2025: 18, val2026: 12, note: 'Kehadiran padat jadwal kejuruan' },
  { month: 6, shortName: 'Jun', monthName: 'Juni', val2025: 58, val2026: 42, note: 'Pasca ujian akhir tahun' },
  { month: 7, shortName: 'Jul', monthName: 'Juli', val2025: 15, val2026: 22, note: 'Siswa baru MPLS' },
  { month: 8, shortName: 'Agu', monthName: 'Agustus', val2025: 48, val2026: 26, note: 'Latihan baris-berbaris' },
  { month: 9, shortName: 'Sep', monthName: 'September', val2025: 22, val2026: 9, note: 'Program patroli kedisiplinan' },
  { month: 10, shortName: 'Okt', monthName: 'Oktober', val2025: 31, val2026: 23, note: 'Kompensasi poin pelanggaran' },
  { month: 11, shortName: 'Nov', monthName: 'November', val2025: 11, val2026: 7, note: 'Tingkat kehadiran prima' },
  { month: 12, shortName: 'Des', monthName: 'Desember', val2025: 39, val2026: 21, note: 'Akhir semester & pembagian rapor' }
]

const currentData = computed(() => {
  return activeDataset.value === 'kehadiran' ? datasetKehadiran : datasetKasus
})

// Scale Boundaries
const minY = computed(() => {
  if (activeDataset.value === 'kehadiran') {
    return zoomScale.value ? 60 : 0
  }
  return 0
})

const maxY = computed(() => {
  if (activeDataset.value === 'kehadiran') {
    return 100
  }
  return 60
})

const currentConfig = computed(() => {
  if (activeDataset.value === 'kehadiran') {
    return {
      yLabel: 'Tingkat Kehadiran (%)'
    }
  } else {
    return {
      yLabel: 'Jumlah Kasus (Siswa)'
    }
  }
})

// Dynamic Gridlines
const gridTicks = computed(() => {
  if (activeDataset.value === 'kehadiran') {
    if (zoomScale.value) {
      return [
        { val: 100, label: '100%' },
        { val: 90, label: '90%' },
        { val: 80, label: '80%' },
        { val: 70, label: '70%' },
        { val: 60, label: '60%' }
      ]
    } else {
      return [
        { val: 100, label: '100%' },
        { val: 75, label: '75%' },
        { val: 50, label: '50%' },
        { val: 25, label: '25%' },
        { val: 0, label: '0%' }
      ]
    }
  } else {
    return [
      { val: 60, label: '60 Kasus' },
      { val: 45, label: '45 Kasus' },
      { val: 30, label: '30 Kasus' },
      { val: 15, label: '15 Kasus' },
      { val: 0, label: '0' }
    ]
  }
})

// Geometry Helper Functions
const stepX = computed(() => {
  return chartWidth / (currentData.value.length - 1)
})

const getX = (index) => {
  return padding.left + index * stepX.value
}

const getY = (val) => {
  const min = minY.value
  const max = maxY.value
  const clamped = Math.max(min, Math.min(max, val))
  const ratio = (clamped - min) / (max - min)
  return padding.top + chartHeight - ratio * chartHeight
}

// Line and Area SVG Coordinate Generators
const linePointsAmber = computed(() => {
  return currentData.value.map((d, i) => `${getX(i)},${getY(d.val2025)}`).join(' ')
})

const linePointsBlue = computed(() => {
  return currentData.value.map((d, i) => `${getX(i)},${getY(d.val2026)}`).join(' ')
})

const areaPointsAmber = computed(() => {
  const points = currentData.value.map((d, i) => `${getX(i)},${getY(d.val2025)}`)
  const lastX = getX(currentData.value.length - 1)
  const firstX = getX(0)
  const bottomY = getY(minY.value)
  return `${points.join(' ')} ${lastX},${bottomY} ${firstX},${bottomY}`
})

const areaPointsBlue = computed(() => {
  const points = currentData.value.map((d, i) => `${getX(i)},${getY(d.val2026)}`)
  const lastX = getX(currentData.value.length - 1)
  const firstX = getX(0)
  const bottomY = getY(minY.value)
  return `${points.join(' ')} ${lastX},${bottomY} ${firstX},${bottomY}`
})

// Hover state
const currentHoverItem = computed(() => {
  if (hoverIndex.value === null) return null
  return currentData.value[hoverIndex.value] || null
})

// Summary Stats
const avg2026 = computed(() => {
  const sum = datasetKehadiran.reduce((acc, cur) => acc + cur.val2026, 0)
  return (sum / datasetKehadiran.length).toFixed(1)
})

const avg2025 = computed(() => {
  const sum = datasetKehadiran.reduce((acc, cur) => acc + cur.val2025, 0)
  return (sum / datasetKehadiran.length).toFixed(1)
})

const bestMonth = computed(() => {
  return [...datasetKehadiran].sort((a, b) => b.val2026 - a.val2026)[0]
})

const lowestMonth = computed(() => {
  return [...datasetKehadiran].sort((a, b) => a.val2026 - b.val2026)[0]
})

const formatValue = (val) => {
  if (activeDataset.value === 'kehadiran') {
    return `${val.toFixed(1)}%`
  }
  return `${val} Kasus`
}

const toggleSeries = (series) => {
  if (isFiltered.value && activeSeries.value === series) {
    isFiltered.value = false
    activeSeries.value = 'all'
  } else {
    isFiltered.value = true
    activeSeries.value = series
  }
}

const toggleScale = () => {
  zoomScale.value = !zoomScale.value
}
</script>
