<script lang="ts" setup>
import { useUserPermissionsStore } from '@/stores/user-permissions'

const userPermissionsStore = useUserPermissionsStore()

function onAccessChange(id: number){
  userPermissionsStore.$patch(state => {
    state.permissions[id - 1].access = true
  })
}

</script>

<template>
  <VTable
    v-if="userPermissionsStore.permissions.length"
    height="250"
    fixed-header
  >
    <thead>
      <tr>
        <th>id</th>
        <th>ФИО</th>
        <th class="text-center">
          Доступ
        </th>
        <th class="text-center"></th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="item in userPermissionsStore.permissions"
        :key="item.id"
      >
        <td>{{ item.id }}</td>
        <td>{{ item.name }}</td>
        <td class="text-center">
          {{ item.access ? 'Есть' : 'Нет' }}
        </td>
        <td class="text-center">
          <VBtn v-if="!item.access" @click="onAccessChange(item.id)">Дать доступ</VBtn>
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
  <VContainer />
</template>
