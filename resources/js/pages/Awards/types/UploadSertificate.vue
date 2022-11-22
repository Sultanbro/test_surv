<template>
  <div>
    <div class="d-flex file">
      <BFormFile
        v-model="image"
        class="form-file"
        placeholder="Выберите Сертификат"
        drop-placeholder="Перетащите файл сюда..."
        accept=".pdf"
        required
        type="file"
        id="file"
        ref="file"
      >
        <template slot="file-name" slot-scope="{ names }">
          <div class="file-name" v-for="(name, key) in names" :key="key">
            <BBadge class="badge-img" variant="dark">{{ name }}</BBadge>
          </div>
        </template></BFormFile
      >
      <BButton
        v-if="hasImage"
        variant="danger"
        class="ml-3 clear-btn"
        @click="clearImage"
        >Очистить</BButton
      >
    </div>


    <template v-if="imagePath.length > 0">
      <div class="sertificate-prewiev">
        <div class="sertificate-modal">
          <div class="preview-canvas" v-b-modal="'modal-constructor'">
            <vue-pdf-embed :source="imagePath"/>
          </div>
          <BModal id="modal-constructor" title="Контсруктор сертификата" size="xl" hide-footer centered>
            <UploadSertificateModal :styles="styles" :img="imagePath" @save-changes="saveStyles"/>
          </BModal>
        </div>
        </div>
    </template>
    <template v-else>
      <div v-if="hasImage" class="sertificate-prewiev">
        <div class="sertificate-modal">
          <div class="preview-canvas" v-b-modal="'modal-constructor'">
            <vue-pdf-embed v-if="imageSrc" :source="imageSrc"/>
          </div>
          <BModal id="modal-constructor" title="Контсруктор сертификата" size="xl" hide-footer centered>
            <UploadSertificateModal :styles="styles" :img="imageSrc" @save-changes="saveStyles"/>
          </BModal>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import UploadSertificateModal from "../types/UploadSertificateModal.vue";
import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";

const base64Encode = (data) =>
  new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(data);
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
  });

export default {
  name: "UploadSertificate",
  components: {
    UploadSertificateModal,
    VuePdfEmbed
  },
  props: {
    path: String,
    format: String,
    styles: String
  },
  data() {
    return {
      file: "",
      image: null,
      imageSrc: null,
      imagePath: ''
    };
  },
  mounted(){
    this.imagePath = this.path;
  },
  computed: {
    hasImage() {
      this.$emit("image-download", this.image);
      return !!this.image;
    },
  },
  watch: {
    image(newValue, oldValue) {
      let newValueString = newValue.name + newValue.size;
      let oldValueString = null;
      if (oldValue !== null) {
        oldValueString = oldValue.name + oldValue.size;
      }
      if (newValueString !== oldValueString) {
        this.imageSrc = '';
        if (newValue) {
          base64Encode(newValue)
                  .then((val) => {
                    this.imageSrc = val;
                    // this.$emit("image-download", this.image);
                  })
                  .catch(() => {
                    this.imageSrc = '';
                  });
        } else {
          this.imageSrc = '';
        }
      }
    },
  },
  methods: {
    saveStyles(fullName, courseName, hours, date){
      const styles = {};
      styles.fullName = fullName;
      styles.courseName = courseName;
      styles.hours = hours;
      styles.date = date;
      this.$emit("styles-change", styles);
    },
    clearImage() {
      this.image = null;
    },
    onSubmit() {
      if (!this.image) {
        alert("Please select an image.");
        return;
      }

      alert("Form submitted!");
    },
  },
};
</script>

<style lang="scss" scoped>
  .preview-canvas{
    cursor: pointer;
    border: 1px solid #999;
    border-radius: 10px;
    overflow: hidden;
    display: inline-block;
    transition: 0.2s all ease;
    &:hover{
      transform: scale(1.05);
      box-shadow: 0 0 6px 0 #999;
    }
    canvas{
      height: 170px!important;
      width: auto!important;
    }
  }
.sertificate-modal {
  margin-top: 20px;
}
.img {
  object-fit: cover;
  width: 100%;
  height: 100%;
}
.custom-file .custom-file-label {
  position: relative;
  height: auto;
}
.custom-file-input {
  display: none;
}
.file {
  height: auto;
  margin: auto;
}
.custom-file {
  height: auto;
}
.form-file {
  margin: auto;
}
.file-name {
  text-overflow: ellipsis;
  overflow: hidden;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}
.badge-img {
  text-overflow: ellipsis;
  overflow: hidden;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}
.sertificate-prewiev {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  justify-content: space-between;
}
.edit-sertificate-btn {
  height: 40px;
  width: 120px;
}
.clear-btn {
  height: 40px;
}
.modal-body{
  max-height: calc(100vh - 130px) !important;
  min-height: calc(100vh - 130px) !important;
  overflow: auto !important;
}
.modal-dialog{
  width: 100% !important;
  max-width: 100% !important;
  margin: 0 !important;
}

</style>
