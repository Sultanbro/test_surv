<template>
	<div
		v-if="$can('kpi_view')"
		class="stats px-3 py-1"
	>
		<!-- top line -->
		<div class="d-flex my-4 jcsb aifs">
			<div class="d-flex aic mr-2">
				<!-- <div class="d-flex aic mr-2">
					<span>Показывать:</span>
					<input
						type="number"
						min="1"
						max="100"
						v-model="pageSize"
						class="form-control ml-2 input-sm"
					>
				</div> -->
				<SuperFilter
					ref="child"
					:groups="groups"
					@apply="fetchData"
				/>
				<span
					v-if="items"
					class="ml-2"
				>
					Найдено: {{ totalRows }}
				</span>
				<span
					v-else
					class="ml-2"
				>
					Найдено: 0
				</span>
			</div>
			<i
				v-if="isAdmin"
				class="fa fa-cog btn ml-a"
				@click="isSettingsOpen = true"
			/>
		</div>

		<!-- table -->
		<template v-if="s_type_main == 1">
			<b-tabs>
				<b-tab title="Месяц">
					<StatsTableV2
						:activities="activities"
						:groups="groups"
						:items="page_items"
						:editable="true"
						:search-text="searchText"
						:date="date"
						:filters="filters"
						class="mt-4"
					/>
					<b-col>
						<b-row>
							<b-col class="d-flex aic">
								<b-pagination
									v-model="currentPage"
									:total-rows="totalRows"
									:per-page="perPage"
									size="sm"
									class="mt-4"
								/>
							</b-col>
							<b-col
								class="d-flex aic"
								cols="3"
							>
								<b-form-select
									v-model="perPage"
									:options="[10, 20, 50, 100]"
								/>
							</b-col>
						</b-row>
					</b-col>
				</b-tab>
				<b-tab title="Годовая">
					<StatsTableYear
						:year="filters.data_from ? filters.data_from.year : new Date().getFullYear()"
						:groups="groups"
						class="mt-4"
					/>
				</b-tab>
			</b-tabs>
		</template>

		<StatsTableBonus
			v-if="s_type_main == 2"
			:key="bonus_groups"
			:groups="bonus_groups"
			:group_names="groups"
			:month="month"
		/>

		<StatsTableQuartal
			v-if="s_type_main == 3"
			:key="quartal_users"
			:users="quartal_users"
			:groups="quartal_groups"
			:search-text="searchText"
		/>

		<SideBar
			:open="isSettingsOpen"
			width="50vw"
			title="Настройки"
			@close="isSettingsOpen = false"
		>
			<h3>Подсветка ячеек</h3>
			<div
				v-for="item, index in kpiBacklight"
				:key="index"
				class="KPIBacklight-row"
			>
				от:
				<input
					v-model="item.startValue"
					type="number"
					:min="item.prevMax"
					:max="99"
					class="form-control input-surv KPIBacklight-input"
				>
				до:
				<input
					v-model="item.endValue"
					type="number"
					:min="item.prevMax + 1"
					:max="100"
					class="form-control input-surv KPIBacklight-input"
				>
				цвет:
				<input
					v-model="item.color"
					type="color"
					class="form-control input-surv KPIBacklight-input"
				>
				<i
					class="fa fa-trash btn btn-danger btn-icon"
					@click="deleteBacklightColor(index)"
				/>
			</div>
			<button
				class="btn btn-primary"
				@click="addBacklightColor(kpiBacklight[kpiBacklight.length-1])"
			>
				Добавить
			</button>
			<hr>
			<button
				class="btn btn-success"
				@click="updateBacklightColors().then(() => {isSettingsOpen = false})"
			>
				Сохранить
			</button>
		</SideBar>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import SideBar from '@/components/ui/Sidebar'
import SuperFilter from '@/pages/kpi/SuperFilter' // filter like bitrix
import StatsTableV2 from '@/pages/kpi/StatsTableV2'
import StatsTableBonus from '@/pages/kpi/StatsTableBonus'
import StatsTableQuartal from '@/pages/kpi/StatsTableQuartal'
import StatsTableYear from '@/pages/kpi/StatsTableYear'

// import {formatDate} from './kpis.js';

