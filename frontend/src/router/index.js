import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import ForgotPassword from '../pages/ForgotPassword.vue';

// User Pages
import UserLayout from '../components/user/layout/UserLayout.vue';
import UserDashboard from '../pages/user/Dashboard.vue';
import UserPlaces from '../pages/user/Places.vue';
import UserPlaceDetails from '../pages/user/PlaceDetails.vue';
import UserInquiries from '../pages/user/Inquiries.vue';
import UserProfile from '../pages/user/Profile.vue';

// Admin Pages
import AdminLayout from '../components/admin/layout/AdminLayout.vue';
import AdminDashboard from '../pages/admin/Dashboard.vue';
import AdminPlaces from '../pages/admin/Places.vue';
import AdminInquiries from '../pages/admin/Inquiries.vue';
import AdminAnalytics from '../pages/admin/Analytics.vue';

import { useAuthStore } from '../stores/auth';

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login, meta: { guest: true } },
  { path: '/register', component: Register, meta: { guest: true } },
  { path: '/forgot-password', component: ForgotPassword, meta: { guest: true } },
  
  // User Routes (Shared Layout)
  {
    path: '/',
    component: UserLayout,
    meta: { requiresAuth: true },
    children: [
      { path: 'dashboard', component: UserDashboard },
      { path: 'places', component: UserPlaces },
      { path: 'places/:id', component: UserPlaceDetails },
      { path: 'inquiries', component: UserInquiries },
      { path: 'profile', component: UserProfile },
    ]
  },
  
  // Admin Routes
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      { path: 'dashboard', component: AdminDashboard },
      { path: 'places', component: AdminPlaces },
      { path: 'inquiries', component: AdminInquiries },
      { path: 'admins', component: () => import('../pages/admin/Admins.vue') },
      { path: 'analytics', component: AdminAnalytics },
    ]
  },

  // Fallback
  { path: '/:pathMatch(.*)*', redirect: '/dashboard' }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const auth = useAuthStore();
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login');
  } else if (to.meta.guest && auth.isAuthenticated) {
    next(auth.isAdmin ? '/admin/dashboard' : '/dashboard');
  } else if (to.meta.requiresAdmin && !auth.isAdmin) {
    next('/dashboard');
  } else {
    next();
  }
});

export default router;
