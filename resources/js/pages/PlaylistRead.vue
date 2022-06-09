<template>
  <div class="video-playlist">
    <div class="d-flex jcsb mb-4">
      <div class="s">
        <h5 class="mb-0">{{ playlist.title }}</h5>
      </div>
      <div class="d-flex align-items-start">
        <div class="btn btn-grey" @click="$emit('back')">
          <i class="fa fa-arrow-left"></i>
          <span>Вернуться к разделам</span>
        </div>
      </div>
    </div>

   
    <div class="row">

      <div class="col-lg-8">
            <div class="block  br" v-if="activeVideo">
                <!-- <vue-core-video-player :src="activeVideo.links"  class="mb-3 w65"></vue-core-video-player> -->
                <div class="description">

                  <div class="video-tit mb-2">
                      {{ activeVideo.title }}
                    </div>
                    <div class="text js_text">
                        {{ activeVideo.text }}
                    </div>

                    <div class="info bordero paddingo mt-3">
                        <p class="title">{{ playlist.title }}</p>
                        <p>{{ playlist.text }}</p>
                    </div>
                    
                </div>

                <div class="vid mt-3">
                    <questions
                        :questions="activeVideo.questions"
                        :id="activeVideo.id"
                        type="video"
                        mode="read"
                        />
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="block">
                <div class="video-list">
                  <div class="video-title"
                    v-for="(video, index) in playlist.videos" 
                    @click="showVideo(index)"
                    :class="{'active': activeVideo.id == video.id}"
                    >
                    {{ video.title }}
                  </div>
                </div>

    

            </div>
        </div>



    </div>
  </div>
</template>

<script>
export default {
  name: "PlaylistRead",
  props: {
    id: Number,
    auth_user_id: Number,
  },
  data: function() {
    return {
      activeVideo: null,
      playlist: {
        id: 1,
        category_id: 1,
        title: "Плейлист",
        text: "<b>Плейлист</b>",
        videos: [],
      },
    };
  },
  watch: {
    id(val) {
      this.fetchData();
    },
  },

  created() {
    this.fetchData();
  },

  mounted() {},
  methods: { 

    fetchData() {
      axios
        .get("/playlists/get/" + this.id)
        .then((response) => {
          this.playlist = response.data.playlist;

          if(this.playlist.videos.length > 0) this.activeVideo = this.playlist.videos[0];
          this.categories = response.data.categories;
        })
        .catch((error) => {
          alert(error);
        });
    },

    showVideo(i) {
      this.activeVideo = this.playlist.videos[i];
    }

  },
};
</script>
