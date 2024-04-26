<template>
	<div class="material-course">
		<div class="material-course-header">
			<MaterialHeaderIcon />
			<div class="material-course-icon">
				<MaterialsIcon current-color="white" />
			</div>
		</div>
		<div class="material-course-body">
			<h1 class="material-course-title">
				Название курса
			</h1>
			<div class="material-course-button-group">
				<button
					class="material-upload-icon"
				>
					<InputFile
						accept="image/*"
						@change="uploadFile('icon')"
					>
						<div v-if="loadingIcon">
							<CustomSpinner />
						</div>
						<template v-else>
							<UpLoadIcon />
							<p class="material-upload-icon-text">
								Загрузите html
							</p>
						</template>
					</InputFile>
				</button>
				<button
					class="material-upload-icon"
					@click="uploadModal"
				>
					<UploadMaterial />
					<p class="material-upload-icon-text">
						Выбрать материал
					</p>
				</button>
			</div>
			<JobtronOverlay
				v-if="isAccessOverlay"
				@close="isAccessOverlay = false"
			>
				<MaterialModal

					submit-button=""
					absolute
				/>
			</JobtronOverlay>
		</div>
		<div class="material-course-bottom">
			<button
				class="material-course-back-button"
				@click="addCourse"
			>
				Назад
			</button>

			<button
				class="material-course-next-button"
				@click="addCourse"
			>
				Далее
			</button>
		</div>
	</div>
</template>

<script>

import MaterialHeaderIcon from '../../../assets/icons/MaterialHeaderIcon.vue';
import MaterialsIcon from '../../../assets/icons/MaterialsIcon.vue';
import CustomSpinner from '../../../../../components/Spinners/Spinner.vue';
import UpLoadIcon from '../../../assets/icons/UpLoadIcon.vue';
import InputFile from '../../../../../components/ui/InputFile.vue';
import UploadMaterial from '../../../assets/icons/UploadMaterial.vue';
import JobtronOverlay from '../../../../../components/ui/Overlay.vue';
import MaterialModal from './MaterialModal.vue';

export default {
	name: 'MaterialCourse',
	components: {
		MaterialModal,
		JobtronOverlay, UploadMaterial, InputFile, UpLoadIcon, CustomSpinner, MaterialsIcon, MaterialHeaderIcon},
	data() {
		return {
			loadingIcon: false,
			loadingImage: false,
			isAccessOverlay: false,

		}
	},
	methods: {
		async uploadFile(type) {
			// const files = event.target.files;
			// const file = files[0];
			// const fileId = Date.now();

			if (type === 'icon') {
				this.loadingIcon = true;
			} else if (type === 'image') {
				this.loadingImage = true;
			}

			setTimeout(() => {
				if (type === 'icon') {
					this.loadingIcon = false;
				} else if (type === 'image') {
					this.loadingImage = false;
				}
				// store.commit('addCourse', {
				// 	id: fileId,
				// 	file: file
				// });
			}, 2000);
		},
		addCourse(){

		},
		uploadModal() {
			this.isAccessOverlay = true;
		}
	}
}
</script>

<style scoped>
.material-course{
		width: 100%;
		height: 100vh;
	display: flex;
	flex-direction: column;
}

.material-course-header{
	position: relative;
	background-color: #F8F9FD;
	display: flex;
	align-items: center;
	justify-content: center;
		padding: 20px;
}
.material-course-icon{
	position: absolute;
	bottom: -20px;
	left: 37px;
	background-color: white;
	padding: 10px;
	border-radius: 50%;
}

.material-course-body{
	display: flex;
	flex-direction:column;
}

.material-course-title{
		font-weight: 700;
		font-size: 32px;
		line-height: 36px;
		padding: 40px;
}

.material-course-button-group{
	display: flex;
	gap: 26px;
	margin: 25px 38px;
}

.material-upload-icon{
	width: 180px;
		height: 180px;
	border-radius: 8px;
	background-color: #F7F7F7;
	padding: 10px 25px;
	display: flex;
	flex-direction: column;
	gap: 6px;
	justify-content: center;
	align-items: center;
	font-size: 16px;
}

.material-upload-icon-text{
	width: 130px;
	text-align: center;
}

.material-course-bottom{
	display: flex;
		gap: 10px;
	margin-top: auto;
	background-color: #F8F9FD;
	padding: 30px 38px;
}

.material-course-back-button{
	background-color: #F8F9FD;
	border: 1px #156AE8 solid;
	padding: 12px 55px;
	color: #156AE8;
	border-radius: 8px;
}

.material-course-next-button{
	background-color: #156AE8;
	padding: 12px 55px;
	color: white;
	border-radius: 8px;
}

.material-course-next-button:hover{
	background-color: #1e61c5;
}

.material-course-back-button:hover{
	border-color: #1e61c5;
}
</style>
