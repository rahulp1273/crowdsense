<template>
  <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-50 dark:border-gray-700 hover:scale-[1.03] transition-all duration-300 group">
    <div class="flex justify-between items-start mb-4">
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl transition-all duration-300 group-hover:rotate-6" :class="iconBg">
        {{ icon }}
      </div>
      <div v-if="trend" class="text-xs font-black px-2 py-1 rounded-full" :class="trend > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
        {{ trend > 0 ? '+' : '' }}{{ trend }}%
      </div>
    </div>
    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">{{ label }}</div>
    <div class="text-3xl font-black text-gray-800 dark:text-white leading-none">{{ value }}</div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: [String, Number],
  icon: String,
  iconBg: String,
  trend: Number
});
</script>
