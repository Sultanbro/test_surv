<template>
  <modal v-on="$listeners">
    <template v-slot:header>
      Создание чата
    </template>
    <template v-slot:body>
      <div class="form-group">
        <label for="name">Название</label>
        <input type="text" class="form-control" id="name" v-model="name" placeholder="Название чата">
      </div>
      <div class="form-group">
        <label for="users">Участники</label>
        <multiselect v-model="members"
                     :close-on-select="false"
                     :hide-selected="true"
                     :multiple="true"
                     :options="users"
                     track-by="id"
                      label="name"
                     :searchable="true"
                     :allow-empty="false"
                     placeholder="Выберите участников"
        >
          <template slot="singleLabel" slot-scope="{ option }">
<!--            <strong>{{ option.name }}</strong>-->
            опция
          </template>
        </multiselect>
      </div>
    </template>
    <template v-slot:footer>
      <button type="button" class="btn btn-green" @click="createChat">Создать</button>
    </template>
  </modal>
</template>

<script>
import Modal from "./Modal.vue";
import Multiselect from 'vue-multiselect'

export default {
  components: {
    Modal,
    Multiselect,
  },
  props: {
    users: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      name: '',
      members: [],
    };
  },
  methods: {
    createChat() {
      if (!this.name) {
        return;
      }
      this.$emit('create', {
        title: this.name,
        members: this.members,
      });
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
  label {
    display: block;
  }
</style>
