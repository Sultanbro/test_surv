<script lang="ts" setup>
import { useUserDataStore } from '@/stores/user-data'
import type { UserDataKeys } from '@/stores/user-data'

const userDataStore = useUserDataStore()

function sortSymbol(field: string) {
  if (field === userDataStore.sort[0])
    return (userDataStore.sort[1] === 'desc' ? '▼' : '▲')

  return ''
}
function setSort(field: UserDataKeys | '') {
  if (userDataStore.sort[0] === field) {
    if (userDataStore.sort[1] === 'desc')
      userDataStore.setSort('', '')

    else
      userDataStore.setSort(field, 'desc')
  }
  userDataStore.setSort(field, '')
}
function o(n: number){
  if(n < 10) return '0' + n
  return '' + n
}
function formatDate(dateZ: string){
  const date = new Date(dateZ)
  return `${o(date.getDate())}.${o(date.getMonth() + 1)}.${date.getFullYear()}`
}
function formatDateTime(dateZ: string){
  const date = new Date(dateZ)
  return `${o(date.getHours())}:${o(date.getMinutes())} ${formatDate(dateZ)}`
}
</script>

<template>
  <VContainer>
    <VTable
      v-if="userDataStore.userData.length"
      height="250"
      fixed-header
    >
      <thead>
        <tr>
          <th @click="setSort('id')">
            id{{ sortSymbol('id') }}
          </th>
          <th @click="setSort('full_name')">
            ФИО{{ sortSymbol('full_name') }}
          </th>
          <th
            class="text-center"
            @click="setSort('email')"
          >
            email{{ sortSymbol('email') }}
          </th>
          <th
            class="text-center"
            @click="setSort('created_at')"
          >
            Создан{{ sortSymbol('created_at') }}
          </th>
          <th
            class="text-center"
            @click="setSort('login_at')"
          >
            Вход{{ sortSymbol('login_at') }}
          </th>
          <th
            class="text-center"
            @click="setSort('subdimains')"
          >
            Домены{{ sortSymbol('subdimains') }}
          </th>
          <th
            class="text-center"
            @click="setSort('lead')"
          >
            Лид{{ sortSymbol('lead') }}
          </th>
          <th
            class="text-center"
            @click="setSort('balance')"
          >
            Баланс{{ sortSymbol('balance') }}
          </th>
          <th
            class="text-center"
            @click="setSort('birthday')"
          >
            День рождения{{ sortSymbol('birthday') }}
          </th>
          <th
            class="text-center"
            @click="setSort('country')"
          >
            Страна{{ sortSymbol('country') }}
          </th>
          <th
            class="text-center"
            @click="setSort('city')"
          >
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
          <td>{{ item.full_name }}</td>
          <td class="text-center">
            <a :href="`mailto:${item.email}`">{{ item.email }}</a>
          </td>
          <td class="text-center">
            {{ formatDateTime(item.created_at) }}
          </td>
          <td class="text-center">
            {{ formatDateTime(item.login_at) }}
          </td>
          <td class="text-center">
            <template v-if="item.subdomains">
              <VChip
                v-for="sub in item.subdomains"
                :key="sub"
                class="ma-2"
                size="small"
              >{{ sub }}</VChip>
            </template>
          </td>
          <td class="text-center">
            <a :href="item.lead" target="_blank">{{ item.lead.split('/').reverse()[0] }}</a>
          </td>
          <td class="text-center">
            {{ item.balance }}
          </td>
          <td class="text-center">
            {{ formatDate(item.birthday) }}
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
    <div
      v-else
      class="text-center"
    >
      <VProgressCircular
        indeterminate
        color="primary"
      />
    </div>
  </VContainer>
</template>
