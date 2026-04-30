import api from '../api';

export default {
  createInquiry(placeId, data) {
    return api.post(`/places/${placeId}/inquiries`, data);
  },
  getUserInquiries() {
    return api.get('/user/inquiries');
  }
};
