<script>
import axios from 'axios'
import ProfileGroups from '@/components/profile/ProfileGroups' // настройки user

export default {
	name: 'UserEditMain',
	components: {
		ProfileGroups,
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
	},
	data(){
		return {
			userName: '',
			userLastName: '',
			userEmail: '',
			userBirthday: '',
			userWork_start: '',
			userWork_end: '',
			country: this.user?.working_country || '',
			working_city: this.user?.working_city || '',
			cities: [],
			isSearchResult: false,
			weekdays: (this.user?.weekdays || '0000000').split(''),
		}
	},
	computed:{
		showPositionGroup(){
			return this.user && this.user.position_id === 45
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
		}
	},
	methods: {
		fetchCity(){
			const _token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			if(!this.country){
				this.cities = []
				this.isSearchResult = false
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
			this.isSearchResult = false
		},
		toggleWeekDay(id){
			this.$set(this.weekdays, id, this.weekdays[id] === '1' ? '0' : '1')
		}
	}
}
</script>
<template>
	<div
		id="profile_d"
		class="contacts-info col-md-6 none-block mt-0"
	>
		<h5 class="mb-4">
			Профиль сотрудника
		</h5>
		<div class="form-group row">
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
				>
			</div>
		</div>
		<div class="form-group row">
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
				>
			</div>
		</div>

		<div class="form-group row">
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
				>
			</div>
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
		</div>

		<div class="form-group row">
			<label
				for="lastName"
				class="col-sm-4 col-form-label font-weight-bold"
			>День рождения <span class="red">*</span></label>
			<div class="col-sm-8">
				<input
					name="birthday"
					v-model="userBirthday"
					type="date"
					required
					id="birthday"
					class="form-control"
				>
			</div>
		</div>

		<div class="form-group row">
			<label
				for="position"
				class="col-sm-4 col-form-label font-weight-bold"
			>Должность</label>
			<div class="col-sm-8">
				<select
					name="position"
					id="position"
					class="form-control mb-2"
				>
					<option
						v-for="position in positions"
						:key="position.id"
						:value="position.id"
						:selected="user && user.position_id === position.id"
					>
						{{ position.position }}
					</option>
				</select>

				<ProfileGroups
					v-if="showPositionGroup"
					id="position_group"
					:groups="groups"
					:user_id="user ? user.id : '0'"
					:in-progress="inProgress"
					:user_role="2"
				/>
			</div>
		</div>

		<div class="form-group row">
			<label
				for="userType"
				class="col-sm-4 col-form-label font-weight-bold"
			>Тип <span class="red">*</span>
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
		</div>

		<div class="form-group row">
			<label
				for="programType"
				class="col-sm-4 col-form-label font-weight-bold"
			>Начать день <span class="red">*</span>
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
						<template v-if="program.name === 'Jobtron'">
							Через кнопку в профиле {{ program.name }}
						</template>
						<template v-else>
							{{ program.name }}
						</template>
					</option>
				</select>
			</div>
		</div>

		<div class="form-group row">
			<label
				for="workingDays"
				class="col-sm-4 col-form-label font-weight-bold"
			>Рабочие дни <span class="red">*</span></label>
			<div class="col-sm-8">
				<select
					name="working_days"
					required
					id="workingDays"
					class="form-control"
				>
					<option
						v-for="item in workingDays"
						:key="item.id"
						:value="item.id"
						:selected="user && user.working_day_id == item.id"
					>
						{{ item.name }}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group row">
			<label
				for="workingDays"
				class="col-sm-4 col-form-label font-weight-bold"
			>Найти город <span class="red">*</span></label>
			<div class="col-sm-8">
				<div class="mb-3 xfade">
					<div
						id="selectedCityRU"
						class="form-group row"
					>
						<div class="col-sm-12">
							<input
								name="selectedCityInput"
								v-model="country"
								required
								id="selectedCityInput"
								class="form-control"
								placeholder="Поиск городов"
								@input="fetchCity"
							>
							<input
								name="working_city"
								:value="user && user.working_city ? user.working_city : ''"
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
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
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
						:checked="user && user.full_time === 1"
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
		</div>

		<div class="form-group row">
			<label
				for="workingTimes"
				class="col-sm-4 col-form-label font-weight-bold"
			>Рабочие часы</label>
			<div class="col-sm-8">
				<select
					name="working_times"
					id="workingTimes"
					class="form-control"
				>
					<option
						v-for="item in workingTimes"
						:key="item.id"
						:value="item.id"
						:selected="user && user.working_time_id === item.id"
					>
						{{ item.name }}
					</option>
				</select>
			</div>
		</div>

		<div
			id="workShedule"
			class="form-group row"
		>
			<label
				for="workingTimes"
				class="col-sm-4 col-form-label font-weight-bold"
			>Рабочий график</label>
			<div class="col-sm-8 form-inline">
				<input
					name="work_start_time"
					v-model="userWork_start"
					type="time"
					id="workStartTime"
					class="form-control mr-2 work-start-time"
				>
				<label for="workEndTime">До </label>
				<input
					name="work_start_end"
					v-model="userWork_end"
					type="time"
					id="workEndTime"
					class="form-control mx-2 work-end-time"
				>
			</div>
		</div>

		<div
			class="form-group row"
			id="weekdays"
		>
			<label
				for="workingTimes"
				class="col-sm-4 col-form-label font-weight-bold"
			>Выходные</label>
			<div class="col-sm-8 form-inline">
				<input
					name="weekdays"
					v-model="weekdaysModel"
					type="hidden"
					id="weekdays-input"
				>

				<div
					class="weekday"
					:class="{active: weekdays[1] === '1'}"
					data-id="1"
					@click="toggleWeekDay(1)"
				>
					Пн
				</div>
				<div
					class="weekday"
					:class="{active: weekdays[2] === '1'}"
					data-id="2"
					@click="toggleWeekDay(2)"
				>
					Вт
				</div>
				<div
					class="weekday"
					:class="{active: weekdays[3] === '1'}"
					data-id="3"
					@click="toggleWeekDay(3)"
				>
					Ср
				</div>
				<div
					class="weekday"
					:class="{active: weekdays[4] === '1'}"
					data-id="4"
					@click="toggleWeekDay(4)"
				>
					Чт
				</div>
				<div
					class="weekday"
					:class="{active: weekdays[5] === '1'}"
					data-id="5"
					@click="toggleWeekDay(5)"
				>
					Пт
				</div>
				<div
					class="weekday"
					:class="{active: weekdays[6] === '1'}"
					data-id="6"
					@click="toggleWeekDay(6)"
				>
					Сб
				</div>
				<div
					class="weekday"
					:class="{active: weekdays[0] === '1'}"
					data-id="0"
					@click="toggleWeekDay(0)"
				>
					Вс
				</div>
			</div>
		</div>

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
.searchResultCountry{
    padding: 10px;
    border-bottom: 1px solid white;
    cursor: pointer;
    background-color: #f5f5f5;
    &:hover{
        background-color: rgb(236 244 249);
    }
}
.searchResultCountry-empty{
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
</style>
