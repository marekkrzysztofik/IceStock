<template>
    <nav class="flex items-center justify-around p-4 shadow-lg bg-gradient-to-r from-blue-500 to-blue-700 text-white fixed top-0 left-0 w-full z-10">
        <div class="flex items-center space-x-4">
            <span class="text-2xl font-bold hover:text-blue-300 transition-colors duration-300">IceStock</span>
        </div>
        <ul class="flex space-x-6 text-lg">
            <li><a href="/" class="hover:underline hover:text-blue-300 transition-colors duration-300">Home</a></li>
            <li><a href="/inventory"
                    class="hover:underline hover:text-blue-300 transition-colors duration-300">Inventory</a></li>
            <li><a href="/transfers"
                    class="hover:underline hover:text-blue-300 transition-colors duration-300">Transfers</a></li>
            <li><a href="/reports"
                    class="hover:underline hover:text-blue-300 transition-colors duration-300">Reports</a></li>
        </ul>
        <div class="flex items-center space-x-4">
            <button
                class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-100 hover:scale-105 transition-all duration-300">Profile</button>
            <button
                class="px-4 py-2 bg-red-500 rounded-lg hover:bg-red-600 hover:scale-105 transition-all duration-300">Logout</button>
        </div>
    </nav>
    <div class="mt-20">
        <router-view/>
    </div>
 
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


