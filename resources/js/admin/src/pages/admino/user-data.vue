<script lang="ts" setup>
import UserData from '@/views/user-interface/tables/UserData.vue'
// import TableFooter from '@/views/user-interface/tables/TableFooter.vue'
import UserDataFilters from '@/views/user-interface/form-layouts/UserDataFilters.vue'
import SideBar from '@/layouts/components/SideBar.vue'
import { useUserDataStore } from '@/stores/user-data'
import { useManagersStore } from '@/stores/managers'


import type { UserDataRequest } from '@/stores/api'

const userDataStore = useUserDataStore()
const managersStore = useManagersStore()

onMounted(() => {
  userDataStore.fetchUsers({})
  managersStore.fetchManagers()
})

// const page = ref(1)
// const pages = ref(1)

// const perPage = ref(10)
// const perPageItems = [10, 20, 50, 100]
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
const filtersMenu = ref(false)

function onSubmitFilters(){
  filtersMenu.value = false
  userDataStore.fetchUsers(filters.value)
}
function onSort(){
  filtersMenu.value = false
  userDataStore.fetchUsers(filters.value)
}

function nextPage(){
  if(userDataStore.lastPage > userDataStore.page) userDataStore.nextPage(filters.value)
}

// watch(filters, value => {
//   userDataStore.fetchUsers(filters.value)
// })

const blankManagerOption = {title: '', value: 0}
const managerUserId = ref(0)
const managerOverlay = computed({
  get: () => !!managerUserId.value,
  set: (v) => (managerUserId.value = 0),
})
const manager = ref(blankManagerOption)
const managerOptions = computed(() => {
  return [blankManagerOption, ...managersStore.managers.map(manager => {
    return {
      title: `${manager.name} ${manager.last_name}`,
      value: manager.id,
    }
  })]
})
watch(managerUserId, value => {
  const managerId = userDataStore.userManagers[value] || 0
  manager.value = managerOptions.value.find(item => item.value === managerId) || blankManagerOption
})
function saveManager(){
  managersStore.setManager(managerUserId.value, manager.value.value)
}
</script>

<template>
  <VRow>
    <VCol cols="12">
      <v-menu
        v-model="filtersMenu"
        :close-on-content-click="false"
        location="end"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            color="indigo"
            v-bind="props"
          >
            Фильтры
          </v-btn>
        </template>

        <VCard title="Фильтры">
          <UserDataFilters
            v-model="filters"
            @submit="onSubmitFilters"
          />
        </VCard>
      </v-menu>
    </VCol>
    <VCol cols="12">
      <VCard title="Данные пользователей">
        <UserData
          @manager="managerUserId = $event"
          @scrollEnd="nextPage"
          @sort="onSort"
        />
      </VCard>
    </VCol>
  </VRow>
  <VOverlay
    v-model="managerOverlay"
    class="justify-end"
  >
    <SideBar>
      <VSelect
        label="Выберите менеджера"
        v-model="manager"
        :items="managerOptions"
        return-object
      />
      <template #footer>
        <VBtn @click="saveManager">Save</VBtn>
      </template>
    </SideBar>
  </VOverlay>
</template>
