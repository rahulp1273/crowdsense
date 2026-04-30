<template>
  <Teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="$emit('close')"></div>
      
      <div class="relative bg-white dark:bg-gray-900 w-full max-w-md rounded-[2.5rem] shadow-2xl border border-white/20 overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-8 text-center">
          <div class="w-20 h-20 bg-red-50 dark:bg-red-900/20 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 animate-bounce">
            📍
          </div>
          
          <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">Out of Range</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-relaxed mb-8">
            You are currently too far from this venue to check in. Please move closer to the spot.
          </p>

          <!-- Distance Diagnostics -->
          <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
              <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Your Distance</div>
              <div class="text-xl font-black text-red-500">{{ errorDetails?.distance }}m</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
              <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Required</div>
              <div class="text-xl font-black text-indigo-500">{{ errorDetails?.radius }}m</div>
            </div>
          </div>

          <div class="space-y-3">
            <button @click="$emit('close')" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-600 dark:hover:bg-indigo-50 transition-all active:scale-95 shadow-xl cursor-pointer">
              Understood
            </button>
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
              GPS Accuracy may vary based on your environment
            </p>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  isOpen: Boolean,
  errorDetails: Object
});

defineEmits(['close']);
</script>
