import { defineStore } from 'pinia';
import placeService from '../services/admin/placeService';
import inquiryService from '../services/admin/inquiryService';
import dashboardService from '../services/admin/dashboardService';

export const useAdminStore = defineStore('admin', {
  state: () => ({
    stats: {},
    places: [],
    inquiries: [],
    loading: {
      stats: false,
      places: false,
      inquiries: false
    },
    lastFetched: {
      stats: null,
      places: null,
      inquiries: null
    }
  }),
  actions: {
    async fetchStats(force = false) {
      if (!force && this.lastFetched.stats && (Date.now() - this.lastFetched.stats < 60000)) return;
      this.loading.stats = true;
      try {
        const { data } = await dashboardService.getStats();
        this.stats = data;
        this.lastFetched.stats = Date.now();
      } catch (e) {
        console.error(e);
        throw e;
      } finally {
        this.loading.stats = false;
      }
    },
    async fetchPlaces(force = false) {
      if (!force && this.lastFetched.places && (Date.now() - this.lastFetched.places < 60000)) return;
      this.loading.places = true;
      try {
        const { data } = await placeService.getPlaces();
        this.places = data;
        this.lastFetched.places = Date.now();
      } catch (e) {
        console.error(e);
        throw e;
      } finally {
        this.loading.places = false;
      }
    },
    async fetchInquiries(params = {}) {
      this.loading.inquiries = true;
      try {
        const { data } = await inquiryService.getInquiries(params);
        this.inquiries = data.data || data; // handle paginated response
        this.lastFetched.inquiries = Date.now();
      } catch (e) {
        console.error(e);
      } finally {
        this.loading.inquiries = false;
      }
    },
    async markInquirySeen(id) {
      try {
        await inquiryService.markSeen(id);
        const inq = this.inquiries.find(i => i.id === id);
        if (inq) inq.status = 'seen';
        
        if (this.stats.unread_inquiries > 0) {
          this.stats.unread_inquiries--;
        }
      } catch (e) {
        console.error(e);
        throw e;
      }
    }
  }
});
