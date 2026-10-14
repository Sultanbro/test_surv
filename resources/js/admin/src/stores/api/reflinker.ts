import axios from 'axios'
import { onError } from './error'

export async function fetchRefLinks(){
  try {
    const { data } = await axios.get<Array<RefLink>>('/hr/ref-links')
    return data
  }
  catch (error) {
    return onError(error)
  }
}

export async function saveRefLink(link: RefLink){
  try {
    const { data } = await axios.post<number>('/hr/ref-links/save', {
      ...link,
      method: 'save'
    })
    return data
  }
  catch (error) {
    return onError(error)
  }
}

export async function deleteRefLink(link: RefLink){
  try {
    const { data } = await axios.post<number>('/hr/ref-links/save', {
      ...link,
      method: 'delete'
    })
    return data
  }
  catch (error) {
   return onError(error)
  }
}

export type RefLink = {
  id: number
  name: string
  info: string
  site: string
}
