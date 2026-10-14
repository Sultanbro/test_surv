<template>
	<div class="material-course">
		<div class="material-course-header">
			<MaterialHeaderIcon />
			<div class="material-course-icon">
				<MaterialsIcon current-color="white" />
			</div>
		</div>
		<div class="material-course-body">
			<h1
				title="Tooltip content"
				class="material-course-title"
			>
				Название курса
			</h1>


			<div class="material-course-content">
				<div
					v-if="materialBlocks.length > 0"
					class="material-course-blocks"
					:class="{'activeMaterialBlock' : materialBlocks.length > 0}"
				>
					<button
						v-if="selectedBlocks.length > 0"
						class="material-course-blocks-button-del"
						@click="deleteSelectedBlocks"
					>
						<p>Удалить выбранные</p>
					</button>
					<div
						v-for="item in materialBlocks"
						:key="item.id"
						class="material-course-blocks-content"
						:class="{ 'selected': selectedBlocks.includes(item.name) }"
						@click="toggleBlockSelection(item.name)"
					>
						<div class="material-course-blocks-name">
							<MaterialBlock />
							<p>{{ item.name }}</p>
						</div>
						<div class="material-course-blocks-name">
							<button

								:id="'tooltip-' + item.id + '-question'"
								class="buttonHover"
								@click="addTest(item)"
								@mouseover="showTooltip(item.id + '-question')"
								@mouseleave="hideTooltip(item.id + '-question')"
							>
								<MaterialQuestionIcon />
							</button>
							<button
								v-if="item.selected === false"
								:id="'tooltip-' + item.id + '-trash'"
								class="buttonHover"
								@click="deleteSelectedBlocks(item.name)"
								@mouseover="showTooltip(item.id + '-trash')"
								@mouseleave="hideTooltip(item.id + '-trash')"
							>
								<MaterialTrashIcon />
							</button>
							<div
								v-if="tooltipVisible[item.id + '-question']"
								class="material-course-tooltip show"
							>
								Нет тестовых вопросов
							</div>
							<div
								v-if="tooltipVisible[item.id + '-trash']"
								class="material-course-tooltip show"
							>
								Удалить блок
							</div>
							<MaterialDropDown />
						</div>
					</div>
					<button class="material-course-blocks-button">
						<p> Cмотреть</p><MaterialEyeIcon />
					</button>
				</div>
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
			</div>

			<JobtronOverlay
				v-if="isAccessOverlay"
				@close="isAccessOverlay = false"
			>
				<MaterialModal
					submit-button=""
					absolute
					@close="isAccessOverlay = false"
				/>
			</JobtronOverlay>
			<JobtronOverlay
				v-if="isAddedTest"
				@close="isAddedTest = false"
			>
				<MaterialTestModal
					:selected-test="selectedTest"
					@close="isAddedTest = false"
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
import {useCourseStore} from '../../../../../stores/createCourse';
import {mapState, mapActions} from 'pinia';
import MaterialBlock from '../../../assets/icons/MaterialBlock.vue';
import MaterialDropDown from '../../../assets/icons/MaterialDropDown.vue';
import MaterialQuestionIcon from '../../../assets/icons/MaterialQuestionIcon.vue';
import MaterialEyeIcon from '../../../assets/icons/MaterialEyeIcon.vue';
import MaterialTrashIcon from '../../../assets/icons/MaterialTrashIcon.vue';
import MaterialTestModal from './MaterialTestModal.vue';



export default {
	name: 'MaterialCourse',
	components: {
		MaterialTestModal,
		MaterialTrashIcon,
		MaterialEyeIcon,
		MaterialQuestionIcon,
		MaterialDropDown,
		MaterialBlock,
		MaterialModal,
		JobtronOverlay, UploadMaterial, InputFile, UpLoadIcon, CustomSpinner, MaterialsIcon, MaterialHeaderIcon},
	data() {
		return {
			loadingIcon: false,
			loadingImage: false,
			isAccessOverlay: false,
			tooltipVisible: {},
			selectedBlocks: [],
			isAddedTest: false,
			selectedTest: [],


		}
	},

	computed:{
		...mapState(useCourseStore, ['materialBlocks']),
	},

	methods: {
		...mapActions(useCourseStore, ['removeMaterialBlocksByNames']),
		toggleBlockSelection(id) {
			const index = this.selectedBlocks.indexOf(id);
			if (index === -1) {
				this.selectedBlocks.push(id);
			} else {
				this.selectedBlocks.splice(index, 1);
			}
		},
		addTest(item){
			this.selectedTest = [...this.selectedTest, item]
			this.isAddedTest = true;
		},
		deleteSelectedBlocks(id) {
			if (id.length > 0) {
				const index = this.selectedBlocks.indexOf(id);
				if (index === -1) {
					this.selectedBlocks.push(id);
				} else {
					this.selectedBlocks.splice(index, 1);
				}
			}
			this.removeMaterialBlocksByNames(this.selectedBlocks)
			this.selectedBlocks = [];

		},
		showTooltip(id) {
			this.$set(this.tooltipVisible, id, true);
		},
		hideTooltip(id) {
			this.$set(this.tooltipVisible, id, false);
		},
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
.selected {
	border:2px solid #156AE8;
}
.buttonHover{
		padding: 5px;
		background-color: #F8F9FD;
}
.material-course-tooltip{
	width: max-content;
	position: absolute;
	top: 10%;
	font-weight: 600;
	left: 70%;
	transform: translateX(-50%);
	background-color: #fff;
	color: black;
	padding: 10px 25px;
	border-radius: 5px;
	z-index: 99999;
	display: none;

}
.material-course-tooltip.show {
	display: block;
}




.material-course-blocks-button{
		max-width: 119px;
	width: 100%;
	margin-top: 8px;
	display: flex;
		gap: 5px;
		border-radius: 8px;
		padding: 6px 12px;
		color: #8DA0C1;
		border: 1px solid #8DA0C1;
		background-color: white;
}
.material-course-blocks-button-del{
	justify-content: center;
	margin-left: auto;
	max-width: 229px;
	width: 100%;
	margin-top: 8px;
	display: flex;
	gap: 5px;
	border-radius: 8px;
	padding: 12px;
	color: white;
	background-color: #0B172D;
}
.material-course-content{
	display: flex;
		gap: 15px;

}
.activeMaterialBlock{
	max-width: 793px;
	width: 100%;
		margin: 24px 33px;
}
.material-course-blocks{
		background-color: #FFFFFF;
	display: flex;
		flex-direction: column;
		gap: 8px;
		border-radius: 20px;
		padding: 24px;
}

.material-course-blocks-content{
	position: relative;
		background-color: #F8F9FD;
		display: flex;
		justify-content: space-between;
		color: #156AE8;
		padding: 8px;
		border-radius: 8px;
}

.material-course-blocks-name{
	display: flex;
		gap:6px;
}

.material-course{
	width: 100%;
		height: 100vh;
	display: flex;
	flex-direction: column;
	background-color: #fafafa;
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
	background-color: white;
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
