<script lang="ts" setup>
import UserData from '@/views/user-interface/tables/UserData.vue'
import TableFooter from '@/views/user-interface/tables/TableFooter.vue'
import UserDataFilters from '@/views/user-interface/form-layouts/UserDataFilters.vue'
import { useUserDataStore } from '@/stores/user-data'

import type { UserDataRequest } from '@/stores/api'

const userDataStore = useUserDataStore()

onMounted(() => {
  userDataStore.fetchUsers({})
})

const page = ref(1)
const pages = ref(1)

const perPage = ref(10)
const perPageItems = [10, 20, 50, 100]
const filters = ref<UserDataRequest>({
  '>balance': '',
  '<balance': '',
  '>login_at': '',
  '<login_at': '',
  '>birthday': '',
  '<birthday': '',
  'name': '',
  'last_name': '',
  'email': '',
  'lead': '',
  'city': '',
  'country': '',
})

function onPage(value: number){
  page.value = value
  userDataStore.$patch({
    page: value,
  })
  userDataStore.fetchUsers(filters.value)
}

function onPerPage(value: number){
  perPage.value = value
  userDataStore.$patch({
    onPage: value,
  })
  userDataStore.fetchUsers(filters.value)
}

watch(filters, value => {
  userDataStore.fetchUsers(filters.value)
})
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Фильтры">
        <UserDataFilters
          v-model="filters"
        />
      </VCard>
    </VCol>
    <VCol cols="12">
      <VCard title="Данные пользователей">
        <UserData />
      </VCard>
    </VCol>
  </VRow>
  <TableFooter
    :page="page"
    :pages="pages"
    :perPage="perPage"
    :perPageItems="perPageItems"
    :total="userDataStore.total"
    @update:page="onPage"
    @update:perPage="onPerPage"
  />
</template>
