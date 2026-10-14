<template>
  <button
    v-if="faqEdit"
    block
    @click="addPaper"
    class="post-news__button"
  >
    Добавить статью
  </button>
  <Table title="Добавление статей">
    <template #header-button>
      <VBtn
        v-if="faqEdit && active"
        variant="text"
        color="green-darken-2"
        size="small"
        @click="updatePaper"
      >
        Сохранить
      </VBtn>
      <VBtn
        v-if="faqEdit && active"
        variant="text"
        color="green-darken-2"
        size="small"
      >
        Опубликовать
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
        <div
          class="paper-item d-flex align-center justify-space-between"
          v-if="papers.length"
          v-for="paper in papers"
          :key="paper.id"
          
        >
          <div @click="getPaper(paper)">
            {{ formatDateTime(paper.created_at) }}
            {{ paper.title }}
          </div>
          <v-icon
            v-if="faqEdit"
            @click="deletePaper(paper)"
            icon="mdi-trash"
            color="red"
            class="remove-icon"
          />
        </div>
      </div>
    </template>
    <template #col-2>
      <ArticleContent
        :active="active"
        :faq-edit="faqEdit"
      />
    </template>
  </Table>
</template>

<script setup lang="ts">
import ArticleContent from '@/views/pages/faq/article-content.vue'
import Table from '@/components/ui/table/Table.vue'
import { formatDateTime } from '@/utils/formatDateTime'
import axios from 'axios'

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

type TPaper = {
  id?: string | number
  title: string
  body: string
  description: string
  image?: string | number
  publish: string | number
  created_at?: string
  updated_at?: string
}

const faqEdit = ref(false)
const active = ref<null | TPaper>(null)

const papers = ref<TPaper[]>([])

const getNews = async () => {
  papers.value = (await axios.get('/paper')).data.data
  console.log(papers.value)
}

getNews()

const addPaper = async () => {
  const defaultImageFile = new File([""], "default-image.jpg", { type: "image/jpeg" });
  const formData = new FormData();
  formData.append('title', 'Новая статья');
  formData.append('description', 'Заполните заголовок');
  formData.append('body', '<h1>Заполните содержимое статьи</h1>');
  formData.append('publish', "1");
  formData.append('image', defaultImageFile);

  const paper = (await axios.post('/paper', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })).data;

  papers.value.push(paper.data);
}

const getPaper = async (paper: TPaper) => {
  try {
    const { data } = await axios.get(`/paper/get/${paper.id}`)
    active.value = {
      ...data.data,
    }

    console.log(active.value)
  } catch (error) {
    console.error(error)
  }
}

const deletePaper = async (currentPaper: TPaper) => {
  await axios.delete(`/paper/delete/${currentPaper.id}`)
  papers.value = papers.value.filter(paper => paper.id !== currentPaper.id)
}

const updatePaper = async () => {
  if (!active.value) return

  console.log(active.value);

  try {
    await axios.put(`/paper/update/${active.value.id}`, active.value)
  } catch (error) {
    console.error(error)
  }
}

</script>

<style scoped lang="scss">
.post-news__button {
  background-color: rgb(var(--v-theme-primary)) !important;
  padding: 0.5%;
  color: white;
  border-radius: 8px;
  font-weight: 600;
}

.paper-item {
  cursor: pointer;
  padding: 1%;
  transition: all ease 100ms;
  border-radius: 5px;
  width: 100%;
  &:hover {
    background-color: #c4c4c41d;
  }
  .remove-icon {
    cursor: pointer;
  }
}
</style>
