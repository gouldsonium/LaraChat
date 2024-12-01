<script setup>
import { computed } from 'vue';
import { formatMessage } from '@/utils/formatMessage'; // Import formatMessage

// Define the prop for message
const props = defineProps({
    message: {
        type: String,
        required: true
    },
    role: {
        type: String,
        required: true
    }
});

// Use computed property to format message
const formattedMessage = computed(() => formatMessage(props.message));
const messageClass = computed(() => {
    return props.role == 'user' ? 'self-end bg-primary text-white ml-5'
    : 'self-start bg-gray-200 text-black dark:bg-gray-800 dark:text-white mr-5'
})
</script>

<template>
    <div class="rounded-md max-w-7xl p-3 overflow-x-auto" :class="messageClass" v-html="formattedMessage"></div>
</template>
