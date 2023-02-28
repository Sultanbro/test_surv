<script lang="ts" setup>
import UserData from '@/views/user-interface/tables/UserData.vue'
import TableFooter from '@/views/user-interface/tables/TableFooter.vue'
import UserDataFilters from '@/views/user-interface/form-layouts/UserDataFilters.vue'
import SideBar from '@/layouts/components/SideBar.vue'
import { useUserDataStore } from '@/stores/user-data'
import { useManagersStore } from '@/stores/managers'


import type { UserDataRequest } from '@/stores/api.d'

const userDataStore = useUserDataStore()
const managersStore = useManagersStore()

onMounted(() => {
  userDataStore.fetchUsers({})
  managersStore.fetchManagers()
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

function nextPage(){
  if(userDataStore.lastPage > page.value) userDataStore.nextPage(filters.value)
}

watch(filters, value => {
  userDataStore.fetchUsers(filters.value)
})


const managerUserId = ref(0)
const managerOverlay = computed({
  get: () => !!managerUserId.value,
  set: (v) => (managerUserId.value = 0),
})
const managerId = ref(0)
const managerOptions = computed(() => {
  return [{title: '', value: 0}, ...managersStore.managers.map(manager => {
    return {
      title: `${manager.name} ${manager.last_name}`,
      value: manager.id,
    }
  })]
})
watch(managerUserId, value => {
  managerId.value = userDataStore.userManagers[value] || 0
})
function saveManager(){
  managersStore.setManager(managerUserId.value, managerId.value)
}
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
        <UserData
          @manager="managerUserId = $event"
          @scrollEnd="nextPage"
        />
      </VCard>
    </VCol>
  </VRow>
  <!-- <TableFooter
    :page="page"
    :pages="pages"
    :perPage="perPage"
    :perPageItems="perPageItems"
    :total="userDataStore.total"
    @update:page="onPage"
    @update:perPage="onPerPage"
  /> -->
  <VOverlay
    v-model="managerOverlay"
    class="justify-end"
  >
    <SideBar>
      <VSelect
        label="Выберите менеджера"
        v-model="managerId"
        :items="managerOptions"
      />
      <template #footer>
        <VBtn @click="saveManager">Save</VBtn>
      </template>
    </SideBar>
  </VOverlay>
</template>
