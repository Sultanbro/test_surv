<script setup lang="ts">
// const props = defineProps<{
//   prop: Object,
// }>()
</script>

<template>
<button class="ActionButton">
  <slot/>
</button>
</template>

<style lang="scss">
.ActionButton{
  appearance: none;
}
</style>
