import { createRouter, createWebHistory } from "vue-router";
import AttemptList from "./components/AttemptList.vue";
import AttemptDetail from "./components/AttemptDetail.vue";
import SubmitAnswer from "./components/SubmitAnswer.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import { isAuthenticated } from "./lib/auth";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "login",
            component: Login,
        },
        {
            path: "/speaking",
            name: "attempts",
            component: AttemptList,
            meta: { requiresAuth: true },
        },
        {
            path: "/speaking/login",
            redirect: "/",
        },
        {
            path: "/register",
            name: "register",
            component: Register,
        },
        {
            path: "/speaking/submit",
            name: "submit",
            component: SubmitAnswer,
            meta: { requiresAuth: true },
        },
        {
            path: "/speaking/:id",
            name: "attempt-detail",
            component: AttemptDetail,
            props: true,
            meta: { requiresAuth: true },
        },
    ],
});

router.beforeEach((to) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        return { name: "login" };
    }
    if ((to.name === "login" || to.name === "register") && isAuthenticated()) {
        return { name: "attempts" };
    }
});

export default router;
