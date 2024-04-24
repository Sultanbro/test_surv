<template>
	<div class="appearance-upload-picture">
		<div class="appearance-upload-image-group">
			<button class="appearance-upload-icon">
				<UpLoadIcon />
				<p class="appearance-upload-icon-text">
					Загрузите иконку
				</p>
			</button>
			<button
				class="appearance-upload-image"
				@mouseover="showTooltip('image')"
				@mouseleave="hideTooltip('image')"
			>
				<UpLoadImage />
				<p class="appearance-upload-icon-text">
					Загрузите обложку
				</p>
				<TooltipCourse v-if="isImageHovered">
					<div class="appearance-upload-icon-text-tooltip">
						Размер изображения: 20 px * 20px
					</div>
					<div class="appearance-upload-icon-text-tooltip">
						Формат: PNG. JPEG, SVG
					</div>
					<div class="appearance-upload-icon-text-tooltip">
						Размер: до 10 MB
					</div>
				</TooltipCourse>
			</button>
		</div>
	</div>
</template>

<script>
import UpLoadIcon from '../../assets/icons/UpLoadIcon.vue';
import UpLoadImage from '../../assets/icons/UpLoadImage.vue';
import TooltipCourse from './TooltipCourse.vue';
export default {
	name: 'AppearanceUploadImage',
	components: {TooltipCourse, UpLoadImage, UpLoadIcon},
	data() {
		return {
			isIconHovered: false,
			isImageHovered: false
		};
	},
	methods: {
		showTooltip(type) {
			if (type === 'icon') {
				this.isIconHovered = true;
			} else if (type === 'image') {
				this.isImageHovered = true;
			}
		},
		hideTooltip(type) {
			if (type === 'icon') {
				this.isIconHovered = false;
			} else if (type === 'image') {
				this.isImageHovered = false;
			}
		}
	}
}
</script>


<style scoped>
.appearance-upload-picture{
		background-color: #F7F7F7;
}

.appearance-upload-icon-text-tooltip{
	margin-bottom: 5px;
}
.appearance-upload-image-group{
	display: flex;
		gap: 26px;
		margin: 25px 38px;
}

.appearance-upload-icon{
		border-radius: 8px;
	background-color: white;
		padding: 10px 25px;
	display: flex;
		flex-direction: column;
		gap: 6px;
		justify-content: center;
		align-items: center;
	font-size: 16px;
}

.appearance-upload-image{
	position: relative;
	border-radius: 8px;
	background-color: white;
	padding: 30px 25px;
	display: flex;
	flex-direction: column;
	gap: 17px;
	justify-content: center;
	align-items: center;
		font-size: 16px;
}

.appearance-upload-icon-text{
	width: 130px;
	text-align: center;
}

</style>
