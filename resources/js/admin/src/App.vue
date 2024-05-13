<script setup lang="ts"></script>

<template>
  <VApp>
    <VLayout class="layout-wrapper layout-nav-type-vertical">
      <RouterView />
    </VLayout>
  </VApp>
</template>

<style>
.aic {
  align-items: center;
}
.scrollable {
  overflow: auto;
}
.vam {
  vertical-align: middle;
}
.whsnw {
  white-space: nowrap;
}
.por {
  position: relative;
}
.layout-nav-type-vertical .layout-vertical-nav .nav-header.nav-header {
  margin: 0;
}
</style>
