<script lang="ts" setup>
import { ref } from 'vue'
import { useUserRolesStore } from '@/stores/user-roles'
import { VueCropper }  from 'vue-cropperjs'
import FilePicker from '@core/components/FilePicker.vue'
import { rejects } from 'assert';

type Props = {
	errors: {
    [key: string]: Array<string>
  }
  user?: Manager
}
const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'submit', data: AddUserPermissionsRequest): void
}>()

const userRolesStore = useUserRolesStore()
userRolesStore.fetchRoles()

const id = ref(0)
const name = ref('')
const last_name = ref('')
const email = ref('')
const password = ref('')
const role_id = ref(0)
const image = ref('')
const phone = ref('')
const form = ref(true)

watchEffect(() => {
	id.value = props.user?.id || 0
	name.value = props.user?.name || ''
	last_name.value = props.user?.last_name || ''
	email.value = props.user?.email || ''
	phone.value = props.user?.phone || ''
	password.value = ''
	role_id.value = props.user?.role_id || 0
  image.value = props.user?.img_url || ''
})

const roleOptions = computed(() => {
  return [
    {
      value: 0,
      title: '',
    },
    ...userRolesStore.roles.map(role => {
      return {
        value: role.id,
        title: role.name,
      }
    })
  ]
})

function onSubmit(){
  emit('submit', {
    id: id.value,
    name: name.value,
    last_name: last_name.value,
    email: email.value,
    password: password.value,
    password_confirmation: password.value,
    role_id: role_id.value,
    phone: phone.value,
    image: srcImage.value && srcImage.value[0] ? srcImage.value[0] : null,
  })
}

// function getBase64(file: File) {
//   return new Promise((resolve, reject) => {
//     const reader = new FileReader()
//     reader.readAsDataURL(file)
//     reader.onload = function () {
//       resolve(reader.result)
//     }
//     reader.onerror = function (error) {
//       reject(error)
//     }
//   })
// }

const cropper = ref<InstanceType<typeof VueCropper> | null>(null)
const srcImage = ref<FileList | null>(null)
const newImage = ref<Blob | null>(null)
const baseImage = ref('')
// watch(srcImage, value => {
//   if(!value) return
// })
watchEffect(() => {
  if(!srcImage.value || !srcImage.value[0]) return
  if (cropper.value && cropper.value.replace) {
    cropper.value.replace(srcImage.value[0])
  }
  else {
    // not mounted yet, or the element was unmounted (e.g. by v-if)
  }
})
// watchEffect(() => {
//   if(!srcImage.value || !srcImage.value[0]) return
//   getBase64(srcImage.value[0]).then(img => {
//     baseImage.value = img
//   })
// })
function resetCropper(){
  srcImage.value = null
}
function cropImage(){
  cropper.value.getCroppedCanvas().toBlob(blob => {
    newImage.value = blob
  }, 'image/jpeg', 0.8)
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
        <FilePicker
          v-if="true"
          v-model="srcImage"
          class="text-center mb-4 d-block"
          :error-messages="errors.image || []"
        >
          <VAvatar
            size="48px"
            color="info"
          >
            <VImg
              v-if="image"
              alt="Avatar"
              :src="image"
            />
            <template v-else>{{ (name[0] || '') + (last_name[0] || '') }}</template>
            <!-- Тут можно сделать вычисление цвета фона из первых букв имени или email [(item.name.charCodeAt(0) % 16).toString(16)] и поставить в css автоконтраст текста -->
          </VAvatar>
        </FilePicker>
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
          label="Телефон"
          v-model="phone"
          class="mb-4"
          :error-messages="errors.phone || []"
        />
        <VTextField
          label="Пароль"
          v-model="password"
          class="mb-4"
          :error-messages="errors.password || []"
        />
        <VSelect
          label="Роль"
          v-model="role_id"
          :items="roleOptions"
          class="mb-4"
          :error-messages="errors.role || []"
        />
        <VBtn
          block
          color="success"
          size="large"
          type="submit"
          variant="elevated"
        >Добавить доступ</VBtn>
      </VForm>

      <VDialog
        :model-value="false && !!srcImage && !!srcImage[0]"
        max-width="500"
        persistent
      >
        <VCard class="pt-6 pb-3">
          <VCardText class="pb-3">
            <VueCropper
              ref="cropper"
              class="image-container"
              :aspect-ratio="1 / 1"
              :guides="true"
              :background="false"
              :view-mode="3"
              drag-mode="move"
              alt="Image not available"
            />
          </VCardText>
          <VCardActions class="py-0 mx-10">
            <VBtn
              @click="resetCropper"
              text color="red"
            >
              Cancel
            </VBtn>
            <VSpacer/>
            <VBtn
              @click="cropImage"
              text color="blue"
            >
              Crop
            </VBtn>
          </VCardActions>
        </VCard>
      </VDialog>
    </VCardText>
  </VCard>
</template>
