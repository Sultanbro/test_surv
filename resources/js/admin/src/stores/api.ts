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

export const fetchUserPermissions = async (req: UserDataRequest) => {
  try {
    const { data, status } = await axios.get<UserDataResponse>('/admin/owners', { params: req })

    return data
  }
  catch (error) {
    onError(error)
  }
}
