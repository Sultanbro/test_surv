<script lang="ts" setup>
interface Props {
  page: number
  pages: number
  perPage: number
  perPageItems: number[]
  total: number
}
const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:page'): void
  (e: 'update:perPage'): void
}>()
</script>
<template>
  <VRow class="aic">
    <VCol cols="12" lg="8" order-lg="2">
      <VPagination
        v-if="pages > 1"
        :modelValue="page"
        :length="pages"
        @update:modelValue="emit('update:page', $event)"
      />
    </VCol>
    <VCol cols="12" md="6" lg="2" order-lg="1">
      Всего: {{ total }}
    </VCol>
    <VCol cols="12" md="6" lg="2" order-lg="3">
      <VContainer>
        <VRow
          class="aic"
          no-gutters
        >
          <VCol>На странице:</VCol>
          <VCol>
            <!-- Vuetify bug, Readonly not for promitives -->
            <VSelect
              :modelValue="perPage"
              :items="perPageItems"
              @update:modelValue="emit('update:perPage', $event)"
              type="number"
            />
          </VCol>
        </VRow>
      </VContainer>
    </VCol>
  </VRow>
</template>
<style></style>
