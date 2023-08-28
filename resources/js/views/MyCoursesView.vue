<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const MyCourse = () => import(/* webpackChunkName: "MyCoursePage" */ '@/pages/MyCourse')

export default {
	name: 'MyCoursesView',
	components: {
		DefaultLayout,
		MyCourse,
	},
	data(){
		return {
			userId: 0,
		}
	},
	mounted(){
		useAsyncPageData('/my-courses').then(data => {
			this.userId = +data.user_id
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<!-- <script src="/video_learning/playerjs.js" ></script> -->
		<div class="old__content my-course-content">
			<MyCourse
				v-show="userId"
				:user_id="userId"
			/>
		</div>
	</DefaultLayout>
</template>
