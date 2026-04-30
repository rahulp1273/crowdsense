import api from '../api';

export default {
  checkIn(placeId, lat, lng) {
    return api.post('/checkin', { place_id: placeId, lat, lng });
  },
  checkOut() {
    return api.post('/checkout');
  }
};
