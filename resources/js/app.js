
import { createApp } from "vue";
import router from "./router";

import App from "@/Components/App.vue";

import PrimeVue from "primevue/config";



import "primeicons/primeicons.css"; //icons
import Aura from '@primevue/themes/aura';
import ToastService from "primevue/toastservice";
import Toast from "primevue/toast";
import ConfirmationService from "primevue/confirmationservice";
import Dialog from 'primevue/dialog';
import ScrollPanel from 'primevue/scrollpanel';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';  


const app = createApp(App);
app.use(router);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            prefix: 'p',
            darkModeSelector: 'system',
            cssLayer: false
        }
    }
 });
app.use(ToastService);
app.use(ConfirmationService);
app.component("Column", Column);
app.component("ColumnGroup", ColumnGroup);
app.component("Row", Row);
app.component("DataTable", DataTable);
app.component("Button", Button);
app.component("InputText", InputText);
app.component("InputNumber", InputNumber);
app.component("Dialog", Dialog);
app.component("Toast", Toast);
app.component("ScrollPanel", ScrollPanel);


app.mount("#app");

