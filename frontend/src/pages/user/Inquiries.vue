<template>
  <div class="space-y-8 pb-20 max-w-4xl mx-auto">
    <div class="px-2">
      <h2 class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">Your Inquiries</h2>
      <p class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">Track your requests and reports</p>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
    </div>

    <div v-else class="space-y-4">
      <div v-for="inquiry in inquiries" :key="inquiry.id" class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-50 dark:border-gray-700 group hover:translate-y-[-4px] transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
          <div>
            <div class="text-[10px] font-black uppercase tracking-widest text-indigo-500">{{ inquiry.type }}</div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white leading-tight">Regarding {{ inquiry.place?.name }}</h3>
          </div>
          <div class="px-3 py-1 rounded-full text-[10px] font-black uppercase" :class="inquiry.status === 'new' ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600'">
            {{ inquiry.status }}
          </div>
        </div>
        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-4">“{{ inquiry.message }}”</p>
        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ new Date(inquiry.created_at).toLocaleString() }}</div>
      </div>

      <div v-if="inquiries.length === 0" class="text-center py-20 bg-gray-100/50 dark:bg-gray-800/50 rounded-[2rem] border border-dashed border-gray-300 dark:border-gray-700">
        <p class="text-gray-500 font-bold">No inquiries sent yet.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import inquiryService from '../../services/user/inquiryService';

const inquiries = ref([]);
const loading = ref(true);

const fetchInquiries = async () => {
  try {
    const { data } = await inquiryService.getUserInquiries();
    inquiries.value = data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchInquiries);
</script>
