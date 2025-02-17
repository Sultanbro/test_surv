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
            <template #activator="{ props }">
              <div class="d-flex align-center gap-1 payers__edit">
                <div v-bind="props">
                  {{ getStatusText(payer.status) }}
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
                  @click="() => selectStatus(status, payer)"
                  :class="{ payers__option: true, selected: status.type === payer.status }"
                >
                  {{ status.title }}
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>

          <div v-else>
            <div>{{ getStatusText(payer.status) }}</div>
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
import { ref, reactive, computed } from 'vue'
import { formatDateTime } from '@/utils/formatDateTime'
import { sortedByDatePayersUsers } from '@/utils/sortedPayersUsers'
import { updatePayerStatus } from '@/api/payers/updatePayer'
import { updatePayerStatusBitrix } from '@/api/payers/updatePayerStatusBitrix'
import { usePayersUsersStore } from '@/stores/usePayersUsersStore'
import { type TPayersUsers } from '@/types/payersUsers'
import EditIcon from '../../assets/icons/EditIcon.vue'

type TStatuses = {
  id: number
  title: string
  type: string
}

const payersUsersStore = usePayersUsersStore()

const isEdit = ref<boolean>(false)

const statuses: TStatuses[] = reactive([
  { id: 1, title: 'Оплатил', type: 'success' },
  { id: 2, title: 'В ожидании', type: 'pending' },
])

payersUsersStore.getUsers()

const sortedPayersUsers = computed(() => {
  return sortedByDatePayersUsers(payersUsersStore.users)
})

const toggleEditTable = () => {
  isEdit.value = !isEdit.value
}

const titleButton = computed(() => {
  return isEdit.value ? 'Сохранить' : 'Редактировать'
})

const selectStatus = (status: TStatuses, payer: TPayersUsers) => {
  payer.status = status.type
  updatePayerStatus(status.type, payer.id).then(async (res: TPayersUsers) => {
    await updatePayerStatusBitrix(res.lead_id)
    payer.status = status.type
  })
}

const getStatusText = (status: string) => {
  return status === 'pending' ? 'В ожидании' : 'Оплатил'
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
