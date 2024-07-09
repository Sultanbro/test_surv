<script setup lang="ts">
import Editor from '@tinymce/tinymce-vue'
import type {Settings} from '@types/tinymce'
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

type TPaper = {
  id?: string | number
  title: string
  body: string
  description: string
  image?: string
  publish: string
  created_at?: string
  updated_at?: string
}

defineProps<{
  active: TPaper | null
  faqEdit: boolean
}>()

const emit = defineEmits<{
  (e: 'change', q: Question): void
}>()

const divider = '___'

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
    {title: 'lineheight20px', format: 'lineheight20px'},
    {title: 'lineheight22px', format: 'lineheight22px'},
    {title: 'lineheight24px', format: 'lineheight24px'},
    {title: 'lineheight26px', format: 'lineheight26px'},
    {title: 'lineheight28px', format: 'lineheight28px'},
    {title: 'lineheight30px', format: 'lineheight30px'},
    {title: 'lineheight32px', format: 'lineheight32px'},
    {title: 'lineheight34px', format: 'lineheight34px'},
    {title: 'lineheight36px', format: 'lineheight36px'},
    {title: 'lineheight38px', format: 'lineheight38px'},
    {title: 'lineheight40px', format: 'lineheight40px'},
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
    const {data} = await axios.post('/admin/upload/images/', formData, {onUploadProgress})
    return data.location
  } catch (error) {
    console.error(error)
  }
}
</script>

<template>
  <div v-if="active" class="faq-content">
    <div v-if="faqEdit">
      <VRow>
        <VCol cols="9">
          <VTextField v-model="active.title">
            <template v-slot:append-inner>
              <v-tooltip location="bottom">
                <template v-slot:activator="{ props }">
                  <v-icon v-bind="props" icon="mdi-help-circle-outline"/>
                </template>
                Название пункта меню в попапе FAQ
              </v-tooltip>
            </template>
          </VTextField>
        </VCol>
        <VCol cols="3"></VCol>
        <div class="w-100">
          <VCol cols="9">
            <v-file-input
                v-model="active.image"
                label="Добавьте изображение"
                variant="outlined"
            ></v-file-input>
          </VCol>
          <div class="ma-1">
            <v-textarea variant="outlined" v-model="active.description"></v-textarea>
          </div>
        </div>
      </VRow>
    </div>
    <h4 v-else class="faq-content-title">
      {{ active.title }}
    </h4>

    <div v-if="faqEdit" class="faq-content-editor mt-2">
      <Editor v-model="active.body" api-key="jv0h9szrpjbnrx2g3pftvxsd4lcdaaiacb96dvzabbkzszff" :init="mceInit"/>
    </div>
    <div v-else class="faq-content-body scrollable" v-html="active.body"/>
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
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* Internet Explorer 10+ */
  flex: 1;
  max-width: 1225px;
  margin: 0 auto;
  overflow-y: auto;
  height: calc(100vh - 80px); /* Регулируем высоту для скроллинга */
}
</style>
