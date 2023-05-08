<template>
	<div v-if="groupss">
		<div class="mb-0">
			<!-- filters -->
			<div class="row mb-4">
				<div class="col-3">
					<v-select
						:options="groups"
						label="name"
						v-model="selectedGroup"
						class="group-select"
					>
						<template #option="{ name, salary_approved, id }">
							<div class="selector">
								<p style="margin: 0">
									{{ name }} : <span v-if="showAccruals">{{ accruals[id] }}</span>
								</p>
								<img
									v-if="salary_approved"
									src="/images/double-check.png"
									alt=""
								>
							</div>
						</template>
					</v-select>
				</div>
				<div class="col-2">
					<select
						class="form-control"
						v-model="dateInfo.currentMonth"
						@change="fetchData()"
					>
						<option
							v-for="month in $moment.months()"
							:key="month"
							:value="month"
						>
							{{ month }}
						</option>
					</select>
				</div>
				<div class="col-2">
					<select
						class="form-control"
						v-model="dateInfo.currentYear"
						@change="fetchData()"
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
				<div class="col-2  align-items-start">
					<a
						class="btn btn-primary mr-1 rounded text-white"
						@click="fetchData()"
					>
						<i class="fa fa-redo-alt" />
					</a>
				</div>
				<div class="col-2" />
			</div>

			<!-- filters -->
			<hr>
			<div
				v-if="hasPermission"
				class="row my-2"
			>
				<div class="col-6">
					<div>
						<p class="mb-0 fz-08 text-black">
							<b>Итого действующие ФОТ
								<i
									class="fa fa-info-circle"
									v-b-popover.hover.right.html="'<b>ФОТ</b>- Фонд оплаты труда<br>Сумма без вычета расходов (Штрафы и авансы)<br>ФОТ = Начисления (Отработанные + Стажировочные) + Бонусы + KPI'"
									title="ФОТ"
								/>
								:</b>
							{{ actualFOT }} тг.
						</p>
						<p class="mb-0 fz-08 text-black">
							<b>Итого уволенные ФОТ:</b>
							{{ group_fired }} тг.
						</p>
						<p
							v-if="showTotals"
							class="fz-08 text-black mr-1 mb-0"
						>
							<b>Итого все ФОТ (Действующие):</b>
							{{ allTotal }}тг.
						</p>
						<p
							v-if="showTotals"
							class="fz-08 text-black mr-1 mb-0"
						>
							<b>Итого все ФОТ (Уволенные):</b>
							{{ allTotalFired }}тг.
						</p>
					</div>
				</div>
				<div class="col-6 text-right">
					<a
						v-if="can_edit"
						@click="exportData()"
						class="btn btn-success rounded text-white mr-1"
					>
						<i class="far fa-file-excel" />
					</a>
					<a
						@click="toggleVisible()"
						class="btn btn-info rounded text-white mr-1"
					>
						<i class="fa fa-eye" />
					</a>
					<b-button
						v-if="selectedGroup.salary_approved == 0 && can_edit"
						style="float:right"
						@click="showBeforeApprove = true"
						class="rounded btn-sm mb-3 ml-3"
						variant="info"
					>
						Проверено и готово к выдаче
					</b-button>

					<div
						v-if="selectedGroup.salary_approved == 1"
						class="approved-text"
					>
						<p class="text-success">
							<img
								src="/images/double-check.png"
								style="width: 20px"
							> Начисления утверждены
						</p>
						<p>{{ selectedGroup.salary_approved_by }}</p>
						<small>{{ selectedGroup.salary_approved_date }}</small>
					</div>
				</div>
			</div>
			<hr>
			<div class="row mt-4">
				<div class="col-12 col-md-9">
					<div class="d-flex">
						<b-form-group class="mr-3">
							<b-form-select v-model="user_types">
								<b-form-select-option value="0">
									Действующие
								</b-form-select-option>
								<b-form-select-option value="1">
									Уволенные
								</b-form-select-option>
								<b-form-select-option value="2">
									Стажеры
								</b-form-select-option>
							</b-form-select>
						</b-form-group>
						<b-form-group>
							<b-form-select v-model="show_user">
								<b-form-select-option value="0">
									Все
								</b-form-select-option>
								<b-form-select-option value="1">
									Есть начисления
								</b-form-select-option>
							</b-form-select>
						</b-form-group>
					</div>
				</div>
				<div class="col-12 col-md-3">
					<p class="text-right fz-09 text-black">
						<span>Сотрудники:</span>
						<b> {{ users_count }} | {{ total_resources }}</b>
					</p>
				</div>
			</div>
			<!-- table -->
			<div
				v-if="hasPermission"
				class="table-container table-accrual"
			>
				<b-table
					responsive
					class="text-nowrap text-right salar accrual-table"
					:class="{'hide-special': special_fields}"
					:small="true"
					:bordered="true"
					:items="items"
					:fields="fields"
					show-empty
					empty-text="Нет данных"
				>
					<template #cell(name)="data">
						<div class="badge_table">
							{{ data.value }}
							<b-badge
								v-if="data.index !== 0 && data.value"
								pill
								variant="success"
								class="mr-2"
							>
								{{ data.item.user_type }}
							</b-badge>
							<i
								v-if="data.index == 0"
								class="fa fa-info-circle"
								v-b-popover.hover.right.html="'В суммах этого ряда не учитываются Сотрудники, у которых <b>К выдаче</b> меньше 0'"
								title="Заметка"
							/>
						</div>
					</template>

					<template #cell(bonus)="data">
						<div
							@click="defineClickNumber('bonus', data)"
							class="pointer"
						>
							{{ data.value }}
							<div
								v-if="data.item.edited_bonus !== null && data.index != 0"
								class="cell-border"
							/>
						</div>
					</template>

					<template #cell(kpi)="data">
						<!-- @click="fetchKPIStatistics(data.item.user_id)" -->
						<div
							@click="defineClickNumber('kpi', data)"
							class="pointer"
						>
							{{ data.value }}
							<div
								v-if="data.item.edited_kpi !== null && data.index != 0"
								class="cell-border"
							/>
						</div>
					</template>

					<template #cell(total)="data">
						<div>{{ data.value }}</div>
					</template>

					<template #cell(fines)="data">
						<div>{{ data.value }}</div>
					</template>

					<template #cell(avans)="data">
						<div>{{ data.value }}</div>
					</template>
					<template #cell(taxes)="data">
						<div>{{ data.value }}</div>
					</template>

					<template #cell(final)="data">
						<div
							v-if="user_types == '1'"
							@click="defineClickNumber('final', data)"
							class="pointer"
						>
							{{ data.value }}
							<div
								v-if="data.item.edited_salary !== null && data.index != 0"
								class="cell-border"
							/>
						</div>
						<div v-else>
							{{ data.value }}
						</div>
					</template>

					<template #cell()="data">
						<div
							@click="detectClick(data)"
							:class="{
								'fine': data.item.fine !== undefined && data.item.fine[data.field.key.toString()].length > 0,
								'avans': data.item.avanses !== undefined && data.item.avanses[data.field.key.toString()] !== null,
								'bonus': (data.item.bonuses !== undefined && data.item.bonuses[data.field.key.toString()] !== null) || data.item.awards !== undefined && data.item.awards[data.field.key.toString()] !== null,
								'training': data.item.trainings !== undefined && data.item.trainings[data.field.key.toString()] !== null,
							}"
						>
							{{ data.value }}
						</div>
					</template>
				</b-table>
			</div>
			<div v-else>
				<p>У вас нет доступа к этой группе</p>
			</div>
		</div>

		<!-- kpi -->
		<Sidebar
			v-if="kpiSidebar"
			width="80vw"
			title="KPI Статистика"
			:open="kpiSidebar"
			@close="kpiSidebar = false"
		>
			<div class="px-2 pt-5">
				<KpiContent
					class="px-4 TableAccrual-kpi"
					:items="kpiItems"
					:groups="groups"
					:fields="kpiFields"
				/>
			</div>
		</Sidebar>

		<!-- Premium -->
		<Sidebar
			v-if="editPremiumSidebar"
			:title="sidebarTitle"
			width="400px"
			:open="editPremiumSidebar"
			@close="editPremiumSidebar=false"
		>
			<div class="px-2">
				<div>
					<div v-if="editedField.item.edited_kpi !== null">
						<p class="mt-3">
							<b>Kpi  </b>
							<i
								class="fa fa-info-circle"
								v-b-popover.hover.right.html="'Сумма KPI утвержденная к выдаче'"
								title="Kpi на этот месяц"
							/>
						</p>
						<div>
							<b>Автор:</b>
							<span>{{ editedField.item.edited_kpi.user }}</span>
						</div>
						<div>
							<b>Изменено на:</b>
							<span>{{ editedField.item.edited_kpi.amount }}</span>
						</div>
						<div>
							<b>Комментарии:</b>
							<span>{{ editedField.item.edited_kpi.comment }}</span>
						</div>
						<hr>
					</div>
				</div>

				<div>
					<div v-if="editedField.item.edited_bonus !== null">
						<p class="mt-3">
							<b>Бонусы</b>
							<i
								class="fa fa-info-circle"
								v-b-popover.hover.right.html="'Сумма Бонусов, утвержденная к выдаче'"
								title="Бонусы на этот месяц"
							/>
						</p>
						<div>
							<b>Автор:</b>
							<span>{{ editedField.item.edited_bonus.user }}</span>
						</div>
						<div>
							<b>Изменено на:</b>
							<span>{{ editedField.item.edited_bonus.amount }}</span>
						</div>
						<div>
							<b>Комментарии:</b>
							<span>{{ editedField.item.edited_bonus.comment }}</span>
						</div>
						<hr>
					</div>
				</div>

				<div>
					<div v-if="editedField.item.edited_salary !== null">
						<p class="mt-3">
							<b>К выдаче</b>
							<i
								class="fa fa-info-circle"
								v-b-popover.hover.right.html="'Окончательная суммма утвержденная к выдаче'"
								title="К выдаче на этот месяц"
							/>
						</p>
						<div>
							<b>Автор:</b>
							<span>{{ editedField.item.edited_salary.user }}</span>
						</div>
						<div>
							<b>Изменено на:</b>
							<span>{{ editedField.item.edited_salary.amount }}</span>
						</div>
						<div>
							<b>Комментарии:</b>
							<span>{{ editedField.item.edited_salary.comment }}</span>
						</div>
						<hr>
					</div>
				</div>



				<div class="mt-3">
					<p class="mt-3">
						<b>Бонусы локальные</b>
					</p>
					<div
						v-for="(item,index) in Object.keys(editedField.item.bonuses)"
						:key="index"
					>
						<p
							v-if="editedField.item.bonuses[item] != null"
							class="fz12"
						>
							<b class="text-black">{{ item }}:</b>
							{{ editedField.item.bonuses[item] }}
						</p>
					</div>
					<hr>

					<p class="mt-3">
						<b>Бонусы за активности</b>
					</p>
					<div
						v-for="(item,index) in Object.keys(editedField.item.awards)"
						:key="index"
					>
						<p
							v-if=" editedField.item.awards[item] != null"
							class="fz12"
						>
							<b class="text-black">{{ item }}:</b> {{ editedField.item.awards[item] }}
						</p>
					</div>

					<p class="mt-3">
						<b>Бонусы за обучение</b>
					</p>
					<div
						v-for="(item,index) in Object.keys(editedField.item.test_bonus)"
						:key="index"
					>
						<p
							v-if=" editedField.item.test_bonus[item] != null"
							class="fz12"
						>
							<b class="text-black">{{ item }}:</b> {{ editedField.item.test_bonus[item] }}
						</p>
					</div>
					<hr>

					<p class="mt-3">
						<b>Авансы </b>
					</p>
					<div
						v-for="(item,index) in Object.keys(editedField.item.avanses)"
						:key="index"
					>
						<p
							v-if=" editedField.item.avanses[item] != null"
							class="fz12"
						>
							<b class="text-black">{{ item }}:</b> {{ editedField.item.avanses[item] }}
						</p>
					</div>
					<hr>

					<p class="mt-3">
						<b>История {{ bonus_history.length }}</b>
					</p>
					<div
						v-for="(item,index) in bonus_history"
						class="mb-3"
						:key="index"
					>
						<p class="fz12">
							<b class="text-black">Дата:</b> {{ (new Date(item.created_at)).addHours(6).toLocaleString('ru-RU') }}
						</p>
						<p class="fz12">
							<b class="text-black">Автор:</b> {{ item.author }} <br>
						</p>
						<p
							class="fz14 mb-0"
							v-html="item.description"
						/>
						<br>
						<hr>
					</div>
				</div>
			</div>
		</Sidebar>

		<!-- info -->
		<Sidebar
			v-if="openSidebar"
			width="400px"
			:title="sidebarTitle"
			:open="openSidebar"
			:link="profile_link"
			@close="openSidebar=false"
		>
			<div class="px-2">
				<div
					class="mb-2"
					v-if="sidebarContent.item !== undefined"
				>
					<p
						class="text-black"
						v-if="sidebarContent.item.hours !== undefined"
					>
						<b>Отработано:</b>  {{ sidebarContent.item.hours[sidebarContent.field.key] }}
					</p>
					<p
						class="text-black"
						v-if="sidebarContent.item.hourly_pays !== undefined"
					>
						<b>Оплата за час:</b>  {{ sidebarContent.item.hourly_pays[sidebarContent.field.key] }}
					</p>
					<p class="text-black mb-0">
						<b>Начис:</b>  {{ sidebarContent.item.salaries[sidebarContent.field.key] }}
					</p>

					<template v-if="selectedCell.field.editable">
						<p
							class="text-black mb-0"
							v-if="sidebarContent.item.avanses !== undefined"
						>
							<b>Аванс:</b>
							{{ sidebarContent.item.avanses[sidebarContent.field.key] }}
						</p>
						<p
							class="text-black"
							v-if="sidebarContent.item.bonuses !== undefined"
						>
							<b>Бонус:</b>
							{{ sidebarContent.item.bonuses[sidebarContent.field.key] }}
						</p>
						<p
							class="text-black"
							v-if="sidebarContent.item.awards !== undefined"
						>
							<b>Бонус (авто):</b>
							{{ sidebarContent.item.awards[sidebarContent.field.key] }}
						</p>
						<p
							class="text-black"
							v-if="sidebarContent.item.test_bonus !== undefined"
						>
							<b>Бонус (тесты):</b>
							{{ sidebarContent.item.test_bonus[sidebarContent.field.key] }}
						</p>
					</template>
				</div>
				<div
					class="mb-2"
					v-if="(user_types == '0' || user_types == '1') && can_edit"
				>
					<div class="d-flex row">
						<div class="col-6">
							<b-button
								@click="toggleTab('avans')"
								class="btn-sm rounded btn-primary w-full d-block"
								:class="{'activex': avans.visible}"
							>
								Выдать аванс
							</b-button>
						</div>
						<div class="col-6">
							<b-button
								@click="toggleTab('bonus')"
								class="btn-sm rounded btn-primary w-full d-block"
								:class="{'activex': bonus.visible}"
							>
								Выдать бонус
							</b-button>
						</div>
					</div>
				</div>

				<div
					class="mb-4 bg-bluish p-3"
					v-if="avans.visible"
				>
					<label>Сумма аванса</label>
					<input
						v-model="avans.sum"
						placeholder="Cумма аванса"
						:required="true"
						class="form-control form-control-sm mr-2 mb-2"
						type="number"
					>
					<label>Комментарии <span class="color-red">*</span></label>
					<input
						v-model="avans.comment"
						placeholder="Причина выдачи..."
						:required="true"
						class="form-control form-control-sm mr-2 mb-2"
						type="text"
					>
					<p><span class="color-red">{{ avans.require }}</span></p>
					<b-button
						@click="updateSalary('avans')"
						class="btn-sm rounded btn-primary"
						variant="primary"
					>
						Выдать аванс
					</b-button>
				</div>

				<div
					class="mb-4 bg-bluish p-3"
					v-if="bonus.visible"
				>
					<label>Сумма бонуса</label>
					<input
						v-model="bonus.sum"
						placeholder="Cумма бонуса"
						:required="true"
						class="form-control form-control-sm mr-2 mb-2"
						type="number"
					>
					<label>Комментарии <span class="color-red">*</span></label>
					<input
						v-model="bonus.comment"
						placeholder="Причина выдачи..."
						:required="true"
						class="form-control form-control-sm mr-2 mb-2"
						type="text"
					>
					<p><span class="color-red">{{ avans.require }}</span></p>
					<b-button
						@click="updateSalary('bonus')"
						class="btn-sm rounded btn-primary"
						variant="primary"
					>
						Выдать бонус
					</b-button>
				</div>

				<div
					class="mb-4"
					v-if="selectedCell.item.fine[sidebarContent.field.key].length > 0"
				>
					<p><b>Штрафы</b></p>
					<p class="mb-0 mt-0">
						{{ selectedCell.item.fine[sidebarContent.field.key] }}
					</p>
				</div>
				<div>
					<p class="text-black">
						<b>История</b>
					</p>
					<template v-if="sidebarHistory && sidebarHistory.length > 0">
						<div class="history">
							<div
								v-for="(item,index) in sidebarHistory"
								:key="index"
								class="mb-3"
							>
								<p class="fz12">
									<b class="text-black">Дата:</b> {{ (new Date(item.created_at)).addHours(6).toLocaleString('ru-RU') }}
								</p>
								<p class="fz12">
									<b class="text-black">Автор:</b> {{ item.author }} <br>
								</p>
								<p
									class="fz14 mb-0"
									v-html="item.description"
								/><br>
								<hr>
							</div>
						</div>
					</template>
					<template v-else>
						<p>История изменения отсутствует</p>
					</template>
				</div>
			</div>
		</Sidebar>

		<!-- premium -->
		<b-modal
			v-model="editPremiunWindow"
			ok-text="Да"
			cancel-text="Нет"
			:title="editedField.name + ': ' + editedField.type"
			@ok="editPremium"
			size="md"
		>
			<b-form-input
				type="number"
				v-model="amountEdit"
				placeholder="Сумма"
				:required="true"
				class="mb-2"
			/>

			<b-form-input
				type="text"
				v-model="commentEdit"
				placeholder="Комментарий"
				:required="true"
				class="mb-2"
			/>

			<b-alert
				v-for="error in errors"
				:key="error"
				show
				variant="danger"
			>
				{{ error }}
			</b-alert>
		</b-modal>

		<!-- approve salary -->
		<b-modal
			v-model="showBeforeApprove"
			ok-text="Да"
			cancel-text="Нет"
			title="Утверждение зарплаты"
			@ok="approveSalary"
			size="md"
		>
			<p>Вы подтверждаете, что вы проверили начисления и уверены, в том что они верные?</p>
		</b-modal>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
