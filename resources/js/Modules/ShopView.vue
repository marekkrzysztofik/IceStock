<template>
    <div class="p-6">
        <h1>Stany magazynowe lodów</h1>

        <div class="">
            <div v-for="storage in storages" :key="storage.storage_id"
                class="p-4 bg-gradient-to-b from-blue-100 to-blue-50 rounded-xl shadow-md ">
                <h3 class="text-xl font-semibold mb-4 text-blue-700">{{ storage.storage_name }}</h3>
                <div v-if="storage.inventory.length > 0" class="grid grid-cols-5 gap-4  justify-items-center m-auto">
                    <div v-for="item in storage.inventory" :key="item.id" class="relative p-4 rounded-2xl backdrop-blur-lg bg-white/20 shadow-lg border border-white/30 glass-card w-full h-40 flex flex-col justify-between items-center">
                        <div v-if="item.quantity > 0" class="flex flex-col">
                            <span class="text-lg font-medium text-blue-600">{{ item.ice_cream_name }}</span>
                            <div class="relative flex items-center space-x-1">
                                <div 
                                v-for="n in item.quantity" 
                                :key="n" 
                                class="w-5 h-8 rounded-md backdrop-blur-md border border-white/20 bg-blue-500/40 kuwet-shadow">
                               
                              </div>
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
                        <InputNumber v-model="data[field]" :min="initialValue" />
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
const initialValue = ref();
const storageOptions = ref([])
const quantity = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
const iceCreamOptions = ref([])
const storages = ref([]);
const editDialogVisible = ref(false);
const transferDialogVisible = ref(false);
let selectedStorage = ref(null);
const productionData = reactive({
    shop_id: parseInt(props.id),
    destination_storage_id: null,
    ice_creams: [],
});
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

    productionData.destination_storage_id = storage.storage_id;
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
            await refreshTransferData();

        } else {
            alert('Wystąpił problem podczas transferu.');
        }
    } catch (error) {
        console.error("Błąd:", error.response?.data || error.message);
        alert("Błąd: " + JSON.stringify(error.response?.data));
    }
};

const onCellEditComplete = (event) => {
    let { newValue, data, field } = event;
    initialValue.value = data[field];
    data[field] = newValue;
    const existingIndex = productionData.ice_creams.findIndex(item => item.ice_cream_id === data.ice_cream_id);
    if (existingIndex !== -1) {
        productionData.ice_creams[existingIndex].quantity = data.quantity;
    } else {
        // Jeśli nie istnieje, dodajemy nowy wpis
        productionData.ice_creams.push({
            ice_cream_id: data.ice_cream_id,
            quantity: data.quantity
        });
    }
};

const saveAllChanges = async () => {
    if (productionData.ice_creams.length === 0) {
        alert('Brak zmian do zapisania.');
        return;
    }
    try {
        // Wysyłamy pełne dane, a nie tylko id i quantity
        const response = await axios.post('/api/productions', productionData);

        if (response.status === 200) {
            alert('Zmiany zapisane pomyślnie!');
            await refreshProductionData();

        } else {
            console.log(response.status)
        }
    } catch (error) {
        console.error('Błąd podczas zapisywania zmian:', error);
        alert('Błąd podczas komunikacji z serwerem.');
    }
};
function resetProductionData() {
    editDialogVisible.value = false;
    productionData.shop_id = props.id;
    productionData.destination_storage_id = null;
    productionData.ice_creams = [];

}
function resetTransferData() {
    Object.keys(transferData).forEach(key => transferData[key] = null);
    transferDialogVisible.value = false;
}
function storagesWithQuantity() {
    return storages.value.filter(storage => storage.inventory.length > 0);
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

async function refreshProductionData() {
    await getStorages();
    resetProductionData()
}
async function refreshTransferData() {
    await getStorages();
    resetTransferData()
}
</script>