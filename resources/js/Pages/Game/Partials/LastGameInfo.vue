<script setup>
import { usePage } from '@inertiajs/vue3';

const { props } = defineProps({
    lastGame: {
        type: Object,
    },
    lastRole: {
        type: String
    }
});

const user = usePage().props.auth.user;
console.log( usePage().props.auth.user)

</script>

<template>
    <section>
         <header class="text-center">
            <h1 class="text-2xl font-medium text-gray-900 dark:text-gray-100 mt-6" v-if="lastGame.winner_team === lastRole"> Congratulations!</h1>
            <h1 class="text-2xl font-medium text-gray-900 dark:text-gray-100 mt-6" v-else> Better luck next time!</h1>
            
            <p class="text-lg text-gray-700 dark:text-gray-300 mt-6">The <strong>{{lastGame.title}}</strong> game was won by Team {{lastGame.winner_team}}.</p>
        </header>

    </section>
</template>
