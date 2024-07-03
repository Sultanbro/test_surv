<script setup lang="ts">
import Editor from '@tinymce/tinymce-vue'
import type { Settings } from '@types/tinymce'
import axios from 'axios'

type Question = {
  id: number
  parent_id: number | null
  order: number
  title: string
  body?: string
  page: string
  isCollapsed: boolean
  children: Array<Question>
}

defineProps<{
  active: Question | null
  faqEdit: boolean
}>()

const emit = defineEmits<{
  (e: 'change', q: Question): void
}>()

const divider = '___'
const pageVariants = [
  {
    title: 'Выберите страницу',
    value: divider,
  },
  {
    title: 'Профиль',
    value: '/',
  },
  {
    title: 'Новости',
    value: `/news`,
  },
  {
    title: 'Структура',
    value: `/structure`,
  },
  {
    title: 'База знаний',
    value: `/kb`,
  },
  {
    title: 'База знаний - Глоссарий',
    value: `/kb${divider}glossary`,
  },
  {
    title: 'Читать книги',
    value: `/admin/upbooks`,
  },
  {
    title: 'Смотреть видео',
    value: `/video_playlists`,
  },
  {
    title: 'Курсы',
    value: `/courses`,
  },
  {
    title: 'ТОП - Полезность',
    value: `/timetracking/top`,
  },
  {
    title: 'ТОП - Маржа',
    value: `/timetracking/top/margin`,
  },
  {
    title: 'ТОП - Выручка',
    value: `/timetracking/top/revenue`,
  },
  {
    title: 'ТОП - Прогноз',
    value: `/timetracking/top/forecast`,
  },
  {
    title: 'ТОП - NPS',
    value: `/timetracking/top/nps`,
  },
  {
    title: 'ТОП - Profit',
    value: `/timetracking/top/profit`,
  },
  {
    title: 'Табель',
    value: `/timetracking/reports`,
  },
  {
    title: 'Время прихода',
    value: `/timetracking/reports/enter-report`,
  },
  {
    title: 'HR - рекрутинг - сводная',
    value: `/timetracking/analytics`,
  },
  {
    title: 'HR - рекрутинг - стажеры',
    value: `/timetracking/analytics${divider}recruts`,
  },
  {
    title: 'HR - 2й этап - сводная',
    value: `/timetracking/analytics${divider}2`,
  },
  {
    title: 'HR - 2й этап - оценка',
    value: `/timetracking/analytics${divider}rate`,
  },
  {
    title: 'HR - 2й этап - отсутствие',
    value: `/timetracking/analytics${divider}miss`,
  },
  {
    title: 'HR - забота',
    value: `/timetracking/analytics${divider}care`,
  },
  {
    title: 'HR - Увольнение - текучка',
    value: `/timetracking/analytics${divider}flood`,
  },
  {
    title: 'HR - Увольнение - бот',
    value: `/timetracking/analytics${divider}bot`,
  },
  {
    title: 'HR - Увольнение - причины',
    value: `/timetracking/analytics${divider}reasons`,
  },
  {
    title: 'HR - маркетинг - рефералка',
    value: `/timetracking/analytics${divider}ref`,
  },
  {
    title: 'HR - маркетинг - лиды',
    value: `/timetracking/analytics${divider}leads`,
  },
  {
    title: 'Аналитика - сводная',
    value: `/timetracking/an`,
  },
  {
    title: 'Аналитика - подробная',
    value: `/timetracking/an${divider}detail`,
  },
  {
    title: 'Начисления',
    value: `/timetracking/salaries`,
  },
  {
    title: 'ОКК - оценка - неделя',
    value: `/timetracking/salaries`,
  },
  {
    title: 'ОКК - оценка - месяц',
    value: `/timetracking/salaries${divider}month`,
  },
  {
    title: 'ОКК - оценка - оценка',
    value: `/timetracking/salaries${divider}rate`,
  },
  {
    title: 'ОКК - курсы',
    value: `/timetracking/salaries${divider}course`,
  },
  {
    title: 'Карта',
    value: `/maps`,
  },
  {
    title: 'KPI',
    value: `/kpi`,
  },
  {
    title: 'KPI - бонусы',
    value: `/kpi/bonus`,
  },
  {
    title: 'KPI - квартальная',
    value: `/kpi/premium`,
  },
  {
    title: 'KPI - статистика',
    value: `/kpi/statistics`,
  },
  {
    title: 'KPI - показатели',
    value: `/kpi/indicators`,
  },
  {
    title: 'FAQ',
    value: `/timetracking/info`,
  },
  {
    title: 'Штрафы',
    value: `/timetracking/fines`,
  },
  {
    title: 'Настройки - сотрудники',
    value: `/timetracking/settings`,
  },
  {
    title: 'Настройки - создание сотрудника',
    value: `/timetracking/create-person`,
  },
  {
    title: 'Настройки - редактирование сотрудника',
    value: `/timetracking/edit-person`,
  },
  {
    title: 'Настройки - компания - должности',
    value: `/timetracking/settings${divider}pos`,
  },
  {
    title: 'Настройки - компания - отсделы',
    value: `/timetracking/settings${divider}group`,
  },
  {
    title: 'Настройки - компания - смены',
    value: `/timetracking/settings${divider}shift`,
  },
  {
    title: 'Настройки - компания - налоги',
    value: `/timetracking/settings${divider}tax`,
  },
  {
    title: 'Настройки - штрафы',
    value: `/timetracking/settings${divider}fine`,
  },
  {
    title: 'Настройки - уведомления',
    value: `/timetracking/settings${divider}noti`,
  },
  {
    title: 'Настройки - доступы',
    value: `/timetracking/settings${divider}rules`,
  },
  {
    title: 'Настройки - интеграции',
    value: `/timetracking/settings${divider}api`,
  },
  {
    title: 'Настройки - награды',
    value: `/timetracking/settings${divider}awards`,
  },
]

