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
const name = ref('')
const last_name = ref('')
const email = ref('')
const lead = ref('')
const city = ref('')
const country = ref('')

watchEffect(() => {
  emit('update:modelValue', {
    '>balance': balanceFrom.value,
    '<balance': balanceTo.value,
    '>login_at': loginFrom.value,
    '<login_at': loginTo.value,
    '>birthday': birthdayFrom.value,
    '<birthday': birthdayTo.value,
    'name': name.value,
    'last_name': last_name.value,
    'email': email.value,
    'lead': lead.value,
    'city': city.value,
    'country': country.value,
  })
})
</script>

<template>
  <VForm @submit.prevent="() => {}">
    <VContainer>
      <VRow>
        <VCol
          cols="12"
          md="6"
          lg="4"
        >
          <VTextField
            v-model="name"
            label="Имя"
            class="mb-2"
          />
          <VTextField
            v-model="last_name"
            label="Фамилия"
            class="mb-2"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="4"
        >
          <VTextField
            v-model="email"
            label="email"
            class="mb-2"
          />
          <VTextField
            v-model="lead"
            label="Лид"
            class="mb-2"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="4"
        >
          <VTextField
            v-model="country"
            label="Страна"
            class="mb-2"
          />
          <VTextField
            v-model="city"
            label="Город"
            class="mb-2"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="4"
        >
          <VTextField
            v-model="balanceFrom"
            label="Баланс от"
            class="mb-2"
          />
          <VTextField
            v-model="balanceTo"
            label="Баланс до"
            class="mb-2"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="4"
        >
          <VDateTime
            v-model="loginFrom"
            label="Врямя входа от"
            class="mb-2"
          />
          <VDateTime
            v-model="loginTo"
            label="Врямя входа до"
            class="mb-2"
          />
        </VCol>
        <VCol
          cols="12"
          md="6"
          lg="4"
        >
          <VDateTime
            v-model="birthdayFrom"
            label="День рождения от"
            class="mb-2"
          />
          <VDateTime
            v-model="birthdayTo"
            label="День рождения до"
            class="mb-2"
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
