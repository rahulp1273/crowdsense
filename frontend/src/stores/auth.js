import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isSuperAdmin: (state) => state.user?.is_super_admin === true,
    hasPermission: (state) => (permission) => {
      if (state.user?.is_super_admin) return true;
      if (!state.user?.permissions) return false;
      return state.user.permissions.includes(permission) || state.user.permissions.includes('*');
    }
  },
  actions: {
    async login(credentials) {
      const { data } = await api.post('/login', credentials);
      this.token = data.token;
      this.user = data.user;
      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));
    },
    async register(details) {
      const { data } = await api.post('/register', details);
      return data;
    },
    async resendOtp(email) {
      const { data } = await api.post('/resend-otp', { email });
      return data;
    },
    async verifyOtp(payload) {
      const { data } = await api.post('/verify-otp', payload);
      this.token = data.token;
      this.user = data.user;
      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));
    },
    async logout() {
      try {
        await api.post('/logout');
      } catch (e) {
        console.error('Logout API failed, clearing local state anyway');
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    },
    loadUser() {
      const userStr = localStorage.getItem('user');
      if (userStr) {
        this.user = JSON.parse(userStr);
      }
    }
  }
});
