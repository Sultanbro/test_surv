<template>
	<div
		v-if="$can('kpi_edit')"
		class="kpi"
	>
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
					@apply="fetchKPI"
				/>
				<!--<input
                class="searcher mr-2 input-sm"
                v-model="searchText"
                type="text"
                placeholder="Поиск по совпадениям..."
                @keyup="onSearch"
            >-->
				<span class="ml-2">
					Найдено: {{ items.length }}
				</span>
			</div>

			<button
				class="btn rounded btn-success"
				@click="addKpi"
			>
				<i class="fa fa-plus mr-2" />
				<span>Добавить</span>
			</button>
		</div>

		<!-- table -->
		<table class="j-table">
			<thead>
				<tr class="table-heading">
					<th
						class="first-column text-center pointer"
						@click="adjustFields"
					>
						<i class="icon-nd-settings" />
					</th>
					<th
						v-for="(field, i) in fields"
						:key="i"
						:class="field.class"
					>
						{{ field.name }}
					</th>
					<th>Действия</th>
				</tr>
			</thead>

			<tbody>
				<template v-for="(item, i) in page_items">
					<!-- <tr v-if="item.target.name.includes(searchText) || searchText.length == 0 || (item.creator && (item.creator.last_name + ' ' + item.creator.name).includes(searchText)) || (item.updater && (item.updater.last_name + ' ' + item.updater.name).includes(searchText)) || (item.items.filter( i => { return i.name.includes(searchText)  } ).length > 0)"></tr> -->
					<tr :key="i">
						<td
							class="pointer"
							@click="expand(i)"
						>
							<div class="d-flex align-items-center px-2">
								<span class="mr-2">{{ i + 1 }}</span>
								<i
									v-if="item.expanded"
									class="fa fa-minus mt-1"
								/>
								<i
									v-else
									class="fa fa-plus mt-1"
								/>
							</div>
						</td>
						<td
							v-for="(field, f) in fields"
							:key="f"
							:class="field.class"
						>
							<div v-if="field.key == 'target'">
								<SuperSelect
									v-if="item.id == 0"
									:key="i"
									class="w-full"
									:values="item.targets"
									:open-on-mount="!item.id"
									@choose="(target) => item.target = target"
									@remove="() => item.target = null"
								/>
								<div
									v-else
									class="d-flex aic"
								>
									<div class="">
										<span
											v-for="target, targetIndex in item.targets"
											:key="targetIndex"
										>
											<i
												v-if="target.type == 1"
												class="fa fa-user ml-2"
											/>
											<i
												v-if="target.type == 2"
												class="fa fa-users ml-2"
											/>
											<i
												v-if="target.type == 3"
												class="fa fa-briefcase ml-2"
											/>
											<span class="ml-2 kpi-name-rows">
												{{ target.name }}
												<span
													v-if="item.user && 0"
													class="kpi-name-row"
												>
													({{ getUserGourpsString(item.user) }})
												</span>
											</span>
										</span>
									</div>


									<b-form-checkbox
										class="kpi-status-switch"
										switch
										:checked="!!item.is_active"
										:disabled="statusRequest"
										@input="changeStatus(item, $event)"
									>
										&nbsp;
									</b-form-checkbox>
								</div>
							</div>

							<div
								v-else-if="field.key == 'stats'"
								:class="field.class"
							>
								<a
									:href="'/kpi?target='+ (item.target ? item.target.name : '')"
									target="_blank"
									class="btn btn-primary btn-icon"
								>
									<i class="fa fa-chart-bar" />
								</a>
							</div>

							<div v-else-if="field.key == 'created_by' && item.creator != null">
								{{ item.creator.last_name + ' ' + item.creator.name }}
							</div>

							<div v-else-if="field.key == 'updated_by' && item.updater != null">
								{{ item.updater.last_name + ' ' + item.updater.name }}
							</div>

							<div
								v-else-if="non_editable_fields.includes(field.key)"
								:class="field.class"
							>
								{{ item[field.key] }}
							</div>

							<div
								v-else
								:class="field.class"
							>
								<input
									v-model="item[field.key]"
									type="text"
									class="form-control"
									@change="validate(item[field.key], field.key)"
								>
							</div>
						</td>
						<td>
							<div class="d-flex">
								<i
									class="fa fa-cog ml-2 mr-1 btn btn-icon"
									@click="settingsItem = item"
								/>
								<i
									class="fa fa-save ml-2 mr-1 btn btn-success btn-icon"
									@click="saveKpi(i)"
								/>
								<i
									class="fa fa-trash btn btn-danger btn-icon"
									@click="deleteKpi(i)"
								/>
							</div>
						</td>
					</tr>

					<template v-if="item.items !== undefined">
						<tr
							:key="i + 'a'"
							class="collapsable"
							:class="{'active': item.expanded}"
						>
							<td :colspan="fields.length + 2">
								<div class="table__wrapper w-100">
									<KpiItems
										:kpi_id="item.id"
										:items="item.items"
										:expanded="item.expanded"
										:activities="activities"
										:groups="groups"
										:completed_80="item.completed_80"
										:completed_100="item.completed_100"
										:lower_limit="item.lower_limit"
										:upper_limit="item.upper_limit"
										:editable="true"
										:kpi_page="true"
										:allow_overfulfillment="!!item.off_limit"
										@getSum="item.my_sum = $event"
									/>
								</div>
							</td>
						</tr>
					</template>
				</template>
			</tbody>
		</table>

		<!-- pagination -->
		<JwPagination
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


		<!-- modal Adjust Visible fields -->
		<b-modal
			v-model="modalAdjustVisibleFields"
			title="Настройка списка «KPI»"
			ok-text="Закрыть"
			size="lg"
			@ok="modalAdjustVisibleFields = !modalAdjustVisibleFields"
		>
			<div class="row">
				<div
					v-for="(field, f) in all_fields"
					:key="f"
					class="col-md-4 mb-4"
				>
					<b-form-checkbox
						v-model="show_fields[field.key]"
						:value="true"
						:unchecked-value="false"
					>
						{{ field.name }}
					</b-form-checkbox>
				</div>
			</div>
		</b-modal>

		<SimpleSidebar
			v-if="settingsItem"
			title="Настройки kpi"
			:open="!!settingsItem"
			width="400px"
			@close="settingsItem = null"
		>
			<template #body>
				<label class="d-flex aic mb-2">
					<b-form-checkbox
						class="kpi-status-switch"
						switch
						:checked="!!settingsItem.off_limit"
						:disabled="statusRequest"
						@input="changeOffLimit(settingsItem, $event)"
					>
						&nbsp;
					</b-form-checkbox>
					<p>Доначислять за превышение 100% от плана</p>
				</label>
			</template>
			<template #footer />
		</SimpleSidebar>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import JwPagination from 'jw-vue-pagination'
