<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold">Manage Places</h2>
      <button @click="openForm()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition shadow-sm cursor-pointer">
        + Add New Place
      </button>
    </div>

    <!-- Map Placeholder if we wanted to show geo intelligence -->
    <div class="bg-indigo-50 dark:bg-indigo-900/10 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800 text-sm text-indigo-800 dark:text-indigo-300 flex items-center space-x-2">
      <span class="text-xl">📍</span>
      <span><strong>Location Intelligence Active:</strong> Just enter a Pincode when creating a place, and the system automatically resolves the GPS Lat/Lng and Address.</span>
    </div>

    <div v-if="adminStore.loading.places && adminStore.places.length === 0" class="text-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-4 text-gray-500 font-medium">Fetching venue data...</p>
    </div>

    <div v-if="fetchError" class="bg-red-50 border border-red-100 p-6 rounded-2xl text-center">
      <p class="text-red-600 font-bold mb-2">Failed to load places</p>
      <p class="text-red-500 text-sm mb-4">{{ fetchError }}</p>
      <button @click="fetchPlaces" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Try Again</button>
    </div>

    <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 text-sm uppercase tracking-wider">
            <th class="p-4 font-semibold">Name & Type</th>
            <th class="p-4 font-semibold">Address / Location</th>
            <th class="p-4 font-semibold">Live Crowd</th>
            <th class="p-4 font-semibold text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          <tr v-for="place in adminStore.places" :key="place.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition">
            <td class="p-4">
              <div class="font-bold text-gray-800 dark:text-gray-100">{{ place.name }}</div>
              <div class="text-xs text-gray-500 uppercase mt-0.5">{{ place.type }}</div>
            </td>
            <td class="p-4">
              <div class="text-sm truncate max-w-xs">{{ place.address || place.location || 'Unknown' }}</div>
              <div v-if="place.lat" class="text-xs text-blue-500 mt-0.5">{{ place.lat }}, {{ place.lng }}</div>
            </td>
            <td class="p-4">
              <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm font-bold shadow-inner">
                {{ place.current_crowd_count }}
              </span>
            </td>
            <td class="p-4 text-right space-x-2">
              <button @click="openForm(place)" class="text-indigo-600 hover:bg-indigo-50 px-3 py-1 rounded text-sm transition cursor-pointer">Edit</button>
              <button @click="deletePlace(place.id)" class="text-red-600 hover:bg-red-50 px-3 py-1 rounded text-sm transition cursor-pointer">Delete</button>
            </td>
          </tr>
          <tr v-if="adminStore.places.length === 0">
            <td colspan="4" class="p-8 text-center text-gray-500">No places found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modular Form Component -->
    <PlaceFormModal 
      :is-open="isFormOpen" 
      :form="form" 
      :is-looking-up="isLookingUp" 
      :is-saving="saving" 
      @close="isFormOpen = false" 
      @resolve="handlePincodeLookup" 
      @save="savePlace" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAdminStore } from '../../stores/adminStore';
import placeService from '../../services/admin/placeService';
import PlaceFormModal from '../../components/admin/PlaceFormModal.vue';

const adminStore = useAdminStore();
const saving = ref(false);
const isLookingUp = ref(false);
const fetchError = ref(null);

const isFormOpen = ref(false);
const form = ref({ 
  id: null, 
  name: '', 
  type: 'cafe', 
  country_code: 'JP',
  country: 'Japan',
  city: '',
  state: '',
  pincode: '', 
  street: '',
  mansion: '',
  address: '', 
  lat: '', 
  lng: '', 
  radius: 300, 
  max_capacity: 100 
});

const fetchPlaces = async () => {
  fetchError.value = null;
  try {
    await adminStore.fetchPlaces(true);
  } catch (err) {
    fetchError.value = err.response?.data?.message || 'Check your internet connection or backend status.';
  }
};

const handlePincodeLookup = async () => {
  if (!form.value.pincode) return;
  isLookingUp.value = true;
  try {
    const { data } = await placeService.lookupPincode(form.value.pincode, form.value.country_code);
    form.value.city = data.city;
    form.value.state = data.state;
    form.value.country = data.country;
    form.value.lat = data.lat;
    form.value.lng = data.lng;
  } catch (err) {
    alert('Pincode lookup failed for the selected country. Please fill manually.');
  } finally {
    isLookingUp.value = false;
  }
};

const openForm = (place = null) => {
  if (place) {
    form.value = { 
      ...place, 
      country_code: place.country_code || 'JP',
      radius: place.radius || 300,
      max_capacity: place.max_capacity || 100
    };
  } else {
    form.value = { 
      id: null, 
      name: '', 
      type: 'cafe', 
      country_code: 'JP',
      country: 'Japan',
      city: '',
      state: '',
      pincode: '', 
      street: '',
      mansion: '',
      address: '', 
      lat: '', 
      lng: '', 
      radius: 300, 
      max_capacity: 100 
    };
  }
  isFormOpen.value = true;
};

const savePlace = async () => {
  saving.value = true;
  try {
    if (form.value.id) {
      const { data } = await placeService.updatePlace(form.value.id, form.value);
      const index = adminStore.places.findIndex(p => p.id === form.value.id);
      if (index !== -1) adminStore.places[index] = data; // Optimistic UI
    } else {
      const { data } = await placeService.createPlace(form.value);
      adminStore.places.unshift(data); // Optimistic UI
    }
    isFormOpen.value = false;
    adminStore.fetchStats(true); // refresh stats silently
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to save place';
    alert(`Error: ${msg}`);
  } finally {
    saving.value = false;
  }
};

const deletePlace = async (id) => {
  if (!confirm('Delete this place permanently?')) return;
  
  // Optimistic delete
  const previousPlaces = [...adminStore.places];
  adminStore.places = adminStore.places.filter(p => p.id !== id);
  
  try {
    await placeService.deletePlace(id);
    adminStore.fetchStats(true);
  } catch (err) {
    adminStore.places = previousPlaces; // Revert if failed
    const msg = err.response?.data?.message || 'Failed to delete';
    alert(`Error: ${msg}`);
  }
};

onMounted(() => fetchPlaces());
</script>
