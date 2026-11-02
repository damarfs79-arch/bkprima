<template>
  <div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row font-sans text-gray-800 relative">
    <aside class="hidden lg:flex w-64 xl:w-72 flex-col bg-white border-r border-gray-200/80 min-h-screen sticky top-0 h-screen shrink-0 z-30 shadow-xs select-none">
      
      <!-- Brand Logo Header (Foto 1) -->
      <div class="p-4 sm:p-5 border-b border-gray-100">
        <router-link to="/absensi" class="flex items-center gap-3 group">
          <img
            src="/logo-bkprima.png"
            alt="BK Prima - Sistem Informasi &amp; Konseling SMK PGRI 5 Jember"
            class="h-10 sm:h-11 w-auto max-w-full object-contain transition-transform duration-200 group-hover:scale-102"
          />
        </router-link>
      </div>


      <!-- Navigation Menu List -->
      <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>
        
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition group"
          :class="isRouteActive(item.key) ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-slate-100 hover:text-gray-900'"
        >
          <div
            class="p-1 rounded-lg transition"
            :class="isRouteActive(item.key) ? 'bg-white/20 text-white' : 'text-gray-400 group-hover:text-blue-600'"
          >
            <component :is="item.icon" class="w-4 h-4" />
          </div>
          <span class="flex-1">{{ item.label }}</span>
          <span v-if="item.badge" class="px-2 py-0.5 text-[10px] font-black rounded-full" :class="isRouteActive(item.key) ? 'bg-white text-blue-600' : 'bg-blue-50 text-blue-600'">
            {{ item.badge }}
          </span>
        </router-link>
      </nav>

      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-gray-100 space-y-3">
        <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-gray-100 rounded-xl text-xs font-medium text-gray-600">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="truncate">Semester Genap 24/25</span>
        </div>
        <button
          type="button"
          @click="handleLogout"
          class="flex items-center justify-center gap-2 w-full py-2.5 px-3 rounded-xl text-xs font-bold text-rose-600 bg-rose-50/60 hover:bg-rose-100 transition cursor-pointer"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Keluar Akun</span>
        </button>
      </div>

    </aside>

    <!-- ============================================================ -->
    <!-- 2. MOBILE TOP BAR (Exact Hamburger Button as Image 1)       -->
    <!-- ============================================================ -->
    <div class="lg:hidden bg-white border-b border-gray-200/90 px-4 py-2.5 sticky top-0 z-30 flex items-center justify-between shadow-2xs">
      
      <!-- Left: Hamburger Button (Exact Image 1 Replica) -->
      <div class="flex items-center gap-2.5">
        <button
          @click="mobileDrawerOpen = true"
          class="w-10 h-10 rounded-2xl bg-white hover:bg-gray-50 border border-gray-200/70 shadow-2xs flex flex-col items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
          title="Buka Menu Sidebar"
        >
          <span class="w-5 h-[3px] bg-[#1e293b] rounded-full block"></span>
          <span class="w-5 h-[3px] bg-[#1e293b] rounded-full block"></span>
          <span class="w-5 h-[3px] bg-[#1e293b] rounded-full block"></span>
        </button>

        <button v-if="showBack" @click="handleBack" class="p-1.5 rounded-xl text-gray-500 hover:bg-gray-100" title="Kembali">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <router-link to="/absensi" class="flex items-center">
          <img src="/logo-bkprima.png" alt="BK Prima SMK PGRI 5 Jember" class="h-8 w-auto object-contain" />
        </router-link>
      </div>

      <!-- Right: Share & Profile Avatar -->
      <div class="flex items-center gap-2">
        <button v-if="showShare" @click="handleShare" class="p-2 text-gray-500 hover:text-gray-800 rounded-xl" title="Bagikan">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
          </svg>
        </button>
        <router-link to="/profil" class="relative">
          <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200" />
          <div class="absolute bottom-0 right-0 w-2 h-2 bg-emerald-500 rounded-full border-2 border-white"></div>
        </router-link>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- 3. MOBILE SLIDE-OVER DRAWER (Full Sidebar on Mobile)         -->
    <!-- ============================================================ -->
    <div v-if="mobileDrawerOpen" class="fixed inset-0 z-50 lg:hidden flex">
      <!-- Backdrop -->
      <div @click="mobileDrawerOpen = false" class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity animate-in fade-in"></div>

      <!-- Drawer Content -->
      <div class="relative w-72 max-w-[82vw] bg-white h-full flex flex-col z-50 shadow-2xl animate-in slide-in-from-left duration-200">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <img src="/logo-smk.png" alt="Logo SMK" class="w-8 h-8 object-contain" />
            <div>
              <div class="flex items-center gap-1">
                <span class="bg-blue-600 text-white text-[9px] font-black px-1 py-0.5 rounded">BK</span>
                <span class="font-black text-gray-900 text-lg">Prima</span>
              </div>
              <p class="text-[9px] font-bold text-gray-400">SMK PGRI 05 JEMBER</p>
            </div>
          </div>
          <button @click="mobileDrawerOpen = false" class="p-2 text-gray-400 hover:text-gray-700 rounded-xl cursor-pointer" title="Tutup Menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>


        <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
          <router-link
            v-for="item in menuItems"
            :key="item.path"
            :to="item.path"
            @click="mobileDrawerOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition"
            :class="isRouteActive(item.key) ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-700 hover:bg-gray-100'"
          >
            <component :is="item.icon" class="w-4 h-4" />
            <span class="flex-1">{{ item.label }}</span>
            <span v-if="item.badge" class="px-2 py-0.5 text-[10px] font-black rounded-full" :class="isRouteActive(item.key) ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-700'">
              {{ item.badge }}
            </span>
          </router-link>
        </nav>

        <div class="p-4 border-t border-gray-100">
          <button
            type="button"
            @click="mobileDrawerOpen = false; handleLogout()"
            class="flex items-center justify-center gap-2 w-full py-2.5 px-3 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Keluar Akun</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- 4. MAIN CONTENT WRAPPER                                      -->
    <!-- ============================================================ -->
    <div class="flex-1 flex flex-col min-w-0">
      
      <!-- Optional Desktop Top Minimal Bar -->
      <header class="hidden lg:flex items-center justify-between px-8 py-4 bg-white/60 backdrop-blur-md border-b border-gray-200/50 sticky top-0 z-20">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
          <button v-if="showBack" @click="handleBack" class="p-1 text-gray-400 hover:text-gray-800 rounded cursor-pointer mr-1">
            &larr; Kembali
          </button>
          <span class="text-gray-400">Portal BK</span>
          <span class="text-gray-300">/</span>
          <span class="text-blue-600 uppercase tracking-wider text-[11px]">{{ currentTitle }}</span>
        </div>

        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 border border-gray-200/80 rounded-xl text-xs text-gray-600 font-medium">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>HadirMu Terhubung</span>
          </div>

          <!-- Notification Button -->
          <router-link to="/piket" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition relative cursor-pointer" title="Notifikasi Piket">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
          </router-link>
        </div>
      </header>

      <!-- Main Content Slot -->
      <main class="flex-1 p-3 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full pb-32 lg:pb-10">
        <slot />
      </main>
    </div>

    <!-- ============================================================ -->
    <!-- 5. MOBILE BOTTOM NAVIGATION (ANIMATED SLIDING CIRCLE)        -->
    <!-- ============================================================ -->
    <nav class="lg:hidden fixed bottom-4 left-4 right-4 z-40 max-w-sm mx-auto">
      <div class="bg-blue-600 rounded-3xl shadow-xl shadow-blue-600/35 h-[68px] px-1 flex items-center relative select-none">
        
        <!-- Sliding Elevated Animated Indicator (Geser & Naik ke Atas / Tidak Sejajar) -->
        <div 
          class="absolute top-0 bottom-0 left-0 w-1/4 flex items-center justify-center pointer-events-none transition-transform duration-300 ease-[cubic-bezier(0.34,1.35,0.64,1)] z-10"
          :style="{
            transform: `translateX(${activeBottomIndex * 100}%)`
          }"
        >
          <!-- Elemen Bulat Putih dengan Pusat Biru Naik ke Atas (-translate-y-4) -->
          <div class="w-[62px] h-[62px] rounded-full bg-white flex items-center justify-center shadow-xl shadow-blue-950/35 border-[3px] border-white -translate-y-4 transition-transform duration-200">
            <div class="w-[48px] h-[48px] rounded-full bg-blue-600 flex flex-col items-center justify-center shadow-inner">
              <component :is="bottomNavTabs[activeBottomIndex].activeIcon || bottomNavTabs[activeBottomIndex].icon" class="w-5 h-5 text-white" />
              <span class="text-[7.5px] font-black text-white leading-none mt-0.5 tracking-tight px-0.5">
                {{ bottomNavTabs[activeBottomIndex].label }}
              </span>
            </div>
          </div>
        </div>

        <!-- 4 Tab Buttons -->
        <router-link
          v-for="(item, idx) in bottomNavTabs"
          :key="item.key"
          :to="item.path"
          @click="handleBottomTabClick(idx)"
          class="flex-1 flex flex-col items-center justify-center relative z-20 h-full py-1 cursor-pointer select-none text-center"
        >
          <!-- Inactive State: Sejajar di dalam bar, memudar saat lingkaran aktif meluncur di atasnya -->
          <div 
            class="flex flex-col items-center justify-center py-1 transition-all duration-200"
            :class="activeBottomIndex === idx ? 'opacity-0 scale-75' : 'opacity-85 hover:opacity-100'"
          >
            <component :is="item.inactiveIcon || item.icon" class="w-5 h-5 text-white" />
            <span class="text-[10px] font-semibold text-white mt-1 leading-tight">{{ item.label }}</span>
          </div>
        </router-link>

      </div>
    </nav>
    <!-- ============================================================ -->
    <!-- MODAL CARD: KONFIRMASI KELUAR AKUN                           -->
    <!-- ============================================================ -->
    <div
      v-if="showLogoutModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-150"
      @click.self="showLogoutModal = false"
    >
      <div class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-center animate-in zoom-in-95 duration-150">
        
        <!-- Icon Logout -->
        <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </div>

        <!-- Eyebrow & Title -->
        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
          Konfirmasi Keluar Akun
        </div>
        <h3 class="text-base sm:text-lg font-bold text-slate-900">
          Keluar dari Akun BK Prima?
        </h3>

        <!-- Description -->
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
          Sesi aktif Anda akan diakhiri. Pastikan seluruh pekerjaan atau catatan bimbingan konseling telah tersimpan.
        </p>

        <!-- Session Info Card -->
        <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-left text-xs">
          <div class="flex items-center justify-between">
            <span class="font-bold text-slate-800">Guru Bimbingan Konseling</span>
            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[10px]">Portal Pengguna</span>
          </div>
          <p class="text-[11px] text-slate-400 mt-0.5">SMK PGRI 05 Jember &bull; Sesi Bimbingan Aktif</p>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-2.5 mt-6 pt-2">
          <button
            type="button"
            @click="showLogoutModal = false"
            class="w-full py-2.5 px-4 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
          >
            Batal
          </button>

          <button
            type="button"
            @click="confirmLogout"
            class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition cursor-pointer flex items-center justify-center gap-1.5"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Ya, Keluar</span>
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, h } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { router as inertiaRouter } from '@inertiajs/vue3'

