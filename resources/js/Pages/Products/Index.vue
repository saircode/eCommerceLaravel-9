<script>
import AppLayout from '@/Layouts/AppLayout.vue';
export default {
    components: {
        AppLayout
    },
    props: {
        allProducts: {
            type: Array,
            required: true
        }
    },
    setup(props) {
        
        
        return {

        }
    },
}
</script>

<template>
    <AppLayout title="Productos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de productos
            </h2>
        </template>
    </AppLayout>
</template>