<template>
  <sidebar
    id="edit-award-sidebar"
    title="Сертификат"
    :open="open"
    @close="$emit('update:open', false)"
    width="60%"
  >
    <BForm ref="newSertificateForm" @submit.prevent="onSubmit">
      <BFormGroup
        id="input-group-1"
        label="Название награды"
        label-for="input-1"
        label-cols-sm="3"
        label-align-sm="left"
        description="Например, сертификаты, грамоты и т.п."
      >
        <BFormInput
          id="input-1"
          v-model="form.name"
          type="text"
          placeholder="Название"
          required
        ></BFormInput>
      </BFormGroup>
      <BFormGroup
        id="input-group-2"
        label="Описание"
        label-for="input-2"
        label-cols-sm="3"
        label-align-sm="left"
        description="Например, сертификаты, грамоты и т.п."
      >
        <BFormTextarea
          id="input-2"
          v-model="form.discription"
          type="text"
          placeholder="Сертификаты выдаются каждому сотруднику, который прошел курс в профиле сотрудника и набрал проходной балл"
          rows="3"
          max-rows="6"
          required
        ></BFormTextarea>
      </BFormGroup>

      <BFormGroup>
        <BDropdown text="Выберете тип награды" required>
          <BDropdownItem href="#" @click="setFileType(1)"
            >Загрузка картинки</BDropdownItem
          >
          <BDropdownItem href="#" @click="setFileType(2)"
            >Конструктор сертификата</BDropdownItem
          >
          <BDropdownItem href="#" @click="setFileType(3)"
            >Данные начислений</BDropdownItem
          >
        </BDropdown>
      </BFormGroup>

      <BFormGroup class="file-type">
        <UploadFile
          @image-download="formFile"
          v-if="fileType === 1"
          :fileType="fileType"
          required
        />

        <UploadSertificate
          @image-download="formFile"
          v-if="fileType === 2"
          :fileType="fileType"
          :sertificate="form.formFile"
          required
        />

        <FormUsers v-if="fileType === 3" required />
      </BFormGroup>

      <BFormGroup id="input-group-4" switches>
        <BFormCheckbox v-model="form.visibleToOthers" required>
          Отображать пользователям награды других участников
        </BFormCheckbox>
      </BFormGroup>

      <BButton type="submit" variant="primary">Сохранить</BButton>
    </BForm>
  </sidebar>
</template>

<script>
import UploadFile from "./types/UploadFile.vue";
import FormUsers from "./types/FormUsers.vue";
import UploadSertificate from "./types/UploadSertificate.vue";

export default {
  name: "EditAwardSidebar",
  components: {
    UploadFile,
    FormUsers,
    UploadSertificate,
  },
  props: {
    open: Boolean,
  },
  data() {
    return {
      fileType: null,
      checked: false,
      form: {
        name: "",
        discription: "",
        visibleToOthers: false,
        formFile: null,
        award_type_id: null,
      },
    };
  },
  computed: {},
  methods: {
    onSubmit() {
      this.formFile;
      this.$refs.newSertificateForm.reset();
    },
    setFileType(id) {
      console.log("id", id);
      this.fileType = id;
      this.form.award_type_id = id;
    },
    formFile(val) {
      console.log(val, "val");
      this.form.formFile = val;
    },
  },
};
</script>

<style lang="scss">
#edit-award-sidebar {
  .ui-sidebar__body {
    overflow: visible;
    display: flex;
    flex-direction: column;
  }
  .ui-sidebar__content {
    flex: 1;
    max-height: 100%;
    overflow: auto;
  }
  .file-type {
    margin-bottom: 20px;
  }
  .dropItems {
    display: flex;
    flex-direction: column;
  }
  .input-group {
    display: flex;
    flex-direction: column;
    width: 80%;
    margin: auto 0 !important;
  }
}
</style>
