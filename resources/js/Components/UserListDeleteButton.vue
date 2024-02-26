<script setup>
    import axios from 'axios';
    import { defineProps, defineEmits } from 'vue';

    const userData = defineProps(['userData']);
    const emit = defineEmits(['userDeleted']);

    function deleteUser(){
        let confirmation = confirm('Confirm to delete user '+ userData.userData);

        if(confirmation){
            axios.delete('/api/users/' +  userData.userData )
            .then(response => {
                console.log(response.data);
                alert('User '+ userData.userData + ' has been deleted');
                emit('userDeleted', userData.userData);
            })
            .catch(error => {
                console.log(error);
            })
        }
    };

</script>
<template>
    <button @click="deleteUser">Delete</button>
</template>
<style scoped>
button{
    background-color: #0F1035;
    color: aliceblue;
    border-radius: 5px;
    padding: 1px 3px;
    margin: 3px 10px;
}
</style>