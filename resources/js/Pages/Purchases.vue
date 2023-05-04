<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import Pagination from '@/Components/Pagination.vue';

    defineProps({
        purchases: {
            type: Object,
            required: true
        }
    })

    const formatCoin = (number) => {
        const num = parseFloat(number.toString().slice(0, -2));
        const options = { style: "currency", currency: "COP" };
        const COPformat = num.toLocaleString("es-CO", options);
        return COPformat; // "COP 1.234.567,89"
    };


</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis compras 
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="grid grid-cols-1 gap-4 p-2">
                        <div v-for="(item, key) in purchases.data" :key="key" class="col-span-1 bg-white rounded-lg shadow p-2">
                            <h2 class="text-lg font-bold">{{ item.created_at }}</h2>
                            <p class="text-gray-600 mt-2">
                                <span>Ref: {{ item.reference }}</span> <br>
                                <span>Total: {{ formatCoin(parseInt(item.amount_in_cents)) }}</span>
                            </p>
                            <hr>
                            <p class="text-black-900 ">Productos:</p>
                            <ul class="ml-3">
                                <li v-for="(product, key) in item.products" :key="key">
                                    * {{ product.name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex justify-center items-center mt-4 mb-4">
                        <Pagination  :links="purchases.links"></Pagination>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>