import { mapActions, mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'

const classToType = {
	'App\\User': 1,
	'App\\ProfileGroup': 2,
	'App\\Position': 3,
}

const typeToClass = [
	'',
	'App\\User',
	'App\\ProfileGroup',
	'App\\Position',
]

export default {
	name: 'KPIStatsV2',
	components: {
		SideBar,
		SuperFilter,
		StatsTableV2,
		StatsTableBonus,
		StatsTableQuartal,
		StatsTableYear,
	},
	props: {},
	data() {
		return {
			searchText: new URL(location.href).searchParams.get('target') ? new URL(location.href).searchParams.get('target') : '',
			s_type_main: 1,
			month: new Date().getMonth(),
			active: 1,
			paginationKey: 1,
			pageSize: 20,
			items: [],
			all_items: [],
			page_items: [],
			groups: {},
			date: null,
			activities: [],
			bonus_items: [],
			bonus_groups: [],
			quartal_users: [],
			quartal_groups: [],

			currentPage: 1,
			totalRows: 1,
			perPage: 10,
			filters: {
				data_from: {}
			},

			isSettingsOpen: false,
			timeout: null,
		}
	},
	computed: {
		...mapState(usePortalStore, ['kpiBacklight']),
		isAdmin(){
			return this.$laravel.is_admin
		},
	},
	watch: {
		pageSize: {
			handler: function(val) {
				if(val < 1) {
					val = 1;
					return;
				}

				if(val > 100) {
					val = 100;
					return;
				}

				this.paginationKey++;
			}
		},

		perPage(){
			this.pageSize = this.perPage
			this.fetchData(this.filters, 1, this.perPage)
		},
		currentPage(){
			this.fetchData(this.filters, this.currentPage, this.perPage)
		},
		searchText(){
			this.onSearchQuery()
		},
	},

	created() {
		this.fetchData({})
		this.page_items = this.items.slice(0, this.pageSize);
	},
	mounted() {
		this.$watch(
			'$refs.child.searchText',
			new_value => (this.searchText = new_value)
		);
		this.fetchPortal()
	},
	methods: {
		...mapActions(usePortalStore, [
			'fetchPortal',
			'updatePortal',
			'addBacklightColor',
			'deleteBacklightColor',
			'updateBacklightColors',
		]),

		onChangePage(page_items) {
			this.page_items = page_items;
		},

		fetchData(filters, page = 1, limit = 10) {
			let loader = this.$loading.show();
			this.s_type_main = filters.data_from ? Number(filters.data_from.s_type) : 1;
			this.month = filters.data_from ? filters.data_from.month : new Date().getMonth();
			this.filters = filters

			if(![1,2,3].includes(this.s_type_main)) this.s_type_main = 1

			if(this.s_type_main == 1){
				this.axios.post('/statistics/kpi/groups-and-users', {
					filters: {
						...filters,
						query: this.searchText,
					}
				}, {
					params: {
						page,
						limit
					}
				}).then(({data}) => {
					// items
					const items = data.paginator.data || []
					this.items = items.map(this.processKpi)
					this.totalRows = data.paginator.total
					// this.activities = data.activities;
					this.groups = data.groups;

					// paginate
					this.page_items = this.items.slice(0, this.pageSize);

					this.date = filters.data_from != undefined
						? new Date(filters.data_from.year, filters.data_from.month, 1).toISOString().substring(0, 10)
						: new Date().toISOString().substring(0, 10);

					loader.hide()
				}).catch(error => {
					loader.hide()
					alert(error)
				});
			}
			else if(this.s_type_main == 2){
				this.axios.get('/statistics/bonuses').then(response => {
					this.bonus_groups = response.data;
					loader.hide();
				}).catch(error => {
					loader.hide();
					alert(error);
				});
			}
			else if(this.s_type_main == 3){
				this.axios.post('/statistics/quartal-premiums', {
					filters: {
						...filters,
						query: this.searchText,
					}
				}).then(response => {
					this.quartal_users = response.data[0].map(res=> ({...res, expanded: false}));
					this.quartal_groups = response.data[1].map(res=> ({...res, expanded: false}));
					loader.hide();
				}).catch(error => {
					loader.hide();
					alert(error);
				});
			}
			else{
				loader.hide();
				alert('error!');
			}
		},

		processKpi(kpi){
			let isActive = kpi.is_active
			if(kpi?.histories_latest?.payload && typeof kpi.histories_latest.payload === 'string') {
				kpi.histories_latest.payload = JSON.parse(kpi.histories_latest.payload)
				if(Object.keys(kpi.histories_latest.payload).includes('is_active')){
					isActive = kpi.histories_latest.payload.is_active
				}
			}

			return {
				...kpi,
				targets: kpi.target ? [
					{
						kpiable_id: kpi.targetable_id,
						kpiable_type: kpi.targetable_type,
						name: kpi.target.name,
						type: classToType[kpi.targetable_type],
					},
					...this.combineKpiTargets(kpi)
				] : this.combineKpiTargets(kpi),
				is_active: isActive,
			}
		},

		combineKpiTargets(kpi){
			return [
				...kpi.users.map(item => ({
					...item,
					kpiable_id: item.id,
					kpiable_type: typeToClass[1],
					type: 1,
					name: `${item.last_name} ${item.name}`
				})),
				...kpi.positions.map(item => ({
					...item,
					kpiable_id: item.id,
					kpiable_type: typeToClass[3],
					type: 3,
					name: item.position,
				})),
				...kpi.groups.map(item => ({
					...item,
					kpiable_id: item.id,
					kpiable_type: typeToClass[2],
					type: 2,
					name: item.name,
				})),
			]
		},

		onSearchQuery(){
			if(this.timeout) clearTimeout(this.timeout)
			this.timeout = setTimeout(() => {
				this.fetchData({
					...this.filters,
					query: this.searchText
				})
			}, 300);
		},
	}
}
</script>
