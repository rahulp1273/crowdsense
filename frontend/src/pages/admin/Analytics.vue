<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Platform Analytics</h2>
        <p class="text-sm text-gray-500 font-medium">Real-time insights and growth trends.</p>
      </div>
      <button 
        @click="fetchAnalytics" 
        class="px-6 py-2.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white transition-all active:scale-95 cursor-pointer shadow-sm"
      >
        Refresh Data
      </button>
    </div>

    <!-- Stats Overview Cards -->
    <div v-if="analyticsData?.stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="(val, label) in summaryStats" :key="label" class="bg-white dark:bg-gray-900 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">{{ label }}</div>
        <div class="text-3xl font-black text-gray-900 dark:text-white">{{ val }}</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-24">
      <div class="w-12 h-12 border-4 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
      <p class="mt-4 text-sm font-bold text-gray-500 animate-pulse uppercase tracking-widest">Compiling Analytics...</p>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Crowd Trend Chart -->
      <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-800 group hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-500">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Peak Activity Hours</h3>
          <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">24h Distribution</span>
        </div>
        
        <div v-if="analyticsData?.peak_hours?.length > 0" class="h-64 flex items-end justify-between space-x-1.5 px-2">
          <div v-for="h in 24" :key="h" class="flex-1 bg-gray-50 dark:bg-gray-800/50 rounded-t-lg relative group/bar h-full flex flex-col justify-end">
            <div 
              class="w-full bg-gradient-to-t from-indigo-600 to-indigo-400 rounded-t-lg transition-all duration-1000 ease-out relative"
              :style="{ height: `${getHourCount(h-1)}%` }"
            >
              <!-- Tooltip -->
              <div class="opacity-0 group-hover/bar:opacity-100 absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-black py-1.5 px-3 rounded-lg transition-all z-20 pointer-events-none whitespace-nowrap shadow-xl">
                {{ h-1 }}:00 • {{ getRawCount(h-1) }} Checkins
              </div>
            </div>
          </div>
        </div>
        <div v-else class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-3xl">
           <span class="text-gray-300 font-bold uppercase text-xs">No activity data recorded yet</span>
        </div>
        
        <div class="flex justify-between text-[10px] font-black text-gray-400 uppercase tracking-widest mt-6 pt-6 border-t border-gray-50 dark:border-gray-800 px-2">
          <span>00:00</span>
          <span>12:00</span>
          <span>23:59</span>
        </div>
      </div>

      <!-- Inquiry Growth -->
      <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-800 group hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-500">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Weekly Engagement</h3>
          <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Inquiry Volume</span>
        </div>

        <div v-if="analyticsData?.inquiry_trends?.length > 0" class="h-64 flex items-end justify-between space-x-3 px-2">
          <div v-for="(day, idx) in analyticsData.inquiry_trends" :key="idx" class="flex-1 bg-gray-50 dark:bg-gray-800/50 rounded-t-2xl relative group/bar h-full flex flex-col justify-end">
            <div 
              class="w-full bg-gradient-to-t from-emerald-500 to-teal-400 rounded-t-2xl transition-all duration-1000 ease-out delay-150"
              :style="{ height: `${(day.count / maxInquiries) * 100}%` }"
            >
              <div class="opacity-0 group-hover/bar:opacity-100 absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-black py-1.5 px-3 rounded-lg transition-all z-20 pointer-events-none whitespace-nowrap shadow-xl">
                {{ formatDate(day.date) }} • {{ day.count }} Inquiries
              </div>
            </div>
          </div>
        </div>
        <div v-else class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-3xl">
           <span class="text-gray-300 font-bold uppercase text-xs">No inquiry trends available</span>
        </div>

        <div class="flex justify-between text-[10px] font-black text-gray-400 uppercase tracking-widest mt-6 pt-6 border-t border-gray-50 dark:border-gray-800 px-2">
          <span v-if="analyticsData?.inquiry_trends?.length">{{ formatDate(analyticsData.inquiry_trends[0].date) }}</span>
          <span v-if="analyticsData?.inquiry_trends?.length">{{ formatDate(analyticsData.inquiry_trends[analyticsData.inquiry_trends.length-1].date) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import dashboardService from '../../services/admin/dashboardService';

const loading = ref(true);
const analyticsData = ref(null);

const fetchAnalytics = async () => {
  loading.value = true;
  try {
    const { data } = await dashboardService.getAnalytics();
    analyticsData.value = data;
  } catch (e) {
    console.error("Failed to fetch analytics:", e);
  } finally {
    loading.value = false;
  }
};

const summaryStats = computed(() => {
  if (!analyticsData.value?.stats) return {};
  const s = analyticsData.value.stats;
  return {
    'Total Venues': s.total_places,
    'Live Crowd': s.active_places,
    'Total Feedback': s.total_inquiries,
    'New Issues': s.unread_inquiries
  };
});

const maxInquiries = computed(() => {
  if (!analyticsData.value?.inquiry_trends?.length) return 1;
  return Math.max(...analyticsData.value.inquiry_trends.map(d => d.count), 1);
});

const getHourCount = (hour) => {
  if (!analyticsData.value?.peak_hours) return 0;
  const match = analyticsData.value.peak_hours.find(h => parseInt(h.hour) === hour);
  if (!match) return 5; // minimum visibility
  const max = Math.max(...analyticsData.value.peak_hours.map(h => h.count), 1);
  return (match.count / max) * 100;
};

const getRawCount = (hour) => {
  if (!analyticsData.value?.peak_hours) return 0;
  const match = analyticsData.value.peak_hours.find(h => parseInt(h.hour) === hour);
  return match ? match.count : 0;
};

const formatDate = (dateStr) => {
  const d = new Date(dateStr);
  return d.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
};

onMounted(() => fetchAnalytics());
</script>
