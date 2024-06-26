import { defineStore } from "pinia"
import { TPayersUsers } from "@/types/payersUsers"
import { fetchPayersUsers } from "@/api/payers/getPayers"
import { AxiosResponse } from "axios"

export const usePayersUsersStore = defineStore('payersUsersStore', {
  state: () => {
    return {
      users: [] as TPayersUsers[]
    }
  },
  actions: {
    getUsers() {
      fetchPayersUsers().then(res => {
        this.users = res as TPayersUsers[]
      })
    }
  }
})