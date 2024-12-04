<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ChatInterface from '@/Components/Chat/ChatInterface.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="max-w-7xl mx-auto">
            <ChatInterface model="gpt-4o" />
        </div>
    </AppLayout>
</template>
