<template>
  <div class="flex h-screen bg-gray-50 dark:bg-gray-900 overflow-hidden font-sans">
    <AdminSidebar />
    <div class="flex-1 flex flex-col overflow-hidden">
      <AdminHeader />
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900 p-8">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import AdminSidebar from './AdminSidebar.vue';
import AdminHeader from './AdminHeader.vue';
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
