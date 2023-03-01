import type Manager from './api.d.ts'
import {
  fetchManagers,
  setManager,
} from './api.ts'

export const useManagersStore = defineStore('managers', () => {
  const managers = ref<Manager[]>([])
  async function _fetchManagers() {
    const { data } = await fetchManagers()
    managers.value = data
  }
  async function _setManager(owner_id: number, manager_id: number) {
    const { data } = await setManager(owner_id, manager_id)
    managers.value = data
  }
  return {
    managers,
    fetchManagers: _fetchManagers,
    setManager: _setManager,
  }
})
