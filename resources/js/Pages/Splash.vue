<template>
  <div class="fixed inset-0 z-50 w-full h-full min-h-screen bg-slate-950 flex flex-col justify-between items-center text-center overflow-hidden select-none font-sans">
    
    <!-- School Building Background with Subtle Dark Vignette -->
    <div class="absolute inset-0 z-0 pointer-events-none">
      <img 
        src="/school-bg.jpg" 
        alt="Gedung SMK PGRI 5 JEMBER" 
        class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
        :class="{ 'scale-100': isLoaded }"
      />
      <!-- Dual layer vignette: preserves building colors while giving high contrast to text -->
      <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/55 to-slate-950/90"></div>
      <div class="absolute inset-0 bg-blue-950/20 mix-blend-color"></div>
    </div>

    <!-- Top Spacer for Optimal Vertical Balancing -->
    <div class="w-full h-6 sm:h-10 z-10"></div>

    <!-- Center Brand Section (Exact Match to User Reference) -->
    <div class="relative z-10 flex flex-col items-center justify-center px-6 max-w-sm mx-auto my-auto animate-fadeIn">
      
      <!-- School Logo in Pure White Circular Crest -->
      <div class="relative mb-5">
        <div class="absolute -inset-2 bg-blue-500/25 rounded-full blur-xl animate-pulse"></div>
        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white p-2.5 shadow-2xl shadow-black/50 border-[3px] border-white/95 flex items-center justify-center relative z-10 transition-transform duration-300 hover:scale-105">
          <img 
            src="/logo-smk.png" 
            alt="Logo SMK PGRI 5 Jember" 
            class="w-full h-full object-contain filter drop-shadow-sm" 
          />
        </div>
      </div>

      <!-- App Brand: [BK] Prima -->
      <div class="flex items-center justify-center gap-2 mb-2">
        <span class="bg-blue-600 text-white font-extrabold text-xs sm:text-sm px-2.5 py-1 rounded-md shadow-md shadow-blue-600/30 tracking-wider">
          BK
        </span>
        <span class="text-3xl sm:text-4xl font-black text-white tracking-tight drop-shadow-lg">
          Prima
        </span>
      </div>

      <!-- School Name -->
      <h2 class="text-white font-bold tracking-widest text-xs sm:text-sm uppercase drop-shadow-md mb-2">
        SMK PGRI 5 JEMBER
      </h2>

      <!-- Subtitle / Tagline -->
      <p class="text-slate-200/90 text-xs sm:text-sm text-center leading-relaxed max-w-[280px] sm:max-w-[300px] drop-shadow font-normal">
        Sistem Presensi Digital &amp; Manajemen<br />
        Kedisiplinan Konseling Sekolah
      </p>
    </div>

    <!-- Bottom Loading Section -->
    <div class="relative z-10 px-6 pb-8 sm:pb-10 w-full max-w-md mx-auto">
      
      <!-- Translucent Glass Loading Card -->
      <div class="bg-slate-950/85 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-slate-700/60 shadow-2xl shadow-black/60 mb-5">
        
        <!-- Status & Percentage Row -->
        <div class="flex justify-between items-center mb-2.5">
          <div class="flex items-center gap-2 text-slate-100 text-xs sm:text-sm font-medium tracking-wide">
            <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.9)] animate-pulse"></span>
            <span class="truncate max-w-[220px]">{{ statusMessage }}</span>
          </div>
          <span class="text-cyan-400 font-bold text-xs sm:text-sm tracking-wide tabular-nums">
            {{ Math.round(progress) }}%
          </span>
        </div>

        <!-- Progress Bar Track & Fill -->
        <div class="h-2 w-full bg-slate-900/90 rounded-full overflow-hidden p-0.5 border border-slate-700/50">
          <div 
            class="h-full rounded-full bg-gradient-to-r from-blue-600 via-cyan-400 to-teal-300 transition-all duration-150 ease-out shadow-[0_0_12px_rgba(34,211,238,0.7)]"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
        <!-- Fast Guest Portal Link -->
        <div class="mt-3 text-center">
          <router-link
            to="/tamu"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/70 hover:bg-slate-800 text-[11px] font-bold text-cyan-300 border border-cyan-500/30 transition shadow-sm cursor-pointer group"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-cyan-400 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Masuk Portal Tamu (Siswa / Wali Murid)</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-cyan-400 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </router-link>
        </div>
      </div>

      <!-- Footer Info (Exact match to screenshot) -->
      <div class="flex items-center justify-center gap-2 text-[11px] sm:text-xs text-slate-300/80 font-medium tracking-wider">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-cyan-400/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <span>SMK PGRI 05 JEMBER</span>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const progress = ref(0)
const isLoaded = ref(false)

const emit = defineEmits(['finish'])

const props = defineProps({
  isOverlay: {
    type: Boolean,
    default: false
  },
  targetRoute: {
    type: String,
    default: '/login'
  }
})

const statusMessage = computed(() => {
  if (progress.value < 20) return 'Menyiapkan modul aplikasi...'
  if (progress.value < 88) return 'Memuat modul sistem BK...'
  if (progress.value < 99) return 'Sinkronisasi data sistem...'
  return 'Selesai!'
})

const completeLoading = () => {
  if (props.isOverlay) {
    emit('finish')
  } else {
    router.push(props.targetRoute)
  }
}

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true
  }, 50)

  // Smooth realistic progress animation
  const interval = setInterval(() => {
    if (progress.value < 40) {
      progress.value += Math.random() * 8 + 4
    } else if (progress.value < 85) {
      progress.value += Math.random() * 6 + 3
    } else if (progress.value < 98) {
      progress.value += Math.random() * 4 + 1.5
    } else {
      progress.value = 100
      clearInterval(interval)
      setTimeout(() => {
        completeLoading()
      }, 400)
    }
  }, 100)
})
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.8s ease-out forwards;
}
</style>
