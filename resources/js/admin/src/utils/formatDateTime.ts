function o(n: number) {
  if (n < 10) return '0' + n
  return '' + n
}

export function formatDate(dateZ: string | Date) {
  const date = new Date(dateZ)
  return `${o(date.getDate())}.${o(date.getMonth() + 1)}.${date.getFullYear()}`
}

export function formatDateTime(dateZ: string | undefined) {
  const date = dateZ ? new Date(dateZ) : new Date();
  return `${formatDate(date)} ${o(date.getHours())}:${o(date.getMinutes())}`;
}