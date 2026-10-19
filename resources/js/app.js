import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { RouterLink } from './inertia-compat.js'

const appName = import.meta.env.VITE_APP_NAME || 'BK Prima'

createInertiaApp({
  title: (title) => title ? `${title} - ${appName}` : appName,
  resolve: async (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    const importPage = pages[`./Pages/${name}.vue`]
    if (!importPage) {
      throw new Error(`Inertia page [${name}] not found.`)
    }
    const module = await importPage()
    return module.default || module
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .component('Link', Link)
      .component('Head', Head)
      .component('RouterLink', RouterLink)
      .component('router-link', RouterLink)

    app.config.globalProperties.$router = {
      push: (url) => {
        if (typeof url === 'string') {
          import('@inertiajs/vue3').then(({ router }) => router.visit(url))
        } else if (url && url.path) {
          const query = url.query ? '?' + new URLSearchParams(url.query).toString() : ''
          import('@inertiajs/vue3').then(({ router }) => router.visit(url.path + query))
        }
      },
      replace: (url) => {
        if (typeof url === 'string') {
          import('@inertiajs/vue3').then(({ router }) => router.replace(url))
        } else if (url && url.path) {
          const query = url.query ? '?' + new URLSearchParams(url.query).toString() : ''
          import('@inertiajs/vue3').then(({ router }) => router.replace(url.path + query))
        }
      },
      back: () => window.history.back(),
      forward: () => window.history.forward(),
    }

    app.mount(el)

    // Remove pre-rendered splash loading screen smoothly on mount
    const preloader = document.getElementById('initial-loading-screen')
    if (preloader) {
      preloader.style.opacity = '0'
      preloader.style.transition = 'opacity 0.4s ease-out'
      setTimeout(() => {
        if (preloader.parentNode) preloader.parentNode.removeChild(preloader)
      }, 400)
    }
  },
  progress: {
    delay: 50,
    color: '#2563eb',
    includeCSS: true,
    showSpinner: true,
  },
})
