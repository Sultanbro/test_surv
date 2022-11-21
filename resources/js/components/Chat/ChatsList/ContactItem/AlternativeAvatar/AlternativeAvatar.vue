<template>
  <div v-if="image && !imageError" class="messenger__avatar">
    <img :src="image" :alt="title" @error="imageError = true">
  </div>
  <div v-else class="messenger__avatar_container">
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
  props: {
    image: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
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
  font-size: 20px;
  font-weight: 500;
  background-color: #7ccaed;
  color: #fff;
  margin-right: 10px;
}

.messenger__avatar_name {
  font-size: 17px;
  font-weight: 700;
  line-height: 22px;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
}

</style>
