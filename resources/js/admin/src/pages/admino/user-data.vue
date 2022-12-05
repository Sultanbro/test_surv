<script lang="ts" setup>
import UserData from '@/views/user-interface/tables/UserData.vue'
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
const perPageItems = [
  { value: 10, title: 10 },
  { value: 20, title: 20 },
  { value: 50, title: 50 },
  { value: 100, title: 100 },
]
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
  <VRow>
    <VCol>
      Всего: {{ userDataStore.total }}
    </VCol>
    <VCol cols="8">
      <VPagination
        v-if="pages > 1"
        v-model="page"
        :length="pages"
      />
    </VCol>
    <VCol>
      <VContainer>
        <VRow
          align="center"
          no-gutters
        >
          <VCol>На странице:</VCol>
          <VCol>
            <!-- Vuetify bug, Readonly not for promitives -->
            <VSelect
              v-model="perPage"
              :items="perPageItems"
            />
          </VCol>
        </VRow>
      </VContainer>
    </VCol>
  </VRow>
</template>
