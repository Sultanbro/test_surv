<template>
  <div class="side-contacts-list" @click="openMessenger">
    <ul>
      <li
        v-for="contact in contacts"
        :key="contact.id"
        onkeydown="selectContact(contact)"
        @click="selectContact(contact)"
      >
        <div class="avatar">
          <img :src="contact.image" :alt="contact.title"/>
        </div>
        <span v-if="contact.unseen" class="unread">{{ contact.unseen }}</span>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    contacts: {
      type: Array,
      default: function () {
        return [];
      },
    },
  },
  methods: {
    selectContact(contact) {
      this.$emit("contact-selected", contact);
    },
    openMessenger() {
      this.$emit("open");
    },
  },
};
</script>

<style scoped>
.side-contacts-list {
  width: 80px;
  max-height: 100%;
  height: 600px;
  overflow: auto;
  border-left: 1px solid #e1dddd;
  cursor: pointer;
}

.side-contacts-list ul {
  list-style-type: none;
  padding-left: 0;
}

.side-contacts-list ul li {
  display: flex;
  padding: 2px;
  border-bottom: 1px solid #e1dddd;
  height: 60px;
  position: relative;
}

.side-contacts-list ul li:hover {
  background-color: #f5f5f5;
}

.side-contacts-list ul li span.unread {
  background: #82e0a8;
  color: #fff;
  position: absolute;
  right: 11px;
  top: 20px;
  display: flex;
  font-weight: 700;
  min-width: 20px;
  justify-content: center;
  align-items: center;
  line-height: 20px;
  font-size: 12px;
  padding: 0 4px;
  border-radius: 3px;
}

.side-contacts-list ul li .avatar {
  flex: 1;
  display: flex;
  align-items: center;
}

.side-contacts-list ul li .avatar img {
  width: 35px;
  border-radius: 50%;
  margin: 0 auto;
}

/* total width */
.side-contacts-list::-webkit-scrollbar {
  background-color: #fff;
  width: 16px;
}

/* background of the scrollbar except button or resizer */
.side-contacts-list::-webkit-scrollbar-track {
  background-color: #fff;
}

/* scrollbar itself */
.side-contacts-list::-webkit-scrollbar-thumb {
  background-color: #babac0;
  border-radius: 16px;
  border: 4px solid #fff;
}

/* set button(top and bottom of the scrollbar) */
.side-contacts-list::-webkit-scrollbar-button {
  display: none;
}


</style>
