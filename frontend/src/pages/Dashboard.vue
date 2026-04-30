<template>
  <div class="mt-8">
    <div class="flex justify-between items-center mb-8">
      <div>
        <h2 class="text-3xl font-bold">Dashboard</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ authStore.user?.name }}!</p>
      </div>
      <button @click="fetchPlaces" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer" title="Refresh">
        🔄
      </button>
    </div>

    <div v-if="loading" class="text-center py-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="place in places" :key="place.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4" :class="getBorderColor(place.current_crowd_count)">
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-xl font-bold">{{ place.name }}</h3>
              <p class="text-sm text-gray-500 capitalize">{{ place.type }} • {{ place.location }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-bold" :class="getBadgeClass(place.current_crowd_count)">
              {{ getStatusText(place.current_crowd_count) }}
            </span>
          </div>
          
          <div class="mt-6 flex justify-between items-center">
            <div class="text-2xl font-bold">
              {{ place.current_crowd_count }} <span class="text-sm font-normal text-gray-500">people</span>
            </div>
            
            <div class="space-x-2">
              <button @click="checkIn(place.id)" class="px-4 py-2 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 rounded-md text-sm font-medium transition-colors cursor-pointer disabled:opacity-50" :disabled="actionLoading === place.id">
                Check In
              </button>
              <button @click="checkOut(place.id)" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition-colors cursor-pointer disabled:opacity-50" :disabled="actionLoading === place.id || place.current_crowd_count === 0">
                Check Out
              </button>
              <button v-if="authStore.isAdmin" @click="deletePlace(place.id)" class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-md text-sm font-medium transition-colors cursor-pointer disabled:opacity-50" :disabled="actionLoading === place.id">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Panel -->
    <div v-if="authStore.isAdmin" class="mt-12 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-t-4 border-indigo-500">
      <h3 class="text-2xl font-bold mb-4">Admin Panel: Add New Place</h3>
      <form @submit.prevent="addPlace" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input v-model="newPlace.name" type="text" required class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Type</label>
          <input v-model="newPlace.type" type="text" placeholder="cafe, gym, mall" required class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Location</label>
          <input v-model="newPlace.location" type="text" class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors cursor-pointer" :disabled="isAdding">
            {{ isAdding ? 'Adding...' : 'Add Place' }}
          </button>
        </div>
      </form>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const places = ref([]);
const loading = ref(true);
const actionLoading = ref(null);

const fetchPlaces = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/places');
    places.value = data;
  } catch (err) {
    console.error('Failed to fetch places:', err);
  } finally {
    loading.value = false;
  }
};

const checkIn = async (placeId) => {
  actionLoading.value = placeId;
  try {
    const { data } = await api.post('/checkin', { place_id: placeId });
    const place = places.value.find(p => p.id === placeId);
    if (place) place.current_crowd_count = data.current_crowd_count;
  } catch (err) {
    console.error('Checkin failed', err);
  } finally {
    actionLoading.value = null;
  }
};

const checkOut = async (placeId) => {
  actionLoading.value = placeId;
  try {
    const { data } = await api.post('/checkout', { place_id: placeId });
    const place = places.value.find(p => p.id === placeId);
    if (place) place.current_crowd_count = data.current_crowd_count;
  } catch (err) {
    console.error('Checkout failed', err);
  } finally {
    actionLoading.value = null;
  }
};

const newPlace = ref({ name: '', type: '', location: '' });
const isAdding = ref(false);

const addPlace = async () => {
  if (!authStore.isAdmin) return;
  isAdding.value = true;
  try {
    const { data } = await api.post('/places', newPlace.value);
    places.value.push(data);
    newPlace.value = { name: '', type: '', location: '' };
  } catch (err) {
    console.error('Failed to add place', err);
  } finally {
    isAdding.value = false;
  }
};

const deletePlace = async (placeId) => {
  if (!authStore.isAdmin) return;
  if (!confirm('Are you sure you want to delete this place?')) return;
  
  actionLoading.value = placeId;
  try {
    await api.delete(`/places/${placeId}`);
    places.value = places.value.filter(p => p.id !== placeId);
  } catch (err) {
    console.error('Failed to delete place', err);
  } finally {
    actionLoading.value = null;
  }
};

// UI Helpers
const getBorderColor = (count) => {
  if (count < 20) return 'border-green-500';
  if (count < 50) return 'border-yellow-500';
  return 'border-red-500';
};

const getBadgeClass = (count) => {
  if (count < 20) return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
  if (count < 50) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
  return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
};

const getStatusText = (count) => {
  if (count < 20) return 'Low';
  if (count < 50) return 'Medium';
  return 'High';
};

onMounted(() => {
  fetchPlaces();
});
</script>
