import axios from 'axios';
import $ from 'jquery';
import Swal from 'sweetalert2';

window.axios = axios;
window.$ = window.jQuery = $;
window.Swal = Swal;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// On 419 (CSRF/session expired or logged in on another device): redirect to login
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 419) {
            const url = (error.response.data && error.response.data.redirect) || '/login';
            window.location.href = url;
        }
        return Promise.reject(error);
    }
);
