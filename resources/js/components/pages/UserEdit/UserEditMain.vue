<script>
import axios from 'axios'
import ProfileGroups from '@/components/profile/ProfileGroups' // настройки user
import UserEditGroups from '@/components/pages/UserEdit/UserEditGroups'
import UserEditError from '@/components/pages/UserEdit/UserEditError'
import { getShiftDays } from '@/composables/shifts'
const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

export default {
	name: 'UserEditMain',
	components: {
		ProfileGroups,
		UserEditGroups,
		UserEditError,
	},
	props: {
		formUserName: {
			type: String,
			default: ''
		},
		formUserLastName: {
			type: String,
			default: ''
		},
		formUserEmail: {
			type: String,
			default: ''
		},
		formUserBirthday: {
			type: String,
			default: ''
		},
		positions:{
			type: Array,
			default: () => []
		},
		groups:{
			type: Array,
			default: () => []
		},
		inProgress:{
			type: Array,
			default: () => []
		},
		programs:{
			type: Array,
			default: () => []
		},
		workingDays:{
			type: Array,
			default: () => []
		},
		workingTimes:{
			type: Array,
			default: () => []
		},
		user: {
			type: Object,
			default: null
		},
		in_groups:{
			type: Array,
			default: () => []
		},
		front_valid:{
			type: Object,
			default: () => null
		},
		errors:{
			type: Object,
			default: () => ({})
		}
	},
	data(){
		return {
			userName: '',
			userLastName: '',
			userEmail: '',
			userBirthday: '',
			userWork_start: '',
			userWork_end: '',
			workChart: null,
			workChartId: null,
			country: this.user?.working_country || '',
			working_city: this.user?.working_city || '',
			cities: [],
			isSearchResult: false,
			weekdays: (this.user?.weekdays || '0000000').split(''),
			position: this.user ? this.user.position_id : '',
			userTimezone: null,
			first_work_day: '',
			timezones: [-11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 12, 12.75, 13, 14],
		}
	},
	computed:{
		showPositionGroup(){
			if(!this.position) return false
			const pos = this.positions.find(p => p.id === this.position)
			return pos.is_head
		},
		weekdayClass(){
			return this.weekdays.map(day => +day ? 'active' : '')
		},
		weekdaysModel(){
			return this.weekdays.join('');
		}
	},
	watch: {
		user(user){
			this.country = user?.working_country || '';
			this.working_city = user?.working_city || '';
			this.weekdays = (user?.weekdays || '0000000').split('');
			this.userName = this.formUserName;
			this.userLastName = this.formUserLastName;
			this.userEmail = this.formUserEmail;
			this.userBirthday = this.formUserBirthday;
			this.userWork_start = user ? user.work_start : '';
			this.userWork_end = user ? user.work_end : '';
			this.position = user ? user.position_id : '';
			this.workChartId = user ? user.work_chart_id : null;
			this.userTimezone = user ? user.timezone : 6;
			this.first_work_day = user ? user.first_work_day.substring(0, 10) : '';
		},
		position(value){
			if(value === -1) {
				window.open('/timetracking/settings?tab=2#nav-home', '_blank')
				this.position = ''
			}
		},
	},
	mounted() {
		document.body.addEventListener('click', ({target}) => {
			const citiesBox = document.getElementById('listSearchResult');
			const citiesInput = document.getElementById('selectedCityInput');
			if (citiesBox && !citiesBox.contains(target) && !citiesInput.contains(target)) {
				this.isSearchResult = false;
			}
		});
		this.axios.get('/work-chart').then(res => {
			this.workChart = res.data.data;
		});
	},
	methods: {
		checkValid(event, err){
			if(this.front_valid.formSubmitted){
				const val = event.target.value;
				if(err === 'email'){
					!this.validateEmail(val) ? this.$emit('valid_change', {name: 'email', bool: false}) : this.$emit('valid_change', {name: 'email', bool: true});
				}
				if(err === 'position'){
					!val.length ? this.$emit('valid_change', {name: 'position', bool: false}) : this.$emit('valid_change', {name: 'position', bool: true});
				}
				if(err !== 'email' && err !== 'position'){
					val.length < 3 ? this.$emit('valid_change', {name: err, bool: false}) : this.$emit('valid_change', {name: err, bool: true});
				}
			}
		},
		validChangeGroup(bool){
			if(this.front_valid.formSubmitted) {
				this.$emit('valid_change', {name: 'group', bool: bool})
			}
		},
		validateEmail(email) {
			return re.test(String(email).toLowerCase());
		},
		fetchCity(){
			const _token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			if(!this.country.length){
				this.cities = []
				return
			}

			axios.post('/selected-country/search/', {
				value: this.country,
				_token
			}).then(({data}) => {
				this.isSearchResult = true
				if(!data[0] || !data[0].length){
					this.cities = []
					return
				}
				this.cities = data[0]
			})
		},
		pasteSearchInput(city){
			this.working_city = city.id
			this.country = `Страна: ${ city.country } Город: ${ city.city }`
			this.isSearchResult = false;
		},
		toggleWeekDay(id){
			this.$set(this.weekdays, id, this.weekdays[id] === '1' ? '0' : '1')
		},
		getShiftDays,
		getTimezoneString(timezone) {
			let absolute_value = Math.abs(timezone);
			let decimal_portion = absolute_value - Math.floor(absolute_value);
			let str = 'UTC'  + (timezone >= 0 ? '+' : '-') + String(Math.floor(absolute_value)) + (decimal_portion == 0 ? '' : ':') + (decimal_portion * 60 || '');
			return str;
		}
	}
}
</script>
<template>
	<div
		id="profile_d"
		class="row"
	>
		<div class="col-12 col-xl-6">
			<div
				class="form-group row"
				:class="{'form-group-error': front_valid.formSubmitted && !front_valid.name}"
			>
				<label
					for="firstName"
					class="col-sm-4 col-form-label font-weight-bold"
				>Имя <span class="red">*</span></label>
				<div class="col-sm-8">
					<input
						name="name"
						v-model="userName"
						type="text"
						required
						id="firstName"
						class="form-control"
						placeholder="Имя сотрудника"
						@input="checkValid($event, 'name')"
					>
				</div>
				<UserEditError
					:errors="errors"
					name="name"
				/>
			</div>

			<div
				class="form-group row"
				:class="{'form-group-error': front_valid.formSubmitted && !front_valid.lastName}"
			>
				<label
					for="lastName"
					class="col-sm-4 col-form-label font-weight-bold"
				>Фамилия <span class="red">*</span></label>
				<div class="col-sm-8">
					<input
						name="last_name"
						v-model="userLastName"
						type="text"
						required
						id="lastName"
						class="form-control"
						placeholder="Фамилия сотрудника"
						@input="checkValid($event, 'lastName')"
					>
				</div>
				<UserEditError
					:errors="errors"
					name="last_name"
				/>
			</div>

			<div
				class="form-group row"
				:class="{'form-group-error': front_valid.formSubmitted && !front_valid.email}"
			>
				<label
					for="email"
					class="col-sm-4 col-form-label font-weight-bold"
				>Email <span class="red">*</span></label>
				<div class="col-sm-8">
					<input
						name="email"
						v-model="userEmail"
						type="email"
						required
						id="email"
						class="form-control"
						placeholder="Email"
						@input="checkValid($event, 'email')"
					>
				</div>
				<UserEditError
					:errors="errors"
					name="email"
				/>
			</div>
			<div
				v-if="user"
				class="form-group row"
			>
				<label
					for="email"
					class="col-sm-4 col-form-label font-weight-bold"
				>Новый пароль</label>
				<div class="col-sm-8">
					<input
						name="new_pwd"
						value=""
						type="text"
						id="new_pwd"
						class="form-control"
						placeholder=""
					>
				</div>
				<UserEditError
					:errors="errors"
					name="new_pwd"
				/>
			</div>

			<div class="form-group row">
				<label
					for="lastName"
					class="col-sm-4 col-form-label font-weight-bold"
				>День рождения</label>
				<div class="col-sm-8">
					<input
						name="birthday"
						v-model="userBirthday"
						type="date"
						required
						id="birthday"
						class="form-control"
						@input="checkValid($event, 'birthday')"
					>
				</div>
				<UserEditError
					:errors="errors"
					name="birthday"
				/>
			</div>

			<div
				class="form-group row"
				:class="{'form-group-error': front_valid.formSubmitted && !front_valid.position}"
			>
				<label
					for="position"
					class="col-sm-4 col-form-label font-weight-bold"
				>Должность <span class="red">*</span></label>
				<div class="col-sm-8">
					<select
						name="position"
						id="position"
						class="form-control mb-2"
						v-model="position"
						@change="checkValid($event, 'position')"
					>
						<option
							v-for="pos in positions"
							:key="pos.id"
							:value="pos.id"
						>
							{{ pos.position }}
						</option>
						<option
							:value="-1"
							class="UserEdit-new-position"
						>
							Добавить новую должность
						</option>
					</select>

					<ProfileGroups
						v-if="showPositionGroup"
						id="position_group"
						:groups="groups"
						:user_id="user ? user.id : '0'"
						:in_groups="inProgress"
						:user_role="2"
					/>
				</div>
				<UserEditError
					:errors="errors"
					name="position"
				/>
			</div>

			<!-- groups tab -->
			<UserEditGroups
				:user="user"
				:groups="groups"
				:in_groups="in_groups"
				:front_valid="front_valid"
				@valid_change="validChangeGroup"
			/>
			<!-- end of groups and books tab -->
		</div>
		<div class="col-12 col-xl-6">
			<div class="form-group row">
				<label
					for="userType"
					class="col-sm-4 col-form-label font-weight-bold"
				>Тип <span class="red">*</span>
					<img
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
						id="info1"
					>
					<b-popover
						target="info1"
						triggers="hover"
						placement="right"
					>
						<p style="font-size: 15px">
							Если выбран "Удаленный работник", то в профиле сотрудника будет кнопка "начать рабочий день", через которую будет происходить контроль рабочего дня.
						</p>
					</b-popover>
				</label>
				<div class="col-sm-8">
					<select
						name="user_type"
						required
						id="userType"
						class="form-control"
					>
						<option
							value="office"
							:selected="user && user.user_type === 'office'"
						>
							Офисный работник
						</option>
						<option
							value="remote"
							:selected="user && user.user_type === 'remote' || user === null"
						>
							Удаленный работник
						</option>
					</select>
				</div>
				<UserEditError
					:errors="errors"
					name="user_type"
				/>
			</div>
			<div class="form-group row">
				<label
					for="programType"
					class="col-sm-4 col-form-label font-weight-bold"
				>Начать работу <span class="red">*</span>
					<img
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
						id="info2"
					>
					<b-popover
						target="info2"
						triggers="hover"
						placement="right"
					>
						<p style="font-size: 15px">
							Актуально только для удаленных работников!<br>
							Тут нужно выбрать способ, через который будет производиться учет рабочего времени. По умолчанию
							выбран способ нажатия на кнопку "начать рабочий день" в профиле сотрудника программного обеспечения Jobtron.
						</p>
					</b-popover>
				</label>
				<div class="col-sm-8">
					<select
						name="program_type"
						required
						id="programType"
						class="form-control"
					>
						<option
							v-for="program in programs"
							:key="program.id"
							:value="program.id"
							:selected="user && user.program_id === program.id"
						>
							<template v-if="program.name === 'Jobtron' || program.name === 'Другая'">
								Через кнопку в профиле Jobtron
							</template>
							<template v-else>
								{{ program.name }}
							</template>
						</option>
					</select>
				</div>
				<UserEditError
					:errors="errors"
					name="program_type"
				/>
			</div>
			<div class="form-group row">
				<label
					for="workingDays"
					class="col-sm-4 col-form-label font-weight-bold"
				>Найти город</label>
				<div class="col-sm-8">
					<div class="mb-3 xfade">
						<div
							id="selectedCityRU"
							class="form-group row"
						>
							<div class="col-sm-12 position-relative">
								<input
									name="selectedCityInput"
									v-model="country"
									required
									id="selectedCityInput"
									class="form-control"
									placeholder="Поиск городов"
									@input="fetchCity"
									@click="isSearchResult = true"
									@change="checkValid($event, 'selectedCityInput')"
									autocomplete="off"
								>
								<input
									name="working_city"
									v-model="working_city"
									hidden
									id="working_city"
								>
								<div
									v-if="isSearchResult"
									id="listSearchResult"
									class="listSearchResult"
								>
									<ul
										id="searchResultCountry"
										class="p-0 mb-0"
									>
										<template v-if="cities && cities.length">
											<li
												v-for="city in cities"
												:key="city.id"
												:id="`li-hover-jquery-${city.id}`"
												class="searchResultCountry"
												@click="pasteSearchInput(city)"
											>
												<strong>Страна: </strong> {{ city.country }} <strong>Город: </strong> {{ city.city }}
											</li>
										</template>
										<li
											v-if="country && cities && !cities.length"
											class="searchResultCountry-empty"
										>
											Нет найденных городов
										</li>
										<li v-if="country.length < 1">
											<span class="help-text">Введите хотябы 1 символ для поиска города</span>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<UserEditError
					:errors="errors"
					name="selectedCityInput"
				/>
			</div>
			<div class="form-group row">
				<label
					class="col-sm-4 col-form-label font-weight-bold"
				>Рабочий график</label>
				<div class="col-sm-8">
					<b-form-select
						name="work-chart"
						v-model="workChartId"
						@change="$emit('selectWorkChart', workChartId)"
					>
						<b-form-select-option
							disabled
							value="null"
						>
							Выберите график работы
						</b-form-select-option>
						<template v-for="chart in workChart">
							<b-form-select-option
								:key="chart.id"
								:value="chart.id"
							>
								{{ getShiftDays(chart) }} (с {{ chart.start_time }} по {{ chart.end_time }}) - {{ chart.text_name }}
							</b-form-select-option>
						</template>
					</b-form-select>
				</div>
				<UserEditError
					:errors="errors"
					name="work-chart"
				/>
			</div>
			<div class="form-group row">
				<label
					class="col-sm-4 col-form-label font-weight-bold"
				>Часовой пояс</label>
				<div class="col-sm-8">
					<b-form-select
						name="timezone"
						v-model="userTimezone"
						@change="$emit('selectWorkChart', workChartId)"
					>
						<b-form-select-option
							disabled
							value="null"
						>
							Выберите часовой пояс
						</b-form-select-option>
						<template v-for="t in timezones">
							<b-form-select-option
								:key="t"
								:value="t"
							>
								{{ getTimezoneString(t) }}
							</b-form-select-option>
						</template>
					</b-form-select>
				</div>
				<UserEditError
					:errors="errors"
					name="timezone"
				/>
			</div>
			<div class="form-group row">
				<label
					class="col-sm-4 col-form-label font-weight-bold"
				>Ставка</label>
				<div class="col-sm-8 d-flex">
					<div class="custom-control custom-radio">
						<input
							type="radio"
							name="full_time"
							class="custom-control-input"
							value="1"
							id="ftr1"
							:checked="user ? user.full_time === 1 : true"
						>
						<label
							class="custom-control-label"
							for="ftr1"
						>
							Full-Time
						</label>
					</div>
					<div class="custom-control custom-radio ml-4">
						<input
							type="radio"
							name="full_time"
							class="custom-control-input"
							value="0"
							id="ftr0"
							:checked="user && user.full_time === 0"
						>
						<label
							class="custom-control-label"
							for="ftr0"
						>
							Part-Time
						</label>
					</div>
				</div>
				<UserEditError
					:errors="errors"
					name="full_time"
				/>
			</div>

			<div class="form-group row">
				<label class="col-sm-4 col-form-label font-weight-bold">
					Первый рабочий день
				</label>
				<div class="col-sm-8 d-flex">
					<input
						name="first_work_day"
						v-model="first_work_day"
						type="date"
						class="form-control"
					>
				</div>
				<UserEditError
					:errors="errors"
					name="first_work_day"
				/>
			</div>

			<!-- -->
			<input
				type="hidden"
				name="working_days"
				id="workingDays"
				:value="user ? user.working_day_id : 1"
			>
			<input
				type="hidden"
				name="working_times"
				id="workingTimes"
				:value="user ? user.working_time_id : 1"
			>
			<!--			<input-->
			<!--				type="hidden"-->
			<!--				name="work_start_time"-->
			<!--				id="workStartTime"-->
			<!--				value="09:00:00"-->
			<!--			>-->
			<!--			<input-->
			<!--				type="hidden"-->
			<!--				name="work_start_end"-->
			<!--				id="workStartEnd"-->
			<!--				value="18:00:00"-->
			<!--			>-->
			<input
				type="hidden"
				name="weekdays"
				id="weekdays-input"
				:value="weekdaysModel"
			>
			<!-- -->

			<div class="form-group row">
				<label
					for="description"
					class="col-sm-4 col-form-label font-weight-bold"
				>Дополнительно</label>
				<div class="col-sm-8">
					<textarea
						name="description"
						rows="3"
						id="description"
						class="form-control"
						:value="user ? user.description : ''"
					/>
				</div>
				<UserEditError
					:errors="errors"
					name="description"
				/>
			</div>
		</div>
	</div>
