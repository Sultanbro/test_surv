<template>
  <div id="awards-page">
    <BButton variant="success" class="mb-2" @click="addAwardButtonClickHandler"
      >Создать награду</BButton
    >

    <BTableSimple
      id="awards-table"
      class="mb-3"
      bordered
      hover
      small
      @row-clicked="rowClickedHandler"
    >
      <BThead>
        <BTr>
          <BTh>№</BTh>
          <BTh>Название</BTh>
          <BTh>Описание</BTh>
          <BTh>Тип</BTh>
          <BTh>Дата создания</BTh>
          <BTh>Постановщик</BTh>
        </BTr>
      </BThead>
      <BTbody>
        <BTr v-for="(item, key, id) in tableItems" :key="key">
          <BTd>{{ id }}</BTd>
          <BTd>{{ item.name }}</BTd>
          <BTd>{{ item.discriprion }}</BTd>
          <BTd>{{ item.type }}</BTd>
          <BTd>{{ item.date }}</BTd>
          <BTd>{{ item.user }}</BTd>
        </BTr>
      </BTbody>
    </BTableSimple>

    <EditAwardSidebar
      v-if="showEditAwardSidebar"
      :open.sync="showEditAwardSidebar"
    />
  </div>
</template>

<script>
import EditAwardSidebar from "./EditAwardSidebar(delete).vue";

export default {
  name: "Awards",
  components: { 
    EditAwardSidebar, 
  },
  data() {
    return {
      showEditAwardSidebar: false,
      types: [
        {
          id: 1,
          name: "Сертификаты",
        },
        {
          id: 2,
          name: "Номинаций",
        },
      ],
      tableItems: [
        {
          id: 40,
          award_type_id: 2,
          course_id: null,
          format: "pdf",
          path: "awards/633531d1d84a1_ad26af00bb848ba7e08186f675db0b01.pdf",
          icon: null,
          deleted_at: null,
          created_at: "2022-09-29T05:49:06.000000Z",
          updated_at: "2022-09-29T05:49:06.000000Z",
        },
        {
          name: "Сертификаты",
          discriprion: "Сертификаты по полному окончанию курса",
          files: 0,
          type: "Картинка",
          date: "06.10.2022",
          user: "Адиль Каримов",
        },
        {
          name: "Номинации",
          discriprion: "Сертификаты по полному окончанию курса",
          files: 0,
          type: "Картинка",
          date: "06.10.2022",
          user: "Адиль Каримов",
        },
        {
          name: "Красавчики",
          discriprion: "Сертификаты по полному окончанию курса",
          files: 0,
          type: "Картинка",
          date: "06.10.2022",
          user: "Адиль Каримов",
        },
        {
          name: "Поездки",
          discriprion: "Сертификаты по полному окончанию курса",
          files: 0,
          type: "Картинка",
          date: "06.10.2022",
          user: "Адиль Каримов",
        },
      ],
      tableFields: [
        { key: "name", label: "Название" },
        { key: "files", label: "Шаблоны" },
      ],
    };
  },
  methods: {
    rowClickedHandler(data) {
      this.showEditAwardSidebar = true;
    },
    addAwardButtonClickHandler() {
      this.showEditAwardSidebar = true;
    },
  },
};
</script>

<style lang="scss">
#awards-page {
  #awards-table {
    tbody {
      cursor: pointer;
    }
  }
}
</style>
