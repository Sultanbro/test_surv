import { TPayersUsers } from '@/types/payersUsers'
import axios from 'axios'

export const updatePayerStatus = async (status: string, id: number): Promise<TPayersUsers> => {
  return (await axios.post(`https://jobtron.org/api/v1/invoices/${id}/status`, {
    status,
  })).data
}
