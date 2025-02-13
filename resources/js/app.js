
import { createApp } from "vue";
import router from "./router";

import App from "@/Components/App.vue";

import PrimeVue from "primevue/config";



import "primeicons/primeicons.css"; //icons
import Dialog from "primevue/dialog";
import CascadeSelect from "primevue/cascadeselect";
import InputText from "primevue/inputtext";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup"; //optional for column grouping
import Row from "primevue/row"; //optional for row
import ToastService from "primevue/toastservice";
import Toast from "primevue/toast";
import ConfirmationService from "primevue/confirmationservice";
import Dropdown from "primevue/dropdown";
import SplitButton from 'primevue/splitbutton';
import Calendar from 'primevue/calendar';
import InputNumber from 'primevue/inputnumber';
import Knob from 'primevue/knob';
import Chart from 'primevue/chart';
import ScrollPanel from 'primevue/scrollpanel';
import RadioButton from 'primevue/radiobutton';

const app = createApp(App);
app.use(router);
app.use(PrimeVue);
app.use(ToastService);
app.use(ConfirmationService);

app.mount("#app");

