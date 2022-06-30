<template>
  <div class="questions" :class="{'hide': mode == 'read' && (questions === undefined || questions.length == 0)}">
    <div class="title" v-if="mode == 'read' && type == 'book' || ['kb', 'video'].includes(type)">Проверочные вопросы</div>
    <div class="question mb-2" v-for="(q, q_index) in questions" :key="q_index" :class="{'show': q.editable}">
      <div
        class="title d-flex jcsb"
        @click="editQuestion(q_index)"
        v-if="mode == 'edit'"
      >
        <textarea
          v-model="q.text"
          placeholder="Текст вопроса..."
          @keyup="changed = true"
          v-if="q.editable"
        ></textarea>
        <input
          v-else
          type="text"
          v-model="q.text"
          disabled
          placeholder="Текст вопроса..."
        />
        <div class="btns aic">
          <i
            v-if="q.type == 0"
            class="fas fa-tasks"
          ></i>
          <i
            v-else
            class="fas fa-question"
          ></i>
          <span class="mx-1">{{q.points}}</span>
          <i
            class="far fa-trash-alt pointer"
            @click.stop="deleteQuestion(q_index)"
          ></i>
        </div>
      </div>

      <div class="title d-flex jcsb aic" v-if="mode == 'read'">
        <p class="mb-0">{{ q.text }}</p>
        <i
          class="fa fa-times-circle wrong"
          v-if="points != -1 && q.result == false"
        ></i>
        <i
          class="fa fa-check-circle right"
          v-if="points != -1 && q.result == true"
        ></i>
      </div>

      <div v-if="q.editable || mode == 'read'">
        <select v-model="q.type" class="type mt-2" v-if="mode == 'edit'">
          <option value="0">Тест</option>
          <option value="1">Открытый вопрос</option>
        </select>

     
        <div class="variants" v-if="q.type == 0">
          <div
            class="variant d-flex aic"
            v-for="(v, v_index) in q.variants"
            :key="v_index"
          >
            <input 
              v-if="mode == 'edit'"
              type="checkbox"
              v-model="v.right" 
              class="mr-2" 
              @change="changed = true"
              title="Отметьте галочкой, если думаете, что ответ правильный. Правильных вариантов может быть несколько" />
            <input 
              v-else
              type="checkbox"
              v-model="v.checked" 
              class="mr-2" 
              @change="changed = true"
              title="Отметьте галочкой, если думаете, что ответ правильный. Правильных вариантов может быть несколько" />

            <input
              type="text"
              v-model="v.text"
              :disabled="mode == 'read'"
              placeholder="Введите вариант ответа..."
              @keyup.enter="addVariant(q_index, v_index)"
              @keyup.delete="deleteVariant(q_index, v_index)"
              :ref="`variant${q_index}_${v_index}`"
            />
          </div>
        </div>
        <div v-else>
          <input type="text" v-model="q.result" />
        </div>
        


        <div class="points" v-if="mode == 'edit'">
          <p>Баллы</p>
          <input type="number" v-model="q.points" min="0" max="999" />
        </div>

      </div>
    </div>

    <template v-if="mode == 'read'">
      <div class="d-flex">
        <button class="btn btn-success mr-2" @click="checkAnswers">
          Проверить
        </button>
        <button
          class="btn btn-primary"
          @click="$emit('continueRead')"
          v-if="points != -1 && total == points && type == 'book'"
        >
          Читать дальше
        </button>
      </div>

      <p v-if="points != -1" class="mt-3">Вы набрали: {{ points }} баллов из {{ total }}</p>
    </template>
    <template v-if="mode == 'edit'">
      <button
        v-if="['kb','video'].includes(type) && changed"
        class="btn btn-success mr-2" 
        @click="saveTest"
        :disabled="!can_save"
        >
          Сохранить
      </button>
      <button class="btn" @click="addQuestion" >Добавить вопрос</button>
    </template>
  </div>
</template>

