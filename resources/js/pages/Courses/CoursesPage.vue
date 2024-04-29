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
					@click="openModal"
				>
					Создать курс
				</JobtronButton>
			</b-col>
			<b-col cols="4" />
		</b-row>
		<CreateCourse ref="fullscreenModal" />
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
import CreateCourse from './components/CreateCourse/CreateCourse.vue';

export default {
	name: 'CoursesPage',
	components: {
		CreateCourse,
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
	methods: {
		openModal() {
			this.$refs.fullscreenModal.openModal();
		},
	},
}
</script>

<style lang="scss">
.CoursesPage{
	min-height: calc(100vh - 80px); // для корректной работы клик оутсайд
}
</style>
