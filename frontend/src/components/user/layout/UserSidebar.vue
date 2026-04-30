<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <div 
      v-if="isOpen" 
      class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[60] lg:bg-transparent lg:backdrop-blur-none"
      @click="$emit('close')"
    ></div>

    <!-- Drawer -->
    <aside 
      :class="[isOpen ? 'translate-x-0' : '-translate-x-full']"
      class="fixed top-0 left-0 w-72 h-screen bg-white dark:bg-gray-900 shadow-2xl z-[70] transition-transform duration-300 ease-in-out flex flex-col border-r border-gray-100 dark:border-gray-800"
    >
      <div class="h-20 flex items-center px-6 justify-between border-b border-gray-50 dark:border-gray-800">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-sm">C</div>
          <span class="ml-3 font-black text-lg tracking-tighter text-gray-800 dark:text-white uppercase">CrowdSense</span>
        </div>
        <button @click="$emit('close')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
          <span class="text-xl">✕</span>
        </button>
      </div>

      <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto">
        <router-link 
          v-for="item in navItems" 
          :key="item.path"
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
      </nav>
    </aside>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  isOpen: Boolean
});

defineEmits(['close']);

const navItems = [
  { name: 'Dashboard', path: '/dashboard', icon: '📊' },
  { name: 'Places', path: '/places', icon: '📍' },
  { name: 'Inquiries', path: '/inquiries', icon: '📩' },
  { name: 'Profile', path: '/profile', icon: '👤' }
];
</script>
