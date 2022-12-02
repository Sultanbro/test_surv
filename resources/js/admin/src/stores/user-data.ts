import { fetchUserData } from './api'
import type { UserData, UserDataResponse, UserDataRequest } from './api'

// const testUser: UserData = {
//   id: 1,
//   fio: 'test test test',
//   email: 'test@mail.ru',
//   created_at: '29.11.22',
//   login_at: '29.11.22',
//   subdimains: 3,
//   lead: 'https://bp.jobtron.org',
//   balance: '100 000 KZT',
//   birthday: '22.11.22',
//   country: 'Россия',
//   city: 'Владивосток'
// }
export type UserDataKeys = keyof UserData

function compareNumbers(a: number, b: number){
  return a - b
}
function compareStrings(a: string, b: string){
  return a.localeCompare(b)
}
function compareDate(a: string, b: string){
  // пока как строки, позже прикручу moment
  return a.localeCompare(b)
}
function unformateBalance(balance: string){
  return parseFloat(balance.split(' ').join(''))
}
function formatedNumberCompare(a: string, b: string){
  return unformateBalance(a) - unformateBalance(b)
}
type SortFunctions<T> = {
  [Property in keyof T] : Function
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
  city: compareStrings
}

export const useUserDataStore = defineStore('user-data', () => {
  const userData = ref<Array<UserData>>([])
  const total = ref(2)
  const onPage = ref(10)
  const page = ref(1)
  const sort = ref<[UserDataKeys | '', string]>(['',''])
  const sortedData = computed(() => {
    if(!sort.value[0]) return userData.value

    const field: UserDataKeys = sort.value[0]
    if(!(field in sortFunctions)) return userData.value

    return userData.value.sort((a: UserData, b: UserData) => {
      if(sort.value[1] === 'desc'){
        return sortFunctions[field](b[field], a[field])
      }
      return sortFunctions[field](a[field], b[field])
    })
  })
  function setSort(field: UserDataKeys | '', type: string){
    sort.value = [field, type]
  }
  function fetchUsers(): void {
    fetchUserData({
      page: page.value,
      per_page: onPage.value
    }).then(data => {
      if(data !== undefined && 'items' in data){
        userData.value = data.items.data
      }
    })
  }
  return {
    userData,
    sortedData,

    total,
    onPage,
    page,

    fetchUsers,
    sort,
    setSort
  }
})
