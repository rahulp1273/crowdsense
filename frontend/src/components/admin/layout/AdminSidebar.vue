<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <div 
      v-if="isOpen" 
      class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[60] lg:hidden"
      @click="$emit('close')"
    ></div>

    <!-- Drawer -->
    <aside 
      :class="[
        isOpen ? 'translate-x-0' : '-translate-x-full',
        'lg:translate-x-0'
      ]"
      class="fixed top-0 left-0 w-72 h-screen bg-white dark:bg-gray-900 shadow-2xl z-[70] transition-transform duration-300 ease-in-out flex flex-col border-r border-gray-100 dark:border-gray-800 lg:z-40"
    >
      <div class="h-20 flex items-center px-6 justify-between border-b border-gray-50 dark:border-gray-800">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-sm">C</div>
          <div class="ml-3 flex flex-col">
            <span class="font-black text-lg tracking-tighter text-gray-800 dark:text-white uppercase leading-none">CrowdSense</span>
            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mt-1">Admin Panel</span>
          </div>
        </div>
        <button @click="$emit('close')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors lg:hidden">
          <span class="text-xl">✕</span>
        </button>
      </div>

      <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto">
        <template v-for="item in navItems" :key="item.path">
          <router-link 
            v-if="!item.permission || authStore.hasPermission(item.permission)"
            :to="item.path" 
            @click="$emit('close')"
            class="group flex items-center p-4 rounded-2xl transition-all duration-200" 
            :class="[
              $route.path === item.path || $route.path.startsWith(item.path + '/') 
                ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20' 
                : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'
            ]"
          >
            <span class="text-xl">{{ item.icon }}</span>
            <span class="ml-4 font-bold text-sm">{{ item.name }}</span>
          </router-link>
        </template>
      </nav>

      <div class="p-4 border-t border-gray-50 dark:border-gray-800">
        <button 
          @click="logout" 
          class="w-full flex items-center justify-center p-4 bg-red-50 text-red-600 dark:bg-red-900/10 dark:text-red-400 rounded-2xl hover:bg-red-100 dark:hover:bg-red-900/20 transition-all font-bold text-sm cursor-pointer"
        >
          Logout
        </button>
      </div>
    </aside>
  </Teleport>
</template>

<script setup>
import { useAuthStore } from '../../../stores/auth';
import { useRouter } from 'vue-router';

const props = defineProps({
  isOpen: Boolean
});

defineEmits(['close']);

const authStore = useAuthStore();
const router = useRouter();

const navItems = [
  { name: 'Dashboard', path: '/admin/dashboard', icon: '📊' },
  { name: 'Places', path: '/admin/places', icon: '📍' },
  { name: 'Inquiries', path: '/admin/inquiries', icon: '📩' },
  { name: 'Admins', path: '/admin/admins', icon: '🛡️', permission: 'manage_admins' },
  { name: 'Analytics', path: '/admin/analytics', icon: '📈' }
];

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
