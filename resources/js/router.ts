import { createRouter, createWebHistory } from "vue-router";
import AttemptList from "./components/AttemptList.vue";
import AttemptDetail from "./components/AttemptDetail.vue";
import SubmitAnswer from "./components/SubmitAnswer.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/speaking",
            name: "attempts",
            component: AttemptList,
        },
        {
            path: "/speaking/login",
            name: "login",
            component: Login,
        },
        {
            path: "/speaking/register",
            name: "register",
            component: Register,
        },
        {
            path: "/speaking/submit",
            name: "submit",
            component: SubmitAnswer,
        },
        {
            path: "/speaking/:id",
            name: "attempt-detail",
            component: AttemptDetail,
            props: true,
        },
    ],
});

export default router;
