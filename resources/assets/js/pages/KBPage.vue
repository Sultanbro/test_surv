<template>
  <div>
    <!-- PAGE -->
    <div class="kb-sections d-flex" v-if="activeBook === null">
      <aside id="left-panel" class="lp">
        <div class="btn btn-search mb-3">
          <i class="fa fa-search"></i>
          <span>Искать в базе...</span>
        </div>
        <div class="btn btn-grey mb-3" @click="showArchive = false" v-if="showArchive">
          <i class="fa fa-arrow-left"></i>
          <span>Выйти из архива</span>
        </div>
        
        
        <div class="sections-wrap noscrollbar" v-if="!showArchive"> 


          <draggable 
            class="dragArea" 
            tag="div"
            handle=".fa-bars"
            :list="books"
            :group="{ name: 'g1' }"
            @end="saveOrder">
            <template v-for="(book, b_index) in books">
                  <div
                    class="section d-flex aic jcsb"
                    :key="book.id"
                    @click.stop="selectSection(book)"
                  >
                    <i class="fa fa-bars mover"></i>
                    <p>{{ book.title }}</p>
                    <div class="section-btns">
                      <i class="fa fa-trash mr-1" @click.stop="deleteSection(b_index)"></i>
                      <i class="fa fa-cogs " @click.stop="editAccess(book)"></i>
                    </div>
                  </div>
            </template>
          </draggable>

          
        </div>
        
        <div class="sections-wrap noscrollbar" v-else>
          <template v-for="(book, b_index) in archived_books">
            <div
              class="section d-flex aic jcsb"
              :key="book.id"
              v-if="[5,18,157,84].includes(auth_user_id)"
              @click.stop="selectSection(book)"
            >
              <p>{{ book.title }}</p>
              <div class="section-btns">
                <i class="fa fa-trash mr-1" @click.stop="restoreSection(b_index)"></i>
                <i class="fa fa-cogs " @click.stop="editAccess(book)"></i>
              </div>
            </div>
          </template>
        </div>


        <div class="d-flex">
          <div class="btn btn-grey" @click="getArchivedBooks" v-if="[5,18,157,84].includes(auth_user_id)">
            <i class="fa fa-trash"></i>
            <span>Архив</span>
          </div>
          <div class="btn btn-grey" @click="showCreate = true" v-if="[5,18,157,84].includes(auth_user_id)">
            <i class="fa fa-plus"></i>
            <span>Добавить</span>
          </div>
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
      <booklist :trees="trees" :parent_name="activeBook.title" :parent_id="activeBook.id" @back="back" :auth_user_id="auth_user_id" />
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
      archived_books: [],
      trees: [],
      section: 0,
      activeBook: null,
      showCreate: false,
      showArchive: false,
      section_name: '',
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
      if (confirm("Вы уверены что хотите архивировать раздел?")) {
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

    restoreSection(i) {
      if (confirm("Вы уверены что хотите восстановить раздел?")) {
        axios
          .post("/kb/page/restore-section", {
            id: this.archived_books[i].id
          })
          .then((response) => {
            this.books.push(this.archived_books[i]);
            this.archived_books.splice(i, 1);
            this.$message.success("Восстановлен");
          });
      }
    },

    back() {
      this.activeBook = null;
    },
    
    editAccess(book) {
      alert('Редактирование доступа');
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
    },

    getArchivedBooks() {
      let loader = this.$loading.show();

      axios
        .get("/kb/get-archived")
        .then((response) => {
         
          this.archived_books = response.data.books
          this.showArchive = true
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    saveOrder(event) {
        axios.post('/kb/page/save-order', {
          id: event.item.id,
          order: event.newIndex, // oldIndex
          parent_id: null
        })
        .then(response => {
           this.$message.success('Очередь сохранена');
        })
    },





    
  },
};
</script>

<style lang="scss" scoped></style>
