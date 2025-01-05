<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AssistantForm from './partials/AssistantForm.vue';
</script>

<template>
    <AppLayout title="Assistants">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Assistants
                </h2>
                <p class="text-gray-800 dark:text-gray-200">
                    ${{ Math.floor($page.props.auth.user.balance * 100) / 100 }}
                </p>
            </div>
        </template>

        <div class="max-w-7xl mx-auto">
            <AssistantForm class="mt-5" />
        </div>
    </AppLayout>
</template>
