<template>
	<div class="kpi-item">
		<table class="table table-inner">
			<thead>
				<tr>
					<th />
					<th>Наименование активности</th>
					<th>Вид плана</th>
					<th v-if="kpi_page">
						Показатели <i
							class="fa fa-info-circle"
							@click="showDescription()"
						/>
					</th>
					<th v-if="kpi_page">
						Ед. изм.
					</th>
					<th>Целевое значение на месяц</th>
					<th>Удельный вес, %</th>
					<th v-if="!kpi_page">
						Факт
					</th>
					<th v-if="!kpi_page">
						% выполнения
					</th>
					<th>Сумма премии при выполнении плана, KZT</th>
					<th>Заработано</th>
					<th v-if="kpi_page" />
				</tr>
			</thead>
			<tbody :key="refreshItemsKey">
				<template v-if="kpi_page">
					<tr
						v-for="(item, i) in items"
						:key="i"
						class="jt-row"
						:class="{
							'j-hidden': !expanded,
							'j-deleted': item.deleted != undefined && item.deleted,
						}"
					>
						<td class="first-column text-center">
							{{ i + 1 }}
						</td>
						<td>
							<input
								v-model="item.name"
								type="text"
							>
						</td>
						<td class="text-center">
							<select v-model="item.method">
								<option
									v-for="key in Object.keys(methods)"
									:key="key"
									:value="key"
								>
									{{ methods[key] }}
								</option>
							</select>
						</td>
						<td class="text-center no-hover">
							<div class="d-flex">
								<select
									v-model="item.source"
									@change="++source_key"
								>
									<option
										v-for="key in Object.keys(sources)"
										:key="key"
										:value="key"
									>
										{{ sources[key] }}
									</option>
								</select>

								<select
									v-if="item.source == 1"
									:key="'c' + source_key"
									v-model="item.group_id"
									@change="++source_key"
								>
									<option
										value="0"
										selected
									>
										-
									</option>
									<option
										v-for="(group, id) in groups"
										:key="id"
										:value="id"
									>
										{{ group }}
									</option>
								</select>

								<select
									:key="'d' + source_key"
									v-model="item.activity_id"
									:class="{'hidden' : item.source == 0}"
								>
									<option
										value="0"
										selected
									>
										-
									</option>
									<option
										v-for="activity in grouped_activities(item.source, item.group_id)"
										:key="activity.id"
										:value="activity.id"
									>
										{{ activity.name }}
									</option>
								</select>

								<select
									v-if="item.source == 1 && !isCell(item.activity_id)"
									v-model="item.common"
								>
									<option
										value="0"
										selected
									>
										Свой
									</option>
									<option value="1">
										Всего отдела
									</option>
								</select>

								<input
									v-if="item.source == 1 && isCell(item.activity_id)"
									v-model="item.cell"
									type="text"
									placeholder="Ячейка: C7"
								>
							</div>
						</td>
						<td class="text-center w-sm">
							<input
								v-model="item.unit"
								type="text"
							>
						</td>
						<td class="text-center">
							<input
								v-model="item.plan"
								type="number"
							>
						</td>
						<td class="text-center">
							<input
								v-model="item.share"
								type="number"
								min="0"
								max="100"
							>
						</td>
						<td class="text-center">
							{{ item.sum }}
						</td>
						<td class="text-center">
							{{ parseInt(item.sum * (item.percent / 100)) }}
						</td>
						<td class="no-hover">
							<i
								v-if="item.deleted != undefined && item.deleted"
								class="fa fa-arrow-up mx-3 btn btn-danger btn-icon"
								@click="restoreItem(i)"
							/>
							<i
								v-else
								class="fa fa-trash mx-3 btn btn-danger btn-icon"
								@click="deleteItem(i)"
							/>
						</td>
					</tr>

					<tr>
						<td
							colspan="10"
							class="plus-item"
							@click="addItem"
						>
							<div class="p-4">
								<i class="fa fa-plus mr-2" /> <b>Добавить активность</b>
							</div>
						</td>
					</tr>
				</template>

				<template v-else>
					<tr
						v-for="(item, i) in items"
						:key="i"
						class="jt-row j-hidden"
						:class="{
							'j-hidden': !expanded,
						}"
					>
						<td class="text-center">
							{{ i + 1 }}
						</td>
						<td class="px-2">
							{{ item.histories_latest ? item.histories_latest.payload.name : item.name }}
						</td>
						<td class="text-center">
							{{ methods[item.method] }}
						</td>
						<td class="text-center">
							<b>{{ item.histories_latest ? item.histories_latest.payload.plan : item.plan }} {{ item.histories_latest ? item.histories_latest.payload.unit : item.unit }}</b>
						</td>
						<td class="text-center">
							{{ item.histories_latest ? item.histories_latest.payload.share : item.share }}
						</td>
						<td
							v-if="editable"
							class="text-center"
						>
							<input
								v-if="[1,3,5].includes(item.method)"
								v-model="item.fact"
								type="number"
								min="0"
								@change="updateStat(i)"
							>
							<input
								v-else
								v-model="item.avg"
								type="number"
								min="0"
								@change="updateStat(i)"
							>
						</td>
						<td
							v-else
							class="text-center"
						>
							<!-- sum or avg by method -->
							<div v-if="[1,3,5].includes(item.method)">
								{{ item.fact }}
							</div>
							<div v-else>
								{{ Number(item.avg).toFixed(2) }}
							</div>
						</td>
						<td class="text-center">
							{{ item.percent }}
						</td>
						<td class="text-center">
							{{ my_sum * (parseInt(item.histories_latest ? item.histories_latest.payload.share : item.share)/100) }}
						</td>
						<td class="text-center">
							{{ item.sum }}
						</td>
					</tr>
				</template>
			</tbody>
		</table>

		<sidebar
			title="Показатели"
			:open="show_description"
			width="70%"
			@close="toggle()"
		>
			<p>Тут указывается какой показатель сотрудника нужно смотреть для выявления процента выполнения.</p>
			<p>Первый select источник:</p>
			<p><strong>- без источникa:</strong></p>
			<p>руководитель сам будет ставить нужный коэффициент</p>
			<p><strong>- из показателей отдела:&nbsp;</strong></p>
			<p>берем данные из подробных таблиц в Аналитике отдела.</p>
			<p>Появляются три selectа:</p>
			<ul>
				<li>выбираем отдел</li>
				<li>выбираем показатель</li>
				<li>выбор <em>Свой</em> или <em>Всего отдела</em>. Свой выберет только показатель пользователя, а Всего отдела - какой показатель сделал отдел.</li>
			</ul>
			<p>Если выбрать <em>ячейка из сводной</em> нужно будет указать название ячейки как в Excel.</p>
			<p><strong>- из битрикса:</strong></p>
			<p>Если в интеграции настроен <strong>Битрикс24</strong>, будем брать оттуда показатели, при условии, что&nbsp; ID пользователей из битрикса были связаны с Jobtron.</p>
			<p><strong>- из amocrm:</strong></p>
			<p>Если в интеграции настроен <strong>Amocrm</strong>, будем брать оттуда показатели, при условии, что&nbsp; ID пользователей из amocrm были связаны с Jobtron.</p>
			<p><strong>- другие :</strong></p>
			<p>разные показатели в Jobtron</p>
		</sidebar>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
