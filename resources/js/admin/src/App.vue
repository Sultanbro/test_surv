<script setup lang="ts"></script>

<template>
  <VApp>
    <VLayout class="layout-wrapper layout-nav-type-vertical">
      <RouterView />
    </VLayout>
  </VApp>
</template>

<style>
.aic{
  align-items: center;
}
.scrollable{
  overflow: auto;
}
.whsnw{
  white-space: nowrap;
}
</style>