const router = useRouter()
const route = useRoute()



const showLogoutModal = ref(false)

const handleLogout = () => {
  showLogoutModal.value = true
}

const confirmLogout = () => {
  showLogoutModal.value = false
  inertiaRouter.post('/logout')
}

const props = defineProps({
  activeTab: {
    type: String,
    default: 'beranda'
  },
  showBack: {
    type: Boolean,
    default: false
  },
  showShare: {
    type: Boolean,
    default: false
  }
})

const mobileDrawerOpen = ref(false)

const handleBack = () => {
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/absensi')
  }
}

const handleShare = () => {
  if (navigator.share) {
    navigator.share({
      title: 'BK Prima - HadirMu SMK PGRI 05 Jember',
      url: window.location.href
    }).catch(() => {})
  } else {
    alert('Link halaman berhasil disalin!')
  }
}

// Menu definitions for Desktop Sidebar & Mobile Drawer
const menuItems = [
  {
    key: 'absensi',
    label: 'Rekap Presensi',
    path: '/absensi',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
    ])
  },
  {
    key: 'absensi_bulanan',
    label: 'Rekap Bulanan (A4)',
    path: '/absensi/bulanan',
    badge: 'Cetak',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
    ])
  },
  {
    key: 'pelanggaran',
    label: 'Catatan Pelanggaran',
    path: '/pelanggaran',
    badge: 'Kasus',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
    ])
  },
  {
    key: 'verifikasi',
    label: 'Input Pelanggaran',
    path: '/verifikasi',
    badge: 'Form',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })
    ])
  },
  {
    key: 'statistik',
    label: 'Statistik Siswa',
    path: '/statistik',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' })
    ])
  },
  {
    key: 'piket',
    label: 'Piket Harian',
    path: '/piket',
    badge: 'Jaga',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
    ])
  },
  {
    key: 'panduan',
    label: 'Tata Tertib & Poin',
    path: '/panduan',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
    ])
  },
  {
    key: 'profil',
    label: 'Profil Guru BK',
    path: '/profil',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
    ])
  },
  {
    key: 'tamu',
    label: 'Portal Tamu (Siswa/Wali)',
    path: '/tamu',
    badge: 'Publik',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
    ])
  }
]

