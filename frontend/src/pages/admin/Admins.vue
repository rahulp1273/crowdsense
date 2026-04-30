<template>
  <div class="p-6 max-w-7xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">Admin Management</h1>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Manage your team and their permissions</p>
      </div>
      <button 
        @click="showAddModal = true"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all active:scale-95 shadow-lg shadow-indigo-200 dark:shadow-none flex items-center gap-2"
      >
        <span>+</span> Invite New Admin
      </button>
    </div>

    <!-- Admins List -->
    <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-xl shadow-gray-100 dark:shadow-none border border-gray-100 dark:border-gray-800 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-50 dark:border-gray-800">
              <th class="px-8 py-6 text-[10px] font-bold uppercase tracking-widest text-gray-400">Administrator</th>
              <th class="px-8 py-6 text-[10px] font-bold uppercase tracking-widest text-gray-400">Role</th>
              <th class="px-8 py-6 text-[10px] font-bold uppercase tracking-widest text-gray-400">Permissions</th>
              <th class="px-8 py-6 text-[10px] font-bold uppercase tracking-widest text-gray-400 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
            <tr v-for="admin in admins" :key="admin.id" class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
              <td class="px-8 py-6">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                    {{ admin.name.charAt(0) }}
                  </div>
                  <div>
                    <div class="font-bold text-gray-900 dark:text-white">{{ admin.name }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ admin.email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-6">
                <span 
                  v-if="admin.is_super_admin" 
                  class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[10px] font-black uppercase tracking-widest rounded-full"
                >
                  Super Admin
                </span>
                <span 
                  v-else 
                  class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-full"
                >
                  Admin
                </span>
              </td>
              <td class="px-8 py-6">
                <div class="flex flex-wrap gap-2 max-w-xs">
                  <span 
                    v-if="admin.is_super_admin"
                    class="text-[10px] font-bold text-gray-400 uppercase"
                  >
                    Full Access
                  </span>
                  <template v-else>
                    <span 
                      v-for="perm in admin.permissions" 
                      :key="perm"
                      class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500 text-[9px] font-bold uppercase rounded-lg"
                    >
                      {{ formatPermission(perm) }}
                    </span>
                    <span v-if="!admin.permissions?.length" class="text-[10px] font-medium text-gray-400">No permissions</span>
                  </template>
                </div>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button 
                    @click="editAdmin(admin)"
                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                  >
                    Edit
                  </button>
                  <button 
                    @click="confirmDelete(admin)"
                    v-if="!admin.is_super_admin"
                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || editingAdmin" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-8 space-y-6">
          <div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">
              {{ editingAdmin ? 'Edit Permissions' : 'Invite Administrator' }}
            </h2>
            <p class="text-sm font-medium text-gray-500">Define access rights for your team member</p>
          </div>

          <form @submit.prevent="saveAdmin" class="space-y-6">
            <div class="space-y-4">
              <div v-if="!editingAdmin" class="space-y-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Full Name</label>
                <input v-model="form.name" required class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
              </div>
              <div v-if="!editingAdmin" class="space-y-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Email Address</label>
                <input v-model="form.email" type="email" required class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
              </div>

              <div class="space-y-4 pt-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Permissions</label>
                <div class="grid grid-cols-1 gap-3">
                  <div v-for="perm in availablePermissions" :key="perm.id" 
                    @click="togglePermission(perm.id)"
                    class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition-all"
                    :class="form.permissions.includes(perm.id) ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-100 dark:border-gray-800 hover:bg-gray-50'"
                  >
                    <div>
                      <div class="text-sm font-bold text-gray-900 dark:text-white">{{ perm.label }}</div>
                      <div class="text-[10px] text-gray-500 font-medium">{{ perm.description }}</div>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                      :class="form.permissions.includes(perm.id) ? 'bg-indigo-600 border-indigo-600' : 'border-gray-200'"
                    >
                      <div v-if="form.permissions.includes(perm.id)" class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex gap-3 pt-4">
              <button type="button" @click="closeModal" class="flex-1 px-6 py-4 rounded-xl font-bold text-xs uppercase tracking-widest text-gray-500 hover:bg-gray-100 transition-colors">Cancel</button>
              <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all active:scale-95">
                {{ editingAdmin ? 'Update Access' : 'Send Invite' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import api from '../../services/api';

const admins = ref([]);
const showAddModal = ref(false);
const editingAdmin = ref(null);
const loading = ref(false);

const availablePermissions = [
  { id: 'manage_places', label: 'Manage Places', description: 'Can create, edit, and delete crowd spots' },
  { id: 'manage_admins', label: 'Invite Admins', description: 'Can add other administrators to the platform' },
  { id: 'read_inquiries', label: 'Read Inquiries', description: 'Can view and mark user feedback as seen' },
  { id: 'delete_data', label: 'Delete Content', description: 'Permission to permanently remove entries' },
];

const form = reactive({
  name: '',
  email: '',
  permissions: []
});

const fetchAdmins = async () => {
  try {
    const { data } = await api.get('/admin/admins');
    admins.value = data;
  } catch (err) {
    console.error('Failed to fetch admins');
  }
};

const formatPermission = (id) => {
  return availablePermissions.find(p => p.id === id)?.label || id;
};

const togglePermission = (id) => {
  const index = form.permissions.indexOf(id);
  if (index === -1) {
    form.permissions.push(id);
  } else {
    form.permissions.splice(index, 1);
  }
};

const editAdmin = (admin) => {
  editingAdmin.value = admin;
  form.name = admin.name;
  form.email = admin.email;
  form.permissions = [...(admin.permissions || [])];
  showAddModal.value = false;
};

const closeModal = () => {
  showAddModal.value = false;
  editingAdmin.value = null;
  form.name = '';
  form.email = '';
  form.permissions = [];
};

const saveAdmin = async () => {
  try {
    if (editingAdmin.value) {
      await api.put(`/admin/admins/${editingAdmin.value.id}`, {
        permissions: form.permissions
      });
    } else {
      const { data } = await api.post('/admin/admins', form);
      alert(`Admin invited! Temporary password: ${data.temporary_password}`);
    }
    await fetchAdmins();
    closeModal();
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to save admin';
    alert(`Error: ${msg}`);
  }
};

const confirmDelete = async (admin) => {
  if (confirm(`Are you sure you want to remove ${admin.name}?`)) {
    try {
      await api.delete(`/admin/admins/${admin.id}`);
      await fetchAdmins();
    } catch (err) {
      const msg = err.response?.data?.message || 'Failed to delete admin';
      alert(`Error: ${msg}`);
    }
  }
};

onMounted(fetchAdmins);
</script>
