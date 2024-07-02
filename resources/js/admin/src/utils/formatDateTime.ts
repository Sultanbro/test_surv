function o(n: number) {
  if (n < 10) return '0' + n
  return '' + n
}

export function formatDate(dateZ: string) {
  const date = new Date(dateZ)
  return `${o(date.getDate())}.${o(date.getMonth() + 1)}.${date.getFullYear()}`
}

export function formatDateTime(dateZ: string) {
  const date = new Date(dateZ)
  return `${formatDate(dateZ)} ${o(date.getHours())}:${o(date.getMinutes())}`
}