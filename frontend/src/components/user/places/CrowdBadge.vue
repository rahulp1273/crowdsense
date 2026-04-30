<template>
  <div class="px-4 py-1.5 rounded-2xl font-black uppercase tracking-tighter text-[10px] shadow-sm flex items-center space-x-1.5" :class="badgeClass">
    <span class="w-2 h-2 rounded-full animate-pulse" :class="dotClass"></span>
    <span>{{ label }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  count: Number
});

const label = computed(() => {
  if (props.count < 20) return 'Relaxed';
  if (props.count < 50) return 'Steady';
  return 'Hectic';
});

const badgeClass = computed(() => {
  if (props.count < 20) return 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400';
  if (props.count < 50) return 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400';
  return 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400';
});

const dotClass = computed(() => {
  if (props.count < 20) return 'bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]';
  if (props.count < 50) return 'bg-yellow-500 shadow-[0_0_10px_rgba(234,179,8,0.5)]';
  return 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]';
});
</script>
