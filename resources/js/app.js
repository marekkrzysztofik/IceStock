
import { createApp } from "vue";
import router from "./router";

import App from "@/Components/App.vue";

import PrimeVue from "primevue/config";



import "primeicons/primeicons.css"; //icons

import ToastService from "primevue/toastservice";
import Toast from "primevue/toast";
import ConfirmationService from "primevue/confirmationservice";

import ScrollPanel from 'primevue/scrollpanel';


const app = createApp(App);
app.use(router);
app.use(PrimeVue);
app.use(ToastService);
app.use(ConfirmationService);
app.use(ScrollPanel);

app.mount("#app");

