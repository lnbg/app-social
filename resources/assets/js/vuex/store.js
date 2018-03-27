import Vue from 'vue';
import Vuex from 'vuex';
import facebook from './modules/facebook/index';
import instagram from './modules/instagram/index';
Vue.use(Vuex);
export default new Vuex.Store({
    modules: {
        facebook,
        instagram
    },
});