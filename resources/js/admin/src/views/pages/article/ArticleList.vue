<template>
  <v-card
    class="mx-auto pa-2"
    max-width="300"
  >
    <v-list>
      <v-list-item
        v-for="(item) in items"
        :key="item.id"
        :value="item"
        color="primary"
        @click="selectItem(item)"
      >
        <!-- <template v-slot:prepend>
          <v-icon :icon="item.icon"></v-icon>
        </template> -->

        <v-list-item-title v-text="item.title"></v-list-item-title>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
export default {
  props: {
    items: Array
  },
  data: () => ({
    selectedItem: null,  // This will now hold the ID of the currently selected item
  }),
  methods: {
    selectItem(item) {
        this.selectedItem = item.id;  // Update selectedItem when an item is clicked
        this.$emit('selectItem', item);
    }
  },
}
</script>
