<template>
	<div class="AnalyticsDetailes">
		<b-tabs
			type="card"
			class="mt-4"
			:default-active-key="active_sub_tab"
			@change="showSubTab"
		>
			<template #tabs-end>
				<div
					v-click-outside="onClickOutside"
					class="AnalyticsDetailes-controls ml-a"
				>
					<JobtronButton
						class="ChatIcon-parent"
						fade
						small
						@click="onClickPopup"
					>
						<i
							class="fa fa-bars"
							style="font-size:14px"
						/>
					</JobtronButton>
					<PopupMenu v-if="isPopup">
						<div
							class="PopupMenu-item wsnw"
							@click="add_activity()"
						>
							<i
								class="fa fa-plus-square"
								style="font-size:14px"
							/>
							Добавить таблицу
						</div>
						<div
							class="PopupMenu-item wsnw"
							@click="showOrder = true"
						>
							<i class="fas fa-sort-amount-down" />
							Сортировать
						</div>
					</PopupMenu>
				</div>
			</template>
			<b-tab
				v-for="(activityItem, index) in activities"
				:key="index"
				:title="activityItem.name"
				@change="showSubTab(index)"
			>
				<!-- Switch month and year of Activity in detailed -->
				<button
					class="btn btn-default rounded mt-4"
					@click="switchToMonthInActivity(index)"
				>
					Месяц
				</button>
				<button
					class="btn btn-default rounded mt-4"
					@click="switchToYearInActivity(index)"
				>
					Год
				</button>

				<!-- tabs -->
				<div
					v-if="activityStates[index] !== undefined"
					class="mt-2"
				>
					<!-- Month tab of activity in detailed -->
					<div v-if="activityStates.at(index) === 'month'">
						<TableActivityNew
							v-if="activityItem.type == 'default'"
							:key="activityItem.id"
							:month="monthInfo"
							:activity="activityItem"
							:group_id="currentGroup"
							:work_days="monthInfo.workDays"
							:editable="activityItem.editable == 1 ? true : false"
							:hidden-users="activityHiddenUsers[activityItem.id] || []"
							class="AnalyticsDetailes-monthTable"
						/>

						<TableActivityCollection
							v-if="activityItem.type == 'collection'"
							:key="activityItem.id"
							:month="monthInfo"
							:activity="activityItem"
							:is_admin="true"
							:price="activityItem.price"
							class="AnalyticsDetailes-monthTable"
						/>

						<TableQualityWeekly
							v-if="activityItem.type == 'quality'"
							:key="activityItem.id"
							:month-info="monthInfo"
							:items="activityItem.records"
							:editable="activityItem.editable == 1 ? true : false"
							class="AnalyticsDetailes-monthTable"
						/>
					</div>

					<!-- Year tab of activity in detailed -->
					<div v-else>
						<div class="table-container table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th
											v-for="(field, key) in yearActivityTableFields"
											:key="key"
											:class="field.classes"
										>
											<div>{{ field.name }}</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr
										v-for="( row, indexYear ) in yearActivityTable"
										:key="indexYear"
									>
										<td
											v-for="(field, key) in yearActivityTableFields"
											:key="key"
											:class="field.classes"
											:style="field.key === 'name' || !row[field.key] ? '' : `background: ${getCellColor(row[field.key])};`"
											:data-key="field.key"
										>
											<div>{{ row[field.key] }}</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</b-tab>
		</b-tabs>

		<!-- Modal Order activity -->
		<b-modal
			v-model="showOrder"
			title="Порядок активностей"
			size="md"
			@ok="save_order()"
		>
			<Draggable
				:list="activitySelect"
			>
				<div
					v-for="act in activitySelect"
					:key="act.id"
					class="drag_item"
				>
					<i class="fa fa-grip-vertical" />
					<span class="AnalyticsSort-title">
						{{ act.name }}
					</span>
					<i
						class="fa fa-trash pointer ml-a"
						@click="delete_activity(act)"
					/>
				</div>
			</Draggable>
		</b-modal>

		<!-- Modal Create activity -->
		<b-modal
			v-model="showActivityModal"
			title="Добавить активность"
			size="lg"
			class="modalle"
			@ok="create_activity()"
		>
			<div class="fz-14">
				<div class="row">
					<div class="col-6 mb-3">
						<p class="">
							Название активности
						</p>
					</div>
					<div class="col-6 mb-3">
						<input
							v-model="activity.name"
							type="text"
							class="form-control form-control-sm"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-6 mb-3">
						<p class="">
							Метод
						</p>
					</div>
					<div class="col-6 mb-3">
						<select
							v-model="activity.plan_unit"
							class="form-control form-control-sm"
						>
							<option
								v-for="(value, key) in plan_units"
								:key="key"
								:value="key"
							>
								{{ value }}
							</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-6 mb-3">
						<p class="">
							План (Если сумма, на&nbsp;день)
						</p>
					</div>
					<div class="col-6 mb-3">
						<input
							v-model="activity.daily_plan"
							type="number"
							class="form-control form-control-sm"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-6 mb-3">
						<p class="">
							Кол-во рабочих дней в&nbsp;неделе
						</p>
					</div>
					<div class="col-6 mb-3">
						<input
							v-model="activity.weekdays"
							type="number"
							class="form-control form-control-sm"
							min="1"
							max="7"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-6 mb-3">
						<p class="">
							Ед.&nbsp;измерения (Символ в&nbsp;конце показателя)
						</p>
					</div>
					<div class="col-6 mb-3">
						<input
							v-model="activity.unit"
							type="text"
							class="form-control form-control-sm"
						>
					</div>
				</div>
				<div class="row">
					<div class="col-6 mb-3 d-flex align-items-center">
						<p class="mb-0">
							Редактируемый
						</p>
						<JobtronSwitch
							v-model="activity.editable"
							class="ml-4"
						/>
					</div>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */

