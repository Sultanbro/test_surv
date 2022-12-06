import { fetchUserPermissions } from './api'

export const useUserPermissionsStore = defineStore('user-permissions', () => {
  const permissions = ref<Array<UserPermissions>>([])
  const total = ref(2)
  const onPage = ref(10)
  const page = ref(1)
  
  function fetchPermissions(filters: UserPermissionsRequest): void {
    const options = Object.entries(filters).reduce((opt, [key, value]) => {
      if (value !== '')
        opt[key] = value
      return opt
    }, {
      page: page.value,
      per_page: onPage.value,
    })
    fetchUserPermissions(options).then(data => {
      if (data !== undefined && 'items' in data)
      permissions.value = data.items.data
    })
  }

  return {
    permissions,

    total,
    onPage,
    page,

    fetchPermissions,
  }
})