const mceKey =
  process.env.NODE_ENV === 'production'
    ? 'iijzasm8i8kh2in9jk178tu9bfl7ud3p5kav9w802sggs11c'
    : 'pkzfksqtgrm6lo8hkwxq5p1522u96q0vgpnqxh024n3493dt'
const mceInit: Settings = {
  images_upload_url: '/upload/images/',
  automatic_uploads: true,
  // height: window.innerHeight - 320,
  // setup(editor){
  //   editor.on('init change', function () {
  //     editor.uploadImages();
  //   });
  // },
  images_upload_handler: onUploadImage,
  //paste_data_images: false,
  resize: true,
  autosave_ask_before_unload: true,
  // powerpaste_allow_local_images: true,
  browser_spellcheck: true,
  // contextmenu: true,
  spellchecker_whitelist: ['Ephox', 'Moxiecode'],
  language: 'ru',
  convert_urls: false,
  relative_urls: false,
  language_url: '/static/langs/ru.js',
  fontsize_formats: '8pt 10pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 20pt 22pt 24pt 26pt 28pt 30pt 36pt',
  // lineheight_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt',
  plugins: [
    'advlist',
    'anchor',
    'autolink',
    'codesample',
    'colorpicker',
    'fullscreen',
    'help',
    'image',
    'imagetools',
    'lists',
    'link',
    'media',
    'noneditable',
    'preview',
    'searchreplace',
    'table',
    'template',
    'textcolor',
    'visualblocks',
    'wordcount',
  ],
  menubar: false, //'file edit view insert format tools table help',
  toolbar_mode: 'sliding',
  toolbar: [
    'styleselect | bold italic underline strikethrough | ',
    'table | fontselect fontsizeselect formatselect | ',
    'alignleft aligncenter alignright alignjustify | ',
    'outdent indent |  numlist bullist | forecolor backcolor removeformat | preview |  image media  link | undo redo',
  ].join(''),
  // toolbar_sticky: true,
  content_style:
    '.lineheight20px { line-height: 20px; }' +
    '.lineheight22px { line-height: 22px; }' +
    '.lineheight24px { line-height: 24px; }' +
    '.lineheight26px { line-height: 26px; }' +
    '.lineheight28px { line-height: 28px; }' +
    '.lineheight30px { line-height: 30px; }' +
    '.lineheight32px { line-height: 32px; }' +
    '.lineheight34px { line-height: 34px; }' +
    '.lineheight36px { line-height: 36px; }' +
    '.lineheight38px { line-height: 38px; }' +
    '.lineheight40px { line-height: 40px; }' +
    'body { padding: 20px;max-width: 960px;margin: 0 auto; }' +
    '.tablerow1 { background-color: #D3D3D3; }',
  formats: {
    lineheight20px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight20px',
    },
    lineheight22px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight22px',
    },
    lineheight24px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight24px',
    },
    lineheight26px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight26px',
    },
    lineheight28px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight20px',
    },
    lineheight30px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight30px',
    },
    lineheight32px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight32px',
    },
    lineheight34px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight34px',
    },
    lineheight36px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight36px',
    },
    lineheight38px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight38px',
    },
    lineheight40px: {
      selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
      classes: 'lineheight40px',
    },
  },
  style_formats: [
    { title: 'lineheight20px', format: 'lineheight20px' },
    { title: 'lineheight22px', format: 'lineheight22px' },
    { title: 'lineheight24px', format: 'lineheight24px' },
    { title: 'lineheight26px', format: 'lineheight26px' },
    { title: 'lineheight28px', format: 'lineheight28px' },
    { title: 'lineheight30px', format: 'lineheight30px' },
    { title: 'lineheight32px', format: 'lineheight32px' },
    { title: 'lineheight34px', format: 'lineheight34px' },
    { title: 'lineheight36px', format: 'lineheight36px' },
    { title: 'lineheight38px', format: 'lineheight38px' },
    { title: 'lineheight40px', format: 'lineheight40px' },
  ],
  content_css: ['/static/css/mycontent.css', '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i'],

  // media
  media_alt_source: false,
  media_dimensions: false,
  media_poster: false,
  iframe_template_callback({
    title,
    source,
    width,
    height,
  }: {
    title: string
    source: string
    width: string
    height: string
  }) {
    return `<iframe title="${title}" src="${source}" allowfullscreen="allowfullscreen" style="aspect-ratio: ${width}/${height}; width: 100%;"></iframe>`
  },
  // media
}

