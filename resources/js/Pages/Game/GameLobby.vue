<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';

import { reactive } from 'vue';

const props = defineProps({
    activeGame: {
        type: Object,
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

const user = usePage().props.auth.user;
const form = useForm({
    user_id: null,
});

const data = reactive({
    activeTab: 'summary',
});

const setActiveTab = (tab) => {
    data.activeTab = tab;
};

const shouldDisplayRole = (player) => {
    return user.team === 'Mafia' && player.roles[0].team === 'Mafia';
};

</script>

<template>
    <section>
        <header class="text-center mb-8">
            <h2 class="text-2xl text-gray-900 dark:text-gray-100">Your role is: <span class=" text-3xl font-semibold"> {{user.role_title}}</span></h2>
        </header>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 flex flex-col md:flex-row md:space-x-8 text-gray-900 dark:text-gray-100">
            <div class="flex-grow w-3/5 ">
                <div class="flex space-x-4 mb-6">
                    <button @click="setActiveTab('summary')"
                    :class="{ 'bg-blue-800 text-white hover:bg-blue-900': data.activeTab === 'summary', 'bg-gray-900 text-white': data.activeTab !== 'summary' }"
                    class="py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">Game Summary</button>

                    <button @click="setActiveTab('players')"
                    :class="{ 'bg-blue-800 text-white hover:bg-blue-800': data.activeTab === 'players', 'bg-gray-900 text-white': data.activeTab !== 'players' }"
                    class="py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">Players</button>
                </div>
                
                <div v-if="data.activeTab === 'summary'" class="bg-white dark:bg-gray-700 shadow sm:rounded-lg p-6">
                    <div v-if="rounds.length">
                        <ul>
                            <li v-for="(round,index) in rounds" :key="round.id" class="mb-2">
                                <div>
                                    <h2 class="text-gray-200" v-if="round.type">
                                        {{ `${round.type.toUpperCase()} ${index + 1}` }}
                                    </h2>
                                    <div v-if="round.type == 'Night'">
                                        <p v-if="round.killed && round.user_killed" class="text-gray-300"> <strong>{{round.user_killed.nickname }}</strong> with role <strong>{{ round.user_killed.role_title}} </strong> was killed by Mafia</p>
                                        <p v-if="round.robbed && round.user_robbed" class="text-gray-300"> <strong>{{ round.user_robbed.nickname }}</strong> with role <strong>{{ round.user_robbed.role_title }} </strong> was robbed by Thief</p>
                                        <p v-if="round.saved && round.user_saved" class="text-gray-300"> <strong>{{ round.user_saved.nickname }}</strong> was saved by Doctor</p>
                                        <p v-if="round.investigated && round.user_investigated" class="text-gray-300"><strong>{{ round.user_investigated.nickname}}</strong> with role <strong>{{ round.user_investigated.role_title }}</strong> was investigated by Detective</p>
                                    </div>
                                    <div v-else>
                                        <p v-if="round.voted_out && round.user_voted_out" class="text-gray-300"> <strong>{{ round.user_voted_out.nickname }}</strong> with role <strong>{{ round.user_voted_out.role_title }} </strong> was voted by Villagers</p>
                                    </div>
                                    <hr class="my-2">

                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        No data yet.
                    </div>
                </div>


                <div v-if="data.activeTab === 'players'" class="bg-white dark:bg-gray-700 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Players</h3>

                    <ul>
                        <li v-for="player in players" :key="player.id" class="mb-2 flex items-center justify-between">
                            <span class="text-gray-200">{{ player.nickname }}</span>
                            <span v-if="shouldDisplayRole(player)" class="text-gray-300">{{ player.roles[0].title }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-700 shadow sm:rounded-lg p-6 max-w-xl text-center w-2/5">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Current round: {{ isNight ? 'NIGHT' : 'DAY'}}</h3>


                <form @submit.prevent="form.post('/game/'+ activeGame.id +'/round')">
                    <div v-if="(!isNight || user.role_title !='Villager') && user.is_alive">
                        <h3 v-if="!isNight" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Vote for the suspected mafia member</h3>
                        <h3 v-else-if="isNight && user.role_title === 'Mafia'" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Vote the one you want to kill</h3>
                        <h3 v-else-if="isNight && user.role_title === 'Thief'" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Vote the one you want to rob</h3>
                        <h3 v-else-if="isNight && user.role_title === 'Doctor'" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Vote the one you want to save</h3>
                        <h3 v-else-if="isNight && user.role_title === 'Detective'" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Vote the one you want to investigate</h3>
                        <ul>
                            <li v-for="player in players" :key="player.id" class="mb-2 flex items-center justify-between">
                                <div v-if="!isNight || (isNight && user.team === 'Village' && user.role_title !== 'Villager')">
                                <label v-if="player.pivot.status == 1" :for="'userCheckbox-' + player.id" class="cursor-pointer">
                                    <input
                                    :id="'userCheckbox-' + player.id"
                                    type="radio"
                                    v-model="form.user_id"
                                    :value="player.id"
                                    class="mr-2"
                                    />
                                    {{ player.nickname }}
                                </label>
                                </div>

                                <div v-if="isNight && player.roles[0].team !== 'Mafia' && (user.role_title === 'Mafia' || user.role_title === 'Thief')" :for="'userCheckbox-' + player.id">
                                <label v-if="player.pivot.status == 1" :for="'userCheckbox-' + player.id" class="cursor-pointer">
                                    <input
                                    :id="'userCheckbox-' + player.id"
                                    type="radio"
                                    v-model="form.user_id"
                                    :value="player.id"
                                    class="mr-2"
                                    />
                                    {{ player.nickname }}
                                </label>
                                </div>
                            </li>
                        </ul>

                        <PrimaryButton class="mt-6 py-3 bg-gray-900 text-white" :disabled="form.user_id === null">Vote</PrimaryButton>
                    </div>
                    <div v-else-if=" isNight">
                        <p>Night has fallen in your village. Mafia are voting to decide who to kill tonight</p>

                        <PrimaryButton class="mt-6 py-3 bg-green-500 text-white hover:bg-green-700">Continue</PrimaryButton>
                    </div>
                    <div v-else-if="!user.is_alive && !isNight">
                        <p>Day has come in your village. Villagers are voting to decide who to accuse today.</p>
                        <PrimaryButton class="mt-6 py-3 bg-red-500 text-white hover:bg-red-700">Continue</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>