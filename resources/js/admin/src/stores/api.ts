import axios from 'axios'

function onError(error: any){
  if (axios.isAxiosError(error)) {
    console.log('error message: ', error.message);
    return error.message;
  }
  else {
    console.log('unexpected error: ', error);
    return 'An unexpected error occurred';
  }
}

export type UserDataFilter = {
  field: string,
  operation: string,
  value: string
}
export type UserData = {
  id: number,
  last_name?: string,
  name?: string,
  email: string,
  created_at: string,
  login_at: string,
  birthday: string,
  country: string,
  city: string,
  lead: string,
  balance: string,
  subdimains: Array<string>,
  full_name: string
}
export type UserDataResponse = {
  items: {
    current_page: number,
    data: Array<UserData>,
    first_page_url?: string,
    from?: number,
    last_page?: number,
    last_page_url?: string,
    next_page_url?: string,
    path?: string,
    per_page?: number,
    prev_page_url?: number,
    to?: number,
    total?: number
  }

}
export type UserDataRequest = {
  per_page?: number,
  page?: number,
  '>balance'?: number,
  '<balance'?: number
}
export const fetchUserData = async (req: UserDataRequest) => {
  try{
    const {data, status} = await axios.get<UserDataResponse>('/admin/owners', {params: req})
    return data
  }
  catch (error) {
    onError(error)
  }
}
