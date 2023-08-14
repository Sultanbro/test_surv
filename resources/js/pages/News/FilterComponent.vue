<template>
	<div
		class="FilterComponent"
		:class="'news-header__filter ' + (showFilters ? 'news-header__filter--active' : '')"
	>
		<div
			:class="'news-filter ' + (showFilters ? 'news-filter--active' : '')"
			@click="toggleShowFilters(true)"
		>
			<div class="news-filter__left">
				<img
					v-show="showFilters"
					src="/icon/news/filter/filter.svg"
					:class="'news-filter__img ' + (showFilters ? 'news-filter__img--active' : '')"
					alt="img"
				>
				<input
					v-show="showFilters"
					ref="newsFilterInput"
					v-model="query"
					type="text"
					placeholder="Поиск"
					class="news-filter__query"
				>
			</div>
			<img
				class="news-filter__search"
				src="/icon/news/filter/search.svg"
				alt="img"
			>
		</div>

		<div
			v-show="showFilters"
			class="news-filter-modal"
		>
			<div class="news-filter-modal__inputs">
				<div :class="dateType != '' ? 'news-select--selected' : 'news-select'">
					<div
						class="news-select__clear"
						@click="clearDate"
					/>
					<select
						v-model="dateType"
						@change="value = null"
					>
						<option
							value=""
							selected
						>
							Дата
						</option>
						<option value="1">
							Сегодня
						</option>
						<option value="2">
							Вчера
						</option>
						<option value="3">
							Текущая неделя
						</option>
						<option value="4">
							Текущий месяц
						</option>
						<option value="5">
							Диапазон
						</option>
						<option value="6">
							Точная дата
						</option>
					</select>
				</div>

				<div
					v-show="dateType == 5"
					class="news-filter-modal__range"
				>
					<DatePicker
						v-model="value"
						range
						:open.sync="daterangePopupOpen"
						format="DD.MM.YYYY"
						range-separator=" – "
						placeholder="Диапазон"
						@clear="clearDatePicer"
					>
						<template #icon-calendar>
							<img
								alt="img"
								src="/icon/news/inputs/date-picker.svg"
							>
						</template>
						<template #content>
							<CalendarPanel
								:value="innerValue"
								:get-classes="getClasses"
								@select="handleSelect"
							/>
						</template>
					</DatePicker>
				</div>

				<div
					v-show="dateType == 6"
					class="news-filter-modal__range"
				>
					<DatePicker
						v-model="value"
						:open.sync="datePopupOpen"
						format="DD.MM.YYYY"
						placeholder="Точная дата"
						@clear="clearDatePicer"
					>
						<template #icon-calendar>
							<img
								alt="img"
								src="/icon/news/inputs/date-picker.svg"
							>
						</template>
						<template #content>
							<CalendarPanel
								:value="datePickerValue"
								@select="selectSingleDate"
							/>
						</template>
					</DatePicker>
				</div>

				<div :class="author != '' ? 'news-select--selected' : 'news-select'">
					<div
						class="news-select__clear"
						@click="clearAuthor"
					/>
					<select v-model="author">
						<option
							value=""
							selected
						>
							Автор
						</option>
						<!-- eslint-disable -->
						<option
							v-for="user in users"
							:key="user.id"
							:value="user.id"
							v-html="user.name"
						/>
						<!-- eslint-enable -->
					</select>
				</div>

				<div class="news-filter-modal__favourite mb-4">
					<span @click="searchFavourite = !searchFavourite">
						Избранное
					</span>
					<label class="news-checkbox">
						<input
							type="checkbox"
							:checked="searchFavourite ? 'checked' : ''"
							@click="searchFavourite = !searchFavourite"
						>
						<span class="news-checkmark" />
					</label>
				</div>
			</div>

			<div class="news-filter-modal__footer">
				<JobtronButton
					small
					@click="filterNews"
				>
					<img
						alt="img"
						class="news-filter-modal__footer-img"
						src="/icon/news/filter/search.svg"
					>
					Найти новость
				</JobtronButton>
			</div>
		</div>

		<JobtronButton
			v-if="!showFilters"
			title="Пометить все новости прочитанными"
			small
			class="FilterComponent-readAll"
			@click="readAll"
		>
			<i class="fa fa-check-double" />
		</JobtronButton>
	</div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import 'vue2-datepicker/locale/ru';

const {CalendarPanel} = DatePicker;

import {
	mapState,
	mapActions,
} from 'pinia'
import { useCompanyStore } from '@/stores/Company'
import { useUnviewedNewsStore } from '@/stores/UnviewedNewsCount'

import JobtronButton from '@ui/Button'


function isValidDate(date) {
	return date instanceof Date && !isNaN(date);
}

const moment = require('moment');

