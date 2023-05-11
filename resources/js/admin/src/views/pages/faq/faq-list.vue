<template>
  <draggable
    class="faq-list"
    :class="{'edit': faqEdit}"
    tag="ul"
    handle=".move-icon"
    :list="questions"
    :group="{ name: 'g1' }"
    item-key="name"
  >
    <template #item="{ element }">
      <li class="faq-item" :class="{'edit': faqEdit}">
        <div class="faq-item-content" :class="{'edit': faqEdit}">
          <v-icon icon="mdi-menu" class="move-icon" v-if="faqEdit" @mousedown=""/>
          <p class="faq-link" :class="{'active': activeQuest === element.id}" @click="toggleCollapse(element)">
            <span>{{ element.name }}</span>
            <v-icon v-if="element.child.length"
                    :icon="element.isCollapsed || faqEdit ? 'mdi-chevron-down' : 'mdi-chevron-right'"/>
          </p>
          <v-icon icon="mdi-trash" color="red" class="remove-icon" v-if="faqEdit" @click="openDialog(element)"/>
        </div>
        <FaqList :activeQuestion="activeQuestion" :faqEdit="faqEdit" @choiceQuestion="choiceQuestion"
                 :questions="element.child"
                 v-if="faqEdit || element.isCollapsed && element.child.length > 0"/>
      </li>
    </template>
  </draggable>

  <v-dialog
    v-model="dialog"
    width="450"
  >
    <v-card>
      <v-card-text>
       Вы уверены, что хотите удалить вопрос? Если в нем есть воженные вопросы, они так же будут удалены!
      </v-card-text>
      <v-card-actions class="justify-end">
        <v-btn color="red-darken-1" @click="deleteElement()">Удалить</v-btn>
        <v-btn color="primary" @click="dialog = false">Отмена</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import draggable from "vuedraggable";

  export default {
    name: "faq-list",
    emits: ['choiceQuestion'],
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
      activeQuestion: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        dialog: false,
        deleteElementItem: null
      }
    },
    computed: {
      activeQuest(){
        return this.activeQuestion ? this.activeQuestion.id : null
      }
    },
    methods: {
      toggleCollapse(item) {
        if (item.child) {
          item.isCollapsed = !item.isCollapsed;
        }
        this.choiceQuestion(item);
      },
      choiceQuestion(item) {
        this.$emit('choiceQuestion', item)
      },
      openDialog(element) {
        this.deleteElementItem = element;
        this.dialog = true;
      },
      deleteElement() {
        const index = this.questions.indexOf(this.deleteElementItem);
        if (index !== -1) {
          this.questions.splice(index, 1);
        }
        this.deleteElementItem = null;
        this.dialog = false;
      }
    }
  }
</script>
