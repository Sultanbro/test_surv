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

    <div class="info mb-3">
      <div class="d-flex">
        <b>Автор:</b>
        <p>{{ course.author }}</p>
      </div>
      <div class="d-flex">
        <b>Создано:</b>
        <p>{{ course.created }}</p>
      </div>
    </div>
    <div class="d-flex mb-3">
      <div class="w-full">
        <textarea
          v-model="course.text"
          :style="'height:285px'"
          class="form-control"
          placeholder="Описание курса" 
        ></textarea>
      </div>
  
      <!-- profile image -->
      <div class="ml-3"> 

          <croppa
            v-model="myCroppa"
            :width="250"
            :height="250"
            :canvas-color="'default'"
            :placeholder="'Выберите изображение'"
            :placeholder-font-size="0"
            :placeholder-color="'default'"
            :accept="'image/*'"
            :file-size-limit="0"
            :quality="2"
            :zoom-speed="20"
            :initial-image="image"
            :key="croppa_key"
          ></croppa>

          <button
            style="width: 250px; display: block"
            class="btn btn-success"
            @click="saveCropped"
          >
            Обрезать и сохранить
          </button>
      
      </div>

    </div>

    <div class="items">
      <div class="d-flex ">
        <p class="title mr-3">Курс состоит из ({{ course.elements.length }}):</p>
        <div class="btns w-50 pr-5">
          <div class="d-flex mb-2">
           
            <superselect-alt
              :values="course.elements"
              class="w-full mb-4" 
              :key="1"
              :hide_selected="true"
              />

          </div>
        </div>
      </div>

      <draggable
        class="dragArea ml-0 mr-5"
        tag="ul"
        handle=".fa-bars"
        :list="course.elements"
        :group="{ name: 'g1' }"
        @end="saveOrder"
      >
        <template v-for="(el, e_index) in course.elements">
          <li class="chapter opened" :id="el.id">
            <div class="d-flex aic mb-2">
              <div class="handles">
                <i class="fa fa-bars mover"></i>
                <i class="fa fa-caret-right pointer shower"></i>
              </div>
              <p @click="toggleOpen(el)" class="mb-0">{{ el.name }}</p>
              <i
                class="fa fa-trash pointer ml-2"
                @click.stop="deleteItem(e_index)"
              ></i>
            </div>
          </li>
        </template>
      </draggable>


      <div class="mt-3 pr-5">
        Курс проходят:

        <superselect 
            :values="course.targets"
            class="w-50 mb-4" 
            :key="1"
            :select_all_btn="true" />
        
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
      myCroppa: {},
      newItem: null,
      users: [], 
      course: {
        id: 0,
        elements: [],
        img: ''
      },
      image: '/users_img/noavatar.png',
      croppa_key: 1
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
      let loader = this.$loading.show()
      axios
        .post("/admin/courses/get-item", {
          id: this.id,
        })
        .then((response) => {
          loader.hide()
          this.course = response.data.course;
          this.image = this.course.img;
          this.croppa_key++;
          console.log(this.image)
        })
        .catch((error) => {
          loader.hide()
          alert(error);
        });
    },

    saveCropped() {
      let loader = this.$loading.show();
      const formData = new FormData();

      let _this = this;
      this.myCroppa.generateBlob(
        (blob) => {
          formData.append("file", blob);
          formData.append("course_id", _this.course.id);
          axios
            .post("/admin/courses/upload-image", formData)
            .then(function (res) {
              _this.course.img = response.data.img;
              _this.$toast.success('Сохранено');
              loader.hide();
            })
            .catch(function (err) {
              console.log(err, "error");
               loader.hide();
            });
        },
        "image/jpeg",
        0.8
      ); // 80% compressed jpeg file
    },

    toggleOpen(el) {},

    saveOrder(e) {},

    deleteItem(i) {
      this.course.elements.splice(i, 1);
    },
    
    

    addTag(newTag) {
      console.log(newTag)
      const tag = {
        email: newTag,
        ID: newTag,
      };
      this.users.push(tag);
    },

    saveCourse() {
      let loader = this.$loading.show();
      axios
        .post("/admin/courses/save", {
          course: this.course,
        })
        .then((response) => {
          this.$toast.success("Успешно сохранено!");
          loader.hide();
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },

    limitText(count) {
      return `и еще ${count}`
    }
  },
};
</script>
