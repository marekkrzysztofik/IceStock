<template>
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
                        <button v-if="storage.storage_type==='storage'" class="edit-btn m-1" @click="handleEdit(storage)">Produkcja</button>
                        <button v-if="storage.storage_type==='storage'" class="edit-btn m-1" @click="handleEdit(storage)">Produkcja</button>
                        <button class="transfer-btn m-1" @click="handleTransfer(storage.storage_id)">Transfer z</button>
                        <button v-if="storage.storage_type==='display'" class="transfer-btn m-1" @click="handleTransfer(storage.storage_id)">Sprzedaż</button>
                    </div>
                </div>
            </div>
        </div>
        <Dialog v-model:visible="editDialogVisible" modal header="Edytuj inventory" :style="{ width: '600px' }">
            <DataTable v-model:value="iceCreams" @cell-edit-complete="onCellEditComplete" editMode="cell"
                class="p-datatable-sm">

                <Column style="width: 15rem;" field="name" header="Smak lodów" />
                <Column field="quantity" header="Ilość kuwet">
                    <template #editor="{ data, field }">
                        <input type="number" v-model="data[field]"></input>
                    </template>
                </Column>

            </DataTable>
            <Button label="Zapisz" @click="saveAllChanges" class="mt-3" />
        </Dialog>
        
        <Dialog v-model:visible="transferDialogVisible" modal header="Transfer" :style="{ width: '600px' }">
            <div>
                <DataTable v-model:value="iceCreams" @cell-edit-complete="onCellEditComplete2" editMode="cell"
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
const availableInventory = ref()
const storageOptions = ref([])
const iceCreams = ref()
const quantity = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
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
    ice_cream_id: null,
    quantity: null
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

    data[field] = newValue;
    console.log(data, field, newValue)
    const existingIndex = productionData.ice_creams.findIndex(item => item.ice_cream_id === data.id);
    if (existingIndex !== -1) {
        if (data.quantity > 0) {
            productionData.ice_creams[existingIndex].quantity = data.quantity;
        } else {
            // Jeśli quantity = 0, usuwamy wpis
            productionData.ice_creams.splice(existingIndex, 1);
        }
    } else {
        // Dodaj nowy wpis tylko, jeśli quantity > 0
        if (data.quantity > 0) {
            productionData.ice_creams.push({
                ice_cream_id: data.id,
                quantity: data.quantity
            });
        }
    }
    console.log(productionData)
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

async function getStorages() {
    try {
        const response = await axios.get(`/api/storages/${props.id}`);
        storages.value = response.data;
    } catch (error) {
        console.error("Błąd pobierania magazynów:", error.response?.data || error.message);
        alert("Nie udało się pobrać danych magazynowych.");
    }
}
async function getIceCream() {
    try {
        const response = await axios.get(`/api/icecreams`);
        iceCreams.value = response.data;
    } catch (error) {
        console.error("Błąd pobierania magazynów:", error.response?.data || error.message);
        alert("Nie udało się pobrać danych magazynowych.");
    }
    console.log(iceCreams.value)
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

.edit-btn, .transfer-btn {
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