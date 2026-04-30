<template>
  <aside class="w-64 bg-white dark:bg-gray-800 shadow-xl h-screen sticky top-0 flex flex-col transition-all duration-300">
    <div class="h-16 flex items-center px-6 border-b dark:border-gray-700">
      <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">CrowdSense<span class="text-gray-400 text-sm ml-1 font-medium uppercase tracking-widest">OS</span></span>
    </div>
    
    <nav class="flex-1 px-4 py-6 space-y-2">
      <router-link to="/admin/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all" :class="$route.path === '/admin/dashboard' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
        <span class="text-xl">📊</span>
        <span>Dashboard</span>
      </router-link>
      
      <router-link to="/admin/places" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all" :class="$route.path.includes('/admin/places') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
        <span class="text-xl">📍</span>
        <span>Places</span>
      </router-link>

      <router-link to="/admin/inquiries" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all" :class="$route.path.includes('/admin/inquiries') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
        <span class="text-xl">📩</span>
        <span>Inquiries</span>
      </router-link>

      <router-link v-if="authStore.hasPermission('manage_admins')" to="/admin/admins" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all" :class="$route.path.includes('/admin/admins') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
        <span class="text-xl">🛡️</span>
        <span>Admins</span>
      </router-link>

      <router-link to="/admin/analytics" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all" :class="$route.path.includes('/admin/analytics') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-bold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
        <span class="text-xl">📈</span>
        <span>Analytics</span>
      </router-link>
    </nav>
    
    <div class="p-4 border-t dark:border-gray-700">
      <button @click="logout" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-lg hover:bg-red-100 transition-colors font-medium cursor-pointer">
        <span>Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useAuthStore } from '../../../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
