<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    }
});
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
                    class="rounded-md px-3 py-2 bg-white text-black hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF2D20]"
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
        <main class="flex-1 overflow-y-auto p-4 space-y-4">
            <div class="flex flex-col space-y-2">
                <!-- Example Messages -->
                <div class="self-start bg-gray-200 text-black p-3 rounded-md max-w-xs dark:bg-gray-800 dark:text-white">
                    Hello! How can I help you today?
                </div>
                <div class="self-end bg-primary text-white p-3 rounded-md max-w-xs">
                    I need assistance with my project.
                </div>
                <!-- Add more messages dynamically here -->
            </div>
        </main>

        <!-- Input Box -->
        <footer class="bg-white dark:bg-gray-800 py-4 px-6">
            <div class="flex items-center space-x-4">
                <TextInput
                    type="text"
                    placeholder="Type your message..."
                    class="flex-1"
                    autofocus
                />
                <PrimaryButton class="h-full py-3">
                    Send
                </PrimaryButton>
            </div>
        </footer>
    </div>
</template>
