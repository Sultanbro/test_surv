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
				class="appearance-description-short-course"
				placeholder-text="Введите название курса 🛈"
				small
			/>
			<InputCourse
				class="appearance-description-short-course"
				placeholder-text="Коротко опишите курс 🛈"
				:max-chars="20"
				medium
			/>
			<InputCourse
				class="appearance-description-short-course"
				placeholder-text="Опишите курс более подробно 🛈"
				:max-chars="100"
				big
			/>
		</div>
		<div class="appearance-course-bottom">
			<button class="appearance-course-button">
				Далее
			</button>
		</div>
	</div>
</template>

<script>

import InputCourse from './InputCourse.vue';
import AppearanceMainIcon from '../../assets/icons/AppearanceMainIcon.vue';

export default {
	name: 'AppearanceCourse',
	components: {
		AppearanceMainIcon,
		InputCourse
	}
}
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
