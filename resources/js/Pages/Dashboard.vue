<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CreateNewGameForm from '@/Pages/Game/Partials/CreateNewGameForm.vue';
import GameLobby from '@/Pages/Game/GameLobby.vue';
import LastGameInfo from '@/Pages/Game/Partials/LastGameInfo.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    activeGame: {
        type: Object,
    },
    lastGame: {
        type: Object,
    },
    lastRole: {
        type: String,
    },
    players: {
        type: Object,
    },
    rounds: {
        type: Object,
    },

    isNight: {
        type: Boolean
    }
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">Welcome to Mafia Game</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">This is a social game of mystery, wit, decisions, and discernment. Once you begin, you will see a town with 10 players. Each player is assigned a role. Those aligned with the Mafia must kill off other players until they outnumber the village. Those aligned with the village vote during the day to execute Mafia members. The role of each player is initially unknown until more evidence is revealed!</div>
                
                <div v-if="lastGame && !activeGame" class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <LastGameInfo
                        :lastGame="lastGame"
                        :lastRole="lastRole"
                    />
                </div>
                
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <CreateNewGameForm v-if="!activeGame"
                        class="max-w-xl"
                    />

                    <GameLobby v-else
                    :activeGame="activeGame"
                    :players="players"
                    :rounds="rounds"
                    :isNight="isNight"
                    class="max-w-7xl"
                    />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
