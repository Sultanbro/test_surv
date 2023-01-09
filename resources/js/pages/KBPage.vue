<template>
  <div v-if="auth_user_id">
    <!-- PAGE -->
    <div class="kb-sections d-flex" v-if="activeBook === null">

      <!-- Левая часть -->
      <aside id="left-panel" class="lp">
        <div class="btn btn-search mb-3" @click="showSearch = true">
          <i class="fa fa-search"></i>
          <span>Искать в базе...</span>
        </div>

        <div class="btn btn-grey mb-3" v-if="activeBook === null" @click="openGlossary">
          <span>Глоссарий</span>
        </div>

        <div class="btn btn-grey mb-3" @click="showArchive = false" v-if="showArchive">
          <i class="fa fa-arrow-left"></i>
          <span>Выйти из архива</span>
        </div>

        <!-- Существующие разделы -->
        <div class="sections-wrap noscrollbar" v-if="!showArchive" :class="{ 'expand' : mode == 'read'}">


          <Draggable
            class="dragArea ml-0"
            tag="div"
            handle=".fa-bars"
            :list="books"
            :id="null"
            :group="{ name: 'g1' }"
            @start="startChangeOrder"
            @end="saveOrder"
          >
            <template v-for="(book, b_index) in books">
                  <div
                    class="section d-flex aic jcsb"
                    :id="book.id"
                    :key="book.id"
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
          </Draggable>


        </div>

        <!-- Архивные разделы -->
        <div class="sections-wrap noscrollbar" v-else>
          <template v-for="(book, b_index) in archived_books">
            <div
              class="section d-flex aic jcsb"

              v-if="can_edit"
              @click.stop="selectSection(book)"
            >
              <p>{{ book.title }}</p>
              <div class="section-btns">
                <i class="fa fa-trash-restore mr-1" @click.stop="restoreSection(b_index)"></i>
              </div>
            </div>
          </template>
        </div>

        <!-- Кнопки внизу сайдбара -->
        <div  v-if="mode == 'edit'">
          <div class="d-flex jscb" v-if="!showArchive">

            <div class="btn btn-grey w-full mr-1" @click="showCreate = true" v-if="can_edit">
              <i class="fa fa-plus"></i>
              <span>Добавить</span>
            </div>
            <div class="btn btn-grey" title="Архив" @click="getArchivedBooks" v-if="can_edit">
              <i class="fa fa-box"></i>
            </div>
          </div>
        </div>
      </aside>

      <!-- Правая часть -->
      <div class="rp" style="flex: 1 1 0%; padding-bottom: 50px;">
        <div class="hat">
          <div class="d-flex jsutify-content-between hat-top">
            <div class="bc">
              <a href="#">База знаний</a>
              <!---->
            </div>

            <!-- Кнопки на правом верхнем углу -->
            <div class="control-btns d-flex">
              <div class="mode_changer mr-2" v-if="can_edit">
                  <i class="fa fa-edit"
                    @click="toggleMode"
                    :class="{'active': mode == 'edit'}" />
                </div>
              <div class="mode_changer" v-if="can_edit">
                  <i class="fa fa-cogs"
                    @click="get_settings()" />
                </div>
            </div>
          </div>
          <div></div>
        </div>

        <!-- Глоссарий -->
        <div class="content mt-3">
            <Glossary
              v-if="show_glossary"
              :mode="mode"
            />
        </div>
      </div>
    </div>

    <!-- PAGE -->
    <div v-if="activeBook">
      <Booklist
        ref="booklist"
        :trees="trees"
        :can_edit="activeBook.access == 2 || can_edit"
        :parent_name="activeBook.title"
        :parent_id="activeBook.id"
        :show_page_id="show_page_id"
        :course_item_id="0"
        @back="back"
        @toggleMode="toggleMode"
        :mode="mode"
        :enable_url_manipulation="true"
        :auth_user_id="auth_user_id"
      />
    </div>




    <!-- Новый раздел -->
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


    <!-- Настройки раздела -->
    <Sidebar
      title="Настройки базы знаний"
      :open="showBookSettings"
      @close="showBookSettings = false"
      width="30%"
    >
      <label class="d-flex">
        <input
          type="checkbox"
          v-model="send_notification_after_edit"
          class="form- mb-2 mr-2"
        />
        <p>Отправлять уведомления сотрудникам об изменениях в базе знаний</p>
      </label>
      <label class="d-flex">
        <input
          type="checkbox"
          v-model="show_page_from_kb_everyday"
          class="form- mb-2 mr-2"
        />
        <p>Показывать одну из страниц базы знаний каждый день, после нажатия на кнопку "начать рабочий день"</p>
      </label>
      <label class="d-flex">
        <input
          type="checkbox"
          v-model="allow_save_kb_without_test"
          class="form- mb-2 mr-2"
        />
        <p>Разрешить вносить изменения без тестовых вопросов в разделах базы знаний</p>
      </label>

      <button class="btn btn-primary rounded m-auto" @click="save_settings()">
        <span>Сохранить</span>
      </button>

    </Sidebar>

    <!-- Редактирование раздела  -->
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

        <div :key="superselectKey">
          <p class="mb-2">Кто может видеть</p>
          <SuperSelect
            :values="who_can_read"
            class="w-full mb-4"
            :select_all_btn="true"
          />
          <p class="mb-2">Кто может редактировать</p>
          <SuperSelect
            :values="who_can_edit"
            class="w-full mb-4"
            :select_all_btn="true"
          />
        </div>
        <button class="btn btn-primary rounded m-auto" @click="updateSection">
          <span>Сохранить</span>
        </button>
      </div>

    </b-modal>

    <!-- Поиск -->
    <b-modal
      v-model="showSearch"
      title="Поиск"
      size="md"
      dialog-class="modal-search"
      hide-header
      hide-footer
    >

      <div>
        <div class="d-flex relative  mb-2">
          <input
            type="text"
            v-model="search.input"
            @keyup.enter="searchInput"
            placeholder="Поиск по всей базе..."
            class="form-control"
          />
          <button class="search-btn btn" v-if="search.input != ''" @click="searchInput">Искать</button>
        </div>

        <div class="s-content">
           <div class="sss" v-if="search.input.length >=3 && search.items.length == 0">
            <p>По запросу "{{ search.input }}" ничего не найдено.</p>
          </div>
         <div class="item" v-for="item in search.items" @click="selectSection(item.book, item.id)" >
           <p v-if="item.book != null" class="book">{{ item.book.title }}</p>
           <p>{{ item.title }}</p>
           <div class="text" v-html="item.text"></div>
         </div>
        </div>

      </div>

    </b-modal>

  </div>
