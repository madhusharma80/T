import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api', // Your API base URL
  headers: {
    'Content-Type': 'application/json',
  }
});

// Attach the token to each request
api.interceptors.request.use(
  (config) => {
    // Get token from localStorage (or sessionStorage)
    const token = localStorage.getItem('token');

    // If token exists, attach it to the request header as Authorization Bearer token
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }

    return config;
  },
  (error) => Promise.reject(error)
);

// CSRF cookie setup (for Sanctum cookie-based authentication)
api.interceptors.request.use(
  async (config) => {
    // If CSRF cookie is not present, request it before making other requests
    if (!document.cookie.includes('XSRF-TOKEN')) {
      // Ensure CSRF token is fetched from Sanctum
      await axios.get('/sanctum/csrf-cookie');  // This will set the CSRF cookie in the browser
    }

    return config;
  },
  (error) => Promise.reject(error)
);

export default api;
