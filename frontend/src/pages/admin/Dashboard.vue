<template>
  <div class="space-y-6">
    <div v-if="adminStore.loading.stats && !Object.keys(adminStore.stats).length" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>
    
    <template v-else>
      <!-- Stat Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/20 rounded-full group-hover:scale-110 transition-transform"></div>
          <p class="text-sm font-medium text-gray-500 mb-1">Total Places</p>
          <h3 class="text-3xl font-black text-gray-800 dark:text-gray-100">{{ adminStore.stats.total_places }}</h3>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 dark:bg-green-900/20 rounded-full group-hover:scale-110 transition-transform"></div>
          <p class="text-sm font-medium text-gray-500 mb-1">Active Places</p>
          <h3 class="text-3xl font-black text-gray-800 dark:text-gray-100">{{ adminStore.stats.active_places }}</h3>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full group-hover:scale-110 transition-transform"></div>
          <p class="text-sm font-medium text-gray-500 mb-1">Total Inquiries</p>
          <h3 class="text-3xl font-black text-gray-800 dark:text-gray-100">{{ adminStore.stats.total_inquiries }}</h3>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-full group-hover:scale-110 transition-transform"></div>
          <p class="text-sm font-medium text-gray-500 mb-1">Unread Inquiries</p>
          <h3 class="text-3xl font-black text-red-600">{{ adminStore.stats.unread_inquiries }}</h3>
        </div>
      </div>

      <!-- Live Crowd Dashboard & Recent Inquiries -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center">
            <span class="mr-2">🔥</span> Live Top Places
          </h3>
          <div class="space-y-4">
            <div v-for="place in adminStore.stats.top_places" :key="place.id" class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
              <div class="font-medium">{{ place.name }}</div>
              <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full animate-pulse" :class="getDotColor(place.current_crowd_count)"></span>
                <span class="font-bold">{{ place.current_crowd_count }} live</span>
              </div>
            </div>
            <div v-if="!adminStore.stats.top_places?.length" class="text-center text-gray-500 py-4">No active crowds right now.</div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold flex items-center">
              <span class="mr-2">📩</span> Recent Inquiries
            </h3>
            <router-link to="/admin/inquiries" class="text-xs font-bold text-indigo-600 hover:underline">View All</router-link>
          </div>
          <div class="space-y-3">
            <div v-for="inq in adminStore.inquiries.slice(0, 5)" :key="inq.id" class="p-4 border border-gray-50 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
              <div class="flex justify-between items-start mb-1">
                <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500">{{ inq.type }}</span>
                <span v-if="inq.status === 'new'" class="w-2 h-2 bg-red-500 rounded-full"></span>
              </div>
              <p class="text-sm font-bold text-gray-800 dark:text-gray-200 line-clamp-1">"{{ inq.message }}"</p>
              <div class="text-[10px] text-gray-400 mt-2 font-medium">By {{ inq.user?.name }} • {{ new Date(inq.created_at).toLocaleTimeString() }}</div>
            </div>
            <div v-if="!adminStore.inquiries.length" class="text-center text-gray-500 py-10">No inquiries yet.</div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import { useAdminStore } from '../../stores/adminStore';

const adminStore = useAdminStore();
let pollInterval = null;

onMounted(() => {
  adminStore.fetchStats(true);
  adminStore.fetchInquiries();
  
  // Real-time silent background polling every 10 seconds
  pollInterval = setInterval(() => {
    adminStore.fetchStats(true);
    adminStore.fetchInquiries();
  }, 10000);
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});

const getDotColor = (count) => {
  if (count < 20) return 'bg-green-500';
  if (count < 50) return 'bg-yellow-500';
  return 'bg-red-500';
};
</script>
