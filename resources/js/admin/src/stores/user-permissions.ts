import {
  fetchUserPermissions,
  addUserPermissions,
  removeUserPermissions,
  updateUserPermissions,
} from './api'

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
      permissions.value = data.items?.map(user => ({
        ...user,
        is_default: !!user.is_default
      })) || []
      total.value = data.items?.length || 0
    })
  }

  async function addPermissions(req: AddUserPermissionsRequest){
    const data = await addUserPermissions(req)
    if(!data.errors) fetchPermissions({})
    return data
  }
  async function editPermissions(id: number, req: AddUserPermissionsRequest){
    const data = await updateUserPermissions(id, req)
    if(!data.errors) fetchPermissions({})
    return data
  }
  async function removePermissions(id: number){
    const data = await removeUserPermissions(id)
    if(!data.errors) fetchPermissions({})
    return data
  }

  return {
    permissions,

    total,
    onPage,
    page,

    fetchPermissions,
    addPermissions,
    removePermissions,
    editPermissions,
  }
})
