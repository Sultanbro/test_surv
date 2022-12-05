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

export interface UserDataFilters {
  '>balance'?: string | number
  '<balance'?: string | number
  '>login_at'?: string
  '<login_at'?: string
  '>birthday'?: string
  '<birthday'?: string
  'name'?: string
  'last_name'?: string
  'email'?: string
  'lead'?: string
  'city'?: string
  'country'?: string
}
export interface UserData {
  id: number
  last_name?: string
  name?: string
  email: string
  created_at: string
  login_at: string
  birthday: string
  country: string
  city: string
  lead: string
  balance: string
  subdimains: Array<string>
  full_name: string
}
export interface UserDataResponse {
  items: {
    current_page: number
    data: Array<UserData>
    first_page_url?: string
    from?: number
    last_page?: number
    last_page_url?: string
    next_page_url?: string
    path?: string
    per_page?: number
    prev_page_url?: number
    to?: number
    total?: number
  }
}
export interface UserDataRequest {
  per_page?: number
  page?: number
  '>balance'?: string | number
  '<balance'?: string | number
  '>login_at'?: string
  '<login_at'?: string
  '>birthday'?: string
  '<birthday'?: string
  'name'?: string
  'last_name'?: string
  'email'?: string
  'lead'?: string
  'city'?: string
  'country'?: string
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
