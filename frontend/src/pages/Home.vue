<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex flex-col">
    <!-- Simple Transparent Header for Home -->
    <header class="p-8 flex justify-between items-center max-w-7xl mx-auto w-full">
      <div class="flex items-center">
        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black">C</div>
        <span class="ml-3 font-black text-xl tracking-tighter dark:text-white">Crowd<span class="text-indigo-600">Sense</span></span>
      </div>
      <div class="space-x-4">
        <router-link v-if="!authStore.isAuthenticated" to="/login" class="text-sm font-bold text-gray-500 hover:text-gray-800">Login</router-link>
        <router-link v-if="!authStore.isAuthenticated" to="/register" class="bg-gray-900 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-gray-800 transition-all">Join Now</router-link>
        <router-link v-else to="/dashboard" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-indigo-700 transition-all">Go to Dashboard</router-link>
      </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center px-4 py-20 text-center max-w-5xl mx-auto">
      <div class="relative">
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-indigo-500/10 blur-[100px] rounded-full"></div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-blue-500/10 blur-[100px] rounded-full"></div>
        
        <h1 class="text-6xl md:text-8xl font-black text-gray-900 dark:text-white tracking-tighter leading-none mb-8">
          Know before <br />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">you go.</span>
        </h1>
      </div>

      <p class="text-xl text-gray-500 dark:text-gray-400 mb-12 max-w-2xl font-medium leading-relaxed">
        Real-time crowd intelligence for your favorite spots. Skip the queue, avoid the crowd, and reclaim your time.
      </p>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
        <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-100 dark:border-gray-800">
          <div class="text-4xl mb-4">🟢</div>
          <h3 class="text-xl font-black mb-2 dark:text-white">Relaxed</h3>
          <p class="text-sm text-gray-500">Perfect time to visit. Plenty of space available.</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-100 dark:border-gray-800">
          <div class="text-4xl mb-4">🟡</div>
          <h3 class="text-xl font-black mb-2 dark:text-white">Steady</h3>
          <p class="text-sm text-gray-500">Getting busy. Minor wait times expected.</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-100 dark:border-gray-800">
          <div class="text-4xl mb-4">🔴</div>
          <h3 class="text-xl font-black mb-2 dark:text-white">Hectic</h3>
          <p class="text-sm text-gray-500">Very busy. Consider visiting later if possible.</p>
        </div>
      </div>
    </main>

    <footer class="p-8 text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
      © 2026 CrowdSense SaaS Intelligence
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
const authStore = useAuthStore();
</script>
