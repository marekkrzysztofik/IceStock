<template>
  <h1>siema</h1>
  
  <router-view></router-view>
</template>
<script setup>
import { ref, onMounted, } from "vue";
import { useRouter, useRoute } from "vue-router";
import { usePrimeVue } from "primevue/config";

const visible = ref(false)
const componentKey = ref(0)
const router = useRouter();
const route = useRoute()
const menuItems = [
    { path: "/home", label: "Home", icon: 'pi pi-bars mr-2' },
    { path: "/budgets", label: "Budgets", icon: 'pi pi-dollar mr-2' },
    { path: "/categories", label: "Categories", icon: 'pi pi-th-large mr-2' },
    { path: "/transactions", label: "Transactions", icon: 'pi pi-book mr-2' },
    { path: "/analytics", label: "Analytics", icon: 'pi pi-database mr-2' },
    { path: "/expenses", label: "Expenses", icon: 'pi pi-wallet mr-2' },
    { path: "/login", label: "Log in", icon: 'pi pi-user mr-2' },
] 
onMounted(() => {
    polishDateFormat();
});
const logout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("userID");
    router.push("/");
    location.reload();
};
const newTransaction = () => {
    visible.value = true;
}
const polishDateFormat = () => {
    const primevue = usePrimeVue();
    primevue.config.locale.dateFormat = "dd/mm/yy";
}
</script>


