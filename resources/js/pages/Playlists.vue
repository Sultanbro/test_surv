<template>
  <div class="video-playlists">


      

      <div class="d-flex">

        <div class="lp">
          <h1 class="page-title">Плейлисты</h1>

           
          <div
            class="section d-flex aic jcsb my-2"
            v-for="(cat, index) in categories" 
            :key="cat.id"
            :class="{'active': activeCat != null && activeCat.id == cat.id}"
            @click="selectCat(index)"
          >
            <p class="mb-0">{{ cat.title }}</p>
              
              <div class="d-flex">
                  <i
                  class="fa fa-edit ml-2"
                  v-if="cat.id != 0 && mode == 'edit'"
                  @click.stop="editCat(index)"
                ></i>
                <i
                  class="fa fa-trash ml-2"
                  v-if="cat.id != 0 && mode == 'edit'"
                  @click.stop="deleteCat(index)"
                ></i>
              </div>
          </div>
            

          <button class="btn-add" @click="showAddCategory = true" v-if="mode == 'edit'">
            Добавить категорию
          </button>

        </div>

        
        <div class="rp" style="flex: 1 1 0%;overflow:auto;">

          <div class="hat">
            <div class="d-flex jsutify-content-between hat-top">
              <div class="bc">
                <a href="#" @click="back">Видеоплейлисты</a>
                <template v-if="activeCat">
                  <i class="fa fa-chevron-right"></i> 
                  <a href="#"  @click="back">{{ activeCat.title + ' (' + activeCat.playlists.length + ')' }}</a>
                </template>
                <template v-if="activePlaylist">
                  <i class="fa fa-chevron-right"></i>
                  <a href="#" >{{ activePlaylist.title }}</a>
                </template>
                <!---->
              </div>
              <div class="control-btns d-flex" >
                <div class="mode_changer" v-if="can_edit">
                  <i class="fa fa-edit"  @click="toggleMode" :class="{'active': mode == 'edit'}" />
                </div>

                <i class="btn btn-success fa fa-plus ml-2 d-flex px-2 aic"  @click="showAddPlaylist = true" v-if="mode == 'edit' && activePlaylist == null" />


                <!-- buttons for playlist like Save Group -->
                <template v-if="activePlaylist && mode == 'edit'">
                  <i class="btn btn-info fa-upload fa ml-2 d-flex px-2 aic" @click="uploadVideo" title="Добавить видео"/>
                  <i class="btn btn-info fa fa-folder-plus ml-2 d-flex px-2 aic" @click="addGroup" title="Добавить группу"/>
                  <i class="btn btn-success fa fa-save ml-2 d-flex px-2 aic"  @click="savePlaylistEdit" title="Сохранить плейлист" />
                </template>

              </div>
            </div>
            <div><!----></div> 
          </div>


          <div class="content mt-3">
            
            <div v-if="activeCat != null" class="p-3 ">


              <div v-if="activePlaylist != null" class="">
                <page-playlist-edit 
                  ref="playlist"
                  @back="back" 
                  :token="token"
                  :id="activePlaylist.id"
                  :is_course="false"
                  :auth_user_id="user_id"
                  :mode="mode"
                  :myvideo="myvideo" />
         
              </div>

              <div v-else>

                  <!-- playlists -->
                  <div class="els">

                      <div class="playlist mb-3" v-for="(playlist, p_index) in activeCat.playlists" :key="playlist.id" @click="selectPl(p_index)">

                        <div class="left" :style="'background-image: url(' + (playlist.img == '' || playlist.img == null ? '/images/author.jpg' : playlist.img ) + ')'">
                        </div>

                        <div class="right">
                            <div class="title">  {{ playlist.title }}</div>
                             <div class="d-flex btns mb-2"  v-if="mode == 'edit'"> 
                                <i
                                  class="fa fa-edit ml-2"
                                  v-if="playlist.id != 0"
                                  title="Переместить"
                                  @click.stop="movePl(p_index)"
                                ></i>
                                <i
                                  class="fa fa-trash ml-2"
                                  v-if="playlist.id != 0"
                                  @click.stop="deletePl(p_index)"
                                ></i>
                            </div>
                            <div class="text">  {{ playlist.text }}</div>
                              
                        </div>

                      </div>
                      
                     
                  </div>

                 

                    
              </div>

            </div>


          </div>
        </div>
      </div>



    <b-modal
      v-model="showAddPlaylist"
      title="Новый плейлист"
      size="md"
      class="modalle"
      hide-footer
    >
      <input
        type="text"
        v-model="newPlaylist"
        placeholder="Название..."
        class="form-control mb-2"
      />
      <button class="btn btn-primary rounded m-auto" @click="addPlaylist">
        <span>Сохранить</span>
      </button>
    </b-modal>

    <b-modal
      v-model="showAddCategory"
      title="Новая категория"
      size="md"
      class="modalle"
      hide-footer
    >
      <input
        type="text"
        v-model="newcat"
        placeholder="Название категории..."
        class="form-control mb-2"
      />
      <button class="btn btn-primary rounded m-auto" @click="addCat">
        <span>Сохранить</span>
      </button>
    </b-modal>

     <b-modal
      v-model="showEditCat"
      title="Переименовать категорию"
      size="md"
      class="modalle"
      hide-footer
    >
      <input
        type="text"
        v-model="newcat"
        placeholder="Название категории..."
        class="form-control mb-2"
      />
      <button class="btn btn-primary rounded m-auto" @click="saveCat">
        <span>Сохранить</span>
      </button>
    </b-modal>

    <b-modal
      v-model="showEditPlaylist"
      title="Редактировать плейлист"
      size="md"
      class="modalle"
      hide-footer
    >
      <div class="d-flex mb-2">
        <p class="mb-0 mr-2">Название</p>
        <input
          type="text"
          v-model="editingPlaylist.title"
          placeholder="Название плейлиста..."
          class="form-control mb-2"
        />
      </div>

      <div class="d-flex mb-2">
        <p class="mb-0 mr-2">Описание</p>
        <textarea
          v-model="editingPlaylist.text"
          placeholder="Описание плейлиста..."
          class="form-control"
        />
      </div>

      <div class="d-flex mb-2">
        <p class="mb-0 mr-2">Категория</p>
         <select
          class="form-control"
          v-model="editingPlaylist.category_id">
          <option v-for="cat in categories" :value="cat.id" :key="cat.id">{{ cat.title }}</option>
        </select>
      </div>


      <button class="btn btn-primary rounded m-auto" @click="savePlaylist">
        <span>Сохранить</span>
      </button>
    </b-modal>

  </div>
