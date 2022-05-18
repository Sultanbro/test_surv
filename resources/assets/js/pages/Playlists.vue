<template>
  <div class="video-playlists">


      

      <div class="d-flex">

        <div class="lp">
          <h1 class="page-title">Плейлисты</h1>

          
          <div
            class="section d-flex aic jcsb my-2"
            v-for="(cat, index) in categories" 
            :key="cat.id"
            @click="selecCat(index)"
          >
            <p class="mb-0">{{ cat.title }}</p>

            <i
              class="fa fa-trash ml-2"
              v-if="cat.id != 0"
              @click.stop="deleteCat(index)"
            ></i>
            </div>
            
          </div>

          <button class="btn-add" @click="showAddCategory = true">
            Добавить категорию
          </button>

        </div>


        <div class="rp" style="flex: 1 1 0%; padding-bottom: 50px;">
          <div class="hat">
            <div class="d-flex jsutify-content-between hat-top">
              <div class="bc">
                <a href="#">Видеоплейлисты</a>
                <template v-if="activePlaylist">
                  <i class="fa fa-chevron-right"></i>
                  <a href="#" >{{ activePlaylist.title }}</a>
                </template>
                <!---->
              </div>
              <div class="control-btns"></div>
            </div>
            <div><!----></div> 
          </div>
          <div class="content mt-3">
            <div v-if="activePlaylist != null" class="p-3">

              <page-playlist-edit 
                ref="playlist"
                @back="back" 
                :token="token"
                :id="activePlaylist.id"
                :auth_user_id="user_id" />

            </div>


            <div v-else class="p-3">

              <div
                class="section d-flex aic jcsb my-2"
                v-for="(playlist, p_index) in playlists"
                :key="playlist.id"
                @click="selectPl(p_index)"
              >
                <p class="mb-0">{{ playlist.title }}</p>

                <div class="d-flex">
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
                
              </div>

              <button class="btn-add" @click="showAddPlaylist = true">
                Добавить плейлист
              </button>
            </div>

          </div>
        </div>
      </div>
  </div>
</template>

<script>
export default {
  name: "Playlists",
  props: ['token'],
  data: function() {
    return {
      playlists: [],
      categories: [],
      user_id: 0,
      activePlaylist: null,
      showAddPlaylist: false,

    };
  },
  watch: {},

  created() {
    this.fetchData();
  },

  mounted() {},
  methods: {
    
    fetchData() {
      axios
        .get("/playlists/get")
        .then((response) => {
          this.playlists = response.data.playlists;
          this.user_id = response.data.user_id;
        })
        .catch((error) => {
          alert(error);
        });
    },

    selectPl(i) { 
      console.log(this.playlists[i].title)
      this.activePlaylist = this.playlists[i];

    },
    editAccess(i) {

    },
     deletePl(i) {

     },
      selecCat(i) {

      },

      deleteCat(i) {

      },
     back() {
        this.activePlaylist = null;
        window.history.replaceState({ id: "100" }, "Плейлисты", "/video_playlists");
      },

    
  },
};
</script>
