import axios from 'axios';

const http = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

http.interceptors.response.use(
    (response) => response.data,
    (error) => {
        if (error.response?.status === 401 || error.response?.status === 419) {
            window.location.href = '/login';
        }

        return Promise.reject(error.response?.data || error);
    }
);

export default http;