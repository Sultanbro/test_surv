<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const NewsPages = () => import(/* webpackChunkName: "NewsPage" */ '@/pages/News/NewsPages')
const BirthdayFeed = () => import(/* webpackChunkName: "NewsPage" */ '@/pages/News/BirthdayFeed')

export default {
	name: 'NewsView',
	components: {
		DefaultLayout,
		NewsPages,
		BirthdayFeed,
	},
	data(){
		return {
			page: '',
			access: '',
		}
	},
	mounted(){
		useAsyncPageData('/news').then(data => {
			this.page = data.page
			this.access = data.access
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout :has-bg="true">
		<link
			rel="stylesheet"
			href="/css/news.css"
		>
		<div class="news-page">
			<NewsPages
				v-show="access"
				:page="page"
				:access="access"
			/>
			<BirthdayFeed v-show="access" />
		</div>
	</DefaultLayout>
</template>
