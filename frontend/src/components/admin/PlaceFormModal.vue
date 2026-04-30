<template>
  <Teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 md:p-8 overflow-hidden">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-md" @click="$emit('close')"></div>

      <!-- Modal Content -->
      <div class="relative bg-white dark:bg-gray-900 w-full max-w-2xl max-h-[90vh] flex flex-col rounded-[2.5rem] shadow-2xl border border-white/10 overflow-hidden animate-in fade-in zoom-in duration-300">
        
        <!-- Header -->
        <div class="sticky top-0 z-10 px-8 py-6 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b dark:border-gray-800 flex justify-between items-center">
          <div>
            <h3 class="font-black text-2xl tracking-tighter text-gray-900 dark:text-white">
              {{ form.id ? 'Update Venue' : 'Create New Venue' }}
            </h3>
            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500 mt-1">SaaS Location Intelligence</p>
          </div>
          <button @click="$emit('close')" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:hover:text-white transition-all cursor-pointer">✕</button>
        </div>

        <!-- Scrollable Body -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8 scrollbar-hide">
          
          <!-- Basic & Capacity Section -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Venue Identity</label>
                <input v-model="form.name" required placeholder="Starbucks Reserve" type="text" class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Category</label>
                <select v-model="form.type" class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white appearance-none">
                  <option value="cafe">Cafe / Restaurant</option>
                  <option value="gym">Gym / Fitness</option>
                  <option value="mall">Shopping Mall</option>
                  <option value="park">Public Park</option>
                  <option value="office">Workplace / Office</option>
                  <option value="library">Library / Education</option>
                </select>
              </div>
            </div>

            <div class="space-y-6">
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Max Capacity</label>
                <input v-model.number="form.max_capacity" type="number" class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">GPS Radius (Meters)</label>
                <input v-model.number="form.radius" type="number" class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
              </div>
            </div>
          </div>

          <!-- Location Intelligence Section -->
          <div class="bg-indigo-50/30 dark:bg-indigo-900/10 p-8 rounded-[2.5rem] border border-indigo-100/50 dark:border-indigo-800/30 space-y-6">
            <div class="flex items-center justify-between mb-2">
              <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600">Geospatial Intelligence</h4>
              <span class="text-[9px] font-black px-3 py-1 bg-white dark:bg-gray-800 rounded-full text-indigo-400 border border-indigo-100 dark:border-indigo-800 shadow-sm">AI RESOLUTION READY</span>
            </div>
            
            <!-- Country & Pincode -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Region</label>
                <select v-model="form.country_code" class="w-full px-5 py-4 bg-white dark:bg-gray-800 border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white appearance-none">
                  <option value="JP">Japan 🇯🇵</option>
                  <option value="IN">India 🇮🇳</option>
                  <option value="US">United States 🇺🇸</option>
                </select>
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Postal Code</label>
                <div class="flex items-center space-x-2">
                  <input v-model="form.pincode" @keyup.enter="$emit('resolve')" placeholder="e.g. 950-0076" type="text" class="flex-1 min-w-0 px-5 py-4 bg-white dark:bg-gray-800 border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
                  <button type="button" @click="$emit('resolve')" :disabled="isLookingUp" class="h-[56px] px-6 bg-indigo-600 text-white rounded-2xl font-black uppercase text-[11px] tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 dark:shadow-none disabled:opacity-50 flex items-center justify-center min-w-[100px] shrink-0 cursor-pointer">
                    <span v-if="!isLookingUp">Resolve</span>
                    <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  </button>
                </div>
              </div>
            </div>

            <!-- City & State -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">City / Township</label>
                <input v-model="form.city" placeholder="Auto-filled" type="text" class="w-full px-5 py-4 bg-white/50 dark:bg-gray-800/50 border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">State / Prefecture</label>
                <input v-model="form.state" placeholder="Auto-filled" type="text" class="w-full px-5 py-4 bg-white/50 dark:bg-gray-800/50 border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
              </div>
            </div>

            <!-- Detailed Address -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Area / Street</label>
                <input v-model="form.street" placeholder="MG Road, Sector 5" type="text" class="w-full px-5 py-4 bg-white dark:bg-gray-800 border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Building / Mansion / Floor</label>
                <input v-model="form.mansion" placeholder="Tower B, 4th Floor" type="text" class="w-full px-5 py-4 bg-white dark:bg-gray-800 border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-bold dark:text-white">
              </div>
            </div>

            <!-- GPS -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1 text-center">LATITUDE</label>
                <input v-model="form.lat" type="text" class="w-full px-5 py-4 bg-white/40 dark:bg-gray-900/40 border-none rounded-2xl shadow-inner focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-mono text-[11px] text-center dark:text-indigo-300">
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1 text-center">LONGITUDE</label>
                <input v-model="form.lng" type="text" class="w-full px-5 py-4 bg-white/40 dark:bg-gray-900/40 border-none rounded-2xl shadow-inner focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all font-mono text-[11px] text-center dark:text-indigo-300">
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 z-10 px-8 py-6 bg-gray-50 dark:bg-gray-800 border-t dark:border-gray-700 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
          <button type="button" @click="$emit('close')" class="flex-1 px-8 py-4 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl font-black uppercase tracking-widest text-[10px] text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all cursor-pointer">
            Dismiss
          </button>
          <button type="button" @click="$emit('save')" :disabled="isSaving" class="flex-[2] bg-gray-900 dark:bg-indigo-600 text-white font-black uppercase tracking-widest text-[10px] py-4 rounded-2xl hover:bg-indigo-600 dark:hover:bg-indigo-500 transition-all shadow-xl active:scale-95 disabled:opacity-50 cursor-pointer">
            {{ isSaving ? 'Committing Changes...' : (form.id ? 'Push Update' : 'Initialize Venue') }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  isOpen: Boolean,
  form: Object,
  isLookingUp: Boolean,
  isSaving: Boolean
});

defineEmits(['close', 'save', 'resolve']);
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
