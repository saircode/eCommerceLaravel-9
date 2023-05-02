<script>
import AppLayout from '@/Layouts/AppLayout.vue';
export default {
    components: {
        AppLayout
    },
    setup() {
        
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