<template>
	<div class="col-12 col-xl-9 col-lg-8 col-posts">
		<NewsCreate
			v-if="isRedactor"
			ref="newsCreate"
			:me="me"
			@update-news-list="getPosts"
		/>

		<div class="news-container">
			<div class="news-container__header">
				<span class="news-header__title">Новости</span>
				<FilterComponent
					ref="filterComponent"
					@searchNews="getPosts"
					@toggleWhiteBg="showWhiteBg"
				/>
			</div>
			<PostComponent
				v-for="post in pinnedPosts"
				:key="post.id + post.content + post.is_pinned + post.is_favourite + (post.available_for == null ? '' : post.available_for.length)"
				:post="post"
				:me="me"
				@editPost="updatePost"
				@update-news-list="getPosts"
			/>

			<PostComponent
				v-for="post in posts"
				:key="post.id + post.content + post.is_pinned + post.is_favourite + (post.available_for == null ? '' : post.available_for.length)"
				:post="post"
				:me="me"
				@editPost="updatePost"
				@update-news-list="getPosts"
			/>
			<div
				class="news-paginate"
				:style="(nextPageURL != null && showPaginator == true) ? '' : 'opacity: 0;'"
				@click="getNextPage()"
			>
				<img src="/icon/news/some-icons/next-page.svg">
				<span>Загрузить ещё</span>
			</div>
		</div>

		<div
			v-show="showBg"
			class="news-bg"
			@click.self="hideWhiteBg"
		/>
	</div>
</template>

<script>
import NewsCreate from '@/pages/News/NewsCreate'
import FilterComponent from '@/pages/News/FilterComponent'
import PostComponent from '@/pages/News/PostComponent'

export default {
	name: 'NewsFeed',
	components: {
		NewsCreate,
		FilterComponent,
		PostComponent,
	},
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
		const postId = urlParams.get('post_id');
		if (postId != null) {
			let params = {
				params: '?post_id=' + postId,
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
			// this.$eventBus = undefined
			// this.$eventBus.$emit('hide-emoji-keyboard');
			// this.$eventBus.$emit('hide-gift-popup');
			// this.$eventBus.$emit('hide-access-popup');
		},

		showWhiteBg() {
			this.showBg = true;
		},

		async getMe() {
			await this.axios.get('/me')
				.then(response => {
					this.me = response.data.data;
				})
				.catch();
		},
		async getPosts(data = null) {
			await this.axios.get('/news/get' + (data == null ? '' : data.params))
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

			await this.axios.get(this.nextPageURL)
				.then(response => {
					this.nextPageURL = response.data.data.pagination.next_page_url;
					this.posts = this.posts.concat(response.data.data.articles);
					this.showPaginator = true;
				})
				.catch(() => {
					this.showPaginator = true;
				});
		}
	}
}
</script>
