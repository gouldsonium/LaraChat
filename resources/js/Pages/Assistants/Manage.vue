<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AssistantForm from './partials/AssistantForm.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import UploadFile from './partials/UploadFile.vue';

const props = defineProps({
    assistant: Object,
    showFiles: Boolean
});
const confirmingAssistantDeletion = ref(null);

const form = useForm({});

const deleteAssistant = () => {
    form.delete(route('assistants.delete', props.assistant.id));
};

const openModal = () => {
    confirmingAssistantDeletion.value = true;
}

const closeModal = () => {
    confirmingAssistantDeletion.value = false;
};

</script>

<template>
    <AppLayout title="Assistants">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Manage {{ props.assistant.name }}
                </h2>
                <DangerButton @click="openModal">
                    Delete
                </DangerButton>
            </div>
        </template>
        <DialogModal :show="confirmingAssistantDeletion" @close="closeModal">
            <template #title>
                Delete Assistant
            </template>

            <template #content>
                Are you sure you want to delete this Assistant? Once this assistant is deleted, all of its resources and
                data will be permanently deleted.
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    @click="deleteAssistant"
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Delete Assistants
                </DangerButton>
            </template>
        </DialogModal>
        <div class="max-w-7xl mx-auto">
            <AssistantForm :assistant="props.assistant" class="my-5" />
            <UploadFile v-if="showFiles" class="my-5" />
        </div>
    </AppLayout>
</template>
