import type Manager from './api.d.ts'
import { fetchManagers } from './api.ts'

export const useManagersStore = defineStore('managers', () => {
  const managers = ref<Manager[]>([])
  async function _fetchManagers() {
    const { data } = await fetchManagers()
    managers.value = data
  }
  return {
    managers,
    fetchManagers: _fetchManagers,
  }
})
