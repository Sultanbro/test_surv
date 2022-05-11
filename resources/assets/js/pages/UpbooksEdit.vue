<template>
  <div>
    <div class="upbooks-page" v-if="activeBook === null">
      <div class="lp">
        <h1 class="page-title">Книги</h1>

        <div
          class="section d-flex aic jcsb"
          v-for="(cat, c_index) in categories"
          :key="cat.id"
          @click="selectCategory(c_index)"
        >
          <p>{{ cat.name }}</p>
          <i
            class="fa fa-trash"
            v-if="cat.id != 0 && access == 'edit'"
            @click.stop="deleteCat(c_index)"
          ></i>
        </div>

        <button class="btn-add" @click="modals.add_category.show = true" v-if="access == 'edit'">
          Добавить категорию
        </button>
      </div>

      <div class="cont">
        <div class="hat">
          <div class="d-flex jsutify-content-between hat-top">
            <div class="bc">
              <p v-if="activeCategory" class="mb-0">
                <b>{{ activeCategory.name }}</b>
              </p>
              <!---->
            </div>
            <div class="control-btns" v-if="activeCategory != null">
              <button
                v-if="access == 'edit'"
                class="btn btn-success"
                @click="modals.upload_book.show = true"
              >
                Добавить книгу
              </button>
            </div>
          </div>
          <div><!----></div>
        </div>

        <div class="d-flex flex-wrap p-3" v-if="activeCategory != null">
          <div
            class="box"
            v-for="(book, b_index) in activeCategory.books"
            :style="'background-image: url(' + (book.img != '' ? book.img : '/images/book_cover.jpg') + ')'"
            :key="book.id"
            @click="go(book)"
          >
            <div class="cover">
              <p class="title">{{ book.title }}</p>
              <p class="author">{{ book.author }}</p>
              <div class="buttons" v-if="access == 'edit'">
                <i
                  class="fa fa-trash mr-1"
                  @click.stop="deleteBook(b_index)"
                ></i>
                <i class="fa fa-edit" @click.stop="editBook(book)"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <page-upbooks-read v-else :activeBook="activeBook" mode="read" @back="back" />

    <b-modal
      v-model="modals.add_category.show"
      title="Новая категория книг"
      size="md"
      class="modalle"
      hide-footer
      hide-header
    >
      <input
        type="text"
        v-model="modals.add_category.name"
        placeholder="Название категории..."
        class="form-control mb-2"
      />
      <button class="btn btn-primary rounded m-auto" @click="createCategory">
        <span>Сохранить</span>
      </button>
    </b-modal>

    <b-modal
      v-model="modals.upload_book.show"
      title="Загрузить книгу"
      size="md"
      class="modalle"
      hide-footer
      hide-header
    >
      <upload-files
        :token="token"
        type="file"
        :file_types="['pdf']"
        @onupload="onupload"
      />

      <div v-if="modals.upload_book.file">
        <input
          type="text"
          v-model="modals.upload_book.file.model.title"
          placeholder="Название книги..."
          class="form-control mt-2"
        />
        <input
          type="text"
          v-model="modals.upload_book.file.model.author"
          placeholder="Название автора..."
          class="form-control mt-2 mb-2"
        />
        <button class="btn btn-primary rounded m-auto" @click="saveBook">
          <span>Сохранить</span>
        </button>
      </div>
    </b-modal>

    <b-modal
      v-model="modals.edit_book.show"
      title="Редактировать книгу"
      size="xl"
      class="modalle"
      hide-footer
      hide-header
    >
      <div v-if="modals.edit_book.item != null" class="p-3">
        <input
          type="text"
          v-model="modals.edit_book.item.title"
          placeholder="Название книги..."
          class="form-control mb-2"
        />
        <input
          type="text"
          v-model="modals.edit_book.item.author"
          placeholder="Название автора..."
          class="form-control mb-2"
        />

        <div class="tests mb-2" v-if="modals.edit_book.tests.length > 0">
          <div class="row">
            <div class="col-3">
              <b>Страница книги</b>
            </div>
            <div class="col-9">
              <b>Вопросы</b>
            </div>
          </div>

          <div
            class="test mb-3"
            v-for="(test, block) in modals.edit_book.tests"
            :key="block"
          >
            <div class="row">
              <div class="col-3">
                <input
                  type="number"
                  min="1"
                  max="9999"
                  v-model="test.page"
                  placeholder="Страница"
                  class="form-control mb-2"
                />
              </div>
              <div class="col-9">
                <questions
                  :questions="test.questions"
                  :id="modals.edit_book.item.id"
                  type="book"
                  mode="edit"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex">
          <button class="btn btn-success mr-2 rounded" @click="saveTests">
            <span>Сохранить</span>
          </button>
          <button class="btn rounded" @click="addTest">
            <span>Добавить тест</span>
          </button>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import Questions from "./Questions.vue";
