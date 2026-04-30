<template>
  <nav class="bg-white dark:bg-gray-800 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/" class="flex-shrink-0 flex items-center">
            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">CrowdSense</span>
          </router-link>
        </div>
        <div class="flex items-center space-x-4">
          <template v-if="authStore.isAuthenticated">
            <router-link :to="authStore.isAdmin ? '/admin/dashboard' : '/dashboard'" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 font-medium">Dashboard</router-link>
            <button @click="logout" class="text-red-500 hover:text-red-700 font-medium cursor-pointer">Logout</button>
          </template>
          <template v-else>
            <router-link to="/login" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 font-medium">Login</router-link>
            <router-link to="/register" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-medium">Sign up</router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
