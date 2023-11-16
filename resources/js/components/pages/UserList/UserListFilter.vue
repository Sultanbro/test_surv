<template>
	<div
		v-click-outside="onClickOutside"
		class="UserListFilter"
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
			class="UserListFilter-filters"
		>
			<!-- Отдел, Должность -->
			<b-row class="mb-3">
				<b-col cols="6">
					<JobtronSelect
						v-model="filters.group"
						:options="groups"
						small
					/>
				</b-col>
				<b-col cols="6">
					<JobtronSelect
						v-model="filters.position"
						:options="positions"
						small
					/>
				</b-col>
			</b-row>

			<!-- remote/office, full/part -->
			<b-row class="mb-3">
				<b-col cols="6">
					<JobtronSelect
						v-model="filters.type"
						:options="typeOptions"
						small
					/>
				</b-col>
				<b-col cols="6">
					<JobtronSelect
						v-model="filters.fullpart"
						:options="partOptions"
						small
					/>
				</b-col>
			</b-row>

			<!-- Работающие\Уволенные -->
			<b-row class="mb-3">
				<b-col>
					<JobtronSelect
						v-model="filters.userType"
						:options="userTypes"
						small
					/>
				</b-col>
			</b-row>

			<!-- Дата регистрации -->
			<b-row class="mb-3">
				<b-col>
					<div
						v-click-outside="onClickOutsideRegister"
						class="relative"
					>
						<InputText
							:value="textRegister"
							readonly
							small
							clear
							placeholder="Дата регистрации"
							@clear="onClearRegister"
							@focus="onFocusRegister"
							@blur="onBlurRegister"
							@click="onClickRegister"
						>
							<template #after>
								<!-- <i class="far fa-calendar-alt" /> -->
								<ChatIconSearchDate />
							</template>
						</InputText>
						<CalendarInput
							v-show="isOpenRegister"
							v-model="filters.register"
							:tabs="[]"
							:open="isOpenRegister"
							range
							popup
						/>
					</div>
				</b-col>
			</b-row>

			<!-- Дата восстановления -->
			<b-row
				v-if="filters.userType !== 'trainees'"
				class="mb-3"
			>
				<b-col>
					<div
						v-click-outside="onClickOutsideRestore"
						class="relative"
					>
						<InputText
							:value="textRestore"
							readonly
							small
							clear
							placeholder="Дата восстановления"
							@clear="onClearRestore"
							@focus="onFocusRestore"
							@blur="onBlurRestore"
							@click="onClickRestore"
						>
							<template #after>
								<!-- <i class="far fa-calendar-alt" /> -->
								<ChatIconSearchDate />
							</template>
						</InputText>
						<CalendarInput
							v-show="isOpenRestore"
							v-model="filters.restore"
							:tabs="[]"
							:open="isOpenRestore"
							range
							popup
						/>
					</div>
				</b-col>
			</b-row>

			<!-- Дата увольнения -->
			<b-row class="mb-3">
				<b-col>
					<div
						v-click-outside="onClickOutsideFire"
						class="relative"
					>
						<InputText
							:value="textFire"
							readonly
							small
							clear
							placeholder="Дата увольнения"
							@clear="onClearFire"
							@focus="onFocusFire"
							@blur="onBlurFire"
							@click="onClickFire"
						>
							<template #after>
								<!-- <i class="far fa-calendar-alt" /> -->
								<ChatIconSearchDate />
							</template>
						</InputText>
						<CalendarInput
							v-show="isOpenFire"
							v-model="filters.fire"
							:tabs="[]"
							:open="isOpenFire"
							range
							popup
						/>
					</div>
				</b-col>
			</b-row>

			<!-- Дата принятия -->
			<b-row
				v-if="filters.userType !== 'trainees'"
				class="mb-3"
			>
				<b-col>
					<div
						v-click-outside="onClickOutsideApplied"
						class="relative"
					>
						<InputText
							:value="textApplied"
							readonly
							small
							clear
							placeholder="Дата принятия"
							@clear="onClearApplied"
							@focus="onFocusApplied"
							@blur="onBlurApplied"
							@click="onClickApplied"
						>
							<template #after>
								<!-- <i class="far fa-calendar-alt" /> -->
								<ChatIconSearchDate />
							</template>
						</InputText>
						<CalendarInput
							v-show="isOpenApplied"
							v-model="filters.applied"
							:tabs="[]"
							:open="isOpenApplied"
							range
							popup
						/>
					</div>
				</b-col>
			</b-row>
			<b-row class="mb-3">
				<b-col>
					<multiselect
						v-model="filters.segment"
						:options="segments"
						:multiple="true"
						:preserve-search="true"
						:hide-selected="true"
						:close-on-select="false"
						:show-no-options="false"
						placeholder="Все сегменты"
						label="name"
						track-by="id"
					/>
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
	</div>
</template>

