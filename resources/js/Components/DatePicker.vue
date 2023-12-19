<script setup>
  import { ref } from 'vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';
  import axios from 'axios';
  import dayjs from 'dayjs';

  const date = ref();
  const dateToPost = ref();
  const punchIn = ref();
  const punchOut = ref();
  const searchDate = ref();
  const textToShow = ref('下班打卡時間');

  const handleDate = (modelData) => {
    const formattedDate = dayjs(modelData).format('YYYY-MM-DD');
    dateToPost.value = formattedDate;

    if(formattedDate == dayjs().format('YYYY-MM-DD')){
      textToShow.value = '最後刷卡時間:';
    }else{
      textToShow.value = '下班打卡時間:';
    }
  
    axios.get('/api/data-from-database/' + dateToPost.value)
      .then(response => { 
        punchIn.value = dayjs(response.data.punchIn).format('HH:mm:ss')
        punchOut.value = dayjs(response.data.punchOut).format('HH:mm:ss')
        searchDate.value = dayjs(response.data.punchIn).format('YYYY-MM-DD')
      })
      .catch(error => {
        console.error(error);
      });
  };
</script>

<template>
  <div class="md:flex">
      <VueDatePicker class="basis-1/3 p-6" 
      :enable-time-picker="false" 
      inline :model-value="date" 
      @update:model-value="handleDate" 
      :max-date="new Date()" 
      :six-weeks="true">
      </VueDatePicker>
      <div>
          <div  class="p-6 text-gray-900 flex">
              <h2 class="pr-14">查詢日期:</h2>
              <h2 class="pl-4">{{ searchDate }}</h2>
          </div>
          <div  class="p-6 text-gray-900 flex">
              <h2 class="pr-10">上班打卡時間:</h2>
              <h2>{{ punchIn }}</h2>
          </div>
          <div  class="p-6 text-gray-900 flex">
              <h2 class="pr-10">{{ textToShow }}</h2>
              <h2>{{ punchOut }}</h2>
          </div>
      </div>
  </div>

</template>