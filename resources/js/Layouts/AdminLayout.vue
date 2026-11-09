<template>
  <div class="min-h-screen bg-slate-100 flex flex-col lg:flex-row font-sans text-slate-800">
    
    <!-- ============================================================ -->
    <!-- 1. DESKTOP ADMIN SIDEBAR                                     -->
    <!-- ============================================================ -->
    <aside class="hidden lg:flex w-64 xl:w-72 flex-col bg-slate-900 border-r border-slate-800 min-h-screen sticky top-0 h-screen shrink-0 z-30 select-none">
      
      <!-- Brand Logo Header (Foto 1) -->
      <div class="p-4 sm:p-5 border-b border-slate-800">
        <router-link to="/admin" class="flex items-center gap-3 group">
          <img
            src="/logo-bkprima-dark.png"
            alt="BK Prima - Sistem Informasi &amp; Konseling SMK PGRI 5 Jember"
            class="h-10 sm:h-11 w-auto max-w-full object-contain transition-transform duration-200 group-hover:scale-102"
          />
        </router-link>
      </div>

      <!-- Admin Profile in Sidebar (Natural Integrated Section) -->
      <div class="px-5 py-4 border-b border-slate-800 flex items-center gap-3 bg-slate-900">
        <div class="relative shrink-0">
          <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-xs font-bold text-white truncate leading-tight">Admin IT Sekolah</h4>
          <p class="text-[10px] text-slate-400 truncate mt-0.5">Super Administrator</p>
        </div>
        <span class="text-[11px] text-slate-400 font-mono">
          root
        </span>
      </div>

      <!-- Admin Navigation Menu -->
      <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
        <p class="px-3 text-xs font-semibold text-slate-400 mb-2">Panel Kontrol</p>
        
        <button
          v-for="item in adminMenuItems"
          :key="item.key"
          @click="$emit('select-tab', item.key)"
          class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium transition text-left cursor-pointer group"
          :class="activeTab === item.key ? 'bg-slate-800 text-white font-semibold border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'"
        >
          <div
            class="transition"
            :class="activeTab === item.key ? 'text-blue-400' : 'text-slate-400 group-hover:text-slate-300'"
          >
            <component :is="item.icon" class="w-4 h-4" />
          </div>
          <span class="flex-1">{{ item.label }}</span>
          <span v-if="item.badge" class="text-[11px] font-semibold text-rose-400">
            {{ item.badge }}
          </span>
        </button>

        <div class="pt-4 mt-4 border-t border-slate-800">
          <p class="px-3 text-xs font-semibold text-slate-400 mb-2">Akses Cepat</p>
          <router-link
            to="/absensi"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-slate-400 hover:bg-slate-800/60 hover:text-slate-200 transition group"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 group-hover:text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Pratinjau Portal Guru BK</span>
          </router-link>
        </div>
      </nav>

      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-slate-800 space-y-3">
        <div class="px-1 text-xs font-medium text-slate-400">
          <span class="truncate">Server Online &bull; DB Terhubung</span>
        </div>
        <button
          type="button"
          @click="handleLogout"
          class="flex items-center justify-center gap-2 w-full py-2 px-3 rounded-lg text-xs font-semibold text-rose-300 hover:text-white bg-slate-800 hover:bg-rose-700 border border-slate-700 transition cursor-pointer"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Keluar Administrator</span>
        </button>
      </div>

    </aside>

    <!-- ============================================================ -->
    <!-- 2. MOBILE ADMIN TOP BAR                                      -->
    <!-- ============================================================ -->
    <div class="lg:hidden bg-slate-900 text-white border-b border-slate-800 px-4 py-3 sticky top-0 z-30 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <button
          @click="mobileDrawerOpen = true"
          class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-slate-700 flex flex-col items-center justify-center gap-1 cursor-pointer"
        >
          <span class="w-4 h-[2px] bg-white rounded-full"></span>
          <span class="w-4 h-[2px] bg-white rounded-full"></span>
          <span class="w-4 h-[2px] bg-white rounded-full"></span>
        </button>
        <router-link to="/admin" class="flex items-center">
          <img src="/logo-bkprima-dark.png" alt="BK Prima SMK PGRI 5 Jember" class="h-8 w-auto object-contain" />
        </router-link>
      </div>
      <button type="button" @click="handleLogout" class="text-xs font-bold text-rose-400 hover:text-rose-300 cursor-pointer">
        Keluar
      </button>
    </div>

    <!-- Mobile Drawer -->
    <div v-if="mobileDrawerOpen" class="fixed inset-0 z-50 lg:hidden flex">
      <div @click="mobileDrawerOpen = false" class="fixed inset-0 bg-slate-900/60"></div>
      <div class="relative w-72 max-w-[85vw] bg-slate-900 border-r border-slate-800 text-white h-full flex flex-col z-50 p-5 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <span class="text-sm font-black">Panel Administrator</span>
          <button @click="mobileDrawerOpen = false" class="p-1 text-slate-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <nav class="space-y-1 flex-1 overflow-y-auto">
          <button
            v-for="item in adminMenuItems"
            :key="item.key"
            @click="$emit('select-tab', item.key); mobileDrawerOpen = false"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-left transition"
            :class="activeTab === item.key ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800'"
          >
            <component :is="item.icon" class="w-4 h-4" />
            <span>{{ item.label }}</span>
          </button>
        </nav>
        <button
          type="button"
          @click="mobileDrawerOpen = false; handleLogout()"
          class="w-full py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs text-center cursor-pointer transition"
        >
          Keluar Administrator
        </button>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- 3. MAIN CONTENT WRAPPER                                      -->
    <!-- ============================================================ -->
    <div class="flex-1 flex flex-col min-w-0">
      
      <!-- Desktop Top Bar -->
      <header class="hidden lg:flex items-center justify-between px-8 py-3.5 bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
          <span class="text-slate-400">Administrator</span>
          <span class="text-slate-300">/</span>
          <span class="text-blue-700 font-semibold">Panel Kontrol BK Prima</span>
        </div>

        <div class="flex items-center gap-3">
          <!-- Notification Bell Button for Consultation Messages -->
          <button
            @click="$emit('select-tab', 'konsultasi')"
            class="relative px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 transition cursor-pointer flex items-center gap-2"
            title="Lihat Pesan Konsultasi Wali Murid"
          >
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
            <span class="text-xs font-medium text-slate-700">Pesan Masuk</span>
            <span
              v-if="unreadCount > 0"
              class="text-xs font-semibold text-rose-600"
            >
              ({{ unreadCount }})
            </span>
          </button>

          <div class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-md text-xs text-slate-600 font-medium">
            <span>SIM-Pegawai &bull; Mode Kelola Penuh</span>
          </div>
        </div>
      </header>

      <!-- Main Content Slot -->
      <main class="flex-1 p-3 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full pb-20 lg:pb-10">
        <slot />
      </main>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL CARD: KONFIRMASI KELUAR ADMINISTRATOR                  -->
    <!-- ============================================================ -->
    <div
      v-if="showLogoutModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 animate-in fade-in duration-150"
      @click.self="showLogoutModal = false"
    >
      <div class="bg-white rounded-xl max-w-sm sm:max-w-md w-full p-6 sm:p-7 shadow-xl border border-slate-200 text-center animate-in zoom-in-95 duration-150">
        
        <!-- Icon Logout -->
        <div class="w-12 h-12 mx-auto rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </div>

        <!-- Eyebrow & Title -->
        <div class="text-xs font-medium text-slate-500 mb-1">
          Konfirmasi Akses Administrator
        </div>
        <h3 class="text-base sm:text-lg font-bold text-slate-900">
          Keluar dari Panel Administrator?
        </h3>

        <!-- Description -->
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
          Sesi aktif Anda sebagai administrator akan diakhiri. Anda perlu memasukkan kredensial akun kembali untuk dapat mengakses panel ini.
        </p>

        <!-- Session Info Card -->
        <div class="mt-4 p-3 bg-slate-50 rounded-lg border border-slate-200 text-left text-xs">
          <div class="flex items-center justify-between">
            <span class="font-bold text-slate-800">Admin IT Sekolah</span>
            <span class="text-xs font-medium text-slate-500">Super Administrator</span>
          </div>
          <p class="text-[11px] text-slate-400 mt-0.5">SMK PGRI 05 Jember &bull; Sesi Manajemen Penuh</p>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-2.5 mt-6 pt-2">
          <button
            type="button"
            @click="showLogoutModal = false"
            class="w-full py-2.5 px-4 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
          >
            Batal
          </button>

          <button
            type="button"
            @click="confirmLogout"
            class="w-full py-2.5 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition cursor-pointer flex items-center justify-center gap-1.5"
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
import { router as inertiaRouter } from '@inertiajs/vue3'

const props = defineProps({
  activeTab: {
    type: String,
    default: 'dashboard'
  },
  unreadCount: {
    type: Number,
    default: 0
  }
})

defineEmits(['select-tab'])

const mobileDrawerOpen = ref(false)
const showLogoutModal = ref(false)

const handleLogout = () => {
  showLogoutModal.value = true
}

const confirmLogout = () => {
  showLogoutModal.value = false
  inertiaRouter.post('/logout')
}

const adminMenuItems = computed(() => [
  {
    key: 'dashboard',
    label: 'Dashboard & Tren 30 Hari',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '1.75' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' })
    ])
  },
  {
    key: 'konsultasi',
    label: 'Pesan Konsultasi',
    badge: props.unreadCount > 0 ? `${props.unreadCount} Baru` : null,
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z' })
    ])
  },
  {
    key: 'guru',
    label: 'Kelola Guru BK',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })
    ])
  },
  {
    key: 'kelas',
    label: 'Kelola Kelas / Rombel',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
    ])
  },
  {
    key: 'siswa',
    label: 'Kelola Data Siswa',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
    ])
  }
])
</script>
