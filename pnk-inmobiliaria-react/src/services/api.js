import axios from 'axios';
import Cookies from 'js-cookie';

// Configuración base de la API
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:3001/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor para agregar el token a las peticiones
api.interceptors.request.use(
  (config) => {
    const token = Cookies.get('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor para manejar respuestas
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      Cookies.remove('token');
      Cookies.remove('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// API de autenticación
export const authAPI = {
  login: (credentials) => api.post('/auth/login', credentials),
  register: (userData, userType) => api.post(`/auth/register/${userType}`, userData),
  recoverPassword: (email) => api.post('/auth/recover-password', { email }),
  resetPassword: (token, password) => api.post('/auth/reset-password', { token, password }),
};

// API de propiedades
export const propertiesAPI = {
  getAll: (filters = {}) => api.get('/properties', { params: filters }),
  getById: (id) => api.get(`/properties/${id}`),
  create: (propertyData) => api.post('/properties', propertyData),
  update: (id, propertyData) => api.put(`/properties/${id}`, propertyData),
  delete: (id) => api.delete(`/properties/${id}`),
  uploadImages: (id, images) => {
    const formData = new FormData();
    images.forEach((image, index) => {
      formData.append(`images`, image);
    });
    return api.post(`/properties/${id}/images`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  },
};

// API de ubicaciones
export const locationsAPI = {
  getRegions: () => api.get('/locations/regions'),
  getProvinces: (regionId) => api.get(`/locations/provinces/${regionId}`),
  getCommunes: (provinceId) => api.get(`/locations/communes/${provinceId}`),
  getSectors: (communeId) => api.get(`/locations/sectors/${communeId}`),
};

// API de usuarios
export const usersAPI = {
  getAll: () => api.get('/users'),
  getById: (id) => api.get(`/users/${id}`),
  update: (id, userData) => api.put(`/users/${id}`, userData),
  delete: (id) => api.delete(`/users/${id}`),
};

// API de gestores
export const gestoresAPI = {
  getAll: () => api.get('/gestores'),
  getById: (id) => api.get(`/gestores/${id}`),
  update: (id, gestorData) => api.put(`/gestores/${id}`, gestorData),
  delete: (id) => api.delete(`/gestores/${id}`),
};

// API de propietarios
export const propietariosAPI = {
  getAll: () => api.get('/propietarios'),
  getById: (id) => api.get(`/propietarios/${id}`),
  update: (id, propietarioData) => api.put(`/propietarios/${id}`, propietarioData),
  delete: (id) => api.delete(`/propietarios/${id}`),
};

export default api;