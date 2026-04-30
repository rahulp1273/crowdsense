import api from './apiClient';

export default {
  getInquiries(params = {}) {
    return api.get('/admin/inquiries', { params });
  },
  markSeen(id) {
    return api.patch(`/admin/inquiries/${id}/seen`);
  }
};
