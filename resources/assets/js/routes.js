router.map({
    '/': {
        auth: true,
        component: Vue.component('home', require('./components/Layout.vue')),
        subRoutes: {
            '/': {
                component: Vue.component('home', require('./components/Home.vue'))
            },
            '/users': {
                auth: ['manager', 'admin'],
                component: Vue.component('users', require('./components/Users.vue'))
            },
            '/expenses': {
                auth: 'admin',
                component: Vue.component('expenses', require('./components/Expenses.vue'))
            }
        }
    },
    '/login': {
        component: Vue.component('home', require('./components/Login.vue'))
    },
    '/register': {
        component: Vue.component('home', require('./components/Register.vue'))
    }
});

router.redirect({
    '*': '/'
});
