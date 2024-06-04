import axios from 'axios'

export const fetchPayersUsers = async () => {
  return await axios.get('https://jobtron.org/api/v1/invoices')
}
