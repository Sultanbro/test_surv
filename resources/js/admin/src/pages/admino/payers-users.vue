<template>
  <v-btn
    @click="toggleEditTable"
    class="deep-purple-accent-1 mb-4"
  >
    {{ titleButton }}
  </v-btn>
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
          <v-menu
            location="bottom"
            v-if="isEdit"
          >
            <template v-slot:activator="{ props }">
              <div class="d-flex align-center gap-1 payers__edit">
                <div
                  v-bind="props"
                  v-if="payer.status === 'pending'"
                >
                  В ожидании
                </div>
                <div
                  v-bind="props"
                  v-else
                >
                  Оплатил
                </div>
                <EditIcon v-bind="props" />
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

          <div v-else>
            <div v-if="payer.status === 'pending'">В ожидании</div>
            <div v-else>Оплатил</div>
          </div>
        </td>
        <td>{{ payer.amount }}</td>
        <td>
          <a
            :href="payer.url"
            target="_blank"
          >
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
import { fetchPayersUsers } from '@/api/payers/getPayers'
import { updatePayerStatus } from '@/api/payers/updatePayer'
import EditIcon from '../../assets/icons/EditIcon.vue'

type TStatuses = {
  id: number
  title: string
}

const isEdit = ref<boolean>(false)

const payersUsers = ref<TPayersUsers[]>([])

const statuses: TStatuses[] = reactive([
  { id: 1, title: 'Оплачено' },
  { id: 2, title: 'В ожидании' },
])

const sortedPayersUsers = computed(() => {
  return sortedByDatePayersUsers(payersUsers.value)
})

fetchPayersUsers().then(res => {
  return (payersUsers.value = res.data)
})

const toggleEditTable = () => {
  isEdit.value = !isEdit.value
}

const titleButton = computed(() => {
  return isEdit.value ? 'Сохранить' : 'Редактировать'
})
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
