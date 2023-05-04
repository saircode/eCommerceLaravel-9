<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCartVariant } from '@mdi/js'
import { onMounted, ref } from 'vue';
export default {
    name: 'Shop',
    components: {
        AppLayout,PrimaryButton,SecondaryButton,Pagination,
        SvgIcon
    },
    props: {
        allProducts: {
            type: Object,
            required: true
        }
    },

    setup () {
        onMounted(()=> {
            getCart();
        })

        let iconCart = ref(mdiCartVariant),
            userCart = ref([])

        async function addToCart(product_id){
            await axios.post(route('shoppingcart.add') , {
                product_id : product_id,
                quantity: 1
            })
            .then(res=> {
                getCart() 
            })
        }

        async function getCart() {
            await axios.get(route('shoppingcart.index'))
            .then(res=> {
                userCart.value = res.data;
            })
        }

        function productOnCart(product_id){
            let result = false;
            userCart.value.map(item=> {
                if(item.product_id === product_id) result = true;
            })

            return result;
        } 

        return {
            iconCart,addToCart,getCart,userCart,
            productOnCart
        }
    }
}
</script>

<template>
    <AppLayout title="Tienda">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tu carrito 
            </h2>
            <button class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-md">
                <svg-icon type="mdi" :path="iconCart"></svg-icon>
                <span class="text-lg font-bold ml-2">{{ userCart.length }}</span>
            </button>



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
                                <PrimaryButton :disabled="productOnCart(item.id)" 
                                    @click="addToCart(item.id)" class="mr-2 mt-2">
                                    Agregar al carrito
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
