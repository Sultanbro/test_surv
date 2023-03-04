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
		</div>

		<!-- table -->
		<StatsTableV2
			v-if="s_type_main == 1"
			:activities="activities"
			:groups="groups"
			:items="page_items"
			:editable="true"
			:search-text="searchText"
			:date="date"
		/>

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

		<b-pagination
			v-if="s_type_main == 1"
			v-model="currentPage"
			:total-rows="totalRows"
			:per-page="perPage"
			size="sm"
			class="mt-4"
		/>
	</div>
</template>

<script>
import SuperFilter from '@/pages/kpi/SuperFilter' // filter like bitrix
import StatsTableV2 from '@/pages/kpi/StatsTableV2'
import StatsTableBonus from '@/pages/kpi/StatsTableBonus'
import StatsTableQuartal from '@/pages/kpi/StatsTableQuartal'

// import {formatDate} from './kpis.js';

export default {
	name: 'KPIStatsV2',
	components: {
		SuperFilter,
		StatsTableV2,
		StatsTableBonus,
		StatsTableQuartal,
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
			}
		}
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
		currentPage(value){
			this.fetchData(this.filters, value, this.perPage)
		}
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
	},
	methods: {

		onChangePage(page_items) {
			this.page_items = page_items;
		},

		fetchData(filtersObject, page = 1, limit = 10) {
			let loader = this.$loading.show();
			this.s_type_main = filtersObject.data_from ? filtersObject.data_from.s_type : 1;
			this.month = filtersObject.data_from ? filtersObject.data_from.month : new Date().getMonth();
			this.filters = filtersObject

			const filters = {
				group_id: filtersObject.group_id,
				month: filtersObject.data_from?.month,
				year: filtersObject.data_from?.year,
				s_type: filtersObject.data_from?.s_type,
			}

			if(this.s_type_main == 1){
				this.axios.get('/statistics/kpi/groups-and-users', {
					params: {
						...filters,
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

					this.date = filtersObject.data_from != undefined
						? new Date(filtersObject.data_from.year, filtersObject.data_from.month, 1).toISOString().substr(0, 10)
						: new Date().toISOString().substr(0, 10);

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
					console.log(this.quartal_groups);
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

	}
}
</script>
