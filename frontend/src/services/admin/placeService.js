import api from './apiClient';

export default {
  getPlaces() {
    return api.get('/admin/places');
  },
  createPlace(data) {
    return api.post('/admin/places', data);
  },
  updatePlace(id, data) {
    return api.put(`/admin/places/${id}`, data);
  },
  deletePlace(id) {
    return api.delete(`/admin/places/${id}`);
  },
  lookupPincode(pincode, country = 'IN') {
    return api.get(`/admin/lookup-pincode/${pincode}`, { params: { country } });
  }
};
