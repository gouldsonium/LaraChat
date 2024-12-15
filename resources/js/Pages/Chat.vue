<script setup>
import { Head } from '@inertiajs/vue3';
import ChatInterface from '@/Components/Chat/ChatInterface.vue';
</script>

<template>
    <Head title="Chat Interface" />
    <!-- Chat Header with Navigation -->
    <header class="bg-primary text-white py-4 px-6 flex items-center justify-between z-50">
        <h1 class="text-lg font-semibold">LaraChat</h1>
    </header>
    <ChatInterface />
</template>
