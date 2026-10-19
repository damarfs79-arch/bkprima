<template>
  <div class="relative min-h-screen bg-slate-50">
    <router-view />

    <transition name="fade-splash">
      <Splash 
        v-if="showInitialSplash" 
        :is-overlay="true" 
        @finish="handleSplashFinish" 
      />
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import Splash from './Pages/Splash.vue'

const route = useRoute()
const showInitialSplash = ref(false)

onMounted(() => {
  const hasLoaded = sessionStorage.getItem('bk_app_initialized')
  if (!hasLoaded && route.path !== '/') {
    showInitialSplash.value = true
  }
})

const handleSplashFinish = () => {
  sessionStorage.setItem('bk_app_initialized', 'true')
  showInitialSplash.value = false
}
</script>

<style>
.fade-splash-leave-active {
  transition: opacity 0.5s ease-out, transform 0.5s ease-out;
}
.fade-splash-leave-to {
  opacity: 0;
  transform: scale(1.02);
}
</style>
