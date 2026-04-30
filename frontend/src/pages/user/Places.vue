<template>
  <div class="space-y-8 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between px-2 space-y-4 md:space-y-0">
      <div>
        <h2 class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">Explore Spots</h2>
        <p class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">Found {{ userStore.places.length }} locations near you</p>
      </div>
      
      <div class="flex space-x-3">
        <div class="relative">
          <input type="text" placeholder="Search spots..." class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 px-6 py-3 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none w-64 shadow-lg shadow-gray-100 dark:shadow-none transition-all" />
          <span class="absolute right-4 top-3.5 opacity-40">🔍</span>
        </div>
      </div>
    </div>

    <div v-if="userStore.loading.places && userStore.places.length === 0" class="flex flex-col items-center justify-center py-20 space-y-4">
      <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
      <p class="text-xs font-black uppercase tracking-widest text-gray-400">Loading intelligence...</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <PlaceCard 
        v-for="place in userStore.places" 
        :key="place.id" 
        :place="place" 
        @checkin="handleCheckIn"
      />
    </div>

    <!-- Empty State -->
    <div v-if="!userStore.loading.places && userStore.places.length === 0" class="text-center py-20 bg-white dark:bg-gray-800 rounded-[2rem] border border-dashed border-gray-200 dark:border-gray-700">
      <div class="text-4xl mb-4">🏜️</div>
      <h3 class="font-bold text-xl text-gray-800 dark:text-white">No spots found</h3>
      <p class="text-gray-500 text-sm mt-1">Try expanding your GPS search radius.</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useUserStore } from '../../stores/userStore';
import PlaceCard from '../../components/user/places/PlaceCard.vue';
import checkInService from '../../services/user/checkInService';

const userStore = useUserStore();

const handleCheckIn = async (id) => {
  try {
    await userStore.setCheckIn(id);
    alert('Checked in successfully!');
  } catch (err) {
    if (err.response?.status !== 400 || !err.response.data.active_place) {
      alert(err.message || 'Check-in failed');
    }
  }
};

onMounted(() => {
  if (userStore.places.length === 0) {
    userStore.detectLocation().then(() => {
      userStore.fetchPlaces(true);
    });
  }
});
</script>
