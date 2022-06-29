<template>
  <div class="video-playlist">
    <div class="d-flex jcsb mb-3">
      <div class="s w-full">
        <div class="d-flex">
         <input
            type="text"
            class="form-control form-control-sm w-full p-itle mb-0 mr-2"
            v-model="playlist.title"
            name="title"
            :disabled="mode == 'read'" />
        </div>
         
      </div>

      <div class="d-flex align-items-start">
        <button class="btn btn-success px-2" @click="savePlaylist">
          <i class="fa fa-save"></i>
        </button>
      </div>
    </div>

   
    <div class="row">

      <div class="col-lg-12">
        <div class="form-group">
          <textarea
            name="text"
            class="form-control textarea"
            required
            title="Описание плейлиста"
            placeholder="Описание плейлиста"
            v-model="playlist.text"
              :disabled="mode == 'read'"
          ></textarea>
        </div>
      </div>  
       
      <div class="col-lg-6">
        <div class="block  br" v-if="activeVideo != null">
            <v-player :src="activeVideo.links" :key="activeVideo.id" />
           
            <div class="row mb-2 mt-2">
            
              <div class="col-md-12">
                <input
                  type="text"
                  class="form-control"
                  v-model="activeVideo.title"
                  :disabled="mode == 'read'"
                />
              </div>
            </div>
            <div class="row mb-2" v-if="mode == 'edit'">
              <div class="col-md-12">
                <input
                  type="text"
                  placeholder="Ссылка на видео"
                  class="form-control"
                  v-model="activeVideo.links"
                />
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-12">
                <textarea
                  class="form-control"
                  placeholder="Описание видео"
                  v-model="activeVideo.text"
                  :disabled="mode == 'read'"
                ></textarea>
              </div>
            </div>


            <div class="vid mt-3">
                <questions
                    :questions="activeVideo.questions"
                    :id="activeVideo.id"
                    type="video"
                    :mode="mode"
                    />
                    
            </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div v-if="mode == 'edit'" class="mb-3">
          <button class="btn btn-primary" v-if="!group_edit" @click="group_edit = true">
            <i class="fa fa-edit mr-2"></i>
            Редактировать группы  
          </button>
          <button class="btn btn-primary" v-else  @click="group_edit = false">
            <i class="fa fa-save mr-2"></i>
            Сохранить группы  
          </button>
          <button class="btn btn-default" v-if="group_edit" @click="addGroup">
            <i class="fa fa-plus mr-2"></i>
            Добавить группу  
          </button>
        </div>
        <video-accordion 
          :groups="playlist.groups"
          :mode="mode"
          :group_edit="group_edit"
          @showVideo="showVideo"
          @uploadVideo="uploadVideo"
          />
      </div>
    </div>

    <b-modal
      v-model="modals.addVideo.show"
      hide-footer
      title="Добавить видео из существующих"
      size="lg"
    >
      <div class="video-search">
        <input
          type="text"
          class="search-input form-control"
          @keyup="search($event)"
        />
        <div class="items">
          <div
            v-for="video in modals.addVideo.searchVideos"
            class="item d-flex"
            @click="addVideo(video.id)"
            :key="video.id"
          >
            <img src="/video_learning/noimage.png" alt="image" />
            <p class="title">{{ video.title }}</p>
          </div>
        </div>
      </div>
    </b-modal>

    <b-modal
      v-model="modals.upload.show"
      hide-footer
      title="Загрузить видео"
      size="lg"
    >
      <div class="steps">
        <p
          :class="{ active: modals.upload.step == 1 }"
          @click="moveToStep(1)"
          class="mr-2"
        >
          <b>1. Загрузить видео ></b>
        </p>
        <p :class="{ active: modals.upload.step == 2 }" @click="moveToStep(2)">
          <b>2. Редактировать видео</b>
        </p>
      </div>
      <div v-if="modals.upload.step == 1">
        <upload-files
          :token="token"
          type="video"
          :file_types="['mp4', 'flv']"
          @onupload="onupload"
        />

        <div class="uploaded_files" v-if="modals.upload.file !== null">
          <p>
            <b
              >Загружено
              {{
                Number(modals.upload.file.size / 1024 / 1024).toFixed(3)
              }}mb</b
            >
          </p>
          <p>{{ modals.upload.file.name }}</p>
        </div>
      </div>
      <div v-if="modals.upload.step == 2">
        <div class="row mb-2" v-if="modals.upload.file !== null">
          <div class="col-md-4">
            Название
          </div>
          <div class="col-md-8">
            <input
              type="text"
              class="form-control"
              v-model="modals.upload.file.model.title"
            />
          </div>
          <div class="col-md-12">
            <p>
              <b
                >Загружено
                {{
                  Number(modals.upload.file.size / 1024 / 1024).toFixed(3)
                }}mb</b
              >
            </p>
            <p>{{ modals.upload.file.name }}</p>
          </div>
        </div>
        <div class="d-flex mt-3">
          <button class="btn mr-1" @click="saveVideo">Сохранить</button>
          <button class="btn" @click="deleteVideo">Удалить</button>
        </div>
      </div>
    </b-modal>

  </div>
