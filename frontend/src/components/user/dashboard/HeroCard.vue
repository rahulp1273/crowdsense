<template>
  <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600 to-blue-700 p-8 lg:p-12 text-white shadow-2xl shadow-indigo-200 dark:shadow-none group transition-all duration-500" :class="userStore.isCheckedIn ? 'from-green-600 to-teal-700' : ''">
    <!-- Abstract 3D Shapes -->
    <div class="absolute top-[-20%] right-[-10%] w-64 h-64 bg-white/10 blur-3xl rounded-full group-hover:scale-110 transition-transform duration-700"></div>
    <div class="absolute bottom-[-10%] left-[-5%] w-48 h-48 bg-indigo-400/20 blur-2xl rounded-full group-hover:translate-x-4 transition-transform duration-700"></div>

    <div class="relative z-10 max-w-2xl">
      <h1 v-if="!userStore.isCheckedIn" class="text-4xl lg:text-5xl font-black tracking-tighter leading-none mb-4">
        Where should you <br />
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-white">go today?</span>
      </h1>
      <h1 v-else class="text-4xl lg:text-5xl font-black tracking-tighter leading-none mb-4">
        You are <br />
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-200 to-white">Checked In!</span>
      </h1>
      
      <p class="text-indigo-100 font-medium mb-8 text-lg opacity-80">
        {{ userStore.isCheckedIn ? 'Your session is active. Stay safe and enjoy your time.' : 'Real-time crowd intelligence at your fingertips. Find the perfect spot, avoid the queue.' }}
      </p>
      
      <div v-if="userStore.isCheckedIn" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="flex flex-wrap gap-3">
          <div class="inline-flex items-center bg-white/10 backdrop-blur-md border border-white/20 px-4 py-3 rounded-2xl">
            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center text-sm mr-3">⏱</div>
            <div>
              <div class="text-[8px] font-black uppercase tracking-widest text-indigo-200">Duration</div>
              <div class="font-bold text-sm leading-tight">{{ userStore.sessionDuration }}</div>
            </div>
          </div>
          <div class="inline-flex items-center bg-white/10 backdrop-blur-md border border-white/20 px-4 py-3 rounded-2xl">
            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center text-sm mr-3">🟢</div>
            <div>
              <div class="text-[8px] font-black uppercase tracking-widest text-indigo-200">Since</div>
              <div class="font-bold text-sm leading-tight">{{ userStore.formattedCheckInTime }}</div>
            </div>
          </div>
        </div>
        <button @click="userStore.clearCheckIn" class="h-14 px-8 bg-white text-green-700 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-red-50 hover:text-red-600 transition-all shadow-xl active:scale-95 flex items-center justify-center cursor-pointer">
          Check Out Now
        </button>
      </div>

      <div v-else-if="suggestedPlace" class="inline-flex items-center bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-3xl hover:bg-white/20 transition-all cursor-pointer group/card">
        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-2xl shadow-lg mr-4 group-hover/card:rotate-12 transition-transform">📍</div>
        <div>
          <div class="text-[10px] font-black uppercase tracking-widest text-indigo-200">Recommended for you</div>
          <div class="font-bold text-lg leading-tight">{{ suggestedPlace.name }}</div>
          <div class="text-xs font-medium text-indigo-100 opacity-70">{{ suggestedPlace.distance ? (suggestedPlace.distance / 1000).toFixed(1) + 'km away' : 'Nearby' }}</div>
        </div>
      </div>
    </div>
    
    <div class="absolute right-8 bottom-0 hidden lg:block opacity-20">
      <span class="text-[15rem] font-black tracking-tighter leading-none select-none">{{ userStore.isCheckedIn ? 'IN' : 'OS' }}</span>
    </div>
  </div>
</template>

<script setup>
import { useUserStore } from '../../../stores/userStore';
const userStore = useUserStore();

defineProps({
  suggestedPlace: Object
});
</script>
