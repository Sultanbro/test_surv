import type Manager from './api.d.ts'
import { useToast } from 'vue-toastification'
import {
  fetchManagers,
  setManager,
} from './api.ts'

const toast = useToast()

export const useManagersStore = defineStore('managers', () => {
  const managers = ref<Manager[]>([])
  async function _fetchManagers() {
    const { data } = await fetchManagers()
    managers.value = data
  }
  async function _setManager(owner_id: number, manager_id: number) {
    const { errors } = await setManager(owner_id, manager_id)
    if(errors?.message) toast.error(errors?.message)
  }
  return {
    managers,
    fetchManagers: _fetchManagers,
    setManager: _setManager,
  }
})
