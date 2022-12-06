<script lang="ts" setup>
import { ref } from 'vue'
import { useUserPermissionsStore } from '@/stores/user-permissions'
import UserPermissionsEdit from '@/views/user-interface/form-layouts/UserPermissionsEdit.vue'

const userPermissionsStore = useUserPermissionsStore()

const editDialog = ref(false)
const errors = ref({})
const loadingAdd = ref(false)
const loadingRemove = ref(false)

async function onSubmit(user: AddUserPermissionsRequest){
  errors.value = {}
  loadingAdd.value = true
  const data = await userPermissionsStore.addPermissions(user)
  loadingAdd.value = false
  if(data.errors){
    errors.value = data.errors
  }
  else{
    editDialog.value = false
  }
}
async function onRemove(id: number){
  if(!confirm('Вы действительно хотите удалить пользователя?')) return
  errors.value = {}
  loadingRemove.value = true
  const data = await userPermissionsStore.removePermissions(id)
  loadingRemove.value = false
  if(data.errors){
    // errors.value = data.errors
    alert(data.errors)
  }
}
</script>

<template>
  <VBtn
    @click="(editDialog = true)"
    class="mb-4"
  >Дать доступ</VBtn>
  <VTable
    v-if="userPermissionsStore.permissions.length"
    height="250"
    fixed-header
  >
    <thead>
      <tr>
        <th>id</th>
        <th>ФИО</th>
        <th>
          email
        </th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="item in userPermissionsStore.permissions"
        :key="item.id"
      >
        <td>{{ item.id }}</td>
        <td>{{ item.name }} {{ item.last_name }}</td>
        <td>
          {{ item.email }}
        </td>
        <td>
          <VBtn
            icon="mdi-account-remove-outline"
            variant="text"
            @click="onRemove(item.id)"
          />
        </td>
      </tr>
    </tbody>
  </VTable>
  <div
    v-else
    class="text-center"
  >
    <VProgressCircular
      indeterminate
      color="primary"
    />
  </div>
  <VDialog v-model="editDialog">
    <UserPermissionsEdit
      :errors="errors"
      @submit="onSubmit"
    />
  </VDialog>
</template>