import {newKpiItem, /* numberize,  */calcCompleted, calcSum} from './kpis.js';
import {sources, methods} from './helpers.js';

export default {
	name: 'KpiItemsV2',
	props: {
		my_sum:{
			default:0
		},
		kpi_id: {
			default: 0
		},
		expanded: {
			default: false,
		},
		items: {
			default: []
		},
		activities: {
			default: {}
		},
		groups: {
			default: {}
		},
		completed_80: {
			default: 0,
		},
		completed_100: {
			default: 0,
		},
		lower_limit: {
			default: 80,
		},
		upper_limit: {
			default: 100,
		},
		editable: {
			default: false
		},
		kpi_page: {
			default: false
		},
		allow_overfulfillment: {
			default: false
		},
		date: {
			default: null
		},
	},
	data() {
		return {
			active: 1,
			methods: methods,
			sources: sources,
			refreshItemsKey: 1,
			source_key: 1,
			show_description: false
		}
	},
	computed: {
	},
	watch: {
		items: {
			handler: function() {
				this.recalc();
				this.getSum();
			},
			deep: true
		},
		lower_limit: {
			handler: function() {
				this.recalc();
			},
		},
		upper_limit: {
			handler: function() {
				this.recalc();
			},
		},
		completed_80: {
			handler: function() {
				this.recalc();
			},
		},
		completed_100: {
			handler: function() {
				this.recalc();
			},
		}
	},

	created() {
		this.fillSelectOptions()

		this.defineSourcesAndGroups('with_sources_and_group_id');

		this.recalc();
		this.getSum();
		if(!this.editable) {
			this.items.forEach(el => el.expanded = true);
		}

	},
	mounted(){

	},

	methods: {
		toggle() {
			this.show_description = false;
		},
		showDescription(){
			this.show_description = !this.show_description;
		},
		recalc() {
			this.items.forEach(el => {

				// if(
				//     [1,3,5].includes(el.method)
				//     && !this.kpi_page
				//     && el.common != 1
				//     && el.source == 1
				//     && el.activity != null
				//     && el.activity.view != 7
				// ) {
				//     el.plan = el.daily_plan * numberize(el.workdays);
				// }
				el.percent = calcCompleted(el);
				el.sum = calcSum(el,
					{
						lower_limit: this.lower_limit,
						upper_limit: this.upper_limit,
						completed_80: this.completed_80,
						completed_100: this.completed_100,
						allow_overfulfillment: this.allow_overfulfillment,
					},
					this.kpi_page ? 1 : el.percent / 100.0,
				);
			});
		},

		deleteItem(i) {
			this.items[i].deleted = true
			if(this.kpi_id == 0) this.items.splice(i, 1);
			this.refreshItemsKey++;
		},

		restoreItem(i) {
			this.items[i].deleted = false
			this.refreshItemsKey++;
		},

		addItem() {
			this.items.push(newKpiItem());
		},

		getActivity(id) {
			return this.activities.find(el => el.id == id)
		},

		fillSelectOptions() {
		},

		groupBy(xs, key) {
			return xs.reduce(function(rv, x) {
				(rv[x[key]] = rv[x[key]] || []).push(x);
				return rv;
			}, {});
		},

		defineSourcesAndGroups() {
			this.items.forEach(el => {
				el.source = 0;
				el.group_id = 0;

				if(el.activity_id != 0) {
					let i = this.activities.findIndex(a => a.id == el.activity_id);
					if(i != -1) {
						el.source = this.activities[i].source
						if(el.source == 1) el.group_id = this.activities[i].group_id
					}
				}
			});
		},

		grouped_activities(source, group_id) {
			if(source == 1) {
				return this.activities.filter(el => el.source == source && el.group_id == group_id);
			} else {
				group_id = 0
				return this.activities.filter(el => el.source == source);
			}
		},

		isCell(activity_id) {
			let i = this.activities.findIndex(el => el.id == activity_id);
			return i != -1 && this.activities[i].view == 7;
		},

		updateStat(i) {
			let loader = this.$loading.show();

			const item = this.items[i]
			const date = this.date != null
				? this.date
				: this.$moment(Date.now()).format('YYYY-MM-DD')

			const value = [1,3,5].includes(item.method) ? item.fact : item.avg

			this.axios.post('/statistics/update-stat', {
				user_id: this.kpi_id,
				kpi_item_id: item.id,
				activity_id: item.activity_id,
				value: value,
				date: date,
			}).then(() => {
				this.$toast.success('Изменено');
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},
		getSum(){
			let sum = 0;
			this.items.forEach(item => {
				sum += item.sum;
			});
			this.$emit('getSum', sum);
		}
	}
}
</script>
