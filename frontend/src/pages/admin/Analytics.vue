<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Platform Analytics</h2>
      <button @click="fetchStats" class="text-indigo-600 font-bold hover:underline cursor-pointer">Refresh Data</button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mx-auto"></div>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Crowd Trend Chart (Simulated) -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold mb-6">Peak Crowd Hours (Simulated)</h3>
        <div class="h-64 flex items-end justify-between space-x-2">
          <!-- Fake bars for visual -->
          <div v-for="i in 12" :key="i" class="w-full bg-indigo-100 dark:bg-indigo-900/30 rounded-t-md relative group">
            <div class="absolute bottom-0 w-full bg-indigo-500 rounded-t-md transition-all duration-500" :style="{ height: `${Math.max(10, Math.random() * 100)}%` }"></div>
            <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded transition">{{ i + 8 }}:00</div>
          </div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-4 border-t dark:border-gray-700 pt-4">
          <span>9 AM</span>
          <span>3 PM</span>
          <span>8 PM</span>
        </div>
      </div>

      <!-- Inquiry Growth (Simulated) -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold mb-6">Inquiry Volume Trends</h3>
        <div class="h-64 flex items-end justify-between space-x-2">
          <div v-for="i in 7" :key="i" class="w-full bg-blue-100 dark:bg-blue-900/30 rounded-t-md relative group">
            <div class="absolute bottom-0 w-full bg-blue-500 rounded-t-md transition-all duration-500" :style="{ height: `${Math.max(20, Math.random() * 80)}%` }"></div>
          </div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-4 border-t dark:border-gray-700 pt-4">
          <span>Mon</span>
          <span>Wed</span>
          <span>Sun</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import dashboardService from '../../services/admin/dashboardService';

const loading = ref(true);

const fetchStats = async () => {
  loading.value = true;
  try {
    await dashboardService.getStats();
  } finally {
    setTimeout(() => { loading.value = false; }, 500); // fake delay for animation
  }
};

onMounted(() => fetchStats());
</script>
