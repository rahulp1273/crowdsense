<template>
  <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 border border-gray-100 dark:border-gray-800">
    <form @submit.prevent="$emit('submit')" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 ml-1">Full Name</label>
          <input 
            v-model="form.name" 
            :disabled="!isEditing"
            type="text" 
            class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none disabled:opacity-60"
            placeholder="Your Name"
          >
        </div>
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 ml-1">Email Address</label>
          <input 
            v-model="form.email" 
            :disabled="!isEditing"
            type="email" 
            class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none disabled:opacity-60"
            placeholder="email@example.com"
          >
        </div>
      </div>

      <div v-if="isEditing" class="pt-4 border-t dark:border-gray-800 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 ml-1">New Password</label>
            <input 
              v-model="form.password" 
              type="password" 
              class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none"
              placeholder="Leave blank to keep same"
            >
          </div>
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 ml-1">Confirm Password</label>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium dark:text-white outline-none"
              placeholder="Confirm new password"
            >
          </div>
        </div>
        
        <div v-if="requiresOtp" class="bg-amber-50 dark:bg-amber-900/10 p-3 rounded-xl flex items-center">
          <span class="text-amber-500 mr-2">⚠️</span>
          <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
            Email or Password changes will require OTP verification
          </p>
        </div>
      </div>

      <div v-if="isEditing" class="flex justify-start pt-4">
        <button 
          type="submit" 
          :disabled="loading"
          class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-bold uppercase tracking-widest text-[10px] shadow-lg hover:bg-indigo-700 transition-all active:scale-95 disabled:opacity-50 flex items-center"
        >
          <span v-if="loading" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
          Save Changes
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
defineProps({
  form: Object,
  isEditing: Boolean,
  loading: Boolean,
  requiresOtp: Boolean
});

defineEmits(['submit']);
</script>
