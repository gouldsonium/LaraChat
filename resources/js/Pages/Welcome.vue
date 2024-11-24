<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ChatMessage from '@/Components/Chat/ChatMessage.vue';

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
const { props } = usePage();
let conversationHistory = props.conversationHistory || [];

// Initialize the Inertia form
const form = useForm({
    message: '', // The message to send
});


const submitMessage = () => {
    // Add the user message to the conversation history
    conversationHistory.push({
        role: 'user',
        content: form.message,
    });

    // Send the message to the server
    form.post(route('chat.send'), {
        onSuccess: (page) => {
            const reply = page.props.reply; // Get the reply from the server

            // Add the system's reply to the conversation history
            if (reply) {
                conversationHistory.push({
                    role: 'system',
                    content: reply,
                });
            }

            form.reset(); // Reset the form after successful submission
        },
    });
};

</script>

<template>
    <Head title="Chat Interface" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 min-h-screen flex flex-col">
        <!-- Chat Header with Navigation -->
        <header class="bg-primary text-white py-4 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold">Chat Interface</h1>
            <nav v-if="canLogin" class="flex items-center space-x-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="rounded-md px-3 py-2 bg-white text-black hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')">
                        <SecondaryButton>Log in</SecondaryButton>
                    </Link>
                    <Link v-if="canRegister" :href="route('register')">
                        <SecondaryButton>Register</SecondaryButton>
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Chat Area -->
        <main class="flex-1 overflow-y-auto p-4 space-y-4 pb-24">
            <div class="flex flex-col space-y-2">
                <template v-for="(message, index) in conversationHistory" :key="index">
                    <ChatMessage :role="message.role" :message="message.content" />
                </template>
            </div>
        </main>

        <!-- Input Box -->
        <footer class="bg-white dark:bg-gray-800 py-4 px-6 fixed bottom-0 w-full">
            <form @submit.prevent="submitMessage" class="flex items-center space-x-4">
                <TextInput
                    v-model="form.message"
                    type="text"
                    placeholder="Type your message..."
                    class="flex-1"
                    :disabled="form.processing"
                    autofocus
                />
                <PrimaryButton
                    :disabled="form.processing"
                    class="h-full py-3"
                >
                    Send
                </PrimaryButton>
            </form>
        </footer>
    </div>
</template>