import { useYearOptions } from '../composables/yearOptions'
// import KpiItemsV2 from '@/pages/kpi/KpiItemsV2'
import { kpi_fields, parseKPI } from '@/pages/kpi/kpis.js'
import KpiContent from '@/pages/Profile/Popups/KpiContent.vue'

export default {
	name: 'TableAccrual',
	components: {
		Sidebar,
		// KpiItemsV2,
		KpiContent,
	},
	props: {
		groupss: Array,
		activeuserid: String,
		activeuserpos: Number,
		can_edit: Boolean,
		is_admin: Boolean,
	},
	data() {
		const now = new Date()
		return {
			data: {},
			groups: [],
			accruals: [],
			bonus_history: [],
			selectedGroup: null,
			user_types: 0,
			users_count: 0,
			openSidebar: false,
			show_user: 1,
			sidebarTitle: '',
			sidebarContent: {},
			sidebarHistory: [],
			numClicks: 0,
			total_resources: 0,
			allTotalFired: 0, // sum of pay for all fired users
			group_fired: 0, // sum of payment for fired users in group
			group_total: 0, // sum of payment for  users in group
			items: [],
			fields: [],
			errors: [],
			auth_token: '',
			profile_link: '',
			selectedCell: null,
			special_fields: false,
			editPremiumSidebar: false,
			avans: {
				sum: null,
				comment: '',
				require: '',
				visible: false
			},
			bonus: {
				sum: null,
				comment: '',
				require: '',
				visible: false
			},
			dayInfoText: '',
			hasPermission: false,
			total: 0,
			allTotal: 0,
			dateInfo: {
				currentMonth: null,
				currentYear: now.getFullYear(),
				month: 0,
				year: 0,
				monthEnd: 0,
				workDays: 0,
				weekDays:0,
				daysInMonth: 0,
			},
			editedField: {
				name: '',
				type: 'kpi'
			},
			commentEdit: '',
			amountEdit: 0,
			editPremiunWindow: false,
			showBeforeApprove: false,
			dataLoaded: false,
			currentGroup: null,
			maxScrollWidth: 0,
			scrollLeft: 0,
			defaultScrollValue: 0,
			dayPercentage: (now.getDate() / 31) * 100,
			delay: 700,
			clicks: 0,
			timer: null,

			kpiSidebarUserId: 0,
			kpiSidebar: false,
			kpiFields: kpi_fields,
			kpiItems: [],
			// stats:
		};
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		showAccruals(){
			return this.activeuserid && [5,18,84,157].includes(Number(this.activeuserid))
		},
		showTotals(){
			return this.activeuserid && [5,18,84,157].includes(Number(this.activeuserid))
		},
		actualFOT(){
			if (!this.items || !this.items[0]) return 0
			return (parseInt(this.items[0].kpi) || 0) + (parseInt(this.items[0].bonus) || 0) + (parseInt(this.items[0].total) || 0)
		},
		date(){
			return this.$moment(`${this.dateInfo.currentYear}-${this.dateInfo.currentMonth}-1`, 'YYYY-MMMM-D').format('YYYY-MM-DD')
		}
	},
	watch: {
		scrollLeft(value) {
			var container = document.querySelector('.table-responsive');
			container.scrollLeft = value;
		},
		user_types() {
			this.fetchData()
		},
		show_user() {
			this.fetchData()
		},
		selectedGroup() {
			this.fetchData()
		},
		groupss(){
			this.init()
		}
	},
	created() {
		if(this.groupss){
			this.init()
		}
	},
	methods: {
		init(){
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ?
				this.dateInfo.currentMonth :
				this.$moment().format('MMMM');
			const currentMonth = this.$moment(`${this.dateInfo.currentYear}-${this.dateInfo.currentMonth}`, 'YYYY-MMMM')

			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]); //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth(); //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays; //Колличество рабочих дней

			this.groups = this.groupss;
			this.selectedGroup = this.groups[0];
		},
		//Установка выбранного года
		setYear() {
			this.dateInfo.currentYear = this.dateInfo.currentYear ?
				this.dateInfo.currentYear :
				this.$moment().format('YYYY');
		},

		//Установка выбранного месяца
		setMonth() {
			let year = this.dateInfo.currentYear;
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ?
				this.dateInfo.currentMonth :
				this.$moment().format('MMMM');
			this.dateInfo.month = this.dateInfo.month ?
				this.dateInfo.month :
				this.$moment().format('M');
			this.dateInfo.year = year;

			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`;

			const currentMonth = this.$moment(`${this.dateInfo.currentYear}-${this.dateInfo.currentMonth}`, 'YYYY-MMMM')
			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(
				this.dateInfo.monthEnd,
				[6]
			); //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth(); //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays; //Колличество рабочих дней
		},

		//Установка заголовока таблицы
		setFields() {
			let fields = [];
			fields = [
				{
					key: 'name',
					stickyColumn: true,
					label: 'Имя',
					variant: 'primary',
					sortable: true,
					class: 'text-left px-3 t-name',
					editable: false
				},
				{
					key: 'kpi',
					label: 'KPI',
					sortable: true,
					editable: false,
					stickyColumn: true
				},
				{
					key: 'bonus',
					label: 'Бонусы',
					sortable: true,
					editable: false,
					stickyColumn: true
				},
				{
					key: 'total',
					label: 'Оклад',
					sortable: true,
					editable: false,
					stickyColumn: true
				},
				{
					key: 'fines',
					label: 'Штрафы',
					sortable: true,
					editable: false,
					stickyColumn: true
				},
				{
					key: 'avans',
					label: 'Авансы',
					sortable: true,
					editable: false,
					stickyColumn: true
				},
				{
					key: 'taxes',
					label: 'Налоги',
					sortable: true,
					editable: false,
					stickyColumn: true
				},
				{
					key: 'final',
					label: 'К выдаче',
					sortable: true,
					editable: false,
					stickyColumn: true
				}];

			let days = this.dateInfo.daysInMonth;

			for (let i = 1; i <= days; i++) {
				let dayName = this.$moment(`${i} ${this.dateInfo.date}`, 'D MMMM YYYY')
					.locale('en')
					.format('ddd');

				let field = {
					key: `${i}`,
					label: `${i}`,
					sortable: true,
					class: `day ${dayName}`,
					editable: true
				};

				fields.push(field);
			}
			this.fields = fields;
		},

		//Загрузка данных для таблицы
		fetchData() {
			if(this.selectedGroup == null) return '';

			let loader = this.$loading.show();
			this.dateInfo.month = this.$moment(
				this.dateInfo.currentMonth,
				'MMMM'
			).format('M');

			this.axios.post('/timetracking/salaries', {
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				year: this.dateInfo.currentYear,
				group_id: this.selectedGroup.id,
				user_types: this.user_types,
			})
				.then((response) => {
					let data = response.data;
					if (data.error && data.error == 'access') {
						console.error(data.error);
						this.hasPermission = false;
						loader.hide();
						return;
					}

					this.hasPermission = true;

					this.data = data;
					this.group_fired = data.group_fired;
					this.allTotal = data.all_total;
					this.allTotalFired = data.all_total_fired;
					this.group_total = data.group_total;
					this.total_resources = data.total_resources;
					this.users_count = data.users.length;
					this.groups = data.groups;
					this.accruals = data.accruals;
					this.auth_token = data.auth_token

					if(data.currentGroup) {
						this.selectedGroup.salary_approved = data.currentGroup.salary_approved;
						this.selectedGroup.salary_approved_by = data.currentGroup.salary_approved_by;
						this.selectedGroup.salary_approved_date = data.currentGroup.salary_approved_date;
					}

					this.setYear();
					this.setMonth();
					this.setFields();
					this.loadItems();
					this.dataLoaded = true;

					// maybe scroll table to right
					setTimeout(() => {
						var container = document.querySelector('.table-responsive');
						this.maxScrollWidth = container.scrollWidth - container.offsetWidth;
						if (this.dayPercentage > 50) {
							// this.scrollLeft =
							//     (this.maxScrollWidth * this.dayPercentage) / 100;
							// this.defaultScrollValue = this.scrollLeft;
						}

					}, 1000);

					loader.hide();
				}).catch(error => {
					this.$toast.error('Ошибка');
					console.error(error)
				});
		},

		// get salaries total for all group
		getTotals() {
			const  loader = this.$loading.show();
			const currentMonth = this.$moment(`${this.dateInfo.currentYear}-${this.dateInfo.currentMonth}`, 'YYYY-MMMM')
			this.axios.post('/timetracking/salaries/get-total', {
				month: currentMonth.format('M'),
				year: this.dateInfo.currentYear,
			})
				.then(() => {
					loader.hide();
				}).catch((e) => {
					console.error(e);
					loader.hide();
				});
		},

		toggleTab(type) {
			if(type == 'avans') {
				this.bonus.visible = false
				this.avans.visible = !this.avans.visible
			} else {
				this.avans.visible = false
				this.bonus.visible = !this.bonus.visible
			}
		},

		//Добавление загруженных данных в таблицу
		loadItems() {
			let hasMoney = 0;
			let items = [];
			let daySalariesSum = [];

			items.push({
				name: 'Общая сумма',
			});

			let total_final = 0; // К выдаче сумма
			let total_kpi = 0; // Кpi total
			let total_bonus = 0; // Bonus total
			let total_fines = 0; // fines total
			let total_avanses = 0; // Avans total
			let total_taxes = 0; // Taxes total
			let total_total = 0; // Начислено total

			this.data.users.forEach(item => {
				var daySalaries = [];
				var daySalariesOnly = [];
				var personalTotal = 0;
				var personalFinal = 0;
				var personalAvanses = 0;
				var personalFines = 0;
				var personalBonuses = 0;
				var personalTaxes = 0;


				item.salaries.forEach(tt => {

					let salary = 0;
					let total = 0;

					if(item.earnings[tt.day] !== null) {
						salary = Number(item.earnings[tt.day]);
						total = salary;
					}

					// salary earned to total
					if(Number(salary) != 0) personalTotal += parseInt(salary);

					if(tt.paid !== null) {
						personalAvanses += parseInt(tt.paid, 0);
					}

					if(item.bonuses[tt.day] !== null) {
						personalBonuses += Number(item.bonuses[tt.day]);
						total += Number(item.bonuses[tt.day]);
					}

					if(item.awards[tt.day] !== null) {
						personalBonuses += Number(item.awards[tt.day]);
						total += Number(item.awards[tt.day]);
					}

					if(item.test_bonus[tt.day] !== null) {
						personalBonuses += Number(item.test_bonus[tt.day]);
						total += Number(item.test_bonus[tt.day]);
					}

					if(item.fine[tt.day] !== null) {
						let fine_for_day = 0;
						item.fine[tt.day].forEach(el => {
							Object.values(el).forEach(fine_sum => fine_for_day += Number(fine_sum));
						})

						total -= Number(fine_for_day);
					}

					daySalaries[tt.day] = Number(total) != 0
						? Number(total).toFixed(0)
						: '';

					daySalariesOnly[tt.day] = Number(salary) != 0
						? Number(salary).toFixed(0)
						: '';
				});

				item.taxes.forEach(t => {
					personalTaxes = personalTaxes + t.amount;
				});

				let personalKpi =  Number(item.kpi);
				if(item.edited_kpi) {
					personalKpi = item.edited_kpi.amount
				}

				if(item.edited_bonus) {
					personalBonuses = item.edited_bonus.amount
				}

				personalFines = Number(item.fines_total);

				personalFinal = personalTotal - personalAvanses + personalBonuses - personalFines + personalKpi - personalTaxes;

				if(item.edited_salary) {
					personalFinal = item.edited_salary.amount
				}

				daySalaries['bonus'] = Number(personalBonuses).toFixed(0);
				daySalaries['avans'] = Number(personalAvanses).toFixed(0);
				daySalaries['fines'] = Number(personalFines).toFixed(0);
				daySalaries['total'] = Number(personalTotal).toFixed(0);
				daySalaries['taxes'] = Number(personalTaxes).toFixed(0);
				daySalaries['final'] = Number(personalFinal).toFixed(0);

				total_final += Number(personalFinal) >= 0 ? Number(personalFinal) : 0;
				total_total += Number(personalFinal) >= 0 ? Number(personalTotal) : 0;
				total_kpi += Number(personalFinal) >= 0 ? Number(item.kpi) : 0;
				total_fines += Number(personalFinal) >= 0 ? Number(personalFines) : 0;
				total_bonus += Number(personalFinal) >= 0 ? Number(personalBonuses) : 0;
				total_taxes += Number(personalFinal) >= 0 ? Number(personalTaxes) : 0;
				total_avanses += Number(personalFinal) >= 0 ? Number(personalAvanses) : 0;

				daySalaries.forEach((amount, day) => {
					if(isNaN(amount) || isNaN(Number(amount))) {
						amount = 0;
					}

					if (typeof daySalariesSum[day] === 'undefined') {
						daySalariesSum[day] = 0;
					}

					daySalariesSum[day] = parseInt(daySalariesSum[day]) + Number(amount);

					if(daySalariesSum[day] > 0){
						hasMoney = 1;
					}
				});

				let obj = {
					kpi: item.kpi,
					fine: item.fine,
					user_id: item.id,
					hours: item.hours,
					awards: item.awards,
					name: `${item.name} ${item.last_name}`,
					avanses: item.avanses,
					bonuses: item.bonuses,
					user_type: item.user_type,
					trainings: item.trainings,
					history: item.track_history,
					edited_kpi: item.edited_kpi,
					test_bonus: item.test_bonus,
					hourly_pays: item.hourly_pays,
					edited_bonus: item.edited_bonus,
					edited_salary: item.edited_salary,
					salaries: daySalariesOnly,
					...daySalaries,
				};

				if(this.show_user == 0) {
					items.push(obj);
				} else if(hasMoney > 0) { // show if has salary records
					items.push(obj);
					hasMoney = 0
				}
			});


			let total = 0;

			if(this.user_types != '2') { // стажеры
				daySalariesSum.forEach(sum => {
					total = total + parseInt(sum);
				});
			}

			total_bonus = Number(total_bonus).toFixed(0);

			items[0] = {
				name: 'Общая сумма',
				final: total_final,
				kpi: total_kpi,
				bonus: total_bonus,
				avans: total_avanses,
				fines: total_fines,
				taxes: total_taxes,
				total: total_total,
				...daySalariesSum,
			};

			this.total = total_final;
			this.items = items;
		},

		// окно редактирования kpi бонус  к выдаче на месяц
		showEditPremiumWindow(type, data) {
			if(data.index == 0) return false;
			data.type = type;
			this.editedField = data;

			this.editedField.name = data.item.name

			this.editPremiunWindow = true;
		},

		// сайдбар kpi бонус  к выдаче на месяц
		showEditPremiumSidebar(type, data) {
			if(data.index == 0) {
				return false;
			}
			data.type = type;

			this.fetchBonusHistory(data.item.user_id);

			this.editedField = data;
			this.editPremiumSidebar = true;
			this.sidebarTitle = data.item.name + ' : ' + type;

		},

		// история бонусов для showEditPremiumSidebar
		fetchBonusHistory(user_id) {
			const currentMonth = this.$moment(`${this.dateInfo.currentYear}-${this.dateInfo.currentMonth}`, 'YYYY-MMMM')
			this.axios.post('/timetracking/salaries/bonuses',{
				user_id: user_id,
				date: currentMonth.startOf('month').format('YYYY-MM-DD'),
			}).then((response) => {
				this.bonus_history = response.data;
			});
		},

		editPremium() {
			const currentMonth = this.$moment(`${this.dateInfo.currentYear}-${this.dateInfo.currentMonth}`, 'YYYY-MMMM')

			if(this.commentEdit < 3) {
				this.errors = ['Комментарии обязательны!'];
				return '';
			}

			this.axios.post('/timetracking/salaries/edit-premium', {
				date: currentMonth.startOf('month').format('YYYY-MM-DD'),
				user_id: this.editedField.item.user_id,
				amount: this.amountEdit,
				comment: this.commentEdit,
				type: this.editedField.type
			})
				.then(() => {

					if(this.editedField.type == 'kpi') {
						this.items[this.editedField.index].kpi = this.amountEdit
					}

					if(this.editedField.type == 'bonus') {
						this.items[this.editedField.index].bonus = this.amountEdit
					}

					this.commentEdit = '';
					this.amountEdit = 0;
					this.$toast.success('Сохранено');
					this.editedField = {name:'', type:'kpi'}
					this.editPremiunWindow = false

				}).catch(error => {
					this.$toast.error('Не сохранилось');
					console.error(error)
				});
		},

		// утверждено к выдаче
		approveSalary() {
			this.axios.post('/timetracking/salaries/approve-salary', {
				group_id: this.selectedGroup.id,
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				year: this.dateInfo.currentYear,
			})
				.then(() => {
					this.$toast.success('Сохранено');
					this.showBeforeApprove = false
					this.selectedGroup.salary_approved = 1;
				}).catch(error => {
					this.$toast.error('Не получилось');
					console.error(error)
				});
		},

		updateSalary(type) {
			if(this.selectedCell.index == 0) return '';

			let comment,
				amount;

			if(type == 'avans') {
				if(this.avans.comment.length < 3) {
					this.avans.require = 'Комментарии обязательны!'
					return '';
				}

				if(this.avans.sum == 0 || this.avans.sum == null) {
					this.avans.require = 'Напишите сумму аванса!'
					return '';
				}

				comment = this.avans.comment;
				amount = this.avans.sum;
			}

			if(type == 'bonus') {
				if(this.bonus.comment.length < 3) {
					this.bonus.require = 'Комментарии обязательны!'
					return '';
				}

				if(this.bonus.sum == 0 || this.bonus.sum == null) {
					this.bonus.require = 'Напишите сумму бонуса!'
					return '';
				}

				comment = this.bonus.comment;
				amount = this.bonus.sum;
			}

			this.axios.post('/timetracking/salaries/update', {
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				year: this.dateInfo.currentYear,
				day: this.selectedCell.field.key,
				user_id: this.selectedCell.item.user_id,
				amount: amount,
				comment: comment,
				type: type
			})
				.then((response) => {

					if(type == 'avans') {
						this.$toast.success('Аванс успешно сохранен');
						this.selectedCell.item.avanses[this.selectedCell.field.key] = this.avans.sum;

						this.avans.sum = 0;
						this.avans.comment = '';
						this.avans.require = '';
						this.avans.visible = false;
					}

					if(type == 'bonus')  {
						this.$toast.success('Бонус успешно сохранен');
						this.selectedCell.item.bonuses[this.selectedCell.field.key] = this.bonus.sum;

						this.bonus.sum = 0;
						this.bonus.comment = '';
						this.bonus.require = '';
						this.bonus.visible = false;
					}

					this.sidebarHistory.unshift(response.data);
					this.items[this.selectedCell.index].history.unshift(response.data)

				}).catch(error => {
					this.$toast.error('Не сохранилось');
					console.error(error)
				});
		},

		// excel
		exportData() {
			var link = '/timetracking/salaries/export';
			link += '?group_id=' + this.selectedGroup.id;
			link += '&month=' + this.dateInfo.month;
			link += '&year=' + this.dateInfo.year;
			link += '&user_types=' + this.user_types;
			window.location.href = link;
		},

		detectClick(data) {
			this.selectedCell = data
			this.openDay(data)
		},

		toggleVisible() {
			this.special_fields = !this.special_fields;
		},

		defineClickNumber(type, data) {

			//var self = this
			this.clicks++;
			if (this.clicks === 1) {
				this.timer = setTimeout(() => {
					if(type === 'kpi'){
						this.fetchKPIStatistics(data.item.user_id)
					}
					else{
						this.showEditPremiumSidebar(type, data)
					}
					this.clicks = 0
				}, 350);
			}
			else {
				clearTimeout(this.timer);
				if(this.can_edit) {
					this.showEditPremiumWindow(type, data);
				}
				else {
					if(type === 'kpi'){
						this.fetchKPIStatistics(data.item.user_id)
					}
					else{
						this.showEditPremiumSidebar(type, data)
					}
				}
				this.clicks = 0;
			}
		},

		openDay(data) {
			this.openSidebar = true
			this.sidebarContent = data

			if(this.hasPermission) {
				this.profile_link = '<a href="https://test.jobtron.org/login-as-employee/' + data.item.user_id + '?auth=' + this.auth_token + '" target="_blank">';
				this.profile_link += '<i class="fa fa-link pointer ml-2 mr-2"></i></a>';
			} else {
				this.profile_link = '';
			}

			this.sidebarTitle = `${data.item.name} - ${data.field.key} ${this.dateInfo.currentMonth} `
			this.sidebarHistory = data.item.history.filter(x => parseInt(x.day) === parseInt(data.field.key))
		},

		// Дичайший костыль, переделать при первой возможности
		getUserGroups(userId){
			return this.groups.reduce((result, group) => {
				if(!group.users) return result
				const users = JSON.parse(group.users)
				if(~users.indexOf(userId) && !~result.indexOf(group.id)) result.push(group.id)
				return result
			}, [this.selectedGroup.id])
		},

		async fetchKPIStatistics(userId){
			if(!userId) return
			if(!this.is_admin) return

			this.kpiSidebarUserId = userId
			this.kpiItems = []

			const loader = this.$loading.show();
			try{
				const {data: userData} = await this.axios.post(`/statistics/kpi/groups-and-users/${userId}`, {
					filters: {
						data_from: {
							year: this.dateInfo.currentYear,
							month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M')
						}
					},
					type: 1
				})
				if(!userData.message){
					this.kpiItems.push(parseKPI(userData.kpi))
				}
				const groups = this.getUserGroups(userId)
				await Promise.all(groups.map(async groupId => {
					const {data: groupData} = await this.axios.post(`/statistics/kpi/groups-and-users/${groupId}`, {
						filters: {
							data_from: {
								year: this.dateInfo.currentYear,
								month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M')
							}
						},
						type: 2
					})
					if(!groupData.message){
						groupData.kpi.users = groupData.kpi.users.filter(user => user.id === userId)
						this.kpiItems.push(parseKPI(groupData.kpi))
					}
				}))
				this.kpiSidebar = true
			}
			catch(error){
				console.error(error)
				this.$toast.error('Ну удалось получить статистику')
			}
			loader.hide()
		},
	},
};
</script>

<style lang="scss">
$fine: red;
$avans: #28a761;
$bonus: #007bff;
$training: orange;

.fz-09 {
	font-size: 0.9rem;
}

.fine,.avans,.bonus {
	color:#fff;
}

.TableAccrual{
	&-kpi{
		font-size: 10px;
	}
}
.table-accrual{
	.fine {
		background: #f58c94;
	}
	.b-table-sticky-header{
		height: 100% !important;
	}
}

.bonus {
	background: $bonus;
	&.fine {background: linear-gradient(110deg, $bonus 50%, $fine 50%);}
	&.training {background: linear-gradient(110deg, $bonus 50%, $training 50%);}
	&.fine.training {background: linear-gradient(110deg, $bonus 33%, transparent 33%), linear-gradient(110deg, $fine 66%, $training 66%);}
}

.avans {
	background:$avans;
	&.fine {background: linear-gradient(110deg, $avans 50%, $fine 50%);}
	&.bonus {background: linear-gradient(110deg, $avans 50%, $bonus 50%);}
	&.training {background: linear-gradient(110deg, $avans 50%, $training 50%);}
	&.bonus.fine {background: linear-gradient(110deg, $avans 33%, transparent 33%), linear-gradient(110deg, $bonus 66%, $fine 66%);}
	&.bonus.fine.training {background: linear-gradient(110deg, $avans 25%, transparent 25%), linear-gradient(110deg, $bonus 50%, $fine 50%), linear-gradient(110deg, $fine 75%, $training 75%);}
}

.training {
	background: $training;
	color: #fff;
	&.fine {background: linear-gradient(110deg, $training 50%, $fine 50%);}
}

.ddf div {
	display: flex;
}
.ddf .custom-control {
	margin-right: 15px;
}

.ddf-br{
	padding-left: 20px;
	border-left: 1px solid #ddd;
}

.form-control.normal:disabled, .form-control.normal {
	padding: 0 2px;
	font-size: 11px;
	opacity: 1;
	background: transparent;
	text-align: center;
	height: 100%;
	font-weight: 500;
	border: none;
	border-radius: 0;
	width: 100%;
	font-family: 'Open Sans';
}
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
	-webkit-appearance: none;
	margin: 0;
}
.fz12 {
	line-height: 1.4em;
	font-size: 12px;
	margin-bottom: 0;
}
.fz14 {
	font-size: 14px;
	line-height: 1.4em;
	padding: 10px 0;
}
.ssssssssss .ui-sidebar__body * {
	color: #333;
}
hr {
	margin: 2px !important;
}
.color-red {
	color:red;
}
.bg-bluish {
	background: #a1bdd6;
}
.btn.activex {
	background: red;
}
.progresso {
	height: 20px;
	margin-top: 10px;
	margin-bottom: 10px;
	padding-left: 10px;
	color: white !important;
	line-height: 20px;
	background: #007bff;
	transition: width linear 0.5s;
}
.my-table.salar .cell-border {
	position: absolute;
	right: -47px;
	bottom: -23px;
	z-index: 2;
}
.accrual-table {
	.b-table{
		table-layout: fixed;
	}
	.cell-border {
		border-left-color: red !important;
	}
	th,
	td {
		width: 72px;
		padding: 0 !important;
		& > div{
			padding: 0 15px;
			height: 40px;
			min-width: 50px;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		&:first-child{
			& > div{
				justify-content: space-between;
			}
		}
		&:nth-child(1) {
			width: 290px;
			left:0 !important;
			div {
				width: 288px;
				white-space: normal;
			}
		}
		&:nth-child(2) {
			left:290px !important;
		}
		&:nth-child(3) {
			left:362px!important;
		}
		&:nth-child(4) {
			left:434px!important;
		}
		&:nth-child(5) {
			left:506px!important;
		}
		&:nth-child(6) {
			left:578px!important;
		}
		&:nth-child(7) {
			left:650px!important;
		}
		&:nth-child(8) {
			width: 98px;
			left:722px!important;
		}
		&:nth-child(2),
		&:nth-child(3),
		&:nth-child(4),
		&:nth-child(5),
		&:nth-child(6),
		&:nth-child(7){
			div{
				width: 70px;
			}
		}
	}

	&.hide-special {
		th,
		td {
			&:nth-child(2),
			&:nth-child(3),
			&:nth-child(4),
			&:nth-child(5),
			&:nth-child(6),
			&:nth-child(7) {
				display: none;
			}
			&:nth-child(8) {
				left:290px !important;
			}
		}
	}

	td {
		&:nth-child(2),
		&:nth-child(3),
		&:nth-child(4),
		&:nth-child(5),
		&:nth-child(6),
		&:nth-child(7) {
			background: #DDE9FF !important;
			border-color: #b4bed2 !important;
			div {
				font-size: 13px;
				text-align: center;
			}
		}

		&:nth-child(8) {
			background: #28a745 !important;
			border-color: #208738 !important;
			color: #fff;
		}
	}
}
.group-select {
	.vs__dropdown-toggle {
		padding: 5px 3px 6px;
		margin: 0;
		border: 1px solid #d3d7db;
	}

	.ddf div {
		display: flex;
	}
	.ddf .custom-control {
		margin-right: 15px;
	}
	.form-control.normal:disabled, .form-control.normal {
		padding: 0 2px;
		font-size: 11px;
		opacity: 1;
		background: transparent;
		text-align: center;
		height: 100%;
		font-weight: 500;
		border: none;
		border-radius: 0;
		width: 100%;
		font-family: 'Open Sans';
	}
	input[type="number"]::-webkit-outer-spin-button,
	input[type="number"]::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	.fz12 {
		line-height: 1.4em;
		font-size: 12px;
		margin-bottom: 0;
	}
	.fz14 {
		font-size: 14px;
		line-height: 1.4em;
		padding: 10px 0;
	}
	.ssssssssss .ui-sidebar__body * {
		color: #333;
	}
	hr {
		margin: 2px !important;
	}
	.color-red {
		color:red;
	}
	.bg-bluish {
		background: #a1bdd6;
	}
	.btn.activex {
		background: red;
	}
	.progresso {
		height: 20px;
		margin-top: 10px;
		margin-bottom: 10px;
		padding-left: 10px;
		color: white !important;
		line-height: 20px;
		background: #007bff;
		transition: width linear 0.5s;
	}
	.my-table.salar .cell-border {
		position: absolute;
		right: -47px;
		bottom: -23px;
		z-index: 2;
	}
	.accrual-table {
		th,td{
			padding: 0 !important;
			& > div{
				padding: 0 15px;
				height: 40px;
				min-width: 50px;
				display: flex;
				align-items: center;
				justify-content: center;
			}
			&:first-child{
				& > div{
					justify-content: space-between;
				}
			}
		}
		.cell-border {
			border-left-color: red !important;
		}
		th,
		td {
			&:nth-child(1) {
				left:0 !important;
				div {
					width: 288px;
					white-space: normal;
				}
			}
			&:nth-child(2) {
				left:289px !important;
			}
			&:nth-child(3) {
				left:360px!important;
			}
			&:nth-child(4) {
				left:431px!important;
			}
			&:nth-child(5) {
				left:502px!important;
			}
			&:nth-child(6) {
				left:573px!important;
			}
			&:nth-child(7) {
				left:644px!important;
			}
			&:nth-child(2),
			&:nth-child(3),
			&:nth-child(4),
			&:nth-child(5),
			&:nth-child(6){
				div{
					width: 70px;
				}
			}
		}

		td {
			&:nth-child(2),
			&:nth-child(3),
			&:nth-child(4),
			&:nth-child(5),
			&:nth-child(6) {
				background: #DDE9FF !important;
				outline-color: #c1cee5 !important;
				div {
					font-size: 13px;
					text-align: center;
				}
			}

			&:nth-child(7) {
				background: #28a745 !important;
				outline-color: #228f3b !important;
				color: #fff;
			}

		}


	}
	.group-select {
		.vs__dropdown-toggle {
			padding: 5px 3px 6px;
			margin: 0;
			border: 1px solid #d3d7db;
		}
	}
	.selector {
		position: relative;
		p {
			font-size: 13px;
		}
		img {
			position: absolute;
			right: 0;
			top: 3px;
			z-index: 5;
			height: 16px;
		}
	}
	.approved-text  {
		text-align: right;
		span {
			font-size: 11px;
		}
	}
}
.fz-08 {
	font-size: 0.8rem;
}
</style>
