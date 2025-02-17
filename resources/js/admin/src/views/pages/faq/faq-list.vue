<template>
  <draggable
    class="faq-list"
    :class="{ edit: faqEdit }"
    tag="ul"
    handle=".move-icon"
    :list="questions"
    :group="{ name: 'g1' }"
    item-key="id"
    :data-id="parentId || 0"
    @start="handleDragStart"
    @end="handleDragEnd"
  >
    <template #item="{ element }">
      <li
        class="faq-item"
        :class="{ edit: faqEdit }"
        :data-id="element.id"
      >
        <div
          class="faq-item-content"
          :class="{ edit: faqEdit }"
        >
          <v-icon
            v-if="faqEdit"
            icon="mdi-menu"
            class="move-icon"
          />
          <p
            class="faq-link"
            :class="{ active: activeQuest === element.id }"
            @click="toggleCollapse(element)"
          >
            <span>{{ element.title }}</span>
            <v-icon
              v-if="element.children.length"
              :icon="isOpen(element) || faqEdit ? 'mdi-chevron-down' : 'mdi-chevron-right'"
            />
          </p>
          <v-icon
            v-if="faqEdit"
            icon="mdi-trash"
            color="red"
            class="remove-icon"
            @click="openDialog(element)"
          />
        </div>
        <FaqList
          v-if="faqEdit || (isOpen(element) && element.children.length > 0)"
          :activeQuestion="activeQuestion"
          :questions="element.children"
          :faqEdit="faqEdit"
          :level="level + 1"
          :parentId="element.id"
          @select="onSelect"
          @delete="$emit('delete', $event)"
          @order="$emit('order', $event)"
        />
      </li>
    </template>
  </draggable>

  <v-dialog
    v-model="dialog"
    width="450"
  >
    <v-card>
      <v-card-text>
        Вы уверены, что хотите удалить вопрос? Если в нем есть вложенные вопросы, они так же будут удалены!
      </v-card-text>
      <v-card-actions class="justify-end">
        <v-btn
          color="red-darken-1"
          @click="deleteElement()"
          >Удалить</v-btn
        >
        <v-btn
          color="primary"
          @click="dialog = false"
          >Отмена</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import draggable from 'vuedraggable'

export default {
  name: 'FaqList',
  emits: ['select'],
  components: {
    draggable
  },
  props: {
    questions: {
      type: Array,
      default: () => []
    },
    faqEdit: {
      type: Boolean,
      default: false
    },
    active: {
      type: Object,
      default: null
    },
    level: {
      type: Number,
      default: 0
    },
    parentId: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      dialog: false,
      toDelete: null
    }
  },
  computed: {
    activeQuest(){
      return this.active ? this.active.id : null
    },
  },
  methods: {
    handleDragEnd(event) {
    const item = event.item;
    const to = event.to;
    const itemId = item.getAttribute('data-id') || '';
    const toId = to.getAttribute('data-id') || '';

    this.$emit('order', { itemId, toId });
  },
    toggleCollapse(item) {
      if (item.children?.length) {
        item.isCollapsed = !item.isCollapsed;
      }
      this.onSelect(item);
    },
    onSelect(item) {
      this.$emit('select', item)
    },

    openDialog(element) {
      this.toDelete = element
      this.dialog = true
    },
    deleteElement() {
      this.$emit('delete', this.toDelete)
      this.dialog = false
    },

    isOpen(item){
      return item.id === this.active?.id || item.children.some(this.isOpen)
    },
  }
}
</script>

<style lang="scss">
.faq-list {
  padding: 15px 10px;

  .faq-list {
    padding: 0 0 0 15px;
  }
}
.faq-item {
  transition: 0.1s all ease;

  &.edit {
    padding: 5px 0;
    .faq-list {
      padding: 0 0 5px 15px;
    }
  }

  &.sortable-ghost {
    padding: 5px;
    border: 1px dashed #ddd;
  }
}

.faq-item-content {
  display: flex;
  align-items: center;

  &.edit {
    .faq-link {
      margin-left: 10px;
      margin-right: 10px;
    }
  }

  .remove-icon {
    cursor: pointer;

    &:hover {
      color: red;
    }
  }

  .move-icon {
    cursor: move;

    &:hover {
      color: #9961fd;
    }
  }
}

.faq-link {
  margin: 0;
  min-height: 35px;
  padding: 4px 10px;
  line-height: 1.1;
  border-radius: 6px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: 0.15s all ease;

  &:hover {
    background-color: rgb(var(--v-theme-background));
  }

  &.active {
    color: #9961fd;
  }
}
</style>
