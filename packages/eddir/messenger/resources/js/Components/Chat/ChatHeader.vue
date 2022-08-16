<template>
  <div class="chat-header" v-if="contact">
    <div class="chat-header__avatar">
      <img src="https://i.pravatar.cc/" alt="">
    </div>
    <div class="chat-header__info">
      <div class="chat-header__name">
        {{ contact.title }}
      </div>
      <div class="chat-header__time">
        {{ contact.time }}
        <span v-if="contact.active_status">Онлайн</span>
      </div>
    </div>
    <div class="chat-header__actions" @click="close">
      <div class="chat-header__action">
        x
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    contact: {
      type: Object,
      default: function () {
        return null;
      },
    },
  },
  methods: {
    close() {
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
.chat-header {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  border-bottom: 1px solid #e6e6e6;
}
.chat-header__avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
}
.chat-header__avatar img {
  width: 100%;
  height: 100%;
}
.chat-header__info {
  display: flex;
  flex-direction: column;
  margin-left: 0.5rem;
}
.chat-header__name {
  font-size: 18px;
  line-height: 20px;
  font-weight: 500;
  color: #000;
}
.chat-header__time {
  font-size: 12px;
  color: #6ab3f3;
}
.chat-header__actions {
  display: flex;
  align-items: center;
  margin-left: auto;
}
.chat-header__action {
  display: inline-block;
  width: 25px;
  height: 25px;
  border: 0;
  background: #fff;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}
.chat-header__action:hover {
  background: #e6e6e6;
}
.chat-header__action i {
  font-size: 18px;
  color: #6ab3f3;
}


</style>
