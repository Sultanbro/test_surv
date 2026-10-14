<template>
	<button
		v-if="selectedTab === 'База знаний' ? option.type === 3 : ''"
		class="material-modal-select"
		@click="setActiveSelect(option.id)"
	>
		<MaterialModalIcon class="material-modal-icon" />
		<div class="material-modal-title">
			{{ option.name || 'Loading...' }}
		</div>
		<div class="material-modal-select-button">
			<div
				v-if="!isActiveSelect"
				class="material-modal-select-default"
			/>
			<MaterialModalSelectIcon
				v-else
				class="material-modal-select-icon"
			/>
		</div>
	</button>

	<button
		v-else-if="selectedTab === 'Книги' ? option.type === 1 : ''"
		class="material-modal-select"
		@click="setActiveSelect(option.id)"
	>
		<MaterialModalIcon class="material-modal-icon" />
		<div class="material-modal-title">
			{{ option.name || 'Loading...' }}
		</div>
		<div class="material-modal-select-button">
			<div
				v-if="!isActiveSelect"
				class="material-modal-select-default"
			/>
			<MaterialModalSelectIcon
				v-else
				class="material-modal-select-icon"
			/>
		</div>
	</button>

	<button
		v-else-if="selectedTab === 'Видео' ? option.type === 2 : ''"
		class="material-modal-select"
		@click="setActiveSelect(option.id)"
	>
		<MaterialModalIcon class="material-modal-icon" />
		<div class="material-modal-title">
			{{ option.name || 'Loading...' }}
		</div>
		<div class="material-modal-select-button">
			<div
				v-if="!isActiveSelect"
				class="material-modal-select-default"
			/>
			<MaterialModalSelectIcon
				v-else
				class="material-modal-select-icon"
			/>
		</div>
	</button>
</template>

<script>
import MaterialModalIcon from '../../../assets/icons/MaterialModalIcon.vue';
import MaterialModalSelectIcon from '../../../assets/icons/MaterialModalSelectIcon.vue';

export default {
	name: 'MaterialModalSelect',
	components: {MaterialModalSelectIcon, MaterialModalIcon},
	props: {
		selectedTab : {
			type: String,
			default: 'База знаний'
		},
		option : {
			type: Object,
			default: Object
		},
		activeSelect: {
			type: Array,
			default: Array
		}
	},
	data(){
		return {

		}
	},
	computed: {
		isActiveSelect(){
			return this.activeSelect.includes(this.option.id)
		}
	},
	methods: {
		setActiveSelect(id) {
			let updatedActiveSelect;
			if (this.activeSelect.includes(id)) {
				updatedActiveSelect = this.activeSelect.filter(item => item !== id);
			} else {
				updatedActiveSelect = [...this.activeSelect, id];
			}
			this.$emit('update:activeSelect', updatedActiveSelect);
		}
	}
}
</script>

<style scoped>
.material-modal-select{
	min-height: 70px;
		width: 100%;
	display: flex;
		justify-content:space-between;
		align-items:center;
		background-color: white;
}

.material-modal-select:focus{
	outline: none;

}

.material-modal-title{
		font-size: 14px;
		font-weight: 500;
	max-width: 220px;
		color: #0A1323;
}

.material-modal-icon{
		min-width: 40px;
		min-height: 40px;
}

.material-modal-select-icon{
	margin-top: 10px;
	min-width: 30px;
	min-height: 30px;
}
.material-modal-select-button{
	background-color: white;
}

.material-modal-select-default{
	width: 30px;
	height: 30px;
	background-color: #EEF6FF80;
	border-radius: 50%;
	margin-right: 10px;
}
</style>
