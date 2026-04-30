import api from '../api';

export default {
  getPlaces(lat = null, lng = null) {
    const params = lat && lng ? { lat, lng } : {};
    return api.get('/places', { params });
  },
  getPlaceCrowd(id) {
    return api.get(`/places/${id}/crowd`);
  }
};
