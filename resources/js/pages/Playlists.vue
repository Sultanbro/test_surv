<template>
  <div class="video-playlists">


      

      <div class="d-flex">

        <div class="lp">
          <h1 class="page-title">Плейлисты</h1>

           
          <div
            class="section d-flex aic jcsb my-2"
            v-for="(cat, index) in categories" 
            :key="cat.id"
            @click="selectCat(index)"
          >
            <p class="mb-0">{{ cat.title }}</p>

            <i
              class="fa fa-trash ml-2"
              v-if="cat.id != 0 && mode == 'edit'"
              @click.stop="deleteCat(index)"
            ></i>
            </div>
            

          <button class="btn-add" @click="showAddCategory = true" v-if="mode == 'edit'">
            Добавить категорию
          </button>

        </div>

        
        <div class="rp" style="flex: 1 1 0%;">
          <div class="hat">
            <div class="d-flex jsutify-content-between hat-top">
              <div class="bc">
                <a href="#">Видеоплейлисты</a>
                <template v-if="activeCat">
                  <i class="fa fa-chevron-right"></i>
                  <a href="#" >{{ activeCat.title }}</a>
                </template>
                <template v-if="activePlaylist">
                  <i class="fa fa-chevron-right"></i>
                  <a href="#" >{{ activePlaylist.title }}</a>
                </template>
                <!---->
              </div>
              <div class="control-btns" >
                <div class="mode_changer" v-if="can_edit">
                  <i class="fa fa-edit"  @click="toggleMode" :class="{'active': mode == 'edit'}" />
                </div>
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
                  :auth_user_id="user_id"
                  :mode="mode"
                  :myvideo="myvideo" />
         
              </div>

              <div v-else>

                  <div class="d-flex align-items-start mb-3">
                    <div>
                      <h4>{{ activeCat.title }}</h4>
                      <p class="mb-0">Кол-во плейлистов: {{ activeCat.playlists.length }}</p>
                    </div>

                    
                    <button class="btn-add mt-0 ml-2 mb-3" @click="showAddPlaylist = true"  v-if="mode == 'edit'">
                      Добавить плейлист
                    </button>
                  </div>
                    

                  <table class="table">
                  
                      <tr 
                        v-for="(playlist, p_index) in activeCat.playlists"
                        :key="playlist.id"
                        class="playlist"
                        @click="selectPl(p_index)"
                      >
                          <td class="poster_count">
                            <div>
                                <img :src="playlist.img == '' || playlist.img == null ? '/video_learning/noimage.png' : playlist.img"
                                  class="img-fluid"/>
                              <span></span>
                            </div>
                          </td>
                          <td>
                            <div class="title">  {{ playlist.title }}</div>
                            <div class="text">  {{ playlist.text }}</div>
                               <div class="d-flex"  v-if="mode == 'edit'"> 
                                <i
                                  class="fa fa-cogs"
                                  v-if="playlist.id != 0"
                                  @click.stop="editAccess(p_index)"
                                ></i>
                              <i
                                class="fa fa-trash ml-2"
                                v-if="playlist.id != 0"
                                @click.stop="deletePl(p_index)"
                              ></i>
                            </div>
                          </td>
                      </tr>
                 
                  </table>

                 

                    
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
    editAccess(i) {
      this.$message.info('Настройка доступов не работает');
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
