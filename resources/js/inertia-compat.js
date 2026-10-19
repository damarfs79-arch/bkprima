import { h, computed, reactive, defineComponent } from 'vue'
import { router as inertiaRouter, usePage, Link } from '@inertiajs/vue3'

export const useRouter = () => {
  return {
    push: (target) => {
      if (typeof target === 'string') {
        inertiaRouter.visit(target)
      } else if (target && target.path) {
        const queryStr = target.query ? '?' + new URLSearchParams(target.query).toString() : ''
        inertiaRouter.visit(target.path + queryStr)
      }
    },
    replace: (target) => {
      if (typeof target === 'string') {
        inertiaRouter.replace(target)
      } else if (target && target.path) {
        const queryStr = target.query ? '?' + new URLSearchParams(target.query).toString() : ''
        inertiaRouter.replace(target.path + queryStr)
      }
    },
    back: () => window.history.back(),
    forward: () => window.history.forward(),
    go: (delta) => window.history.go(delta)
  }
}

export const useRoute = () => {
  const page = usePage()
  
  return reactive({
    path: computed(() => {
      try {
        return new URL(page.url, window.location.origin).pathname
      } catch (e) {
        return page.url || '/'
      }
    }),
    fullPath: computed(() => page.url || '/'),
    query: computed(() => {
      try {
        const urlObj = new URL(page.url, window.location.origin)
        return Object.fromEntries(urlObj.searchParams.entries())
      } catch (e) {
        return {}
      }
    }),
    params: computed(() => page.props?.params || {}),
    meta: computed(() => page.props?.meta || {})
  })
}

// RouterLink component wrapper for Inertia Link
export const RouterLink = defineComponent({
  name: 'RouterLink',
  props: {
    to: {
      type: [String, Object],
      required: false
    },
    href: {
      type: String,
      required: false
    }
  },
  setup(props, { slots, attrs }) {
    const computedHref = computed(() => {
      if (props.href) return props.href
      if (typeof props.to === 'string') return props.to
      if (props.to && props.to.path) {
        const query = props.to.query ? '?' + new URLSearchParams(props.to.query).toString() : ''
        return props.to.path + query
      }
      return '/'
    })

    return () => h(Link, { ...attrs, href: computedHref.value }, slots.default ? slots.default() : [])
  }
})

export const createRouter = () => ({
  beforeEach: () => {},
  afterEach: () => {},
  push: (target) => {
    const url = typeof target === 'string' ? target : target.path
    inertiaRouter.visit(url)
  }
})

export const createWebHistory = () => ({})

export default {
  useRouter,
  useRoute,
  RouterLink,
  createRouter,
  createWebHistory
}
