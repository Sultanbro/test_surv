<template>
  <div class="video-playlist">

    <!-- Header -->
    <div class="d-flex jcsb mb-1" v-if="!is_course">
      <div class="s w-full">
        <div class="d-flex">
         <input
            v-if="mode == 'edit'"
            type="text"
            class="form-control form-control-sm w-full p-itle mb-0 mr-2"
            v-model="playlist.title"
            name="title"
          />
          <p v-else class="p-title mb-0"> {{ playlist.title }} </p>
        </div>
         
      </div>
    </div>

   
    <div class="row">

      <!-- playlist description -->
      <div class="col-lg-12" v-if="!is_course">
        <div class="form-group">
          <textarea
            v-if="mode == 'edit'"
            name="text"
            class="form-control textarea h-70"
            required
            title="Описание плейлиста"
            placeholder="Описание плейлиста"
            v-model="playlist.text"
          ></textarea>
          <p v-else class="p-desc">{{ playlist.text }}</p>
        </div>
      </div>  
       
      <!-- Player and test questions -->
      <div class="col-lg-6 pr-0">
        <div class="block  br" v-if="activeVideo != null">
            <v-player :src="activeVideoLink" :key="video_changed" />
           
            <div class="row mb-2 mt-3">
              <div class="col-md-12">
                <input
                  type="text"
                  v-if="mode == 'edit'"
                  class="form-control"
                  v-model="activeVideo.title"
                  :disabled="mode == 'read'"
                />
                <p v-else class="v-title mb-0"> {{ activeVideo.title }} </p>
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-md-12">
                <textarea
                  class="form-control"
                  v-if="mode == 'edit'"
                  placeholder="Описание видео"
                  v-model="activeVideo.text"
                  :disabled="mode == 'read'"
                ></textarea>
                <p v-else class="v-desc"> {{ activeVideo.title }} </p>
              </div>
            </div>

 
            <div class="vid mt-3">
                <questions 
                    v-if="activeVideo.questions.length > 0 || mode == 'edit'"
                    :questions="activeVideo.questions"
                    :id="activeVideo.id"
                    type="video"
                    @passed="passedTest()"
                    :mode="mode"
                    />
                
                <button class="next-btn btn btn-primary" v-if="is_course && (activeVideo.questions.length == 0 || activeVideo.item_models.length > 0)"
                  @click="nextElement()">
                  Продолжить курс
                  <i class="fa fa-angle-double-right ml-2"></i>
                </button>
                    
            </div>
        </div>
      </div>

      <!-- nav accordion -->
      <div class="col-lg-6">
        <div v-if="mode == 'edit'" class="mb-3">
          <button class="btn btn-primary" v-if="!group_edit" @click="group_edit = true">
            <i class="fa fa-edit mr-2"></i>
            Редактировать группы  
          </button>
          <button class="btn btn-primary" v-else  @click="saveGroups()">
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
          :active="activeVideo ? activeVideo.id : -1"
          @showVideo="showVideo"
          @uploadVideo="uploadVideo"
          :is_course="is_course"
          />
      </div>
    </div>


    <!-- Upload Video -->
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
          :id="playlist.id"
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
    mode: String,
    enable_url_manipulation: {
      default: true
    },
    is_course: {
      default: false
    },
    course_item_id: {
      default: 0
    }
  },
  
  data() {
    return {
      video_changed: 1,
      activeVideo: null,
      activeVideoLink: '',
      group_edit: false,
      playlist: {
        id: 1,
        category_id: 1,
        title: "Плейлист",
        text: "<b>Плейлист</b>",
        videos: [],
      },
      modals: {
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

      axios
        .get("/playlists/get/" + this.id)
        .then((response) => {
   
          this.playlist = response.data.playlist;
          this.activeVideo = this.playlist.videos.filter(video => video.id === this.myvideo)[0];
          this.activeVideoLink = this.activeVideo.links;

          if(this.playlist.groups[this.activeVideo.group_id] != null){
            this.playlist.groups[this.activeVideo.group_id].opened = true;
          }

          this.sidebars.edit_video.show = true;
          
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

    passedTest() {
      if(this.is_course) {
        this.activeVideo.item_models.push({status: 1});
        // axios passed
      }
    },

    nextElement() {
      this.setVideoPassed()

      // create array of video ids
      let arr = [];
      this.playlist.groups.forEach((group, g_index) => {
          group.videos.forEach((el, v_index) => {
            arr.push({
              id: el.id,
              g: g_index,
              c: -1,
              v: v_index,
            })
          })
          
          if(group.children !== undefined) group.children.forEach((c, c_index) => c.videos.forEach((el, v_index) => {
            arr.push({
              id: el.id,
              g: g_index,
              c: c_index,
              v: v_index
            })
          }))
      });


      let index = arr.findIndex(el => el.id == this.activeVideo.id); 
 
      // find next element 
      if(index != -1 && arr.length - 1 > index) {

        let el;
        let i = arr[index + 1];
        if(i.c == -1) {
          el = this.playlist.groups[i.g].videos[i.v];
        } else {
          el = this.playlist.groups[i.g].children[i.c].videos[i.v];
        }

        this.activeVideo = el;
        this.activeVideoLink = this.activeVideo.links 
        el.item_models.push({status: 1});  

      } else {
        // move to next course item
        this.$parent.after_click_next_element();
      }
    },

    setVideoPassed() {
      axios
        .post("/my-courses/pass", {
          id: this.activeVideo.id,
          type: 2,
          course_item_id: this.course_item_id,
        })
        .then((response) => {
         // this.activeVideo.item_models.push(response.data.item_model);
        })
        .catch((error) => {
          alert(error);
        });
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

    moveToStep(i) {
      if (i == 2 && this.modals.upload.file === null) {
        return "";
      }
      this.modals.upload.step = i;
    },

    showVideo(video, key) {
    
      this.activeVideo = video;
      
       axios
        .post("/playlists/video", {
          id: video.id,
        })
        .then((response) => {
          this.activeVideoLink = response.data.links;
          this.video_changed++;
        })
        .catch((error) => {
          alert(error);
        });


      this.sidebars.edit_video.show = true;

      if(this.enable_url_manipulation)
      {
        if (history.pushState) {
          var newUrl = this.mylink.concat('/'+this.$parent.data_category, '/'+this.$parent.data_playlist+'/'+(video.id));
          history.pushState(null, null, newUrl);
        } else {
            console.warn('History API не поддерживает ваш браузер');
        }
      } 
     

    },

    closeSidebar() {
      this.sidebars.edit_video.show = false;

      if(this.enable_url_manipulation)
      {
        if (history.pushState) {
            var newUrl = this.mylink.concat('/'+this.$parent.data_category, '/'+this.$parent.data_playlist);
            history.pushState(null, null, newUrl);
        } else {
            console.warn('History API не поддерживает ваш браузер');
        }
      }
      
      this.activeVideo = null;
      console.log(this.$children[3].$children[0]);
      //syuda
    },

    fetchData() {
      axios
        .get("/playlists/get/" + this.id)
        .then((response) => {
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
          this.activeVideoLink = this.activeVideo.links;
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
        children: []
      });
    },


    saveGroups() {
      this.group_edit = false

      axios
        .post("/playlists/groups/save", {
          playlist: this.playlist,
        })
        .then((response) => {
          this.$message.success('Сохранено');

          this.playlist.groups = response.data.groups;
        })
        .catch((error) => {
          alert(error);
        });
      
    }
 
  },
};
</script>