export default {
	name: 'FilterComponent',
	components: {
		DatePicker,
		CalendarPanel,
		JobtronButton,
	},
	data() {
		return {
			//date
			value: [],

			//date-picker
			datePickerValue: new Date(NaN),
			datePopupOpen: false,

			//date-range-picker
			innerValue: [new Date(NaN), new Date(NaN)],
			daterangePopupOpen: false,

			lang: {
				formatLocale: {
					firstDayOfWeek: 1,
				},
				monthBeforeYear: false,
			},

			showFilters: false,

			query: '',
			author: '',
			dateType: '',
			searchFavourite: false,
		}
	},
	computed: {
		...mapState(useCompanyStore, ['dictionaries']),
		users(){
			return this.dictionaries.users
		}
	},
	watch: {
		// whenever question changes, this function will run
		dateType() {
			this.value = [];
			this.datePickerValue = new Date(NaN);
			this.innerValue = [new Date(NaN), new Date(NaN)];
		},
	},
	created() {},
	mounted() {
		let dateInputs = document.getElementsByClassName('mx-input');

		for (let i = 0; i < dateInputs.length; i++) {
			dateInputs[i].disabled = true;
		}
		//
		// dateInputs.forEach(el => {
		//     el.disabled = true;
		// });

		this.fetchDictionaries();
	},
	methods: {
		...mapActions(useCompanyStore, ['fetchDictionaries']),
		...mapActions(useUnviewedNewsStore, ['getUnviewedNewsCount']),
		clearDate() {
			this.dateType = '';
		},

		clearAuthor() {
			this.author = '';
		},

		clearDatePicer() {
			this.value = [];
			this.datePickerValue = new Date(NaN);
			this.innerValue = [new Date(NaN), new Date(NaN)];
		},
		//datepicker
		getClasses(cellDate, currentDates, classes) {
			if (currentDates.length === 2 &&
                cellDate.getTime() == currentDates[0].getTime()) {
				return 'left';
			}
			if (currentDates.length === 2 &&
                cellDate.getTime() == currentDates[1].getTime()) {
				return 'right';
			}
			if (
				!/disabled|active|not-current-month/.test(classes) &&
                currentDates.length === 2 &&
                cellDate.getTime() > currentDates[0].getTime() &&
                cellDate.getTime() < currentDates[1].getTime()
			) {
				return 'in-range';
			}
			return '';
		},
		handleSelect(date) {
			const [startValue, endValue] = this.innerValue;
			if (isValidDate(startValue) && !isValidDate(endValue)) {
				if (startValue.getTime() > date.getTime()) {
					this.innerValue = [date, startValue];
				} else {
					this.innerValue = [startValue, date];
				}
				this.daterangePopupOpen = false;
				this.value = this.innerValue;
			} else {
				this.innerValue = [date, new Date(NaN)];
			}
		},

		selectSingleDate(date) {
			this.datePickerValue = date;
			this.value = this.datePickerValue;
			this.datePopupOpen = false;
		},


		toggleShowFilters(newValue) {
			if (newValue) {
				this.$refs.newsFilterInput.focus();
				this.$emit('toggleWhiteBg');
			}
			this.showFilters = newValue;
		},

		async getUsers() {
			await this.axios.get('/dictionaries')
				.then(res => {
					this.users = res.data.data.users;
				})
				.catch(res => {
					console.error(res)
				});
		},

		async readAll(){
			await this.axios.post('/news/mark-articles-as-viewed')
			this.getUnviewedNewsCount()
		},

		async filterNews() {
			let startDate = null;
			let endDate = null;

			let getParams = '?';

			switch (this.dateType) {
			case '1': {
				startDate = moment().format('DD-MM-YYYY');
				endDate = startDate;
				break;
			}
			case '2': {
				startDate = moment().subtract(1, 'd').format('DD-MM-YYYY');
				endDate = startDate;
				break;
			}
			case '3': {
				startDate = moment().startOf('week').format('DD-MM-YYYY');
				endDate = moment().endOf('week').format('DD-MM-YYYY');
				break;
			}
			case '4': {
				startDate = moment().startOf('month').format('DD-MM-YYYY');
				endDate = moment().endOf('month').format('DD-MM-YYYY');
				break;
			}
			case '5': {
				if (this.value == null && this.value.length != 2) {
					return
				}
				startDate = moment(this.value[0]).format('DD-MM-YYYY');
				endDate = moment(this.value[1]).format('DD-MM-YYYY');
				break;
			}
			case '6': {
				if (this.value == null) {
					return
				}
				startDate = moment(this.value).format('DD-MM-YYYY');
				endDate = startDate;
				break;
			}
			}

			if (startDate != null && endDate != null) {
				getParams = getParams + 'start_date=' + startDate + '&' + 'end_date=' + endDate;
			}

			if (this.author != '') {
				getParams = getParams + '&' + 'author_id=' + this.author;
			}

			if (this.query != '') {
				getParams = getParams + '&' + 'q=' + this.query;
			}

			if (this.query != '') {
				getParams = getParams + '&' + 'q=' + this.query;
			}

			getParams = getParams + '&' + 'is_favourite=' + (this.searchFavourite == true ? 1 : 0);

			this.$root.$emit('toggle-white-bg', false);
			this.showFilters = false;

			this.$emit('searchNews', {
				params: getParams,
			})
		},
	}
}
</script>

<style lang="scss">
.FilterComponent{
	&-readAll{
		font-size: 2rem;
	}
}
.news-filter-modal__footer{
	&-img{
		filter: invert(100%) sepia(100%) saturate(38%) hue-rotate(254deg) brightness(110%) contrast(110%);
	}
}
.news-filter-modal__range{
	.mx-input{
		height: 22px;
		padding: 0;
	}
	.mx-icon-calendar{
		right: 16px !important;
	}
}
</style>
