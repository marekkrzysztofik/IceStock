<template>

    <nav class="flex items-center justify-between p-4 shadow-lg bg-gradient-to-r from-blue-500 to-blue-700 text-white">
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


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
        <Card v-for="shop in data" :key="shop.id"
            class="p-4 bg-gradient-to-b from-blue-100 to-blue-50 shadow-md rounded-2xl">
            <h2 class="text-xl font-bold mb-2 text-blue-700">{{ shop.name }}</h2>
            <ScrollPanel style="width: 100%; height: 200px">
                <div style="width: 100%; height: 200px">
                    <div v-for="storage in shop.storages" :key="storage.id" class="mb-4">
                        <h3 class="text-lg font-semibold text-blue-600">{{ storage.name }}</h3>
                        <p class="text-sm text-gray-600 mb-2">Łączna liczba kuwet: {{ totalKuwetas(storage.iceCreams) }}
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            <div v-for="item in storage.iceCreams" :key="item.id"
                                class="m-1 p-2 bg-white border border-blue-200 rounded-xl text-sm shadow-sm w-full">
                                <h4 class="font-medium mb-1 text-blue-500">{{ item.flavor }} - {{ item.quantity }}
                                    kuwety</h4>
                                <div class="flex items-center gap-1">
                                    <div v-for="n in Math.floor(item.quantity)" :key="n"
                                        class="w-10 h-16 bg-blue-500 rounded-lg m-1 shadow-md"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </ScrollPanel>
        </Card>

    </div>


</template>
<script setup>

const totalKuwetas = (iceCreams) => {
    return iceCreams.reduce((sum, item) => sum + item.quantity, 0);
};
defineProps(['data']);
const data = [
    {
        id: 1,
        name: 'Shop 1',
        storages: [
            {
                id: 1,
                name: 'Storage 1',
                iceCreams: [
                    { id: 1, flavor: 'Vanilla', quantity: 5 },
                    { id: 2, flavor: 'Chocolate', quantity: 5.5 },
                    { id: 3, flavor: 'Strawberry', quantity: 2 },
                    { id: 1, flavor: 'Vanilla', quantity: 3 },
                    { id: 2, flavor: 'Chocolate', quantity: 2 },
                    { id: 3, flavor: 'Strawberry', quantity: 1 },
                    { id: 1, flavor: 'Vanilla', quantity: 3 },
                    { id: 2, flavor: 'Chocolate', quantity: 4 },
                    { id: 3, flavor: 'Strawberry', quantity: 5 },
                    { id: 2, flavor: 'Chocolate', quantity: 6 },
                    { id: 3, flavor: 'Strawberry', quantity: 4 },
                    { id: 1, flavor: 'Vanilla', quantity: 2 },
                    { id: 2, flavor: 'Chocolate', quantity: 1 },
                    { id: 3, flavor: 'Strawberry', quantity: 2 },
                ],
            },
            {
                id: 2,
                name: 'Storage 2',
                iceCreams: [
                    { id: 1, flavor: 'Vanilla', quantity: 2 },
                    { id: 2, flavor: 'Chocolate', quantity: 1 },
                    { id: 3, flavor: 'Strawberry', quantity: 2 },
                ],
            },
        ],
    },
    {
        id: 2,
        name: 'Shop 2',
        storages: [
            {
                id: 3,
                name: 'Storage 3',
                iceCreams: [
                    { id: 7, flavor: 'Vanilla', quantity: 5 },
                    { id: 8, flavor: 'Chocolate', quantity: 4 },
                    { id: 9, flavor: 'Strawberry', quantity: 3 },
                ],
            },
            {
                id: 4,
                name: 'Storage 4',
                iceCreams: [
                    { id: 10, flavor: 'Vanilla', quantity: 2 },
                    { id: 11, flavor: 'Chocolate', quantity: 3 },
                    { id: 12, flavor: 'Strawberry', quantity: 5 },
                ],
            },
        ],
    },
    {
        id: 2,
        name: 'Shop 3',
        storages: [
            {
                id: 3,
                name: 'Storage 3',
                iceCreams: [
                    { id: 10, flavor: 'Vanilla', quantity: 2 },
                    { id: 11, flavor: 'Chocolate', quantity: 3 },
                    { id: 12, flavor: 'Strawberry', quantity: 5 },
                ],
            },
            {
                id: 4,
                name: 'Storage 4',
                iceCreams: [
                    { id: 10, flavor: 'Vanilla', quantity: 2 },
                    { id: 11, flavor: 'Chocolate', quantity: 3 },
                    { id: 12, flavor: 'Strawberry', quantity: 5 },
                ],
            },
        ],
    },
    {
        id: 2,
        name: 'Shop 4',
        storages: [
            {
                id: 3,
                name: 'Storage 3',
                iceCreams: [
                    { id: 10, flavor: 'Vanilla', quantity: 2 },
                    { id: 11, flavor: 'Chocolate', quantity: 3 },
                    { id: 12, flavor: 'Strawberry', quantity: 5 },
                ],
            },
            {
                id: 4,
                name: 'Storage 4',
                iceCreams: [
                    { id: 10, flavor: 'Vanilla', quantity: 2 },
                    { id: 11, flavor: 'Chocolate', quantity: 3 },
                    { id: 12, flavor: 'Strawberry', quantity: 5 },
                ],
            },
        ],
    },
]

</script>
