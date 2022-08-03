<template>
  <div class="video-playlist">

    <!-- Header -->
   
    <div class="d-flex mb-3"  v-if="!is_course">
      <div class="d-flex jcsb mb-1 left f-70">
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

          <!-- playlist description -->
          <div class="form-group mt-2">
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
      </div>
      <div class="right f-30 pl-4">
            <img class="book-img mb-2"
              v-if="playlist.img != ''"
              :src="playlist.img"
              />
            <b-form-file
              v-if="mode == 'edit'"
              ref="edit_img"
              v-model="file_img"
              :state="Boolean(file_img)"
              placeholder="Выберите или перетащите файл сюда..." 
              drop-placeholder="Перетащите файл сюда..."
              class="mt-3"
              ></b-form-file> 
        </div>
    </div>

    <div class="row">

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

            <div class="row mb-2" v-if="mode == 'edit'">
              <div class="col-md-12">
                <button class="btn btn-primary" @click="saveActiveVideoFast">Сохранить</button>
              </div>
            </div>

 
            <div class="vid mt-3">
                <questions 
                    v-if="activeVideo.questions.length > 0 && mode != 'edit'"
                    :questions="activeVideo.questions"
                    :course_item_id="course_item_id"
                    :id="activeVideo.id"
                    :key="refreshTest"
                    type="video"
                    :pass_grade="activeVideo.pass_grade"
                     :pass="activeVideo.item_model !== null"
                    @passed="passedTest()"
                    :mode="mode"
                    @nextElement="nextElement"
                    />
                <!-- v-if="(activeVideo.questions.length == 0 || activeVideo.item_model != null) && mode == 'read'" -->
                <button class="next-btn btn btn-primary" v-if="activeVideo.questions.length == 0 && mode == 'read'"
                  @click="nextElement()">
                  Следующее видео
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
          @order-changed="formMap"
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
        <div class=" mb-3">
          <p class="mt-2 font-bold">Название видео</p>
          <input type="text" class="form-control" v-model="activeVideo.title" ref="activevideo_input" />
        </div>
         <div class="row mb-2" v-if="mode == 'edit'">
          <div class="col-md-12">
            <button class="btn btn-primary" @click="saveActiveVideoFast">Сохранить</button>
          </div>
        </div>

        
        <questions 
          :questions="activeVideo.questions"
          :id="activeVideo.id"
          :pass_grade="activeVideo.pass_grade"
          type="video"
          :key="refreshTest"
          @changePassGrade="changePassGrade"
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
    },
    all_stages: {
      default: 0
    },
    completed_stages: {
      default: 0
    },
  },
  
  data() {
    return {
      ids: [],
      video_changed: 1,
      activeVideo: null,
      activeVideoLink: '',
      refreshTest: 1, //key
      file_img: null,
      item_models: [],
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
   this.fetchData();
  },

  mounted() {},

  methods: { 

    passedTest() {
      if(this.activeVideo.item_model == null) {
        this.setVideoPassed()
      }

      let i = this.item_models.findIndex(im => im.item_id == this.activeVideo.id);
      if(i == -1) this.item_models.push({ 
        item_id: this.activeVideo.id,
        status: 1
      });
      
      this.connectItemModels(this.playlist.groups)
     
      ////

        //this.nextElement()
    },

    nextElement() {

      window.scrollTo(0,0); // scroll to top
      
      if(this.activeVideo.item_model == null) {
        this.setVideoPassed()
      }

      /// 
      
      let i = this.item_models.findIndex(im => im.item_id == this.activeVideo.id);
      if(i == -1) this.item_models.push({
        item_id: this.activeVideo.id,
        status: 1
      });
      
      this.connectItemModels(this.playlist.groups)
      
      ////
      
      let index = this.ids.findIndex(el => el.id == this.activeVideo.id); 
 
      // find next element 
      if(index != -1 && this.ids.length - 1 > index) {

        this.showVideo({
          id: this.ids[index + 1].id,
        });

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
          questions: this.activeVideo.questions,
          all_stages: this.all_stages,
          completed_stages: this.completed_stages,
        })
        .then((response) => {
          setTimeout(loader.hide(), 500);
          this.activeVideo.item_model = response.data.item_model;
          this.$emit('changeProgress');
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
          this.$toast.success("Исключен из плейлиста. Файл не удален");
        })
        .catch((error) => {
          alert(error);
        });
    },

    openControlsMenu(video) {
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
          this.$toast.success("Сохранено!");
        })
        .catch((error) => {
          alert(error);
        });
    },

    saveActiveVideoFast() {
      axios
        .post("/playlists/save-video-fast", {
          id: this.activeVideo.id,
          title: this.activeVideo.title,
        })
        .then((response) => {

          let i = this.ids.findIndex(el => el.id == this.activeVideo.id);
          if(i != -1) {
            let video = this.findItem(this.ids[i]);
            video.title = this.activeVideo.title;
          }
        

          this.$toast.success("Сохранено");
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
            this.$toast.success("Не добавлен");
          } else {
            this.playlist.videos.push(response.data.video);
            this.$toast.success("Добавлен");
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

    showVideo(video, autoplay = true) {
    
      let loader = this.$loading.show();

       axios
        .post("/playlists/video", {
          id: video.id,
          course_item_id: this.course_item_id
        })
        .then((response) => {
           loader.hide()

           this.activeVideo = response.data.video;
             this.activeVideoLink = this.activeVideo.links;
         
            this.refreshTest++

            this.setActiveGroup();
          if(autoplay) {
               this.video_changed++;
             
            
          }
        })
        .catch((error) => {
          loader.hide()
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
  
    showTests(video, input_focus = false) {
      const NO_AUTOPLAY = false;
      this.showVideo(video, NO_AUTOPLAY);
      this.show_tests = true;
      this.$refs.activevideo_input.focus()
    },

    moveTo(video) {
      this.$toast.info('Переместить: ' + video.title);
    }, 

    fetchData() {
      axios
        .post("/playlists/get/", {
          id: this.id,
          course_item_id: this.course_item_id
        })
        .then((response) => {
          this.playlist = response.data.playlist;
          this.item_models = response.data.item_models;
          
          
          this.formMap();
                
          this.connectItemModels(this.playlist.groups);
                     
          this.setActiveVideo();

        })
        .catch((error) => {
          alert(error);
        });
    },  
    
    connectItemModels(groups) {
      groups.forEach((el, e) => {

        el.videos.forEach((vid, v) => {
          let i = this.item_models.findIndex(im => im.item_id == vid.id);
          if(i != -1) {
            vid.item_model = this.item_models[i];
            //this.item_models.splice(i,1);
          } else {
            vid.item_model = null;
          }
        });

        
        if(el.children !== undefined) {
          this.connectItemModels(el.children)
        }
        
      });

    
    
    },

    changePassGrade(grade) {
      console.log('pass grade')

      this.activeVideo.pass_grade = grade;
      let len = this.activeVideo.questions.length;

      if(grade > len) this.activeVideo.pass_grade = len;
      if(grade < 1) this.activeVideo.pass_grade = 1;
    },

    returnArray(items, indexes = []) { 
      items.forEach((item, i_index) => {

        let arr = [...indexes, i_index];

        item.videos.forEach((video, v) => {
          this.ids.push({
            id: video.id,
            i: [...arr, v]
          })
        });

        if(item.children !== undefined) this.returnArray(item.children, arr);
      });
    },

    formMap() {
      this.ids = [];
      this.returnArray(this.playlist.groups);
    },

    setActiveVideo() {
      if(this.myvideo > 0) {

        // find element 
        let index = this.ids.findIndex(el => el.id == this.myvideo);
        if(index != -1) {
          this.activeVideo = this.findItem(this.ids[index]);
        }

      } else if(this.playlist.groups[0].videos.length > 0) { 
          // set active video
          this.activeVideo = this.playlist.groups[0].videos[0];
          this.activeVideoLink = this.activeVideo.links;
         
      } else if(this.ids.length > 0) {
        this.activeVideo = this.findItem(this.ids[0]);
      }
      
      this.showVideo(this.activeVideo);
      
    
    },

    findItem(el) {

      let x = this.playlist;
      let found = false;

      for (let i = 0; i < el.i.length; i++) {
        if(i == 0) {
          x = x.groups[el.i[i]]
        } else if(el.i.length - 1 != i) {
          x = x.children[el.i[i]]
        } else {
          found = true;
          x = x.videos[el.i[i]]
        }
      } 

      return found ? x : null;
    },
    
    setActiveGroup() {
      
      console.log('setActiveGroup')

      // close all
      this.playlist.groups.forEach(g=>{
        g.opened = false;
        g.children.forEach(c=>{
          c.opened = false;
          c.children.forEach(d=>{
            d.opened = false;
          });
        });
      })

      let index = this.ids.findIndex(el => el.id == this.activeVideo.id);

      if(index != -1) {
        let l = this.playlist;

        for(let i=0;i<this.ids[index].i.length - 1;i++){

          if(i==0){
            l = l.groups[this.ids[index].i[i]];
          } else {
            l = l.children[this.ids[index].i[i]];
          }

          l.opened = true;

        }
       
      }


    },

    savePlaylist() {

      let loader = this.$loading.show();

      let formData = new FormData();
          formData.append('file', this.file_img);
          formData.append('playlist', JSON.stringify(this.playlist));

      axios.post( '/playlists/save', formData)
        .then((response) => {
          this.$toast.success('Сохранено');
          if(response.data !== '') this.playlist.img = response.data;
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    saveGroups() {

      axios
        .post("/playlists/groups/save", {
          playlist: this.playlist,
        })
        .then((response) => {
          this.$toast.success('Сохранено');

          this.playlist.groups = response.data.groups;
        })
        .catch((error) => {
          alert(error);
        });
      
    }
 
  },
};
</script>
