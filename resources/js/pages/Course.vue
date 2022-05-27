<template>
  <div class="p-3 course">
    <div class="d-flex relative align-items-start">
      <div class="w-full namer">
        <input
          type="text"
          v-model="course.name"
          class="mb-3 name"
          placeholder="Название курса"
        />
        <i class="fa fa-edit"></i>
      </div>
      <button class="btn btn-success ml-3" @click="saveCourse">
        Сохранить
      </button>
    </div>

    <div class="d-flex mb-3">
      <div class="w-full">
        <textarea
          v-model="course.text"
          class="form-control"
          placeholder="Описание курса"
        ></textarea>
      </div>
      <div class="img ml-3">
        <input
          type="file"
          ref="file"
          style="display: none"
          @change="onFileChange($event)"
        />
        <img class="course-img" :src="course.img" />
        <button @click="$refs.file.click()" class="btn">
          <i class="fa fa-edit"></i>
        </button>
      </div>
    </div>

    <div class="items">
      <div class="d-flex">
        <p class="title mr-3">Курс состоит из ({{ course.items.length }}):</p>
        <div class="btns">
          <div class="d-flex mb-2">
            <select
              class="form-control form-control-sm mr-3"
              v-model="newItem"
              placeholder="Выберите из списка"
            >
              <option v-for="(ai, ai_index) in all_items" :value="ai_index">
                {{ ai.title }}
              </option>
            </select>

            <button class="btn btn-primary" @click="addItem">Добавить</button>
          </div>
        </div>
      </div>

      <draggable
        class="dragArea ml-0"
        tag="ul"
        handle=".fa-bars"
        :list="course.items"
        :group="{ name: 'g1' }"
        @end="saveOrder"
      >
        <template v-for="(el, e_index) in course.items">
          <li class="chapter opened" :id="el.id">
            <div class="d-flex aic mb-2">
              <div class="handles">
                <i class="fa fa-bars mover"></i>
                <i class="fa fa-caret-right pointer shower"></i>
              </div>
              <p @click="toggleOpen(el)" class="mb-0">{{ el.title }}</p>
              <i
                class="fa fa-trash pointer ml-2"
                @click.stop="deleteItem(e_index)"
              ></i>
            </div>
          </li>
        </template>
      </draggable>


      <div class="mt-3">
        Курс проходят:

        <multiselect
              v-model="course.users"
              :options="users"
              :multiple="true"
              :close-on-select="false"
              :clear-on-select="true"
              :preserve-search="true"
              placeholder="Выберите"
              label="EMAIL"
              track-by="EMAIL"
              :taggable="true"
              :limit="3"
              :limit-text="limitText"
              @tag="addTag"
            >
            </multiselect>

     
        </multiselect>
        <div v-for="user in course.users">{{ user }}</div>
       
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Course",
  props: ["id"],
  data() {
    return {
      test: "dsa",
      hover: false,
      file: null,
      newItem: null,
      all_items: [],
      users: [],
      course: {
        id: 0,
        items: [],
      },
    };
  },
  created() {
    this.get();
  },
  watch: {
    id(val) {
      this.get();
    },
  },
  mounted() {},
  methods: {
    get() {
      axios
        .post("/admin/courses/get-item", {
          id: this.id,
        })
        .then((response) => {
          this.course = response.data.course;
          this.all_items = response.data.all_items;
          this.users = response.data.users;
        })
        .catch((error) => {
          alert(error);
        });
    },

    toggleOpen(el) {},

    saveOrder(e) {},

    deleteItem(i) {
      this.course.items.splice(i, 1);
    },
    addItem() {
      if (this.newItem !== null) {
        this.course.items.push(this.all_items[this.newItem]);
        this.newItem = null;
      } 
    },

    addTag(newTag) {
      console.log(newTag)
      const tag = {
        EMAIL: newTag,
        ID: newTag,
      };
      this.users.push(tag);
    },

    uploadFile() {
      let formData = new FormData();
      formData.append("file", this.file);
      formData.append("course_id", this.course.id);

      let _this = this;
      axios
        .post("/admin/courses/upload-image", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        })
        .then(function (response) {
          _this.course.img = response.data.img;
        })
        .catch(function (e) {
          alert(e);
        });
    },

    saveCourse() {
      let loader = this.$loading.show();
      axios
        .post("/admin/courses/save", {
          course: this.course,
        })
        .then((response) => {
          this.$message.success("Успешно сохранено!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.file = files[0];
      this.uploadFile();
    },

    limitText(count) {
      return `и еще ${count}`
    }
  },
};
</script>
