<template>
  <div class="d-flex">
    <aside id="left-panel" class="lp">
      <div class="btn btn-search mb-3" @click="showSearch = true" v-if="!course_page">
        <i class="fa fa-search"></i>
        <span>Искать в базе...</span>
      </div>
      <div class="btn btn-grey mb-3" @click="$emit('back')"  v-if="!course_page">
        <i class="fa fa-arrow-left"></i>
        <span>Вернуться к разделам</span> 
      </div>

      <div class="kb-wrap noscrollbar">

        <div class="chapter opened mb-3" v-if="!course_page">
          <div class="d-flex">
            <span class="font-16 font-bold">{{ parent_title }}</span>
            <div class="chapter-btns">
              <i class="fa fa-plus" v-if="mode =='edit'" @click="addPageToTree"></i> 
            </div>
          </div>
        </div>

        <nested-course
          v-if="course_page"
          :tasks="tree"
          :active="activesbook != null ? activesbook.id : 0"
          @showPage="showPage"
        />

        <nested-draggable
          v-else
          :tasks="tree"
          :mode="mode"
          :auth_user_id="auth_user_id" 
          :opened="true"
          @showPage="showPage"
          @addPage="addPage"
          :parent_id="id"
        />
      </div>
      
    </aside>
    <!-- /#left-panel -->


    <!-- Right Panel -->

    <div class="rp" style="flex: 1;padding-bottom: 0px;flex: 1 1 0%;height: 100vh;overflow-y: auto;">
      <div class="hat"  >
        <div class="d-flex jsutify-content-between hat-top" v-if="!course_page">
          <div class="bc">
            <a href="#" @click="$emit('back')">База знаний</a>
            <template v-for="(bc, bc_index) in breadcrumbs">
              <i class="fa fa-chevron-right"></i>
              <a href="#" @click="showPage(bc.id)">{{ bc.title }}</a>
            </template>
          </div>

          <div class="mode_changer" v-if="can_edit">
            <i class="fa fa-edit"
              @click="$emit('toggleMode')"
              :class="{'active': mode == 'edit'}" />
          </div>

          <div class="control-btns" v-if="can_edit">

           

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
              v-if="mode == 'edit'"
                class="form-control btn-danger btn-medium ml-2"
                @click="deletePage"
              >
                  <i class="fa fa-trash"></i>
              </button>

              <button
              v-if="mode == 'edit'"
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
              class="article_title px-4 py-3"
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
            api-key="mve9w0n1tjerlwenki27p4wjid4oqux1xp0yu0zmapbnaafd"
            :init="{
              images_upload_url: '/upload/images/',
              automatic_uploads: true,
              height: editorHeight,
              setup: function (editor) {
                editor.on('init change', function () {
                    editor.uploadImages();
                });
            },
             images_upload_handler: submit_tinymce,
              //paste_data_images: false,
              resize: true, 
              autosave_ask_before_unload: true,
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
                'styleselect  | bold italic underline strikethrough | table | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | fullscreen  preview |  media  link | undo redo',
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


            <questions
                  :questions="activesbook.questions"
                  :id="activesbook.id"
                  type="kb"
                  :mode="mode"
                  :count_points="true"
                  @passed="passed"
                  :key="questions_key"
                  :pass_grade="activesbook.pass_grade"
                  @changePassGrade="checkPassGrade"
                />

        
        </template>

        <template  v-if="activesbook != null && !edit_actives_book">
          <div class="book_page">
            <div class="author d-flex aic mb-4 justify-end">
              <img src="/images/avatar.png" alt="avatar icon">
              <div class="text">
                <p class="edited"><span>Cоздано:</span> {{ activesbook.created }} {{ activesbook.author }}</p>
                <p class="edited"><span>Изменено:</span> {{ activesbook.edited_at }} {{ activesbook.editor }}</p>
              </div>
            </div> 
            <div class="bp-text" v-html="activesbook.text">
            
            </div>

            <questions
                  :questions="activesbook.questions"
                  :id="activesbook.id"
                  type="kb"
                  :mode="mode"
                  :count_points="true"
                  @passed="passed"
                  :key="questions_key"
                  :pass_grade="activesbook.pass_grade"
                  @changePassGrade="checkPassGrade"
                />
              <div class="pb-5"></div> 
          </div>
        </template>
      </div>

    </div>


    <button class="next-btn btn btn-primary" 
      v-if="course_page && (passedTest )"
      @click="nextElement()">
      Продолжить курс 
      <i class="fa fa-angle-double-right ml-2"></i>
    </button>


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
                      >Выберите файл</label>
                    <p>прогресс бар</p>
                  </div>
                </div>
              </form>
              <progress-bar
                :percentage="myprogress"
                :label="Загрузка"
              />
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
                    <p>прогресс бар</p>
                  </div>
                </div>
              </form>
    </b-modal>


    <b-modal v-model="showPermissionModal" :title="'Настройка доступа к разделу'">
      <div v-if="activesbook != null">{{ activesbook.title}} </div>
       Пока не сделано
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
         <div class="item" v-for="(item, x) in search.items" @click="showPage(item.id, true)" :key="x">
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
import nestedDraggable from "../components/nested";
import nestedCourse from "../components/nested_course";

