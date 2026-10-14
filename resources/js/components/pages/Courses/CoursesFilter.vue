<template>
	<div
		v-click-outside="onClickOutside"
		class="CoursesFilter"
	>
		<InputText
			v-model="localSearch"
			small
			primary
			placeholder="Поиск"
			@focus="onFocusSearch"
			@blur="onBlurSearch"
			@click="onClickSearch"
		/>
		<div
			v-if="isOpen"
			class="CoursesFilter-filters"
		>
			<!-- Назначен -->
			<b-row class="mb-3">
				<b-col cols="12">
					<AccessSelectFormControl
						:items="filters.targets"
						placeholder="Назначен"
						@click="isTargetSelect = true"
					/>
				</b-col>
			</b-row>

			<!-- Тип курса -->
			<b-row class="mb-3">
				<b-col cols="12">
					<JobtronSelect
						v-model="filters.type"
						:options="typeOptions"
						small
					/>
				</b-col>
			</b-row>

			<!-- Продается -->
			<b-row class="mb-3">
				<b-col>
					<JobtronSelect
						v-model="filters.sale"
						:options="saleOptions"
						small
					/>
				</b-col>
			</b-row>

			<!-- Дата создания -->
			<b-row class="mb-3">
				<b-col>
					<div
						v-click-outside="onClickOutsideCreated"
						class="relative"
					>
						<InputText
							:value="textCreated"
							readonly
							small
							clear
							placeholder="Дата регистрации"
							@clear="onClearCreated"
							@focus="onFocusCreated"
							@blur="onBlurCreated"
							@click="onClickCreated"
						>
							<template #after>
								<!-- <i class="far fa-calendar-alt" /> -->
								<ChatIconSearchDate />
							</template>
						</InputText>
						<CalendarInput
							v-show="isOpenCreated"
							v-model="filters.created"
							:tabs="[]"
							:open="isOpenCreated"
							range
							popup
						>
							<template #footerAfter>
								<JobtronButton
									class="ml-a"
									@click="onClickOutsideCreated"
								>
									ок
								</JobtronButton>
							</template>
						</CalendarInput>
					</div>
				</b-col>
			</b-row>

			<b-row>
				<b-col>
					<JobtronButton
						small
						@click="onSubmit"
					>
						Найти
						<i class="fa fa-search" />
					</JobtronButton>
				</b-col>
			</b-row>
		</div>

		<JobtronOverlay
			v-if="isTargetSelect"
			:z="99999"
			@close="isTargetSelect = false"
		>
			<AccessSelect
				v-model="filters.targets"
				:access-dictionaries="{
					users: [],
					positions: accessDictionaries.positions,
					profile_groups: accessDictionaries.profile_groups,
				}"
				:tabs="['Должности', 'Отделы']"
				search-position="beforeTabs"
				submit-button=""
				absolute
			/>
		</JobtronOverlay>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'

import InputText from '@ui/InputText.vue'
import JobtronSelect from '@ui/Select.vue'
import JobtronButton from '@ui/Button.vue'
import CalendarInput from '@ui/CalendarInput/CalendarInput.vue'
import JobtronOverlay from '@ui/Overlay.vue'
import AccessSelect from '@ui/AccessSelect/AccessSelect.vue'
import AccessSelectFormControl from '@ui/AccessSelect/AccessSelectFormControl.vue'
import { ChatIconSearchDate } from '@icons'

export default {
	name: 'CoursesFilter',
	components: {
		InputText,
		JobtronSelect,
		JobtronButton,
		CalendarInput,
		ChatIconSearchDate,
		JobtronOverlay,
		AccessSelect,
		AccessSelectFormControl,
	},
	props: {
		search: {
			type: String,
			default: '',
		},
		value: {
			type: Object,
			required: true,
		},
	},
	data(){
		return {
			touchpadfix: null,
			isOpen: false,
			isFocusSearch: false,

			isTargetSelect: false,

			touchpadfixCreated: null,
			isOpenCreated: false,
			isFocusCreated: false,

			localSearch: this.search,
			filters: JSON.parse(JSON.stringify(this.value)),
			typeOptions: [
				{
					value: '',
					title: 'Тип курса',
				},
				{
					value: '1',
					title: 'Автоматический'
				},
				{
					value: '2',
					title: 'Индивидуальный'
				},
				{
					value: '3',
					title: 'Купленный'
				},
			],
			saleOptions: [
				{
					value: '',
					title: 'Продается?',
				},
				{
					value: '1',
					title: 'Да'
				},
				{
					value: '0',
					title: 'Нет'
				},
			],
		}
	},
	computed: {
		...mapGetters([
			'user',
			'users',
			'accessDictionaries',
		]),
		textСreated(){
			if(!(this.filters.created[0] || this.filters.created[1])) return ''
			if(this.filters.created[0] === this.filters.created[1]) return `Дата создания: ${this.filters.created[0]}`
			return `Дата создания: ${this.filters.created[0]} - ${this.filters.created[1]}`
		},
	},
	watch: {
		search(){
			this.localSearch = this.search
		},
		value(){
			this.filters = JSON.parse(JSON.stringify(this.value))
		},
		localSearch(){
			this.$emit('search', this.localSearch)
		},
	},
	methods: {
		onClickOutside(){
			this.isOpen = false
			if(this.touchpadfix) clearTimeout(this.touchpadfix)
		},
		onClickSearch(){
			if(this.isFocusSearch) this.isOpen = !this.isOpen
		},
		onFocusSearch(){
			this.isFocusSearch = true
			this.touchpadfix = setTimeout(() => {
				this.isOpen = true
			}, 300)
		},
		onBlurSearch(){
			this.isFocusSearch = false
		},

		onClearCreated(){
			this.filters.created = ['', '']
		},
		onClickOutsideCreated(){
			this.isOpenCreated = false
			if(this.touchpadfix) clearTimeout(this.touchpadfix)
		},
		onClickCreated(){
			if(this.isFocusCreated) this.isOpenCreated = !this.isOpenCreated
		},
		onFocusCreated(){
			this.isFocusCreated = true
			this.touchpadfixCreated = setTimeout(() => {
				this.isOpenCreated = true
			}, 300)
		},
		onBlurCreated(){
			this.isFocusCreated = false
		},

		onSubmit(){
			this.isOpen = false
			this.$emit('input', this.filters)
		},
	},
}
</script>

<style lang="scss">
.CoursesFilter{
	position: relative;
	&-filters{
		width: 500px;
		padding: 25px;
		margin-top: 4px;

		position: absolute;
		z-index: 100;
		top: 100%;

		background-color: #fff;
		border-radius: 12px;
		box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.15), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
	}
	.multiselect{
		.multiselect__select{
			&:before{
				content: url("/icon/news/inputs/select-arrow.svg");
				display: block;
				border: none;
				top: 50%;
				margin-top: -10px;
			}
		}
		.multiselect__tags{
			background-color: #F7FAFC !important;
			border: none !important;
		}
		.multiselect__placeholder{
			color: #8DA0C1 !important;
		}
	}
}
</style>
