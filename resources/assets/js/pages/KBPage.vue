<template>
  <div>
    <!-- PAGE -->
    <div class="kb-sections d-flex" v-if="activeBook === null">
      <aside id="left-panel" class="lp">
        <div class="btn btn-search mb-3">
          <i class="fa fa-search"></i>
          <span>Искать в базе...</span>
        </div>
        <template v-for="book in books">
          <div
            class="section d-flex aic jcsb"
            :key="book.id"
            @click.stop="selectSection(book)"
          >
            <p>{{ book.title }}</p>
            <i class="fa fa-cogs" @click.stop="editAccess(book)"></i>
          </div>
        </template>

        <div class="btn-add" @click="addSection">
          <i class="fa fa-plus"></i>
          <span>Добавить раздел</span>
        </div>
      </aside>
      <div class="rp" style="flex: 1 1 0%; padding-bottom: 50px;">
        <div class="hat">
          <div class="d-flex jsutify-content-between hat-top">
            <div class="bc">
              <a href="#">База знаний</a>
              <!---->
            </div>
            <div class="control-btns"><!----></div>
          </div>
          <div><!----></div>
        </div>
        <div class="content mt-3"></div>
      </div>
    </div>

    <!-- PAGE -->
    <div v-else>
      <booklist :trees="trees" :parent_id="activeBook.id" @back="back" />
    </div>
  </div>
</template>

<script>
export default {
  name: "KBPage",
  props: {},
  data: function() {
    return {
      books: [],
      trees: [],
      section: 0,
      activeBook: null,
    };
  },
  watch: {},

  created() {
    this.fetchData();
  },

  methods: {
    fetchData() {
      axios
        .get("/kb/get", {})
        .then((response) => {
          this.books = response.data.books;
        })
        .catch((error) => {
          alert(error);
        });
    },

    selectSection(book) {
      axios
        .post("kb/tree", {
          id: book.id,
        })
        .then((response) => {
          this.trees = response.data.trees;
          this.activeBook = book;
        })
        .catch((error) => {
          alert(error);
        });
    },
    back() {
      this.activeBook = null;
    },

    editAccess(book) {
      alert(book.title);
    },

    addSection() {
        alert('Новый раздел');
    }
  },
};
</script>

<style lang="scss" scoped></style>
