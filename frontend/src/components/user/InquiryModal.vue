<template>
  <Teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
      <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md flex flex-col overflow-hidden border border-white/20">
        
        <div class="p-8 border-b dark:border-gray-700 flex justify-between items-center">
          <div>
            <h3 class="font-black text-2xl tracking-tighter text-gray-800 dark:text-white">Send Inquiry</h3>
            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">{{ place?.name }}</p>
          </div>
          <button @click="$emit('close')" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 hover:text-gray-800 transition-colors cursor-pointer">✕</button>
        </div>
        
        <form @submit.prevent="submitInquiry" class="p-8 space-y-6">
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Inquiry Type</label>
            <select v-model="form.type" class="w-full px-5 py-4 border rounded-2xl dark:bg-gray-700 dark:border-gray-600 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold text-gray-700 dark:text-white">
              <option value="general">General Question</option>
              <option value="issue">Report an Issue</option>
              <option value="support">Support Request</option>
            </select>
          </div>
          
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Priority Level</label>
            <div class="grid grid-cols-3 gap-2">
              <button 
                v-for="p in ['low', 'medium', 'high']" 
                :key="p"
                type="button"
                @click="form.priority = p"
                class="py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border"
                :class="form.priority === p ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-400'"
              >
                {{ p }}
              </button>
            </div>
          </div>
          
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Detailed Message</label>
            <textarea v-model="form.message" rows="4" required placeholder="Describe your inquiry..." class="w-full px-5 py-4 border rounded-2xl dark:bg-gray-700 dark:border-gray-600 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all resize-none font-medium text-gray-700 dark:text-white"></textarea>
          </div>
          
          <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-black uppercase tracking-widest text-xs py-5 rounded-[1.5rem] hover:bg-indigo-600 dark:hover:bg-indigo-100 hover:text-white dark:hover:text-indigo-600 transition shadow-xl disabled:opacity-50 active:scale-95" :disabled="loading">
            {{ loading ? 'Processing...' : 'Submit Inquiry' }}
          </button>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import api from '../../services/api';
import { useUserStore } from '../../stores/userStore';

const userStore = useUserStore();

const props = defineProps({
  isOpen: Boolean,
  place: Object
});

const emit = defineEmits(['close']);

const loading = ref(false);
const form = reactive({
  type: 'general',
  priority: 'low',
  message: ''
});

const submitInquiry = async () => {
  if (!props.place) return;
  loading.value = true;
  try {
    await api.post(`/places/${props.place.id}/inquiries`, form);
    await userStore.fetchInquiries();
    alert('Inquiry sent successfully!');
    form.message = '';
    emit('close');
  } catch (err) {
    console.error('Failed to send inquiry', err);
    alert(err.response?.data?.message || 'Failed to send inquiry');
  } finally {
    loading.value = false;
  }
};

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    form.message = '';
    form.type = 'general';
    form.priority = 'low';
  }
});
</script>
