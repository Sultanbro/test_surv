<script lang="ts" setup>
import TableFooter from '@/views/user-interface/tables/TableFooter.vue'
import UserPermissions from '@/views/user-interface/tables/UserPermissions.vue'
import { useUserPermissionsStore } from '@/stores/user-permissions'

import type { UserPermissionsRequest } from '@/stores/api'

const userPermissionsStore = useUserPermissionsStore()

onMounted(() => {
  userPermissionsStore.fetchPermissions({})
})

const page = ref(1)
const pages = ref(1)

const perPage = ref(10)
const perPageItems = [10, 20, 50, 100]

function onPage(value: number){
  page.value = value
  userPermissionsStore.$patch({
    page: value,
  })
  userPermissionsStore.fetchPermissions({})
}

function onPerPage(value: number){
  perPage.value = value
  userPermissionsStore.$patch({
    onPage: value,
  })
  userPermissionsStore.fetchPermissions({})
}
</script>

<template>
  <VCard
    title="Права пользователей"
    class="user-permissions"
  >
    <VContainer>
      <VRow>
        <VCol cols="12">
          <UserPermissions/>
        </VCol>
      </VRow>
    </VContainer>
  </VCard>
  <TableFooter
    v-if="false"
    :page="page"
    :pages="pages"
    :perPage="perPage"
    :perPageItems="perPageItems"
    :total="userPermissionsStore.total"
    @update:page="perPage"
    @update:perPage="onPerPage"
  />
</template>

<style lang="scss">
.user-permissions{
  .v-table__wrapper{
    --j-other-height: calc(var(--v-layout-top) + var(--v-layout-bottom) + 48px + 140px + 18px);
    height: calc(100vh - var(--j-other-height));
    overflow-y: auto;
  }
}
</style>
