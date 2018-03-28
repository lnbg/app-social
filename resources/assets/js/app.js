import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import VueAxios from 'vue-axios'
import store from './vuex/store.js'
import Numeral from 'numeral'
import VueMixins from './mixins/mixins'
import { EventBus } from './global/event-bus'

// router setup
import routes from './routes/routes'
// library imports
import App from './App.vue'
// plugin setup
Vue.use(VueRouter)
Vue.use(store)
Vue.use(VueAxios, axios)
// set axios as global
window.axios = axios;
axios.interceptors.response.use(function (response) {
    if (response.data.status_code === 410) { 
        console.log("error .......")
    }
    return response;
}, function (error) {
    return Promise.reject(error)
});
// configure router
const router = new VueRouter({
    routes, // short for routes: routes
})
router.beforeEach((to, from, next) => {
    next();
})
Vue.mixin(VueMixins)
Vue.filter("currency", function (value) {
    return Numeral(value).format("0,0[.]00");
});

window.NumberFormatter = function(num) {
    return num.replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
    });
}

Vue.config.productionTip = false
/* eslint-disable no-new */
new Vue({
    store,
    el: '#app',
    render: h => h(App),
    router,
    EventBus,
})