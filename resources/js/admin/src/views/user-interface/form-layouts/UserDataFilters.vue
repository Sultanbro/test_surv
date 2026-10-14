<script lang="ts" setup>
import VDateTime from '@core/components/VDateTime.vue'

import type { UserDataRequest } from '@/stores/api.d'

interface Props {
  modelValue: UserDataRequest
}
const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:modelValue', data: UserDataRequest): void
  (e: 'submit'): void
}>()

const balanceFrom = ref<string | number>('')
const balanceTo = ref<string | number>('')
const loginFrom = ref('')
const loginTo = ref('')
const birthdayFrom = ref('')
const birthdayTo = ref('')
const query = ref('')

watchEffect(() => {
  emit('update:modelValue', {
    '>balance': balanceFrom.value,
    '<balance': balanceTo.value,
    '>login_at': loginFrom.value,
    '<login_at': loginTo.value,
    '>birthday': birthdayFrom.value,
    '<birthday': birthdayTo.value,
    'query': query.value,
  })
})
</script>

<template>
  <VForm @submit.prevent="() => {}">
    <VContainer>
      <VRow>
        <VCol
          cols="12"
          class="py-0"
        >
          <VTextField
            v-model="query"
            label="Поиск"
            class="mb-2"
            density="compact"
          />
        </VCol>

        <VCol
          cols="12"
          md="6"
          lg="6"
          class="py-0"
        >
          <VTextField
            v-model="balanceFrom"
            label="Баланс от"
            class="mb-2"
            density="compact"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="6"
          class="py-0"
        >
          <VTextField
            v-model="balanceTo"
            label="Баланс до"
            class="mb-2"
            density="compact"
          />
        </VCol>

        <VCol
          cols="12"
          md="6"
          lg="6"
          class="py-0"
        >
          <VDateTime
            v-model="loginFrom"
            label="Врямя входа от"
            class="mb-2"
            density="compact"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="6"
          class="py-0"
        >
          <VDateTime
            v-model="loginTo"
            label="Врямя входа до"
            class="mb-2"
            density="compact"
          />
        </VCol>

        <VCol
          cols="12"
          md="6"
          lg="6"
          class="py-0"
        >
          <VDateTime
            v-model="birthdayFrom"
            label="День рождения от"
            class="mb-2"
            density="compact"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="6"
          class="py-0"
        >
          <VDateTime
            v-model="birthdayFrom"
            label="День рождения от"
            class="mb-2"
            density="compact"
          />
        </VCol>
      </VRow>
      <VRow>
        <VCol>
          <VBtn
            @click="emit('submit')"
          >
            Применить
          </VBtn>
        </VCol>
      </VRow>
    </VContainer>
  </VForm>
</template>
