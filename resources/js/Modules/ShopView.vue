<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-6 text-blue-800">Stany magazynowe lodów</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="storage in storages" :key="storage.storage_id" class="p-4 bg-gradient-to-b from-blue-100 to-blue-50 rounded-xl shadow-md hover:scale-105 transition-transform duration-300">
            <h3 class="text-xl font-semibold mb-4 text-blue-700">{{ storage.storage_name }}</h3>
            <div v-if="storage.inventory.length > 0" class="space-y-4">
              <div v-for="item in storage.inventory" :key="item.inventory_id" class="mb-2">
                <span class="text-lg font-medium text-blue-600">{{ item.ice_cream_name }}</span>
                <div class="relative w-full h-6 bg-gray-200 rounded-lg overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-green-400 to-green-600" :style="{ width: `${item.quantity * 20}%` }"></div>
                </div>
                <p class="text-sm text-gray-700 mt-2">Ilość kuwet: {{ item.quantity }}</p>
              </div>
            </div>
            <div v-else class="text-gray-500">Brak dostępnych lodów</div>
          </div>
        </div>
      </div>
</template>
<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const props = defineProps({
    id: {
        type: String,
        default: 0,
    },
});
onMounted(async () => {
    await getStorages()
});
const storages = ref([]);

const getStorages = async () => {
    const response = await axios.get(`/api/storages/${props.id}`)
    storages.value = response.data
}
</script>