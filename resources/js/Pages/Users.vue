<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue'
    import SecondaryButton from '@/Components/SecondaryButton.vue'
    import Pagination from '@/Components/Pagination.vue'
    import SectionTitle from '@/Components/SectionTitle.vue'
    import InputError from '@/Components/InputError.vue'
    import TextInput from '@/Components/TextInput.vue'
    import modal from '@/Components/modal.vue'
    import { reactive, ref } from 'vue';
    import { router, useForm } from '@inertiajs/vue3';

    name: 'UsersAdmin'
    defineProps({
        Users: {
            type: Object,
            required: true
        }
    })
    let showModalCreateUser = ref(false),
        form = useForm({
            name: '',
            email: '',
            password: ''
        }),
        errors = ref([])

    async function submit() {
        await axios.post(route('users.create'), form)
        .then(res=> {
            showModalCreateUser.value = false;
            router.get(route('users.index'));
        })
        .catch(err=> {
            errors.value = err.response.data
        })
    }

</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de usuarios
            </h2>
            <PrimaryButton @click="showModalCreateUser = true" >
                Nuevo
            </PrimaryButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="relative sm:justify-left sm:items-center bg-dots-darker p-2">
                        <table class="border table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="bg-gray-200 text-left py-2 px-4">Nombre</th>
                                    <th class="bg-gray-200 text-left py-2 px-4">Correo</th>
                                    <th class="bg-gray-200 text-left py-2 px-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, key) in Users.data" :key="key">
                                    <td class="py-2 px-4">{{item.name}}</td>
                                    <td class="py-2 px-4">{{item.email}}</td>
                                 
                                    <td>
                                        <div class="flex space-x-2 mt-2">
                                            <button  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
                        <div class="flex justify-center items-center mt-4 mb-4">
                            <Pagination :links="Users.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- MODAL CREATE USERS -->
    <modal :show="showModalCreateUser">
        <section-title>
            <template v-slot:title>
                <p v-text="'Registrar usuario'"></p>  
            </template>
        </section-title> 

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <label for="">Nombre</label>
                <TextInput
                    v-model="form.name"
                    type="text"
                    class=" block w-full"
                />
                <InputError :message="item" v-for="(item,key) in errors.name" :key="key" />

                <label for="">Correo</label>
                <TextInput
                    v-model="form.email"
                    type="email"
                    class=" block w-full"
                />
                <InputError :message="item" v-for="(item,key) in errors.email" :key="key" />

                <label for="">Contraseña</label>
                <TextInput
                    v-model="form.password"
                    type="password"
                    class="block w-full"
                />
                <InputError :message="item" v-for="(item,key) in errors.password" :key="key" />

            </div>
                <primary-button @click="submit()" class="float-right mr-4 mt-4 mb-4" >
                    Guardar
                </primary-button>
                <SecondaryButton class="float-right mr-4 mt-4 mb-4" 
                    @click="showModalCreateUser = false">
                    Cerrar
                </SecondaryButton>
        </div>
    </modal>

</template>