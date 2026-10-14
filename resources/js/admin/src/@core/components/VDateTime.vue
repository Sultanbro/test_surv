<script lang="ts" setup>
import FlatPickr from 'vue-flatpickr-component'

interface Props {
  modelValue: string
  label: string
  density: string
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
  <label
    class="v-date-time"
    :class="[
      `v-date-time--density-${density || 'comfortable'}`,
      {'v-date-time_value': modelValue},
    ]"
  >
    <FlatPickr
      :model-value="modelValue"
      :config="dateConfig"
      class="v-date-time-input"
      @update:modelValue="emit('update:modelValue', $event)"
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
  border: 1px solid rgba(208, 203, 215, 1);
  border-radius: 6px;
  font-size: 16px;
  line-height: 1.35;
  color: rgba(58, 53, 65, 0.68);
  background: #fff;
  letter-spacing: .009375em;
  transition: 300ms ease all;
}
.v-date-time-input::placeholder{
  opacity: 1 !important;
}
.v-date-time-input:hover{
  border-color: rgb(108, 103, 115);
}
.v-date-time-input:focus ~ .v-date-time-label,
.v-date-time_value .v-date-time-input ~ .v-date-time-label{
  left: 1rem;
  top: -0.357rem;
  font-size: 12px;
  line-height: 1;
  color: rgb(158, 153, 165);
  transform: none;
  padding: 0 4px;
}
.v-date-time-input:focus{
  outline: none;
  border-color: rgb(145, 85, 253);
}
.v-date-time-input:focus ~ .v-date-time-label{
  color: rgb(145, 85, 253);
}
.v-date-time-label{
  position: absolute;
  z-index: 1;
  top: 50%;
  left: 1rem;
  font-size: 1rem;
  line-height: 1;
  color: rgba(58, 53, 65, 0.34);
  background: #fff;
  transform: translateY(-50%);
  transition: 300ms ease all;
  white-space: nowrap;
}
.v-date-time--density-compact .v-date-time-input{
  padding: 8px 16px;
}
</style>
