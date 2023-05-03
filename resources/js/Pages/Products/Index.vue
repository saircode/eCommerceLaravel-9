<script>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import modal from "@/Components/Modal.vue";
    import { computed, reactive, ref, watch } from 'vue';
    import SectionTitle from '@/Components/SectionTitle.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputLabel from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import Pagination from '@/Components/Pagination.vue';
    import { router , useForm } from '@inertiajs/vue3';
    import ConfirmationModal from "@/Components/ConfirmationModal.vue";


    export default {
        name: 'ProductsIndex',
        components: {
            AppLayout,
            PrimaryButton,
            SecondaryButton,
            modal,
            SectionTitle,
            TextInput,InputLabel,
            InputError,Pagination,ConfirmationModal
        },
        props: {
            allProducts: {
                type: Object  ,
                required: true
            }
        },
        setup(props) {
      
            let openModalCreateProduct = ref(false),//->Determina si el modal de registro de productos esta abierto o no
                form = useForm({
                    name: '',
                    stock: '',
                    price: '',
                    description: '',
                    image: ''
                }),
                inputFile=ref(null),//-> referencia del input file para poder leer su valor
                imagePreviewUrl=ref(null),//-> variable temporal para previsualizar la imagen que sube el usuario
                errors = ref([]),
                ShowConfirmDelete = ref(false),
                productToDelete = ref (null)

            const productSelected = ref(null)
            
            const WhatImage = computed(()=> {
                if(imagePreviewUrl.value) return imagePreviewUrl.value;
                else if(productSelected.value && productSelected.value.image) return productSelected.value.image;
                else
                return '/assets/images/product-no-image.png';
            })

            const formData = computed(()=>{
                return {
                    name: form.name,
                    stock: form.stock,
                    price: form.price,
                    description: form.description,
                    id: form.id
                }
            })
                
            const activateInputFile = (()=>{ //-> activa el input file que esta oculto .
                inputFile.value.click();
            }) 

            /**
             * valido el tipo de archivo que se intenta subir y 
             * lo almaceno en la variable correspondiente
             * @param {*} value 
             */
            const readFile = (value=> {
                imagePreviewUrl.value = null;
                form.image = inputFile.value.files[0]
                const validExtensions = ['.jpg', '.jpeg', '.png'];
                const fileExtension = form.image.name.slice(form.image.name.lastIndexOf('.'));
                if (!validExtensions.includes(fileExtension.toLowerCase())){
                    errors.value.image = ['Solo se permiten formatos .jpg .jpeg .png']
                    return ;
                }else{
                    errors.value.image = []
                    imagePreviewUrl.value = URL.createObjectURL(form.image);

                    if(productSelected.value) productSelected.value.image =null;
                }
            })

            /**
             * Se envia la data del nuevo producto al back 
             */
            async function submit () {
                await axios.post(route('products.create'), formData.value)
                .then(res=> {
                    saveImageAndFinish(res.data.product_id);
                })
                .catch(err=> {
                    errors.value =err.response.data 
                })
            }

            async function update () {
                await axios.put(route('products.update'), formData.value)
                .then(res=> {
                    saveImageAndFinish(res.data.product_id);
                })
                .catch(err=> {
                    errors.value =err.response.data 
                })
            }

            async function saveImageAndFinish(product_id) {
                let formdata = new FormData();
                formdata.append('image', form.image);
                formdata.append('product_id', product_id);
                await axios.post(route('products.save.image'), formdata,{
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res=> {
                    openModalCreateProduct.value = false;
                    router.get(route('products.index'));
                })
                .catch(err=> {
                    errors.value =err.response.data 
                })
            }

            /**
             * Confirmar y eliminar un producto
             * @param {*} product El producto a eliminar
             * @param {*} action confirm || delete 
             */
            async function deleteProduct(product = productToDelete.value, action = null) {
                productToDelete.value = product;
                if(action === 'confirm'){
                    ShowConfirmDelete.value = true;
                    return;
                }
                else {
                    ShowConfirmDelete.value = false;
                }
                

                await axios.delete(route('products.delete', {id: product.id}))
                .then(res=> {
                    router.get(route('products.index'));
                })
                .catch (err=> {
                    console.log(err.response.data)
                }) 
            }

            const formatCoin = (number =>{
                const options = { style: "currency", currency: "COP" };
                const COPformat = number.toLocaleString("es-CO", options);

                return COPformat; // "COP 1.234.567,89"
            })

            /**
             * Cada vez que cambia el producto seleccionado ...
             * el form toma los daros del mismo o se resetea si se trata de un nuevo resgistro
             */
            watch(() => productSelected.value, (currentValue, oldValue) => {
                if(currentValue && currentValue.id) {
                    form.name = currentValue.name;
                    form.description = currentValue.description;
                    form.stock = currentValue.stock;
                    form.price = currentValue.price;
                    form.image = currentValue.image;
                    form.id = currentValue.id

                    setTimeout(() => {
                        openModalCreateProduct.value = true
                    }, 100);
                }else {
                    form.reset()
                }
            })

            return {
                openModalCreateProduct,
                form,inputFile,activateInputFile,readFile,imagePreviewUrl,submit,
                errors,productSelected,WhatImage,update,saveImageAndFinish,formData,
                deleteProduct,ShowConfirmDelete,productToDelete,formatCoin
            }
        }
    }
</script>

<template>
    <AppLayout title="Productos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de productos
            </h2>
            <PrimaryButton @click=" productSelected = null, openModalCreateProduct = true">
                Nuevo
            </PrimaryButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="relative sm:justify-left sm:items-center min-h-screen bg-dots-darker">
                        <table class="border table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="bg-gray-200 text-left py-2 px-4">Imagen</th>
                                    <th class="bg-gray-200 text-left py-2 px-4">Nombre</th>
                                    <th class="bg-gray-200 text-left py-2 px-4">Descripcion</th>
                                    <th class="bg-gray-200 text-left py-2 px-4">Stock</th>
                                    <th class="bg-gray-200 text-left py-2 px-4">Precio</th>
                                    <th class="bg-gray-200 text-left py-2 px-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, key) in allProducts.data" :key="key">
                                    <td class="py-2 px-4"><img :src="item.image !== '' && item.image !== null ? item.image : '/assets/images/product-no-image.png'" width="200" alt=""></td>
                                    <td class="py-2 px-4">{{item.name}}</td>
                                    <td class="py-2 px-4">{{item.description}}</td>
                                    <td class="py-2 px-4">{{item.stock}}</td>
                                    <td class="py-2 px-4">{{formatCoin(item.price)}}</td>
                                    <td>
                                        <div class="flex space-x-2 mt-2">
                                            <button @click="productSelected = item" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Editar
                                            </button>
                                            <button @click="deleteProduct(item, 'confirm')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        
                    </div>
                    <div class="flex justify-center items-center mt-4 mb-4">
                        <Pagination :links="allProducts.links"></Pagination>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <modal :show="openModalCreateProduct">
            <section-title>
                <template v-slot:title>
                   <p v-text="productSelected ? 'Editar producto' : 'Registrar producto'"></p>  
                </template>
            </section-title> 
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                   
                    <label for="">Nombre del producto</label>
                    <TextInput
                        v-model="form.name"
                        type="text"
                        class=" block w-full"
                        required
                        autofocus
                    />
                    <InputError :message="item" v-for="(item,key) in errors.name" :key="key" />

                    
                    <label class="mt-4" for="">Stock</label>
                    <TextInput
                        v-model="form.stock"
                        type="number"
                        class=" block w-full"
                        required
                    />
                    <InputError  :message="item" v-for="(item,key) in errors.stock" :key="key" />


                    <label class="mt-4" for="">Precio</label>
                    <TextInput
                        v-model="form.price"
                        type="number"
                        class=" block w-full"
                        required
                    />
                    <InputError  :message="item" v-for="(item,key) in errors.price" :key="key" />


                    <div class="mt-4" style="cursor: pointer; width: 200px;" @click="activateInputFile()">
                        <label for="">Imagen del producto</label>
                        <img :src="WhatImage" 
                            alt="Imagen producto"
                            width="200">
                            <input style="display: none;"
                                type="file"
                                @change="readFile()"
                                ref="inputFile"
                            />
                          
                    </div>
                    <InputError  :message="item" v-for="(item,key) in errors.image" :key="key" />


                    <label class="mt-4" for="">Descripción</label>
                    <textarea v-model="form.description" class="w-full form-textarea rounded-md shadow-sm " id="" cols="30" rows="4"></textarea>
                    <InputError  :message="item" v-for="(item,key) in errors.description" :key="key" />

                </div>

                <primary-button class="float-right mr-4 mt-4 mb-4" 
                    @click="productSelected ? update() : submit()">
                    Guardar
                </primary-button>
                <SecondaryButton class="float-right mr-4 mt-4 mb-4" 
                    @click="openModalCreateProduct = false">
                    Cerrar
                </SecondaryButton>
                
            </div>

    </modal>

    <ConfirmationModal :show="ShowConfirmDelete">
        <template v-slot:title>
            Desea eliminar este producto?
        </template>
        <template v-slot:footer>
            <secondary-button class="mr-2" @click="ShowConfirmDelete =false">
                Cancelar
            </secondary-button>
            <PrimaryButton @click="deleteProduct()">
                Eliminar
            </PrimaryButton>
        </template>
    </ConfirmationModal>
</template>