import SuperFilter from '@/pages/kpi/SuperFilter' // filter like bitrix
import KpiItems from '@/pages/kpi/KpiItems'
import SuperSelect from '@/components/SuperSelect'
import SimpleSidebar from '@/components/ui/SimpleSidebar' // сайдбар table

import {kpi_fields, newKpi} from './kpis.js'

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
	name: 'KPI',
	components: {
		JwPagination,
		SuperFilter,
		SuperSelect,
		KpiItems,
		SimpleSidebar,
	},
	props: {},
	data() {
		return {
			active: 1,
			show_fields: [],
			all_fields: kpi_fields,
			fields: [],
			uri: '/kpi',
			groups: [],
			searchText: '',
			modalAdjustVisibleFields: false,
			page_items: [],
			pageSize: 100,
			paginationKey: 1,
			items: [],
			all_items: [],
			activities: [],
			non_editable_fields: [
				'created_at',
				'updated_at',
				'created_by',
				'updated_by',
			],
			statusRequest: false,
			offLimitRequest: false,
			timeout: null,
			filters: null,
			settingsItem: null,
		}
	},
	watch: {
		show_fields: {
			handler: function (val) {
				localStorage.kpi_show_fields = JSON.stringify(val);
				this.prepareFields();
			},
			deep: true
		},
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
		searchText(){
			this.onSearchQuery()
		}
	},

	created() {
		this.fetchKPI()

		this.setDefaultShowFields()
		this.prepareFields();
		this.addStatusToItems();
	},
	mounted() {
		this.$watch(
			'$refs.child.searchText', // WTF!?!?!?
			new_value => (this.searchText = new_value)
		);
	},
	methods: {
		changeStatus(item, e){
			if(this.statusRequest) return
			this.statusRequest = true
			this.axios.post('/kpi/set/status', {
				id: item.id,
				is_active: e
			}).then(() => {
				this.$toast.success('Статус изменен')
				this.statusRequest = false
			}).catch(() => {
				this.$toast.error('Статус не изменен')
				this.statusRequest = false
			})
		},
		async changeOffLimit(item){
			if(this.offLimitRequest) return
			this.offLimitRequest = true
			try {
				await this.axios.put('/kpi/set-off-limit', {
					id: item.id,
					off_limit: !item.off_limit
				})
				item.off_limit = !item.off_limit
			}
			catch (error) {
				console.error(error)
			}
			this.offLimitRequest = false
		},
		expand(i) {
			this.page_items[i].expanded = !this.page_items[i].expanded
		},

		getUserGourpsString(user){
			return user.groups.map(group => group.name).join(', ')
		},

		onChangePage(page_items) {
			this.page_items = page_items;
		},

		fetchKPI(filter = null) {
			let loader = this.$loading.show();

			this.filters = filter
			this.axios.post(this.uri + '/' + 'get', {
				filters: {
					...filter,
					query: this.searchText
				}
			}).then(response => {
				this.items = response.data.data.kpis.map(this.addKpiTargets);
				this.all_items = response.data.data.kpis.map(this.addKpiTargets);
				this.activities = response.data.data.activities;
				this.groups = response.data.data.groups;

				this.page_items = this.items.slice(0, this.pageSize);

				this.addStatusToItems();
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		addKpiTargets(kpi){
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
				] : this.combineKpiTargets(kpi)
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

		setDefaultShowFields() {
			let obj = {}; // Какие поля показывать
			kpi_fields.forEach(field => obj[field.key] = true);

			if(localStorage.kpi_show_fields) {
				this.show_fields = JSON.parse(localStorage.getItem('kpi_show_fields'));
				if(this.show_fields == null) this.show_fields = obj
			} else {
				this.show_fields = obj
			}
		},

		adjustFields() {
			this.modalAdjustVisibleFields = true;
		},

		addStatusToItems() {
			this.items.forEach(el => {

				el.items.forEach(a => {
					a.source = 0
					a.group_id = 0
				});

				el.on_edit = false

			});
		},

		prepareFields() {
			const visible_fields = []

			kpi_fields.forEach(field => {
				if(this.show_fields[field.key] != undefined && this.show_fields[field.key]) {
					visible_fields.push(field)
				}
			});

			this.fields = visible_fields;
		},

		addKpi() {
			this.items.unshift(newKpi());
			this.$toast.info('Добавлен KPI');
		},

		validateMsg(item) {
			let msg = '';

			if(item.target == null && !item.targets.length) msg = 'Выберите Кому назначить'

			// wtf share ???
			// eslint-disable-next-line no-unused-vars
			let share = 0

			if(item.items != undefined) {

				item.items.every((el, i) => {

					if(!(el.deleted !== undefined && el.deleted)) share += Math.abs(el.share);


					if(el.name.length <= 1) {
						msg = 'Заполните название активности #' + (i+1);
						return false;
					}

					if((el.activity_id == 0 || el.activity_id == undefined) && el.source != 0) {
						msg = 'Выберите показатель #' + (i+1);
						return false;
					}


					// if(Number(el.plan) <= 0) {
					//     msg = 'План должен быть больше 0 #' + (i+1);
					//     return false;
					// }


					return true;
				});
			}

			return msg;
		},

		saveKpi(i) {

			let item = this.items[i]
			let method = this.items[i].id == 0 ? 'save' : 'update';

			/**
			 * validate item
			 */
			let not_validated_msg = this.validateMsg(item);
			if(not_validated_msg != '') {
				this.$toast.error(not_validated_msg)
				return;
			}

			let loader = this.$loading.show();

			let fields = {
				id: item.id,
				// targetable_id: item.target.id,
				// targetable_type: findModel(item.target.type),
				completed_80: item.completed_80,
				completed_100: item.completed_100,
				upper_limit: item.upper_limit,
				lower_limit: item.lower_limit,
				items: item.items,
				off_limit: item.off_limit,
				kpiables: item.targets.map(item => ({
					...item,
					kpiable_id: item.id,
					kpiable_type: typeToClass[item.type],
				})),
			};

			let req = this.items[i].id == 0
				? this.axios.post(this.uri + '/' + method, fields)
				: this.axios.put(this.uri + '/' + method, fields);

			req.then(({data}) => {
				item.id = data.data.id
				item.items.forEach((el, index) => {
					el.id = data.data.items[index]
				});

				this.removeDeletedItems(item.items)

				this.$toast.info('KPI Сохранен');
				loader.hide()
			}).catch(error => {
				let m = error;
				if(error.message == 'Request failed with status code 409') {
					m = 'Выберите другую цель "Кому". Этому объекту уже назначен KPI';
				}

				loader.hide()
				alert(m)
			});


		},

		removeDeletedItems(items) {
			let indexes = [];
			let counter = 0;
			items.forEach((el, index) => {
				if(el.deleted != undefined && el.deleted) {
					indexes.push(index)
				}
			});

			indexes.forEach(index => {
				items.splice(index-counter, 1);
				counter++;
			});

		},

		deleteKpi(i) {

			let item = this.items[i]
			let a = this.all_items.findIndex(el => el.id == item.id);

			if(!confirm('Вы уверены?')) {
				return;
			}

			if(item.id == 0) {
				if(a != -1) this.all_items.splice(a, 1);
				// this.onSearch();
				this.$toast.info('KPI Удален!');
				return;
			}

			let loader = this.$loading.show();
			this.axios.delete(this.uri + '/delete/' + item.id).then(() => {


				if(a != -1) this.all_items.splice(a, 1);
				// this.onSearch();

				this.$toast.info('KPI Удален!');
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		showStat() {
			this.$toast.info('Показать статистику');
		},

		onSearch() {
			const text = this.searchText.toLowerCase();

			if(this.searchText == '') {
				this.items = this.all_items;
			} else {
				this.items = this.all_items.filter(el => {
					let has = false;

					if (el.target != null && el.target.name.toLowerCase().indexOf(text) > -1) {
						has = true;
					}

					if (
						el.title.toLowerCase().indexOf(text) > -1
					) {
						has = true;
					}

					if (
						el.creator != null
						&& (
							el.creator.name.toLowerCase().indexOf(text) > -1
							|| el.creator.last_name.toLowerCase().indexOf(text) > -1
						)
					) {
						has = true;
					}

					if (
						el.updater != null
						&& (
							el.updater.name.toLowerCase().indexOf(text) > -1
							|| el.updater.last_name.toLowerCase().indexOf(text) > -1
						)
					) {
						has = true;
					}

					return has;
				});
			}

			this.page_items = this.items.slice(0, this.pageSize);
		},

		validate(value, field) {
			value = Math.abs(Number(value));
			if(isNaN(value) || isFinite(value)) {
				value = 0;
			}

			if(['lower_limit', 'upper_limit'].includes(field) && value > 100) {
				value = 100;
			}
		},

		onSearchQuery(){
			if(this.timeout) clearTimeout(this.timeout)

			this.timeout = setTimeout(() => {
				this.fetchKPI({
					...this.filters,
					query: this.searchText
				})
			}, 300);
		},

		countAvg() {

			this.items.forEach(kpi => {

				let kpi_sum = 0;
				let kpi_count = 0;

				kpi.users.forEach(user => {

					let count = 0;
					let sum = 0;
					let avg = 0;

					user.items.forEach(item => {
						sum += Number(item.percent);
						count++;
					});

					/**
					* count avg of user items
					*/
					avg = count > 0 ? Number(sum / count).toFixed(2) : 0;

					user.avg = avg;

					// all kpi sum
					kpi_sum += Number(avg);
					kpi_count++;
				});
				/**
				* count avg completed percent of kpi by users
				*/
				kpi.avg = kpi_count > 0 ? Number(Number(kpi_sum / kpi_count * 100).toFixed(2)) : 0;

			});
		},
	},
}
</script>

<style>
.kpi-name-rows{
	display: inline-flex;
	flex-flow: column;
}
.kpi-name-row{
	font-size: 1.2rem;
	color: #777;
}
</style>
