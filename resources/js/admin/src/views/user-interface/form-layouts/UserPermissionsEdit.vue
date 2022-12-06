<script lang="ts" setup>
import { ref } from 'vue'

type Props = {
	errors: {
    [key: string]: Array<string>
  }
}
const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'submit', data: AddUserPermissionsRequest): void
}>()

const name = ref('')
const last_name = ref('')
const email = ref('')
const password = ref('')

const form = ref(true)

function onSubmit(){
  emit('submit', {
    name: name.value,
    last_name: last_name.value,
    email: email.value,
    password: password.value,
    password_confirmation: password.value,
  })
}
</script>

<template>
  <VCard>
    <VCardTitle>
      <span class="text-h5">Добавить доступ</span>
    </VCardTitle>
    <VCardText>
      <VForm
        v-model="form"
        lazy-validation
        @submit.prevent="onSubmit"
      >
        <VTextField
          label="Имя"
          v-model="name"
          class="mb-4"
          :error-messages="errors.name || []"
        />
        <VTextField
          label="Фамилия"
          v-model="last_name"
          class="mb-4"
          :error-messages="errors.last_name || []"
        />
        <VTextField
          label="Email"
          v-model="email"
          class="mb-4"
          :error-messages="errors.email || []"
        />
        <VTextField
          label="Пароль"
          v-model="password"
          class="mb-4"
          :error-messages="errors.password || []"
        />
        <VBtn
          block
          color="success"
          size="large"
          type="submit"
          variant="elevated"
        >Добавить доступ</VBtn>
      </VForm>
    </VCardText>
  </VCard>
</template>