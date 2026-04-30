<template>
  <div class="mt-8">
    <!-- GPS Banner -->
    <div v-if="!userLocation" class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800 mb-8 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <span class="text-xl">📡</span>
        <span class="text-sm font-medium text-indigo-800 dark:text-indigo-300">Detecting your location for the best experience...</span>
      </div>
      <button @click="detectLocation" class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 hover:underline">Retry GPS</button>
    </div>

    <div class="flex justify-between items-center mb-8">
      <div>
        <h2 class="text-3xl font-bold">Local Crowd Intelligence</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">
          <span v-if="userLocation">Showing places near you</span>
          <span v-else>Welcome back, {{ authStore.user?.name }}!</span>
        </p>
      </div>
      <button @click="fetchPlaces" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer transition-colors" title="Refresh">
        🔄
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="place in places" :key="place.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border-t-4" :class="getBorderColor(place.current_crowd_count)">
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-xl font-black text-gray-800 dark:text-gray-100">{{ place.name }}</h3>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">{{ place.type }} • {{ place.distance ? (place.distance / 1000).toFixed(2) + ' km away' : place.location }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-tighter" :class="getBadgeClass(place.current_crowd_count)">
              {{ getStatusText(place.current_crowd_count) }}
            </span>
          </div>
          
          <div class="mt-8 flex justify-between items-end">
            <div class="flex flex-col">
              <span class="text-4xl font-black text-gray-800 dark:text-gray-100">{{ place.current_crowd_count }}</span>
              <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Active People</span>
            </div>
            
            <div class="flex flex-col space-y-2">
              <button @click="checkIn(place.id)" 
                class="px-6 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-sm font-black uppercase transition-all hover:scale-105 active:scale-95 disabled:opacity-50 cursor-pointer" 
                :disabled="actionLoading === place.id">
                Check In
              </button>
              <button @click="checkOut(place.id)" 
                class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-xl text-xs font-bold uppercase transition-all hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 cursor-pointer" 
                :disabled="actionLoading === place.id">
                Check Out
              </button>
            </div>
          </div>
          
          <div class="mt-6 pt-6 border-t dark:border-gray-700 flex justify-between">
            <button @click="viewPrediction(place)" class="text-xs font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors">🔮 Prediction</button>
            <button @click="openInquiry(place)" class="text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-800 transition-colors">📩 Inquiry</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modals -->
    <InquiryModal :is-open="isInquiryOpen" :place="selectedPlace" @close="isInquiryOpen = false" />
    <PredictionModal :is-open="isPredictionOpen" :place="selectedPlace" @close="isPredictionOpen = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import InquiryModal from '../../components/InquiryModal.vue';
import PredictionModal from '../../components/PredictionModal.vue';

const authStore = useAuthStore();
const places = ref([]);
const loading = ref(true);
const actionLoading = ref(null);
const userLocation = ref(null);

const isInquiryOpen = ref(false);
const isPredictionOpen = ref(false);
const selectedPlace = ref(null);

const detectLocation = () => {
  if (!navigator.geolocation) {
    console.error('Geolocation not supported');
    fetchPlaces();
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      userLocation.value = {
        lat: pos.coords.latitude,
        lng: pos.coords.longitude
      };
      fetchPlaces();
    },
    (err) => {
      console.error('Geolocation error:', err);
      fetchPlaces();
    }
  );
};

const fetchPlaces = async () => {
  loading.value = true;
  try {
    const params = userLocation.value ? { lat: userLocation.value.lat, lng: userLocation.value.lng } : {};
    const { data } = await api.get('/places', { params });
    places.value = data;
  } catch (err) {
    console.error('Failed to fetch places:', err);
  } finally {
    loading.value = false;
  }
};

const checkIn = async (placeId) => {
  if (!userLocation.value) {
    alert('Please enable GPS to check in.');
    detectLocation();
    return;
  }

  actionLoading.value = placeId;
  try {
    await api.post('/checkin', { 
      place_id: placeId,
      lat: userLocation.value.lat,
      lng: userLocation.value.lng
    });
    alert('Checked in successfully!');
    fetchPlaces(); // Refresh to get sorted list and updated counts
  } catch (err) {
    console.error('Checkin failed', err);
    alert(err.response?.data?.error || 'Checkin failed');
  } finally {
    actionLoading.value = null;
  }
};

const checkOut = async (placeId) => {
  actionLoading.value = placeId;
  try {
    await api.post('/checkout');
    alert('Checked out successfully!');
    fetchPlaces();
  } catch (err) {
    console.error('Checkout failed', err);
    alert(err.response?.data?.error || 'Checkout failed');
  } finally {
    actionLoading.value = null;
  }
};

const viewPrediction = (place) => {
  selectedPlace.value = place;
  isPredictionOpen.value = true;
};

const openInquiry = (place) => {
  selectedPlace.value = place;
  isInquiryOpen.value = true;
};

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
  if (count < 20) return 'Safe';
  if (count < 50) return 'Steady';
  return 'Crowded';
};

let autoRefresh = null;

onMounted(() => {
  detectLocation();
  autoRefresh = setInterval(() => {
    const params = userLocation.value ? { lat: userLocation.value.lat, lng: userLocation.value.lng } : {};
    api.get('/places', { params }).then(({ data }) => {
      data.forEach(newPlace => {
        const existing = places.value.find(p => p.id === newPlace.id);
        if (existing) {
          existing.current_crowd_count = newPlace.current_crowd_count;
          existing.distance = newPlace.distance;
        }
      });
    }).catch(e => console.error(e));
  }, 10000);
});

onUnmounted(() => {
  if (autoRefresh) clearInterval(autoRefresh);
});
</script>
