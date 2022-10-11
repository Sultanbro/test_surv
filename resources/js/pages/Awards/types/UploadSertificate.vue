<template>
  <BContainer class="" fluid>
    <div class="d-flex file">
      <BFormFile
        v-model="image"
        class="form-file"
        placeholder="Выберите Сертификат"
        drop-placeholder="Перетащите файл сюда..."
        accept=".jpg, .png, .pdf"
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
    <div v-if="hasImage" class="sertificate-prewiev">
      <div class="sertificate-modal">
        <BImg :src="imageSrc" class="mb-3 img" fluid block rounded></BImg>
        <BModal id="modal-1" title="BootstrapVue" class="w-80%">
          <UploadSertificateModal :img="imageSrc" />
        </BModal>
      </div>
      <BButton
        v-if="hasImage"
        variant="primary"
        class="ml-3 edit-sertificate-btn"
        v-b-modal.modal-1
        @click="uploadFile"
        >Разметить</BButton
      >
    </div>
  </BContainer>
</template>

<script>
import UploadSertificateModal from "../types/UploadSertificateModal.vue";
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
  },
  props: {
    fileType: Number,
  },
  data() {
    return {
      file: "",
      image: null,
      imageSrc: null,
    };
  },
  computed: {
    hasImage() {
      this.$emit("image-download", this.image);
      return !!this.image;
    },
  },
  watch: {
    image(newValue, oldValue) {
      if (newValue !== oldValue) {
        this.imageSrc = null;
        if (newValue) {
          base64Encode(newValue)
            .then((val) => {
              this.imageSrc = val;
            })
            .catch(() => {
              this.imageSrc = null;
            });
        } else {
          this.imageSrc = null;
        }
      }
    },
  },
  methods: {
    uploadFile() {
      this.file = this.$refs.file.files[0];
      let formData = new FormData();
      formData.append("file", this.file);
      this.axios
        .post("/upload.php", formData, {
          headers: {
            "Content-Type":
              "multipart/form-data; charset=utf-8; boundary=" +
              Math.random().toString().substr(2),
          },
        })
        .then(function (response) {
          if (!response.data) {
            alert("File not uploaded.");
          } else {
            alert("File uploaded successfully.");
            console.log(response.data);
          }
        })
        .catch(function (error) {
          console.log('error');
          console.log(error);
        });
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

<style>
.sertificate-modal {
  cursor: default;
  width: 150px;
  height: 150px;
  padding: 10px;
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
</style>
