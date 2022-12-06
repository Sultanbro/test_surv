
declare interface UserData {
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

declare interface UserDataResponse {
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

declare interface UserDataRequest {
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