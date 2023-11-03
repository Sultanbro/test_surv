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
import * as API from '@/stores/api/news.js'

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

		const queryString = window.location.search
		const urlParams = new URLSearchParams(queryString)
		const postId = urlParams.get('post_id')

		this.getMe()
		// eslint-disable-next-line camelcase
		this.getPosts(postId ? { post_id: postId } : undefined)

		this.$root.$on('toggle-white-bg', (value) => {
			this.showBg = value
		})
	},

	methods: {
		hideWhiteBg() {
			this.showBg = false
			window.onscroll = function(){}
			this.$refs.filterComponent.toggleShowFilters(false)
		},

		showWhiteBg() {
			this.showBg = true
		},

		async getMe() {
			try {
				const {data} = await this.axios.get('/me')
				this.me = data.data
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
			}
		},
		async getPosts(params) {
			let data

			try {
				data = await API.newsFetch(params)
			}
			catch (error) {
				console.error(error)
				window.onerror && window.onerror(error)
				return
			}
			if(!data) return

			this.nextPageURL = data.pagination.next_page_url
			this.posts = data.articles
			this.pinnedPosts = data.pinned_articles
			this.$forceUpdate()
		},

		updatePost(data) {
			this.$refs.newsCreate.getOldData(data)
		},

		async getNextPage() {
			let data

			this.showPaginator = false

			try {
				data = await API.newsNextPage(this.nextPageURL)
			}
			catch (error) {
				this.showPaginator = true
				console.error(error)
				window.onerror && window.onerror(error)
				return
			}

			this.nextPageURL = data.pagination.next_page_url
			this.posts = this.posts.concat(data.articles)
			this.showPaginator = true
		}
	}
}
</script>

<style lang="scss">
.news-paginate{
	transform-origin: center;
	transform: scale(0.8);
}
</style>
