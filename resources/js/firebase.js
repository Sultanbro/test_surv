import axios from 'axios'

// Do not send user identities, request bodies, or errors to external telemetry.
axios.interceptors.response.use(
  response => response,
  error => {
    if (error?.response?.status === 401) location.assign('/')
    return Promise.reject(error)
  }
)
