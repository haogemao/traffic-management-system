import Vue from 'vue';
import App from './app.vue';
import Routers from './router';
import Vuex from 'vuex';
import store from './vuex/store';

import ViewUI from 'view-design';
Vue.use(ViewUI);

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Util from './libs/util';
Vue.prototype.$util = Util;

import axios from 'axios';
Vue.prototype.$axios = axios;

import echarts from 'echarts'
Vue.prototype.$echarts = echarts

import 'view-design/dist/styles/iview.css';

/**
 * route configuration
 */
const RouterConfig = {
    mode: 'hash',
    routes: Routers
};

const router = new VueRouter(RouterConfig);

router.beforeEach((to, from, next) => {
    ViewUI.LoadingBar.start();
    Util.title(to.meta.title);
    if (to.matched.some(m => m.meta.requireAuth)) {
        if (window.sessionStorage.token) {
            if (to.path == '/login') {
                next({ path: '/person' })
            } else {
                next();
            }
        } else {
            next({ path: '/login' })
        }
    } else if (to.path == '/login' && window.sessionStorage.token) {
        next({ path: '/person' })
    }
    else {
        next();
    }
});

router.afterEach((to, from, next) => {
    ViewUI.LoadingBar.finish();
    window.scrollTo(0, 0);
});

/**
 * Configure the request interceptor
 */
axios.defaults.baseURL = "./"; //public url

axios.interceptors.request.use(config => {
    if (window.sessionStorage.token) {
        config.headers.common['token'] = window.sessionStorage.token; 
    } else {
    }
    return config
}, error => {
    // 异常
    if (error.response) {
        // switch (error.response.status) {
        //     case 401:
        //         sessionStorage.removeItem('token');
        //         router.replace({
        //             path: '/',
        //             query: { redirect: router.currentRoute.fullPath }//登录成功后跳入浏览的当前页面
        //         })
        // }
    }
})

/**
 * Configure the response interceptor
 */
axios.interceptors.response.use(response => {
    if (response.data.code != undefined && response.data.code != '') {
        if (response.data.code != 200) {
            window.sessionStorage.removeItem('token');//Removal of the local token
            router.push('/');
        }
    }
    return response
}, error => {
    // abnormal
    return Promise.reject(error)
})

new Vue({
    el: '#app',
    router: router,
    store,
    render: h => h(App)
});
