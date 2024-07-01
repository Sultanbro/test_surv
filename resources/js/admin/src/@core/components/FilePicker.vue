<script setup lang="ts">

const props = defineProps<{
  errorMessages: Array<string> | null
}>()
const emit = defineEmits<{
  (e: 'change', files: FileList): void
}>()

const className = computed(() => {
  return {
    'v-input--error': props.errorMessages && props.errorMessages.length
  }
})

function onChange(event){
  emit('change', event.target.files)
}
</script>

<template>
<label
  class="FilePicker"
  :class="className"
>
  <slot />
  <input
    type="file"
    accept=".jpg,.jpeg,.gif,.png"
    class="FilePicker-input"
    @change="onChange"
  >
  <div
    v-if="errorMessages && errorMessages.length"
    class="v-input__details"
  >
    <div class="v-messages">
      <div
        v-for="msg in errorMessages"
        class="v-messages__message"
      >{{ msg }}</div>
    </div>
  </div>
</label>
</template>

<style lang="scss">
.FilePicker{
  overflow: hidden;
  position: relative;
  &-input{
    position: absolute;
    z-index: 5;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.001;
  }
}
</style>
