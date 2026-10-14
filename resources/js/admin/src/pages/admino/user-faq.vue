<script setup lang="ts">
import FaqList from '@/views/pages/faq/faq-list.vue'
import FaqContent from '@/views/pages/faq/faq-content.vue'
import Table from '@/components/ui/table/Table.vue'
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
  const items = parentId ? questionsMap.value[parentId]?.children : questions.value

  const quetion = questionsMap.value[currentId]

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
  itemId: string
  toId: string
}

function onOrder(item: TItem) {
  const currentQuestion = questionsMap.value[item.itemId]

  if (!currentQuestion) return

  // const parentId = currentQuestion.parent_id
  const currentQuestionId = currentQuestion.id
  currentQuestion.parent_id = +item.toId || null

  //if (+item.toId !== parentId) - условие которое мешело порядку сохранение элементу внутри родительского элемента

  saveOrder(+item.toId, currentQuestionId)
}
</script>

<template>
  <Table title="Вопросы и ответы">
    <template #header-button>
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
    </template>

    <template #col-1>
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
    </template>

    <template #col-2>
      <FaqContent
        :active="active"
        :faq-edit="faqEdit"
      />
    </template>
  </Table>
</template>