
window.Vue = require('vue');


Vue.component('edit-playlist', require('./components/videos/EditPlaylist.vue'));

const app = new Vue({
	el: '#app'
});