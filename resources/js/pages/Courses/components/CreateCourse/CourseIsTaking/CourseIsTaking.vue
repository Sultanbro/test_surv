<template>
	<div class="material-course">
		<div class="material-course-header">
			<CoursesIsTakingHeaderIcon />
			<div class="material-course-icon">
				<CursesIsTakingIcon />
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
				<div>
					<TakingDropDown :options="[{name:'Автоматический', id: 1}, {name:'Индивидуальный', id: 2} ]" />
				</div>
				<div>
					<div class="taking-title-input">
						<svg
							width="12"
							height="2"
							viewBox="0 0 12 2"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M11 1L1 1"
								stroke="#A6B7D4"
								stroke-width="1.75"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
						</svg>
						<p>Выберите сотрудников</p>
						<InfoIcon />
					</div>
					<div v-if="materialBlocks.length > 0">
						<div
							class="taking-input taking-count"
						>
							<div
								v-for="course in materialBlocks"
								:key="course.id"
								class="taking-content"
							>
								<img
									class="taking-avatar"
									:src="course.avatar"
								>
								<p class="taking-name">
									{{ course.name }}
								</p>
								<svg
									width="11"
									height="10"
									viewBox="0 0 11 10"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
									@click="removeUser(course.name)"
								>
									<path
										d="M9.75 0.75L1.25 9.25M1.25 0.75L9.75 9.25"
										stroke="#0B172D"
										stroke-linecap="round"
										stroke-linejoin="round"
									/>
								</svg>
							</div>
						</div>
					</div>
					<div v-else>
						<button
							class="taking-input"
							@click="isAccessOverlay=true"
						/>
						<SearchIcon class="taking-search" />
					</div>
				</div>
			</div>
			<JobtronOverlay
				v-if="isAccessOverlay"
				@close="isAccessOverlay = false"
			>
				<TakingModal
					submit-button=""
					absolute
					@close="isAccessOverlay = false"
				/>
			</JobtronOverlay>
		</div>
		<div class="material-course-bottom">
			<button
				class="material-course-back-button"
			>
				Назад
			</button>

			<button
				class="material-course-next-button"
			>
				Далее
			</button>
		</div>
	</div>
</template>

<script>
import CoursesIsTakingHeaderIcon from '../../../assets/icons/CoursesIsTakingHeaderIcon.vue';
import CursesIsTakingIcon from '../../../assets/icons/CursesIsTakingIcon.vue';
import TakingDropDown from './TakingDropDown.vue';
import InfoIcon from '../../../assets/icons/InfoIcon.vue';
import SearchIcon from '../../../assets/icons/SearchIcon.vue';
import JobtronOverlay from '../../../../../components/ui/Overlay.vue';
import TakingModal from './TakingModal.vue';
import {mapActions, mapState} from 'pinia';
import {useCourseStore} from '../../../../../stores/createCourse';

export default {
	name: 'CourseIsTaking',
	components: {
		TakingModal,
		JobtronOverlay, SearchIcon, InfoIcon, TakingDropDown, CursesIsTakingIcon, CoursesIsTakingHeaderIcon},
	data(){
		return{
			isAccessOverlay: false,
		}
	},
	computed:{
		...mapState(useCourseStore, ['materialBlocks']),

	},
	methods:{
		...mapActions(useCourseStore, ['removeMaterialBlocksByNames']),
		removeUser(name){
			this.removeMaterialBlocksByNames([name])
		}
	}

}
</script>

<style scoped>

.taking-count{
	display: flex;
		gap: 4px;
		flex-wrap: wrap;
		width: 531px;
}

.taking-content{
	gap: 6px;
	align-items: center;
	display: flex;
	justify-content: space-between;
	border-radius: 100px;
	padding: 4px 6px;
	background-color: #EBF3FB;
	width: max-content;
}

.taking-name{
		font-size: 18px;
		color: #0B172D;
}
.taking-avatar{
		border-radius: 50%;
		object-fit: cover;
}
.material-course-content{
	display: flex;
	flex-direction: column;
	gap: 29px;
	padding: 36px;
}

.taking-input{
	position: absolute;
		background-color: #F7FAFC;
		border-radius: 8px;
		padding: 20px 20px;
		max-width:507px;
		width: 100%;
}

.taking-search{
	position: relative;
	left: 10px;
	top: 10px;
}

.taking-title-input{
	display: flex;
		gap: 10px;
		font-size: 20px;
	font-weight: 600;
	align-items: center;
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
