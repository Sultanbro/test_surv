<script lang="ts" setup>
import UserData from '@/views/user-interface/tables/UserData.vue'
import { useUserDataStore } from '@/stores/user-data'
// import type { UserDataFilter } from '@/stores/api'

const userDataStore = useUserDataStore()

const page = ref(1)
const pages = ref(1)
const perPage = ref(10)
const perPageItems = [10, 20, 50, 100]

// const filters = ref<Array<UserDataFilter>>([])
</script>

<template>
  <VRow>
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
        <VRow align="center" no-gutters>
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
