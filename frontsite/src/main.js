import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";

// You can use the following starter router instead of the default one as a clean starting point
import router from "./router/starter";
// import router from "./router/starter";
// import router from "./router";

// Template components
import BaseBlock from "@/components/BaseBlock.vue";
import BaseBackground from "@/components/BaseBackground.vue";
import BasePageHeading from "@/components/BasePageHeading.vue";

// Vue Paginate
import Paginate from "vuejs-paginate-next";

// Vue Datepicker
import VueDatePicker from '@vuepic/vue-datepicker';

// Template directives
import clickRipple from "@/directives/clickRipple";

// Bootstrap framework
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

import moment from "moment";
window.moment = moment;

import axios from "axios";
import { baseUrl } from "@/stores/utils.js";
window.axios = axios;

window.axios.defaults.baseURL = `${baseUrl}/`;
window.axios.defaults.headers.common["ngrok-skip-browser-warning"] = '1'
window.axios.defaults.headers.common["Accept"] = "application/json";
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Craft new application
const app = createApp(App);

// Register global components
app.component("BaseBlock", BaseBlock);
app.component("BaseBackground", BaseBackground);
app.component("BasePageHeading", BasePageHeading);
app.component("Datepicker", VueDatePicker);
app.component("paginate", Paginate);

// Register global directives
app.directive("click-ripple", clickRipple);

// Use Pinia and Vue Router
app.use(createPinia());
app.use(router);

// ..and finally mount it!
app.mount("#app");
