<script lang="ts" setup>
import { useUserDataStore } from '@/stores/user-data'
import type { UserDataKeys } from '@/stores/user-data'

const userDataStore = useUserDataStore()

function sortSymbol(field: string) {
  if(field === userDataStore.sort[0]) return (userDataStore.sort[1] === 'desc' ? '▼' : '▲')
  return ''
}
function setSort(field: UserDataKeys | ''){
  if(userDataStore.sort[0] === field){
    if(userDataStore.sort[1] === 'desc') {
      userDataStore.setSort('', '')
    }
    else{
      userDataStore.setSort(field, 'desc')
    }
  }
  userDataStore.setSort(field, '')
}
</script>

<template>
  <VTable
    v-if="userDataStore.userData.length"
    height="250"
    fixed-header
  >
    <thead>
      <tr>
        <th @click="setSort('id')">id{{ sortSymbol('id') }}</th>
        <th @click="setSort('fio')">ФИО{{ sortSymbol('fio') }}</th>
        <th @click="setSort('email')" class="text-center">
          email{{ sortSymbol('email') }}
        </th>
        <th @click="setSort('created_at')" class="text-center">
          Создан{{ sortSymbol('created_at') }}
        </th>
        <th @click="setSort('login_at')" class="text-center">
          Вход{{ sortSymbol('login_at') }}
        </th>
        <th @click="setSort('subdimains')" class="text-center">
          Домены{{ sortSymbol('subdimains') }}
        </th>
        <th @click="setSort('lead')" class="text-center">
          Лид{{ sortSymbol('lead') }}
        </th>
        <th @click="setSort('balance')" class="text-center">
          Баланс{{ sortSymbol('balance') }}
        </th>
        <th @click="setSort('birthday')" class="text-center">
          День рождения{{ sortSymbol('birthday') }}
        </th>
        <th @click="setSort('country')" class="text-center">
          Страна{{ sortSymbol('country') }}
        </th>
        <th @click="setSort('city')" class="text-center">
          Город{{ sortSymbol('city') }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="item in userDataStore.userData"
        :key="item.id"
      >
        <td>{{ item.id }}</td>
        <td>{{ item.fio }}</td>
        <td class="text-center">
          <a :href="`mailto:${item.email}`">{{ item.email }}</a>
        </td>
        <td class="text-center">
          {{ item.created_at }}
        </td>
        <td class="text-center">
          {{ item.login_at }}
        </td>
        <td class="text-center">
          {{ item.subdimains }}
        </td>
        <td class="text-center">
          <a :href="item.lead">{{ item.lead }}</a>
        </td>
        <td class="text-center">
          {{ item.balance }}
        </td>
        <td class="text-center">
          {{ item.birthday }}
        </td>
        <td class="text-center">
          {{ item.country }}
        </td>
        <td class="text-center">
          {{ item.city }}
        </td>
      </tr>
    </tbody>
  </VTable>
  <div v-else class="text-center">
    <VProgressCircular
      indeterminate
      color="primary"
    />
  </div>
  <VContainer>

  </VContainer>
</template>
