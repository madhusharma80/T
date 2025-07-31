import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {'Content-Type': 'application/json'}
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');  // Ensure token is stored in localStorage during login

    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }

    return config;
  },
  (error) => Promise.reject(error)
);

export default api;
