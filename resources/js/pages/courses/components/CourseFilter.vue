<!-- eslint-disable -->
<template>
	<div v-click-outside="onClickOutside" class="UserListFilter">
		<div class='UserListFilter__header'>
			<InputText v-model="localSearch" small primary placeholder="Поиск" @focus="onFocusSearch"
				@blur="onBlurSearch" @click="onClickSearch" />


			<button class="UserListFilter__header__create">
				<span>Создать курс</span>
				<svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M16.5 9H0.5M8.5 1V17V1Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
		</div>
		<div v-if="isOpen" class="UserListFilter-filters">
			<!-- Отдел, Должность -->
			<b-row class="mb-3">
				<b-col cols="12" class="mb-4">
					<JobtronSelect v-model="selectDepartment" :options="department" small />
				</b-col>
				<b-col cols="12" class="mb-4">
					<JobtronSelect v-model="selectPosition" :options="positions" small />
				</b-col>

				<b-col cols="12" class="mb-4">
					<JobtronSelect v-model="selectType" :options="type" small />
				</b-col>

				<b-col cols="12" class="mb-4">
					<JobtronSelect v-model="selectSale" :options="sale" small />
				</b-col>

				<b-col cols="12" class="mb-4">
					<InputText v-model="author" placeholder="Автор" />
				</b-col>
				<b-col cols="12" class="mb-4">
					<div v-click-outside="onClickOutsideRegister" class="relative">
						<InputText :value="textRegister" readonly small clear placeholder="Дата регистрации"
							@clear="onClearRegister" @focus="onFocusRegister" @blur="onBlurRegister"
							@click="onClickRegister">
							<template #after>
								<ChatIconSearchDate />
							</template>
						</InputText>
						<CalendarInput v-show="isOpenRegister" v-model="filters.register" :tabs="[]"
							:open="isOpenRegister" range popup>
							<template #footerAfter>
								<JobtronButton class="ml-a" @click="onClickOutsideRegister">
									ок
								</JobtronButton>
							</template>
						</CalendarInput>
					</div>
				</b-col>
			</b-row>

			<hr>
			<div class="section__action mt-5">
				<button class="section__action__search" @click='filter'>
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M19.1818 19.091L14.7879 14.6971M17.1616 8.98999C17.1616 13.4529 13.5437 17.0708 9.08081 17.0708C4.6179 17.0708 1 13.4529 1 8.98999C1 4.52708 4.6179 0.90918 9.08081 0.90918C13.5437 0.90918 17.1616 4.52708 17.1616 8.98999Z"
							stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
					<span>Найти</span>
				</button>
				<button class="section__action__reset">Сбросить фильтры</button>
			</div>
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
	props: {
		positions: {
			type: Array,
			default: () => []
		},
		department: {
			type: Array,
			default: () => []
		},
		type: {
			type: Array,
			default: () => []
		},
		sale: {
			type: Array,
			default: () => []
		},
		selectDepartment: {
			type: Object,
			default: () => null
		},
		selectPosition: {
			type: Object,
			default: () => null
		},
		selectType: {
			type: Object,
			default: () => null
		},
		selectSale: {
			type: Object,
			default: () => null
		}, 
		author: {
			type: String,
			default: () => null
		}, 
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
			// selectDepartment: null,
			// selectPosition: null,
			// selectType: null,
			// selectSale: null,
			// author: null,
			// type: [
			// 	{ title: 'Тип курсы', value: null },
			// 	{ title: 'Two', value: '2' },
			// 	{ title: 'Three', value: '3' }
			// ],
			// sale: [
			// 	{ title: 'Продается', value: null },
			// 	{ title: 'Two', value: '2' },
			// 	{ title: 'Three', value: '3' }
			// ]
		}
	},
	computed: {
		textRegister() {
			if (!(this.filters.register[0] || this.filters.register[1])) return ''
			if (this.filters.register[0] === this.filters.register[1]) return `Дата регистрации: ${this.filters.register[0]}`
			return `Дата регистрации: ${this.filters.register[0]} - ${this.filters.register[1]}`
		},
	},
	watch: {
		localSearch(newVal) {
			this.$emit('search', newVal)
		}
	},

	mounted() {
		document.addEventListener('click', this.onClickOutside);
	},
	beforeDestroy() {
		document.removeEventListener('click', this.onClickOutside);
		this.clearTouchpadTimeout();
	},
	methods: {
		filter() {
			this.$emit('get-course')
			this.isOpen = false
		},
		updateValue(event) {
			this.$emit('input', event.target.value); // Emit an event with the new value
		},
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
.UserListFilter {
	margin-top: 20px;
	position: relative;
	width: 600px;

	.InputText {
		width: 400px !important;

	}

	&__header {
		display: flex;
		align-items: center;


		&__create {
			display: flex;
			align-items: center;
			background: #156AE8;
			padding: 10px 20px;
			font-size: 14px;
			color: #fff;
			margin-left: 20px;
			border-radius: 10px;

			svg {
				margin-left: 15px;
			}

		}
	}

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

.section__action {
	display: flex;
	align-items: center;

	&__search {
		display: flex;
		align-items: center;
		background: #156AE8;
		padding: 15px 25px;
		font-size: 14px;
		color: #fff;
		margin-right: 40px;
		border-radius: 10px;

		svg {
			margin-right: 15px;
		}

	}

	&__reset {
		background: none;
		font-size: 14px;
		color: #8DA0C1;
	}
}
</style>
