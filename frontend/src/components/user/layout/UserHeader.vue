<template>
  <header class="h-20 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40 border-b border-gray-100 dark:border-gray-800">
    <div class="flex items-center space-x-3">
      <!-- Menu Toggle -->
      <button 
        @click="$emit('toggle-sidebar')"
        class="p-3 bg-gray-50 dark:bg-gray-800 rounded-2xl text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all active:scale-90"
      >
        <span class="text-xl">☰</span>
      </button>

      <!-- Back Button -->
      <button 
        v-if="$route.path !== '/dashboard'"
        @click="$router.back()"
        class="w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all active:scale-90 group"
      >
        <span class="text-lg group-hover:-translate-x-1 transition-transform">←</span>
      </button>

      <div class="hidden sm:flex flex-col">
        <h1 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">{{ pageTitle }}</h1>
        <div v-if="userStore.loading.location" class="text-[8px] font-black text-indigo-500 animate-pulse mt-1 uppercase tracking-widest">
          Syncing...
        </div>
      </div>
    </div>

    <div class="flex items-center space-x-4">
      <div class="hidden md:block text-right">
        <div class="text-sm font-black text-gray-800 dark:text-white">{{ authStore.user?.name }}</div>
        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ authStore.user?.role }}</div>
      </div>
      <router-link to="/profile" class="w-10 h-10 bg-gradient-to-tr from-indigo-100 to-white dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center border border-gray-200 dark:border-gray-600 shadow-sm hover:scale-110 transition-transform cursor-pointer">
        <span class="text-lg">👤</span>
      </router-link>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';
import { useUserStore } from '../../../stores/userStore';

const authStore = useAuthStore();
const userStore = useUserStore();
const route = useRoute();

defineEmits(['toggle-sidebar']);

const pageTitle = computed(() => {
  const path = route.path;
  if (path === '/dashboard') return 'Overview';
  if (path.includes('/places')) return 'Venues';
  if (path.includes('/inquiries')) return 'Help Center';
  if (path === '/profile') return 'My Profile';
  return 'CrowdSense';
});
</script>
