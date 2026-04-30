<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-[400px] space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="w-12 h-12 bg-indigo-600 rounded-2xl mx-auto flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-indigo-200 dark:shadow-none mb-4">C</div>
        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">Reset Password</h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">
          {{ step === 1 ? 'Enter your email to receive a reset code' : 'Enter the code and your new password' }}
        </p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-8 rounded-[2rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-100 dark:border-gray-800">
        <!-- Step 1: Email Entry -->
        <form v-if="step === 1" @submit.prevent="handleSendOtp" class="space-y-6">
          <div class="space-y-2">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Email Address</label>
            <input 
              v-model="form.email" 
              type="email" 
              required 
              placeholder="name@company.com"
              class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none"
            >
          </div>
          
          <div v-if="error" class="bg-red-50 dark:bg-red-900/20 p-3 rounded-xl text-center">
            <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest">{{ error }}</p>
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-black uppercase tracking-widest text-xs py-4 rounded-xl hover:bg-indigo-600 dark:hover:bg-indigo-50 transition-all active:scale-95 disabled:opacity-50 cursor-pointer"
          >
            {{ loading ? 'Sending Code...' : 'Send Reset Code' }}
          </button>
        </form>

        <!-- Step 2: OTP + New Password -->
        <form v-else @submit.prevent="handleResetPassword" class="space-y-6">
          <div class="space-y-4">
            <div class="flex justify-center mb-6">
              <OtpInput :length="6" @complete="onOtpComplete" />
            </div>

            <div class="space-y-2">
              <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">New Password</label>
              <input 
                v-model="form.password" 
                type="password" 
                required 
                placeholder="••••••••"
                class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none"
              >
            </div>

            <div class="space-y-2">
              <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Confirm Password</label>
              <input 
                v-model="form.password_confirmation" 
                type="password" 
                required 
                placeholder="••••••••"
                class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none"
              >
            </div>
          </div>

          <div v-if="error" class="bg-red-50 dark:bg-red-900/20 p-3 rounded-xl text-center">
            <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest">{{ error }}</p>
          </div>

          <button 
            type="submit" 
            :disabled="loading || !form.otp"
            class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-black uppercase tracking-widest text-xs py-4 rounded-xl hover:bg-indigo-600 dark:hover:bg-indigo-50 transition-all active:scale-95 disabled:opacity-50 cursor-pointer"
          >
            {{ loading ? 'Resetting...' : 'Reset Password' }}
          </button>
        </form>
      </div>

      <div class="text-center">
        <router-link to="/login" class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:text-indigo-600 transition-colors">
          ← Back to Login
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import OtpInput from '../components/OtpInput.vue';

const router = useRouter();
const step = ref(1);
const loading = ref(false);
const error = ref('');

const form = ref({
  email: '',
  otp: '',
  password: '',
  password_confirmation: ''
});

const handleSendOtp = async () => {
  loading.value = true;
  error.value = '';
  try {
    await axios.post('/api/forgot-password', { email: form.value.email });
    step.value = 2;
  } catch (err) {
    error.value = err.response?.data?.message || 'Could not find account';
  } finally {
    loading.value = false;
  }
};

const onOtpComplete = (otp) => {
  form.value.otp = otp;
};

const handleResetPassword = async () => {
  loading.value = true;
  error.value = '';
  try {
    await axios.post('/api/reset-password', form.value);
    alert('Password reset successfully! You can now login.');
    router.push('/login');
  } catch (err) {
    error.value = err.response?.data?.message || 'Verification failed';
  } finally {
    loading.value = false;
  }
};
</script>
