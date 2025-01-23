<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MultiSelectDropdown from '@/Components/MultiSelectDropdown.vue';

// Props
const props = defineProps({
    assistant: Object, // Optional prop for editing an existing assistant
});

// Initialize form with assistant data if provided
const form = useForm({
    model: props.assistant?.model || '',
    name: props.assistant?.name || '',
    description: props.assistant?.description || '',
    instructions: props.assistant?.instructions || '',
    tools: props.assistant?.tools || [],
    top_p: props.assistant?.top_p || 1,
    temperature: props.assistant?.temperature || 1
});

let errorDetails = null;

const emit = defineEmits(['success']);

// Determine endpoint based on presence of assistant
const submitForm = () => {
    errorDetails = null;

    const endpoint = props.assistant
        ? route('assistants.update', props.assistant.id)
        : route('assistants.create');

    const method = props.assistant ? 'put' : 'post';

    form[method](endpoint, {
        onSuccess: () => {
            if (!props.assistant) {
                form.reset();  // Only reset the form if it's a create action
            }
            emit('success');  // Emit event on successful submission
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
        <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">
            {{ props.assistant ? 'Update Assistant' : 'Create Assistant' }}
        </h3>
        <div v-if="errorDetails" class="bg-red-500 text-white p-4 mb-4 rounded-md">
            <pre>{{ errorDetails }}</pre>
        </div>
        <div>
            <InputLabel for="model" value="Model" />
            <select
                id="model"
                v-model="form.model"
                name="model"
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
            <TextInput id="name" v-model="form.name" placeholder="Assistant Name" class="w-full" name="name" autocomplete="name" />
            <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <div>
            <InputLabel for="description" value="Description" />
            <TextAreaInput
                id="description"
                name="description"
                v-model="form.description"
                placeholder="Provide assistant description"
                rows="4"
                class="w-full"
            />
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ 512 - form.description.length }} Characters remaining
            </p>
            <InputError :message="form.errors.description" class="mt-2" />
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
            <InputLabel value="Tools (comma-separated)" />
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

        <details>
            <summary class="text-gray-700 dark:text-gray-300">Advanced</summary>

            <div class="mt-3">
                <InputLabel for="top_p" value="top_p" />
                <TextInput
                    id="top_p"
                    v-model="form.top_p"
                    placeholder="Assistant top_p"
                    class="w-full"
                    name="top_p"
                    autocomplete="top_p"
                    type="number"
                    min="0.1" max="1"
                    step="0.1"
                />
                <InputError :message="form.errors.top_p" class="mt-2" />
            </div>

            <div class="mt-3">
                <InputLabel for="temperature" value="temperature" />
                <TextInput
                    id="temperature"
                    v-model="form.temperature"
                    placeholder="Assistant temperature"
                    class="w-full"
                    name="temperature"
                    autocomplete="temperature"
                    type="number"
                    min="0.1" max="2"
                    step="0.1"
                />
                <InputError :message="form.errors.temperature" class="mt-2" />
            </div>
        </details>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
            <SecondaryButton @click="form.reset()" :disabled="form.processing" class="mr-2">
                Reset
            </SecondaryButton>
            <PrimaryButton :disabled="form.processing">
                {{ props.assistant ? 'Update Assistant' : 'Create Assistant' }}
            </PrimaryButton>
        </div>
    </form>
</template>
