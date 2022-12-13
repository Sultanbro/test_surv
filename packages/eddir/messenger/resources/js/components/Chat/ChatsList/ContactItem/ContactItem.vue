<template>
  <div class="messenger__chat-item-container messenger__text-ellipsis">

    <div v-if="item.image && !imageError" class="messenger__avatar messenger__clickable">
      <img :src="item.image" :alt="item.name" @error="imageError = true">
      <div class="messenger__chat-name-label" v-if="!fullscreen">
        <img v-if="item.unread_messages_count > 0"
             src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAABYAAAAXCAMAAAA4Nk+sAAAA+VBMVEUAAADv///////v9//39/f39//09Pr39//z8/vz9/v19f/y9fz1+fz09/z3+vzy9Pz09/rz9v319/33+f32+P319/z19/309fz19vz09/z29/z09v32+P319/z19/329/z19vyWuGScvG6dvG6dvW6iwHejwXejwXioxICoxIGpxYGpxoGtx4quyIqwyYqwyou0zJS3zpS50J260J290p6/06bA06bA1KbE16jF17DG2LDL27rR37vR38PR4LvR4MPX48zX5MTY5MXd59be6M7e6c7j69/j6+Dk7djl7djp7+ns8eLs8uLv8/Ly9uvy9uz19/z4+/X///86wAZwAAAAIXRSTlMAEBAgICAwP0BAT1BQX19gYG9/f4+fn6Cvv7/Pz9/f3++Oh2qrAAABHElEQVQYGVXBC1vBYBgG4DenlNLJISp5vrUoRfKRQ1Qk22r2rP//Y3LNiPuWlUgimUxGZFuqyEAhLf8SlyRnk8mM5HVMQsek01ZYUM8OmZbAATlQWOmRaVmIl9nDhh7LMRHJcYgtbyyIxEkThu3/BvwPA8plUjIcAuN5s7708woMeCpFPgC2bnSx0L3XFvDIgpSpAFvXvE/DmPo1bQGKZSEB2BoVb+5930JbAEghFWBr4GY0NgBtASClxCpga4S0BdzxQnJsA9MRQmMLaDMre/wCGr4X8uuAw32REl+ASlMvGUCLVyKSomtig+nyUBbydEysVR1mJJAn+yYCqk/mJHRG8r3z1OoMXTIja9EiQ4Vd2RQ9ypxnsyc7svQHUKlMqjz0UDEAAAAASUVORK5CYII="
             alt="Есть непрочитанные сообщения">
        <img v-else
             src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAABYAAAAXCAMAAAA4Nk+sAAABF1BMVEUAAADv///////////v9/fv9//09Pr39//3+//z9/v1+f/y9fz3+vz09/z2+P309vr19/33+f309v319/z19/309fz19vz2+Pz09/z29/z1+P32+P319/z29/z29/309vz19vzCxMrFx83GyM3GyM7IytDJy9DJy9HKzNDLzdPNz9PNz9TO0NbP0dbP0dfR09nS1NnU1tzV19rV19vV19zV193Y2t3Y2t/Y2uDZ2t3Z2t7b3eLc3eHc3uPd3eHd3uHe4Obg4eXh4+ni5Onk5efk5ejl5+zo6Ovo6evo6u/o6vDr7fPs7O7u8PXu8Pbv8PHv8PLv8fbw8PLx8/jy9Pnz9PX19/z39/n3+Pj4+Pn7+/v7+/z////wvFnAAAAAIXRSTlMAEBAfICAwPz9AT1BfYG9wf3+Pn5+gr6+/v8/P39/f7+92KeMYAAABI0lEQVQoz13RX1sBYRCG8RFCUmqlJWUf7KrovxQRkkq1KbZH4vt/jg7etRfdZ/M7mmtGRBXZ0HQ9GV+VxSIpuu2ve7iyQ47aF+Xy2YNNbvtc3ePoLgfVjcO08gRtE16mzaSISGxJAdPhpogcsAKUam5F4JyGT2K0gavZ96dqdgn0uSa7rADDRxQAoIDOO3BLTdI8AobV+rgIlCbV2gAwmRESwLCKzvS+Pu2hNgBAeozW7LcBjw2ainF4DMU5GpLiicsAgMYAOKUuW2wDnfF878kz0GVcgnQs5F9+3Hp5WGRAJMk+lnqiJiJBg81FbTITEBGJkF1rjvkuGVIHjxr8urYAoNh0mI3O3xNMkXRe3z5I6oGFZ4YTWZLMaiH5lz8c9nvDHzsAT7v65f2xAAAAAElFTkSuQmCC"
             alt="Все сообщения прочитаны">
      </div>
    </div>

    <div v-else class="messenger__short-name-container">
      <div class="messenger__chat-name_short messenger__text-ellipsis messenger__text-center"
           v-text="item.title[0]"></div>

      <div class="messenger__chat-name-label" v-if="!fullscreen">
        <img v-if="item.unread_messages_count > 0"
             src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAABYAAAAXCAMAAAA4Nk+sAAAA+VBMVEUAAADv///////v9//39/f39//09Pr39//z8/vz9/v19f/y9fz1+fz09/z3+vzy9Pz09/rz9v319/33+f32+P319/z19/309fz19vz09/z29/z09v32+P319/z19/329/z19vyWuGScvG6dvG6dvW6iwHejwXejwXioxICoxIGpxYGpxoGtx4quyIqwyYqwyou0zJS3zpS50J260J290p6/06bA06bA1KbE16jF17DG2LDL27rR37vR38PR4LvR4MPX48zX5MTY5MXd59be6M7e6c7j69/j6+Dk7djl7djp7+ns8eLs8uLv8/Ly9uvy9uz19/z4+/X///86wAZwAAAAIXRSTlMAEBAgICAwP0BAT1BQX19gYG9/f4+fn6Cvv7/Pz9/f3++Oh2qrAAABHElEQVQYGVXBC1vBYBgG4DenlNLJISp5vrUoRfKRQ1Qk22r2rP//Y3LNiPuWlUgimUxGZFuqyEAhLf8SlyRnk8mM5HVMQsek01ZYUM8OmZbAATlQWOmRaVmIl9nDhh7LMRHJcYgtbyyIxEkThu3/BvwPA8plUjIcAuN5s7708woMeCpFPgC2bnSx0L3XFvDIgpSpAFvXvE/DmPo1bQGKZSEB2BoVb+5930JbAEghFWBr4GY0NgBtASClxCpga4S0BdzxQnJsA9MRQmMLaDMre/wCGr4X8uuAw32REl+ASlMvGUCLVyKSomtig+nyUBbydEysVR1mJJAn+yYCqk/mJHRG8r3z1OoMXTIja9EiQ4Vd2RQ9ypxnsyc7svQHUKlMqjz0UDEAAAAASUVORK5CYII="
             alt="Есть непрочитанные сообщения">
        <img v-else
             src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAABYAAAAXCAMAAAA4Nk+sAAABF1BMVEUAAADv///////////v9/fv9//09Pr39//3+//z9/v1+f/y9fz3+vz09/z2+P309vr19/33+f309v319/z19/309fz19vz2+Pz09/z29/z1+P32+P319/z29/z29/309vz19vzCxMrFx83GyM3GyM7IytDJy9DJy9HKzNDLzdPNz9PNz9TO0NbP0dbP0dfR09nS1NnU1tzV19rV19vV19zV193Y2t3Y2t/Y2uDZ2t3Z2t7b3eLc3eHc3uPd3eHd3uHe4Obg4eXh4+ni5Onk5efk5ejl5+zo6Ovo6evo6u/o6vDr7fPs7O7u8PXu8Pbv8PHv8PLv8fbw8PLx8/jy9Pnz9PX19/z39/n3+Pj4+Pn7+/v7+/z////wvFnAAAAAIXRSTlMAEBAfICAwPz9AT1BfYG9wf3+Pn5+gr6+/v8/P39/f7+92KeMYAAABI0lEQVQoz13RX1sBYRCG8RFCUmqlJWUf7KrovxQRkkq1KbZH4vt/jg7etRfdZ/M7mmtGRBXZ0HQ9GV+VxSIpuu2ve7iyQ47aF+Xy2YNNbvtc3ePoLgfVjcO08gRtE16mzaSISGxJAdPhpogcsAKUam5F4JyGT2K0gavZ96dqdgn0uSa7rADDRxQAoIDOO3BLTdI8AobV+rgIlCbV2gAwmRESwLCKzvS+Pu2hNgBAeozW7LcBjw2ainF4DMU5GpLiicsAgMYAOKUuW2wDnfF878kz0GVcgnQs5F9+3Hp5WGRAJMk+lnqiJiJBg81FbTITEBGJkF1rjvkuGVIHjxr8urYAoNh0mI3O3xNMkXRe3z5I6oGFZ4YTWZLMaiH5lz8c9nvDHzsAT7v65f2xAAAAAElFTkSuQmCC"
             alt="Все сообщения прочитаны">
      </div>
    </div>

    <div class="messenger__name-container messenger__text-ellipsis" v-if="fullscreen">
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

  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  name: "ContactItem",
  props: {
    item: {
      type: Object,
      required: true
    },
    fullscreen: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      imageError: false
    }
  },
  methods: {
    ...mapActions(['toggleInfoPanel']),
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
  border-radius: 50%;
  height: 40px;
  width: 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 20px;
  font-weight: 500;
  background-color: #7ccaed;
  color: #fff;
}

