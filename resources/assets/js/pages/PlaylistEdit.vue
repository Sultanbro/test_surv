<template>
  <div class="p-3">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="h3">{{ playlist.title }}</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <draggable
          class="videos mb-4"
          tag="div"
          handle=".fa-bars"
          :list="playlist.videos"
          :group="{ name: 'g1' }"
          @end="saveOrder"
        >
          <template v-for="(video, v_index) in playlist.videos">
            <div
              class="video-block"
              :key="video.id"
              @click.stop="showVideoSettings(video)"
            >
              <div class="mover">
                <i class="fa fa-bars"></i>
              </div>
              <div class="img">
                <img src="/video_learning/noimage.png" alt="text" />
              </div>
              <div class="desc">
                <h4>{{ video.title }}</h4>
                <div class="text" v-html="video.desc"></div>
              </div>
              <div class="controls">
                <i class="fas fa-ellipsis-h"></i>
                <div class="controls-menu">
                  <div class="item" @click.stop="removeVideo(v_index)">
                    <i class="fa far fa-trash"></i>
                    <div class="text">Убрать из плейлиста</div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </draggable>
      </div>
      <div class="col-lg-6">
        <div class="d-flex justify-content-between">
          <p class="mr-2">Количество видео: {{ playlist.videos.length }}</p>
          <div class="d-flex">
            <button
              class="btn btn-sm mr-2"
              @click="modals.addVideo.show = true"
            >
              Добавить
            </button>
            <button class="btn btn-sm" @click="modals.upload.show = true">
              Загрузить видео
            </button>
          </div>
        </div>
        <div>
          <label for="title">Название</label>
          <div class="form-group">
            <input
              type="text"
              class="form-control"
              v-model="playlist.title"
              name="title"
            />
          </div>

          <div class="form-group">
            <label for="playlist_id">Категория</label>
            <select
              name="category_id"
              class="form-control"
              v-model="playlist.category_id"
            >
              <option v-for="cat in categories" :value="cat.id" :key="cat.id">
                {{ cat.title }}
              </option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="text">Текст</label>
          <textarea
            name="text"
            class="form-control"
            required
            v-model="playlist.text"
          ></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <button class="btn btn-primary" @click="savePlaylist">Сохранить</button>
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
          <div class="col-md-4">Название</div>
          <div class="col-md-8">
            <input
              type="text"
              class="form-control"
              v-model="modals.upload.file.video.title"
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

    <sidebar
      title="Редактирование видео"
      :open="sidebars.edit_video.show"
      @close="closeSidebar"
      width="50%"
    >
      <div class="fast-edit">
        <div v-if="activeVideo !== null">
          <div id="video" class="mb-3 w65"></div>

          <div class="row mb-2">
            <div class="col-md-4">Название</div>
            <div class="col-md-8">
              <input
                type="text"
                class="form-control"
                v-model="activeVideo.title"
              />
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-4">Ссылка на видео</div>
            <div class="col-md-8">
              <input
                type="text"
                class="form-control"
                v-model="activeVideo.links"
                disabled
              />
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-4">Описание</div>
            <div class="col-md-8">
              <textarea
                class="form-control"
                v-model="activeVideo.text"
              ></textarea>
            </div>
          </div>

          <div class="vid">
            <questions
              v-if="[5, 18, 157, 84].includes(auth_user_id)"
              :questions="activeVideo.questions"
              :id="activeVideo.id"
              type="video"
              mode="edit"
            />
          </div>

          <div class="d-flex mt-3">
            <button class="btn mr-1" @click="updateVideo">Сохранить</button>
          </div>
        </div>
      </div>
    </sidebar>
  </div>
</template>

<script>
export default {
  name: "PlaylistEdit",
  props: {
    token: String,
    id: Number,
    auth_user_id: Number,
  },
  data: function () {
    return {
      categories: [],
      all_videos: [],
      activeVideo: null,
      playlist: {
        id: 1,
        category_id: 1,
        title: "test",
        text: "<b>tesxt text</b>",
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
          file: null,
        },
      },
      sidebars: {
        edit_video: {
          show: false,
        },
      },
      player: null,
    };
  },
  watch: {},

  created() {
    this.fetchData();
  },

  mounted() {},
  methods: {
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
      if (confirm("Вы уверены убрать видео из плейлиста?")) {
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
      }
    },

    deleteVideo() {
      axios
        .post("/playlists/delete-video", {
          id: this.modals.upload.file.video.id,
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
          video: this.modals.upload.file.video,
          //size: this.modals.upload.file.size,
        })
        .then((response) => {
          this.modals.upload.step = 1;
          this.modals.upload.show = false;

          this.playlist.videos.push(response.data.video);
          this.$message.success("Добавлен");
          this.modals.upload.file = null;
        })
        .catch((error) => {
          alert(error);
        });
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

    showVideoSettings(video) {
      this.activeVideo = video;
      this.sidebars.edit_video.show = true;
      var player = new Playerjs({
        id: "video",
        poster: "",
        file: video.links,
      });
      console.log(player.url);
      console.log(player.file);
    },

    fetchData() {
      axios
        .get("/playlists/get/" + this.id)
        .then((response) => {
          this.all_videos = response.data.all_videos;
          this.modals.addVideo.searchVideos = this.all_videos;

          this.playlist = response.data.playlist;
          this.categories = response.data.categories;
        })
        .catch((error) => {
          alert(error);
        });
    },

    savePlaylist() {
      axios
        .post("/playlists/save", {
          playlist: this.playlist,
        })
        .then((response) => {
          window.location.href = "/video_playlists";
        })
        .catch((error) => {
          alert(error);
        });
    },

    closeSidebar() {
      this.sidebars.edit_video.show = false;
      this.activeVideo = null;
    },
  },
};
</script>
