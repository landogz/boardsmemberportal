import axios from 'axios';
import $ from 'jquery';
import Swal from 'sweetalert2';

window.axios = axios;
window.$ = window.jQuery = $;
window.Swal = Swal;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
