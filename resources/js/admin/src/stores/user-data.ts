import { fetchUserData } from './api'
import type { UserData, UserDataRequest, UserDataResponse } from './api.d'

export type UserDataKeys = keyof UserData

function compareNumbers(a: number, b: number) {
  return parseFloat('' + a) - parseFloat('' + b)
}
function compareStrings(a: string, b: string) {
  if(!a) return -1
  if(!b) return 1
  return a.localeCompare(b)
}
function compareDate(a: string, b: string) {
  if(!a) return -1
  if(!b) return 1
  // пока как строки, позже прикручу moment
  return a.localeCompare(b)
}
function unformateBalance(balance: string) {
  return balance ? parseFloat(balance.split(' ').join('')) : 0
}
function formatedNumberCompare(a: string, b: string) {
  return unformateBalance(a) - unformateBalance(b)
}
type SortFunctions<T> = {
  [Property in keyof T]: Function
}
const sortFunctions: SortFunctions<UserData> = {
  id: compareNumbers,
  full_name: compareStrings,
  email: compareStrings,
  created_at: compareDate,
  login_at: compareDate,
  subdimains: compareNumbers,
  lead: compareStrings,
  balance: formatedNumberCompare,
  birthday: compareDate,
  country: compareStrings,
  city: compareStrings,
}

export const useUserDataStore = defineStore('user-data', () => {
  const userData = ref<Array<UserData>>([])
  const userManagers = ref<{[key: number]: number}>({})
  const total = ref(2)
  const onPage = ref(10)
  const lastPage = ref(99999)
  const page = ref(1)
  const sort = ref<[UserDataKeys | '', string]>(['', ''])
  const sortedData = computed(() => {
    if (!sort.value[0])
      return userData.value

    const field: UserDataKeys = sort.value[0]
    if (!(field in sortFunctions))
      return userData.value

    return userData.value.sort((a: UserData, b: UserData) => {
      if (sort.value[1] === 'desc')
        return sortFunctions[field](b[field], a[field])

      return sortFunctions[field](a[field], b[field])
    })
  })
  function setSort(field: UserDataKeys | '', type: string) {
    sort.value = [field, type]
  }
  function fetchUsers(filters: UserDataRequest): void {
    page.value = 1
    const options = Object.entries(filters).reduce((opt: {[key: string]: unknown}, [key, value]) => {
      if (value !== '') opt[key] = value
      return opt
    }, {
      page: page.value,
      per_page: onPage.value,
    })
    fetchUserData(options).then(data => {
      if (data !== undefined && 'items' in data) {
        lastPage.value = data.items.last_page || 1
        userData.value = data.items.data
        data.manager.forEach(pivot => {
          userManagers.value[pivot.owner_id] = pivot.manager_id
        });
      }
    })
  }
  function nextPage(filters: UserDataRequest): void {
    const options = Object.entries(filters).reduce((opt: {[key: string]: unknown}, [key, value]) => {
      if (value !== '') opt[key] = value
      return opt
    }, {
      page: page.value,
      per_page: onPage.value,
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
    sortedData,
    userManagers,

    total,
    onPage,
    page,
    lastPage,

    fetchUsers,
    nextPage,
    sort,
    setSort,
  }
})