export default { 
  name: "booklist",
  props: [
    "trees",
    'parent_id',
    'auth_user_id',
    'parent_name',
    'show_page_id',
    'can_edit',
    'mode',
    'course_page',
    'enable_url_manipulation',
    'course_item_id'
  ],
  components: { 
    nestedDraggable,
    nestedCourse
  }, 
  data() {
    return {
      myprogress: 0,
      id: 0,
      loader: false,
      delo: 0,
      parent_title: '',
      showSearch: false,
      can_save: false,
      search: {
        input: '',
        items: []
      },
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
      breadcrumbs: [],
      ids: [],
      passedTest: false,
      questions_key: 1,
      text_was: '',                                                        
      title_was: '',     
      item_models: []                                                   
    }
  },

  created() {

    this.getTree();
 
    this.parent_title = this.parent_name;

 
    this.id = this.parent_id;
    
  },

  mounted() {

    if(!this.course_page) {
      window.addEventListener('beforeunload', e => this.beforeunloadFn(e))
    }
    
  },

  methods: {
    beforeunloadFn(e) {
      if(this.text_was != this.activesbook.text || this.title_was != this.activesbook.title) {
        e.returnValue = 'Are you sure you want to leave?';
      }
    },
    passed() {
      this.passedTest = true;
      if(this.activesbook.item_model == null) {
        this.setSegmentPassed();
        this.activesbook.item_model = {status: 1}; 
      }
      console.log('passed test')
    },

    setSegmentPassed() {
      axios
        .post("/my-courses/pass", {
          id: this.activesbook.id,
          type: 3,
          course_item_id: this.course_item_id,
          questions: this.activesbook.questions
        })
        .then((response) => {
         // this.activeVideo.item_models.push(response.data.item_model);
        })
        .catch((error) => {
          alert(error);
        });
    },

    setArticlePassed() {
      axios
        .post("/my-courses/pass", {
          id: this.activesbook.id,
          type: 3,
          course_item_id: this.course_item_id,
        })
        .then((response) => {
        

        
        })
        .catch((error) => {
          alert(error);
        });
    },

    returnArray(items, indexes = []) { 
      items.forEach((item, i_index) => {
          let arr = [...indexes, i_index];
          this.ids.push({
            id: item.id,
            i: arr
          })
          
          if(item.children !== undefined) this.returnArray(item.children, arr);
      });
    },

    nextElement() {
      if(this.activesbook.item_model == null) {
        this.setArticlePassed();
        this.activesbook.item_model = {status: 1}; 
      }
   
      // find next element 
      let index = this.ids.findIndex(el => el.id == this.activesbook.id); 
      if(index != -1 && this.ids.length - 1 > index) {
        
        let el = this.findItem(this.ids[index + 1]);

       
        this.passedTest = false;
        this.activesbook = el;
        this.questions_key++;

        if(this.activesbook != null) {
          console.log(this.activesbook)
          if(this.activesbook.questions.length == 0) {
            this.passedTest = true;
          }
        }
          
      } else {
        // move to next course item
        this.$parent.after_click_next_element();
      }
    },

    findItem(el) {
      if(el.i.length == 1) return this.tree[el.i[0]];
      if(el.i.length == 2) return this.tree[el.i[0]].children[el.i[1]];
      if(el.i.length == 3) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]];
      if(el.i.length == 4) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]].children[el.i[3]];
      if(el.i.length == 5) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]].children[el.i[3]].children[el.i[4]];
      if(el.i.length == 6) return this.tree[el.i[0]].children[el.i[1]].children[el.i[2]].children[el.i[3]].children[el.i[4]].children[el.i[5]];
    },

    getTree() {
       axios
        .post("/kb/tree", {
          id: this.parent_id,
          can_read: this.course_page
        })
        .then((response) => {
          this.tree = response.data.trees;
          this.item_models = response.data.item_models;

          this.can_save = response.data.can_save; // without test

          this.books = [];

          const urlParams = new URLSearchParams(window.location.search);
          let book_id = urlParams.get('b');
          this.breadcrumbs = [{id:this.id, title: this.parent_title}];
         
          
          if(this.course_page) {
              
            // create array of books ids
            this.ids = [];
            this.returnArray(this.tree);

            book_id = this.show_page_id


            if(this.show_page_id == 0 || this.show_page_id == null) {
              this.activesbook = this.tree[0];
            } else {
              // find element 
             
              let index = this.ids.findIndex(el => el.id == this.show_page_id); 
              
              console.log(index)
              if(index != -1) {
                let el = this.findItem(this.ids[index]);
                  console.log(el)
                this.activesbook = el;
                if(this.activesbook != null && this.activesbook.questions.length == 0) {
                  this.passedTest = true;
                }
              }
            }
            

          

          } else {


              let result = null
              this.tree.every(obj => {
                result = this.deepSearchId(obj, book_id)


                if (result != null) { 
                  console.log(result);
                  this.showPage(book_id, false, true);
                  return false;
                }
                return true;
              });

          }

          this.connectItemModels(this.tree)
          
        })
        .catch((error) => {
          alert(error);
        });
    },

    connectItemModels(tree) {
      tree.forEach((el, e) => {
        console.log(this.item_models)
        let i = this.item_models.findIndex(im => im.item_id == el.id);
        if(i != -1) {
          el.item_model = this.item_models[i];
          this.item_models.splice(i,1);
        } else {
          el.item_model = null;
        }
        if(el.children !== undefined) {
          this.connectItemModels(el.children)
        }
      });
    },
    
    searchInput() {
      if(this.search.input.length <= 2) return null;
      
      axios
        .post("/kb/search", {
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

    movecatt() {
      this.actives.parent_cat_id = this.selectone;
      axios
        .post("/books/move/", {
          id: this.actives.id,
          parent: this.selectone,
        }) 
        .then((response) => {}); 
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

      let loader = this.$loading.show();

      axios
        .post("/books/order/", {
          tree: this.tree,
          id: this.id,
        })
        .then((response) => {
          loader.hide()
          this.$message.success("Порядок сохранен!");
        })
        .catch((error) => {
            loader.hide()
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

    submit_tinymce(blobInfo, success, failure) {
      
      this.loader = true;
      const config = { "content-type": "multipart/form-data" };
      const formData = new FormData();
      formData.append('attachment', blobInfo.blob());
      formData.append("id", this.activesbook.id);
      axios
        .post("/upload/images/", formData)
        .then((response) => {
          console.log("Загруэенно =>", response.data.location);
            
          success(response.data.location);
          this.loader = false;
        })
        .catch((error) => console.log(error));
    },

    submit() {
      this.loader = true;
      const config = { 
        onUploadProgress: progressEvent => {
          let { progress } = this.myprogress;
          progress = (progressEvent.loaded / progressEvent.total) * 100;
          this.myprogress = progress;
        }
      };
      const formData = new FormData();
      formData.append("attachment", this.attachment);
      formData.append("id", this.activesbook.id);
      axios
        .post("/upload/images/", formData, config)
        .then((response) => {
          console.log("Загруэенно =>", response.data.location);

          this.addimage(response.data.location);
        //   this.imagegroup.push({
        //     url: "https://bp.jobtron.org/" + response.data.location,
        //   });

          if(this.myprogress >= 100){
            this.showImageModal = false;
            this.loader = false;
            this.myprogress = 0;
          }
        })
        .catch((error) => console.log(error));
    },

    copyLink(book) {
      var Url = this.$refs["mylink" + book.id];
      Url.value = "http://bp.jobtron.org/corp_book/" + book.hash;

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
        //     url: "https://bp.jobtron.org/" + response.data.location,
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
      if(this.activesbook.questions.length == 0 && !this.can_save) {
        this.$message.error('Нельзя вносить изменения без тестов');
        return;
      }

      let loader = this.$loading.show();
      axios
        .post("/kb/page/update", {
          text: this.activesbook.text,
          title: this.activesbook.title,
          pass_grade: this.activesbook.pass_grade,
          id: this.activesbook.id,
        })
        .then((response) => { 
        
          this.edit_actives_book = false;
          this.$message.info("Сохранено");
          this.renameNode(this.tree, this.activesbook.id, this.activesbook.title);
          loader.hide()

        })
        .catch((error) => {loader.hide()})
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
        id: this.id
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

    deepSearchId(obj, targetId) {
      console.log(obj.id + ' === ' + targetId)
      if (obj.id == targetId) {
        return obj
      }
    
      for (let item of obj.children) {
        let check = this.deepSearchId(item, targetId)
        if (check) {
          return check
        }
      }
      
      return null
    },

    removeNode(arr, id) {
      arr.forEach((it, index) => {
        if (it.id === id) {
          arr.splice(index, 1)
        }
        this.removeNode(it.children, id)
      })
    },

    renameNode(arr, id, title) {
      arr.forEach((it, index) => {
        if (it.id === id) {
          it.title = title;
          console.log('IT title')
        }
        this.renameNode(it.children, id, title)
      })
    },

    
    recurse(arr, id, objToMerge, inAncestor = false) {
      return arr.map(obj => {
        const mergeThis = inAncestor || obj.id == id;
        const merged = !mergeThis ? obj : { ...obj, config: { ...obj.config, ...objToMerge } };
        if (merged.children) {
          merged.children = this.recurse(merged.children, id, objToMerge, mergeThis);
        }
        return merged;
      });
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

    showPage(id, refreshTree = false, expand = false) {
      console.log(this.activesbook)
      if(this.activesbook != null && (this.text_was != this.activesbook.text || this.title_was != this.activesbook.title)) {
        if(!this.course_page) {
           if(!confirm('У вас на странице остались несохранненные изменения. Точно хотите выйти?'))  {
            return;
          }
        }
      }

      if(this.activesbook && this.activesbook.id == id) return '';
      
      let loader = this.$loading.show();
      axios.post("/kb/get", {
        id: id,
        refresh: refreshTree
      }).then((response) => {
        loader.hide()
        this.activesbook = response.data.book;
        this.text_was = this.activesbook.text;
        this.title_was = this.activesbook.title;
        this.breadcrumbs = response.data.breadcrumbs;
        this.edit_actives_book = false;
        
    
        if(refreshTree) {
          this.id = response.data.top_parent.id;
          this.parent_title = response.data.top_parent.title
          this.tree = response.data.tree
          this.showSearch = false;
          this.search.input = false;
          this.search.items = [];
         
        }

        if(expand)  this.expandTree();
        this.setTargetBlank();
        
        if(this.enable_url_manipulation) {
          window.history.replaceState({ id: "100" }, "База знаний", "/kb?s=" + this.id + '&b=' + id);
        }
        
        
      })
      .catch((e) => {loader.hide()})
      
    },
    
    expandTree() {
      let item = null;
      
      this.breadcrumbs.forEach(bc => {
        console.log(bc.id + '--- ' + bc.parent_id)

        let s_index = this.tree.findIndex(t => t.id == bc.id);

          if(s_index != -1) {
           
            if(item != null) {
              item = item.children[s_index];
            } else {
              item = this.tree[s_index]
            }
             item.opened = true;
        
        }
        
      });
    },

    setTargetBlank() {
      this.$nextTick(() => {
        var links = document.querySelectorAll(".bp-text a");
        links.forEach(l => l.setAttribute("target", "_blank"));
      })
      
    },

    editorSave() {},

    checkPassGrade() {
      console.log('pass grade')
      let len = this.activesbook.questions.length;
      let min = len != 0 ? Number((100 / len).toFixed()) : 100;

      if(this.activesbook.pass_grade > 100) this.activesbook.pass_grade = 100;
      if(this.activesbook.pass_grade < min) this.activesbook.pass_grade = Number(min);
    },
  },
};

</script>
<style>
.content {
    max-height: unset;
    overflow: unset;
}
</style>


