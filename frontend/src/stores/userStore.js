import { defineStore } from 'pinia';
import placeService from '../services/user/placeService';
import locationService from '../services/user/locationService';
import checkInService from '../services/user/checkInService';
import api from '../services/api';

export const useUserStore = defineStore('user', {
  state: () => ({
    places: [],
    userLocation: null,
    activeCheckIn: null, 
    activePlaceConflict: null,
    geofenceError: null,
    checkInTime: null,
    checkOutTime: null,
    sessionDuration: '0m',
    timerInterval: null,
    inquiries: [],
    loading: {
      places: false,
      location: false,
      checkIn: false,
      inquiries: false
    }
  }),

  getters: {
    isCheckedIn: (state) => !!state.activeCheckIn,
    getNearbyPlaces: (state) => state.places,
    formattedCheckInTime: (state) => {
      if (!state.checkInTime) return null;
      return new Date(state.checkInTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
  },

  actions: {
    async detectLocation(force = false) {
      if (!force && this.userLocation) return this.userLocation;
      this.loading.location = true;
      try {
        const coords = await locationService.getCurrentPosition();
        this.userLocation = coords;
        return coords;
      } catch (error) {
        console.error('Location detection failed:', error);
        return null;
      } finally {
        this.loading.location = false;
      }
    },

    async fetchPlaces(force = false) {
      if (!force && this.places.length > 0) return;
      this.loading.places = true;
      try {
        const lat = this.userLocation?.lat;
        const lng = this.userLocation?.lng;
        const { data } = await placeService.getPlaces(lat, lng);
        this.places = data;
      } catch (error) {
        console.error('Failed to fetch places:', error);
      } finally {
        this.loading.places = false;
      }
    },

    async setCheckIn(placeId) {
      if (!this.userLocation) {
        throw new Error('GPS Location required for check-in');
      }
      this.loading.checkIn = true;
      try {
        this.activePlaceConflict = null;
        this.geofenceError = null;
        const { data } = await checkInService.checkIn(
          placeId, 
          this.userLocation.lat, 
          this.userLocation.lng
        );
        this.activeCheckIn = data.check_in;
        this.checkInTime = new Date().toISOString();
        this.startTimer();
        await this.fetchPlaces(true); 
      } catch (error) {
        if (error.response?.status === 400) {
          if (error.response.data.active_place) {
            this.activePlaceConflict = error.response.data.active_place;
          } else if (error.response.data.details) {
            this.geofenceError = error.response.data.details;
          }
        }
        throw error;
      } finally {
        this.loading.checkIn = false;
      }
    },

    async clearCheckIn() {
      this.loading.checkIn = true;
      try {
        await checkInService.checkOut();
        this.activeCheckIn = null;
        this.checkOutTime = new Date().toISOString();
        this.stopTimer();
        await this.fetchPlaces(true);
      } finally {
        this.loading.checkIn = false;
      }
    },

    async fetchInquiries() {
      this.loading.inquiries = true;
      try {
        const { data } = await api.get('/user/inquiries');
        this.inquiries = data;
      } catch (error) {
        console.error('Failed to fetch inquiries:', error);
      } finally {
        this.loading.inquiries = false;
      }
    },

    async fetchActiveCheckIn() {
      try {
        const { data } = await api.get('/user/active-checkin');
        if (data) {
          this.activeCheckIn = data;
          this.checkInTime = data.created_at;
          this.startTimer();
        }
      } catch (error) {
        console.error('Failed to fetch active session:', error);
      }
    },

    async initialize() {
      await this.detectLocation();
      await this.fetchActiveCheckIn();
      await this.fetchPlaces(true);
    },

    startTimer() {
      this.stopTimer();
      this.timerInterval = setInterval(() => {
        if (!this.checkInTime) return;
        const now = new Date();
        const start = new Date(this.checkInTime);
        const diffMs = now - start;
        const diffMins = Math.floor(diffMs / 60000);
        const hours = Math.floor(diffMins / 60) ;
        const mins = diffMins % 60;
        this.sessionDuration = hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
      }, 1000);
    },

    stopTimer() {
      if (this.timerInterval) {
        clearInterval(this.timerInterval);
        this.timerInterval = null;
      }
    }
  }
});
