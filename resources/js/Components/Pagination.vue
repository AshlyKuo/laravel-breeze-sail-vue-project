<script setup>
    import { Link } from '@inertiajs/vue3';
    import { defineProps, defineEmits, ref } from 'vue';

    const props = defineProps({
        links: Array
    });
    const emit = defineEmits({
        urlNow: String
    });

    function pageChanged(newURL){
        emit('urlNow', newURL);
    };

</script>
<template>
    <div class="paginate">
        <button
            v-for="(link, index) in props.links"
            :key="index"
            v-html="link.label"
            class="px-1"
            :class="{ 'text-gray-500': !link.url, 'font-bold': link.active}"
            @click="pageChanged(link.url)"
            :disabled="!link.url"
        ></button>
    </div>
</template>
<style scoped>
    .paginate{
        display: flex;
        flex-direction: row;
        justify-content: center;
        margin-top: 30px;
    }
</style>