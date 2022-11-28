<template>
    <div class="col-9">
        <news-create v-if="isRedactor" ref="newsCreate" @update-news-list="getPosts" :me="me"></news-create>

        <div class="news-container">
            <div class="news-container__header">
                <span class="news-header__title">Новости</span>
                <filter-component @searchNews="getPosts" ref="filterComponent" @toggleWhiteBg="showWhiteBg"/>
            </div>
            <post-component
                @editPost="updatePost"
                @update-news-list="getPosts"
                v-for="post in pinnedPosts"
                :key="post.id + post.content + post.is_pinned + post.is_favourite
                + (post.available_for == null ? '' : post.available_for.length)"
                :post="post"
                :me="me"
                ref="post"/>

            <post-component
                @editPost="updatePost"
                @update-news-list="getPosts"
                v-for="post in posts"
                :key="post.id + post.content + post.is_pinned + post.is_favourite
                + (post.available_for == null ? '' : post.available_for.length)"
                :post="post"
                :me="me"
                ref="post"/>
            <div :style="(nextPageURL != null && showPaginator == true) ? '' : 'opacity: 0;'" @click="getNextPage()" class="news-paginate">
                <img src="/icon/news/some-icons/next-page.svg">
                <span>Загрузить ещё</span>
            </div>
        </div>

        <div class="news-bg" v-show="showBg" @click.self="hideWhiteBg"/>
    </div>
</template>

<script>

export default {
    name: "NewsFeed",
    components: {},
    data() {
        return {
          isRedactor: true,
            posts: [],
            pinnedPosts: [],
            nextPageURL: null,
            showPaginator: true,
            me: null,
            showBg: false,
        }
    },
    mounted() {
        this.isRedactor = this.$can('news_edit')

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const post_id = urlParams.get('post_id');
        if (post_id != null) {
            let params = {
                params: '?post_id=' + post_id,
            };
            this.getPosts(params);
        } else {
            this.getPosts();
        }

        this.getMe();

        this.$root.$on('toggle-white-bg', (value) => {
            this.showBg = value;
        });
    },

    methods: {
        hideWhiteBg() {
            this.showBg = false;
            if(this.showBg === false){
                window.onscroll = function() {};
            }
            this.$refs.filterComponent.toggleShowFilters(false);
            this.$eventBus.$emit('hide-emoji-keyboard');
            this.$eventBus.$emit('hide-gift-popup');
            this.$eventBus.$emit('hide-access-popup');
        },

        showWhiteBg() {
            this.showBg = true;
        },

        async getMe() {
            await axios.get('/me')
                .then(response => {
                    this.me = response.data.data;
                })
                .catch();
        },
        async getPosts(data = null) {
            await axios.get('/news/get' + (data == null ? '' : data.params))
                .then(response => {
                    this.nextPageURL = response.data.data.pagination.next_page_url;
                    this.posts = response.data.data.articles;
                    this.pinnedPosts = response.data.data.pinned_articles;
                    this.$forceUpdate();
                })
                .catch();
        },

        updatePost(data) {
            this.$refs.newsCreate.getOldData(data)
        },

        async getNextPage() {
            this.showPaginator = false;

            await axios.get(this.nextPageURL)
                .then(response => {
                    this.nextPageURL = response.data.data.pagination.next_page_url;
                    this.posts = this.posts.concat(response.data.data.articles);
                    this.showPaginator = true;
                })
                .catch(response => {
                    this.showPaginator = true;
                });
        }
    }
}
</script>
