<template>
    <DataTable :value="transfers" paginator :rows="10" :rowsPerPageOptions="[10,15]" v-model:filters="filters"
    responsiveLayout="scroll" editMode="row" dataKey="id" filterDisplay="row" class="p-datatable" scrollable
    scrollHeight="80vh" size="small">
    <Column header="Budget Name" filterField="type">
        <template #body="{ data }">
            <div class="flex align-items-center gap-2">
                <span>{{ data.type }}</span>
            </div>
        </template>
        <template #filter="{ filterModel, filterCallback }">
            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Name"
                class="p-column-filter" style="width: 8rem">

            </InputText>
        </template>
    </Column>
    <Column header="Budget Name" filterField="source">
        <template #body="{ data }">
            <div class="flex align-items-center gap-2">
                <span>{{ data.source }}</span>
            </div>
        </template>
        <template #filter="{ filterModel, filterCallback }">
            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Name"
                class="p-column-filter" style="width: 8rem">

            </InputText>
        </template>
    </Column>
    <Column header="Budget Name" filterField="destination">
        <template #body="{ data }">
            <div class="flex align-items-center gap-2">
                <span>{{ data.destination }}</span>
            </div>
        </template>
        <template #filter="{ filterModel, filterCallback }">
            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Name"
                class="p-column-filter" style="width: 8rem">

            </InputText>
        </template>
    </Column>
    <Column header="Budget Name" filterField="ice_cream">
        <template #body="{ data }">
            <div class="flex align-items-center gap-2">
                <span>{{ data.ice_cream }}</span>
            </div>
        </template>
        <template #filter="{ filterModel, filterCallback }">
            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Name"
                class="p-column-filter" style="width: 8rem">

            </InputText>
        </template>
    </Column>
    <Column header="Budget Name" filterField="quantity">
        <template #body="{ data }">
            <div class="flex align-items-center gap-2">
                <span>{{ data.quantity }}</span>
            </div>
        </template>
        <template #filter="{ filterModel, filterCallback }">
            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Name"
                class="p-column-filter" style="width: 8rem">

            </InputText>
        </template>
    </Column>
    
</DataTable>

</template>

<script setup>
import axios from "axios";
import { onMounted, reactive, ref } from "vue";
import { FilterMatchMode } from '@primevue/core/api';
const transfers = ref([])
onMounted(async () => {
    await getTransfers()
});

const filters = ref({
    type: { value: null, matchMode: FilterMatchMode.CONTAINS },
    source: { value: null, matchMode: FilterMatchMode.CONTAINS },
    destination: { value: null, matchMode: FilterMatchMode.CONTAINS },
    ice_cream: { value: null, matchMode: FilterMatchMode.CONTAINS },
    quantity: { value: null, matchMode: FilterMatchMode.CONTAINS },
});


async function getTransfers() {
    try {
        const response = await axios.get(`/api/transfers`);
        transfers.value = response.data;
    } catch (error) {
        console.error("Błąd pobierania magazynów:", error.response?.data || error.message);
        alert("Nie udało się pobrać danych magazynowych.");
    }
}
</script>