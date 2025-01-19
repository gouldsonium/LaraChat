<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CompletionForm from './partials/CompletionForm.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';

const props = defineProps({
    completion: Object
});

const confirmingCompletionDeletion = ref(null);
const form = useForm({});

const deleteCompletion = () => {
    form.delete(route('completions.delete', props.completion.id));
};

const openModal = () => {
    confirmingCompletionDeletion.value = true;
}

const closeModal = () => {
    confirmingCompletionDeletion.value = false;
};
</script>

<template>
    <AppLayout title="Completions">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Manage {{props.completion.name}}
                </h2>
                <DangerButton @click="openModal">
                    Delete
                </DangerButton>
            </div>
        </template>
        <DialogModal :show="confirmingCompletionDeletion" @close="closeModal">
            <template #title>
                Delete Completion
            </template>

            <template #content>
                Are you sure you want to delete this Completion? Once this Completion is deleted, all of its resources and
                data will be permanently deleted.
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    @click="deleteCompletion"
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Delete Completion
                </DangerButton>
            </template>
        </DialogModal>
        <div class="max-w-7xl mx-auto">
            <CompletionForm :completion="props.completion" class="my-5" />
        </div>
    </AppLayout>
</template>
