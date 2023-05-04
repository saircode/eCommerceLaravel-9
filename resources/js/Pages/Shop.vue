<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCartVariant } from '@mdi/js'
import { onMounted, reactive, ref } from 'vue';
import modal from "@/Components/Modal.vue";

export default {
    name: 'Shop',
    components: {
        AppLayout,PrimaryButton,SecondaryButton,Pagination,
        SvgIcon,modal,SectionTitle,TextInput
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
            userCart = ref([]),
            showModalCart = ref(false),
            showModalBuys = ref (false),
            form = reactive({
                address: ''
            })

        async function addToCart(product_id){
            await axios.post(route('shoppingcart.add') , {
                product_id : product_id,
                quantity: 1
            })
            .then(res=> {
                getCart() 
            })
        }

        async function RemoveProductCart(cart_id){
            await axios.delete(route('shoppingcart.delete', {
                id : cart_id
            }) )
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

        const formatCoin = (number =>{
            const options = { style: "currency", currency: "COP" };
            const COPformat = number.toLocaleString("es-CO", options);

            return COPformat; // "COP 1.234.567,89"
        })

        const openModalBuys = (() =>{
            showModalCart.value = false
            showModalBuys.value = true
        })

        return {
            iconCart,addToCart,getCart,userCart,
            productOnCart,showModalCart,formatCoin,RemoveProductCart,
            openModalBuys,showModalBuys,form
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
            <button @click="showModalCart = true" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-md">
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

    <modal :show="showModalCart">
        <section-title>
            <template v-slot:title>
                <p v-text="'Tu carrito'"></p>  
            </template>
        </section-title> 
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="relative sm:justify-left sm:items-center  bg-dots-darker">
                    <table class="border table-auto w-full">
                        <thead>
                            <tr>
                                <th class="bg-gray-200 text-left py-2 px-4">Imagen</th>
                                <th class="bg-gray-200 text-left py-2 px-4">Nombre</th>
                                <th class="bg-gray-200 text-left py-2 px-4">Precio</th>
                                <th class="bg-gray-200 text-left py-2 px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, key) in userCart" :key="key">
                                <td class="py-2 px-4"><img :src="item.image !== '' && item.image !== null ? item.image : '/assets/images/product-no-image.png'" width="200" alt=""></td>
                                <td class="py-2 px-4">{{item.name}}</td>
                                <td class="py-2 px-4">{{formatCoin(item.price)}}</td>
                                <td>
                                    <div class="flex space-x-2 mt-2">
                                        <button @click="RemoveProductCart(item.id)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Quitar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <primary-button  @click="openModalBuys()" :disabled="!userCart.length" class="float-right mr-4 mt-4 mb-4" >
                Comprar 
            </primary-button>
            <SecondaryButton @click="showModalCart = false" class="float-right mr-4 mt-4 mb-4">
                Cerrar
            </SecondaryButton>
            
        </div>
    </modal>

    <modal :show="showModalBuys">
        <section-title>
            <template v-slot:title>
                <p v-text="'Finalizar compra'"></p>  
            </template>
        </section-title> 
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="relative sm:justify-left sm:items-center  bg-dots-darker">
                    <label for="">Dirección de envio</label>
                    <TextInput
                        v-model="form.address"
                        type="text"
                        class=" block w-full"
                        required
                        autofocus
                    />
                    <!-- <InputError :message="item" v-for="(item,key) in errors.name" :key="key" /> -->
                </div>
            </div>

            <primary-button :disabled="!userCart.length" class="float-right mr-4 mt-4 mb-4" >
                Ir a pagar 
            </primary-button>
            <SecondaryButton @click="showModalBuys = false" class="float-right mr-4 mt-4 mb-4">
                Cerrar
            </SecondaryButton>
            
        </div>
    </modal>
</template>