async function onUploadImage(blobInfo, progress) {
  const formData = new FormData()
  formData.append('attachment', blobInfo.blob())
  const onUploadProgress = event => {
    progress(Math.round((event.loaded * 100) / event.total))
  }
  // formData.append('id', 0)
  try {
    const { data } = await axios.post('/admin/upload/images/', formData, { onUploadProgress })
    return data.location
  } catch (error) {
    console.error(error)
  }
}
</script>

<template>
  <div
    v-if="active"
    class="faq-content"
  >
    <div v-if="faqEdit">
      <VRow>
        <VCol cols="9">
          <VTextField v-model="active.title">
            <template v-slot:append-inner>
              <v-tooltip location="bottom">
                <template v-slot:activator="{ props }">
                  <v-icon
                    v-bind="props"
                    icon="mdi-help-circle-outline"
                  />
                </template>
                Название пункта меню в попапе FAQ
              </v-tooltip>
            </template>
          </VTextField>
        </VCol>
        <VCol cols="3">
          <VSelect
            v-model="active.page"
            :items="pageVariants"
          >
            <template v-slot:append>
              <v-tooltip location="bottom">
                <template v-slot:activator="{ props }">
                  <v-icon
                    v-bind="props"
                    icon="mdi-help-circle-outline"
                  />
                </template>
                На указанной странице автоматически выберется этот пункт
              </v-tooltip>
            </template>
          </VSelect>
        </VCol>
        <VCol cols="6">
          
        </VCol>
      </VRow>
    </div>
    <h4
      v-else
      class="faq-content-title"
    >
      {{ active.title }}
    </h4>

    <div
      v-if="faqEdit"
      class="faq-content-editor"
    >
      <Editor
        v-model="active.body"
        :api-key="mceKey"
        :init="mceInit"
      />
    </div>
    <div
      v-else
      class="faq-content-body scrollable"
      v-html="active.body"
    />
  </div>
</template>

<style lang="scss">
.faq-content {
  // display: flex;
  // flex-direction: column;
  // height: 100%;
}

.faq-content-title {
  text-align: center;
  border-bottom: 1px solid #ddd;
}

// .faq-content-input {
//   margin-bottom: 20px;
// }

// .faq-content-editor {
//   display: flex;
//   flex-direction: column;
//   flex: 1;
//   .tox-tinymce {
//     height: 100% !important;
//   }
// }

.faq-content-body {
  flex: 1;
  max-width: 960px;
  margin: 0 auto;
  overflow-y: auto;
  height: calc(100vh - 80px); /* Регулируем высоту для скроллинга */
}
</style>
