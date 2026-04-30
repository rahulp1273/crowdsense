import api from '../api';

export default {
  getProfile() {
    return api.get('/user/profile');
  },
  updateProfile(data) {
    return api.put('/user/profile', data);
  }
};
