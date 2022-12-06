import axios from 'axios'

function onError(error: any) {
  if (axios.isAxiosError(error)) {
    console.log('error message: ', error.message)

    return error.message
  }
  else {
    console.log('unexpected error: ', error)

    return 'An unexpected error occurred'
  }
}

export const fetchUserData = async (req: UserDataRequest) => {
  try {
    const { data, status } = await axios.get<UserDataResponse>('/admin/owners', { params: req })

    return data
  }
  catch (error) {
    onError(error)
  }
}

export const fetchUserPermissions = async (req: UserPermissionsRequest) => {
  try {
    // const { data, status } = await axios.get<UserPermissionsResponse>('/admin/owners', { params: req })
    const data: UserPermissionsResponse = {
      items: {
        data: [
          {
            id: 1,
            name: 'Тестовый Юзер',
            access: false
          },
          {
            id: 2,
            name: 'Тестовый Пользователь',
            access: true
          }
        ],
        total: 2,
        current_page: 1,
        last_page: 1,
      }
    }
    return data
  }
  catch (error) {
    onError(error)
  }
}
