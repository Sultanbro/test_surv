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
  fio: string,
  email: string,
  created_at: string,
  login_at: string,
  subdimains: number,
  lead: string,
  balance: string,
  birthday: string,
  country: string,
  city: string
}
export type UserDataResponse = {
  user_data: Array<UserData>,
  total: number
}
export type UserDataRequest = {
  onpage?: number,
  page?: number,
  filters?: Array<UserDataFilter>
}
export const fetchUserData = async (req: UserDataRequest) => {
  try{
    const {data, status} = await axios.get<UserDataResponse>('/someapi', {params: req})
    return data
  }
  catch (error) {
    onError(error)
  }
}
