import { fetchUserData } from './api'
import type { UserData, UserDataRequest, UserDataResponse } from './api'

export type UserDataKeys = keyof UserData

export const useUserDataStore = defineStore('user-data', () => {
  const userData = ref<Array<UserData>>([])
  const userManagers = ref<{[key: number]: number}>({})
  const total = ref(0)
  const onPage = ref(10)
  const lastPage = ref(1)
  const page = ref(1)
  const sortField = ref('id')
  const sortOrder = ref('asc')
  const isLoading = ref(false)
  function setSort(field: UserDataKeys | '', type: string) {
    sortField.value = field
    sortOrder.value = type
    page.value = 1
  }
  function fetchUsers(filters: UserDataRequest): void {
    page.value = 1
    const options = Object.entries(filters).reduce((opt: {[key: string]: unknown}, [key, value]) => {
      if (value !== '') opt[key] = value
      return opt
    }, {
      page: page.value,
      per_page: onPage.value,
      order_by: sortField.value,
      order_direction: sortOrder.value,
    })

    isLoading.value = true
    fetchUserData(options).then(data => {
      if (data !== undefined && 'items' in data) {
        lastPage.value = data.items.last_page || 1
        userData.value = data.items.data
        total.value = data.items.total || 0
        // userData.value = [...data.items.data, ...data.items.data, ...data.items.data, ...data.items.data, ...data.items.data, ...data.items.data]
        data.manager.forEach(pivot => {
          userManagers.value[pivot.owner_id] = pivot.manager_id
        });
      }
      isLoading.value = false
    })
  }
  function nextPage(filters: UserDataRequest): void {
    const options = Object.entries(filters).reduce((opt: {[key: string]: unknown}, [key, value]) => {
      if (value !== '') opt[key] = value
      return opt
    }, {
      page: ++page.value,
      per_page: onPage.value,
      order_by: sortField.value,
      order_direction: sortOrder.value,
    })
    fetchUserData(options).then(data => {
      if (data !== undefined && 'items' in data){
        lastPage.value = data.items.last_page || 1
        userData.value = [...userData.value, ...data.items.data]
      }
    })
  }

  return {
    userData,
    userManagers,

    total,
    onPage,
    page,
    lastPage,
    sortField,
    sortOrder,
    isLoading,

    fetchUsers,
    nextPage,
    setSort,
  }
})
