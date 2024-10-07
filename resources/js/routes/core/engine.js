import VueRouter from "vue-router";
import Vue from "vue";
Vue.use(VueRouter);


let routes = [];

import users from "../users";
routes = routes.concat(users);

import messages from "../messages";
routes = routes.concat(messages);

import roles from "../roles";
routes = routes.concat(roles);

import dashboard from "../dashboard";
routes = routes.concat(dashboard);


const router = new VueRouter({
    routes :  routes
});

// router.beforeEach((to, from, next) => {
//     window.scrollTo( 0 , 0 );
//     if (to.path === "/") {
//         next("/messages");
//     }
//     next();
// });

export default router;
