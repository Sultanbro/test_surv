<template>
	<div
		class="AccessSelect"
		:class="{
			'AccessSelect_absolute': absolute
		}"
	>
		<slot
			name="before"
			:search="accessSearch"
			:selected-tab="selectedTab"
		/>
		<AccessSelectSearch
			v-model="accessSearch"
			class="AccessSelect-search"
		/>
		<MaterialSelectTabs
			v-model="selectedTab"
			:tabs="['Сотрудники', 'Отделы', 'Должности']"
			class="AccessSelect-tabs"
		/>
		<div class="material-modal-select-block">
			<div
				v-if="loading"
				class="customSpinner"
			>
				<CustomSpinner />
			</div>
			<div v-else>
				<div v-if="selectedTab === 'Сотрудники'">
					<div
						v-for="(option, index) in users"

						:key="index"
					>
						<TakingModalSelect
							:key="index"
							:option="option"
							:name="option.name"
							:selected-tab="selectedTab"
							:active-select="activeSelect"
							@update:activeSelect="activeSelect = $event"
						/>
					</div>
				</div>
				<div v-if="selectedTab === 'Отделы'">
					<div
						v-for="(option, index) in profileGroups"

						:key="index"
					>
						<TakingModalSelect
							:key="index"
							:option="option"
							:name="option.name"
							:selected-tab="selectedTab"
							:active-select="activeSelect"
							@update:activeSelect="activeSelect = $event"
						/>
					</div>
				</div>
				<div v-if="selectedTab === 'Должности'">
					<div
						v-for="(option, index) in positions"

						:key="index"
					>
						<TakingModalSelect
							:key="index"
							:option="option"
							:name="option.position"
							:selected-tab="selectedTab"
							:active-select="activeSelect"
							@update:activeSelect="activeSelect = $event"
						/>
					</div>
				</div>

				<div class="material-modal-bottom">
					<button
						class="material-modal-added-data"
						@click="test"
					>
						<div class="material-modal-added-data-plus">
							+
						</div>
						<div class="material-modal-added-data-title">
							Добавить новую базу знаний
						</div>
					</button>
					<div class="material-modal-added-border-bottom" />
					<div class="material-modal-added-footer">
						<div class="material-modal-added-footer-text">
							Добавлено {{ activeSelect.length }} материала
						</div>
						<button
							class="material-modal-added-footer-button"
							@click="saveMaterial"
						>
							Сохранить
						</button>
					</div>
				</div>
			</div>
		</div>

		<slot
			name="after"
			:search="accessSearch"
			:selected-tab="selectedTab"
		/>
	</div>
</template>

<script>



import { mapState, mapActions } from 'pinia'
import {useCourseStore} from '../../../../../stores/createCourse';
import CustomSpinner from '../../../../../components/Spinners/Spinner.vue';
import MaterialSelectTabs from '../MaterialCourse/MaterialSelectTabs.vue';
import AccessSelectSearch from '../../../../../components/ui/AccessSelect/AccessSelectSearch.vue';
import TakingModalSelect from './TakingModalSelect.vue';

const ALL = [{id: 0, type: 0, name: 'Все'}]

