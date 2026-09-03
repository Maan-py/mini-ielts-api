import { createApp } from "vue";
import SpeakingApp from "./SpeakingApp.vue";
import router from "./router";

import "../css/app.css";

createApp(SpeakingApp).use(router).mount("#app");
