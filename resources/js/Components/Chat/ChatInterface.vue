<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import TextAreaInput from '../TextAreaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ChatMessage from '@/Components/Chat/ChatMessage.vue';

const props = defineProps({
  paid: {
    type: Boolean,
    default: false
  },
  completion: {
    type: Object,
    required: false
  },
  conversationHistory: {
    type: Array,
    required: true
  },
  assistant: {
    type: Object,
    required: false
  },
  threadId: {
    type: String,
    required: false
  },
});

let conversationHistory = props.conversationHistory || [];
const message = ref('');
const isLoading = ref(false);

const submitMessage = async () => {
  if (!message.value.trim()) return;

  isLoading.value = true;
  conversationHistory.push({
    role: 'user',
    content: message.value,
  });

  try {
    const payload = {
      message: message.value,
      paid: props.paid,
      conversationHistory,
      completion: props.completion,
      assistant_id: props.assistant?.assistant_id,
      thread_id: props.threadId,
    };

    const url = props.completion
      ? route('completions.send', props.completion.id)
      : route('assistants.send', props.assistant.id);

    const res = await axios.post(url, payload);

    conversationHistory.push({
      role: 'system',
      content: res.data.reply,
    });
  } catch (error) {
    console.error('Error sending message:', error);
  } finally {
    isLoading.value = false;
    message.value = '';
  }
};

const clearChat = async () => {
  isLoading.value = true;

  try {
    const url = props.completion
      ? route('completions.clear', props.completion.id)
      : route('assistants.clear', props.assistant.id);

    const res = await axios.delete(url);
    console.log(res)
    conversationHistory.length = 0;
  } catch (error) {
    console.error('Error clearing chat:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleKeydown = (event) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    submitMessage();
  }
};

const footerHeight = ref(0);

onMounted(() => {
  const footer = document.querySelector('footer');
  if (footer) {
    footerHeight.value = footer.offsetHeight;
  }
});

const filteredConversationHistory = computed(() => {
  return conversationHistory.filter((message, index) => !(index === 0 && message.role === 'system'));
});
</script>

<template>
  <div class="flex flex-col bg-gray-50 dark:bg-black ">
    <p class="sticky right-0 top-0 text-xl text-primary self-end mr-5 mt-1" style="z-index: 100;">
      Balance: ${{ (Math.floor($page.props.auth.user.balance * 100) / 100).toFixed(2) }}
    </p>
    <!-- Chat Area -->
    <div class="flex-1 overflow-y-auto space-y-4 relative">
      <!-- Loading Overlay -->
      <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-black/50">
        <div class="text-white text-lg font-bold sticky bottom-0">Processing...</div>
      </div>

      <!-- Messages -->
      <div class="flex flex-col space-y-2 p-4">
        <template v-for="(message, index) in filteredConversationHistory" :key="index">
          <ChatMessage :role="message.role" :message="message.content" />
        </template>
      </div>
    </div>

    <!-- Sticky Footer -->
    <footer class="sticky bottom-0 bg-white dark:bg-gray-800 py-4 px-2 sm:px-4 md:px-6">
      <form @submit.prevent="submitMessage" class="flex flex-col items-end space-y-2">
        <TextAreaInput v-model="message" type="text" placeholder="Type your message..." class="w-full"
          :disabled="isLoading" autofocus rows="3" @keydown="handleKeydown" />
        <div class="flex items-center justify-between w-full">
          <SecondaryButton @click="clearChat" :disabled="isLoading">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-4 sm:size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
          </SecondaryButton>
          <PrimaryButton :disabled="isLoading || !message.trim()">
            <span class="hidden md:flex">Send</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-4 md:hidden">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
          </PrimaryButton>
        </div>
      </form>
    </footer>
  </div>
</template>
