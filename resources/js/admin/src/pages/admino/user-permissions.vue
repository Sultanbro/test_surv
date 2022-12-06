<script lang="ts" setup>
import UserData from '@/views/user-interface/tables/UserData.vue'
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

watch(page, value => {
  userDataStore.$patch({
    page: value,
  })
  userDataStore.fetchUsers(filters.value)
})

watch(perPage, value => {
  userDataStore.$patch({
    onPage: value,
  })
  userDataStore.fetchUsers(filters.value)
})

watch(filters, value => {
  userDataStore.fetchUsers(filters.value)
})
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Права пользователей">
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
