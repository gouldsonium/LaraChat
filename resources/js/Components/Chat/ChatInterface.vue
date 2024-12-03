<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import TextAreaInput from '../TextAreaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ChatMessage from '@/Components/Chat/ChatMessage.vue';

const { props } = usePage();
let conversationHistory = props.conversationHistory || [];

// Initialize the Inertia form
const form = useForm({
    message: '',
});

const isLoading = ref(false);

const submitMessage = () => {
    isLoading.value = true;

    conversationHistory.push({
        role: 'user',
        content: form.message,
    });

    form.post(route('chat.send'), {
        preserveScroll: true,
        onSuccess: (page) => {
            const reply = page.props.reply;

            if (reply) {
                conversationHistory.push({
                    role: 'system',
                    content: reply,
                });
            }

            form.reset();
        },
        onError: () => {
            console.error('Error sending message');
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const clearChat = () => {
    isLoading.value = true;

    form.delete(route('chat.clear'), {
        onSuccess: () => {
            conversationHistory.length = 0;
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const handleKeydown = (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        submitMessage();
    }
};

const footerHeight = ref(0);

onMounted(() => {
    const footer = document.querySelector('footer');
    if (footer) {
        footerHeight.value = footer.offsetHeight;
    }
});

// Filter out the first system message for display
const filteredConversationHistory = computed(() => {
    return conversationHistory.filter((message, index) => !(index === 0 && message.role === 'system'));
});
</script>

<template>
<div class="relative flex flex-col bg-gray-50 dark:bg-black min-h-screen">
    <!-- Chat Area -->
    <div class="flex-1 overflow-y-auto space-y-4 relative">
        <!-- Loading Overlay -->
        <div
            v-if="isLoading"
            class="absolute inset-0 flex items-center justify-center bg-black/50"
        >
            <div class="text-white text-lg font-bold sticky bottom-0">Processing...</div>
        </div>

        <!-- Messages -->
        <div class="flex flex-col space-y-2 p-4">
            <template v-for="(message, index) in filteredConversationHistory" :key="index">
                <ChatMessage :role="message.role" :message="message.content" />
            </template>
        </div>
    </div>

    <!-- Sticky Footer -->
    <footer class="sticky bottom-0 bg-white dark:bg-gray-800 py-4 px-2 sm:px-4 md:px-6">
        <form @submit.prevent="submitMessage" class="flex flex-col items-end space-y-2">
            <TextAreaInput
                v-model="form.message"
                type="text"
                placeholder="Type your message..."
                class="w-full"
                :disabled="form.processing"
                autofocus
                rows="3"
                @keydown="handleKeydown"
            />
            <div class="flex items-center justify-between w-full">
                <SecondaryButton @click="clearChat" :disabled="form.processing">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 sm:size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </SecondaryButton>
                <PrimaryButton :disabled="form.processing">
                    <span class="hidden md:flex">Send</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 md:hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </PrimaryButton>
            </div>
        </form>
    </footer>
</div>
</template>
