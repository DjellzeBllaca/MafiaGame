
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    roles: {
        type: Object,
    },
});
</script>

<template>
    <Head title="Roles" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">Welcome to Mafia Game</h2>
            <p class="text-gray-500 dark:text-gray-250 mb-6 text-center">
            Explore the various roles in the game and learn about their unique abilities and affiliations.
            </p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4">
                            <div v-for="role in roles" :key="role.id" class="flex items-center bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-transform transform hover:scale-105">
                                <img :src="role.avatar" :alt="role.title" class="w-32 h-32 object-cover rounded-md mr-4">
                                <div class="text-gray-900 dark:text-gray-100 max-w-xs">
                                <h3 class="text-lg font-semibold ml-4">{{ role.title }}</h3>
                                <p class="text-sm text-gray-500 ml-4 mb-2">Sided with the {{ role.team }}</p>
                                <p class="text-sm text-gray-500 ml-4">{{ role.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>


