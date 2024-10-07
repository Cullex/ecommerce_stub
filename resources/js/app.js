import Vue from "vue";
import router from "./routes/core/engine";
import { Plugin } from 'vue-fragment';
Vue.use(Plugin);
require('./boot/axios');
require('./boot/alerts');
require('./boot/filters');
require('./boot/helpers');

import Login  from "./components/auth/Login";
import Reset from "./components/auth/Reset";

window.vue = new Vue({
    router : router,
    components : { Login , Reset } ,
    el: '#app',
    methods : {
        logout : function () {
            document.getElementById('logout-form').submit();
        }
    }
});
