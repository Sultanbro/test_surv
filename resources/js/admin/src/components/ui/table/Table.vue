<template>
  <VCard class="faq-card mt-4">
    <VCardTitle class="faq-card-header">
      {{ title }}
      <VSpacer />
      <div class="faq-card-actions">
        <slot name="header-button" />
      </div>
    </VCardTitle>
    <VDivider />
    <VContainer class="faq-card-body">
      <VRow>
        <VCol class="faq-card-list">
          <slot name="col-1" />
        </VCol>
        <VCol
          cols="9"
          clasa="faq-card-content"
        >
          <slot name="col-2" />
        </VCol>
      </VRow>
    </VContainer>
  </VCard>
</template>

<script setup lang="ts">
type TTable = {
  title: string
}

defineProps<TTable>()
</script>

<style lang="scss">
.faq-card {
  display: flex;
  flex-flow: column;
  height: calc(100vh - (48px + var(--v-layout-top) + var(--v-layout-bottom)));
  ul {
    list-style: none;
  }
}

.faq-card-header {
  display: flex;
  align-items: center;
}

.faq-card-body {
  flex: 1;
  display: flex;
  flex-flow: column;
  max-height: calc(100% - 59px);
  padding: 12px;
  > .v-row {
    flex: 1;
    max-height: calc(100% + 24px);
  }
}

.faq-card-list {
  display: flex;
  flex-flow: column;
  max-height: 100%;
  outline: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.faq-list-add {
  padding: 0 15px;
}

.no-questions {
  opacity: 0.7;
  padding: 0 15px;
  margin-bottom: 20px;
}

.faq-card-content {
  display: flex;
  flex-flow: column;
  height: 100%;
}
</style>