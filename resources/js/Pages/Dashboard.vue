<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
const { props } = usePage();

// Array of stats to display
const stats = [
  {
    title: "Completions on the platform",
    value: () => props.completions.length,
  },
  {
    title: "Assistants on the platform",
    value: () => props.assistants.length,
  },
  {
    title: "Models",
    value: () => props.models.length,
  },
  {
    title: "Balance",
    value: () => Math.floor(props.auth.user.balance * 100) / 100,
  },
];
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Dashboard
        </h2>
        <p class="text-gray-800 dark:text-gray-200"></p>
      </div>
    </template>

    <div class="max-w-7xl mx-auto">
      <div class="dark:bg-gray-900 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
          <div class="mx-auto max-w-2xl lg:max-w-none">
            <div class="text-center">
              <h2 class="text-balance text-4xl font-semibold tracking-tight dark:text-white sm:text-5xl">
                Platform Overview
              </h2>
              <p class="mt-4 text-lg/8 text-gray-800 dark:text-gray-300">
                Below is an overview of what is on this platform
              </p>
            </div>
            <dl
              class="mt-16 grid grid-cols-1 gap-0.5 overflow-hidden rounded-2xl text-center sm:grid-cols-2 lg:grid-cols-4">
              <div v-for="(stat, index) in stats" :key="index" class="flex flex-col bg-gray-200 dark:bg-white/5 p-8">
                <dt class="text-sm/6 font-semibold text-gray-800 dark:text-gray-300">{{ stat.title }}</dt>
                <dd class="order-first text-3xl font-semibold tracking-tight dark:text-white">{{ stat.value() }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
