<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Inquiry Desk</h2>
        <p class="text-sm text-gray-500 font-medium">Manage and respond to user feedback and reports.</p>
      </div>
      
      <div class="flex flex-wrap items-center gap-3">
        <!-- Status Filter -->
        <div class="relative group">
          <select 
            v-model="filters.status" 
            @change="fetchInquiries"
            class="appearance-none pl-10 pr-10 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-sm font-bold text-gray-700 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all cursor-pointer"
          >
            <option value="">All Statuses</option>
            <option value="new">New (Unread)</option>
            <option value="seen">Read / Seen</option>
          </select>
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        </div>

        <!-- Place Filter -->
        <div class="relative group">
          <select 
            v-model="filters.place_id" 
            @change="fetchInquiries"
            class="appearance-none pl-10 pr-10 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-sm font-bold text-gray-700 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all cursor-pointer"
          >
            <option value="">All Places</option>
            <option v-for="place in adminStore.places" :key="place.id" :value="place.id">
              {{ place.name }}
            </option>
          </select>
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">📍</span>
        </div>

        <button 
          @click="resetFilters" 
          class="p-2.5 bg-gray-50 dark:bg-gray-800 text-gray-500 hover:text-indigo-600 rounded-2xl transition-colors cursor-pointer"
          title="Reset Filters"
        >
          🔄
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="adminStore.loading.inquiries && adminStore.inquiries.length === 0" class="flex flex-col items-center justify-center py-24">
      <div class="w-12 h-12 border-4 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
      <p class="mt-4 text-sm font-bold text-gray-500 animate-pulse uppercase tracking-widest">Loading Inquiries...</p>
    </div>

    <!-- Content Grid -->
    <div v-else class="grid grid-cols-1 gap-6 relative">
      <div v-if="adminStore.loading.inquiries" class="absolute inset-0 bg-white/40 dark:bg-gray-950/40 backdrop-blur-[1px] z-10 rounded-3xl transition-opacity"></div>
      
      <div 
        v-for="inquiry in adminStore.inquiries" 
        :key="inquiry.id" 
        class="group bg-white dark:bg-gray-900 p-6 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 relative overflow-hidden"
      >
        <!-- Status & Priority Badges -->
        <div class="absolute right-8 top-8 flex items-center space-x-2">
          <span 
            v-if="inquiry.priority" 
            class="px-3 py-1 text-[10px] font-black uppercase rounded-full tracking-wider" 
            :class="getPriorityColor(inquiry.priority)"
          >
            {{ inquiry.priority }}
          </span>
          <span 
            class="px-3 py-1 text-[10px] font-black uppercase rounded-full tracking-wider" 
            :class="inquiry.status === 'new' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'"
          >
            {{ inquiry.status }}
          </span>
        </div>
        
        <div class="flex items-start space-x-4">
          <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl flex items-center justify-center text-2xl">
            {{ inquiry.type === 'report' ? '⚠️' : '💬' }}
          </div>
          <div class="flex-1 pr-24">
            <div class="flex items-center space-x-2 mb-1">
              <span class="text-xs font-black text-indigo-500 uppercase tracking-widest">{{ inquiry.type }}</span>
              <span class="text-xs text-gray-300">•</span>
              <span class="text-xs font-bold text-gray-400">{{ inquiry.place?.name }}</span>
            </div>
            <p class="text-gray-800 dark:text-gray-200 text-lg font-medium leading-relaxed mb-6">
              "{{ inquiry.message }}"
            </p>
          </div>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-6 border-t border-gray-50 dark:border-gray-800">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-xs font-bold">
              {{ inquiry.user?.name.charAt(0) }}
            </div>
            <div>
              <div class="text-sm font-black text-gray-900 dark:text-white">{{ inquiry.user?.name }}</div>
              <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ formatTime(inquiry.created_at) }}</div>
            </div>
          </div>
          
          <button 
            v-if="inquiry.status === 'new'" 
            @click="markSeen(inquiry.id)" 
            class="px-6 py-2.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white transition-all active:scale-95 cursor-pointer shadow-sm"
          >
            Mark as Resolved
          </button>
          <div v-else class="flex items-center text-emerald-500 space-x-1">
            <span class="text-lg">✓</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Resolved</span>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-if="adminStore.inquiries.length === 0" class="flex flex-col items-center justify-center py-24 bg-gray-50/50 dark:bg-gray-900/30 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-gray-800">
        <span class="text-5xl mb-4 text-gray-300">📭</span>
        <h3 class="text-lg font-black text-gray-400 uppercase tracking-tighter">No inquiries found</h3>
        <p class="text-sm text-gray-400 mt-1">Try adjusting your filters to find what you're looking for.</p>
        <button @click="resetFilters" class="mt-6 text-indigo-600 font-bold hover:underline cursor-pointer">Clear all filters</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAdminStore } from '../../stores/adminStore';

const adminStore = useAdminStore();
const filters = ref({ 
  status: '',
  place_id: ''
});

// Load filters from localStorage
const storedFilters = localStorage.getItem('inq_filters');
if (storedFilters) {
  try {
    filters.value = { ...filters.value, ...JSON.parse(storedFilters) };
  } catch (e) {}
}

const fetchInquiries = async () => {
  localStorage.setItem('inq_filters', JSON.stringify(filters.value));
  await adminStore.fetchInquiries(filters.value);
};

const resetFilters = () => {
  filters.value = { status: '', place_id: '' };
  fetchInquiries();
};

const markSeen = async (id) => {
  try {
    await adminStore.markInquirySeen(id);
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to mark as seen';
    alert(`Permission Denied: ${msg}`);
  }
};

const formatTime = (dateStr) => {
  return new Date(dateStr).toLocaleString([], { 
    dateStyle: 'medium', 
    timeStyle: 'short' 
  });
};

const getPriorityColor = (p) => {
  if (p === 'high') return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400';
  if (p === 'medium') return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
  return 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400';
};

onMounted(async () => {
  // Fetch places for the filter dropdown
  await adminStore.fetchPlaces();
  fetchInquiries();
});
</script>
