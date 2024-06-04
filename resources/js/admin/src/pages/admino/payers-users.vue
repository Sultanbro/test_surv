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
        <th>Ссылка</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="payer in sortedPayersUsers"
        :key="payer.id"
      >
        <td>{{ payer.id }}</td>
        <td>{{ formatDateTime(payer.created_at) }}</td>
        <td>{{ payer.payer_name }}</td>
        <td>{{ payer.payer_phone }}</td>
        <td>
          <v-menu location="bottom">
            <template v-slot:activator="{ props }">
              <!-- <v-btn
                color="primary"
                dark
                v-bind="props"
              >
                {{ payerUser.status }}
              </v-btn> -->
              <div
                class="payers__edit"
                v-bind="props"
                v-if="payer.status === 'pending'"
              >
                В ожидании
              </div>
              <div
                class="payers__edit"
                v-bind="props"
                v-else
              >
                Оплатил
              </div>
            </template>

            <v-list>
              <v-list-item
                v-for="status in statuses"
                :key="status.id"
              >
                <v-list-item-title
                  @click="updatePayerStatus(status.title, payer.id)"
                  class="payers__option"
                >
                  {{ status.title }}
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </td>
        <td>{{ payer.amount }}</td>
        <td>
          <a :href="payer.url" target="_blank">
            {{ payer.name }}
          </a>
        </td>
      </tr>
    </tbody>
  </VTable>
</template>

<script setup lang="ts">
import { formatDateTime } from '@/utils/formatDateTime'
import { type TPayersUsers } from '@/types/payersUsers'
import { sortedByDatePayersUsers } from '@/utils/sortedPayersUsers'
import axios from 'axios'

const sortedPayersUsers = computed(() => {
  return sortedByDatePayersUsers(payersUsers.value)
})

type TStatuses = {
  id: number
  title: string
}

const statuses: TStatuses[] = reactive([
  { id: 1, title: 'Оплачено' },
  { id: 2, title: 'В ожидании' },
])

const payersUsers = ref<TPayersUsers[]>([])

const PAERS_USERS = 'https://jobtron.org/api/v1/invoices'

const fetchPayersUsers = async () => {
  payersUsers.value = (await axios.get(PAERS_USERS)).data
}

fetchPayersUsers()

const updatePayerStatus = async (status: string, id: number) => {
  console.log(status, id)
}
</script>

<style scoped lang="scss">
.payers__edit,
.payers__option {
  cursor: pointer;
}

a {
  color: #808080;
  &:hover {
    text-decoration: underline;
  }
}
</style>