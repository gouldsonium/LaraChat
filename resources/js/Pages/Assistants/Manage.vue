<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AssistantForm from './partials/AssistantForm.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    assistant: Object
});

const form = useForm({});

const deleteAssistant = () => {
    form.delete(route('assistants.delete', props.assistant.id));
};

</script>

<template>
    <AppLayout title="Assistants">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Manage {{props.assistant.name}}
                </h2>
                <form @submit.prevent="deleteAssistant">
                    <DangerButton type="submit">
                        Delete
                    </DangerButton>
                </form>
            </div>
        </template>
        <div class="max-w-7xl mx-auto">
            <AssistantForm :assistant="props.assistant" class="my-5" />
        </div>
    </AppLayout>
</template>
