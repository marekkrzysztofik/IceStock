import { createRouter, createWebHistory } from "vue-router";
import Home from "@/Components/Home.vue"
import Welcome from "@/Components/Welcome.vue"


const routes = [
    {
        path: "/",
        component: Welcome,
        name: "Welcome",
       
    },
    {
        path: "/home",
        component: Home,
        name: "Home",
       
    },
   
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// router.beforeEach(to => {
//     if (to.meta.requiresAuth && !localStorage.getItem("token")) {
//         return { name: "Welcome" };
//     }
//     if (to.meta.requiresAuth == false && localStorage.getItem("token")) {
//         return { name: "Home" };
//     }
// });

export default router;
