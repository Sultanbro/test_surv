<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const Page = () => import(/* webpackChunkName: "Page" */ '@/pages/Page/Page')

export default {
	name: 'TemplateView',
	components: {
		DefaultLayout,
		Page,
	},
	data(){
		return {
			page: '',
		}
	},
	mounted(){
		useAsyncPageData('/page').then(data => {
			this.page = data.page
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<Page
				v-show="page"
				:page="page"
			/>
		</div>
	</DefaultLayout>
</template>