export default {
	name: 'TakingModal',
	components: {
		TakingModalSelect,
		AccessSelectSearch,
		MaterialSelectTabs,
		CustomSpinner,

	},
	props: {
		isAccessOverlay: {
			type: Boolean,
			default: true
		},
		values: {
			type: Array,
			default: () => []
		},
		tabs: {
			type: Array,
			default: () => ['Сотрудники', 'Отделы', 'Должности']
		},
		preselectTab: {
			type: String,
			default: ''
		},
		value: {
			type: [Array, String],
			default: () => [],
		},
		submitButton: {
			type: String,
			default: 'Пригласить сотрудника'
		},
		searchPosition: {
			type: String, // beforeTabs, afterTabs
			default: 'beforeTabs'
		},
		accessDictionaries: {
			type: Object,
			default: () => ({
				users: [],
				/* eslint-disable-next-line camelcase */
				profile_groups: [],
				positions: [],
			})
		},
		submitDisabled: {
			type: Boolean,
			default: false
		},
		single: {
			type: Boolean,
			default: false
		},
		absolute: {
			type: Boolean,
			default: false
		},
		search: {
			type: String,
			default: ''
		}
	},



	data(){
		return {
			selectedTab: 'Сотрудники',
			accessList: JSON.parse(JSON.stringify(this.value)),
			options: [],
			accessSearch: '',
			filteredOptions: [],
			type: 3,
			loading: false,
			activeSelect: [],
			tabTypeMap: {
				'Сотрудники': 3,
				'Отделы': 1,
				'Должности': 2,
			},
			positions:[],
			profileGroups:[],
			users:[]
		}
	},

	computed: {
		...mapState(useCourseStore, ['materialBlocks']),
		searchResults(){
			const lowerSearch = this.accessSearch.toLowerCase()
			const result = []

			// Пользователи
			this.accessDictionaries.users.forEach(user => {
				if(user.name?.toLowerCase().includes(lowerSearch)) return result.push({
					...user,
					type: 1
				})
				if(user.position?.toLowerCase().includes(lowerSearch)) return result.push({
					...user,
					type: 1
				})
			})

			// Отделы
			this.accessDictionaries.profile_groups.forEach(group => {
				if(group.name?.toLowerCase().includes(lowerSearch)) return result.push({
					...group,
					type: 2
				})
			})

			// Должности
			this.accessDictionaries.positions.forEach(position => {
				if(position.name?.toLowerCase().includes(lowerSearch)) return result.push({
					...position,
					type: 3
				})
			})
			return result
		}
	},
	watch: {
		value(value){
			this.accessList = typeof value === 'string' ? ALL : JSON.parse(JSON.stringify(value))
		},
		search(value){
			this.accessSearch = value
		},
		selectedTab(newValue){

			if(typeof this.accessList === 'string') this.accessList = ALL
			this.type = this.tabTypeMap[newValue];
			this.filterType();

		},



	},

	mounted() {
		this.fetch();
	},
	methods: {
		...mapActions(useCourseStore, ['addMaterialsBlock']),


		addMaterial(){
			if (this.activeSelect){
				const users = this.activeSelect.map(name => {
					return this.users.find(option => option.name === name);
				}).filter(Boolean);
				this.addMaterialsBlock(users)

				const profileGroups = this.activeSelect.map(name => {
					return this.profileGroups.find(option => option.name === name);
				}).filter(Boolean);
				this.addMaterialsBlock(profileGroups)

				const positions = this.activeSelect.map(name => {
					return this.positions.find(option => option.position === name);
				}).filter(Boolean);
				this.addMaterialsBlock(positions)



			}
		},
		test(){
		},

		saveMaterial(){
			this.addMaterial()
			this.$emit('close')
		},
		onSubmit(){
			if(this.selectedTab === 'Все') return this.$emit('submit', ALL)
			this.$emit('submit', this.accessList)

		},
		filterType() {
			this.addMaterial();
			this.filteredOptions = this.options.filter(el => {
				return el.type == this.type
			});
		},

		addSelectedAttr() {
			this.filteredOptions.forEach(el => {
				el.selected = this.values.findIndex(v => v.id == el.id && v.type == el.type) != -1
			});
		},


		fetch() {
			this.loading = true;
			this.axios
				.get('/messenger/api/v2/company', {})
				.then((response) => {
					this.users = response.data.users;
					this.positions = response.data.positions;
					this.profileGroups = response.data.profile_groups;

					this.options = [...this.users, ...this.profileGroups, ...this.positions];


					this.filterType();
					this.addSelectedAttr();
				})
				.catch((error) => {
					console.error(error);
				})
				.finally(() => {
					this.loading = false;
				});
		},
	},
}
</script>

<style lang="scss" scoped>
.AccessSelect {
  display: flex;
  flex-flow: column nowrap;

  overflow: auto;
  &-icon {
	filter: invert(99%) sepia(1%) saturate(1439%) hue-rotate(238deg) brightness(107%) contrast(69%);
	cursor: pointer;
	&:hover {
		filter: invert(27%) sepia(73%) saturate(2928%) hue-rotate(209deg) brightness(96%) contrast(89%);
	}
  }
  // &-search,
  // &-tabs,
  // &-list{}
  &-list {
	display: flex;
	flex-flow: column nowrap;
	overflow: hidden;
  }

  &_absolute{
	width: 420px;
	height: 720px;
	max-height: 80vh;
	padding: 20px;
	border-radius: 15px;

	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);

	background-color: #fff;
  }
}

.material-modal-select-block{
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 10px 0;
}

.material-modal-added-data{
  border: 1px solid #E5EBF6;
  padding: 15px 25px;
  display: flex;
  gap: 10px;
  align-items: center;
  color: #658CDA;
  background-color: white;
}

.material-modal-added-data-plus{
  font-size: 22px;
  font-weight: 500;
}

.material-modal-added-data-title{
  font-size: 14px;
  font-weight: 500;
}

.material-modal-bottom{
  position: sticky;
  bottom: -20px;
  width: 100%;
  background: white;
}
.material-modal-added-border-bottom{
  margin: 10px 0;
  border-bottom: 1px solid #EAEAEA;
  width: 100%;
}

.material-modal-added-footer{
  margin: 10px 0 0 0 ;
  display: flex;
  justify-content: space-between;
  padding-bottom: 10px;
}

.material-modal-added-footer-text{
  padding: 15px 10px;
  color: #8DA0C1;
  font-size: 13px;
  line-height: 20px;
}

.material-modal-added-footer-button{
  background-color: #156AE8;
  padding: 10px 38px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  line-height: 20px;
  color: white;
}

.customSpinner{
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

</style>
