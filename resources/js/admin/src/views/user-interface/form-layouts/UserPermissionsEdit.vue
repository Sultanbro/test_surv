<script lang="ts" setup>
import { ref } from 'vue'
import { useUserRolesStore } from '@/stores/user-roles'
import { VueCropper }  from 'vue-cropperjs'
import Cropper from 'cropperjs'
import FilePicker from '@core/components/FilePicker.vue'
import {useToast} from "vue-toastification";

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
const toast = useToast()
const id = ref(0)
const name = ref('')
const last_name = ref('')
const email = ref('')
const password = ref('')
const role_id = ref(0)
const image = ref('')
const phone = ref('')
const isDefault = ref(true)
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
  isDefault.value = props.user?.is_default || ''
})
console.log(props.user?.is_default)
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
       is_default: isDefault.value,
       image: newImage.value ? newImage.value : null,
     })

}

let cropper = null
const cropperImage = ref<InstanceType<typeof VueCropper> | null>(null)
const srcImage = ref<string | ArrayBuffer | null>(null)
const newImage = ref<Blob | null>(null)

watchEffect(() => {
  if(!srcImage.value) return
  if (cropperImage.value) {
    cropper = new Cropper(cropperImage.value, {
      aspectRatio: 1,
      minCropBoxWidth: 200,
      minCropBoxHeight: 200,
      viewMode: 1,
      dragMode: 'move',
      background: false,
      cropBoxMovable: false,
      cropBoxResizable: false,
    })
  }
  else {
    cropper && cropper.destroy()
  }
})
watch(srcImage, () => {
  if(!cropper) return
  cropper.replace(srcImage.value)
}, {
  flush: 'post'
})
function resetCropper(){
  srcImage.value = null
}
function cropImage(){
  if(!cropper) return
  const canvas = cropper.getCroppedCanvas()
  image.value = canvas.toDataURL('image/jpeg', 0.8)
  canvas.toBlob(blob => {
    newImage.value = blob
    resetCropper()
  }, 'image/jpeg', 0.8)
}
function onSelectImage(files: FileList){
  const image = files ? files[0] : null
  if (!image) return
  if (image.type.indexOf('image/') === -1) return alert('Выберете изображение')
  const reader = new FileReader()
  reader.onload = event => {
    srcImage.value = event.target?.result || null
  }
  reader.readAsDataURL(image)
}
</script>

<template>
  <VCard
    class="UserPermissionsEdit"
  >
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
          @change="onSelectImage"
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
          <div
            v-if="!image"
            class="whsnw"
          >
            Добавить аватарку
          </div>
          <div
            v-if="image"
            class="whsnw"
          >
            Аватарка успешно добавлена
          </div>
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
          type="password"
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
        <VCheckbox
          v-model="isDefault"
          label="Закрепить менеджера по умолчанию"
          class="mb-4"
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
        :model-value="!!srcImage"
        max-width="500"
        persistent
        class="UserPermissionsEdit-cropper"
      >
        <VCard class="pt-6 pb-3">
          <VCardText class="pb-3">
            <div>
              <img
                ref="cropperImage"
                :src="srcImage"
                alt=""
              >
            </div>
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

<style lang="scss">
.UserPermissionsEdit {
  &-cropper{
    .cropper-crop-box {
      &::before,
      &::after{
        content: '';
        position: absolute;
        pointer-events: none;
        border: 1px dashed #eee;
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.75;
      }
      &::before{
        border-radius: 50rem;
      }
      &::after{
        border-radius: 2rem;
      }
    }
  }
  .FilePicker{
    width: fit-content;
    margin: 0 auto;
  }
}

</style>
