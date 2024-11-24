<script setup>
defineProps({
    message: {
        type: String,
        required: true
    }
})
</script>
<template>
    <div class="self-start bg-gray-200 text-black p-3 rounded-md max-w-7xl dark:bg-gray-800 dark:text-white">
        {{ message }}
    </div>
</template>
