<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md h-[600px] flex flex-col">
      <!-- Header -->
      <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-indigo-600 text-white rounded-t-xl">
        <h3 class="font-bold text-lg">💬 Chat: {{ place?.name }}</h3>
        <button @click="closeModal" class="text-white hover:text-gray-200 cursor-pointer">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
      
      <!-- Messages List -->
      <div class="flex-1 p-4 overflow-y-auto flex flex-col space-y-3 bg-gray-50 dark:bg-gray-900" ref="messagesContainer">
        <div v-if="loading" class="text-center py-4 text-gray-500">Loading messages...</div>
        <div v-else-if="messages.length === 0" class="text-center py-4 text-gray-500">No messages yet. Be the first to say hi!</div>
        
        <div v-for="msg in messages" :key="msg.id" class="flex flex-col" :class="msg.user_id === authStore.user?.id ? 'items-end' : 'items-start'">
          <span class="text-xs text-gray-500 mb-1 px-1">{{ msg.user?.name || 'User' }}</span>
          <div class="px-4 py-2 rounded-2xl max-w-[80%]" :class="msg.user_id === authStore.user?.id ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-bl-none shadow-sm'">
            {{ msg.message }}
          </div>
        </div>
      </div>
      
      <!-- Input Area -->
      <div class="p-4 bg-white dark:bg-gray-800 border-t dark:border-gray-700 rounded-b-xl">
        <form @submit.prevent="sendMessage" class="flex gap-2">
          <input v-model="newMessage" type="text" placeholder="Type a message..." class="flex-1 px-4 py-2 border rounded-full dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" :disabled="sending">
          <button type="submit" class="bg-indigo-600 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-indigo-700 transition-colors disabled:opacity-50 cursor-pointer" :disabled="!newMessage.trim() || sending">
            ➤
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onUnmounted } from 'vue';
import api from '../services/api';
import { useAuthStore } from '../stores/auth';
// Import Echo (uncomment when pusher-js and laravel-echo are installed)
// import echo from '../services/echo';

const props = defineProps({
  isOpen: Boolean,
  place: Object
});

const emit = defineEmits(['close']);

const authStore = useAuthStore();
const messages = ref([]);
const newMessage = ref('');
const loading = ref(false);
const sending = ref(false);
const messagesContainer = ref(null);
let pollingInterval = null;

const closeModal = () => {
  stopPolling();
  // if (echo) echo.leave(`place.${props.place?.id}`);
  emit('close');
};

const fetchMessages = async () => {
  if (!props.place) return;
  try {
    const { data } = await api.get(`/places/${props.place.id}/chat`);
    messages.value = data.reverse(); 
    scrollToBottom();
  } catch (err) {
    console.error('Failed to fetch messages', err);
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !props.place) return;
  
  const tempMsg = {
    id: Date.now(),
    message: newMessage.value,
    user_id: authStore.user.id,
    user: { name: authStore.user.name }
  };
  
  messages.value.push(tempMsg);
  const msgToSend = newMessage.value;
  newMessage.value = '';
  sending.value = true;
  scrollToBottom();

  try {
    const { data } = await api.post(`/places/${props.place.id}/chat`, { message: msgToSend });
    const index = messages.value.findIndex(m => m.id === tempMsg.id);
    if (index !== -1) messages.value[index] = data;
  } catch (err) {
    console.error('Failed to send message', err);
    messages.value = messages.value.filter(m => m.id !== tempMsg.id);
  } finally {
    sending.value = false;
  }
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

const startPolling = () => {
  // We use polling as a reliable fallback
  pollingInterval = setInterval(() => {
    if (props.isOpen) fetchMessages();
  }, 3000);
};

const stopPolling = () => {
  if (pollingInterval) clearInterval(pollingInterval);
};

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    loading.value = true;
    fetchMessages().then(() => {
      loading.value = false;
      startPolling(); // Fallback
      
      // Real-time listener (uncomment when installed)
      /*
      if (echo && props.place) {
        echo.channel(`place.${props.place.id}`)
          .listen('MessageSent', (e) => {
            // Avoid duplicate if we just sent it
            if (!messages.value.find(m => m.id === e.message.id)) {
              messages.value.push(e.message);
              scrollToBottom();
            }
          });
      }
      */
    });
  } else {
    stopPolling();
  }
});

onUnmounted(() => {
  stopPolling();
});
</script>
