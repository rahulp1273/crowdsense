<template>
  <header class="h-20 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40 border-b border-gray-100 dark:border-gray-800">
    <div class="flex items-center space-x-3">
      <!-- Menu Toggle (Mobile) -->
      <button 
        @click="$emit('toggle-sidebar')"
        class="p-3 bg-gray-50 dark:bg-gray-800 rounded-2xl text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all active:scale-90 lg:hidden"
      >
        <span class="text-xl">☰</span>
      </button>

      <div class="flex flex-col">
        <h1 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">{{ routeName }}</h1>
        <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">
          Management Console
        </div>
      </div>
    </div>

    <div class="flex items-center space-x-4">
      <div class="hidden md:block text-right">
        <div class="text-sm font-black text-gray-800 dark:text-white">{{ authStore.user?.name }}</div>
        <div class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Administrator</div>
      </div>
      <div class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-indigo-400 rounded-2xl flex items-center justify-center text-white font-black shadow-lg shadow-indigo-200 dark:shadow-none">
        {{ authStore.user?.name.charAt(0) }}
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';

const route = useRoute();
const authStore = useAuthStore();

defineEmits(['toggle-sidebar']);

const routeName = computed(() => {
  const path = route.path;
  if (path === '/admin/dashboard') return 'Dashboard Overview';
  if (path.includes('/admin/places')) return 'Place Management';
  if (path.includes('/admin/inquiries')) return 'Inquiry Desk';
  if (path.includes('/admin/admins')) return 'Staff Access';
  if (path.includes('/admin/analytics')) return 'Data Analytics';
  return 'Admin Panel';
});
</script>
