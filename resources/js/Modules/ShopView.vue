<template>
    <Toast />
    <div class="p-6">
        <div class="">
            <div class="content-wrapper">
                <div class="storage-list">
                    <div v-for="storage in storages" :key="storage.storage_id" class="storage-card">
                        <h3 class="storage-title">{{ storage.storage_name }}</h3>
                        <div v-if="storage.inventory.length > 0" class="inventory-grid">
                            <div v-for="item in storage.inventory" :key="item.id" class="inventory-item">
                                <div v-if="item.quantity > 0" class="item-info">
                                    <span class="item-name">{{ item.ice_cream_name }}</span>
                                    <div class="quantity-container">
                                        <div v-for="n in item.quantity" :key="n" class="quantity-box"></div>
                                    </div>
                                    <p class="item-quantity">Ilość kuwet: {{ item.quantity }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="no-inventory">Brak dostępnych lodów</div>
                        <button v-if="storage.storage_type === 'storage'" class="edit-btn m-1"
                            @click="handleEdit(storage)">Produkcja</button>
                        <button class="transfer-btn m-1" @click="handleTransfer(storage.storage_id)">Transfer z</button>
                        <button v-if="storage.storage_type === 'display'" class="transfer-btn m-1">Sprzedaż</button>
                    </div>
                </div>
            </div>
        </div>
        <Dialog v-model:visible="editDialogVisible" modal header="Wprowadź produkcję" :style="{ width: '600px' }">
            <DataTable v-model:value="iceCreams"
                @cell-edit-complete="(event) => onCellEditComplete(event, productionData)" editMode="cell"
                class="p-datatable-sm">
                <Column style="width: 15rem;" field="name" header="Smak lodów" />
                <Column field="quantity" header="Ilość kuwet">
                    <template #editor="{ data, field }">
                        <input type="number" v-model="data[field]"></input>
                    </template>
                </Column>

            </DataTable>
            <Button label="Zapisz" @click="submitData('production')" class="mt-3" />
        </Dialog>

        <Dialog v-model:visible="transferDialogVisible" modal header="Transfer" :style="{ width: '600px' }">
            <div>
                <DataTable v-model:value="iceCreamOptions"
                    @cell-edit-complete="(event) => onCellEditComplete(event, transferData)" editMode="cell"
                    class="p-datatable-sm">

                    <Column style="width: 15rem;" field="name" header="Smak lodów" />
                    <Column field="quantity" header="Ilość kuwet">
                        <template #editor="{ data, field }">
                            <input type="number" v-model="data[field]"></input>
                        </template>
                    </Column>

                </DataTable>
                <Select v-model="transferData.destination_storage_id" :options="storageOptions"
                    optionLabel="storage_name" optionValue="storage_id" placeholder="Magazyn docelowy" class="w-60" />

            </div>

            <Button label="Zapisz" @click="submitData('transfer')" class="mt-3" />
        </Dialog>

    </div>

    <Button label="Zapisz" @click="showSuccess()" class="mt-3" />
</template>
<script setup>
import axios from "axios";
import { onMounted, reactive, ref } from "vue";
import { useToast } from "primevue/usetoast";

const toast = useToast();

const props = defineProps({
    id: {
        type: String,
        default: 0,
    },
});
const storageOptions = ref([])
const iceCreams = ref()
const iceCreamOptions = ref([])
const storages = ref([]);
const editDialogVisible = ref(false);
const transferDialogVisible = ref(false);
const productionData = reactive({
    shop_id: parseInt(props.id),
    destination_storage_id: null,
    ice_creams: [],
});
const transferData = reactive({
    source_storage_id: null,
    destination_storage_id: null,
    ice_creams: [],
});
onMounted(async () => {
    await getStorages()
    await getIceCream()
});

const handleEdit = (storage) => {
    productionData.destination_storage_id = storage.storage_id;
    editDialogVisible.value = true;
};

const handleTransfer = (id) => {

    transferData.source_storage_id = id
    iceCreamOptions.value = storages.value.find((storage) => storage.storage_id == id)?.inventory.reduce((acc, item) => {
        if (item.quantity > 0) {
            acc.push({ id: item.ice_cream_id, name: item.ice_cream_name, quantity: 0 });
        }
        return acc;
    }, []) || [];
    storageOptions.value = storages.value.filter((storage) => storage.storage_id != id);
    transferDialogVisible.value = true;
    console.log(iceCreamOptions.value)
};



const onCellEditComplete = (event, dataObject) => {
    let { newValue, data, field } = event;
    console.log(newValue, data, field)
    data[field] = newValue;
    const existingIndex = dataObject.ice_creams.findIndex(item => item.ice_cream_id === data.id);
    if (existingIndex !== -1) {
        if (data.quantity > 0) {
            dataObject.ice_creams[existingIndex].quantity = data.quantity;
        } else {
            // Jeśli quantity = 0, usuwamy wpis
            dataObject.ice_creams.splice(existingIndex, 1);
        }
    } else {
        // Dodaj nowy wpis tylko, jeśli quantity > 0
        if (data.quantity > 0) {
            dataObject.ice_creams.push({
                ice_cream_id: data.id,
                quantity: data.quantity
            });
        }
    }
    console.log(productionData, transferData)
};
const submitData = async (type) => {
    const endpoints = {
        transfer: { data: transferData, api: "/api/transfers", refresh: refreshTransferData },
        production: { data: productionData, api: "/api/productions", refresh: refreshProductionData }
    };

    if (!endpoints[type] || endpoints[type].data.ice_creams.length === 0) {
        toast.add({ severity: 'warn', summary: 'Uwaga', detail: 'Brak zmian do zapisania', life: 4000 });
        return;
    }

    try {
        const response = await axios.post(endpoints[type].api, endpoints[type].data);

        if (response.status === 200) {
            toast.add({ severity: 'success', summary: 'Sukces!', detail: 'Zmiany wprowadzone pomyślnie', life: 4000 });
            await endpoints[type].refresh();
        }
    } catch (error) {
        console.error("Błąd:", error.response?.data || error.message);
        toast.add({ severity: 'error', summary: 'Error', detail: `Błąd: ${JSON.stringify(error.response?.data || error.message)}`, life: 6000 });
    }
};
const showSuccess = () => {
    toast.add({ severity: 'success', summary: 'Success Message', detail: 'Message Content', life: 3000 });
};
const showInfo = () => {
    toast.add({ severity: 'info', summary: 'Info Message', detail: 'Message Content', life: 3000 });
};

const showWarn = () => {
    toast.add({ severity: 'warn', summary: 'Warn Message', detail: 'Message Content', life: 3000 });
};

const showError = () => {
    toast.add({ severity: 'error', summary: 'Error Message', detail: 'Message Content', life: 3000 });
};
function resetProductionData() {
    editDialogVisible.value = false;
    productionData.shop_id = props.id;
    productionData.destination_storage_id = null;
    productionData.ice_creams = [];

}
function resetTransferData() {
    transferData.ice_creams = [];
    transferDialogVisible.value = false;
}

async function getStorages() {
    try {
        const response = await axios.get(`/api/storages/${props.id}`);
        storages.value = response.data;
    } catch (error) {
        console.error("Błąd pobierania magazynów:", error.response?.data || error.message);
        toast.add({ severity: 'warn', summary: 'Uwaga! Nie udało się pobrać danych magazynowych', detail: `${error.response?.data || error.message}`, life: 5000 });
    }
}
async function getIceCream() {
    try {
        const response = await axios.get(`/api/icecreams`);
        iceCreams.value = response.data;
    } catch (error) {
        toast.add({ severity: 'warn', summary: 'Uwaga! Nie udało się pobrać danych magazynowych', detail: `${error.response?.data || error.message}`, life: 5000 });
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
<style scoped>
.nav-bar {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 16px;
    background: linear-gradient(to right, #FFF2F2, #A9B5DF);
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.storage-list {
    padding: 20px;
}

.storage-card {
    background: linear-gradient(to bottom, #A9B5DF, #FFF2F2);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.storage-title {
    color: #2D336B;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px;
}

.inventory-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}

.inventory-item {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 10px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

.item-name {
    color: #2D336B;
    font-size: 16px;
    font-weight: bold;
}

.quantity-container {
    display: flex;
    gap: 5px;
    justify-content: center;
    margin-top: 5px;
}

.quantity-box {
    width: 20px;
    height: 40px;
    background: #2D336B;
    border-radius: 4px;
}

.edit-btn,
.transfer-btn {
    margin-top: 10px;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    border: none;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.edit-btn {
    background-color: #A9B5DF;
    color: white;
}

.edit-btn:hover {
    background-color: #7886C7;
    transform: scale(1.05);
}

.transfer-btn {
    background-color: #2D336B;
    color: white;
}

.transfer-btn:hover {
    background-color: #7886C7;
    transform: scale(1.05);
}
</style>