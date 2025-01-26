<script setup>
  import { ref } from 'vue';
  import axios from 'axios';

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
  <div class="upload-container">
    <h1>Upload File for Assistant</h1>
    <form @submit.prevent="uploadFile">
      <input type="file" @change="onFileChange" />
      <button type="submit" :disabled="!file" class="upload-button">Upload</button>
    </form>
    <p v-if="message" :class="{'success-message': success, 'error-message': !success}">
      {{ message }}
    </p>
  </div>
</template>

<style scoped>
  .upload-container {
    max-width: 500px;
    margin: 2rem auto;
    padding: 1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .upload-button {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    background-color: #4caf50;
    color: white;
    cursor: pointer;
  }

  .upload-button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }

  .success-message {
    color: #4caf50;
  }

  .error-message {
    color: #f44336;
  }
</style>
