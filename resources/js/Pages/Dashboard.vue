<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import TimeList from '../Components/TimeList.vue';
    import DatePicker from '../Components/DatePicker.vue';
    import { ref } from 'vue';

    const today = ref(new Date());
    const year = ref( today.value.getFullYear());
    const month = ref( today.value.getMonth() + 1);
    const day = ref( today.value.getDate());
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- 當日紀錄 -->
                <h3 class="pl-5 font-bold">今日（{{ year }}-{{ month }}-{{ day }}）出勤紀錄</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <TimeList></TimeList>
                </div>
                <!-- 紀錄查詢 -->
                <h3 class="pt-10 pl-5 font-bold">出勤紀錄查詢</h3>
                <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                    <DatePicker></DatePicker>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
