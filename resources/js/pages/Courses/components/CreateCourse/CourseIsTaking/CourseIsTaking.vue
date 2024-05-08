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
					<input
						class="taking-input"
						@click="isAccessOverlay=true"
					>
					<SearchIcon class="taking-search" />
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

export default {
	name: 'CourseIsTaking',
	components: {
		TakingModal,
		JobtronOverlay, SearchIcon, InfoIcon, TakingDropDown, CursesIsTakingIcon, CoursesIsTakingHeaderIcon},
	data(){
		return{
			isAccessOverlay: false,
		}
	}

}
</script>

<style scoped>
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
		padding: 10px 20px;
		max-width:507px;
		width: 100%;
}

.taking-search{
	position: relative;
	left: 0%;
	top: 22%;
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