</template>

<style lang="scss" scoped>
.weekday {
    text-align: center;
    display: flex;
    align-items:center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 3px;
    border: 1px solid #efefef;
    margin-right: 3px;
    cursor: pointer;
    background: #fff;
    color: #000;
    padding: 15px;
    user-select: none;
}
.weekday.active {
    background: #28a745;
}
.weekday:hover {
    background: green;
}

.listSearchResult{
	position: absolute;
	top: 40px;
	left: 15px;
	width: calc(100% - 30px);
	max-height: 350px;
	z-index: 22;
	background-color: #fff;
	border-radius: 6px;
	overflow: auto;
	box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
	font-size: 14px;
	.help-text{
		font-size: 14px;
		color: #777;
		padding: 10px 15px;
		line-height: 1.4;
		display: block;
	}
}

.searchResultCountry {
	padding: 10px;
	border-bottom: 1px solid #f5f5f5;
	cursor: pointer;

	&:hover {
		background-color: #f5f5f5;
	}
}

.searchResultCountry-empty {
	padding: 10px;
	cursor: pointer;
	background-color: #f5f5f5;
}

.radio {
	display: flex;
	font-size: 13px;
	align-items: center;
	cursor: pointer;
	text-align: center;
	margin-right: 25px;

	input {
		position: relative;
		top: 1px;
		margin-right: 7px;
	}
}

.UserEdit-new-position {
	color: green;
}
</style>

<style lang="scss">
// .UserEditMain{}
</style>
