<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCartVariant } from '@mdi/js'
import { computed, onMounted, reactive, ref } from 'vue';
import modal from "@/Components/Modal.vue";
import { usePage , router } from '@inertiajs/vue3';


export default {
    name: 'Shop',
    components: {
        AppLayout,PrimaryButton,SecondaryButton,Pagination,
        SvgIcon,modal,SectionTitle,TextInput,InputLabel
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

            /**
             * Cuando WOMPI redirecciona hacia nuestro sitio porque el usuario 
             * ya ha finalizado su compra... 
             */
            const URLactual = new URL(window.location.href);
            const params = new URLSearchParams(URLactual.search);
            if (params.get('id')) {
                verficateTransaction(params.get('id'))
            }

        })

        let iconCart = ref(mdiCartVariant),
            userCart = ref([]),
            showModalCart = ref(false),
            total = ref(0),
            showModalShippingInfo = ref(false),
            wompi = reactive({
                public_key: usePage().props.wompi.public_key,
                user: usePage().props.auth.user,
                legalIdType: 'CC',
                country: 'CO',
                address: '',
                phonenumber: '',
                city: '',
                region: ''
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
                userCart.value = res.data.cart;
                total.value = res.data.total
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

        const amountInCents = computed(()=> {
            return total.value+'00'
        })


        const openModalShippingInfo = (()=> {
            showModalCart.value = false;
            showModalShippingInfo.value = true;
        })

        async function verficateTransaction(transactionID) {
            const url = 'https://sandbox.wompi.co/v1/transactions/' + transactionID;

            fetch(url)
            .then(response => response.json())
            .then(data => {
                const transaction = data.data
                if(transaction.status === "APPROVED"){
                    savePurchase(transaction)
                }else {
                    router.get(route('shop.index'));
                }
            })
            .catch(error => {
                console.log(error);
                router.get(route('shop.index'));
            });
        }

        const randReference = (()=>{
           return Date.now() + Math.floor(Math.random() * 1000001);
        })


        /**
         * Solo se puede ir a pagar cuando los datos han sido 
         * recolectados correctamente
         */
        const canGoToPay = computed(()=> {
            if(!wompi.address || !wompi.region || !wompi.city || !wompi.phonenumber) return false;

            return true;
        })


        async function savePurchase(transaction) {

        }


        return {
            iconCart,addToCart,getCart,userCart,
            productOnCart,showModalCart,formatCoin,RemoveProductCart,
            total,wompi,amountInCents,openModalShippingInfo,showModalShippingInfo,
            verficateTransaction,randReference,savePurchase,canGoToPay
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
                <p> Tu carrito <span class="text-green-700">{{ formatCoin(total) }}</span></p>  
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

           
            <primary-button @click="openModalShippingInfo()" :disabled="!userCart.length" class="float-right mr-4 mt-4 mb-4" >
                Finalizar compra 
            </primary-button>
            
            <SecondaryButton @click="showModalCart = false" class="float-right mr-4 mt-4 mb-4">
                Cerrar
            </SecondaryButton>
            
        </div>
    </modal>


    <modal :show="showModalShippingInfo">
        <section-title>
            <template v-slot:title>
                <p> Datos de envio </p>  
            </template>
        </section-title> 
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- CAMPOS A LLENAR POR EL USUARIO -->
                <InputLabel value="Dirección" />
                <TextInput
                    v-model="wompi.address"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />

                <InputLabel value="Departamento" class="mt-2" />
                <TextInput
                    v-model="wompi.region"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />

                <InputLabel value="Ciudad" class="mt-2" />
                <TextInput
                    v-model="wompi.city"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />

                <InputLabel value="Telefono" class="mt-2" />
                <TextInput
                    v-model="wompi.phonenumber"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />

                

                <!-- WOMPI PASARELA -->
                <form action="https://checkout.wompi.co/p/" method="GET">
                    <!-- OBLIGATORIOS -->
                    <input type="hidden" name="public-key" :value="wompi.public_key" />
                    <input type="hidden" name="currency" value="COP" />
                    <input type="hidden" name="amount-in-cents" :value="amountInCents" />
                    <input type="hidden" name="reference" :value="randReference()" />
                    <!-- OPCIONALES -->
                    <input type="hidden" name="redirect-url" :value="route('shop.index')" />
                    <input type="hidden" name="tax-in-cents:vat" value="0" />
                    <input type="hidden" name="tax-in-cents:consumption" value="0" />
                    <input type="hidden" name="customer-data:email" :value="wompi.user.email" />
                    <input type="hidden" name="customer-data:full-name" :value="wompi.user.name" />
            

                    <input type="hidden" name="customer-data:legal-id-type" :value="wompi.legalIdType" />
                    <input type="hidden" name="shipping-address:address-line-1" :value="wompi.address" />
                    <input type="hidden" name="shipping-address:country" :value="wompi.country" />
                    <input type="hidden" name="shipping-address:phone-number" :value="wompi.phonenumber" />
                    <input type="hidden" name="shipping-address:city" :value="wompi.city" />
                    <input type="hidden" name="shipping-address:region" :value="wompi.region" />
                    
                
                    <primary-button :disabled="!canGoToPay" type="submit" class="float-right mt-4 mb-4" >
                        Ir a pagar 
                    </primary-button>
                    <SecondaryButton @click="showModalShippingInfo = false" class="float-right mr-4 mt-4 mb-4">
                    Cerrar
                    </SecondaryButton>
                </form>
            </div>
        </div>
    </modal>
</template>
