<script>
import { Link } from '@inertiajs/vue3';
export default {
    name: 'Pagination',
    components: {
        Link
    },
    props: {
        links:{
            type: Object, 
            required: true
        }
    },
    setup() {
        
    },
}
</script>

<template>
    <nav aria-label="Page navigation example">
    <ul class="inline-flex -space-x-px">
        <li v-for="(item, key) in links" :key="key">
            <Link v-show="item.label.split(' ')[1] === 'Previous'" :href="item.url ?? '#'" 
                class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Anterior 
            </Link>
            
            <Link v-if="item.label.split(' ')[1] !== 'Previous' && item.label.split(' ')[0] !== 'Next'" 
                :href="item.url ?? '#'" 
                class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                {{ item.label }}
            </Link>

            <Link v-show="item.label.split(' ')[0] === 'Next'" 
                :href="item.url ?? '#'" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Siguiente
            </Link>
        </li>
    </ul>
    </nav>
</template>