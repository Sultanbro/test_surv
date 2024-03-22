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
  'query': '',
})
const filtersMenu = ref(false)

const onPage = computed(() => userDataStore.onPage)
const sort = computed(() => ({
  field: userDataStore.sortField,
  order: userDataStore.sortOrder,
}))
const page = computed(() => userDataStore.page)

watch(onPage, () => {
  userDataStore.fetchUsers(filters.value, {clear: true})
})
watch(sort, () => {
  userDataStore.fetchUsers(filters.value, {clear: true})
})
watch(page, () => {
  userDataStore.fetchUsers(filters.value, {clear: true})
})

function onSubmitFilters(){
  filtersMenu.value = false
  userDataStore.fetchUsers(filters.value, {clear: true})
}

function nextPage(){
  if(userDataStore.lastPage > userDataStore.page) userDataStore.nextPage(filters.value)
}

// watch(filters, value => {
//   userDataStore.fetchUsers(filters.value)
// })

const blankManagerOption = {title: 'Выберите менеджера', value: 0}
const managerOwner = ref<null | {id: number, manager: {id: number}}>(null)
const managerOverlay = computed({
  get: () => !!managerOwner.value,
  set: (v) => (managerOwner.value = null),
})
const manager = ref(0)
const managerOptions = computed(() => {
  return [blankManagerOption, ...managersStore.managers.map(manager => {
    return {
      title: `${manager.name} ${manager.last_name}`,
      value: manager.id,
    }
  })]
})
watch(managerOwner, value => {
  manager.value = managerOwner.value?.manager?.id || 0
})
function saveManager(){
  if(!managerOwner.value?.id) return
  managersStore.setManager(managerOwner.value?.id , manager.value)
}

const colname: {[key: string]: string} = {
  id: 'ID',
  fio: 'ФИО',
  email: 'Email',
  phone: 'Телефон',
  created_at: 'Создан',
  login_at: 'Вход',
  tenants: 'Домены',
  currency: 'Валюта',
  lead: 'Лид',
  balance: 'Баланс',
  birthday: 'День рождения',
  country: 'Страна',
  city: 'Город',
  manager: 'Менеджер',
}
const showColsMenu = ref(false)
const showCols = computed(() => userDataStore.showCols)
watch(showCols, () => userDataStore.saveShowCols(), {deep: true})
</script>

<template>
  <VRow class="por">
    <VCol
      cols="6"
      class="userdata-filters"
    >
      <v-menu
        v-model="filtersMenu"
        :close-on-content-click="false"
        location="bottom"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            color="indigo"
            v-bind="props"
          >
            Фильтры
          </v-btn>
        </template>

        <VCard
          max-width="600"
          title="Фильтры"
        >
          <UserDataFilters
            v-model="filters"
            @submit="onSubmitFilters"
          />
        </VCard>
      </v-menu>
      <v-menu
        v-model="showColsMenu"
        :close-on-content-click="false"
        location="bottom"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            color="indigo"
            icon="mdi-eye"
            density="comfortable"
            v-bind="props"
          />
        </template>

        <VCard title="Показывать колонки">
          <VContainer>
            <VRow>
              <VCol
                v-for="val, col in userDataStore.showCols"
                :key="col"
                cols="4"
                class="py-0"
              >

                <VCheckbox
                  v-model="userDataStore.showCols[col]"
                  :label="colname[col] || `${col}`"
                />
              </VCol>
              <!-- <VCol cols="12">
                <v-btn
                  color="indigo"
                >
                  Сохранить
                </v-btn>
              </VCol> -->
            </VRow>
          </VContainer>
        </VCard>
      </v-menu>
    </VCol>
    <VCol cols="12">
      <VCard title="Данные пользователей">
        <UserData
          @manager="managerOwner = $event"
          @scrollEnd="nextPage"
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
      />
      <template #footer>
        <VBtn @click="saveManager">Save</VBtn>
      </template>
    </SideBar>
  </VOverlay>
</template>

<style lang="scss">
.userdata-filters{
  display: flex;
  align-items: center;
  gap: 1rem;
  position: absolute;
  z-index: 999;
  top: -75px;
}
</style>
