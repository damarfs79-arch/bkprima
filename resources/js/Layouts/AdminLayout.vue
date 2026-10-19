<template>
  <div class="min-h-screen bg-slate-100 flex flex-col lg:flex-row font-sans text-slate-800">
    
    <!-- ============================================================ -->
    <!-- 1. DESKTOP ADMIN SIDEBAR                                     -->
    <!-- ============================================================ -->
    <aside class="hidden lg:flex w-64 xl:w-72 flex-col bg-slate-900 border-r border-slate-800 min-h-screen sticky top-0 h-screen shrink-0 z-30 select-none shadow-xl">
      
      <!-- Brand Logo Header -->
      <div class="p-5 border-b border-slate-800 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center p-1.5 shadow-2xs">
          <img src="/logo-smk.png" alt="Logo SMK" class="w-full h-full object-contain filter brightness-110" />
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="bg-indigo-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-md leading-none tracking-wider">ADMIN</span>
            <span class="text-xl font-black text-white tracking-tight">Prima</span>
          </div>
          <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">SMK PGRI 05 JEMBER</p>
        </div>
      </div>

      <!-- Admin Profile Card in Sidebar -->
      <div class="p-4 mx-3 my-3 bg-slate-800/80 rounded-2xl border border-slate-700/80 flex items-center gap-3 shadow-inner">
        <div class="relative shrink-0">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 text-white flex items-center justify-center font-black shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full"></div>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-xs font-bold text-white truncate leading-tight">Admin IT Sekolah</h4>
          <p class="text-[10px] font-semibold text-indigo-400 truncate mt-0.5">Super Administrator</p>
        </div>
        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
          Root
        </span>
      </div>

      <!-- Admin Navigation Menu -->
      <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Panel Kontrol</p>
        
        <button
          v-for="item in adminMenuItems"
          :key="item.key"
          @click="$emit('select-tab', item.key)"
          class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition text-left cursor-pointer group"
          :class="activeTab === item.key ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white'"
        >
          <div
            class="p-1 rounded-lg transition"
            :class="activeTab === item.key ? 'bg-white/20 text-white' : 'text-slate-500 group-hover:text-indigo-400'"
          >
            <component :is="item.icon" class="w-4 h-4" />
          </div>
          <span class="flex-1">{{ item.label }}</span>
          <span v-if="item.badge" class="px-2 py-0.5 text-[10px] font-black rounded-full" :class="activeTab === item.key ? 'bg-white text-indigo-700' : 'bg-slate-800 text-slate-400'">
            {{ item.badge }}
          </span>
        </button>

        <div class="pt-4 mt-4 border-t border-slate-800">
          <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Akses Cepat</p>
          <router-link
            to="/absensi"
            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-slate-800 hover:text-white transition group"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 group-hover:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Pratinjau Portal Guru BK</span>
          </router-link>
        </div>
      </nav>

      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-slate-800 space-y-3">
        <div class="flex items-center gap-2 px-3 py-2 bg-slate-800/80 border border-slate-700/60 rounded-xl text-xs font-medium text-slate-300">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="truncate">Server Online &bull; DB Terhubung</span>
        </div>
        <button
          type="button"
          @click="handleLogout"
          class="flex items-center justify-center gap-2 w-full py-2.5 px-3 rounded-xl text-xs font-bold text-rose-400 bg-rose-950/40 hover:bg-rose-900/50 border border-rose-800/40 transition cursor-pointer"
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
    <div class="lg:hidden bg-slate-900 text-white border-b border-slate-800 px-4 py-3 sticky top-0 z-30 flex items-center justify-between shadow-md">
      <div class="flex items-center gap-2.5">
        <button
          @click="mobileDrawerOpen = true"
          class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 flex flex-col items-center justify-center gap-1 cursor-pointer"
        >
          <span class="w-4 h-[2px] bg-white rounded-full"></span>
          <span class="w-4 h-[2px] bg-white rounded-full"></span>
          <span class="w-4 h-[2px] bg-white rounded-full"></span>
        </button>
        <div class="flex items-center gap-1.5">
          <span class="bg-indigo-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded leading-none">ADMIN</span>
          <span class="text-base font-black text-white tracking-tight">Prima</span>
        </div>
      </div>
      <router-link to="/login" class="text-xs font-bold text-rose-400 hover:text-rose-300">
        Keluar
      </router-link>
    </div>

    <!-- Mobile Drawer -->
    <div v-if="mobileDrawerOpen" class="fixed inset-0 z-50 lg:hidden flex">
      <div @click="mobileDrawerOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-xs"></div>
      <div class="relative w-72 max-w-[85vw] bg-slate-900 text-white h-full flex flex-col z-50 shadow-2xl p-5 space-y-4">
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
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-left transition"
            :class="activeTab === item.key ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800'"
          >
            <component :is="item.icon" class="w-4 h-4" />
            <span>{{ item.label }}</span>
          </button>
        </nav>
        <button
          type="button"
          @click="mobileDrawerOpen = false; handleLogout()"
          class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs text-center cursor-pointer transition"
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
      <header class="hidden lg:flex items-center justify-between px-8 py-4 bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-20">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
          <span class="text-indigo-600 font-extrabold uppercase tracking-wider text-[11px]">ADMINISTRATOR</span>
          <span class="text-slate-300">/</span>
          <span class="text-slate-800 font-bold uppercase tracking-wider text-[11px]">SISTEM PENDIDIKAN BK PRIMA</span>
        </div>

        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-600 font-medium">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>SIM-Pegawai &bull; Mode Kelola Penuh</span>
          </div>
        </div>
      </header>

      <!-- Main Content Slot -->
      <main class="flex-1 p-3 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full pb-20 lg:pb-10">
        <slot />
      </main>
    </div>

  </div>
</template>

<script setup>
import { ref, h } from 'vue'
import { router as inertiaRouter } from '@inertiajs/vue3'

defineProps({
  activeTab: {
    type: String,
    default: 'dashboard'
  }
})

defineEmits(['select-tab'])

const mobileDrawerOpen = ref(false)

const handleLogout = () => {
  if (confirm('Apakah Anda yakin ingin keluar dari panel administrator?')) {
    inertiaRouter.post('/logout')
  }
}

const adminMenuItems = [
  {
    key: 'dashboard',
    label: 'Dashboard & Tren 30 Hari',
    badge: 'Grafik',
    icon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' })
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
]
</script>
