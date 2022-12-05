<script lang="ts" setup>
import flatPickr from 'vue-flatpickr-component'

interface Props {
  modelValue: string,
  label: string
}
const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:modelValue'): void
}>()

const dateConfig = {
  altInput: true,
  altFormat: 'd.m.Y H:i',
  dateFormat: 'Z',
}

</script>
<template>
  <label class="v-date-time">
    <flat-pickr
      :modelValue="modelValue"
      :config="dateConfig"
      @update:modelValue="emit('update:modelValue', $event)"
      class="v-date-time-input"
    />
    <div class=" v-date-time-label">
      {{ label }}
    </div>
  </label>
</template>

<style>
.v-date-time{
  display: block;
  padding: 0;
  margin: 0;
  border: none;
  border-radius: 6px;
  position: relative;
}

.v-date-time-input{
  display: block;
  width: 100%;
  padding: 12px 16px;
  border: 1px solid rgba(58, 53, 65, 0.68);
  border-radius: 6px;
  font-size: 16px;
  line-height: 1;
  color: rgba(58, 53, 65, 0.68);
  background: #fff;
  letter-spacing: .009375em;
  transition: 300ms ease all;
}
.v-date-time-input::placeholder{
  opacity: 1 !important;
}
.v-date-time-input:focus{
  outline: none;
}
.v-date-time-input:focus ~ .v-date-time-label,
.v-date-time-input:not(:placeholder-shown) ~ .v-date-time-label{
  left: 1rem;
  top: -0.357rem;
  font-size: 12px;
  line-height: 1;
  color: rgba(58, 53, 65, 0.34);
  transform: none;
  padding: 0 4px;
  opacity: 0.68;
}
.v-date-time-label{
  position: absolute;
  z-index: 1;
  top: 50%;
  left: 50%;
  font-size: 1rem;
  line-height: 1;
  color: rgba(58, 53, 65, 0.68);
  background: #fff;
  transform: translate(-50%, -50%);
  transition: 300ms ease all;
  opacity: 0.68;
  white-space: nowrap;
}
</style>