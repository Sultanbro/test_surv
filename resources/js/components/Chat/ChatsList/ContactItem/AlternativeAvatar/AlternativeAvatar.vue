<template>
  <div v-if="image && !imageError" :class="{
    'messenger__avatar': true,
    'messenger__avatar--small': inline,
  }">
    <img :src="image" :alt="title" @error="imageError = true">
  </div>
  <div v-else :class="{
    'messenger__avatar_container': !inline,
    'messenger__avatar_container--small': inline,
  }">
    <div class="messenger__avatar_name" v-text="title[0]"></div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      imageError: false,
    };
  },
  watch: {
    imageError() {
      console.log('imageError', this.image, this.title);
    },
  },
  props: {
    image: {
      type: String | null,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    inline: {
      type: Boolean,
      default: false,
    },
  },
}
</script>

<style scoped>
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

.messenger__avatar--small {
  display: inline-block;
  height: 25px;
  width: 25px;
  min-height: 25px;
  min-width: 25px;
  margin-right: 0;
}

.messenger__avatar img {
  height: 100%;
  width: 100%;
  border-radius: 50%;
}

.messenger__avatar_container {
  border-radius: 50%;
  height: 42px;
  width: 42px;
  min-height: 42px;
  min-width: 42px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #7ccaed;
  color: #fff;
  margin-right: 10px;
}

.messenger__avatar_container--small {
  border-radius: 50%;
  height: 25px;
  width: 25px;
  min-height: 25px;
  min-width: 25px;
  display: inline-block;
  background-color: #7ccaed;
  color: #fff;
  transform: translateY(9px);
}

.messenger__avatar_container .messenger__avatar_name {
  font-size: 17px;
  font-weight: 700;
  line-height: 22px;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
}

.messenger__avatar_container--small .messenger__avatar_name {
  font-size: 12px;
  font-weight: 700;
  line-height: 25px;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
}

</style>