.messenger__card-window .messenger__short-name-container {
  margin-right: 10px;
}

.messenger__chat-name-label {
  position: absolute;
  top: 0;
  right: 0;
  padding: 5px;
}

.messenger__chat-name-label img {
  width: 17px;
  vertical-align: top;
}

.messenger__text-center {
  text-align: center;
}

.messenger__text-ellipsis {
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
}

.messenger__title_container {
  display: flex;
  align-items: center;
  line-height: 25px;
  height: 25px;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-weight: 500;
}

.messenger__chat-name_short {
  font-size: 17px;
  font-weight: 700;
  line-height: 22px;
}

.messenger__last-message {
  align-items: center;
  font-size: 11px;
  line-height: 15px;
  color: #8b8b8b;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  height: 30px;
}

.messenger__chat-selected .messenger__last-message {
  color: #fff;
}

.messenger__avatar {
   background-size: cover;
   background-position: center center;
   background-repeat: no-repeat;
   background-color: #ddd;
   height: 42px;
   width: 42px;
   min-height: 42px;
   min-width: 42px;
   margin-right: 10px;
   border-radius: 50%;
 }

.messenger__last-message__marked {
  color: #1883b2;
  font-weight: 600;
}

.messenger__avatar img {
  height: 100%;
  width: 100%;
  border-radius: 50%;
}

</style>
