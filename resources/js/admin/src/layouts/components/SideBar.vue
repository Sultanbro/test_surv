<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  title?: String,
  width?: string,
}>()

const sidebarWidth = computed(() => {
  return props.width || '50vw'
})
</script>

<template>
<aside class="Sidebar">
  <header class="Sidebar-header">
    <slot name="header">
      <h2 class="Sidebar-title">{{ title }}</h2>
    </slot>
  </header>
  <section class="Sidebar-body">
    <slot />
  </section>
  <footer class="Sidebar-footer">
    <slot name="footer"/>
  </footer>
</aside>
</template>

<style lang="scss">
  .Sidebar{
    width: v-bind(sidebarWidth);
    height: 100vh;
    background: rgb(var(--v-theme-surface));
    color: rgba(var(--v-theme-on-surface),var(--v-medium-emphasis-opacity));
    &-header,
    &-footer{
      padding: 1rem;
      &:empty{
        display: none;
      }
    }
    &-body{
      padding: 1rem;
      overflow-y: auto;
    }
  }
</style>
