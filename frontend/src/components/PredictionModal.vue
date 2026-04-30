<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm flex flex-col overflow-hidden">
      
      <!-- Dynamic Header based on predicted crowd -->
      <div class="p-6 text-center text-white" :class="headerColor">
        <h3 class="font-bold text-2xl mb-1">Crowd Forecast</h3>
        <p class="text-white/80 text-sm">{{ place?.name }}</p>
      </div>
      
      <div class="p-6 flex-1 flex flex-col items-center">
        <div v-if="loading" class="animate-pulse w-full">
          <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg w-full mb-4"></div>
          <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mx-auto"></div>
        </div>
        
        <div v-else-if="prediction" class="w-full text-center">
          <div class="text-5xl font-black mb-2 text-gray-800 dark:text-gray-100">
            {{ prediction.predicted_crowd }}
          </div>
          <p class="text-sm text-gray-500 uppercase tracking-widest font-bold mb-6">Predicted People</p>
          
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-100 dark:border-gray-600">
            <p class="text-gray-700 dark:text-gray-300 font-medium">
              "{{ prediction.suggestion }}"
            </p>
          </div>
        </div>
      </div>
      
      <div class="p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <button @click="$emit('close')" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold py-3 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors cursor-pointer">
          Close Forecast
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import api from '../services/api';

const props = defineProps({
  isOpen: Boolean,
  place: Object
});

const emit = defineEmits(['close']);

const loading = ref(false);
const prediction = ref(null);

const fetchPrediction = async () => {
  if (!props.place) return;
  loading.value = true;
  try {
    const { data } = await api.get(`/places/${props.place.id}/prediction`);
    prediction.value = data;
  } catch (err) {
    console.error('Prediction failed', err);
  } finally {
    loading.value = false;
  }
};

const headerColor = computed(() => {
  if (!prediction.value) return 'bg-indigo-600';
  const count = prediction.value.predicted_crowd;
  if (count < 20) return 'bg-green-500';
  if (count < 50) return 'bg-yellow-500';
  return 'bg-red-500';
});

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    prediction.value = null;
    fetchPrediction();
  }
});
</script>
