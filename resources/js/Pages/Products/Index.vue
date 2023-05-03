<script>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import modal from "@/Components/Modal.vue";
    import { reactive, ref } from 'vue';
    import SectionTitle from '@/Components/SectionTitle.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputLabel from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';

    export default {
        name: 'ProductsIndex',
        components: {
            AppLayout,
            PrimaryButton,
            SecondaryButton,
            modal,
            SectionTitle,
            TextInput,InputLabel,
            InputError
        },
        props: {
            allProducts: {
                type: Array,
                required: true
            }
        },
        setup(props) {
      
            let openModalCreateProduct = ref(false),//->Determina si el modal de registro de productos esta abierto o no
                form = reactive({
                    name: '',
                    stock: '',
                    description: '',
                    image: ''
                }),
                inputFile=ref(null),//-> referencia del input file para poder leer su valor
                imagePreviewUrl=ref(null),//-> variable temporal para previsualizar la imagen que sube el usuario
                errors = ref([])
                
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
                }
            })

            /**
             * Se envia la data del nuevo producto al back 
             */
            async function submit () {
                let formData = new FormData();
                formData.append('image', form.image);
                formData.append('data', JSON.stringify(form));
                
                await axios.post(route('products.create'), formData)
                .then(res=> {
                    openModalCreateProduct.value = false;
                    location.reload()
                })
                .catch(err=> {
                    errors.value = err.response ? err.response.data : []
                })
            }


            return {
                openModalCreateProduct,
                form,inputFile,activateInputFile,readFile,imagePreviewUrl,submit,
                errors
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
            <PrimaryButton @click="openModalCreateProduct = true">
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
                                    <th class="bg-gray-200 text-left py-2 px-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, key) in allProducts.data" :key="key">
                                    <td class="py-2 px-4"><img :src="item.image" width="200" alt=""></td>
                                    <td class="py-2 px-4">{{item.name}}</td>
                                    <td class="py-2 px-4">{{item.description}}</td>
                                    <td class="py-2 px-4">{{item.stock}}</td>
                                    <td>
                                        <div class="flex space-x-2 mt-2">
                                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Editar
                                            </button>
                                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <modal :show="openModalCreateProduct">
            <section-title>
                <template v-slot:title>
                    Registrar producto
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

                    
                    <label class="mt-4      " for="">Stock</label>
                    <TextInput
                        v-model="form.stock"
                        type="number"
                        class=" block w-full"
                        required
                    />
                    <InputError  :message="item" v-for="(item,key) in errors.stock" :key="key" />



                    <div class="mt-4" style="cursor: pointer; width: 200px;" @click="activateInputFile()">
                        <label for="">Imagen del producto</label>
                        <img :src="imagePreviewUrl?imagePreviewUrl:'/assets/images/product-no-image.png'" 
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

                <primary-button class="float-right mr-4 mt-4 mb-4" @click="submit()">
                    Guardar
                </primary-button>
                <SecondaryButton class="float-right mr-4 mt-4 mb-4" @click="openModalCreateProduct = false">
                    Cerrar
                </SecondaryButton>
                
            </div>

    </modal>
</template>