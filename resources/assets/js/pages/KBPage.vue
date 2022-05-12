<template>
  <div>
    <!-- PAGE -->
    <div class="kb-sections d-flex" v-if="activeBook === null">
      <aside id="left-panel" class="lp">
        <div class="btn btn-search mb-3">
          <i class="fa fa-search"></i>
          <span>Искать в базе...</span>
        </div>
        <template v-for="(book, b_index) in books">
          <div
            class="section d-flex aic jcsb"
            :key="book.id"
            @click.stop="selectSection(book)"
          >
            <p>{{ book.title }}</p>
            <div class="section-btns">
              <i class="fa fa-trash mr-1" @click.stop="deleteSection(b_index)"></i>
              <i class="fa fa-cogs " @click.stop="editAccess(book)"></i>
            </div>
          </div>
        </template>

        <div class="btn-add" @click="showCreate = true" v-if="[5,18,157,84].includes(auth_user_id)">
          <i class="fa fa-plus"></i>
          <span>Добавить раздел</span>
        </div>
      </aside>
      <div class="rp" style="flex: 1 1 0%; padding-bottom: 50px;">
        <div class="hat">
          <div class="d-flex jsutify-content-between hat-top">
            <div class="bc">
              <a href="#">База знаний</a>
              <!---->
            </div>
            <div class="control-btns"><!----></div>
          </div>
          <div><!----></div>
        </div>
        <div class="content mt-3"></div>
      </div>
    </div>

    <!-- PAGE -->
    <div v-else>
      <booklist :trees="trees" :parent_id="activeBook.id" @back="back" :auth_user_id="auth_user_id" />
    </div>





    <b-modal
      v-model="showCreate"
      title="Новый раздел"
      size="md"
      class="modalle"
      hide-footer
      hide-header
    >
      <input
        type="text"
        v-model="section_name"
        placeholder="Название раздела..."
        class="form-control mb-2"
      />
      <button class="btn btn-primary rounded m-auto" @click="addSection">
        <span>Сохранить</span>
      </button>
    </b-modal>


  </div>
</template>

<script>
export default {
  name: "KBPage",
  props: {
    auth_user_id: Number 
  },
  data: function() {
    return {
      books: [],
      trees: [],
      section: 0,
      activeBook: null,
      showCreate: false,
      section_name: ''
    };
  },
  watch: {},

  created() {
    this.fetchData();
  },

  methods: {
    fetchData() {
      axios
        .get("/kb/get", {})
        .then((response) => {
          this.books = response.data.books;
        })
        .catch((error) => {
          alert(error);
        });
    },

    selectSection(book) {
      axios
        .post("kb/tree", {
          id: book.id,
        })
        .then((response) => {
          this.trees = response.data.trees;
          this.activeBook = book;
        })
        .catch((error) => {
          alert(error);
        });
    },

    deleteSection(i) {
      if (confirm("Вы уверены что хотите удалить раздел?")) {
        axios
          .post("/kb/page/delete-section", {
            id: this.books[i].id
          })
          .then((response) => {
            this.books.splice(i, 1);
            this.$message.success("Удалено");
          });
      }
    },

    back() {
      this.activeBook = null;
    },

    editAccess(book) {
      alert(book.title);
    },

    addSection() {
      if (this.section_name.length <= 2) {
        alert("Слишком короткое название!");
        return "";
      }

      let loader = this.$loading.show();

      axios
        .post("/kb/page/add-section", {
          name: this.section_name,
        })
        .then((response) => {
          this.showCreate = false;
          this.section_name = "";

          this.books.push(response.data);

          this.$message.success("Раздел успешно создан!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    }
  },
};
</script>

<style lang="scss" scoped></style>
