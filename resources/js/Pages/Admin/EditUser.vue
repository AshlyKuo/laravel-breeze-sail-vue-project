<script setup>
    import { defineProps, onMounted } from 'vue';

    const userId = $route.params.id
</script>

<template>
    <div>
        ID: {{ userId }}
    </div>

</template>