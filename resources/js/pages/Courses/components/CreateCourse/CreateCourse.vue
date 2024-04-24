<template>
	<div
		v-if="isOpen"
		class="fullscreen-modal"
	>
		<DefaultLayout>
			<div class="create-course-content">
				<SideBarCourse />
				<AppearanceCourse />
				<AppearanceUploadImage />
			</div>
		</DefaultLayout>
	</div>
</template>

<script>
import SideBarCourse from './SideBarCourse.vue';
import AppearanceCourse from './AppearanceCourse.vue';
import AppearanceUploadImage from './AppearanceUploadImage.vue';
import DefaultLayout from '../../../../layouts/DefaultLayout.vue';

export default 	{
	name: 'CreateCourse',
	components: {DefaultLayout, AppearanceUploadImage, AppearanceCourse, SideBarCourse},
	data() {
		return {
			isOpen: false,
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
}


</style>
