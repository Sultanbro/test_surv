<template>
	<div class="stats px-3 py-1">
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
					class="ml-2"
					v-if="items"
				>
					Найдено: {{ totalRows }}
				</span>
				<span
					class="ml-2"
					v-else
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
						class="mt-4"
					/>
				</b-tab>
			</b-tabs>
		</template>

		<StatsTableBonus
			v-if="s_type_main == 2"
			:groups="bonus_groups"
			:group_names="groups"
			:month="month"
			:key="bonus_groups"
		/>

		<StatsTableQuartal
			v-if="s_type_main == 3"
			:users="quartal_users"
			:groups="quartal_groups"
			:key="quartal_users"
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
					type="number"
					:min="item.prevMax"
					:max="99"
					class="form-control input-surv KPIBacklight-input"
					v-model="item.startValue"
				>
				до:
				<input
					type="number"
					:min="item.prevMax + 1"
					:max="100"
					class="form-control input-surv KPIBacklight-input"
					v-model="item.endValue"
				>
				цвет:
				<input
					type="color"
					class="form-control input-surv KPIBacklight-input"
					v-model="item.color"
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
import { mapActions, mapState } from 'pinia'
import SideBar from '@/components/ui/Sidebar'
import SuperFilter from '@/pages/kpi/SuperFilter' // filter like bitrix
import StatsTableV2 from '@/pages/kpi/StatsTableV2'
import StatsTableBonus from '@/pages/kpi/StatsTableBonus'
import StatsTableQuartal from '@/pages/kpi/StatsTableQuartal'
import StatsTableYear from '@/pages/kpi/StatsTableYear'
import { usePortalStore } from '@/stores/Portal'

// import {formatDate} from './kpis.js';

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
			this.s_type_main = filters.data_from ? filters.data_from.s_type : 1;
			this.month = filters.data_from ? filters.data_from.month : new Date().getMonth();
			this.filters = filters

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
					this.items = data.paginator.data;
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
					console.log(response.data);
					this.bonus_groups = response.data;
					/*this.bonus_groups = response.data.groups;
                    this.bonus_groups = this.bonus_groups.map(res=> ({...res, expanded: false}));
                    for(let i = 0; i < this.bonus_groups.length; i++){
                        this.bonus_groups[i].users = this.bonus_groups[i].users.map(res=> ({...res, expanded: false, totals: {
                                quantity: 0,
                                sum:0,
                                amount:0
                            }
                        }));
                    }*/
					loader.hide();
				}).catch(error => {
					loader.hide();
					alert(error);
				});
			}
			else if(this.s_type_main == 3){
				this.axios.get('/statistics/quartal-premiums').then(response => {

					//this.quartal_items = response.data;
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
