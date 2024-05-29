<template>
  <VTable>
    <thead>
      <tr>
        <th>id</th>
        <th>Создан</th>
        <th>Имя пользователя</th>
        <th>Номер телефона</th>
        <th>Статус</th>
        <th>Сумма</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="payerUser in sortedPayersUsers"
        :key="payerUser.id"
      >
        <td>{{ payerUser.id }}</td>
        <td>{{ formatDateTime(payerUser.created_at) }}</td>
        <td>{{ payerUser.payer_name }}</td>
        <td>{{ payerUser.payer_phone }}</td>
        <td>{{ payerUser.status }}</td>
        <td>{{ payerUser.amount }}</td>
      </tr>
    </tbody>
  </VTable>
</template>

<script setup lang="ts">
import { formatDateTime } from '@/utils/formatDateTime'
import { type TPayersUsers } from '@/types/payersUsers'
import { sortedByDatePayersUsers } from '@/utils/sortedPayersUsers'
import axios from 'axios'

const payersUsers = ref<TPayersUsers[]>([])

const PAERS_USERS = 'https://jobtron.org/api/v1/invoices'

const fetchPaersUsers = async () => {
  payersUsers.value = (await axios.get(PAERS_USERS)).data
  console.log(payersUsers.value)
}

fetchPaersUsers()

const sortedPayersUsers = computed(() => sortedByDatePayersUsers(payersUsers.value))
</script>
