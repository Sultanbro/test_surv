import axios from 'axios'

function onError(error: any) {
  if (axios.isAxiosError(error)) {
    console.log('error message: ', error.message)

    return error.response ? error.response.data : {
      errors: {
        system: ['An unexpected error occurred']
      }
    }
  }
  else {
    console.log('unexpected error: ', error)

    return {
      errors: {
        system: ['An unexpected error occurred']
      }
    }
  }
}

export const logout = async () => {
  try {
    const { data, status } = await axios.post('/logout', {})
    return data
  }
  catch (error) {
    return onError(error)
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
    const { data, status } = await axios.get<UserPermissionsResponse>('/admins', { params: req })
    return data
  }
  catch (error) {
    return onError(error)
  }
}

export const addUserPermissions = async (req: AddUserPermissionsRequest) => {
  try {
    const { data, status } = await axios.post<AddUserPermissionsResponse>('/admins/add', req)
    return data
  }
  catch (error) {
    return onError(error)
  }
}

export const removeUserPermissions = async (id: number) => {
  try {
    const { data, status } = await axios.delete<UserPermissionsResponse>(`/admins/delete/${id}`)
    return data
  }
  catch (error) {
    return onError(error)
  }
}

export const fetchManagers = async () => {
  try{
    const { data } = await axios.get<FetchManagersResponse>('/managers/get')
    return data
  }
  catch (error) {
    return onError(error)
  }
}
