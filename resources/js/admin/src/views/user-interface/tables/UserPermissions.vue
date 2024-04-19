<script lang="ts" setup>
import { ref } from 'vue'
import { useUserPermissionsStore } from '@/stores/user-permissions'
import UserPermissionsEdit from '@/views/user-interface/form-layouts/UserPermissionsEdit.vue'

const userPermissionsStore = useUserPermissionsStore()

const editDialog = ref(false)
const errors = ref({})
const loadingAdd = ref(false)
const loadingRemove = ref(false)
const user = ref(null)

async function onSubmit(user: AddUserPermissionsRequest){
  errors.value = {}
  loadingAdd.value = true
  let data
  if(user.id){
    data = await userPermissionsStore.editPermissions(user.id, user)
  }
  else{

      data = await userPermissionsStore.addPermissions(user)

  }
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
function onEdit(selected){
  user.value = selected
  editDialog.value = true
}
function onAdd(){
  user.value = null
  editDialog.value = true
}
</script>

<template>
  <VBtn
    @click="onAdd"
    class="mb-4"
  >Дать доступ</VBtn>
  <VTable
    v-if="userPermissionsStore.permissions.length"
    fixed-header
  >
    <thead>
      <tr>
        <th>id</th>
        <th>Фото</th>
        <th>ФИО</th>
        <th>email</th>
        <th>Телефон</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="item in userPermissionsStore.permissions"
        :key="item.id"
      >
        <td>{{ item.id }}</td>
        <td>
          <VAvatar
            size="24px"
            color="info"
          >
            <VImg
              v-if="item.image"
              alt="Avatar"
              :src="item.image"
            />
            <template v-else>{{ (item.name[0] || '') + (item.last_name[0] || '') }}</template>
            <!-- Тут можно сделать вычисление цвета фона из первых букв имени или email [(item.name.charCodeAt(0) % 16).toString(16)] и поставить в css автоконтраст текста -->
          </VAvatar>
        </td>
        <td>{{ item.name }} {{ item.last_name }}</td>
        <td>{{ item.email }}</td>
        <td>{{ item.phone || '' }}</td>
        <td>
          <VBtn
            icon="mdi-account-remove-outline"
            variant="text"
            @click="onRemove(item.id)"
          />
          <VBtn
            icon="mdi-account-edit-outline"
            variant="text"
            @click="onEdit(item)"
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
  <VDialog
    v-model="editDialog"
    max-width="600"
  >
    <UserPermissionsEdit
      :errors="errors"
      :user="user"
      @submit="onSubmit"
    />
  </VDialog>
</template>
