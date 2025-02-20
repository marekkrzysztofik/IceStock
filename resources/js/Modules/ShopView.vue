<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-6 text-blue-800">Stany magazynowe lodów</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="storage in storages" :key="storage.storage_id"
                class="p-4 bg-gradient-to-b from-blue-100 to-blue-50 rounded-xl shadow-md hover:scale-105 transition-transform duration-300">
                <h3 class="text-xl font-semibold mb-4 text-blue-700">{{ storage.storage_name }}</h3>
                <div v-if="storage.inventory.length > 0" class="space-y-4">
                    <div v-for="item in storage.inventory" :key="item.id" class="mb-2">
                        <div v-if="item.quantity > 0">
                            <span class="text-lg font-medium text-blue-600">{{ item.ice_cream_name }}</span>
                            <div class="relative w-full h-6 bg-gray-200 rounded-lg overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-400 to-green-600"
                                    :style="{ width: `${item.quantity * 20}%` }"></div>
                            </div>
                            <p class="text-sm text-gray-700 mt-2">Ilość kuwet: {{ item.quantity }}</p>
                        </div>
                    </div>
                </div>
                <div v-else class="text-gray-500">Brak dostępnych lodów</div>
                <Button label="Edytuj" icon="pi pi-pencil" class="mt-4 mr-2" @click="handleEdit(storage)" />


                <Button label="Transfer z" icon="pi pi-plus" class="mt-4" @click="handleTransfer(storage.storage_id)" />

            </div>
        </div>
        <Dialog v-model:visible="editDialogVisible" modal header="Edytuj inventory" :style="{ width: '600px' }">
            <DataTable v-model:value="selectedStorage.inventory" @cell-edit-complete="onCellEditComplete"
                editMode="cell" class="p-datatable-sm">
                <template #header>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xl font-bold"></span>
                    </div>
                </template>
                <Column style="width: 15rem;" field="ice_cream_name" header="Smak lodów" />
                <Column field="quantity" header="Ilość kuwet">
                    <template #editor="{ data, field }">
                        <InputNumber v-model="data[field]" :min="0" />
                    </template>
                </Column>

            </DataTable>
            <Button label="Zapisz" @click="saveAllChanges" class="mt-3" />
        </Dialog>
        <Dialog v-model:visible="transferDialogVisible" modal header="Transfer" :style="{ width: '600px' }">
            <div>
                <Select v-model="transferData.destination_storage_id" :options="storageOptions"
                    optionLabel="storage_name" optionValue="storage_id" placeholder="Magazyn docelowy" class="w-60" />
                <Select v-model="transferData.ice_cream_id" :options="iceCreamOptions" optionLabel="ice_cream_name"
                    optionValue="ice_cream_id" placeholder="Lody" class="w-60" />
                <Select v-model="transferData.quantity" :options="quantity" placeholder="Ilość" class="w-60" />
            </div>

            <Button label="Zapisz" @click="makeTransfer" class="mt-3" />
        </Dialog>

    </div>

</template>
<script setup>
import axios from "axios";
import { onMounted, reactive, ref } from "vue";

const props = defineProps({
    id: {
        type: String,
        default: 0,
    },
});

const storageOptions = ref([])
const quantity = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
const iceCreamOptions = ref([])
const storages = ref([]);
const editDialogVisible = ref(false);
const transferDialogVisible = ref(false);
let selectedStorage = ref(null);
const editedCells = ref([]);
const transferData = reactive({
    source_storage_id: null,
    destination_storage_id: null,
    ice_cream_id: null,
    quantity: null
});

onMounted(async () => {
    await getStorages()
});


const handleEdit = (storage) => {
    selectedStorage = JSON.parse(JSON.stringify(storage));
    editDialogVisible.value = true;
};

const handleTransfer = (id) => {
    transferData.source_storage_id = id
    iceCreamOptions.value = storages.value.find((storage) => storage.storage_id == id)?.inventory.filter(item => item.quantity > 0) || [];
    storageOptions.value = storages.value.filter((storage) => storage.storage_id != id);
    transferDialogVisible.value = true;
};

const makeTransfer = async () => {
    try {
        const response = await axios.post('/api/transfers', transferData);
        if (response.status === 200) {
            await refreshData();

        } else {
            alert('Wystąpił problem podczas transferu.');
        }
    } catch (error) {
        console.error("Błąd:", error.response?.data || error.message);
        alert("Błąd: " + JSON.stringify(error.response?.data));
    }
};

const onCellEditComplete = (event) => {
    console.log(event);
    let { newData, newValue, data, field } = event;
    data[field] = newValue;
    const existing = editedCells.value.find(item => item.id === newData.id);
    if (existing) {
        Object.assign(existing, newData);
    } else {
        editedCells.value.push(newData);
    }
};

const saveAllChanges = async () => {
    if (editedCells.value.length === 0) {
        alert('Brak zmian do zapisania.');
        return;
    }
    try {
        // Wysyłamy pełne dane, a nie tylko id i quantity
        const response = await axios.post('/api/inventories/bulk-update', { updatedRows: editedCells.value });

        if (response.status === 200) {
            alert('Zmiany zapisane pomyślnie!');
            await refreshData();

        } else {
            alert('Wystąpił problem podczas zapisywania.');
        }
    } catch (error) {
        console.error('Błąd podczas zapisywania zmian:', error);
        alert('Błąd podczas komunikacji z serwerem.');
    }
};

function resetTransferData() {
    Object.keys(transferData).forEach(key => transferData[key] = null);
}

async function getStorages() {
    try {
        const response = await axios.get(`/api/storages/${props.id}`);
        storages.value = response.data;
    } catch (error) {
        console.error("Błąd pobierania magazynów:", error.response?.data || error.message);
        alert("Nie udało się pobrać danych magazynowych.");
    }
}
async function refreshData() {
    await getStorages();
    resetTransferData()
    editedCells.value = [];
    transferDialogVisible.value = false;
}
</script>