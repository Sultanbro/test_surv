<template>
  <div class="messenger__chat-item-container messenger__text-ellipsis">
    <div class="messenger__avatar-list messenger__filter-blue" v-if="item.unread_messages_count > 0"></div>
    <div class="messenger__avatar-list" v-else></div>
    <div class="messenger__name-container messenger__text-ellipsis" v-if="isOpen">
      <div class="messenger__title_container">
        <div class="messenger__chat-name messenger__text-ellipsis" v-text="item.title"></div>
      </div>
      <template v-if="item.unread_messages_count > 0">
        <div class="messenger__last-message messenger__last-message__marked"
             v-text="item.last_message ? item.last_message.body : ''"></div>
      </template>
      <template v-else>
        <div class="messenger__last-message" v-text="item.last_message ? item.last_message.body : ''"></div>
      </template>
    </div>
    <div class="messenger__short-name-container" v-if="!isOpen">
      <div class="messenger__chat-name messenger__text-ellipsis messenger__text-center" v-text="item.title[0]"></div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "ContactItem",
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapGetters(['isOpen'])
  }
}
</script>

<style scoped>

.messenger__chat-item-container {
  display: flex;
  flex: 1;
  align-items: flex-start;
  width: 100%;
}

.messenger__avatar-list {
  background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='iso-8859-1'%3F%3E%3C!-- Generator: Adobe Illustrator 18.0.0  SVG Export Plug-In . SVG Version: 6.00 Build 0) --%3E%3C!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3E%3Csvg version='1.1' id='Capa_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 453.818 453.818' style='enable-background:new 0 0 453.818 453.818%3B' xml:space='preserve'%3E%3Cpath d='M438.818 96.071H15c-8.284 0-15 6.716-15 15v231.676c0 8.284 6.716 15 15 15h423.818c8.284 0 15-6.716 15-15V111.071C453.818 102.787 447.103 96.071 438.818 96.071z M30 133.089l140.736 93.819L30 320.733V133.089z M55.533 327.747l133.231-88.821l32.598 21.731c1.68 1.12 3.613 1.679 5.547 1.679s3.867-0.56 5.547-1.679l32.601-21.733l133.233 88.822H55.533z M226.909 240.319L55.53 126.071h342.759L226.909 240.319z M283.085 226.907l140.734-93.818v187.64L283.085 226.907z'/%3E%3C/svg%3E");
  background-size: cover;
  background-position: top center;
  background-repeat: no-repeat;
  filter: invert(0.8);
  height: 18px;
  width: 18px;
  min-height: 18px;
  min-width: 18px;
  margin-right: 15px;
}

.messenger__filter-blue {
  filter: invert(78%) sepia(92%) saturate(2055%) hue-rotate(168deg) brightness(95%) contrast(96%);
}


.messenger__name-container {
  flex: 1;
}

.messenger__short-name-container {
  background: #f5f5f5;
  border-radius: 50%;
  height: 40px;
  width: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: 500;
  color: #000000;
}

.messenger__text-center {
  text-align: center;
}

.messenger__text-ellipsis {
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.messenger__title_container {
  display: flex;
  align-items: center;
  line-height: 25px;
}

.messenger__chat-name {
  font-size: 17px;
  font-weight: 700;
  line-height: 22px;
  color: #0a0a0a;
}

.messenger__last-message {
  display: flex;
  align-items: center;
  font-size: 12px;
  line-height: 19px;
  color: #8b8b8b;
}

.messenger__last-message__marked {
  color: #1883b2;
  font-weight: 600;
}

</style>
