<template>
  <VCard class="faq-card">
    <v-card-title>
      <p class="faq-card-title">Вопросы и ответы</p>
      <div class="faq-card-actions">
        <v-btn
          variant="text"
          icon="mdi-pencil"
          color="blue-darken-2"
          size="small"
          @click="faqEdit = !faqEdit"
        ></v-btn>
      </div>
    </v-card-title>
    <div class="faq-card-body">
      <VRow>
        <VCol cols="3">
          <div class="faq-list-container">
            <FaqList :activeQuestion="activeQuestion" :questions="questions" :faqEdit="faqEdit"
                     @choiceQuestion="choiceQuestion" v-if="questions.length"/>
            <p class="no-questions" v-else>Добавитьте новый вопрос</p>
            <div class="faq-list-add">
              <v-btn block v-if="faqEdit" @click="addElement">Добавить</v-btn>
            </div>
          </div>
        </VCol>
        <VCol cols="9">
          <div class="faq-content-container">
            <FaqContent :activeQuestion="activeQuestion" :questions="questions" :faqEdit="faqEdit" :faqContent="faqContent" @onChangeContent="onChangeContent"/>
          </div>
        </VCol>
      </VRow>
    </div>
  </VCard>
</template>

<script>
  import FaqList from '@/views/pages/faq/faq-list.vue';
  import FaqContent from '@/views/pages/faq/faq-content.vue';

  export default {
    components: {
      FaqList,
      FaqContent
    },
    data() {
      return {
        faqEdit: false,
        activeQuestion: null,
        contents: [
          {
            qId: 1,
            content: '<p>В профиле отображаются все ваши личные данные</p>'
          },
          {
            qId: 11,
            content: '<p>В балансе оклада вы можете просмотреть детализацию вашего дохода за все время</p>'
          },
          {
            qId: 2,
            content: '<p>Здесь вы можете просматривать новости и события вашей компании</p>'
          },
          {
            qId: 22,
            content: '<p>В правом окне страницы новостей отображаются дни рождения всех сотрудников</p>'
          },
          {
            qId: 3,
            content: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>'
          }
        ],
        faqContent: null,
        questions: [
          {
            id: 1,
            name: "Профиль",
            isCollapsed: false,
            child: [
              {
                id: 11,
                name: "Баланс оклада",
                isCollapsed: false,
                child: []
              }
            ]
          },
          {
            id: 2,
            name: "Новости",
            isCollapsed: false,
            child: [
              {
                id: 22,
                name: "Дни рождения",
                isCollapsed: false,
                child: []
              }
            ]
          },
          {
            id: 3,
            name: "Карта",
            isCollapsed: false,
            child: []
          }
        ]
      }
    },
    methods: {
      choiceQuestion(item) {
        this.faqContent = this.contents.find(c => item.id === c.qId);
        this.activeQuestion = item;
      },
      onChangeContent(data) {
        console.log(data);
      },
      addElement() {
        const id = Date.now();
        this.questions.push({
          id: id,
          name: "Новый вопрос",
          isCollapsed: false,
          child: []
        });
        this.contents.push({
          qId: id,
          content: '<h1>Заполните содержимое вопроса</h1>'
        })
      },
    }
  }
</script>


<style lang="scss">
  .faq-card {
    .faq-list-add {
      padding: 0 15px;
    }

    .no-questions{
      opacity: 0.7;
      padding: 0 15px;
      margin-bottom: 20px;
    }

    .faq-content-title{
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #ddd;
    }

    .faq-content-input{
      margin-bottom: 20px;
    }

    .v-card-title {
      padding: 5px 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      line-height: 1;
      border-bottom: 1px solid #ddd;

      .faq-card-title {
        margin: 0;
      }
    }

    .v-row {
      margin: 0 -12px;
    }

    .v-col-3 {
      border-right: 1px solid #ddd;
      padding-right: 0;
      padding-top: 0;
    }

    .faq-content-container {
      padding: 0 15px 0 0;
      color: #333;
    }

    .faq-list-container, .faq-content-container {
      min-height: calc(100vh - 210px);
      max-height: calc(100vh - 210px);
      overflow: auto;
    }

    ul {
      list-style: none;
    }

    .faq-list {
      padding: 15px 10px;

      .faq-list {
        padding: 0 0 0 15px;
      }

      .faq-item {
        transition: 0.1s all ease;

        &.edit {
          padding: 5px 0;
        }

        &.sortable-ghost {
          padding: 5px;
          border: 1px dashed #ddd;
        }

        .faq-item-content {
          display: flex;
          align-items: center;

          &.edit {
            .faq-link {
              margin-left: 10px;
              margin-right: 10px;
              background-color: #f2f2f2;
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
            background-color: #f2f2f2;
          }

          &.active {
            color: #9961fd;
          }
        }
      }
    }
  }
</style>
