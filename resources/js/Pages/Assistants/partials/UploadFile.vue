<script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  import PrimaryButton from '@/Components/PrimaryButton.vue';

  const file = ref(null);
  const message = ref('');
  const success = ref(false);

  const onFileChange = (event) => {
    file.value = event.target.files[0];
  };

  const uploadFile = async () => {
    if (!file.value) {
      message.value = 'No file selected.';
      success.value = false;
      return;
    }

    const formData = new FormData();
    formData.append('file', file.value);

    try {
      const response = await axios.post(`/assistants/upload`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      message.value = response.data.message;
      success.value = true;
    } catch (error) {
      message.value = error.response?.data?.message || 'Failed to upload file.';
      success.value = false;
    }
  };
</script>

<template>
  <div class="p-4 bg-white dark:bg-gray-800 rounded shadow">
    <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Upload File for Assistant</h2>
    <form @submit.prevent="uploadFile">
      <input type="file" @change="onFileChange" class="text-gray-800 dark:text-gray-200" />
      <PrimaryButton type="submit" :disabled="!file">Upload</PrimaryButton>
    </form>
    <p v-if="message" :class="{'success-message': success, 'error-message': !success}">
      {{ message }}
    </p>
  </div>
</template>

<style scoped>
  .success-message {
    color: #4caf50;
  }

  .error-message {
    color: #f44336;
  }
</style>
