<template>
  <div>
    <div class="info" v-if="!isEdit">
      <h2>{{ selectedItem?.title }}</h2>
      <h4>{{ selectedItem?.subtitle }}</h4>
    </div>
    <div v-else>
    <pre>{{ selectedItem }}</pre>
    <br>

      <v-text-field v-model="selectedItem.title" label="title" variant="outlined"></v-text-field>
      <br>
      <v-text-field v-model="selectedItem.subtitle" label="subtitle" variant="outlined"></v-text-field>
      <br>
      <editor
      
        api-key="iijzasm8i8kh2in9jk178tu9bfl7ud3p5kav9w802sggs11c"
        :init="{
          plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'print', 'preview', 'anchor',
            'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'paste', 'help', 'wordcount'
          ],
          toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image | removeformat | help',
          height: 500,
          menubar: false,
          images_upload_url: 'path/to/your/upload/handler/'
        }"
        v-model="selectedItem.content"
      />
    </div>
  </div>
</template>

<script>
import Editor from '@tinymce/tinymce-vue';

export default {
  props: {
    selectedItem: Object,
    isEdit:Boolean
  },
  components: {
    Editor
  },
  data() {
    return {
      content: null
    };
  },

};
</script>
