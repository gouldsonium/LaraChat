<script setup>
import { Head } from '@inertiajs/vue3';
import ChatInterface from '@/Components/Chat/ChatInterface.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    reply: {
        required: false
    }
});
</script>

<template>
    <Head title="Chat Interface" />
    <ChatInterface :canRegister="canRegister" :canLogin="canLogin" />
</template>
