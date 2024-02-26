<script setup>
    import { ref, onMounted } from 'vue';
    import axios from 'axios';
    import UserListDeleteButton from './UserListDeleteButton.vue';
    import Pagination from './Pagination.vue';    

    const users = ref([]); 
    // User Data Title
    const titles = ref([{
        'name': 'ID',
        'class': 'id'
    }, {
        'name': 'Job Number',
        'class':'name'
    },{
        'name': 'Email',
        'class': 'email'
    },{
        'name': 'Notify Token',
        'class': 'notifyToken'
    },{
        'name': '',
        'class': 'buttons'
    } ]);
    const links = ref([]);
    const currentPage = ref(1);

    // Get Users data
    onMounted(async () => {
        try{
            const response = await axios.get('/api/users');
            users.value = response.data;
            users.value.data = users.value.data.map(user => ({
                ...user,
                editable: false,
                originalData: { ...user }}));
            links.value = response.data.meta.links;
            currentPage.value = response.data.meta.current_page;
        }catch(error){
            console.error(error);
        }
    });

    //刪除用戶後，用戶資料更新
    async function handelUserDeleted(){
        try{
            const response = await axios.get('/api/users?page=' + currentPage.value);
            users.value = response.data;
            users.value.data = users.value.data.map(user => ({
                ...user,
                editable: false,
                originalData: { ...user }}));
            links.value = response.data.meta.links;
            currentPage.value = response.data.meta.current_page;
        }catch(error){
            console.error(error);
        }
    };
    //enable 編輯用戶資料
    function toggleEditUser(user){
        user.editable = !user.editable;
    };
    //更新用戶資料
    function saveChandged(user){
        const isChanged = user.email !== user.originalData.email || user.notifyToken !== user.originalData.notifyToken;
        if (!isChanged) {
            alert('No changes were made.');
            return;
        }

        let confirmation = confirm('Confirm to edit user ' + user.id);
        if(confirmation){
            if(confirmation){                
                axios.patch('/api/users/' +  user.id,{
                    email: user.email,
                    notifyToken: user.notifyToken
                },{
                    headers:{
                        "Content-Type": 'application/json'
                    }
                })
                .then(response => {
                    user.originalData = { ...user };
                    user.editable = !user.editable;
                    alert('User '+ user.id+' has been updated')
                })
                .catch(error => {
                    console.log('ERROR: '+ error);

                    if (error.response) {
                        console.log(`Response status: ${error.response.status}`);
                        console.log(`Response data:`, error.response.data);
                    }
                    console.log('Error message:', error.message);
                    if (error.config) {
                        console.log('Error config:', error.config);
                    }
                })
            }
        }
    }
    //取消更新
    function cancelChanged(user){
        Object.assign(user, user.originalData);
        user.editable = !user.editable;
    }
    //分頁
    async function handlePageChange(urlNow){
        try{
            const response = await axios.get(urlNow);
            users.value = response.data;
            users.value.data = users.value.data.map(user => ({
                ...user,
                editable: false,
                originalData: { ...user }}));
            links.value = response.data.meta.links;
            currentPage.value = response.data.meta.current_page;
        }catch(error){
            console.error(error);
        }
    };

</script>

<template>
    <div class="bg-white p-4 rounded lists">
        <ul class="flex flex-row justify-between w-full border border-gray-500 border-r-0 border-l-0 border-t-0 p-2 titles">
            <li v-for="(title,index) in titles" :key="index" :class=" title.class">{{ title.name }}</li>
        </ul>
        <ul class="w-full">
            <li v-for="(user, index) in users.data" :key="user.id" class="flex flex-row justify-between list" :class="{'bg-zinc-200': index % 2 != 0}">
                <div class="id" contenteditable="false">{{ user.id }}</div>
                <div class="name" contenteditable="false">{{ user.name }}</div>
                <div class="email" :contenteditable="user.editable" :class="{ 'editable': user.editable }" @input="user.email = $event.target.textContent">{{ user.email }}</div>
                <div class="notifyToken" :contenteditable="user.editable" :class="{ 'editable': user.editable }" @input="user.notifyToken = $event.target.textContent">{{ user.notifyToken }}</div>
                <!-- Buttons -->
                <div class="buttons flex flex-row">
                    <!-- Delete Button -->
                    <UserListDeleteButton :userData="user.id" @userDeleted="handelUserDeleted"></UserListDeleteButton>
                    <!-- Edit Button -->
                    <button :userData="user" @click="toggleEditUser(user)" v-if="!user.editable" class="editButton">Edit</button>
                    <div v-if="user.editable" class="flex">
                        <button @click="saveChandged(user)" class="saveButton">Save</button>
                        <button @click="cancelChanged(user)" class="cancelButton">Cancel</button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <!-- Paginator -->
    <Pagination :links="links" @urlNow="handlePageChange"></Pagination>    
</template>

<style scoped>
.lists{
    height: 410px;
}
.list{
    height: 32px;
    line-height: 32px;
    padding: 1px;
}
.id{
    width: 5%;
}
.name{
    width: 12%;
}
.email{
    width: 20%;
}
.notifyToken{
    width: 30%;
}
.editable {
    border: 1px solid #ccc;
    border-radius: 4px;
    height: 32px;
    line-height: 32px;
}
.buttons{
    width: 15%;
    align-items: center; 
}
.buttons button{
    height: 98%;
    line-height: 98%;
}
.editButton{
    background-color: #365486;
    color: aliceblue;
    border-radius: 5px;
    padding: 1px 3px;
    margin: 3px 10px;
}
button.saveButton{
    width: 50px;
    background-color: #365486;
    color: aliceblue;
    border-radius: 5px;
    padding: 1px 3px;
    margin: 3px 5px; 
    line-height: 27px;
}
button.cancelButton{
    width: 55px;
    background-color: #365486;
    color: aliceblue;
    border-radius: 5px;
    padding: 1px 3px;
    margin: 3px 10px; 
    line-height: 27px;
}
</style>