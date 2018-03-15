import Vue from 'vue';
import Vuex from 'vuex';
import facebook from './modules/facebook/index';
Vue.use(Vuex);
export default new Vuex.Store({
    modules: {
        facebook
    },
});