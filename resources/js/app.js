import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { RouterLink } from './inertia-compat.js'

const appName = import.meta.env.VITE_APP_NAME || 'BK Prima'

createInertiaApp({
  title: (title) => title ? `${title} - ${appName}` : `${appName} - SMK PGRI 05 Jember`,
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
  },
  progress: false,
})
