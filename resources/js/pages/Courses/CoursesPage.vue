<template>
	<div class="CoursesPage">
		<b-row class="mt-4">
			<b-col cols="4">
				<CoursesFilter
					v-model="filter"
					:search="search"
					@search="search = $event"
				/>
			</b-col>
			<b-col cols="4">
				<JobtronButton
					small
				>
					Создать курс
				</JobtronButton>
			</b-col>
			<b-col cols="4" />
		</b-row>
	</div>
</template>

<script>
// import {
// 	fetchV2Courses,
// 	fetchV2Course,
// 	createV2Course,
// 	updateV2Course,
// 	deleteV2Course,
// } from '@/stores/api/courses.js'

import CoursesFilter from '@/components/pages/Courses/CoursesFilter.vue'
import JobtronButton from '../../components/ui/Button.vue'

export default {
	name: 'CoursesPage',
	components: {
		CoursesFilter,
		JobtronButton,
	},
	props: {},
	data(){
		return {
			search: '',
			filter: {
				targets: [],
				type: '',
				sale: '',
				created: ['', ''],
			},
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss">
.CoursesPage{
	min-height: calc(100vh - 80px); // для корректной работы клик оутсайд
}
</style>