const isRouteActive = (key) => {
  if (props.activeTab === key) return true
  if (key === 'beranda' && (route.path === '/' || route.path === '/absensi')) return true
  if (key === 'absensi' && route.path === '/absensi') return true
  if (key === 'absensi_bulanan' && route.path === '/absensi/bulanan') return true
  if (key === 'pelanggaran' && (route.path === '/pelanggaran' || route.path.startsWith('/pelanggaran/'))) return true
  if (key === 'pemanggilan' && route.path === '/pemanggilan') return true
  if (key === 'verifikasi' && route.path === '/verifikasi') return true
  if (key === 'statistik' && route.path === '/statistik') return true
  if (key === 'piket' && route.path === '/piket') return true
  if (key === 'panduan' && route.path === '/panduan') return true
  if (key === 'profil' && route.path === '/profil') return true
  return false
}

const currentTitle = computed(() => {
  if (route.path === '/absensi') return 'Rekap Presensi Harian'
  if (route.path === '/absensi/bulanan') return 'Rekap Presensi Bulanan'
  if (route.path === '/pelanggaran') return 'Catatan Pelanggaran'
  if (route.path.startsWith('/pelanggaran/')) return 'Detail Pelanggaran'
  if (route.path === '/pemanggilan') return 'Pemanggilan Orang Tua'
  if (route.path === '/verifikasi') return 'Input Pelanggaran'
  if (route.path === '/statistik') return 'Statistik Siswa'
  if (route.path === '/piket') return 'Piket Harian'
  if (route.path === '/panduan') return 'Tata Tertib & Poin'
  if (route.path === '/profil') return 'Profil Guru BK'
  return 'Dashboard'
})

