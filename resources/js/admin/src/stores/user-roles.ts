import { fetchUserRoles } from './api'

export const useUserRolesStore = defineStore('user-roles', () => {
  const roles = ref<Array<UserRole>>([])
  const errors = ref<ErrorList | null>(null)
  async function fetchRoles(){
    const response = await fetchUserRoles()
    if(response.errors) errors.value = response.errors
    roles.value = response.data
    console.log('roles.value', roles.value)
  }
  return {
    roles,
    fetchRoles,
  }
})
