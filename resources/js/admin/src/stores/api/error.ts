import axios from 'axios'

export function onError(error: AxiosError | unknown): ErrorList {
  if (axios.isAxiosError(error)) {
    console.error('error message: ', error.message, error.response?.data)

    const errorData: ErrorList = error.response ? error.response.data as ErrorList : {
      errors: {
        system: ['An unexpected error occurred']
      }
    }

    return errorData.errors ? errorData : {
      errors: {
        message: errorData.message ? [errorData.message] : ['Неизвестная ошибка']
      }
    }
  }
  else {
    console.error('unexpected error: ', error)

    return {
      errors: {
        system: ['An unexpected error occurred']
      }
    }
  }
}

export interface AxiosError {
  response?: {data: ErrorList}
}
export interface ErrorList {
  errors: {[key: string]: Array<string>}
  message?: string
}
