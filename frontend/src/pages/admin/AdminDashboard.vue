<template>
  <div class="mt-8">
    <div class="flex justify-between items-center mb-8">
      <div>
        <h2 class="text-3xl font-bold">Admin Control Panel</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage places and monitor crowds.</p>
      </div>
      <button @click="fetchPlaces" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer" title="Refresh">
        🔄
      </button>
    </div>

    <!-- Admin Panel: Add Place -->
    <div class="mb-12 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-t-4 border-indigo-500">
      <h3 class="text-xl font-bold mb-4">Create New Place</h3>
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

    <!-- Places List -->
    <h3 class="text-2xl font-bold mb-4">Manage Places</h3>
    <div v-if="loading" class="text-center py-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
    </div>
    
    <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-100 dark:bg-gray-700">
            <th class="p-4 font-semibold text-sm">Place Name</th>
            <th class="p-4 font-semibold text-sm">Type / Location</th>
            <th class="p-4 font-semibold text-sm">Current Crowd</th>
            <th class="p-4 font-semibold text-sm text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="place in places" :key="place.id" class="border-b dark:border-gray-700 last:border-none">
            <td class="p-4 font-medium">{{ place.name }}</td>
            <td class="p-4 text-sm text-gray-500">{{ place.type }} • {{ place.location }}</td>
            <td class="p-4">
              <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                {{ place.current_crowd_count }} active
              </span>
            </td>
            <td class="p-4 text-right">
              <button @click="deletePlace(place.id)" class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded text-sm transition-colors cursor-pointer" :disabled="actionLoading === place.id">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';

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

const newPlace = ref({ name: '', type: '', location: '' });
const isAdding = ref(false);

const addPlace = async () => {
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

onMounted(() => {
  fetchPlaces();
});
</script>
