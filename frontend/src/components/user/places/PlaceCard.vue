<template>
  <div class="relative bg-white dark:bg-gray-800 rounded-[2rem] p-6 shadow-xl shadow-gray-100 dark:shadow-none border border-gray-50 dark:border-gray-700 hover:scale-[1.03] hover:shadow-2xl transition-all duration-500 group overflow-hidden">
    <!-- Glow Effect -->
    <div class="absolute top-0 right-0 w-32 h-32 blur-[60px] opacity-20 group-hover:opacity-40 transition-opacity" :class="glowColor"></div>

    <div class="flex justify-between items-start mb-6">
      <div>
        <h3 class="text-2xl font-black text-gray-800 dark:text-white tracking-tighter group-hover:text-indigo-600 transition-colors">{{ place.name }}</h3>
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ place.type }} • {{ place.location }}</p>
      </div>
      <CrowdBadge :count="place.current_crowd_count" />
    </div>

    <div class="flex items-center space-x-2 text-xs font-bold text-gray-500 mb-8">
      <span>📍</span>
      <span>{{ place.distance ? (place.distance / 1000).toFixed(1) + ' km away' : 'Near you' }}</span>
    </div>

    <div class="flex items-center justify-between">
      <div class="flex flex-col">
        <span class="text-4xl font-black text-gray-800 dark:text-white leading-none">{{ place.current_crowd_count }}</span>
        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Live People</span>
      </div>
      
      <div class="flex space-x-2">
        <button 
          @click="isInquiryOpen = true"
          class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center hover:bg-indigo-100 transition-all cursor-pointer border border-indigo-100 dark:border-indigo-800 active:scale-90"
          title="Send Inquiry"
        >
          <span>📩</span>
        </button>
        
        <button 
          @click="isCurrentCheckIn ? handleCheckOut() : handleCheckIn()" 
          class="min-w-[140px] px-6 h-12 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all active:scale-95 shadow-lg flex items-center justify-center cursor-pointer disabled:opacity-50"
          :class="[
            isCurrentCheckIn 
              ? 'bg-red-500 text-white shadow-red-100 dark:shadow-none hover:bg-red-600' 
              : 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-gray-200 dark:shadow-none hover:bg-indigo-600 dark:hover:bg-indigo-100'
          ]"
          :disabled="userStore.loading.checkIn"
        >
          <div v-if="userStore.loading.checkIn" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          <span v-else>{{ isCurrentCheckIn ? 'Check Out' : 'Check In' }}</span>
        </button>
      </div>
    </div>

    <!-- Inquiry Modal mounted inside card for easy access -->
    <InquiryModal :is-open="isInquiryOpen" :place="place" @close="isInquiryOpen = false" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import CrowdBadge from './CrowdBadge.vue';
import InquiryModal from '../InquiryModal.vue';
import { useUserStore } from '../../../stores/userStore';

const props = defineProps({
  place: Object
});

const userStore = useUserStore();
const isInquiryOpen = ref(false);

const isCurrentCheckIn = computed(() => userStore.activeCheckIn?.place_id === props.place.id);

const glowColor = computed(() => {
  const count = props.place.current_crowd_count;
  if (count < 20) return 'bg-green-400';
  if (count < 50) return 'bg-yellow-400';
  return 'bg-red-400';
});

const handleCheckIn = async () => {
  try {
    await userStore.setCheckIn(props.place.id);
  } catch (err) {
    // Conflict modal is handled globally by userStore.activePlaceConflict
    console.error('Check-in error:', err.message);
  }
};

const handleCheckOut = async () => {
  try {
    await userStore.clearCheckIn();
  } catch (err) {
    console.error('Check-out failed:', err);
  }
};
</script>