</template>

<script>
export default {
  name: "Playlists",
  props: {
    token: String,
    can_edit: {
      type: Boolean,
      default: false
    },
    category: Number,
    playlist: Number,
    video: Number 
  },
  data: function() {
    return {
      categories: [],
      showEditCat: false,
      editingPlaylist: {
        title: '',
        text: '',
        category_id: ''
      },
      showEditPlaylist: false,
      user_id: 0,
      mode: 'read',
      activeCat: null,
      newcat: '',
      newPlaylist: '',
      activePlaylist: null,
      showAddPlaylist: false,
      showAddCategory: false,
      mylink: window.location.protocol + "//" + window.location.host + window.location.pathname.substring(0,16),
      data_category: this.category,
      data_playlist: this.playlist,
      myvideo: this.video,
    };
  },

  created() {
     this.fetchData();
  },

  methods: {

    addGroup() {
      this.$refs.playlist.addGroup()
    },

    uploadVideo() {
      this.$refs.playlist.uploadVideo()
    },

    savePlaylistEdit() {
      this.$refs.playlist.savePlaylist()
    },

    clearUrl(){
      var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname.substring(0,16);
      history.pushState(null, null, newUrl);
    },

    fetchData() {
      axios
        .get("/playlists/get")
        .then((response) => {
          this.categories = response.data.categories;
          this.user_id = response.data.user_id;
          if(this.categories.length > 0) {
            this.activeCat = this.categories[this.category-1];
            if(this.playlist > 0){
              this.activePlaylist = this.activeCat.playlists[this.playlist-1];
            }
          }
        })
        .catch((error) => {
          alert(error);
        });
    },

    selectPl(i) { 
      this.activePlaylist = this.activeCat.playlists[i];
      this.data_playlist = i+1;

      console.log(this.mylink.concat('/'+this.data_category, '/'+this.data_playlist));
      if (history.pushState) {
          var newUrl = this.mylink.concat('/'+this.data_category, '/'+this.data_playlist);
          history.pushState(null, null, newUrl);
      }
      else {
          console.warn('History API не поддерживает ваш браузер');
      }

    },

    editCat(i) {
      this.showEditCat = true;
      this.newcat = this.categories[i].title;
      this.activeCat = this.categories[i];
    },

    movePl(i) {
      this.editingPlaylist = this.activeCat.playlists[i];
      this.showEditPlaylist = true;
    },

     deletePl(i) {
       if (confirm("Вы уверены что хотите удалить плейлист?")) {
          axios
            .post("/playlists/delete", {
              id: this.activeCat.playlists[i].id
            })
            .then((response) => {
              this.activeCat.playlists.splice(i, 1);
              this.$message.success("Удалено");
            });
        }
     },
      selectCat(i) {
        this.activeCat = this.categories[i];
        this.activePlaylist = null;
        this.data_category = i+1;
        this.data_playlist = 0; 
        console.log(this.mylink.concat('/'+this.data_category, '/'+this.data_playlist));

        if (history.pushState) {
            var newUrl = this.mylink.concat('/'+this.data_category, '/'+this.data_playlist);
            history.pushState(null, null, newUrl);
        }
        else {
            console.warn('History API не поддерживает ваш браузер');
        }
        this.myvideo = 0;

      },

      deleteCat(i) {
        if (confirm("Вы уверены что хотите удалить категорию?")) {

          axios
            .post("/playlists/delete-cat", {
              id: this.categories[i].id
            })
            .then((response) => {
              this.categories.splice(i, 1);
              this.$message.success("Удалено");
            });
        }
      },

    savePlaylist() {
      axios
        .post("/playlists/save-fast", {
          playlist: this.editingPlaylist,
        })
        .then((response) => {

          if(this.editingPlaylist.category_id != this.activeCat.id) {
            this.deleteItemFrom(this.editingPlaylist.id, this.activeCat.playlists);
            let i = this.categories.findIndex(el => el.id == this.editingPlaylist.category_id);
            if(i != -1) this.categories[i].playlists.push(this.editingPlaylist);
            this.showEditPlaylist = false;
            this.editingPlaylist = {};
          }

          this.$message.success("Сохранено");
        });
    },

    deleteItemFrom(id, from) {
      let i = from.findIndex(el => el.id == id);
      if(i != -1) from.splice(i, 1);
    },
    
     back() {
        this.activePlaylist = null;
        window.history.replaceState({ id: "100" }, "Плейлисты", "/video_playlists");
      },

    addCat() {
       if (this.newcat.length <= 2) {
        alert("Слишком короткое название!");
        return "";
      }

      let loader = this.$loading.show();

      axios
        .post("/playlists/add-cat", {
          title: this.newcat,
        })
        .then((response) => {
          this.showAddCategory = false;
          this.newcat = "";

          this.categories.push(response.data);

          this.$message.success("Успешно создана!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    saveCat() {
      if (this.activeCat.title.length <= 2) {
        alert("Слишком короткое название!");
        return "";
      }

      let loader = this.$loading.show();

      axios
        .post("/playlists/save-cat", {
          title: this.newcat,
          id: this.activeCat.id,
        })
        .then((response) => {
          this.showEditCat = false;
          this.activeCat.title = this.newcat;
          this.newcat = '';
          this.$message.success("Сохранено!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    deleteCat(i) {
      if (confirm("Вы уверены что хотите удалить категорию?")) {
        axios
          .post("/playlists/delete-cat", {
            id: this.categories[i].id
          })  
          .then((response) => {
            this.categories.splice(i, 1);
            this.activeCat = null;
            this.$message.success("Удалено");
          });
      }
    },

    addPlaylist() {
       if (this.newPlaylist.length <= 2) {
        alert("Слишком короткое название!");
        return "";
      }

      let loader = this.$loading.show();

      axios
        .post("/playlists/add", {
          title: this.newPlaylist,
          cat_id: this.activeCat.id,
        })
        .then((response) => {
          this.showAddPlaylist = false;
          this.newPlaylist = "";

          this.activeCat.playlists.push(response.data);

          this.$message.success("Успешно создан!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    toggleMode() {
      this.mode = (this.mode == 'read') ? 'edit' : 'read';
    }
    
  },
};
</script>
