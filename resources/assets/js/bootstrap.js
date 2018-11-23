

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');
require('vue-resource');

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.http.interceptors.push((request, next) => {
    request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;

    next();
});


window.VueRouter = require('vue-router');
window.router = new VueRouter();


/* progress bar to show on ajax calls */
window.NProgress = require('nprogress');
Vue.http.interceptors.push((request, next)  => {
    NProgress.start();
    $('form .btn').prop('disabled', true).addClass('disabled');

    next((response) => {
        NProgress.done();
        $('form .btn').prop('disabled', false).removeClass('disabled');
    });
});

/* jwt auth */
window.Auth = require('vue-jwt-auth');
Vue.use(Auth, {
    fetchUrl: '/api/auth/user',
    tokenUrl: '/api/auth/token',
    loginUrl: '/api/auth/login',
    registerUrl: '/api/auth/register',
    rolesVar: 'role'
}, router);

/* datetimepicker */
import DatetimepickerDirective from './directives/datetimepicker.js';
Vue.directive('datetimepicker', DatetimepickerDirective);