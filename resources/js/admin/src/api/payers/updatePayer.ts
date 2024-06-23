import axios from 'axios'

export const updatePayerStatus = async (status: string, id: number) => {
  console.log(status, id)
  await axios.post(`https://jobtron.org/api/v1/invoices/${id}/status`, {
    status,
  })
}
