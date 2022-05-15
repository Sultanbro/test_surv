<template>
  <div class="d-flex">
    <aside id="left-panel" class="lp">
      <div class="btn btn-search mb-3">
        <i class="fa fa-search"></i>
        <span>Искать в базе...</span>
      </div>
      <div class="btn btn-grey mb-3" @click="$emit('back')">
        <i class="fa fa-arrow-left"></i>
        <span>Вернуться к разделам</span>
      </div>

      <div class="kb-wrap noscrollbar">

        <div class="chapter mb-3">
          <div class="d-flex">
            <span class="font-16 font-bold">{{ parent_name }}</span>
            <div class="chapter-btns">
              <i class="fa fa-plus" @click="addPageToTree"></i> 
            </div>
          </div>
        </div>

        <nested-draggable
          :tasks="tree"
          :auth_user_id="auth_user_id"
          :open="true"
          @showPage="showPage"
          @addPage="addPage"
          :parent_id="parent_id"
        />
      </div>
      

      <!-- <div class="btn-add" @click="addPage" v-if="[5,18,157,84].includes(auth_user_id)">
        <i class="fa fa-plus"></i>
        <span>Добавить страницу</span>
      </div> -->

          
    </aside>
    <!-- /#left-panel -->


    <!-- Right Panel -->

    <div class="rp" style="flex: 1;padding-bottom: 0px;flex: 1 1 0%;height: 100vh;overflow-y: auto;">
      <div class="hat">
        <div class="d-flex jsutify-content-between hat-top">
          <div class="bc">
            <a href="#">База знаний</a>
            <template v-for="(bc, bc_index) in breadcrumbs">
              <i class="fa fa-chevron-right" :key="bc_index"></i>
              <a href="#" @click="showPage(bc.id)" :key="bc_index">{{ bc.title }}</a>
            </template>
          </div>

          <div class="control-btns" v-if="[5,18,157,84].includes(auth_user_id)">
            <div class="d-flex justify-content-end" :asd="auth_user_id" v-if="activesbook != null">
              <input
                type="text"
                :ref="'mylink' + activesbook.id"
                class="hider"
              />

            <button
                v-if="activesbook != null && activesbook.parent_id == null"
                class="form-control btn-action btn-medium ml-2"
                @click="showPermissionModal = true"
              >
                <i class="fa fa-cogs"></i>
            </button>

            <template v-if="edit_actives_book">
               <button
                class="form-control btn-action btn-medium ml-2"
                @click="showImageModal = true"
              >
                <i class="far fa-image"></i>
              </button>


              <button
                class="form-control btn-action btn-medium ml-2"
                @click="showAudioModal = true"
              >
                <i class="fas fa-volume-up"></i>
              </button>
              <button
                class="form-control btn-action btn-medium ml-2"
                @click="showActionModal = true"
              >
                Действие
              </button>

              
              
              <button
                class="form-control btn-delete btn-medium ml-2"
                @click="deletePage"
              >
                Удалить
              </button>

              <button
                class="form-control btn-save btn-medium ml-2"
                @click="saveServer"
              >
                Сохранить
              </button>
            </template>

            <template v-else>
              <button
                class="form-control btn-action btn-medium ml-2"
                title="Поделиться ссылкой"
                @click="copyLink(activesbook)"
              >
                <i class="fa fa-clone"></i>
              </button>
              
              <button
                class="form-control btn-danger btn-medium ml-2"
                @click="deletePage"
              >
                  <i class="fa fa-trash"></i>
              </button>

              <button
                class="form-control btn-save btn-medium ml-2"
                @click="edit_actives_book = true"
              >
                Редактировать
              </button>
            </template>
             

              
              
              
            </div>
          </div>
        </div>

        <div>
          <template v-if="activesbook != null">
            <input
              type="text"
              class="article_title"
              v-model="activesbook.title"
            />
          </template>
        </div>
      </div>

      <div class="content mt-3">
        <template v-if="activesbook != null && edit_actives_book">
          <editor
            @onKeyUp="editorSave"
            @onChange="editorSave"
            v-model="activesbook.text"
            api-key="dexbevckx3qi9h3r21bk8e8bypxuef909ox6o1kr91be6uv1"
            :init="{
              images_upload_url: '/upload/images/',
              automatic_uploads: false,
              height: editorHeight,
              resize: true,
              autosave_ask_before_unload: false,
              powerpaste_allow_local_images: true,
              browser_spellcheck: true,
              contextmenu: true,
              spellchecker_whitelist: ['Ephox', 'Moxiecode'],
              language: 'ru',
              convert_urls: false,
              relative_urls: false,
              language_url: '/static/langs/ru.js',
              content_css: '/static/css/mycontent.css',
              fontsize_formats:
                '8pt 10pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 20pt 22pt 24pt 26pt 28pt 30pt 36pt',
              lineheight_formats:
                '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt',
              plugins: [
                ' advlist anchor autolink codesample colorpicker fullscreen help image imagetools ',
                ' lists link media noneditable  preview',
                ' searchreplace table template textcolor  visualblocks wordcount ',
              ],
              menubar: false, //'file edit view insert format tools table help',
              toolbar:
                'styleselect  | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | fullscreen  preview |  media  link | undo redo',
              toolbar_sticky: true,
              content_style:
                '.lineheight20px { line-height: 20px; }' +
                '.lineheight22px { line-height: 22px; }' +
                '.lineheight24px { line-height: 24px; }' +
                '.lineheight26px { line-height: 26px; }' +
                '.lineheight28px { line-height: 28px; }' +
                '.lineheight30px { line-height: 30px; }' +
                '.lineheight32px { line-height: 32px; }' +
                '.lineheight34px { line-height: 34px; }' +
                '.lineheight36px { line-height: 36px; }' +
                '.lineheight38px { line-height: 38px; }' +
                '.lineheight40px { line-height: 40px; }' +
                'body { padding: 20px;max-width: 960px;margin: 0 auto; }' +
                '.tablerow1 { background-color: #D3D3D3; }',
              formats: {
                lineheight20px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight20px',
                },
                lineheight22px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight22px',
                },
                lineheight24px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight24px',
                },
                lineheight26px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight26px',
                },
                lineheight28px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight20px',
                },
                lineheight30px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight30px',
                },
                lineheight32px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight32px',
                },
                lineheight34px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight34px',
                },
                lineheight36px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight36px',
                },
                lineheight38px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight38px',
                },
                lineheight40px: {
                  selector:
                    'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                  classes: 'lineheight40px',
                },
              },
              style_formats: [
                { title: 'lineheight20px', format: 'lineheight20px' },
                { title: 'lineheight22px', format: 'lineheight22px' },
                { title: 'lineheight24px', format: 'lineheight24px' },
                { title: 'lineheight26px', format: 'lineheight26px' },
                { title: 'lineheight28px', format: 'lineheight28px' },
                { title: 'lineheight30px', format: 'lineheight30px' },
                { title: 'lineheight32px', format: 'lineheight32px' },
                { title: 'lineheight34px', format: 'lineheight34px' },
                { title: 'lineheight36px', format: 'lineheight36px' },
                { title: 'lineheight38px', format: 'lineheight38px' },
                { title: 'lineheight40px', format: 'lineheight40px' },
              ],
              content_css: [
                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
              ],
            }"
          ></editor>

      

          <div v-if="loader" class="col-md-12 bg">
            <div class="loader" id="loader-2">
              <span></span>
              <span></span>
              <span></span>
            </div>
          </div>

        
        </template>

        <template  v-if="activesbook != null && !edit_actives_book">
          <div class="book_page">
            <div class="author d-flex aic mb-2">
              <img src="/images/avatar.png" alt="avatar icon">
              <div class="text">
                <p class="name">{{ activesbook.author }}</p>
                <p class="edited">{{ activesbook.edited_at }}</p>
              </div>
            </div>
            <div class="bp-text" v-html="activesbook.text">
            
            </div>

            <questions
                  v-if="[5,18,157,84].includes(auth_user_id)"
                  :questions="activesbook.questions"
                  :id="activesbook.id"
                  type="kb"
                  mode="edit"
                />
            <questions
                  v-else
                  :questions="activesbook.questions"
                  :id="activesbook.id"
                  type="kb"
                  mode="read"
                />
              <div class="pb-5"></div> 
          </div>
        </template>
      </div>

    </div>
    <!-- .content -->

    <!-- Right Panel -->

    <b-modal v-model="showImageModal" title="Загрузить изображение">
              <form
                @submit.prevent="submit"
                action="/upload/images/"
                enctype="multipart/form-data"
                method="post"
                style=" max-width: 300px;margin: 0 auto;"
              >
                <div class="form-group">
                  <div class="custom-file">
                    <input
                      type="file"
                      class="custom-file-input"
                      id="customFile"
                      @change="onAttachmentChange"
                      accept="image/*"
                    />
                    <label class="custom-file-label" for="customFile"
                      >Выберите файл</label
                    >
                  </div>
                </div>
              </form>
    </b-modal>

    <b-modal v-model="showAudioModal" title="Загрузить аудио">
         <form
                @submit.prevent="submit"
                action="/upload/audio/"
                enctype="multipart/form-data"
                method="post"
                style=" max-width: 300px;margin: 0 auto;"
              >
                <div class="form-group">
                  <div class="custom-file">
                    <input
                      type="file"
                      class="custom-file-input"
                      id="customFile"
                      @change="onAttachmentChangeaudio"
                      accept="audio/mp3"
                    />
                    <label class="custom-file-label" for="customFile"
                      >Выберите файл</label
                    >
                  </div>
                </div>
              </form>
    </b-modal>


    <b-modal v-model="showPermissionModal" :title="'Настройка доступа к разделу'">
      <div v-if="activesbook != null">{{ activesbook.title}} </div>
       Пока не сделано
    </b-modal>

    <b-modal v-model="showActionModal" title="Действие">
      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
          <a
            class="nav-link active"
            id="pills-home-tab"
            data-toggle="pill"
            href="#pills-home"
            role="tab"
            aria-controls="pills-home"
            aria-selected="true"
            >Копирование (перенос)</a
          >
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            id="pills-profile-tab"
            data-toggle="pill"
            href="#pills-profile"
            role="tab"
            aria-controls="pills-profile"
            aria-selected="false"
            >Поиск схожих</a
          >
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div
          class="tab-pane fade show active"
          id="pills-home"
          role="tabpanel"
          aria-labelledby="pills-home-tab"
        >
          <div class="form-group">
            <label for="element1">Выберите действие</label>
            <select class="form-control" v-model="delo" id="element1">
              <option value="0">Копировать</option>
              <option value="1">Перенести</option>
            </select>
          </div>

          <selectgroup
            :tree="tree"
            :selecttree="selecttree"
            @select="select"
          ></selectgroup>
          <br />
          <button
            type="button"
            @click="copyes"
            class="btn btn-secondary"
            data-dismiss="modal"
          >
            ОК
          </button>
        </div>
        <div
          class="tab-pane fade"
          id="pills-profile"
          role="tabpanel"
          aria-labelledby="pills-profile-tab"
        >
          <li v-if="seatchbooks != null" v-for="book in seatchbooks">
            <input
              type="checkbox"
              :id="book.id"
              :value="book.id"
              v-model="checkedNames"
            />
            <label :for="book.id">({{ book.namecat }}) {{ book.title }}</label>
          </li>

          <button type="button" @click="searchitem" class="btn btn-secondary">
            Найти похожие книги
          </button>
          <br /><br />
          <button
            v-if="checkedNames != ''"
            type="button"
            @click="passte"
            class="btn btn-secondary"
            data-dismiss="modal"
          >
            Заменить в выделенных
          </button>
        </div>
      </div>
    </b-modal>

    <div
      class="modal fade"
      id="smallmodal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="smallmodalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="smallmodalLabel">Доступ</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <template v-if="actives != null && actives.parent_cat_id == null">
              <input v-model="actives.login" />
              <input v-model="actives.password" />
            </template>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click="savepass"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              ОК
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="perenos"
      tabindex="-1"
      role="dialog"
      aria-labelledby="perenoslabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="perenoslabel">Перенести категорию</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <selectgroup
              :perenos="1"
              :tree="tree"
              :selecttree="selecttree"
              @select="select"
            ></selectgroup>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click="movecatt"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Перенести
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import nestedDraggable from "../components/nested";
export default { 
  name: "booklist",
  props: ["trees", 'parent_id', 'auth_user_id', 'parent_name'],
  components: { 
    nestedDraggable,
  },
  data() {
    return {
      loader: false,
      delo: 0,
      showActionModal: false,
      showImageModal: false,
      showAudioModal: false,
      showPermissionModal: false,
      showaddbook: false,
      newbook: "Новая книга",
      selecttree: null,
      selectone: null,
      actives: null,
      activesbook: null,
      edit_actives_book: false,
      audioarray: [],
      tree: [],
      books: [],
      editorHeight: window.innerHeight - 128,
      checkedNames: [],
      seatchbooks: null,
      editors: "",
      imagegroup: [],
      attachment: null,
      breadcrumbs: []                                                                            
    }
  },
  created() {
    this.tree = this.trees;
  },

  mounted() {
    
    this.books = [];


    const urlParams = new URLSearchParams(window.location.search);
    let book_id = urlParams.get('b');
    this.breadcrumbs = [{id:this.parent_id, title: this.parent_name}];
    if(book_id && this.tree.findIndex(b => b.id == book_id) != -1) {
      this.showPage(book_id)
    }

  },
  methods: {
    
    movecatt() {
      this.actives.parent_cat_id = this.selectone;
      axios
        .post("/books/move/", {
          id: this.actives.id,
          parent: this.selectone,
        })
        .then((response) => {});
    },
    moveto(tre) {
      $("#perenos").modal("show");
      this.active(tre);
    },
    renamebooks(book) {
      axios
        .post("/pages/rename/", {
          id: book.id,
          name: book.title,
        })
        .then((response) => {});
    },
    rename(tre) {
      console.log("tre=>", tre);
      axios
        .post("/books/rename/", {
          id: tre.id,
          name: tre.name,
        })
        .then((response) => {});
    },
    onEndSortcat(tree) {
      tree.forEach((xx, index) => {
        xx.queue_number = index;
      });

      this.$message.info("Подождите пока сохранится ...");

      this.loader = true;

      axios
        .post("/books/order/", {
          tree: this.tree,
          id: this.parent_id,
        })
        .then((response) => {
          this.loader = false;
          this.$message.success("Порядок сохранен!");
        })
        .catch((error) => {
          this.loader = false;
          this.$message.error("Ошибка сохранения порядка");
        });

      this.loader = false;
    },
    select(sel) {
      if (sel == "koren") {
        this.selectone = null;
      } else {
        this.selectone = sel;
      }
    },
    passte() {
      this.checkedNames.forEach((xx) => {
        this.books.find((x) => x.id == xx).text = this.activesbook.text;
      });

      axios
        .post("/pages/search/", {
          idbooks: this.checkedNames,
          text: this.activesbook.text,
        })
        .then((response) => {});
    },
    searchitem() {
      this.seatchbooks = this.books.filter(
        (x) => x.title.toLowerCase() == this.activesbook.title.toLowerCase()
      );
      this.seatchbooks.forEach((xx) => {
        xx.namecat = this.tree.find((x) => x.id == xx.category_id).name;
      });
    },
    bookshow() {
      this.showaddbook = !this.showaddbook;
      setTimeout(() => {
        this.$refs.adddglabook.select();
      }, 500);
    },
    copyes() {
      // if (this.delo == 0) {
      //   if (this.selectone != null) {
      //     let book = {
      //       id: 0,
      //       title: this.activesbook.title,
      //       text: this.activesbook.text,
      //       category_id: this.selectone,
      //       order: 0,
      //     };

      //     axios
      //       .post("/page/copy/", {
      //         books: book,
      //       })
      //       .then((response) => {
      //         book.id = response.data;
      //         this.books.push(book);
      //         this.activebook(book);
      //       });
      //   }
      // } else {
      //   if (this.selectone != null) {
      //     this.activesbook.category_id = this.selectone;

      //     axios
      //       .post("/page/move/", {
      //         id: this.activesbook.id,
      //         catid: this.selectone,
      //       })
      //       .then((response) => {});
      //   }
      // }
    },
    onEndSort(books, id) {
      let arr;
      arr = books.filter((book) => book.category_id == id);

      arr.forEach((xx, index) => {
        xx.queue_number = index;
      });

      this.loader = false;

      this.$message.info("Подождите пока сохранится ...");

      axios
        .post("/pages/order/", {
          books: arr,
          id: id,
        })
        .then((response) => {
          this.loader = false;
          this.$message.success("Порядок сохранен!");
        })
        .catch((error) => {
          this.loader = false;
          this.$message.error("Ошибка сохранения порядка");
        });
      this.loader = false;
    },

    addaudio(url) {
      tinymce.activeEditor.insertContent(
        '<audio controls src="' + url + '"></audio>'
      );
    },
    addimage(url) {
      tinymce.activeEditor.insertContent(
        '<img alt="картинка" src="'+ url + '"/>'
      );
    },
    submit() {
      this.loader = true;
      const config = { "content-type": "multipart/form-data" };
      const formData = new FormData();
      formData.append("attachment", this.attachment);
      formData.append("id", this.activesbook.id);
      axios
        .post("/upload/images/", formData)
        .then((response) => {
          console.log("Загруэенно =>", response.data.location);

          this.addimage(response.data.location);
        //   this.imagegroup.push({
        //     url: "https://admin.u-marketing.org/" + response.data.location,
        //   });

        this.showImageModal = false;
          this.loader = false;
        })
        .catch((error) => console.log(error));
    },
    copyLink(book) {
      var Url = this.$refs["mylink" + book.id];
      Url.value = "http://surv.u-marketing.org/corp_book/" + book.hash;

      Url.select();
      document.execCommand("copy");

      this.$message.info("Ссылка на страницу скопирована!");
    },
    onAttachmentChange(e) {
      this.attachment = e.target.files[0];
      console.log(this.attachment);
      this.submit();
    },
    onAttachmentChangeaudio(e) {
      this.attachment = e.target.files[0];
      console.log(this.attachment);
      this.submitaudio();
    },
    submitaudio() {
      this.loader = true;
      const config = { "content-type": "multipart/form-data" };
      const formData = new FormData();
      formData.append("attachment", this.attachment);
      formData.append("id", this.activesbook.id);
      axios
        .post("/upload/audio/", formData)
        .then((response) => {
        //   this.audioarray.push({
        //     url: "https://admin.u-marketing.org/" + response.data.location,
        //   });

          this.addaudio(response.data.location);
          this.showAudioModal = false;
          this.loader = false;
        })
        .catch((error) => console.log(error));
    },
    savepass() {
      axios
        .post("/books/password/", {
          id: this.actives.id,
          login: this.actives.login,
          password: this.actives.password,
        })
        .then((response) => {});
    },
    saveServer() {
       this.loader = true;
      axios
        .post("/kb/page/update", {
          text: this.activesbook.text,
          title: this.activesbook.title,
          id: this.activesbook.id,
        })
        .then((response) => {

          this.edit_actives_book = false;
          this.$message.info("Сохранено");
          this.loader = false;

        });
    },

  
    deletecat(cat) {
      if (confirm("Вы уверены что хотите удалить категорию?") == true) {
        this.tree.splice(this.tree.indexOf(cat), 1);
        axios
          .post("/books/delete/", {
            id: cat.id,
          })
          .then((response) => {});
      }
    },

    addPage(book) {
      axios.post("/kb/page/create", {
        id: book.id
      }).then((response) => {
        this.activesbook = response.data;
        this.edit_actives_book = true;
        book.children.push(this.activesbook);
        this.$message.info('Добавлена страница');
      });
    },

    addPageToTree() {
      axios.post("/kb/page/create", {
        id: this.parent_id
      }).then((response) => {
        this.activesbook = response.data;
        this.edit_actives_book = true;
        this.tree.push(this.activesbook);
        this.$message.info('Добавлена страница');
      });
    },

    addpage(id, name) {
      if (name > "") {
        let ids = new Date().getTime();
        this.books.push({
          id: ids,
          title: name,
          text: "Description for book2",
          category_id: id,
          order: 0,
        });
        this.showaddbook = false;
        this.newbook = "Новая страница";
        console.log(
          "pages",
          this.books.find((x) => x.id == ids)
        );
        axios
          .post("/pages/create/", {
            page: this.books.find((x) => x.id == ids),
          })
          .then((response) => {
            this.books.find((x) => x.id == ids).id = response.data.id;
          });
      }
    },
    addcat(id, name) {
      if (name > "") {
        let ids = new Date().getTime();
        this.tree.push({
          id: ids,
          parent_cat_id: id,
          name: name,
          group_id: 0,
          login: null,
          password: null,
        });
        this.showaddbook = false;
        this.newbook = "Новая книга";

        axios
          .post("/books/create/", {
            parent_cat_id: id,
            categoryes: this.tree.find((x) => x.id == ids),
          })
          .then((response) => {
            this.tree.find((x) => x.id == ids).id = response.data.id;
          });
      }
    },
    addbook() {
      if (this.newbook > "") {
        let ids = new Date().getTime();
        this.tree.push({
          id: ids,
          parent_cat_id: null,
          name: this.newbook,
          group_id: 0,
          login: "admin",
          password: "pass",
        });
        this.showaddbook = false;
        this.newbook = "Новая книга";

        axios
          .post("/books/create/", {
            categoryes: this.tree.find((x) => x.id == ids),
          })
          .then((response) => {
            this.tree.find((x) => x.id == ids).id = response.data.id;
          });
      }
    },
    active(tre) {
      this.actives = tre;
      this.activesbook = null;
    },

    deletePage() {
      if(confirm('Вы уверены?')) {
        axios
        .post("/kb/page/delete", {
          id: this.activesbook.id,
        })
        .then((response) => {
          this.$message.success('Удалено');
          this.removeNode(this.tree, this.activesbook.id)
          this.activesbook = null;
        });
      }
    },
    deepSearch(array, item) {
      return array.some(function s(el) {
        return el == item || ((el instanceof Array) && el.some(s));
      })
    },

    removeNode(arr, id) {
      arr.forEach((it, index) => {
        if (it.id === id) {
          arr.splice(index, 1)
        }
        this.removeNode(it.children, id)
      })
    },

    activebook(book) {
      axios
        .post("/books/get_book/", {
          id: book.id,
        })
        .then((response) => {
          console.log(response.data.book);
          this.activesbook = response.data.book;
        });
      this.actives = null;
    },
    showPage(id) {
      if(this.activesbook && this.activesbook.id == id) return '';
      
      axios.get("/kb/get/" + id, {}).then((response) => {
        this.activesbook = response.data.book;

        this.breadcrumbs = response.data.breadcrumbs;

        this.edit_actives_book = false;
        
        this.setTargetBlank();

        window.history.replaceState({ id: "100" }, "База знаний", "/kb?s=" + this.parent_id + '&b=' + id);
      });
      
    },
    
    setTargetBlank() {
      this.$nextTick(() => {
        var links = document.querySelectorAll(".bp-text a");
        links.forEach(l => l.setAttribute("target", "_blank"));
      })
      
    },

    editorSave() {},
  },
};
/**
 * <style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
 */
</script>



