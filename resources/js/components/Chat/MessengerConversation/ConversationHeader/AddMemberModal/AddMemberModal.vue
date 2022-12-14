<template>
  <ModalWindow v-on="$listeners" :closeButton="false">
    <template #header>
      <template v-if="chat && chat.private">Создать групповой чат</template>
      <template v-else>Пригласить в чат</template>
    </template>
    <template #body>
      <div class="form-group" v-if="chat.private">
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
          <template #singleLabel="{ option }">
            <!--            <strong>{{ option.name }}</strong>-->
            опция
          </template>
        </multiselect>
      </div>
    </template>
    <template #footer>
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
    ...mapGetters(['contacts', 'newChatContacts', 'chat', 'user'])
  },
  data() {
    return {
      title: '',
      members: []
    };
  },
  created() {
    if (!this.chat.private) {
      this.title = this.chat.title;
      this.members = this.chat.users.filter(user => user.id !== this.user.id);
    }
  },
  methods: {
    ...mapActions(['createChat', 'addMembers', 'removeMembers']),
    submitForm(e) {
      e.stopPropagation();
      if (!this.title) {
        return;
      }
      if (this.chat.private) {
        this.createChat({
          title: this.title,
          description: '',
          members: this.members.map(member => member.id)
        });
      } else {
        // find diff between members and chat.users
        // add new members
        // remove old members
        this.members.push(this.user);
        let add_members = this.members.filter(member => !this.chat.users.find(user => user.id === member.id));
        let remove_members = this.chat.users.filter(user => !this.members.find(member => member.id === user.id));

        if (add_members.length > 0) {
          this.addMembers(add_members);
        }
        if (remove_members.length > 0) {
          this.removeMembers(remove_members);
        }
      }
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
