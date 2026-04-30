<template>
  <div v-if="loading" class="flex justify-center py-20">
    <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
  </div>

  <div v-else-if="place" class="space-y-8 pb-20">
    <!-- Active Session Banner for Details Page -->
    <div v-if="userStore.activeCheckIn?.place_id === place.id" class="bg-green-600 text-white p-6 rounded-[2.5rem] flex items-center justify-between shadow-xl shadow-green-100 dark:shadow-none animate-pulse">
      <div class="flex items-center space-x-4">
        <div class="text-3xl">🎯</div>
        <div>
          <div class="font-black uppercase tracking-widest text-[10px] text-green-100">Active Session</div>
          <div class="text-xl font-bold">Checked in here for {{ userStore.sessionDuration }}</div>
        </div>
      </div>
      <button @click="handleCheckOut" class="bg-white text-green-700 px-6 py-2 rounded-2xl font-black uppercase text-xs hover:bg-green-50 transition-all">Check Out</button>
    </div>

    <div class="relative overflow-hidden rounded-[3rem] bg-white dark:bg-gray-800 p-8 lg:p-12 shadow-2xl border border-gray-50 dark:border-gray-700 group">
      <div class="absolute top-0 right-0 w-96 h-96 blur-[120px] opacity-10 bg-indigo-500 rounded-full"></div>
      
      <div class="relative z-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
          <div>
            <router-link to="/places" class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 mb-4 inline-block hover:underline">← Back to Explore</router-link>
            <h1 class="text-5xl lg:text-7xl font-black text-gray-800 dark:text-white tracking-tighter leading-none">{{ place.name }}</h1>
            <p class="text-xs font-black uppercase tracking-[0.3em] text-gray-400 mt-4">{{ place.type }} • {{ place.location }}</p>
          </div>
          <div class="mt-8 md:mt-0">
            <CrowdBadge :count="place.current_crowd_count" class="scale-150 origin-right" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
          <div class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Live Status</div>
            <div class="text-4xl font-black text-gray-800 dark:text-white">{{ place.current_crowd_count }} <span class="text-sm font-bold opacity-40">People</span></div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Max Capacity</div>
            <div class="text-4xl font-black text-gray-800 dark:text-white">{{ place.max_capacity || '∞' }}</div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">GPS Distance</div>
            <div class="text-4xl font-black text-gray-800 dark:text-white">{{ place.distance ? (place.distance / 1000).toFixed(1) + 'km' : 'Unknown' }}</div>
          </div>
        </div>

        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
          <button 
            v-if="userStore.activeCheckIn?.place_id === place.id"
            @click="handleCheckOut" 
            class="flex-1 bg-red-500 text-white py-6 rounded-[2rem] font-black uppercase tracking-widest text-sm hover:scale-[1.02] active:scale-95 transition-all shadow-2xl cursor-pointer"
          >
            Check Out Now
          </button>
          
          <button 
            v-else
            @click="handleCheckIn" 
            class="flex-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-6 rounded-[2rem] font-black uppercase tracking-widest text-sm hover:scale-[1.02] active:scale-95 transition-all shadow-2xl cursor-pointer disabled:opacity-50"
            :disabled="userStore.isCheckedIn"
          >
            {{ userStore.isCheckedIn ? 'Checked in elsewhere' : 'Check In Here' }}
          </button>
          
          <button @click="isInquiryOpen = true" class="px-12 py-6 bg-white dark:bg-gray-700 text-gray-800 dark:text-white border border-gray-200 dark:border-gray-600 rounded-[2rem] font-black uppercase tracking-widest text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-all cursor-pointer">
            Send Inquiry
          </button>
        </div>
      </div>
    </div>

    <!-- Inquiry Modal -->
    <InquiryModal :is-open="isInquiryOpen" :place="place" @close="isInquiryOpen = false" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useUserStore } from '../../stores/userStore';
import placeService from '../../services/user/placeService';
import CrowdBadge from '../../components/user/places/CrowdBadge.vue';
import InquiryModal from '../../components/InquiryModal.vue';

const route = useRoute();
const userStore = useUserStore();
const place = ref(null);
const loading = ref(true);
const isInquiryOpen = ref(false);

const fetchDetails = async () => {
  try {
    const { data } = await placeService.getPlaces(userStore.userLocation?.lat, userStore.userLocation?.lng);
    place.value = data.find(p => p.id === parseInt(route.params.id));
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const handleCheckIn = async () => {
  try {
    await userStore.setCheckIn(place.value.id);
    fetchDetails();
  } catch (err) {
    if (err.response?.status !== 400 || !err.response.data.active_place) {
      alert(err.message || 'Check-in failed');
    }
  }
};

const handleCheckOut = async () => {
  try {
    await userStore.clearCheckIn();
    fetchDetails();
  } catch (err) {
    alert('Check-out failed');
  }
};

onMounted(fetchDetails);
</script>
