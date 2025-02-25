<template>
    <nav class="nav-bar">
        <div class="nav-brand">
            <span class="brand-name">IceStock</span>
        </div>
        <ul class="nav-links">
            <li><a href="/">Home</a></li>
            <li><a href="/home">Inventory</a></li>
            <li><a href="/transfers">Transfers</a></li>
            <li><a href="/reports">Reports</a></li>
        </ul>
        <div class="nav-actions">
            <button class="profile-btn">Profile</button>
            <button class="logout-btn">Logout</button>
        </div>
    </nav>
    <div class="content-wrapper">
        <router-view/>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { usePrimeVue } from "primevue/config";

const visible = ref(false);
const componentKey = ref(0);
const router = useRouter();
const route = useRoute();

const menuItems = [
    { path: "/home", label: "Home", icon: 'pi pi-bars' },
    { path: "/budgets", label: "Budgets", icon: 'pi pi-dollar' },
    { path: "/categories", label: "Categories", icon: 'pi pi-th-large' },
    { path: "/transactions", label: "Transactions", icon: 'pi pi-book' },
    { path: "/analytics", label: "Analytics", icon: 'pi pi-database' },
    { path: "/expenses", label: "Expenses", icon: 'pi pi-wallet' },
    { path: "/login", label: "Log in", icon: 'pi pi-user' },
];

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
};

const polishDateFormat = () => {
    const primevue = usePrimeVue();
    primevue.config.locale.dateFormat = "dd/mm/yy";
};
</script>

<style scoped>
.nav-bar {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 16px;
    background: #2D336B;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.nav-brand .brand-name {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.nav-brand .brand-name:hover {
    color: #93c5fd;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 24px;
    font-size: 18px;
}

.nav-links li a {
    text-decoration: none;
    color: white;
    transition: color 0.3s ease;
}

.nav-links li a:hover {
    color: #93c5fd;
    text-decoration: underline;
}

.nav-actions {
    display: flex;
    gap: 16px;
}

.profile-btn, .logout-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.profile-btn {
    background-color: white;
    color: #2563eb;
}

.profile-btn:hover {
    background-color: #dbeafe;
    transform: scale(1.05);
}

.logout-btn {
    background-color: #ef4444;
    color: white;
}

.logout-btn:hover {
    background-color: #dc2626;
    transform: scale(1.05);
}

.content-wrapper {
    margin-top: 80px;
}
</style>