<script>
import InputText from '@ui/InputText.vue'
import JobtronSelect from '@ui/Select.vue'
import JobtronButton from '@ui/Button.vue'
import CalendarInput from '@ui/CalendarInput/CalendarInput.vue'
import { ChatIconSearchDate } from '@icons'

export default {
	name: 'UserListFilter',
	components: {
		InputText,
		JobtronSelect,
		JobtronButton,
		CalendarInput,
		ChatIconSearchDate,
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
		groups: {
			type: Array,
			default: () => []
		},
		positions: {
			type: Array,
			default: () => []
		},
		userTypes: {
			type: Array,
			default: () => []
		},
		segments: {
			type: Array,
			default: () => []
		},
	},
	data(){
		return {
			touchpadfix: null,
			isOpen: false,
			isFocusSearch: false,

			touchpadfixRegister: null,
			isOpenRegister: false,
			isFocusRegister: false,

			touchpadfixRestore: null,
			isOpenRestore: false,
			isFocusRestore: false,

			touchpadfixFire: null,
			isOpenFire: false,
			isFocusFire: false,

			touchpadfixApplied: null,
			isOpenApplied: false,
			isFocusApplied: false,

			localSearch: this.search,
			filters: JSON.parse(JSON.stringify(this.value)),
			typeOptions: [
				{
					value: '',
					title: 'Тип',
				},
				{
					value: 'remote',
					title: 'Remote'
				},
				{
					value: 'office',
					title: 'Office'
				},
			],
			partOptions: [
				{
					value: '',
					title: 'Full/Part',
				},
				{
					value: 'full',
					title: 'Full-time'
				},
				{
					value: 'part',
					title: 'Part-time'
				},
			],
		}
	},
	computed: {
		textRegister(){
			return (this.filters.register[0] || this.filters.register[1] ? `Дата регистрации: ${this.filters.register[0]} - ${this.filters.register[1]}` : '')
		},
		textRestore(){
			return (this.filters.restore[0] || this.filters.restore[1] ? `Дата восстановления: ${this.filters.restore[0]} - ${this.filters.restore[1]}` : '')
		},
		textFire(){
			return (this.filters.fire[0] || this.filters.fire[1] ? `Дата увольнения: ${this.filters.fire[0]} - ${this.filters.fire[1]}` : '')
		},
		textApplied(){
			return (this.filters.applied[0] || this.filters.applied[1] ? `Дата принятия: ${this.filters.applied[0]} - ${this.filters.applied[1]}` : '')
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

		onClearRegister(){
			this.filters.register = ['', '']
		},
		onClickOutsideRegister(){
			this.isOpenRegister = false
			if(this.touchpadfix) clearTimeout(this.touchpadfix)
		},
		onClickRegister(){
			if(this.isFocusRegister) this.isOpenRegister = !this.isOpenRegister
		},
		onFocusRegister(){
			this.isFocusRegister = true
			this.touchpadfixRegister = setTimeout(() => {
				this.isOpenRegister = true
			}, 300)
		},
		onBlurRegister(){
			this.isFocusRegister = false
		},

		onClearRestore(){
			this.filters.restore = ['', '']
		},
		onClickOutsideRestore(){
			this.isOpenRestore = false
			if(this.touchpadfix) clearTimeout(this.touchpadfix)
		},
		onClickRestore(){
			if(this.isFocusRestore) this.isOpenRestore = !this.isOpenRestore
		},
		onFocusRestore(){
			this.isFocusRestore = true
			this.touchpadfixRestore = setTimeout(() => {
				this.isOpenRestore = true
			}, 300)
		},
		onBlurRestore(){
			this.isFocusRestore = false
		},

		onClearFire(){
			this.filters.fire = ['', '']
		},
		onClickOutsideFire(){
			this.isOpenFire = false
			if(this.touchpadfix) clearTimeout(this.touchpadfix)
		},
		onClickFire(){
			if(this.isFocusFire) this.isOpenFire = !this.isOpenFire
		},
		onFocusFire(){
			this.isFocusFire = true
			this.touchpadfixFire = setTimeout(() => {
				this.isOpenFire = true
			}, 300)
		},
		onBlurFire(){
			this.isFocusFire = false
		},

		onClearApplied(){
			this.filters.applied = ['', '']
		},
		onClickOutsideApplied(){
			this.isOpenApplied = false
			if(this.touchpadfix) clearTimeout(this.touchpadfix)
		},
		onClickApplied(){
			if(this.isFocusApplied) this.isOpenApplied = !this.isOpenApplied
		},
		onFocusApplied(){
			this.isFocusApplied = true
			this.touchpadfixApplied = setTimeout(() => {
				this.isOpenApplied = true
			}, 300)
		},
		onBlurApplied(){
			this.isFocusApplied = false
		},
		onSubmit(){
			this.isOpen = false
			this.$emit('input', this.filters)
		},
	},
}
</script>

<style lang="scss">
.UserListFilter{
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
