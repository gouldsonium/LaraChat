<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ChatInterface from '@/Components/Chat/ChatInterface.vue';

defineProps({
    completion: Object,
    conversationHistory: Array
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{completion.name}}
                </h2>
            </div>
        </template>

        <ChatInterface class="h-[calc(100vh-210px)] max-w-7xl mx-auto"
            :completion="completion"
            :paid="true"
            :conversation-history="conversationHistory"
        />
    </AppLayout>
</template>
