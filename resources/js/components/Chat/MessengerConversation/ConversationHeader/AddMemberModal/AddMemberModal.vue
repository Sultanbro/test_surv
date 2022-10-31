<template>
  <ModalWindow v-on="$listeners" :closeButton="false">
    <template v-slot:header>
      Добавить участника
    </template>
    <template v-slot:body>
      <div class="form-group">
        <label for="name">Название</label>
        <input type="text" class="form-control" id="name" v-model="title" placeholder="Название чата">
      </div>
      <div class="form-group">
        <label for="users">Участники</label>
        <multiselect v-model="members"
                     :close-on-select="false"
                     :hide-selected="true"
                     :multiple="true"
                     :options="contacts"
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
      <button type="button" class="messenger__add-button" @click="submitForm">Добавить</button>
    </template>
  </ModalWindow>
</template>

<script>
import ModalWindow from "./ModalWindow/ModalWindow.vue";
import Multiselect from 'vue-multiselect'
import {mapActions, mapGetters} from "vuex";

export default {
  name: "AddMemberModal",
  components: {
    ModalWindow,
    Multiselect,
  },
  computed: {
    ...mapGetters(['contacts', 'newChatContacts'])
  },
  watch: {
    newChatContacts() {
      this.members = this.newChatContacts
    }
  },
  data() {
    return {
      title: '',
      members: []
    };
  },
  methods: {
    ...mapActions(['createChat']),
    submitForm() {
      if (!this.title) {
        return;
      }
      console.log("create chat", this.members);
      this.createChat({
        title: this.title,
        description: '',
        members: this.members.map(member => member.id)
      });
      this.$emit('close');
    },
  },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>

label {
  display: block;
}

.form-control {
  border: 1px solid #e8e8e8;
}

.messenger__add-button {
  background: #2f80ed;
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 8px 16px;
  font-size: 14px;
  cursor: pointer;
}

</style>
