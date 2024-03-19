<script setup lang="ts">
import FaqList from '@/views/pages/faq/faq-list.vue';
import FaqContent from '@/views/pages/faq/faq-content.vue';
import axios from 'axios';

type Question = {
  id: number
  parent_id: number
  order: number
  title: string
  page: string
  body: string
  isCollapsed: boolean
  children: Array<Question>
}

let fakeId = 0
const fakeItems: Array<Question> = [
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Профиль',
    body: '<p>В профиле отображаются все ваши личные данные</p>',
    isCollapsed: false,
    page: '/',
    children: [
      {
        id: ++fakeId,
        parent_id: 0,
        order: 0,
        title: 'Баланс оклада',
        body: '<p>В балансе оклада вы можете просмотреть детализацию вашего дохода за все время</p>',
        isCollapsed: false,
        page: '/${divider}balance',
        children: [],
      },
    ]
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Новости',
    body: '<p>Здесь вы можете просматривать новости и события вашей компании</p>',
    isCollapsed: false,
    page: '/news',
    children: [
      {
        id: ++fakeId,
        parent_id: 0,
        order: 0,
        title: 'Дни рождения',
        body: '<p>В правом окне страницы новостей отображаются дни рождения всех сотрудников</p>',
        isCollapsed: false,
        page: '/news',
        children: [
          {
            id: ++fakeId,
            parent_id: 0,
            order: 0,
            title: 'Подарки',
            body: '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam officiis temporibus debitis libero fuga placeat quae at praesentium incidunt excepturi. At asperiores officia quasi fugit dignissimos minima non enim nisi?</p>' + (new Array(30)).join('<br>1'),
            isCollapsed: false,
            page: '/news',
            children: [],
          },
        ],
      },
    ],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
  {
    id: ++fakeId,
    parent_id: 0,
    order: 0,
    title: 'Карта',
    body: '<p>На этой карте вы можете посмотреть, где и сколько сотрудников находятся...</p>',
    isCollapsed: false,
    page: '/maps',
    children: [],
  },
]

const faqEdit = ref(false)
const active = ref<null | Question>(null)
const questions = ref(fakeItems)
const newId = ref(0)

onMounted(() => {
  fetchFAQ()
})

async function fetchFAQ(){
  try {
    const {data} = await axios('/faq')
    console.log(data)
  }
  catch (error) {
    console.error(error)
  }
}
async function saveFAQ(){}
async function deleteFAQ(){}
function choiceQuestion(item: Question) {
  active.value = item
}
function onChangeContent(data: any) {
  console.log(data);
}
function addElement(parent_id: number, order: number) {
  const id = --newId.value

  questions.value.push({
    id,
    parent_id,
    order,
    title: 'Новый вопрос',
    isCollapsed: false,
    page: '',
    body: '<h1>Заполните содержимое вопроса</h1>',
    children: [],
  })
}
</script>

<template>
  <VCard class="faq-card">
    <VCardTitle class="faq-card-header">
      <div>Вопросы и ответы</div>
      <VSpacer />
      <div class="faq-card-actions">
        <VBtn
          variant="text"
          icon="mdi-pencil"
          color="blue-darken-2"
          size="small"
          @click="faqEdit = !faqEdit"
        />
      </div>
    </VCardTitle>
    <VDivider />
    <VContainer class="faq-card-body">
      <VRow>
        <VCol cols="3" class="faq-card-list ">
          <div class="scrollable flex-grow-1">
            <FaqList
              v-if="questions.length"
              :active="active"
              :questions="questions"
              :faq-edit="faqEdit"
              :level="1"
              @choiceQuestion="choiceQuestion"
            />
            <p
              v-else
              class="no-questions"
            >
              Добавитьте новый вопрос
            </p>
          </div>

          <div class="faq-list-add">
            <VBtn
              v-if="faqEdit"
              block
              @click="addElement"
            >Добавить</VBtn>
          </div>
        </VCol>
        <VCol cols="9" clasa="faq-card-content">
          <FaqContent
            :active="active"
            :faq-edit="faqEdit"
            @change="onChangeContent"
          />
        </VCol>
      </VRow>
    </VContainer>
  </VCard>
</template>


<style lang="scss">
.faq-card {
  display: flex;
  flex-flow: column;
  // 48px - pa-6  in layout
  height: calc(100vh - (48px + var(--v-layout-top) + var(--v-layout-bottom)));
  ul {
    list-style: none;
  }
}

.faq-card-header{
  display: flex;
  align-items: center;
}

.faq-card-body{
  flex: 1;
  display: flex;
  flex-flow: column;
  max-height: calc(100% - 59px);
  padding: 12px;
  > .v-row{
    flex: 1;
    max-height: calc(100% + 24px);
  }
}

.faq-card-list{
  display: flex;
  flex-flow: column;
  max-height: 100%;
  outline: 1px solid rgba(var(--v-border-color),var(--v-border-opacity));
}

.faq-list-add {
  padding: 0 15px;
}

.no-questions{
  opacity: 0.7;
  padding: 0 15px;
  margin-bottom: 20px;
}

.faq-card-content{
  display: flex;
  flex-flow: column;
  height: 100%;
}
</style>
