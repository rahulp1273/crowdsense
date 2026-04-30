<template>
  <Teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-[120] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="$emit('close')"></div>
      
      <div class="relative bg-white dark:bg-gray-900 w-full max-w-sm rounded-3xl shadow-2xl p-8 text-center animate-in fade-in zoom-in duration-200">
        <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">Verify Changes</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-6">Enter the code sent to your email</p>
        
        <div class="flex justify-center mb-8">
          <OtpInput :length="6" @complete="$emit('complete', $event)" />
        </div>

        <div class="flex space-x-3">
          <button @click="$emit('close')" class="flex-1 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all">
            Cancel
          </button>
          <button 
            @click="$emit('verify')" 
            :disabled="loading || !otpComplete" 
            class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 transition-all disabled:opacity-50 flex items-center justify-center"
          >
            <span v-if="loading" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
            Confirm
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import OtpInput from '../../common/OtpInput.vue';

defineProps({
  isOpen: Boolean,
  loading: Boolean,
  otpComplete: Boolean
});

defineEmits(['close', 'complete', 'verify']);
</script>
