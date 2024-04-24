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
				<b-col
					cols="12"
					class="mb-4"
				>
					<JobtronSelect
						v-model="selectDepartment"
						:options="department"
						small
					/>
				</b-col>
				<b-col
					cols="12"
					class="mb-4"
				>
					<JobtronSelect
						v-model="selectPosition"
						:options="position"
						small
					/>
				</b-col>

				<b-col
					cols="12"
					class="mb-4"
				>
					<JobtronSelect
						v-model="selectType"
						:options="type"
						small
					/>
				</b-col>

				<b-col
					cols="12"
					class="mb-4"
				>
					<JobtronSelect
						v-model="selectSale"
						:options="sale"
						small
					/>
				</b-col>

				<b-col
					cols="12"
					class="mb-4"
				>
					<InputText
						v-model="author"
						placeholder="Автор"
					/>
				</b-col>
				<b-col
					cols="12"
					class="mb-4"
				>
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
						>
							<template #footerAfter>
								<JobtronButton
									class="ml-a"
									@click="onClickOutsideRegister"
								>
									ок
								</JobtronButton>
							</template>
						</CalendarInput>
					</div>
				</b-col>
			</b-row>
		</div>
	</div>
</template>

<script>
import InputText from '@ui/InputText.vue'
import JobtronSelect from '@ui/Select.vue'
import CalendarInput from '@ui/CalendarInput/CalendarInput.vue'
import { ChatIconSearchDate } from '@icons'

export default {
	components: {
		InputText,
		JobtronSelect,
		CalendarInput,
		ChatIconSearchDate,
	},

	data() {
		return {
			localSearch: null,
			filters: {
				register: ['', ''] // Stores the start and end date for the registration filter
			},
			touchpadfixRegister: null,
			isOpenRegister: false,
			isFocusRegister: false,
			isOpen: false,
			isFocusSearch: false,
			touchpadTimeout: null,
			selectDepartment: null,
			selectPosition: null,
			selectType: null,
			selectSale: null,
			author: null,
			department: [
				{ title: 'Назначен отделу', value: null },
				{ title: 'Two', value: '2' },
				{ title: 'Three', value: '3' }
			],
			position: [
				{ title: 'Назначен должности', value: null },
				{ title: 'Two', value: '2' },
				{ title: 'Three', value: '3' }
			],
			type: [
				{ title: 'Тип курсы', value: null },
				{ title: 'Two', value: '2' },
				{ title: 'Three', value: '3' }
			],
			sale: [
				{ title: 'Продается', value: null },
				{ title: 'Two', value: '2' },
				{ title: 'Three', value: '3' }
			]
		}
	},
	computed: {
		textRegister() {
			if (!(this.filters.register[0] || this.filters.register[1])) return ''
			if (this.filters.register[0] === this.filters.register[1]) return `Дата регистрации: ${this.filters.register[0]}`
			return `Дата регистрации: ${this.filters.register[0]} - ${this.filters.register[1]}`
		},
	},

	mounted() {
		document.addEventListener('click', this.onClickOutside);
	},
	beforeDestroy() {
		document.removeEventListener('click', this.onClickOutside);
		this.clearTouchpadTimeout();
	},
	methods: {
		onClickOutside(event) {
			if (!this.$el.contains(event.target)) {
				this.isOpen = false;
				this.clearTouchpadTimeout();
			}
		},

		onClickSearch() {
			if (this.isFocusSearch) this.isOpen = !this.isOpen;
		},

		onFocusSearch() {
			this.isFocusSearch = true;
			this.touchpadTimeout = setTimeout(() => {
				this.isOpen = true;
			}, 300);
		},

		onBlurSearch() {
			this.isFocusSearch = false;
		},

		clearTouchpadTimeout() {
			if (this.touchpadTimeout) clearTimeout(this.touchpadTimeout);
		},

		onClickOutsideRegister() {
			this.isOpenRegister = false
			if (this.touchpadfix) clearTimeout(this.touchpadfix)
		},
		onFocusRegister() {
			this.isFocusRegister = true
			this.touchpadfixRegister = setTimeout(() => {
				this.isOpenRegister = true
			}, 300)
		},
		onBlurRegister() {
			this.isFocusRegister = false
		},
		onClearRegister() {
			this.filter.register = ['', '']
		},
		onClickRegister() {
			if (this.isFocusRegister) this.isOpenRegister = !this.isOpenRegister
		},

	},


}
</script>

<style lang="scss" scoped>
select {
	line-height: 34px !important;
}

.UserListFilter {
	margin-top: 20px;
	position: relative;
	width: 400px;

	&-filters {
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

	.multiselect {
		.multiselect__select {
			&:before {
				content: url("/icon/news/inputs/select-arrow.svg");
				display: block;
				border: none;
				top: 50%;
				margin-top: -10px;
			}
		}

		.multiselect__tags {
			background-color: #F7FAFC !important;
			border: none !important;
		}

		.multiselect__placeholder {
			color: #8DA0C1 !important;
		}
	}


}
</style>
