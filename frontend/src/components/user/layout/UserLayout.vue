<template>
  <div class="h-screen bg-gray-50 dark:bg-gray-950 overflow-hidden font-sans selection:bg-indigo-100 selection:text-indigo-900 flex flex-col">
    <!-- Sidebar Drawer -->
    <UserSidebar :is-open="isSidebarOpen" @close="isSidebarOpen = false" />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
      <!-- Background Ambient Glow -->
      <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/5 blur-[120px] rounded-full pointer-events-none"></div>
      
      <UserHeader @toggle-sidebar="isSidebarOpen = true" />

      <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 lg:p-6 pb-20">
        <router-view v-slot="{ Component }">
          <transition name="page" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>

    <CheckInErrorModal 
      :place="userStore.activePlaceConflict" 
      @close="userStore.activePlaceConflict = null" 
    />

    <GeofenceErrorModal 
      :is-open="!!userStore.geofenceError" 
      :error-details="userStore.geofenceError"
      @close="userStore.geofenceError = null"
    />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import UserSidebar from './UserSidebar.vue';
import UserHeader from './UserHeader.vue';
import CheckInErrorModal from '../CheckInErrorModal.vue';
import GeofenceErrorModal from '../GeofenceErrorModal.vue';
import { useUserStore } from '../../../stores/userStore';

const userStore = useUserStore();
const isSidebarOpen = ref(false);

onMounted(() => {
  userStore.initialize();
});
</script>

<style>
.page-enter-active,
.page-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.page-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