</template>

<script>
import Draggable from 'vuedraggable'
import Glossary from '../components/Glossary.vue'
const Booklist = () => import(/* webpackChunkName: "Booklist" */ '@/pages/booklist') // база знаний разде
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
import SuperSelect from '@/components/SuperSelect' // with User ProfileGroup and Position

export default {
  name: 'KBPage',
  components: {
    Draggable,
    Glossary,
    Booklist,
    Sidebar,
    SuperSelect,
  },
  props: {
    auth_user_id: {
      type:Number
    },
   can_edit: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      books: [],
      mode: 'read',
      archived_books: [],
      trees: [],
      settings: null,
      section: 0,
      activeBook: null,
      showCreate: false,
      show_glossary: false,
      send_notification_after_edit: false,
      show_page_from_kb_everyday: false,
      allow_save_kb_without_test: false,
      showBookSettings: false,
      showArchive: false,
      showSearch: false,
      who_can_read: [],
      who_can_edit: [],
      showEdit: false,
      show_page_id: 0,
      superselectKey: 1,
      section_name: '',
      update_book: null,
      search: {
        input: '',
        items: []
      }
    };
  },
  watch: {
    auth_user_id(){
      this.init()
    }
  },

  created() {
    if(this.auth_user_id){
      this.init()
    }
  },

  methods: {
    init(){
      this.fetchData();

      // бывор группы
      const urlParams = new URLSearchParams(window.location.search);
      let section = urlParams.get('s');
      if(section) {
        this.selectSection({id: section})
      }
    },
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

    get_settings() {

      axios
        .post("/settings/get", {
          type: 'kb'
        })
        .then((response) => {
          this.send_notification_after_edit = response.data.settings.send_notification_after_edit;
          this.show_page_from_kb_everyday = response.data.settings.show_page_from_kb_everyday;
          this.allow_save_kb_without_test = response.data.settings.allow_save_kb_without_test;
          this.showBookSettings = true;
        })
        .catch((error) => {
          alert(error);
        });
    },

    save_settings() {
       axios
        .post("/settings/save", {
          type: 'kb',
          send_notification_after_edit: this.send_notification_after_edit,
          show_page_from_kb_everyday: this.show_page_from_kb_everyday,
          allow_save_kb_without_test: this.allow_save_kb_without_test,
        })
        .then((response) => {
          this.showBookSettings = false;
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
            this.$toast.info('Раздел не найден');
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
            this.$toast.success("Удалено");
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
            this.$toast.success("Восстановлен");
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
          this.superselectKey++;
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

          this.$toast.success("Раздел успешно создан!");
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

          this.$toast.success("Изменения сохранены!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    saveOrder(event) {
        console.log(event)
        axios.post('/kb/page/save-order', {
          id: event.item.id,
          order: event.newIndex, // oldIndex
          parent_id: null
        })
        .then(response => {
           this.$toast.success('Очередь сохранена');
        })
    },


    toggleMode() {
      this.mode = (this.mode == 'read') ? 'edit' : 'read';
    },

    startChangeOrder(event) {
        console.log(event)
    },

    openGlossary() {
      this.show_glossary = true;
    }

  },
};
</script>

<style lang="scss" scoped></style>