<script>
export default {
  name: "Questions",
  props: ["questions", "type", "id", "mode", 'pass'],
  data() {
    return {
      can_save: true,
      changed: false,
      questionsx: [
        {
          text: "Кто это был?",
          order: 0,
          type: "abc",
          editable: false,
          points: 10,
          variants: [
            {
              text: "Asan",
              right: 1,
            },
            {
              text: "Dolik",
              right: 0,
            },
            {
              text: "Burda",
              right: 0,
            },
            {
              text: "Bobik",
              right: 0,
            },
          ],
        },
      ],
      total: 0,
      points: -1,
    };
  },
  created() {
    this.prepareVariants();
    // this.mode = 'edit';
    if (this.mode == "read") {

      this.questions.forEach((q) => {
        q.editable = true;
        this.total += q.points;
      });

      if(this.pass) {
        this.points = this.total;
        this.page = this.page;
      }
      
    }
  },
  mounted() {},
  methods: {
    prepareVariants() {
      if(this.questions === undefined) this.questions = [];
      this.questions.forEach((q) => {
        if (q.type == 0) {
          q.variants.forEach((v) => {
            q.before = q.text;
          });
        }
      });
    },

    checkAnswers() {
      // read
      this.points = 0;
      this.questions.forEach((q) => {
        if (q.type == 0) {
          let right_answers = 0;
          let wrong_answers = 0;
          let checked_answers = 0;
          q.variants.forEach((v) => {
            if (v.checked == 1 && v.checked == v.right) {
              checked_answers++;
            }
            if (v.checked == 1 && v.right == 0) {
              wrong_answers++;
            }
            if (v.right == 1) {
              right_answers++;
            }
          });

          if (right_answers == checked_answers && wrong_answers == 0) {
            this.points += q.points;
            q.result = true;
          } else {
            q.result = false;
          }
        } else {
          this.points += q.points;
          q.result = true;
        }
      });

      if(this.points == this.total) {
        this.$emit('passed');
      }
    },

    addVariant(q_index, v_index = -1) {
      this.questions[q_index].variants.push({
        text: "",
        before: "",
        right: 0,
      });

      if (v_index != -1) {
        this.$nextTick(() => {
          let input = this.$refs["variant" + q_index + "_" + (v_index + 1)][0];

          input.focus();
        });
      }
      
      this.changed = true;
    },

    saveQuestion(q_index) {

      if(this.questions[q_index].text == '' || this.questions[q_index].text == null) {
          alert("Вопрос  №" + (q_index + 1) + " не заполнен!");
        return false;
      }


      if(this.questions[q_index].variants.findIndex((v) => v.text == '') != -1 &&
        this.questions[q_index].type == 0) {
        alert("Не оставляйте варианты пустыми! Вопрос №" + (q_index + 1));
        return false;
      }

      if (
        this.questions[q_index].variants.findIndex((v) => v.right == 1) == -1 &&
        this.questions[q_index].type == 0
      ) {
        alert("Выберите один правильный вариант! Вопрос №" + (q_index + 1));
        return false;
      }

      

      this.questions[q_index].editable = false;
      return true;
    },

    editQuestion(q_index) {
      this.questions.forEach((q) => (q.editable = false));
      this.questions[q_index].editable = true;
    },

    defaultQuestion() {
      return {
        id: 0,
        text: "",
        order: 0,
        points: 10,
        type: 0, // abc
        editable: false,
        variants: [
          {
            text: "",
            right: 0,
          },
        ],
      };
    },

    addQuestion() {
      this.questions.forEach((q) => (q.editable = false));
      this.questions.push(this.defaultQuestion());
      this.questions[this.questions.length - 1].editable = true;
      this.can_save = true;
      this.changed = true;
    },

    deleteQuestion(q_index) {
      if (confirm("Удалить вопрос?")) {
          if(this.questions[q_index].id == 0){
            this.questions.splice(q_index, 1);
          }else{
          axios
            .post("/playlists/delete-question", {
              id: this.questions[q_index].id
            })
            .then((response) => {  
              this.questions.splice(q_index, 1);
            })
        }
        this.changed = true;
      }
    },

    deleteVariant(q, v) {
      let el = this.questions[q].variants[v];
      if (el.text == el.before && el.before == "" && this.questions[q].variants.length > 1) {
        this.questions[q].variants.splice(v, 1);
        if (v > 0) this.$refs["variant" + q + "_" + (v - 1)][0].focus();
      } else {
        this.questions[q].variants[v].before = this.questions[q].variants[
          v
        ].text;
      }
      
      this.changed = true;
    },

    saveTest() {
      let passed = true;
      this.questions.every((q, index) => {
        if(!this.saveQuestion(index)) {
          passed = false;
          return false;
        }
        return true;
      });

      if(!passed) return false;
      let loader = this.$loading.show();

      let url = this.type == 'kb' ? "/kb/page/save-test" : "/playlists/save-test";

      this.can_save = false;

      axios
        .post(url, {
          id: this.id,
          questions: this.questions,
        })
        .then((response) => { 
          this.$message.success("Вопросы сохранены!");
          this.questions.forEach((item, index) => {
            item.id = response.data[index];
          });
          loader.hide();
          this.can_save = true;
        })
        .catch((error) => {
          loader.hide();
          alert(error);
        });
    },
  },
};
</script>
