<template>
	<div class="appearance-course">
		<div class="appearance-course-header">
			<img
				class="appearance-course-header-logo"
				src="/icon/AppearanceLogoIcon.svg"
			>
			<div class="appearance-course-main-logo">
				<AppearanceMainIcon />
			</div>
		</div>

		<div class="appearance-course-input-group">
			<InputCourse
				v-model="courseName"
				class="appearance-description-short-course"
				placeholder-text="Введите название курса 🛈"
				:min-chars="2"
				small
			/>
			<InputCourse
				v-model="shortDescription"
				class="appearance-description-short-course"
				placeholder-text="Коротко опишите курс 🛈"
				:min-chars="20"
				medium
			/>
			<InputCourse
				v-model="detailedDescription"
				class="appearance-description-short-course"
				placeholder-text="Опишите курс более подробно 🛈"
				:min-chars="200"
				big
			/>
		</div>
		<div class="appearance-course-bottom">
			<button
				class="appearance-course-button"
				@click="createCourse"
			>
				Далее
			</button>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import {useCourseStore} from '@/stores/CreateCourse';
import InputCourse from './InputCourse.vue';
import AppearanceMainIcon from '../../../assets/icons/AppearanceMainIcon.vue';



export default {
	name: 'AppearanceCourse',
	components: {
		AppearanceMainIcon,
		InputCourse
	},

	data() {
		return {
			courseName: '',
			shortDescription: '',
			detailedDescription: ''
		};
	},
	computed:{
		...mapState(useCourseStore, ['courses'])
	},
	methods: {
		...mapActions(useCourseStore, ['addCourse']),
		createCourse() {
			this.addCourse({courseName: this.courseName,
				shortDescription: this.shortDescription,
				detailedDescription: this.detailedDescription})
			this.courseName = '';
			this.shortDescription = '';
			this.detailedDescription = '';
		}
	}
};
</script>

<style scoped>
.appearance-course{
		height: 100vh;
		display: flex;
		flex-direction:column;
		max-width: 600px;
		width: 100%	;
}

.appearance-course-header{
	position: relative;
		background-color: #F8F9FD;
		display: flex;
		align-items: center;
		justify-content: center;
}

.appearance-course-input-group{
		display: flex;
		flex-direction: column;
		gap: 20px;
		padding: 20px 39px;
}

.appearance-course-header-logo{

		width: 128px;
		height: 93px;
		padding: 11px;
}

.appearance-course-bottom{
	margin-top: auto;
	background-color: #F8F9FD;
	padding: 20px 39px;

}

.appearance-course-button{
	background: #156AE8;
		padding: 12px 54px;
		font-size: 14px;
		color: white;
		border-radius: 8px;
}

.appearance-course-button:hover{
	background: #0e4ca9;
}
.appearance-course-main-logo{
	position: absolute;
	bottom: -20px;
	left: 37px;
		background-color: white;
		padding: 10px;
		border-radius: 50%;
}

</style>
