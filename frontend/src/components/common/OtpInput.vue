<template>
  <div class="flex justify-between items-center gap-2 max-w-xs mx-auto">
    <input
      v-for="(digit, index) in otpLength"
      :key="index"
      :ref="el => inputRefs[index] = el"
      v-model="otpDigits[index]"
      type="text"
      inputmode="numeric"
      maxlength="1"
      class="w-12 h-14 text-center text-2xl font-bold border-2 rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
      @input="onInput($event, index)"
      @keydown="onKeyDown($event, index)"
      @paste="onPaste($event)"
    />
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  length: {
    type: Number,
    default: 6
  }
});

const emit = defineEmits(['update:modelValue', 'complete']);

const otpLength = props.length;
const otpDigits = ref(Array(otpLength).fill(''));
const inputRefs = ref([]);

watch(() => props.modelValue, (newVal) => {
  if (!newVal) {
    otpDigits.value = Array(otpLength).fill('');
  }
});

const emitOtp = () => {
  const code = otpDigits.value.join('');
  emit('update:modelValue', code);
  if (code.length === otpLength) {
    emit('complete', code);
  }
};

const onInput = (event, index) => {
  const value = event.target.value;
  // Ensure only numbers
  if (!/^\d*$/.test(value)) {
    otpDigits.value[index] = '';
    return;
  }
  
  emitOtp();
  
  if (value && index < otpLength - 1) {
    inputRefs.value[index + 1].focus();
  }
};

const onKeyDown = (event, index) => {
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    inputRefs.value[index - 1].focus();
  }
};

const onPaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text/plain').replace(/\D/g, '').slice(0, otpLength);
  
  if (!pastedData) return;
  
  for (let i = 0; i < pastedData.length; i++) {
    otpDigits.value[i] = pastedData[i];
  }
  
  emitOtp();
  
  const focusIndex = Math.min(pastedData.length, otpLength - 1);
  nextTick(() => {
    inputRefs.value[focusIndex].focus();
  });
};
</script>