// Mobile Bottom Nav Definitions
const bottomNavTabs = [
  {
    key: 'beranda',
    path: '/absensi',
    label: 'Beranda',
    activeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { d: 'M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z' })
    ]),
    inactiveIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
    ])
  },
  {
    key: 'verifikasi',
    path: '/verifikasi',
    label: 'Verifikasi',
    activeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { 'fill-rule': 'evenodd', d: 'M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z', 'clip-rule': 'evenodd' })
    ]),
    inactiveIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
    ])
  },
  {
    key: 'statistik',
    path: '/statistik',
    label: 'Statistik',
    activeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { d: 'M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z' })
    ]),
    inactiveIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' })
    ])
  },
  {
    key: 'profil',
    path: '/profil',
    label: 'Profil BK',
    activeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { 'fill-rule': 'evenodd', d: 'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z', 'clip-rule': 'evenodd' })
    ]),
    inactiveIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
    ])
  }
]

const manualBottomTab = ref(null)

const activeBottomIndex = computed(() => {
  if (manualBottomTab.value !== null) return manualBottomTab.value
  if (isRouteActive('profil')) return 3
  if (isRouteActive('statistik')) return 2
  if (isRouteActive('verifikasi')) return 1
  return 0 // beranda / absensi
})

const handleBottomTabClick = (idx) => {
  manualBottomTab.value = idx
  setTimeout(() => {
    manualBottomTab.value = null
  }, 400)
}
</script>
