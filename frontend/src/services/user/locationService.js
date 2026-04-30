export default {
  getCurrentPosition() {
    return new Promise((resolve, reject) => {
      if (!navigator.geolocation) {
        reject(new Error('Geolocation is not supported by your browser'));
        return;
      }

      navigator.geolocation.getCurrentPosition(
        (position) => {
          resolve({
            lat: position.coords.latitude,
            lng: position.coords.longitude
          });
        },
        (error) => {
          reject(error);
        },
        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
      );
    });
  },

  watchPosition(callback) {
    if (!navigator.geolocation) return null;
    return navigator.geolocation.watchPosition(
      (position) => {
        callback({
          lat: position.coords.latitude,
          lng: position.coords.longitude
        });
      },
      (error) => console.error('GPS Watch Error:', error),
      { enableHighAccuracy: true }
    );
  },

  clearWatch(id) {
    if (id !== null) navigator.geolocation.clearWatch(id);
  }
};
