<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'; // Importing lifecycle hooks
import TextAreaInput from '../TextAreaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ChatMessage from '@/Components/Chat/ChatMessage.vue';

const { props } = usePage();
let conversationHistory = props.conversationHistory || [];

// Initialize the Inertia form
const form = useForm({
    message: '', // The message to send
});

const isLoading = ref(false); // State for loading indicator

// Function to submit a message
const submitMessage = () => {
    isLoading.value = true; // Show loading indicator

    conversationHistory.push({
        role: 'user',
        content: form.message,
    });

    form.post(route('chat.send'), {
        preserveScroll: true, // Prevent page scrolling
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
            // Handle any errors here
            console.error('Error sending message');
        },
        onFinish: () => {
            isLoading.value = false; // Hide loading indicator
        },
    });
};

// Function to clear chat history
const clearChat = () => {
    isLoading.value = true; // Show loading indicator

    form.delete(route('chat.clear'), {
        onSuccess: () => {
            conversationHistory.length = 0; // Clear the local chat history
        },
        onFinish: () => {
            isLoading.value = false; // Hide loading indicator
        },
    });
};

// Function to handle key presses in the textarea
const handleKeydown = (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault(); // Prevent newline
        submitMessage(); // Submit the form
    }
};

// Ensures the message is visible
const footerHeight = ref(0);
const updateHeights = () => {
    const footer = document.querySelector('footer');
    footerHeight.value = footer ? footer.offsetHeight : 0;
};

// Watch for footer height changes dynamically
const observeFooterHeight = () => {
    const footer = document.querySelector('footer');
    if (footer) {
        const observer = new MutationObserver(updateHeights);
        observer.observe(footer, { attributes: true, childList: true, subtree: true });
    }
};

// Add this inside `onMounted` and `onBeforeUnmount`
onMounted(() => {
    updateHeights();
    observeFooterHeight(); // Start observing footer changes
    window.addEventListener('resize', updateHeights);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updateHeights);
});


// Filter out the first system message for display
const filteredConversationHistory = computed(() => {
    return conversationHistory.filter((message, index) => !(index === 0 && message.role === 'system'));
});

</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 min-h-screen flex flex-col">
        <div
            v-if="isLoading"
            class="fixed bg-black/50 flex items-center justify-center z-40 w-full h-screen"
        >
            <div class="text-white text-lg font-bold" style="bottom: 172px;" >Processing...</div>
        </div>

        <!-- Chat Header with Navigation -->
        <header class="bg-primary text-white py-4 px-6 flex items-center justify-between z-50">
            <h1 class="text-lg font-semibold">LaraChat</h1>
            <SecondaryButton @click="clearChat" :disabled="form.processing">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 sm:size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </SecondaryButton>
        </header>

        <!-- Chat Area -->
        <main class="flex-1 overflow-y-auto p-4 space-y-4" :style="{ paddingBottom: footerHeight + 10 + 'px' }">
            <div class="flex flex-col space-y-2">
                <template v-for="(message, index) in filteredConversationHistory" :key="index">
                    <ChatMessage :role="message.role" :message="message.content" />
                </template>
            </div>
        </main>

        <!-- Input Box -->
        <footer class="bg-white dark:bg-gray-800 py-4 px-2 sm:px-4 md:px-6 fixed bottom-0 w-full z-50">
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
                <PrimaryButton
                    :disabled="form.processing"
                    class="h-full md:py-3"
                >
                <span class="hidden md:flex">Send</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 md:hidden">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
                </PrimaryButton>
            </form>
        </footer>
    </div>
</template>
