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
                    v-if="activeVideo.questions.length > 0 && mode != 'edit'"
                    :questions="activeVideo.questions"
                    :id="activeVideo.id"
                    :key="refreshTest"
                    type="video"
                    :pass_grade="activeVideo.pass_grade"
                    @passed="passedTest()"
                    :mode="mode"
                    />
                
                <button class="next-btn btn btn-primary" v-if="(activeVideo.questions.length == 0 || activeVideo.item_model != null)"
                  @click="nextElement()">
                  Продолжить курс
                  <i class="fa fa-angle-double-right ml-2"></i>
                </button>
                    
            </div>
        </div>
      </div>

      <!-- nav accordion -->
      <div class="col-lg-6">
        <video-accordion 
          ref="accordion"
          :groups="playlist.groups"
          :playlist_id="playlist.id"
          :mode="mode"
          :token="token"
          :active="activeVideo ? activeVideo.id : -1"
          :is_course="is_course"
          @showVideo="showVideo"
          @showTests="showTests"
          />
          
      </div>
    </div>


    <!-- edit tests -->
    <sidebar
      title="Редактировать вопросы к видео"
      :open="show_tests && activeVideo != null"
      @close="show_tests = false"
      width="70%"
    >
      <div class="p-3" v-if="activeVideo != null">
        <p class="mt-2 mb-3"><b>{{ activeVideo.title }}</b></p>
        <div class="d-flex aic pass__ball">
          <p class="mr-3" style="width:200px">Проходной балл в процентах (0 - 100):</p>
          <input class="form-control mb-3" v-model="activeVideo.pass_grade" type="number" :min="0" :max="100" @change="checkPassGrade" />
        </div>
        <questions 
          :questions="activeVideo.questions"
          :id="activeVideo.id"
          :pass_grade="activeVideo.pass_grade"
          type="video"
          @changePassGrade="checkPassGrade"
          :mode="'edit'"
          />
      </div>
    </sidebar>

   
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
      refreshTest: 1, //key
      playlist: {
        id: 1,
        category_id: 1,
        title: "",
        text: "",
        videos: [],
      },
      activeGroup: {
        id: 0, // group id
        i: [] // indexes
      },
      show_tests: false, // sidebar
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
          }else{
            this.playlist.groups.forEach((group, index) => {
              if(group.videos.length != 0){
                group.videos.forEach((video, index1) => {
                  if(video.id == this.myvideo){
                    this.playlist.groups[index].opened = true;
                  }
                });
              }else{
                group.children.forEach((child, index1) => {
                  if(child.videos.length != 0){
                    child.videos.forEach((video, index2) => {
                      if(video.id == this.myvideo){
                        this.playlist.groups[index].opened = true;
                        this.playlist.groups[index].children[index1].opened = true;
                      }
                    });
                  }
                });
              }
            })
            
          }
          
          
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
        this.activeVideo.item_model.status = 1
        // axios passed
      }
    },

    nextElement() {

      if(this.activeVideo.item_model != null) {
        this.setVideoPassed()
      }
      
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
        el.item_model.status = 1

      } else {
        // move to next course item
        this.$parent.after_click_next_element();
      }
    },

    setVideoPassed() {
      let loader = this.$loading.show();
      axios
        .post("/my-courses/pass", {
          id: this.activeVideo.id,
          type: 2,
          course_item_id: this.course_item_id,
        })
        .then((response) => {
          setTimeout(loader.hide(), 500);
          // this.activeVideo.item_model.push(response.data.item_model);
        })
        .catch((error) => {
           loader.hide();
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


    openControlsMenu(video) {
      console.log("openControlsMenu");
      video.show_controls = true;
    },
    
  
    
    selectedGroup() {
      return this.modals.upload.children_index == -1 
        ? this.playlist.groups[this.modals.upload.group_index]
        : this.playlist.groups[this.modals.upload.group_index].children[this.modals.upload.children_index] 
    },

    findLocation(id) {
      let o = {
        id: id,
        i: []
      };

      this.activeGroup = o; // TODO
      return o;
    },

    uploadVideo() {
      this.$refs.accordion.uploadVideo(0);
    },

    addGroup() {
      this.$refs.accordion.addGroup(-1)
    },
        
        
    deleteGroup(id) {

      let loc = this.findLocation(id);

        if(c == -1) {
            this.groups.splice(g, 1);
        } else {
            this.groups[g].children.splice(c, 1);
        }
    },

   updateVideo() {
      axios
        .post("/playlists/video/update", {
          id: this.playlist.id,
          video: this.activeVideo,
        })
        .then((response) => {
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

    search(event) {
      this.modals.addVideo.searchVideos = this.all_videos.filter((el) => {
        console.log(el.title.toLowerCase());
        return (
          el.title.toLowerCase().indexOf(event.target.value.toLowerCase()) > -1
        );
      });
    },


    showVideo(video, key, autoplay = true) {
    
      this.activeVideo = video;
      
       axios
        .post("/playlists/video", {
          id: video.id,
        })
        .then((response) => {
          if(autoplay) {
            this.activeVideoLink = response.data.links;
            this.video_changed++;
            this.refreshTest++
          }
        })
        .catch((error) => {
          alert(error);
        });

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

    showTests(video, key) {
      const NO_AUTOPLAY = false;
      this.showVideo(video, key, NO_AUTOPLAY);
      this.show_tests = true;
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
    
    checkPassGrade() {
      console.log('pass grade')
      let len = this.activeVideo.questions.length;
      let min = len != 0 ? Number((100 / len).toFixed()) : 100;

      if(this.activeVideo.pass_grade > 100) this.activeVideo.pass_grade = 100;
      if(this.activeVideo.pass_grade < min) this.activeVideo.pass_grade = Number(min);
    },

    setActiveVideo() {

      // check playlist has videos
      if(this.playlist.groups[0].videos.length > 0) {
          // set active video
          this.activeVideo = this.playlist.groups[0].videos[0];
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

    saveGroups() {

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
