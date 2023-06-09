import { onError } from './error'
export async function fetchRefLinks(){
  return [
    {
      id: 1,
      name: 'test',
      info: 'test',
      site: 'http://job.bpartners.kz/'
    },
    {
      id: 1,
      name: 'test2',
      info: 'test2 adsdasdd',
      site: 'http://job.bpartners.kz/'
    },
    {
      id: 3,
      name: 'test3',
      info: 'test inhouse',
      site: 'http://job.bpartners.kz/inhouse/'
    }
  ]
}

export async function saveRefLink(link: RefLink){
  if(link.id === 3) return onError('test')
  return link.id
}

export async function deleteRefLink(link: RefLink){
  if(link.id === 3) return onError('test')
  return link.id
}

export type RefLink = {
  id: number
  name: string
  info: string
  site: string
}
