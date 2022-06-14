<template>
  <div>
    <!-- PAGE -->
    <div class="kb-sections d-flex" v-if="activeBook === null">
      <aside id="left-panel" class="lp">
        <div class="btn btn-search mb-3" @click="showSearch = true">
          <i class="fa fa-search"></i>
          <span>Искать в базе...</span>
        </div>
        <div class="btn btn-grey mb-3" @click="showArchive = false" v-if="showArchive">
          <i class="fa fa-arrow-left"></i>
          <span>Выйти из архива</span>
        </div>
        
        
        <div class="sections-wrap noscrollbar" v-if="!showArchive"> 


          <draggable 
            class="dragArea ml-0" 
            tag="div"
            handle=".fa-bars"
            :list="books"
            :group="{ name: 'g1' }"
            @end="saveOrder">
            <template v-for="(book, b_index) in books">
                  <div
                    class="section d-flex aic jcsb"
                  
                    :id="book.id"
                    @click.stop="selectSection(book)"
                  >
                    <div class="d-flex aic"  >
                      <i class="fa fa-bars mover mr-2" v-if="mode == 'edit'"></i>
                      <p>{{ book.title }}</p>
                    </div>
                    
                    <div class="section-btns"  v-if="mode == 'edit'">
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

        <div  v-if="mode == 'edit'">
          <div class="d-flex jscb" v-if="!showArchive">
            <div class="btn btn-grey w-full mr-1" @click="getArchivedBooks" v-if="[5,18,157,84].includes(auth_user_id)">
              <i class="fa fa-trash"></i>
              <span>Архив</span>
            </div>
            <div class="btn btn-grey w-full" @click="showCreate = true" v-if="[5,18,157,84].includes(auth_user_id)">
              <i class="fa fa-plus"></i>
              <span>Добавить</span>
            </div>
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
            <div class="control-btns">
              <div class="mode_changer" v-if="can_edit">
                  <i class="fa fa-edit"
                    @click="toggleMode"
                    :class="{'active': mode == 'edit'}" />
                </div>
            </div>
          </div>
          <div><!----></div>
        </div>
        <div class="content mt-3"></div>
      </div>
    </div>

    <!-- PAGE -->
    <div v-else>
      <booklist 
        ref="booklist"
        :trees="trees" 
        :can_edit="activeBook.access == 2 || can_edit"
        :parent_name="activeBook.title" 
        :parent_id="activeBook.id"
        :show_page_id="show_page_id"
        @back="back" 
        @toggleMode="toggleMode" 
        :mode="activeBook.access == 2 || can_edit ? 'edit' : 'read'"
        :auth_user_id="auth_user_id" />
    </div>





    <b-modal
      v-model="showCreate"
      title="Новый раздел"
      size="md"
      class="modalle"
      hide-footer
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


    <b-modal
      v-model="showEdit"
      title="Редактирование раздела"
      size="md"
      dialog-class="modallxe"
      hide-footer
    >

      <div v-if="update_book != null">
        <input
          type="text"
          v-model="update_book.title"
          placeholder="Название раздела..."
          class="form-control mb-2"
        />

        <div>
          <p class="mb-2">Кто может видеть</p>
          <superselect 
            :values="who_can_read"
            class="w-full mb-4" 
            :key="1"
            :select_all_btn="true" /> 
          <p class="mb-2">Кто может редактировать</p>
          <superselect
            :values="who_can_edit" 
            class="w-full mb-4" 
            :key="2"
            :select_all_btn="true" /> 
        </div>
        <button class="btn btn-primary rounded m-auto" @click="updateSection">
          <span>Сохранить</span>
        </button>
      </div>
      
    </b-modal>

    <b-modal
      v-model="showSearch"
      title="Поиск"
      size="md"
      dialog-class="modal-search"
      hide-header
      hide-footer
    >

      <div>
        <input
          type="text"
          v-model="search.input"
          @keyup="searchInput"
          placeholder="Поиск по всей базе..."
          class="form-control mb-2"
        />

        <div class="s-content">
         <div class="item" v-for="item in search.items" @click="selectSection(item, item.id)" >
           <p>{{ item.title }}</p>
           <div class="text" v-html="item.text"></div>
         </div>
        </div>
        
      </div>
      
    </b-modal>
  </div>
</template>

<script>
export default {
  name: "KBPage",
  props: {
    auth_user_id: {
      type:Number
    },
   can_edit: {
      type: Boolean,
      default: false
    } 
  },
  data: function() {
    return {
      books: [],
      mode: 'read',
      archived_books: [],
      trees: [],
      section: 0,
      activeBook: null,
      showCreate: false,
      showArchive: false,
      showSearch: false,
      who_can_read: [],
      who_can_edit: [],
      showEdit: false,
      show_page_id: 0,
      section_name: '',
      update_book: null,
      search: {
        input: '',
        items: []
      }
    };
  },
  watch: {},

  created() {
    if(this.can_edit) {
      this.mode = 'edit';
    } 
    
    this.fetchData();

    // бывор группы
    const urlParams = new URLSearchParams(window.location.search);
    let section = urlParams.get('s');
    if(section) {
      console.log(section)
      this.selectSection({id: section})
    }
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
    
    selectSection(book, page_id = 0) {
      axios
        .post("kb/tree", {
          id: book.id,
        })
        .then((response) => {
          if(response.data.error) {
            this.$message.info('Раздел не найден');
          }
          this.trees = response.data.trees;
          this.activeBook = response.data.book;
          this.show_page_id = page_id;
          this.showSearch = false;
          this.search.input = '';
          this.search.items = [];
          // change URL
          const urlParams = new URLSearchParams(window.location.search);
          let b = urlParams.get('b');
          let uri = "/kb?s=" + book.id;
          if(b) uri+= '&b=' + b;
          window.history.replaceState({}, "База знаний", uri);

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
      window.history.replaceState({ id: "100" }, "База знаний", "/kb");
    },
    
    searchInput() {
      if(this.search.input.length <= 2) return null;
      
      axios
        .post("kb/search", {
          text: this.search.input,
        })
        .then((response) => {
         
          this.search.items = response.data.items;
          this.emphasizeTexts();

        })
        .catch((error) => {
          alert(error);
        });
    },

    emphasizeTexts() {
      this.search.items.forEach(item => {
         item.text = item.text.replace(new RegExp(this.search.input,"gi"), "<b>" + this.search.input +  "</b>");
      });
    },

    editAccess(book) {


      this.showEdit = true;
      this.update_book = book;
      console.log(book)
      axios
        .post("/kb/page/get-access", {
          id: book.id,
        })
        .then((response) => {
          this.who_can_edit = response.data.who_can_edit;
          this.who_can_read = response.data.who_can_read;
        })
        .catch((error) => {
          alert(error);
        });
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

    updateSection() {
      if (this.update_book.title.length <= 2) {
        alert("Слишком короткое название!");
        return "";
      }

      let loader = this.$loading.show();

      axios 
        .post("/kb/page/update-section", {
          title: this.update_book.title,
          who_can_read: this.who_can_read,
          who_can_edit: this.who_can_edit,
          id: this.update_book.id,
        })
        .then((response) => {
          this.showEdit = false;
          let index = this.books.findIndex(b => b.id == this.update_book.id);

          if(index != -1) {
            this.books[index].title = this.update_book.title;
          }

          this.update_book = null;
          this.who_can_read = [];
          this.who_can_edit = [];

          this.$message.success("Изменения сохранены!"); 
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


    toggleMode() {
      this.mode = (this.mode == 'read') ? 'edit' : 'read';
    }


    
  },
};
</script>

<style lang="scss" scoped></style>