const TableActivityNew = () => import(/* webpackChunkName: "TableActivityNew" */ '@/components/tables/TableActivityNew')
import TableActivityCollection from '@/components/tables/TableActivityCollection'
import TableQualityWeekly from '@/components/tables/TableQualityWeekly'
import PopupMenu from '@ui/PopupMenu'
import JobtronButton from '@ui/Button'
import JobtronSwitch from '@ui/Switch'
import Draggable from 'vuedraggable'

import {
	fetchAnalyticsMonthlyStats,
	createAnalyticsActivity,
	deleteAnalyticsActivity,
	updateAnalyticsOrder,
	getAnalyticsActivityHiddenUsers,
} from '@/stores/api'

const API = {
	fetchAnalyticsMonthlyStats,
	createAnalyticsActivity,
	deleteAnalyticsActivity,
	updateAnalyticsOrder,
	getAnalyticsActivityHiddenUsers,
}

function percentMinMax(value, min, max){
	return (value - min) / (max - min)
}

export default {
	name: 'AnalyticsDetailes',
	components: {
		TableActivityNew,
		TableActivityCollection,
		TableQualityWeekly,
		PopupMenu,
		JobtronButton,
		JobtronSwitch,
		Draggable,
	},
	props: {
		activities: {
			type: Array,
			default: () => []
		},
		activitySelect: {
			type: Array,
			default: () => []
		},
		currentGroup: {
			type: Number,
			default: 0
		},
		monthInfo: {
			type: Object,
			default: () => ({})
		}
	},
	data(){
		return {
			isPopup: false,
			showOrder: false,
			showActivityModal: false,
			active_sub_tab: 0,
			activity: {
				name: null,
				daily_plan: null,
				plan_unit: null,
				unit: null,
				editable: 1,
				weekdays: 6,
			},
			plan_units: {
				minutes: 'Сумма показателей',
				percent: 'Среднее значение',
				less_sum: 'Не более, сумма',
				less_avg: 'Не более, сред. зн.',
			},
			activityStates: this.activities.map(() => 'month'),
			users: [], // year table of activity
			statistics: [], // year table of activity,
			yearActivityTable: [],
			yearActivityTableFields: [
				{
					key: 'name',
					name: 'Сотрудник',
					// order: order++,
					classes: ' b-table-sticky-column text-left t-name wd',
				},
				...this.$moment.months().map((name, index)  => ({
					key: index + 1,
					name,
					classes: 'text-center px-1 month',
				}))
			],
			activityHiddenUsers: {},
		}
	},
	watch: {
		activities(){
			this.activityStates = this.activities.map(() => 'month')
		}
	},
	created(){
		this.init()
	},
	methods: {
		init(){
			API.getAnalyticsActivityHiddenUsers(this.currentGroup).then(hidden => {
				this.activityHiddenUsers = hidden
			})
		},
		showSubTab(tab) {
			this.active_sub_tab = tab
		},
		switchToMonthInActivity(index) {
			this.$set(this.activityStates, index, 'month')
		},
		switchToYearInActivity(index) {
			this.$set(this.activityStates, index, 'year')
			this.fetchYearTableOfActivity(this.activities[index].id);
		},

		add_activity() {
			this.showActivityModal = true;
		},
		create_activity() {
			const loader = this.$loading.show();
			API.createAnalyticsActivity({
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.monthInfo.currentYear,
				activity: this.activity,
				group_id: this.currentGroup
			}).then(activities => {
				this.$toast.success('Активность для группы добавлена!')

				this.activity = {
					name: null,
					daily_plan: null,
					plan_unit: null,
					unit: null,
					editable: 1,
					weekdays: 6,
				};

				this.$emit('updateActivity', activities)
				this.showActivityModal = false
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Активность для группы не добавлена!')
				alert(error)
			});
		},
		delete_activity(act) {
			if (!confirm('Вы уверены что хотите удалить активность \'' + act.name + '\' ?')) return

			const loader = this.$loading.show();
			API.deleteAnalyticsActivity({
				id: act.id
			}).then(() => {
				this.$toast.success('Удален!');
				this.$emit('deleteActivity', act)
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});
		},
		save_order() {
			const loader = this.$loading.show();
			API.updateAnalyticsOrder({
				activities: this.activity_select
			}).then(() => {
				this.$toast.success('Порядок сохранен!');
				this.showOrder = false;
				this.$emit('orderActivity')
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});
		},

		fetchYearTableOfActivity(activityId) {
			const loader = this.$loading.show();

			API.fetchAnalyticsMonthlyStats({
				group_id: this.currentGroup,
				date: {
					year: this.monthInfo.currentYear,
					month: this.monthInfo.month
				},
				activity_id: activityId
			}).then(({data}) => {
				this.users = data.users
				this.formYearActivityTable(data.statistics)
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},
		formYearActivityTable(stats) {
			const res = [];

			this.users.forEach((user) => {
				if(user.deleted_at) return
				if(stats[user.id] !== undefined) {
					res.push({
						name: this.fullNameOfUser(user),
						...this.normalizeStat(stats[user.id]),
					});
				}
			});

			this.yearActivityTable = res;
			this.yearCalcMinMax()
		},
		normalizeStat(obj) {
			const res = {}

			Object.keys(obj).forEach((key) => {
				res[key] = obj[key] == 0
					? 0
					: parseInt(obj[key].total) === parseFloat(obj[key].total)
						? parseInt(obj[key].total)
						: Number(obj[key].total).toFixed(2);
			});

			return res
		},
		yearCalcMinMax(){
			let min = 9999999999
			let max = 0
			this.yearActivityTable.forEach(row => {
				Object.keys(row).forEach(key => {
					if(key === 'name') return
					const value = parseFloat(row[key])
					if(value < min) min = value
					if(value > max) max = value
				})
			})
			this.yearMin = min
			this.yearMax = max
		},

		getCellColor(value) {
			const perc = percentMinMax(value, this.yearMin, this.yearMax) * 100
			let r, g, b = 0;
			if(perc < 50) {
				r = 235;
				g = Math.round(5.1 * perc);
				b = Math.round(113 - 1.13 * perc);
			}
			else {
				g = 225;
				r = Math.round(510 - 5.1 * perc);
			}
			const h = r * 0x10000 + g * 0x100 + b * 0x1;
			return '#' + ('000000' + h.toString(16)).slice(-6);
		},

		fullNameOfUser(user) {
			return user.last_name
				? user.last_name + ' ' + user.name
				: user.name
		},

		onClickPopup(){
			this.isPopup = true
		},
		onClickOutside(){
			this.isPopup = false
		},
	}
}
</script>

<style lang="scss">
.AnalyticsDetailes{
	&-controls{
		position: relative;
	}
	&-monthTable{
		margin-top: -38px;
	}
}

.drag_item {
	display: flex;
	justify-content: flex-start;
	align-items: center;
	gap: 0.5rem;

	padding: 5px 8px;
	margin-bottom: 5px;
	border: 1px solid #eee;

	transform: translateZ(0px);
	background: white;
	border-radius: 5px;
	cursor: move;
}
</style>
