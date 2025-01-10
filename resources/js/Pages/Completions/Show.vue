<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

import AppLayout from '@/Layouts/AppLayout.vue';
import CompletionForm from './partials/CompletionForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Props
defineProps({
    completions: Array
})

// State for toggling the form visibility
const isFormVisible = ref(false);

// Toggle form visibility
const toggleForm = () => {
    isFormVisible.value = !isFormVisible.value;
};
</script>

<template>
    <AppLayout title="Completions">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Completions
                </h2>
                <PrimaryButton @click="toggleForm">
                    {{ isFormVisible ? 'Cancel' : 'Create Completion' }}
                </PrimaryButton>
            </div>
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Conditionally show the CompletionForm -->
            <CompletionForm v-if="isFormVisible" @success="toggleForm" class="my-5" />

            <!-- If there are no completions -->
            <h2 v-if="completions.length === 0 && !isFormVisible" class="dark:text-gray-200 text-center py-4">
                No completions available. Please create one.
            </h2>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 my-5">
                <div
                    v-for="(completion, index) in completions"
                    class="p-4 bg-white dark:bg-gray-800 rounded shadow dark:text-gray-200"
                    :key="index"
                >
                    <h3>{{ completion.name }}</h3>
                    <p class="my-2">{{ completion.description }}</p>
                    <p>Model: {{ completion.model }}</p>
                    <div class="flex justify-between items-center mt-5">
                        <Link :href="route('completions.chat', completion.id)">
                            <SecondaryButton>Chat</SecondaryButton>
                        </Link>
                        <Link :href="route('completions.manage', completion.id)">
                            <SecondaryButton>Manage</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
