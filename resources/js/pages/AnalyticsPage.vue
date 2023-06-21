<template>
	<div
		v-if="groups"
		class="AnalyticsPage"
	>
		<div class="AnalyticsPage-filters row mb-4">
			<div class="col-3">
				<select
					class="form-control"
					v-model="currentGroup"
					@change="fetchData"
				>
					<option
						v-for="group in ggroups"
						:value="group.id"
						:key="group.id"
					>
						{{ group.name }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					class="form-control"
					v-model="monthInfo.currentMonth"
					@change="fetchData"
				>
					<option
						v-for="month in $moment.months()"
						:value="month"
						:key="month"
					>
						{{ month }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					class="form-control"
					v-model="currentYear"
					@change="fetchData"
				>
					<option
						v-for="year in years"
						:value="year"
						:key="year"
					>
						{{ year }}
					</option>
				</select>
			</div>
			<div class="col-1">
				<div
					class="btn btn-primary rounded"
					@click="fetchData()"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<div
				class="col-2"
				v-if="$laravel.is_admin"
			>
				<button
					v-if="!firstEnter && !dataLoaded"
					class="btn btn-info rounded add-s"
					@click="add_analytics()"
					title="Создать аналитику"
				>
					<i class="fa fa-plus-square" />
				</button>

				<button
					v-if="!noan"
					class="btn btn-info rounded add-s"
					@click="archive()"
					title="Архивировать"
				>
					<i class="fa fa-trash" />
				</button>

				<button
					class="btn btn-info rounded add-s ml-2"
					@click="showArchive = true"
					title="Восстановить из архива"
				>
					<i class="fa fa-archive" />
				</button>
			</div>
			<div
				class="col-1"
				v-else
			/>
		</div>
		<template v-if="!firstEnter">
			<template v-if="hasPremission">
				<template v-if="dataLoaded">
					<div class="AnalyticsPage-header wrap mb-4">
						<TopGauges
							:utility_items="data.utility"
							:editable="false"
							wrapper_class="d-flex"
							:key="123"
							page="analytics"
							class="AnalyticsPage-gauges"
						/>

						<div class="p-4">
							<p class="ap-text">
								Процент текучки кадров за прошлый месяц: <span>{{ data.fired_percent_prev }}%</span>
							</p>
							<p class="ap-text">
								Процент текучки кадров за текущий месяц: <span>{{ data.fired_percent }}%</span>
							</p>
							<p class="ap-text">
								В прошлом месяце было уволено: <span>{{ data.fired_number_prev }}</span>
							</p>
							<p class="ap-text">
								В текущем месяце было уволено: <span>{{ data.fired_number }}</span>
							</p>
						</div>
					</div>

					<b-tabs
						type="card"
						:default-active-key="active"
						@change="onTabChange"
					>
						<b-tab
							key="1"
							card
						>
							<template #title>
								<span v-b-popover.hover.top="'таблица продаж'">Сводная</span>
							</template>
							<div class="mb-5 mt-4">
								<AnalyticStat
									:table="data.table"
									:fields="data.columns"
									:activeuserid="activeuserid"
									:is-admin="isAdmin"
									:month-info="monthInfo"
									:group_id="currentGroup"
									:activities="activity_select"
								/>
							</div>

							<CallBase
								v-if="currentGroup == 53"
								:data="call_bases"
								:month-info="monthInfo"
							/>

							<TableDecomposition
								:month="monthInfo"
								:data="data.decomposition"
							/>
						</b-tab>

						<b-tab
							key="2"
							card
							class="position-relative"
						>
							<template #title>
								<span v-b-popover.hover.top="'данные по показателям'">Подробная</span>
							</template>
							<AnalyticsDetailes
								:activities="data.activities"
								:current-group="currentGroup"
								:month-info="monthInfo"
								:activity-select="activity_select"
								@updateActivity="onUpdateActivity"
								@deleteActivity="onDeleteActivity"
								@orderActivity="onOrderActivity"
							/>
						</b-tab>
					</b-tabs>
				</template>

				<template v-else>
					<p class="no-info">
						Аналитика для группы еще не создана
					</p>
				</template>
			</template>

			<template v-else>
				<p class="no-info">
					У вас нет доступа к этой группе
				</p>
			</template>
		</template>
		<div class="empty-space" />

		<!-- Modal restore archived group -->
		<b-modal
			v-model="showArchive"
			title="Восстановить из архива"
			@ok="restore_analytics()"
			size="lg"
			class="modalle"
		>
			<div class="row">
				<div class="col-5">
					<p class="">
						Отдел
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="restore_group"
						class="form-control form-control-sm"
					>
						<option
							:value="archived_group.id"
							v-for="(archived_group, key) in archived_groups"
							:key="key"
						>
							{{ archived_group.name }}
						</option>
					</select>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
import AnalyticStat from '@/components/AnalyticStat'
import CallBase from '@/components/CallBase'
const TopGauges = () => import(/* webpackChunkName: "TopGauges" */ '@/components/TopGauges')  // TOП спидометры, есть и в аналитике
import TableDecomposition from '@/components/tables/TableDecomposition'
import AnalyticsDetailes from '@/components/pages/AnalyticsPage/AnalyticsDetailes'
import { useYearOptions } from '../composables/yearOptions'
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import {
	fetchAnalytics,
	createAnalyticsGroup,
	archiveAnalyticsGroup,
	restoreAnalyticsGroup,
} from '@/stores/api.mock'

const API = {
	fetchAnalytics,
	createAnalyticsGroup,
	archiveAnalyticsGroup,
	restoreAnalyticsGroup,
}

export default {
	name: 'AnalyticsPage',
	components: {
		AnalyticStat,
		CallBase,
		TableDecomposition,
		TopGauges,
		AnalyticsDetailes,
	},
	props: ['groups', 'activeuserid', 'isAdmin'],
	data() {
		return {
			data: [],
			ggroups: [],
			active: '1',
			hasPremission: false, // доступ
			yearActivityTableFields: [],
			yearMin: 0,
			yearMax: 0,
			currentYear: new Date().getFullYear(),
			monthInfo: {},
			currentGroup: null,
			loader: null,
			firstEnter: true,
			showArchive: false,
			askey: 1,
			activity_select: [],
			archived_groups: [],
			call_bases: [], // euras call base unique table
			restore_group: null,
			noan: false, // нет аналитики
			dataLoaded: false,
			list: [
				{ name: 'John', id: 0 },
				{ name: 'Joao', id: 1 },
				{ name: 'Jean', id: 2 }
			],
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
	},
	watch: {
		groups(){
			this.init()
		}
	},
	created() {
		if(this.groups){
			this.init()
		}
	},
	methods: {
		init(){
			// выбор группы
			// переделать на роуты
			const urlParams = new URLSearchParams(window.location.search);
			let group = urlParams.get('group');
			let active = urlParams.get('active');
			let load = urlParams.get('load');

			this.ggroups = this.groups
			this.currentGroup = (group == null) ? this.groups[0].id : parseFloat(group)

			this.active = (active == null) ? '1' : active

			this.setMonth()
			this.setYear()
			this.setActivityYearTableFields()

			if(load != null) {
				this.fetchData()
			}
		},

		/**
		 * ACTIVITY YEAR
		 */
		setActivityYearTableFields() {
			let fieldsArray = [];
			let order = 1;

			fieldsArray.push({
				key: 'name',
				name: 'Сотрудник',
				order: order++,
				classes: ' b-table-sticky-column text-left t-name wd',
			});

			for (let i = 1; i <= 12; i++) {
				if (i.length == 1) i = '0' + i;

				fieldsArray.push({
					key: i,
					name: this.$moment(this.currentYear + '-' + i + '-01').format('MMMM'),
					order: order++,
					classes: 'text-center px-1 month',
				});
			}

			this.yearActivityTableFields = fieldsArray;
		},

		setMonth() {
			this.monthInfo.currentMonth = this.monthInfo.currentMonth ? this.monthInfo.currentMonth : this.$moment().format('MMMM')
			this.monthInfo.month = this.monthInfo.currentMonth ? this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M') : this.$moment().format('M')
			let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM')
			//Расчет выходных дней
			this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.monthInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
			this.monthInfo.weekDays5 = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6,0]) //Колличество выходных
			this.monthInfo.daysInMonth = new Date(this.$moment().format('YYYY'), this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'), 0).getDate() //Колличество дней в месяце
			this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays //Колличество рабочих дней
			this.monthInfo.workDays5 = this.monthInfo.daysInMonth - this.monthInfo.weekDays5 //Колличество рабочих дней

		},
		//Установка выбранного года
		setYear() {
			this.currentYear = this.currentYear ? this.currentYear : this.$moment().format('YYYY')
			this.monthInfo.currentYear = this.currentYear;
		},

		onTabChange(active) {
			this.active = active;
			window.history.replaceState({ id: '100' }, 'Аналитика групп', '/timetracking/an?group=' + this.currentGroup + '&active=' + this.active);
		},

		fetchData() {
			let loader = this.$loading.show();

			API.fetchAnalytics({
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
				group_id: this.currentGroup
			}).then(data => {
				if (data.error && data.error == 'access') {
					this.hasPremission = false
					loader.hide();
					return;
				}
				this.hasPremission = true

				this.setMonth()
				this.setYear()

				let urlParamss = new URLSearchParams(window.location.search);

				this.firstEnter = false

				let active = urlParamss.get('active');
				this.active = (active == null) ? '1' : active

				if(data.error !== undefined) {
					this.dataLoaded = false
					this.noan = true;
					this.archived_groups = data.archived_groups
					this.ggroups = data.groups
				}
				else {
					this.dataLoaded = true
					this.data = data
					this.noan = false;

					this.activity_select = [];

					this.data.activities.forEach(act => {
						this.activity_select.push({
							'name':act.name,
							'id':act.id,
						});
					})

					this.call_bases = data.call_bases;
					this.archived_groups = data.archived_groups;
					this.ggroups = data.groups;
				}

				this.askey++;
				window.history.replaceState({ id: '100' }, 'Аналитика групп', '/timetracking/an?group=' + this.currentGroup + '&active=' + this.active);
				this.monthInfo.workDays = this.work_days = this.getBusinessDateCount(this.monthInfo.month,this.monthInfo.currentYear, data.workdays)
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		getBusinessDateCount(month, year, workdays) {
			month = month - 1;
			let next_month = (month + 1) == 12 ? 0 : month + 1;
			let next_year = (month + 1) == 12 ? year + 1 : year;

			var start = new Date(year, month, 1);
			var end = new Date(next_year, next_month, 1);

			let days = (end - start) / 86400000;

			let business_days = 0,
				weekends = workdays == 5 ? [0,6] : [0];

			for(let i = 1; i <= days; i++) {
				let d = new Date(year, month, i).getDay();
				if(!weekends.includes(d)) business_days++;
			}

			return business_days;
		},

		add_analytics() {
			let loader = this.$loading.show();
			API.createAnalyticsGroup({
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
				group_id: this.currentGroup
			}).then(() => {
				this.$toast.success('Аналитика для группы добавлена!')
				this.fetchData()
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Аналитика для группы не добавлена!')
				alert(error)
			});
		},

		restore_analytics() {
			if (!confirm('Вы уверены что хотите восстановить аналитику группы?')) return

			let loader = this.$loading.show();
			API.restoreAnalyticsGroup({
				id: this.restore_group
			}).then(data => {
				this.$toast.success('Восстановлен!');
				this.currentGroup = this.restore_group
				this.ggroups =data.groups
				this.fetchData();
				this.restore_group = null
				this.showArchive = false
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});
		},

		archive() {
			if (!confirm('Вы уверены что хотите архивировать аналитику группы ?')) return

			let loader = this.$loading.show();
			API.archiveAnalyticsGroup({
				id: this.currentGroup
			}).then(() => {
				this.$toast.success('Архивирован!');
				this.currentGroup = this.ggroups[0].id
				this.fetchData();
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});
		},
		onUpdateActivity(activities){
			this.data.activities = activities
			this.fetchData()
		},
		onDeleteActivity(){
			this.fetchData()
		},
		onOrderActivity(){
			this.fetchData()
		},
	}
}
</script>

<style lang="scss">
.AnalyticsPage{
	&-gauges{
		flex: 1;
		.TopGauges-group,
		.TopGauges-gauges{
			flex: 1;
			justify-content: flex-start !important;
			align-items: flex-end;
		}
		.TopGauges-gauge{
			flex: 0 0 content;
			&:last-of-type{
				margin-left: auto;
			}
		}
	}

	.btn {
		padding: .375rem .75rem;
		&.btn-sm {
			padding: 0.15rem 0.5rem;
		}
	}
	.cell-input{
		padding: 0 !important;
	}
}
.mw30 {
	min-width: 30px;
}
.rating {
	display: inline-block;
	unicode-bidi: bidi-override;
	color: #888888;
	font-size: 25px;
	height: 25px;
	width: auto;
	margin: 0;
	position: relative;
	padding: 0;
}

.rating-upper {
	color: #c52b2f;
	padding: 0;
	position: absolute;
	z-index: 1;
	display: flex;
	top: 0;
	left: 0;
	overflow: hidden;
}

.rating-lower {
	padding: 0;
	display: flex;
	z-index: 0;
}
.ap-text {
	margin: 0;
	display: flex;
	font-size: 12px;
	align-items: center;
}
.ap-text span {
	font-size: 16px;
	font-weight: 700;
	margin-left: 5px;
}
.fz12 {
	font-size: 12px;
	margin-bottom: 0;
	line-height: 20px;
	color: #000 !important;
}
.wrap {
	background: #f3f7f9;
	margin-bottom: 15px;
	padding-top: 15px;
	border: 1px solid #dde8ee;
	border-radius: 5px;
}
</style>
