

declare interface UserData {
  id: number
  last_name?: string
  name?: string
  email: string
  created_at: string
  login_at?: string
  birthday?: string
  country?: string
  city?: string
  lead?: string
  balance?: string
  subdimains?: Array<string>
  full_name?: string
}

// declare interface Manager {
//   id: number
//   email: string
//   phone: string
//   name: string
//   last_name: string
// }

declare interface UserManagerPivot {
  owner_id: number
  manager_id: number
  managers: Array<Manager>
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
  },
  manager: Array<UserManagerPivot>
}

declare interface UserPermissions {
  id: number
  last_name: string
  name: string
  email: string
  phone?: string
  role_id?: number
  img_url?: string
  image?: string
  is_admin: number
}

declare interface UserPermissionsRequest {
  per_page?: number
  page?: number
}

declare interface UserPermissionsResponse {
  items?: Array<UserPermissions>
}

declare interface AddUserPermissionsRequest {
  last_name: string
  name: string
  email: string
  password: string
  password_confirmation: string
  role_id?: number
  image: string | null
  phone: string
}

declare interface UserPermissionsResponse {
  items?: Array<UserPermissions>
}

declare interface AddUserPermissionsResponse {
  errors?: {
    [key: string]: Array<string>
  }
}

declare interface FetchManagersResponse {
  message: string
  data: Manager[]
}

declare interface Manager {
  id: number
  email: string
  remember_token?: string
  name: string
  last_name: string
  UF_ADMIN?: number
  position_id?: number
  program_id?: number
  full_time?: number
  user_type?: string
  phone: string
  city?: string
  address?: string
  description?: string
  currency?: string
  timezone?: number
  segment?: number
  working_day_id?: number
  working_time_id?: number
  work_start?: string
  work_end?: string
  birthday?: string
  last_group?: string
  read_corp_book_at?: string
  has_noti?: number
  weekdays?: string
  role_id: number
  is_admin?: number
  created_at?: string
  updated_at?: string
  deleted_at?: string
  groups_all?: number
  working_country?: string
  working_city?: string
  applied_at?: string
  img_url?: string
  phone_1?: string
  phone_2?: string
  phone_3?: string
  phone_4?: string
  headphones_sum?: unknown
  notified_at?: string
  active_status: number
  avatar?: string
  cropped_img_url?: string
  pivot?: {
    role_id: number
    model_id: number
    model_type: string
  }
}

declare interface UserRole{
  id: number
  name: string
  guard_name: string
  created_at?: string
  updated_at?: string
}
