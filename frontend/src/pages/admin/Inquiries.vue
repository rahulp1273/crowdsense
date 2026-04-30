<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold">Inquiry Management</h2>
      <div class="flex space-x-2">
        <select v-model="filters.status" @change="fetchInquiries" class="px-4 py-2 border rounded-xl bg-white dark:bg-gray-800 outline-none">
          <option value="">All Statuses</option>
          <option value="new">New (Unread)</option>
          <option value="seen">Seen</option>
        </select>
      </div>
    </div>

    <div v-if="adminStore.loading.inquiries && adminStore.inquiries.length === 0" class="text-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mx-auto"></div>
    </div>

    <div v-else class="grid grid-cols-1 gap-4">
      <!-- Loading overlay for fast fetches -->
      <div v-if="adminStore.loading.inquiries" class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 z-10"></div>
      
      <div v-for="inquiry in adminStore.inquiries" :key="inquiry.id" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition relative">
        <div class="absolute right-6 top-6 flex space-x-2">
          <span v-if="inquiry.priority" class="px-2 py-1 text-xs font-bold uppercase rounded" :class="getPriorityColor(inquiry.priority)">
            {{ inquiry.priority }}
          </span>
          <span class="px-2 py-1 text-xs font-bold uppercase rounded" :class="inquiry.status === 'new' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'">
            {{ inquiry.status }}
          </span>
        </div>
        
        <div class="text-xs text-gray-500 font-bold uppercase mb-2">{{ inquiry.type }} @ {{ inquiry.place?.name }}</div>
        <p class="text-gray-800 dark:text-gray-200 text-lg mb-4">"{{ inquiry.message }}"</p>
        
        <div class="flex justify-between items-center border-t dark:border-gray-700 pt-4 mt-2">
          <div class="text-sm text-gray-500">From: <span class="font-bold">{{ inquiry.user?.name }}</span> • {{ new Date(inquiry.created_at).toLocaleString() }}</div>
          
          <button v-if="inquiry.status === 'new'" @click="markSeen(inquiry.id)" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer transition">
            Mark as Seen ✓
          </button>
        </div>
      </div>
      
      <div v-if="adminStore.inquiries.length === 0" class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
        <span class="text-gray-500">No inquiries match your filters.</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useAdminStore } from '../../stores/adminStore';

const adminStore = useAdminStore();
const filters = ref({ status: '' });

// Persist filters in localStorage so they survive navigation
if (localStorage.getItem('inq_filters')) {
  try {
    filters.value = JSON.parse(localStorage.getItem('inq_filters'));
  } catch (e) {}
}

const fetchInquiries = async () => {
  localStorage.setItem('inq_filters', JSON.stringify(filters.value));
  await adminStore.fetchInquiries(filters.value);
};

const markSeen = async (id) => {
  try {
    await adminStore.markInquirySeen(id);
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to mark as seen';
    alert(`Permission Denied: ${msg}`);
  }
};

const getPriorityColor = (p) => {
  if (p === 'high') return 'bg-orange-100 text-orange-700';
  if (p === 'medium') return 'bg-yellow-100 text-yellow-700';
  return 'bg-blue-100 text-blue-700';
};

onMounted(() => {
  // Always fetch if filters are applied or store is empty
  fetchInquiries();
});
</script>
