import axios from 'axios';
import { useAuthStore } from '../../stores/auth';

const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
});

// Request Interceptor
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response Interceptor for Error Handling and Normalized Responses
apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // Global Error Handling
    if (error.response?.status === 401) {
      const authStore = useAuthStore();
      authStore.logout();
      window.location.href = '/login';
    } else if (error.response?.status === 403) {
      console.error('Forbidden: You do not have permission to perform this action.');
    } else {
      console.error('API Error:', error.response?.data?.message || error.message);
    }
    return Promise.reject(error);
  }
);

export default apiClient;
