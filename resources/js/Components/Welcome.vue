<template>
    <div class="inventory-grid">
        <div v-for="shop in shopsWithStorages" :key="shop.id" class="shop-card">
            <h2  @click="getShop(shop.id)" class="shop-title">{{ shop.name }}</h2>
            <p class="shop-address">Adres: {{ shop.address }}</p>
            <div class="storages">
                <div v-for="storage in shop.storages" :key="storage.id" class="storage-item">
                    <h3 class="storage-name">{{ storage.name }}</h3>
                    <p class="storage-id">ID magazynu: {{ storage.id }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

const getShop = (id) => {
    router.push(`/shop/${id}`);
};
onMounted(async () => {
    await getShopsWithStorages()
});
const shopsWithStorages = ref([]);

const getShopsWithStorages = async () => {
    const response = await axios.get(`/api/shops`)
    shopsWithStorages.value = response.data
}
</script>
<style scoped>
.inventory-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 2rem;
}

.shop-card {
    background: linear-gradient(to right, #d1fae5, #f0fdf4);
    border-radius: 20px;
    box-shadow: 0 4px 6px rgba(0, 128, 0, 0.2);
    padding: 2rem;
    transition: transform 0.3s ease;
}

.shop-card:hover {
    transform: scale(1.05);
}

.shop-title {
    font-size: 2rem;
    color: #065f46;
    margin-bottom: 1rem;
}

.shop-address {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1.5rem;
}

.storages {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.storage-item {
    background: #ffffff;
    border-left: 6px solid #22c55e;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(34, 197, 94, 0.2);
    padding: 1rem;
    transition: background-color 0.3s ease;
}

.storage-item:hover {
    background-color: #bbf7d0;
}

.storage-name {
    font-size: 1.25rem;
    color: #16a34a;
    margin-bottom: 0.5rem;
}

.storage-id {
    font-size: 0.875rem;
    color: #6b7280;
}
</style>