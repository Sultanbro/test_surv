interface User {
  id: string
  email: string
}

export const useUserStore = defineStore('user', () => {
  const user = ref<User | {}>({})
  function login(email: string, password: string) {
    user.value = {
      id: '1',
      email: 'test@mail.ru',
    }
  }
  function logout() {

  }

  return { user, login, logout }
})
