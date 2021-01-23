<template>
<div>

    <form action="#" @submit.prevent="handleLogin">
        <input type="email" v-model="email" name="email" placeholder="email">
        <input type="password" v-model="password" name="password" placeholder="password">
        <input type="submit">
    </form>


    <h1>Restaurants</h1>
    <ul>
        <li v-for="restaurant of restaurants">{{ restaurant.name }}</li>
    </ul>
</div>
</template>

<script>
    export default {
        data(){
            return {
                email : '',
                password : '',
                restaurants : []
            }
        },
        methods : {
            getRestaurants(){
                axios.get('/api/restaurants')
                    .then( response => {
                        this.restaurants = response.data;
                        console.log( response.data );
                    });
            }
        },
        mounted() {
            this.getRestaurants();
        }
    }
</script>
