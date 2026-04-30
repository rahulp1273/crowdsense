<template>
  <Teleport to="body">
    <div v-if="place" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/60 backdrop-blur-md p-4">
      <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20 animate-in fade-in zoom-in duration-300">
        <div class="p-8 text-center">
          <div class="w-20 h-20 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">⚠️</div>
          <h3 class="text-2xl font-black text-gray-800 dark:text-white tracking-tighter mb-2">Already Checked In!</h3>
          <p class="text-sm text-gray-500 mb-8">You have an active session at another location. You must checkout from your current spot before checking in here.</p>
          
          <!-- Current Place Card -->
          <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 mb-8 flex items-center text-left">
            <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-2xl mr-4 shadow-sm">📍</div>
            <div>
              <div class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Current Location</div>
              <div class="font-black text-lg text-gray-800 dark:text-white leading-tight">{{ place.name }}</div>
              <div class="text-xs font-medium text-gray-400">{{ place.location }}</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <button 
              @click="$emit('close')" 
              class="py-4 rounded-2xl font-black uppercase tracking-widest text-xs border border-gray-100 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all active:scale-95 cursor-pointer"
            >
              Cancel
            </button>
            <button 
              @click="handleCheckOut" 
              class="py-4 rounded-2xl font-black uppercase tracking-widest text-xs bg-red-500 text-white shadow-lg shadow-red-100 dark:shadow-none hover:bg-red-600 transition-all active:scale-95 cursor-pointer"
            >
              Check Out
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { useUserStore } from '../../stores/userStore';

const props = defineProps({
  place: Object
});

const emit = defineEmits(['close']);
const userStore = useUserStore();

const handleCheckOut = async () => {
  try {
    await userStore.clearCheckIn();
    emit('close');
    alert('You are now free to check in at new locations!');
  } catch (err) {
    alert('Failed to check out');
  }
};
</script>
