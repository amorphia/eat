<template>
    <div>
        <div v-if="$route.params.pathMatch" class="">404 : {{ $route.params.pathMatch }}</div>
        <h1>Home</h1>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
