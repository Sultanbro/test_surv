import { TPayersUsers } from '@/types/payersUsers'

export const sortedByDatePayersUsers = (payersUsers: TPayersUsers[]) => {
  return payersUsers.slice().sort((a, b) => {
    return new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
  })
}
