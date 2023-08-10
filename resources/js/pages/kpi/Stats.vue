<template>
	<div class="stats px-3 py-1">
		<!-- top line -->
		<div class="d-flex my-4 jcsb aifs">
			<div class="d-flex aic mr-2">
				<div class="d-flex aic mr-2">
					<span>Показывать:</span>
					<input
						v-model="pageSize"
						type="number"
						min="1"
						max="100"
						class="form-control ml-2 input-sm"
					>
				</div>
				<SuperFilter
					ref="child"
					:groups="groups"
					@apply="fetchData"
				/>
				<span
					v-if="items"
					class="ml-2"
				>
					Найдено: {{ items.length }}
				</span>
				<span
					v-else
					class="ml-2"
				>
					Найдено: 0
				</span>
			</div>
		</div>

		<!-- table -->
		<StatsTable
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

		<!-- pagination -->
		<JwPagination
			v-if="s_type_main == 1"
			:key="paginationKey"
			class="mt-3"
			:items="items"
			:labels="{
				first: '<<',
				last: '>>',
				previous: '<',
				next: '>'
			}"
			:page-size="+pageSize"
			@changePage="onChangePage"
		/>
	</div>
</template>

<script>
import JwPagination from 'jw-vue-pagination'
import SuperFilter from '@/pages/kpi/SuperFilter' // filter like bitrix
import StatsTable from '@/pages/kpi/StatsTable'
import StatsTableBonus from '@/pages/kpi/StatsTableBonus'
import StatsTableQuartal from '@/pages/kpi/StatsTableQuartal'

// import {formatDate} from './kpis.js';

export default {
	name: 'KPIStats',
	components: {
		JwPagination,
		SuperFilter,
		StatsTable,
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
			quartal_groups: []
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
	},

	created() {
		this.fetchData([])
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

		fetchData(filters) {
			let loader = this.$loading.show();
			this.s_type_main = filters.data_from ? filters.data_from.s_type : 1;
			this.month = filters.data_from ? filters.data_from.month : new Date().getMonth();

			if(this.s_type_main == 1){
				this.axios.post('/statistics/kpi', {
					filters: filters
				}).then(response => {
					// items
					this.items = response.data.items;
					this.activities = response.data.activities;
					this.groups = response.data.groups;

					// paginate
					this.page_items = this.items.slice(0, this.pageSize);

					this.date = filters.data_from != undefined
						? new Date(filters.data_from.year, filters.data_from.month, 1).toISOString().substr(0, 10)
						: new Date().toISOString().substr(0, 10);

					loader.hide()
				}).catch(error => {
					loader.hide()
					alert(error)
				});
			}else if(this.s_type_main == 2){
				this.axios.get('/statistics/bonuses').then(response => {
					this.bonus_groups = response.data;
					loader.hide();
				}).catch(error => {
					loader.hide();
					alert(error);
				});
			}else if(this.s_type_main == 3){
				this.axios.get('/statistics/quartal-premiums').then(response => {

					//this.quartal_items = response.data;
					this.quartal_users = response.data[0].map(res=> ({...res, expanded: false}));
					this.quartal_groups = response.data[1].map(res=> ({...res, expanded: false}));
					loader.hide();
				}).catch(error => {
					loader.hide();
					alert(error);
				});
			}else{
				loader.hide();
				alert('error!');
			}
		},

	}
}
</script>
