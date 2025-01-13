<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CompletionForm from './partials/CompletionForm.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    completion: Object
});

const form = useForm({});

const deleteCompletion = () => {
    form.delete(route('completions.delete', props.completion.id));
};

</script>

<template>
    <AppLayout title="Completions">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Manage {{props.completion.name}}
                </h2>
                <form @submit.prevent="deleteCompletion">
                    <DangerButton type="submit">
                        Delete
                    </DangerButton>
                </form>
            </div>
        </template>
        <div class="max-w-7xl mx-auto">
            <CompletionForm :completion="props.completion" class="my-5" />
        </div>
    </AppLayout>
</template>
