<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MultiSelectDropdown from '@/Components/MultiSelectDropdown.vue';

const form = useForm({
    model: '',
    name: '',
    instructions: '',
    tools: [],
});

let errorDetails = null;

const submitForm = () => {
    errorDetails = null;

    form.post(route('assistants.create'), {
        onSuccess: () => {
            form.reset();
        },
        onError: (formError) => {
            console.error(formError);
            errorDetails = formError.details;
        },
    });
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4 p-4 bg-white dark:bg-gray-800 rounded shadow">
        <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Create Assistant</h3>
        <div v-if="errorDetails" class="bg-red-500 text-white p-4 mb-4 rounded-md">
            <pre>{{ errorDetails }}</pre>
        </div>
        <div>
            <InputLabel for="model" value="Model" />
            <select
                id="model"
                v-model="form.model"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
            >
                <option value="" disabled selected hidden>Select a model</option>
                <option value="gpt-4">GPT-4</option>
                <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                <option value="gpt-4o-mini">GPT-4o Mini</option>
            </select>
            <InputError :message="form.errors.model" class="mt-2" />
        </div>

        <div>
            <InputLabel for="name" value="Assistant Name" />
            <TextInput id="name" v-model="form.name" placeholder="Assistant Name" class="w-full" />
            <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <div>
            <InputLabel for="instructions" value="Instructions" />
            <TextAreaInput
                id="instructions"
                v-model="form.instructions"
                placeholder="Provide assistant instructions"
                rows="4"
                class="w-full"
            />
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ 2000 - form.instructions.length }} Characters remaining
            </p>
            <InputError :message="form.errors.instructions" class="mt-2" />
        </div>

        <div>
            <InputLabel for="tools" value="Tools (comma-seperated)" />
            <MultiSelectDropdown
                id="tools"
                :options="[
                    { value: 'code_interpreter', text: 'Code Interpreter' },
                    { value: 'file_search', text: 'File Search' },
                ]"
                v-model="form.tools"
                placeholder="Select tools"
            />
            <InputError :message="form.errors.tools" class="mt-2" />
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
            <SecondaryButton @click="form.reset()" :disabled="form.processing" class="mr-2">
                Reset
            </SecondaryButton>
            <PrimaryButton :disabled="form.processing">
                Create Assistant
            </PrimaryButton>
        </div>
    </form>
</template>
