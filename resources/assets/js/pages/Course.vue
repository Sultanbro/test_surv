<template>
<div class="p-3 course">

   
    <div>
        <input type="text" v-model="course.name" class="mb-3" placeholder="Название курса">
    </div>

    <draggable 
        class="dragArea" 
        tag="ul"
        handle=".fa-bars"
        :list="course.items"
        :group="{ name: 'g1' }"
        @end="saveOrder">
        <template v-for="(el, e_index) in course.items">
            <li 
            class="chapter"
            :id="el.id"
            @mouseover="hover = true"
            @mouseleave="hover = false">
            <div class="d-flex aic mb-2">
            <div class="handles" >
                <i class="fa fa-bars mover" v-if="hover"></i>
                <i class="fa fa-caret-right pointer" v-else></i>
            </div>
            <p @click="toggleOpen(el)" class="mb-0">{{ el.title }}</p>
            <i class="fa fa-trash pointer ml-2" @click.stop="deleteItem(e_index)"></i>
            </div>
        </li>
        </template>
    </draggable>


    <div class="btns">
        <div class="d-flex mb-2">
            
            <select 
                class="form-control form-control-sm"
                v-model="newItem"
                placeholder="Выберите из списка">
                <option v-for="(ai, ai_index) in all_items" :value="ai_index">{{ ai.title }}</option>
            </select>

            <button class="btn btn-primary" @click="addItem">
                Добавить
            </button>
            
        </div>
        
        <button class="btn btn-success" @click="saveCourse">Сохранить</button>
    </div>
</div>
</template>

<script>
export default {
  name: "Course",
  props: ['id'],
  data() {
    return {
      test: 'dsa',
      hover: false,
      newItem: null,
      all_items: [],
      course: {
          id: 0,
          items: []
      }
    };
  },
  created() {
      this.get();
  },
  mounted() {},
  methods: {
      get() {
        let loader = this.$loading.show();
        axios
            .post("/admin/courses/get-item", {
                id: this.id,
            })
            .then((response) => {
                this.course = response.data.course;
                this.all_items = response.data.all_items;
                loader.hide();
            })
            .catch((error) => {
                loader.hide();
                alert(error);
            });
      },

      toggleOpen(el) {

      },

      saveOrder(e) {

      },

        deleteItem(i) {
            this.course.items.splice(i,1);
        },
      addItem() {
        this.course.items.push(this.all_items[this.newItem]);
        this.newItem = null;
      },

      saveCourse() {
          let loader = this.$loading.show();
            axios
            .post("/admin/courses/save", {
                course: this.course,
            })
            .then((response) => {
                this.$message.success('Успешно сохранено!');
                loader.hide();
            })
            .catch((error) => {
                loader.hide();
                alert(error);
            });
      }
  },
};
</script>