</template>

<script>

import 'videojs-hotkeys'
export default {
  name: "PlaylistEdit",
  props: {
    token: String,
    id: Number,
    auth_user_id: Number,
    myvideo: Number,
    mode: String
  },
  data() {
    return {
      all_videos: [],
      activeVideo: null,
      group_edit: false,
      playlist: {
        id: 1,
        category_id: 1,
        title: "Плейлист",
        text: "<b>Плейлист</b>",
        videos: [],
      },
      modals: {
        addVideo: {
          show: false,
          searchVideos: [],
          selected: null,
        },
        upload: {
          show: false,
          step: 1,
          video: {
            title: "",
            links: "",
            text: "",
          },
          group_index: -1,
          children_index: -1,
          file: null,
        },
      },
      sidebars: {
        edit_video: {
          show: false,
        },
      },
      mylink: window.location.protocol + "//" + window.location.host + window.location.pathname.substring(0,16)
        
    };
  },
  watch: {
  
  },

  created() {
    console.log(this.myvideo);
    if(this.myvideo > 0){

     // this.activeVideo = video;
      console.log(this.id);
      
      axios
        .get("/playlists/get/" + this.id)
        .then((response) => {
          this.all_videos = response.data.all_videos;
          this.modals.addVideo.searchVideos = this.all_videos;

          this.playlist = response.data.playlist;
          
          console.log(this.playlist.videos);

          this.activeVideo = this.playlist.videos[this.myvideo-1];
          this.sidebars.edit_video.show = true;
          this.setActiveVideo();
        })
        .catch((error) => {
          alert(error);
        });

        

    } else {

      this.fetchData();
    }
    
  },

  mounted() {},
  methods: { 
    testFun(){
     alert("hello!"); 
    },
    showQuestions(v_index) {
      let questions = this.playlist.videos[v_index].questions;
      if (questions == undefined) this.playlist.videos[v_index].questions = [];
      this.modals.questions.show = true;
      this.activeVideo = this.playlist.videos[v_index];
    },

    saveOrder(evt) {
      console.log(evt.oldIndex);
      console.log(evt.newIndex);
      console.log("save order");
    },

    removeVideo(v_index) {
      if(!confirm('Вы уверены?')) return; 
      let video = this.playlist.videos[v_index];

      axios
        .post("/playlists/remove-video", {
          id: video.id,
        })
        .then((response) => {
          this.playlist.videos.splice(v_index, 1);
          this.$message.success("Исключен из плейлиста. Файл не удален");
        })
        .catch((error) => {
          alert(error);
        });
    },

    deleteVideo() {
      axios
        .post("/playlists/delete-video", {
          id: this.modals.upload.file.model.id,
        })
        .then((response) => {
          this.$message.success("Файл удален");
          this.modals.upload.file = null;
          this.modals.upload.step = 1;
        })
        .catch((error) => {
          alert(error);
        });
    },

    openControlsMenu(video) {
      console.log("openControlsMenu");
      video.show_controls = true;
    },
    
    saveVideo() {
      console.log("saveVideo");
  
      axios
        .post("/playlists/save-video", {
          id: this.playlist.id,
          video: this.modals.upload.file.model,
          group_id: this.selectedGroup().id
        })
        .then((response) => {
          this.modals.upload.step = 1;
          this.modals.upload.show = false;

          this.addVideoToPlaylist(response.data.video)
          

          this.$message.success("Добавлен");
          this.modals.upload.file = null;
        })
        .catch((error) => {
          alert(error);
        });
    },
    
    selectedGroup() {
      return this.modals.upload.children_index == -1 
        ? this.playlist.groups[this.modals.upload.group_index]
        : this.playlist.groups[this.modals.upload.group_index].children[this.modals.upload.children_index] 
    },

    addVideoToPlaylist(video) {
      this.playlist.videos.push(video);
      this.selectedGroup().videos.push(video);
    },

    uploadVideo(g, c = -1) {

      this.modals.upload.show = true;
      this.modals.upload.group_index = g;
      this.modals.upload.children_index = c;

      if(c == -1) {
        console.log('selected group', this.playlist.groups[g]);
      } else {
        console.log('selected group', this.playlist.groups[g].children[c]);
      }
    },

   updateVideo() {
      axios
        .post("/playlists/video/update", {
          id: this.playlist.id,
          video: this.activeVideo,
        })
        .then((response) => {
          this.sidebars.edit_video.show = false;
          this.$message.success("Сохранено!");
        })
        .catch((error) => {
          alert(error);
        });
    },


    saveActiveVideo() {
      console.log("saveActiveVideo");
      axios
        .post("/playlists/save-active-video", {
          id: this.playlist.id,
          title: this.modals.upload.file.title,
          name: this.modals.upload.file.name,
          size: this.modals.upload.file.size,
        })
        .then((response) => {
          this.modals.upload.step = 1;
          this.modals.upload.show = false;

          if (response.data.error) {
            this.$message.success("Не добавлен");
          } else {
            this.playlist.videos.push(response.data.video);
            this.$message.success("Добавлен");
            this.modals.upload.file = null;
          }
        })
        .catch((error) => {
          alert(error);
        });
    },

    onupload(item) {
      console.log("onupload");
      console.log(item);
      this.modals.upload.file = item;
      this.modals.upload.step = 2;
    },
    search(event) {
      this.modals.addVideo.searchVideos = this.all_videos.filter((el) => {
        console.log(el.title.toLowerCase());
        return (
          el.title.toLowerCase().indexOf(event.target.value.toLowerCase()) > -1
        );
      });
    },

    addVideo(id) {
      this.modals.addVideo.selected = id;

      axios
        .post("/playlists/add-video", {
          id: this.playlist.id,
          video_id: id,
        })
        .then((response) => {
          if (response.data.video !== null) {
            if (response.data.was_in_playlist) {
              this.$message.info("Видео уже есть в плейлисте");
            } else {
              this.playlist.videos.push(response.data.video);
              this.$message.success("Добавлен");
            }

            this.modals.addVideo.show = false;
          } else {
            this.$message.error("Не добавлен");
          }
        })
        .catch((error) => {
          alert(error);
        });
    },
    moveToStep(i) {
      if (i == 2 && this.modals.upload.file === null) {
        return "";
      }
      this.modals.upload.step = i;
    },

    showVideo(video, key) {
      console.log(video)
      this.activeVideo = video;
      this.sidebars.edit_video.show = true;
      if (history.pushState) {
          var newUrl = this.mylink.concat('/'+this.$parent.data_category, '/'+this.$parent.data_playlist+'/'+(key+1));
          history.pushState(null, null, newUrl);
      }
      else {
          console.warn('History API не поддерживает ваш браузер');
      }
      console.log(this.$parent.data_category);
    },

    closeSidebar() {
      this.sidebars.edit_video.show = false;
      if (history.pushState) {
          var newUrl = this.mylink.concat('/'+this.$parent.data_category, '/'+this.$parent.data_playlist);
          history.pushState(null, null, newUrl);
      }
      else {
          console.warn('History API не поддерживает ваш браузер');
      }
      this.activeVideo = null;
      console.log(this.$children[3].$children[0]);
      //syuda
    },

    fetchData() {
      axios
        .get("/playlists/get/" + this.id)
        .then((response) => {
          this.all_videos = response.data.all_videos;
          this.modals.addVideo.searchVideos = this.all_videos;

          this.playlist = response.data.playlist;
          
          this.setActiveVideo();
          
          
        })
        .catch((error) => {
          alert(error);
        });
    },  
    
    setActiveVideo() {

      // check playlist has videos
      if(this.playlist.videos.length > 0) {
          // set active video
          this.activeVideo = this.playlist.videos[0];
          this.setActiveGroup();
          
      } 
    },

    setActiveGroup() {
      // check playlist has videos in groups  
      console.log('text')
      console.log(this.playlist.groups.length)
      console.log(this.playlist.groups[0].videos.length > 0)
      console.log('end')
      if(this.playlist.groups.length > 0 && this.playlist.groups[0].videos.length > 0) {
        // set group opened
        this.playlist.groups[0].opened = true;
      } 
    },

    savePlaylist() {
      axios
        .post("/playlists/save", {
          playlist: this.playlist,
        })
        .then((response) => {
          this.$message.success('Сохранено');
        })
        .catch((error) => {
          alert(error);
        });
    },

    addGroup() {
      this.playlist.groups.push({
        id: 0,
        title: 'Тестовая группа',
        videos:[],
        groups: []
      });
    },
 
  },
};
</script>
