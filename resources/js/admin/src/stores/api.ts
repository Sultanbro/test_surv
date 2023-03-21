import axios from 'axios'

function onError(error: AxiosError): ErrorList {
  if (axios.isAxiosError(error)) {
    console.log('error message: ', error.message, error.response?.data)

    const errorData = error.response ? error.response.data : {
      errors: {
        system: ['An unexpected error occurred']
      }
    }

    return errorData.errors ? errorData : {
      errors: {
        message: errorData.message || 'Неизвестная ошибка'
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
  const formData = new FormData()
  Object.entries(req).forEach(([key, value]) => formData.append(key, value))
  try {
    const { data, status } = await axios.post<AddUserPermissionsResponse>('/admins/add', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    })
    return data
  }
  catch (error) {
    return onError(error)
  }
}

export const updateUserPermissions = async (id: number, req: AddUserPermissionsRequest) => {
  const formData = new FormData()
  Object.entries(req).forEach(([key, value]) => formData.append(key, value))
  try {
    const { data, status } = await axios.post<AddUserPermissionsResponse>(`/admins/edit/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    })
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

export const setManager = async (owner_id: number, manager_id: number) => {
  try{
    const { data } = await axios.post<{}>('/managers/put-owner', {
      owner_id,
      manager_id,
    })
    return data
  }
  catch (error) {
    return onError(error)
  }
}

export const fetchUserRoles = async (): Promise<{data: Array<UserRole>} | ErrorList> => {
  try{
    const { data } = await axios.get<{data: Array<UserRole>}>('/roles/get')
    return data
  }
  catch (error) {
    return onError(error)
  }
}