export default {
  components: { Questions },
  name: "UpbookEdit",
  props: {
    token: String,
    access: String // read edit
  },
  data() {
    return {
      activeBook: null,
      activeCategory: null,
      categories: [],
      modals: {
        add_category: {
          show: false,
          name: "",
        },
        upload_book: {
          show: false,
          file: null,
        },
        edit_book: {
          show: false,
          item: null,
          tests: [],
        },
      },
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    selectCategory(index) {
      this.activeCategory = this.categories[index];
    },

    fetchData() {
      let loader = this.$loading.show();

      axios
        .get("/admin/upbooks/get", {})
        .then((response) => {
          this.categories = response.data.categories;
          if (this.categories.length > 0) {
            this.activeCategory = this.categories[0];
          }
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    go(book) {
      this.activeBook = book;
    },

    back() {
      this.activeBook = null;
    },

    deleteCat(i) {
      if (confirm("Вы уверены удалить категорию книг?")) {
        
        let loader = this.$loading.show();

        axios
          .post("/admin/upbooks/category/delete", {
            id: this.categories[i].id
          })
          .then((response) => {
            this.$message.success("Категория успешно удалена!");
            this.categories.splice(i,1)
            loader.hide();
          })
          .catch((error) => {
            loader.hide();
            alert(error);
          });
        }
    },

    createCategory() {
      if (this.modals.add_category.name.length <= 2) {
        alert("Слишком короткое название!");
        return "";
      }

      let loader = this.$loading.show();

      axios
        .post("/admin/upbooks/category/create", {
          name: this.modals.add_category.name,
        })
        .then((response) => {
          this.modals.add_category.show = false;
          this.modals.add_category.name = "";

          this.categories.push({
            id: response.data.id,
            name: response.data.name,
            books: [],
          });

          this.$message.success("Категория успешно создана!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    create_book() {},

    onupload(item) {
      console.log("onupload");
      console.log(item);
      this.modals.upload_book.file = item;
    },

    deleteBook(i) {
      if (confirm("Вы уверены удалить книгу?")) {
      }
    },

    editBook(book) {
      let loader = this.$loading.show();

      this.modals.edit_book.show = true;
      this.modals.edit_book.item = book;

      axios
        .post("/admin/upbooks/tests/get", {
          id: book.id,
        })
        .then((response) => {
          this.modals.edit_book.tests = response.data.tests;
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    saveBook() {
      let loader = this.$loading.show();

      axios
        .post("/admin/upbooks/save", {
          book: this.modals.upload_book.file.model,
          cat_id: this.activeCategory.id,
        })
        .then((response) => {
          this.activeCategory.books.push(this.modals.upload_book.file.model);

          this.modals.upload_book.show = false;
          this.modals.upload_book.file = null;

          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    addTest() {
      this.modals.edit_book.tests.push({
        page: 1,
        pages: 1,
        questions: [],
      });
    },

    saveTests() {
      let loader = this.$loading.show();

      axios
        .post("/admin/upbooks/update", {
          book: this.modals.edit_book.item,
          tests: this.modals.edit_book.tests,
          cat_id: this.activeCategory.id,
        })
        .then((response) => {
          this.modals.edit_book.show = false;
          this.modals.edit_book.item = null;
          this.modals.edit_book.tests = [];

          this.$message.success("Сохранено");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    updateBook() {
      let loader = this.$loading.show();

      axios
        .post("/admin/upbooks/update", {
          book: modals.edit_book.item,
          cat_id: this.activeCategory.id,
        })
        .then((response) => {
          this.modals.edit_book.show = false;
          this.modals.edit_book.item = null;

          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },
  },
};
</script>
