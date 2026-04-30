<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-[400px] space-y-8">
      <!-- Logo/Brand -->
      <div class="text-center">
        <div class="w-12 h-12 bg-indigo-600 rounded-2xl mx-auto flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-indigo-200 dark:shadow-none mb-4">C</div>
        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">
          {{ !showOtpForm ? 'Welcome back' : 'Verify Account' }}
        </h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">
          {{ !showOtpForm ? 'Enter your credentials to access your account' : 'Enter the code sent to your email' }}
        </p>
      </div>

      <div class="bg-white dark:bg-gray-900 p-8 rounded-[2rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-100 dark:border-gray-800">
        <!-- Login Form -->
        <form v-if="!showOtpForm" @submit.prevent="handleLogin" class="space-y-6">
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
          
          <div class="space-y-2">
            <div class="flex justify-between items-center px-1">
              <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400">Password</label>
              <router-link to="/forgot-password" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">
                Forgot password?
              </router-link>
            </div>
            <input 
              v-model="form.password" 
              type="password" 
              required 
              placeholder="••••••••"
              class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none"
            >
          </div>
          
          <div v-if="error" class="bg-red-50 dark:bg-red-900/20 p-3 rounded-xl">
            <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest text-center">{{ error }}</p>
          </div>
          
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-black uppercase tracking-widest text-xs py-4 rounded-xl hover:bg-indigo-600 dark:hover:bg-indigo-50 transition-all active:scale-95 disabled:opacity-50 cursor-pointer"
          >
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>
        </form>

        <!-- OTP Verification Form (for unverified accounts) -->
        <form v-else @submit.prevent="handleVerifyOtp" class="space-y-6">
          <div class="text-center space-y-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Your account needs verification. <br>Check your email <span class="text-gray-900 dark:text-white font-bold">{{ form.email }}</span></p>
            
            <div class="flex justify-center py-4">
              <OtpInput :length="6" v-model="otpCode" @complete="handleVerifyOtp" />
            </div>
          </div>
          
          <div v-if="error" class="bg-red-50 dark:bg-red-900/20 p-3 rounded-xl text-center">
            <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest">{{ error }}</p>
          </div>
          
          <button 
            type="submit" 
            :disabled="loading || otpCode.length !== 6"
            class="w-full bg-indigo-600 text-white font-black uppercase tracking-widest text-xs py-4 rounded-xl hover:bg-indigo-700 transition-all active:scale-95 disabled:opacity-50 cursor-pointer"
          >
            {{ loading ? 'Verifying...' : 'Verify OTP' }}
          </button>

          <button 
            type="button"
            @click="handleResendOtp"
            :disabled="loading"
            class="w-full text-[10px] font-bold text-indigo-600 uppercase tracking-widest hover:text-indigo-700 transition-colors mb-2"
          >
            {{ loading ? 'Sending...' : 'Resend OTP' }}
          </button>

          <button 
            type="button"
            @click="showOtpForm = false"
            class="w-full text-[10px] font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors"
          >
            ← Back to login
          </button>
        </form>
      </div>
      
      <p v-if="!showOtpForm" class="text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
        New here? <router-link to="/register" class="text-indigo-600 hover:text-indigo-700">Create an account</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import OtpInput from '../../components/common/OtpInput.vue';

const authStore = useAuthStore();
const router = useRouter();

const form = reactive({ email: '', password: '' });
const otpCode = ref('');
const showOtpForm = ref(false);
const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
  loading.value = true;
  error.value = '';
  try {
    await authStore.login(form);
    router.push(authStore.isAdmin ? '/admin/dashboard' : '/dashboard');
  } catch (err) {
    if (err.response?.status === 403 && err.response?.data?.unverified) {
      showOtpForm.value = true;
      // We also trigger an OTP resend automatically to be helpful
      try {
        await authStore.resendOtp(form.email);
      } catch (e) {
        console.error('Failed to auto-resend OTP');
      }
    } else {
      error.value = err.response?.data?.message || 'Invalid email or password';
    }
  } finally {
    loading.value = false;
  }
};

const handleResendOtp = async () => {
  loading.value = true;
  error.value = '';
  try {
    await authStore.resendOtp(form.email);
    alert('A new code has been sent to your email.');
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to resend OTP';
  } finally {
    loading.value = false;
  }
};

const handleVerifyOtp = async () => {
  if (otpCode.value.length !== 6) return;
  loading.value = true;
  error.value = '';
  try {
    await authStore.verifyOtp({ email: form.email, otp: otpCode.value });
    router.push(authStore.isAdmin ? '/admin/dashboard' : '/dashboard');
  } catch (err) {
    error.value = err.response?.data?.message || 'Invalid OTP';
  } finally {
    loading.value = false;
  }
};
</script>
