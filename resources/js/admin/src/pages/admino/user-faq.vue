<script setup lang="ts">
import FaqList from '@/views/pages/faq/faq-list.vue'
import FaqContent from '@/views/pages/faq/faq-content.vue'
import axios from 'axios'

type MoveEvent = {
  item: HTMLElement
  to: HTMLElement
}

type Question = {
  id: number
  parent_id: number | null
  order: number
  title: string
  page: string
  body?: string
  isCollapsed: boolean
  children: Array<Question>
}

const faqEdit = ref(false)
const active = ref<null | Question>(null)
const questions = ref<Array<Question>>([])

const questionsMap = computed(() => {
  return treeToMap(questions.value)
})

onMounted(() => {
  fetchFAQ()
})

function treeToMap(items: Array<Question>, result: { [key: string]: Question } = {}) {
  return items.reduce((result, item) => {
    result[`${item.id}`] = item
    if (item.children?.length) treeToMap(item.children, result)
    return result
  }, result)
}

async function fetchFAQ() {
  try {
    const { data } = await axios.get<{ data: Array<Question> }>('/faq')
    console.log(data)
    questions.value = data.data.map(item => ({ ...item, isCollapsed: false }))
  } catch (error) {
    console.error(error)
  }
}
async function createFAQ(item: Question) {
  try {
    const { data } = await axios.post('/faq', item)
    questions.value.push({
      ...data.data,
      children: [],
      isCollapsed: false,
    })
  } catch (error) {
    console.error(error)
  }
}
async function updateFAQ() {
  if (!active.value) return

  try {
    await axios.put(`/faq/update/${active.value.id}`, active.value)
    console.log(active.value)
  } catch (error) {
    console.error(error)
  }
}
async function saveFAQ() {
  if (!active.value) return
  return updateFAQ()
}
async function deleteFAQ(item: Question) {
  try {
    await axios.delete(`/faq/delete/${item.id}`)
    const list = item.parent_id ? questionsMap.value[item.parent_id].children : questions.value
    if (!list) return
    const index = list.findIndex(i => i.id === item.id)
    if (!~index) return
    list.splice(index, 1)
  } catch (error) {
    console.error(error)
  }
}
async function onSelect(item: Question) {
  try {
    const { data } = await axios.get(`/faq/get/${item.id}`)
    active.value = {
      ...data.data,
      isCollapsed: true,
    }
    console.log(active.value)
  } catch (error) {
    console.error(error)
  }
}

async function addElement(parent_id: number, order: number) {
  await createFAQ({
    id: 0,
    parent_id: parent_id || null,
    order,
    title: 'Новый вопрос',
    isCollapsed: false,
    page: '___',
    body: '<h1>Заполните содержимое вопроса</h1>',
    children: [],
  })
}

async function saveOrder(parentId: number, currentId: number) {
  console.log(`Родитель: ${parentId}`, `Дочерний ${currentId}`)
  const items = parentId ? questionsMap.value[parentId]?.children : questions.value

  const quetion = questionsMap.value[currentId]

  console.log(quetion)

  if (!items) return
  const request = {
    items: items.map(({ id, parent_id }, index) => ({
      id,
      parent_id,
      order: index,
    })),
  }
  try {
    await axios.post('/faq/set-order', request)

    if (parentId === 0) {
      await axios.put(`/faq/update/${currentId}`, {
        ...quetion,
        parent_id: null,
      })
    } else {
      await axios.put(`/faq/update/${currentId}`, {
        ...quetion,
        parent_id: parentId,
      })
    }
  } catch (error) {
    console.error(error)
  }
}

type TItem = {
  itemId: string,
  toId: string
}

function onOrder(item: TItem) {
  const currentQuestion = questionsMap.value[item.itemId]

  console.log(`Текущий вопрос ${item.itemId}`)

  if (!currentQuestion) return

  const parentId = currentQuestion.parent_id
  const currentQuestionId = currentQuestion.id
  currentQuestion.parent_id = +item.toId || null

  if (+item.toId !== parentId) saveOrder(+item.toId, currentQuestionId)
}
</script>

<template>
  {{ questions }}
  <VCard class="faq-card">
    <VCardTitle class="faq-card-header">
      <div>Вопросы и ответы</div>
      <VSpacer />
      <div class="faq-card-actions">
        <VBtn
          v-if="faqEdit && active"
          variant="text"
          color="green-darken-2"
          size="small"
          @click="saveFAQ"
        >
          Сохранить
        </VBtn>
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
        <VCol
          cols="3"
          class="faq-card-list"
        >
          <div class="scrollable flex-grow-1">
            <FaqList
              v-if="questions.length"
              :active="active"
              :questions="questions"
              :faq-edit="faqEdit"
              :level="1"
              @select="onSelect"
              @order="onOrder"
              @delete="deleteFAQ"
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
              @click="addElement(0, questions.length)"
              >Добавить</VBtn
            >
          </div>
        </VCol>
        <VCol
          cols="9"
          clasa="faq-card-content"
        >
          <FaqContent
            :active="active"
            :faq-edit="faqEdit"
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
