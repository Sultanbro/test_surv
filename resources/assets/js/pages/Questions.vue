<template>
  <div class="questions">
    <div class="title" v-if="mode == 'read' && type == 'book' || ['kb', 'video'].includes(type)">Проверочные вопросы</div>
    <div class="question mb-2" v-for="(q, q_index) in questions" :key="q_index">
      <div
        class="title d-flex jcsb"
        @click="editQuestion(q_index)"
        v-if="mode == 'edit'"
      >
        <textarea
          v-model="q.text"
          placeholder="Текст вопроса..."
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
          <i class="far fa-edit pointer"></i>
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

      <div v-if="q.editable">
        <select v-model="q.type" class="type mt-2" v-if="mode == 'edit'">
          <option value="0">Тест</option>
          <option value="1">Открытый вопрос</option>
        </select>

        <template v-if="mode == 'edit'">
          <div class="variants" v-if="q.type == 0">
            <div
              class="variant d-flex aic"
              v-for="(v, v_index) in q.variants"
              :key="v_index"
            >
              <input type="checkbox" v-model="v.right" class="mr-2" />
              <input
                type="text"
                v-model="v.text"
                placeholder="..."
                @keyup.enter="addVariant(q_index, v_index)"
                @keyup.delete="deleteVariant(q_index, v_index)"
                :ref="`variant${q_index}_${v_index}`"
              />
            </div>
          </div>
        </template>

        <template v-if="mode == 'read'">
          <div class="variants" v-if="q.type == 0">
            <div
              class="variant d-flex aic"
              v-for="(v, v_index) in q.variants"
              :key="v_index"
            >
              <input type="checkbox" v-model="v.checked" class="mr-2" />
              <input
                type="text"
                v-model="v.text"
                disabled
                placeholder="..."
                @keyup.enter="addVariant(q_index, v_index)"
                @keyup.delete="deleteVariant(q_index, v_index)"
                :ref="`variant${q_index}_${v_index}`"
              />
            </div>
          </div>
          <div v-else>
            <input type="text" v-model="q.result" />
          </div>
        </template>

        <template v-if="mode == 'edit'">
          <div class="points">
            <p>Баллы</p>
            <input type="number" v-model="q.points" min="0" max="999" />
          </div>

          <button class="btn btn-success" @click="saveQuestion(q_index)">
            Сохранить
          </button>
          <button
            class="btn mr-1"
            @click="addVariant(q_index)"
            v-if="q.type == 0"
          >
            Добавить вариант
          </button>
        </template>
      </div>
    </div>

    <template v-if="mode == 'read'">
      <div class="d-flex">
        <button class="btn btn-success mr-2" @click="checkAnswers">
          Проверить
        </button>
        <button
          class="btn btn-primary"
          @click="continueRead"
          v-if="points != -1 && total == points && type == 'book'"
        >
          Читать дальше
        </button>
      </div>

      <p v-if="points != -1">{{ points }} баллов из {{ total }}</p>
    </template>
    <template v-if="mode == 'edit'">
      <button
        v-if="['kb'].includes(type)"
        class="btn btn-success mr-2" 
        @click="saveTest">
          Сохранить
      </button>
      <button class="btn" @click="addQuestion" >Добавить вопрос</button>
    </template>
  </div>
</template>

<script>
export default {
  name: "Questions",
  props: ["questions", "type", "id", "mode"],
  data() {
    return {
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
    if (this.mode == "read") {
      this.questions.forEach((q) => {
        q.editable = true;
        this.total += q.points;
        if (q.type == 0) {
          q.result = false;
          q.variants.forEach((v) => {
            v.checked = 0;
          });
        }
      });
    }
  },
  mounted() {},
  methods: {
    prepareVariants() {
      this.questions.forEach((q) => {
        if (q.type == 0) {
          q.variants.forEach((v) => {
            q.before = q.text;
          });
        }
      });
    },

    continueRead() {
      //read
      this.$emit('continueRead')
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
    },

    saveQuestion(q_index) {
      if (
        this.questions[q_index].variants.findIndex((v) => v.right == 1) == -1 &&
        this.questions[q_index].type == 0
      ) {
        alert("Выберите один правильный вариант!");
        return;
      }
      this.questions[q_index].editable = false;
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
    },

    deleteQuestion(q_index) {
      if (confirm("Удалить вопрос?")) {
        this.questions.splice(q_index, 1);
      }
    },

    deleteVariant(q, v) {
      let el = this.questions[q].variants[v];
      if (el.text == el.before && el.before == "") {
        this.questions[q].variants.splice(v, 1);
        if (v > 0) this.$refs["variant" + q + "_" + (v - 1)][0].focus();
      } else {
        this.questions[q].variants[v].before = this.questions[q].variants[
          v
        ].text;
      }
    },

    saveTest() {
      let loader = this.$loading.show();

      let url = this.type == 'kb' ? "/kb/page/save-test" : "/playlists/save-test";

      axios
        .post(url, {
          id: this.id,
          questions: this.questions,
        })
        .then((response) => {
          this.$message.success("Вопросы сохранены!");
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
