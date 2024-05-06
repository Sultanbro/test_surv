<template>
	<div
		v-if="isOpen"
		class="fullscreen-modal"
	>
		<DefaultLayout>
			<div class="create-course-content">
				<SideBarCourse
					:active-link="activeLink"
					@update:activeLink="activeLink = $event"
				/>
				<AppearanceCourse v-if="activeLink===1" />
				<AppearanceUploadImage v-if="activeLink===1" />
				<MaterialCourse v-if="activeLink===2" />
			</div>
		</DefaultLayout>
	</div>
</template>

<script>
import SideBarCourse from './Appearance/SideBarCourse.vue';
import AppearanceCourse from './Appearance/AppearanceCourse.vue';
import AppearanceUploadImage from './Appearance/AppearanceUploadImage.vue';
import DefaultLayout from '../../../../layouts/DefaultLayout.vue';
import MaterialCourse from './MaterialCourse/MaterialCourse.vue';

export default {
	name: 'CreateCourse',
	components: {MaterialCourse, DefaultLayout, AppearanceUploadImage, AppearanceCourse, SideBarCourse },
	data() {
		return {
			isOpen: false,
			activeLink: 1,
		};
	},
	methods: {
		openModal() {
			this.isOpen = true;
		},
		closeModal() {
			this.isOpen = false;
		},

	},
};
</script>

<style scoped>
.fullscreen-modal {
	position: fixed;
	top: 0;
	left: 0;
	width: 100vw;
	height: 100vh;
	background-color: white;
	z-index: 9999;


}

.create-course-content{
	display: flex;
		justify-content: space-around;
	background-color: #fafafa;
}


</style>
