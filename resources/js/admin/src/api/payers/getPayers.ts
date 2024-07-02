import { TPayersUsers } from '@/types/payersUsers'
import axios, { AxiosResponse } from 'axios'

export const fetchPayersUsers = async (): Promise<TPayersUsers[] | Error> => {
  try {
    return (await axios.get('https://jobtron.org/api/v1/invoices')).data
  } catch (error) {
    return error as Error
  }
}
