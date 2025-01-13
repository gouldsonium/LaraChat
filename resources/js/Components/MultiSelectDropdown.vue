<script setup>
import { ref } from 'vue';

// Define the props and emit setup
const props = defineProps({
  options: {
    type: Array,
    required: true, // Array of all available options (objects with `value` and `text`)
  },
  modelValue: {
    type: Array,
    default: () => [], // Array of selected `value`s
  },
  placeholder: {
    type: String,
    default: 'Select options',
  },
});

const emit = defineEmits(['update:modelValue']); // Emits to sync changes with parent component

// Dropdown state
const dropdownOpen = ref(false);

// Toggle the selection of an option
const toggleOption = (value) => {
  const selected = [...props.modelValue]; // Copy current selected options
  const index = selected.indexOf(value);
  if (index > -1) {
    selected.splice(index, 1); // Remove the option if it exists
  } else {
    selected.push(value); // Add the option if it doesn't exist
  }
  emit('update:modelValue', selected); // Emit updated value to parent
};

// Check if an option is selected
const isSelected = (value) => props.modelValue.includes(value);
</script>

<template>
  <div class="relative">
    <!-- Dropdown Button -->
    <button
      type="button"
      class="border bg-white dark:bg-gray-900 rounded-md px-4 py-2 shadow-sm w-full text-left"
      @click="dropdownOpen = !dropdownOpen"
      :class="dropdownOpen ? 'border-primary' : 'border-gray-300 dark:border-gray-700'"
    >
      <span v-if="props.modelValue.length" class="dark:text-gray-300">
        {{ props.options
          .filter((option) => props.modelValue.includes(option.value))
          .map((option) => option.text)
          .join(', ') }}
      </span>
      <span v-else class="text-gray-500">{{ props.placeholder }}</span>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="dropdownOpen"
      class="absolute z-10 mt-2 bg-white dark:bg-gray-800 border border-primary rounded-md shadow-lg w-full"
    >
      <ul>
        <li
          v-for="(option, index) in props.options"
          :key="index"
          class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center dark:text-gray-300"
          @click="toggleOption(option.value)"
        >
          <input
            type="checkbox"
            :checked="isSelected(option.value)"
            class="mr-2 text-primary"
            readonly
          />
          {{ option.text }}
        </li>
      </ul>
    </div>
  </div>
</template>
