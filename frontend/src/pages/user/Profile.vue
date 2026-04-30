<template>
  <div class="max-w-4xl mx-auto space-y-6 pb-20 px-2">
    <!-- Header with Buttons -->
    <div class="flex items-center justify-between py-2">
      <div class="flex items-center space-x-4">
        <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Profile Settings</h2>
        <button 
          v-if="!isEditing"
          @click="isEditing = true"
          class="px-5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm"
        >
          Edit Profile
        </button>
        <button 
          v-else
          @click="cancelEdit"
          class="px-5 py-2 bg-gray-100 dark:bg-gray-700 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-gray-200 transition-all active:scale-95"
        >
          Cancel
        </button>
      </div>

      <button 
        @click="handleLogout"
        class="px-5 py-2 border-2 border-red-50 dark:border-red-900/20 text-red-500 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-50 dark:hover:bg-red-900/20 transition-all active:scale-95"
      >
        Logout
      </button>
    </div>

    <!-- Main Content -->
    <ProfileForm 
      :form="form" 
      :is-editing="isEditing" 
      :loading="loading"
      :requires-otp="requiresOtp"
      @submit="handleSubmit"
    />

    <!-- OTP Modal -->
    <ProfileOtpModal 
      :is-open="showOtpModal"
      :loading="loading"
      :otp-complete="!!form.otp"
      @close="showOtpModal = false"
      @complete="onOtpComplete"
      @verify="verifyAndSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import profileService from '../../services/user/profileService';
import ProfileForm from '../../components/user/profile/ProfileForm.vue';
import ProfileOtpModal from '../../components/user/profile/ProfileOtpModal.vue';

const authStore = useAuthStore();
const router = useRouter();
const loading = ref(false);
const isEditing = ref(false);
const showOtpModal = ref(false);

const form = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  password: '',
  password_confirmation: '',
  otp: ''
});

const requiresOtp = computed(() => {
  return form.value.email !== authStore.user?.email || !!form.value.password;
});

const formatDate = (dateString) => {
  if (!dateString) return '...';
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric'
  });
};

const cancelEdit = () => {
  isEditing.value = false;
  form.value.name = authStore.user?.name;
  form.value.email = authStore.user?.email;
  form.value.password = '';
  form.value.password_confirmation = '';
};

const onOtpComplete = (otp) => {
  form.value.otp = otp;
};

const handleSubmit = async () => {
  loading.value = true;
  try {
    const { data } = await profileService.updateProfile(form.value);
    
    if (data.otp_required) {
      showOtpModal.value = true;
    } else {
      authStore.user = data.user;
      isEditing.value = false;
      alert('Profile updated successfully');
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Update failed');
  } finally {
    loading.value = false;
  }
};

const verifyAndSubmit = async () => {
  loading.value = true;
  try {
    const { data } = await profileService.updateProfile(form.value);
    authStore.user = data.user;
    showOtpModal.value = false;
    isEditing.value = false;
    form.value.otp = '';
    form.value.password = '';
    form.value.password_confirmation = '';
    alert('Profile updated successfully');
  } catch (err) {
    alert(err.response?.data?.message || 'Verification failed');
  } finally {
    loading.value = false;
  }
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
