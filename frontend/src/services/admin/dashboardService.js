import api from './apiClient';

export default {
  getStats() {
    return api.get('/admin/dashboard');
  },
  getAnalytics() {
    return api.get('/admin/analytics');
  }
};
