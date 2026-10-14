<template>
	<div class="material-course">
		<div class="material-course-header">
			<SettingsHeaderIcon />
			<div class="material-course-icon">
				<SettingIcon current-color="white" />
			</div>
		</div>
	</div>
</template>

<script>
import SettingsHeaderIcon from '../../../assets/icons/SettingsHeaderIcon.vue';
import SettingIcon from '../../../assets/icons/SettingIcon.vue';

export default {
	name: 'SettingsCourses',
	components: {
		SettingsHeaderIcon,
		SettingIcon
	}
}
</script>

<style scoped lang="scss">
.material-course {
	width: 100%;
	height: 100vh;
	display: flex;
	flex-direction: column;
	background-color: #fafafa;
}

.material-course-header {
	position: relative;
	background-color: #f8f9fd;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 20px;
}
.material-course-icon {
	position: absolute;
	bottom: -20px;
	left: 37px;
	background-color: white;
	padding: 10px;
	border-radius: 50%;
}
</style>../../../assets/icons/SettingsHeaderIcon.vue
