<template>
  <header class="h-16 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-sm flex items-center justify-between px-8 sticky top-0 z-40">
    <div class="flex items-center">
      <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100 capitalize">{{ routeName }}</h1>
    </div>
    <div class="flex items-center space-x-4">
      <div class="text-sm text-gray-500 font-medium">Admin: {{ authStore.user?.name }}</div>
      <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
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

const routeName = computed(() => {
  const path = route.path;
  if (path === '/admin/dashboard') return 'Dashboard Overview';
  if (path === '/admin/places') return 'Place Management';
  if (path === '/admin/inquiries') return 'Inquiry Desk';
  return 'Admin Panel';
});
</script>
