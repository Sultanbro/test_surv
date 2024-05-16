<script lang="ts" setup>
import { useUserDataStore } from '@/stores/user-data'
import { useManagersStore } from '@/stores/managers'
import type { UserDataKeys } from '@/stores/user-data'
import Action from '@core/components/Action.vue'

const emit = defineEmits<{
  (e: 'manager', owner: {id: number, manager: {id: number}}): void
  (e: 'scrollEnd'): void
}>()

const userDataStore = useUserDataStore()
const managersStore = useManagersStore()

function sortSymbol(field: string) {
  if (field === userDataStore.sortField)
    return (userDataStore.sortOrder === 'desc' ? '▼' : '▲')

  return ''
}
function setSort(field: string) {
  console.log('setSort', field, userDataStore.sortField, userDataStore.sortOrder)
  if (userDataStore.sortField === field && userDataStore.sortOrder === 'asc') {
    return userDataStore.setSort(field, 'desc')
  }
  userDataStore.setSort(field, 'asc')
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
  return `${formatDate(dateZ)} ${o(date.getHours())}:${o(date.getMinutes())}`
}

// const scrollTD = ref<Element | null>(null)
// function scrollObserverCallback(entries: IntersectionObserverEntry[]){
//   entries.forEach(entry => {
//     if(entry.target === scrollTD.value && entry.isIntersecting){
//       emit('scrollEnd')
//     }
//   })
// }
// const scrollObserver = new IntersectionObserver(scrollObserverCallback)
// watchEffect(() => {
//   if (scrollTD.value) {
//     scrollObserver.observe(scrollTD.value)
//   }
//   else {
//     // not mounted yet, or the element was unmounted (e.g. by v-if)
//   }
// })

function getManagerName(userId: number){
  const managerId = userDataStore.userManagers[userId]
  const manager = managersStore.managers.find(manager => manager.id === managerId)
  return manager ? `${manager.name} ${manager.last_name}` : 'Нет'
}

function defineEmits<T>() {
  throw new Error('Function not implemented.')
}
</script>

