<template>
  <news-feed></news-feed>
<!--  <birthday-feed></birthday-feed>-->

</template>

<script>
import Vue from 'vue'

Vue.component('news-feed', require('@/pages/News/NewsFeed').default); // новостная лента раздел
Vue.component('news-create', require('@/pages/News/NewsCreate').default);
Vue.component('drop-zone', require('@/pages/News/DropZone').default);
Vue.component('post-component', require('@/pages/News/PostComponent').default);
Vue.component('comments-component', require('@/pages/News/CommentsComponent').default);
Vue.component('filter-component', require('@/pages/News/FilterComponent').default);
Vue.component('reactions', require('@/pages/News/ReactionComponent').default);
export default {
  name: "Index"
}
</script>

<style scoped>

</style>