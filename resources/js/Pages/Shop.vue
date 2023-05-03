<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
export default {
    name: 'Shop',
    components: {
        AppLayout,PrimaryButton,SecondaryButton,Pagination
    },
    props: {
        allProducts: {
            type: Object,
            required: true
        }
    }
}
</script>

<template>
    <AppLayout title="Tienda">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tienda
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="grid grid-cols-3 gap-4 p-2">
                        <div v-for="(item, key) in allProducts.data" :key="key" class="col-span-1 bg-white rounded-lg shadow">
                            <img :src="item.image" alt="Imagen de ejemplo" class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-4">
                            <h2 class="text-lg font-bold">{{ item.name }}</h2>
                            <p class="text-gray-600 mt-2">
                                {{ item.description }}
                            </p>
                                <PrimaryButton class="mr-2 mt-2">
                                    Comprar ahora
                                </PrimaryButton>
                                <SecondaryButton>
                                    Ver mas
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center items-center mt-4 mb-4">
                        <Pagination  :links="allProducts.links"></Pagination>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
