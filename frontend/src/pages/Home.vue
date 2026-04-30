<template>
  <div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-white selection:bg-indigo-100 selection:text-indigo-700">
    
    <!-- Professional Frosted Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4 bg-white/70 dark:bg-gray-950/70 backdrop-blur-md border-b border-gray-100 dark:border-gray-800">
      <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-indigo-200 dark:shadow-none">C</div>
          <span class="font-black text-2xl tracking-tighter">Crowd<span class="text-indigo-600">Sense</span></span>
        </div>
        
        <div class="flex items-center space-x-6">
          <template v-if="!authStore.isAuthenticated">
            <router-link to="/login" class="text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors uppercase tracking-widest">Login</router-link>
            <router-link to="/register" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-600 dark:hover:bg-indigo-50 hover:text-white transition-all shadow-xl">Join Now</router-link>
          </template>
          <router-link v-else to="/dashboard" class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 dark:shadow-none">Dashboard</router-link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-48 pb-32 px-6">
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-64 h-64 bg-indigo-100/50 dark:bg-indigo-900/10 blur-[100px] rounded-full"></div>
        <div class="absolute top-40 right-10 w-64 h-64 bg-blue-100/50 dark:bg-blue-900/10 blur-[100px] rounded-full"></div>
      </div>

      <div class="max-w-4xl mx-auto text-center relative z-10 space-y-10">
        <div class="inline-flex items-center px-4 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-full text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-[0.2em]">
          Next-Gen Intelligence
        </div>
        
        <h1 class="text-6xl md:text-8xl font-black tracking-tighter leading-[0.9] text-gray-900 dark:text-white">
          Real-time insights for <br />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">smarter urban living.</span>
        </h1>
        
        <p class="text-xl text-gray-500 dark:text-gray-400 font-medium max-w-2xl mx-auto leading-relaxed">
          Experience world-class crowd monitoring and predictive analytics. Skip the queue, avoid the hectic rush, and reclaim your most valuable asset: time.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 pt-6">
          <router-link to="/register" class="w-full sm:w-auto bg-indigo-600 text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-100 dark:shadow-none">Get Started Free</router-link>
          <a href="#about" class="w-full sm:w-auto px-10 py-5 border border-gray-200 dark:border-gray-800 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-gray-50 dark:hover:bg-gray-900 transition-all">Learn More</a>
        </div>
      </div>
    </section>

    <!-- Value Propositions (Replacing the dots) -->
    <section id="about" class="py-32 px-6 bg-gray-50 dark:bg-gray-950/50 border-y border-gray-100 dark:border-gray-800">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
          <div class="space-y-6">
            <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white">Live Occupancy</h3>
            <p class="text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Get accurate, second-by-second updates on crowd density at your favorite cafes, gyms, and retail spots.</p>
          </div>
          
          <div class="space-y-6">
            <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3 class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white">Smart Predictions</h3>
            <p class="text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Plan your visit hours ahead with AI-driven forecasts that analyze historical patterns and event data.</p>
          </div>
          
          <div class="space-y-6">
            <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm border border-gray-100 dark:border-gray-700">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h3 class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white">Admin Control</h3>
            <h3 class="text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Business owners can manage capacity, handle inquiries, and track engagement through a unified dashboard.</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- Simple Modern CTA -->
    <section class="py-32 px-6 text-center space-y-8">
      <h2 class="text-4xl md:text-6xl font-black tracking-tighter text-gray-900 dark:text-white">Ready to join the movement?</h2>
      <p class="text-gray-500 font-medium max-w-xl mx-auto leading-relaxed">Join thousands of users who are already planning their city life smarter with CrowdSense.</p>
      <div class="pt-4">
        <router-link to="/register" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-sm hover:scale-105 transition-all shadow-2xl">Create Your Free Account</router-link>
      </div>
    </section>

    <!-- Minimalist Footer -->
    <footer class="py-12 px-6 border-t border-gray-100 dark:border-gray-800 text-center">
      <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex justify-center items-center space-x-2">
          <div class="w-6 h-6 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-[10px]">C</div>
          <span class="font-black text-lg tracking-tighter text-gray-900 dark:text-white">CrowdSense</span>
        </div>
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400">© 2026 SaaS Crowd Intelligence. Production Ready.</p>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
const authStore = useAuthStore();
</script>

<style scoped>
/* Smooth scroll for Learn More button */
html {
  scroll-behavior: smooth;
}
</style>
