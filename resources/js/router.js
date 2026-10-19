import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'splash',
    component: () => import('./Pages/Splash.vue'),
    meta: { title: 'BK Prima - Sistem Presensi Digital & Kedisiplinan' }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('./Pages/Auth/Login.vue'),
    meta: { title: 'Masuk Akun Konseling - BK Prima' }
  },
  {
    path: '/absensi',
    name: 'absensi',
    component: () => import('./Pages/AttendanceList.vue'),
    meta: { title: 'Rekap Presensi Harian - BK Prima' }
  },
  {
    path: '/verifikasi',
    name: 'verifikasi',
    component: () => import('./Pages/InputViolation.vue'),
    meta: { title: 'Form Input Pelanggaran - BK Prima' }
  },
  {
    path: '/statistik',
    name: 'statistik',
    component: () => import('./Pages/AttendanceStats.vue'),
    meta: { title: 'Statistik Kehadiran Siswa - BK Prima' }
  },
  {
    path: '/piket',
    name: 'piket',
    component: () => import('./Pages/Piket.vue'),
    meta: { title: 'Tugas & Penugasan Piket - BK Prima' }
  },
  {
    path: '/panduan',
    name: 'panduan',
    component: () => import('./Pages/RulePoints.vue'),
    meta: { title: 'Tata Tertib & Poin Panduan Sanksi - BK Prima' }
  },
  {
    path: '/profil',
    name: 'profil',
    component: () => import('./Pages/Profile.vue'),
    meta: { title: 'Edit Profil Guru BK - BK Prima' }
  },
  {
    path: '/pelanggaran',
    name: 'pelanggaran',
    component: () => import('./Pages/ViolationList.vue'),
    meta: { title: 'Catatan Pelanggaran Siswa - BK Prima' }
  },
  {
    path: '/pelanggaran/detail',
    name: 'pelanggaran-detail',
    component: () => import('./Pages/ViolationDetail.vue'),
    meta: { title: 'Detail Pelanggaran Siswa - BK Prima' }
  },
  {
    path: '/pemanggilan',
    redirect: '/pelanggaran?tab=panggilan'
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('./Pages/AdminDashboard.vue'),
    meta: { title: 'Panel Administrator - BK Prima' }
  },
  {
    path: '/tamu',
    name: 'tamu',
    component: () => import('./Pages/GuestPortal.vue'),
    meta: { title: 'Portal Siswa & Wali Murid - BK Prima' }
  },
  {
    path: '/portal-tamu',
    redirect: '/tamu'
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/absensi'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title
  }
  next()
})

export default router
