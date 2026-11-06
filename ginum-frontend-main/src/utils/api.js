import axios from "axios";

// Export base API URL:
// - In development (import.meta.env.DEV): use VITE_API_URL or default to 'http://localhost:8081'.
// - In production: use same-origin relative paths ('') so all API calls route securely through HTTPS (/api/...).
const rawApiUrl = import.meta.env.DEV
  ? (import.meta.env.VITE_API_URL || 'http://localhost:8081')
  : (import.meta.env.VITE_API_URL && !import.meta.env.VITE_API_URL.includes('localhost') ? import.meta.env.VITE_API_URL : '');

export const apiUrl = rawApiUrl.replace(/\/+$/, '');

// Create an Axios instance
const api = axios.create({
  baseURL: apiUrl, // Set the base URL
  timeout: 10000, // Set a timeout of 10 seconds
  withCredentials: true, // Send cookies with cross-origin requests
  headers: {
    "Content-Type": "application/json", // Default content type
  },
});

// Add a Request Interceptor
api.interceptors.request.use(
  (config) => {
    // Attach Authorization token if it exists
    const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    // Handle errors before they are sent
    return Promise.reject(error);
  }
);

// Add a Response Interceptor
api.interceptors.response.use(
  (response) => {
    // Directly return response data
    return response.data;
  },
  (error) => {
    // Handle errors globally
    if (error.response) {
      if (error.response.status === 401) {
        // Handle unauthorized error
        console.warn("Unauthorized access. Redirecting to login.");
        window.location.href = "/login"; // Redirect to login page
      } else if (error.response.status === 500) {
        console.error("Server error:", error.response.data.message);
      }
    } else if (error.message.includes("timeout")) {
      console.error("Request timed out. Please try again.");
    } else {
      console.error("Network error:", error.message);
    }

    return Promise.reject(error);
  }
);

export default api;