<template>
  <VContainer class="UserData">
    <template v-if="userDataStore.userData.length">
      <VTable fixed-header>
        <thead>
          <tr>
            <th
              v-if="userDataStore.showCols.id"
              @click="setSort('id')"
            >
              id{{ sortSymbol('id') }}
            </th>
            <th
              v-if="userDataStore.showCols.fio"
              @click="setSort('last_name')"
            >
              ФИО{{ sortSymbol('last_name') }}
            </th>
            <th
              v-if="userDataStore.showCols.email"
              class="text-center"
              @click="setSort('email')"
            >
              email{{ sortSymbol('email') }}
            </th>
            <th
              v-if="userDataStore.showCols.phone"
              class="text-center"
              @click="setSort('phone')"
            >
              Телефон{{ sortSymbol('phone') }}
            </th>
            <th
              v-if="userDataStore.showCols.created_at"
              class="text-center"
              @click="setSort('created_at')"
            >
              Создан{{ sortSymbol('created_at') }}
            </th>
            <th
              v-if="userDataStore.showCols.login_at"
              class="text-center"
              @click="setSort('login_at')"
            >
              Вход{{ sortSymbol('login_at') }}
            </th>
            <th
              v-if="userDataStore.showCols.tenants"
              class="text-center"
              @click="setSort('subdimains')"
            >
              Домены{{ sortSymbol('subdimains') }}
            </th>
            <th
              v-if="userDataStore.showCols.currency"
              class="text-center"
            >
              Валюта
            </th>
            <th
              v-if="userDataStore.showCols.lead"
              class="text-center"
              @click="setSort('lead')"
            >
              Лид{{ sortSymbol('lead') }}
            </th>
            <th
              v-if="userDataStore.showCols.balance"
              class="text-center"
              @click="setSort('balance')"
            >
              Баланс{{ sortSymbol('balance') }}
            </th>
            <th
              v-if="userDataStore.showCols.birthday"
              class="text-center"
              @click="setSort('birthday')"
            >
              День рождения{{ sortSymbol('birthday') }}
            </th>
            <th
              v-if="userDataStore.showCols.country"
              class="text-center"
              @click="setSort('country')"
            >
              Страна{{ sortSymbol('country') }}
            </th>
            <th
              v-if="userDataStore.showCols.city"
              class="text-center"
              @click="setSort('city')"
            >
              Город{{ sortSymbol('city') }}
            </th>
            <th
              v-if="userDataStore.showCols.manager"
              class="text-center"
            >
              Менеджер
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in userDataStore.userData"
            :key="item.id"
          >
            <td v-if="userDataStore.showCols.id">{{ item.id }}</td>
            <td v-if="userDataStore.showCols.fio">{{ item.full_name }}</td>
            <td
              v-if="userDataStore.showCols.email"
              class="text-center"
            >
              <a :href="`mailto:${item.email}`">{{ item.email }}</a>
            </td>
            <td
              v-if="userDataStore.showCols.phone"
              class="text-center"
            >
              {{ item.phone }}
            </td>
            <td
              v-if="userDataStore.showCols.created_at"
              class="text-center whsnw"
            >
              {{ formatDateTime(item.created_at) }}
            </td>
            <td
              v-if="userDataStore.showCols.login_at"
              class="text-center whsnw"
            >
              {{ item.login_at ? formatDateTime(item.login_at) : '' }}
            </td>
            <td
              v-if="userDataStore.showCols.tenants"
              class="text-center"
            >
              <template v-if="item.subdomains">
                <VChip
                  v-for="sub in item.subdomains"
                  :key="sub.id"
                  class="ma-2"
                  size="small"
                >{{ sub.id }}</VChip>
              </template>
            </td>
            <td
              v-if="userDataStore.showCols.currency"
              class="text-center"
            >
              <template v-if="item.subdomains">
                <VChip
                  v-for="sub in item.subdomains"
                  :key="sub.id"
                  class="ma-2"
                  size="small"
                >{{ sub.currency }}</VChip>
              </template>
            </td>
            <td
              v-if="userDataStore.showCols.lead"
              class="text-center"
            >
              <a v-if="item.lead" :href="item.lead" target="_blank">
                {{ item.lead.split('/').reverse()[0] }}
              </a>
            </td>
            <td
              v-if="userDataStore.showCols.balance"
              class="text-center"
            >
              {{ item.balance === 'KZT' ? '0' : '' }} {{ item.balance }}
            </td>
            <td
              v-if="userDataStore.showCols.birthday"
              class="text-center"
            >
              {{ item.birthday ? formatDate(item.birthday) : '' }}
            </td>
            <td
              v-if="userDataStore.showCols.country"
              class="text-center"
            >
              {{ item.country }}
            </td>
            <td
              v-if="userDataStore.showCols.city"
              class="text-center"
            >
              {{ item.city }}
            </td>
            <td
              v-if="userDataStore.showCols.manager"
              class="text-center"
            >
              <Action
                v-if="item.manager"
                @click="$emit('manager', item)"
              >
                {{ item.manager?.last_name || '' }} {{ item.manager?.name || '' }}
              </Action>
              <VBtn
                v-else
                icon="mdi-account-question"
                density="compact"
                @click="$emit('manager', item)"
              />
            </td>
          </tr>
        </tbody>
      </VTable>
      <VRow>
        <VCol
          cols="4"
          class="d-flex aic"
        >
          {{ userDataStore.userData.length }} / {{ userDataStore.total }}
        </VCol>
        <VCol
          cols="4"
          class="d-flex aic justify-center"
        >
          <!-- <VBtn @click="emit('scrollEnd')">
            Загрузить еще
          </VBtn> -->
          <VPagination
            v-model="userDataStore.page"
            :length="userDataStore.lastPage"
            :total-visible="5"
          />
        </VCol>
        <VCol
          cols="4"
          class="d-flex aic justify-end"
        >
          <div class="UserData-perPage">
            <VSelect
              v-model="userDataStore.onPage"
              :items="[5, 10, 20, 50, 100]"
              density="compact"
            />
          </div>
        </VCol>
      </VRow>
    </template>
    <div
      v-else-if="userDataStore.isLoading"
      class="text-center"
    >
      <VProgressCircular
        indeterminate
        color="primary"
      />
    </div>
    <div v-else>
      Пользователи не найдены
    </div>
  </VContainer>
</template>

<style lang="scss">
.UserData{
  margin-top: -16px;
  .v-table__wrapper{
    --j-other-height: calc(var(--v-layout-top) + var(--v-layout-bottom) + 48px + 140px);
    // height: calc(100vh - var(--j-other-height));
    // overflow-y: auto;
  }
  &-perPage{
    width: 100px;
  }
}
</style>
