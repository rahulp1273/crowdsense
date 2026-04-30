<template>
  <div class="space-y-8 pb-12">
    <!-- Hero Section -->
    <HeroCard :suggested-place="userStore.places[0]" />

    <!-- Location Diagnostics (For Debugging) -->
    <div class="bg-indigo-600 rounded-[2rem] p-6 text-white shadow-xl shadow-indigo-100 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl animate-pulse">🛰️</div>
        <div>
          <h3 class="font-black text-lg tracking-tighter">Live GPS Status</h3>
          <p v-if="userStore.userLocation" class="text-xs font-medium text-indigo-100 uppercase tracking-widest">
            LAT: {{ userStore.userLocation.lat.toFixed(6) }} | LNG: {{ userStore.userLocation.lng.toFixed(6) }}
          </p>
          <p v-else class="text-xs font-medium text-indigo-100 uppercase tracking-widest">Waiting for satellites...</p>
        </div>
      </div>
      <button @click="userStore.detectLocation(true)" class="px-6 py-3 bg-white text-indigo-600 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-50 transition active:scale-95 shadow-lg">
        Refresh Live GPS
      </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <StatCard 
        label="Places Nearby" 
        :value="userStore.places.length" 
        icon="📍" 
        icon-bg="bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400"
        :trend="12"
      />
      <StatCard 
        label="Global Activity" 
        value="842" 
        icon="🌍" 
        icon-bg="bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"
        :trend="5"
      />
      <StatCard 
        label="Your Check-ins" 
        value="0" 
        icon="✅" 
        icon-bg="bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400"
      />
    </div>

    <!-- Suggested Places List -->
    <div class="space-y-6">
      <div class="flex justify-between items-end px-2">
        <div>
          <h2 class="text-3xl font-black text-gray-800 dark:text-white tracking-tighter">Live Crowd Feed</h2>
          <p class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">Real-time status of spots around you</p>
        </div>
        <router-link to="/places" class="text-xs font-black uppercase tracking-widest text-indigo-600 hover:underline">View All</router-link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <PlaceCard 
          v-for="place in userStore.places.slice(0, 4)" 
          :key="place.id" 
          :place="place" 
          @checkin="handleCheckIn"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useUserStore } from '../../stores/userStore';
import HeroCard from '../../components/user/dashboard/HeroCard.vue';
import StatCard from '../../components/user/dashboard/StatCard.vue';
import PlaceCard from '../../components/user/places/PlaceCard.vue';
import checkInService from '../../services/user/checkInService';

const userStore = useUserStore();

const handleCheckIn = async (id) => {
  try {
    await userStore.setCheckIn(id);
  } catch (err) {
    // Handled by global conflict modal or console
    console.error('Check-in error:', err);
  }
};

onMounted(() => {
  userStore.detectLocation().then(() => {
    userStore.fetchPlaces(true);
  });
});
</script>
