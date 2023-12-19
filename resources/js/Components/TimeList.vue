<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import dayjs from 'dayjs';

    const today = ref(dayjs().format('YYYY-MM-DD'));
    const data = ref([]);
    const expectedPunchout = ref('');
    const punchIn = ref('');

    axios.get('/api/data-from-database/' + today.value)
        .then(response =>{
            punchIn.value = dayjs(response.data.punchIn).format('HH:mm:ss')
            countPunchOutTime(response.data.punchIn);
        })
        .catch(error =>{
            console.log(error);
        });
    
    function countPunchOutTime(punchIn){

        const hour = dayjs(punchIn).hour();
        const minute = dayjs(punchIn).minute();

        if(hour == 8 || (hour == 9 && minute == 0)){
            expectedPunchout.value = dayjs(punchIn).hour(hour + 9).minute(minute + 20).format('HH:mm:ss');
        }else{
            expectedPunchout.value = dayjs(punchIn).hour(17).minute(20).second(0).format('HH:mm:ss');
        }
    }
    
</script>

<template>
    <div  class="p-6 text-gray-900 flex">
        <h2 class="pr-10">打卡上班時間</h2>
        <h2>{{ punchIn }}</h2>
    </div>
    <div  class="p-6 text-gray-900 flex">
        <h2 class="pr-10">預計下班時間</h2>
        <h2>{{ expectedPunchout }}</h2>
    </div>
</template>