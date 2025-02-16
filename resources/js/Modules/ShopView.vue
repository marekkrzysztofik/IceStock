<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-6 text-blue-800">Stany magazynowe lodów</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="storage in storages" :key="storage.storage_id"
                class="p-4 bg-gradient-to-b from-blue-100 to-blue-50 rounded-xl shadow-md hover:scale-105 transition-transform duration-300">
                <h3 class="text-xl font-semibold mb-4 text-blue-700">{{ storage.storage_name }}</h3>
                <div v-if="storage.inventory.length > 0" class="space-y-4">
                    <div v-for="item in storage.inventory" :key="item.id" class="mb-2">
                        <span class="text-lg font-medium text-blue-600">{{ item.ice_cream_name }}</span>
                        <div class="relative w-full h-6 bg-gray-200 rounded-lg overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-green-400 to-green-600"
                                :style="{ width: `${item.quantity * 20}%` }"></div>
                        </div>
                        <p class="text-sm text-gray-700 mt-2">Ilość kuwet: {{ item.quantity }}</p>
                    </div>
                </div>
                <div v-else class="text-gray-500">Brak dostępnych lodów</div>
                <Button label="Edytuj inventory" icon="pi pi-pencil" class="mt-4 mr-2"
                    @click="openEditDialog(storage)" />


                <Button label="Dodaj inventory" icon="pi pi-plus" class="mt-4" @click="openAddDialog(storage)" />

            </div>
        </div>
        <Dialog v-model:visible="editDialogVisible" modal header="Edytuj inventory" :style="{ width: '600px' }">
            <DataTable v-model:value="selectedStorage.inventory" @cell-edit-complete="onCellEditComplete"
                editMode="cell" class="p-datatable-sm">
                <Column style="width: 15rem;" field="ice_cream_name" header="Smak lodów" />
                <Column field="quantity" header="Ilość kuwet">
                    <template #editor="{ data, field }">
                        <InputNumber v-model="data[field]" :min="0" />
                    </template>
                </Column>

            </DataTable>
            <Button label="Zapisz" @click="saveAllChanges" class="mt-3" />

        </Dialog>

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
const storages = ref([]);
const editDialogVisible = ref(false);
const addDialogVisible = ref(false);
let selectedStorage = ref(null);
const newInventory = ref({ name: '', quantity: 0 });
const editedCells = ref([]);
const openEditDialog = (storage) => {
    selectedStorage = JSON.parse(JSON.stringify(storage));
    editDialogVisible.value = true;
};

const openAddDialog = (storage) => {
    selectedStorage.value = storage;
    addDialogVisible.value = true;
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
            await getStorages();
            editedCells.value = [];
        } else {
            alert('Wystąpił problem podczas zapisywania.');
        }
    } catch (error) {
        console.error('Błąd podczas zapisywania zmian:', error);
        alert('Błąd podczas komunikacji z serwerem.');
    }
    editDialogVisible.value = false;
};
    

const addInventory = () => {
    if (newInventory.value.name && newInventory.value.quantity > 0) {
        const newId = Math.max(0, ...selectedStorage.value.inventory.map(i => i.inventory_id)) + 1;
        selectedStorage.value.inventory.push({
            inventory_id: newId,
            ice_cream_name: newInventory.value.name,
            quantity: newInventory.value.quantity,
            ice_cream_id: newId
        });
        newInventory.value = { name: '', quantity: 0 };
        addDialogVisible.value = false;
    }
};
onMounted(async () => {
    await getStorages()
});


const getStorages = async () => {
    const response = await axios.get(`/api/storages/${props.id}`)
    storages.value = response.data
}
</script>