<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

import AppLayout from '@/Layouts/AppLayout.vue';
import AssistantForm from './partials/AssistantForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Props
defineProps({
    assistants: Array
})

// State for toggling the form visibility
const isFormVisible = ref(false);

// Toggle form visibility
const toggleForm = () => {
    isFormVisible.value = !isFormVisible.value;
};
</script>

<template>
    <AppLayout title="Assistants">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Assistants
                </h2>
                <PrimaryButton @click="toggleForm">
                    {{ isFormVisible ? 'Cancel' : 'Create Assistant' }}
                </PrimaryButton>
            </div>
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Conditionally show the AssistantForm -->
            <AssistantForm v-if="isFormVisible" class="my-5" />

            <!-- If there are no assistants -->
            <h2 v-if="assistants.length === 0 && !isFormVisible" class="dark:text-gray-200 text-center py-4">
                No assistants available. Please create one.
            </h2>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 my-5">
                <div
                    v-for="(assistant, index) in assistants"
                    class="p-4 bg-white dark:bg-gray-800 rounded shadow dark:text-gray-200"
                    :key="index"
                >
                    <h3>{{ assistant.name }}</h3>
                    <p class="my-2">{{ assistant.description }}</p>
                    <p>Model: {{ assistant.model }}</p>
                    <div class="flex justify-between items-center mt-5">
                        <Link :href="route('dashboard')">
                            <SecondaryButton>Chat</SecondaryButton>
                        </Link>
                        <Link :href="route('assistants.manage', assistant.id)">
                            <SecondaryButton>Manage</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
