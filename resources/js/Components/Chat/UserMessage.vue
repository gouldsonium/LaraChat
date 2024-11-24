<script setup>
defineProps({
    message: {
        type: String,
        required: true
    }
})
</script>
<template>
    <div class="self-end bg-primary text-white p-3 rounded-md max-w-xs">
        {{ message }}
    </div>
</template>
