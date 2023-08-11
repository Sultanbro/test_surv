<template>
	<div class="bonuses px-3 py-1">
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
					@apply="fetch"
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
				@click="addItemRow"
			>
				<i class="fa fa-plus mr-2" />
				<span>Добавить</span>
			</button>
		</div>

		<!-- table NEW -->
		<table class="j-table mb-3 collapse-table bonuses-table">
			<thead>
				<tr>
					<th
						class="text-center pointer"
						@click="adjustFields"
					>
						<i class="icon-nd-settings" />
					</th>
					<th class="text-left w-100">
						Кому
					</th>
				</tr>
			</thead>
			<tbody>
				<template v-if="bonus && newBonusesArray.length > 0">
					<tr>
						<td
							class="text-center pointer"
							@click="bonus.expanded = !bonus.expanded"
						>
							<i
								v-if="bonus.expanded"
								class="fa fa-minus mt-1"
							/>
							<i
								v-else
								class="fa fa-plus mt-1"
							/>
						</td>
						<td class="text-left">
							<div
								v-if="all_fields[0].key == 'target'"
								class="d-flex align-items-center"
							>
								<SuperSelect
									v-if="bonus.id == 0"
									style="width: 60%"
									:onlytype="2"
									:values="(new_target == null && newBonusesArray.length > 0) ? [] : [new_target]"
									:single="true"
									@choose="(target) => new_target = target"
									@remove="() => new_target = null"
								/>
								<div v-else>
									<i
										v-if="bonus.target.type == 1"
										class="fa fa-user ml-2 color-user"
									/>
									<i
										v-if="bonus.target.type == 2"
										class="fa fa-users ml-2 color-group"
									/>
									<i
										v-if="bonus.target.type == 3"
										class="fa fa-briefcase ml-2 color-position"
									/>
									<span class="ml-2">{{ bonus.target.name }}</span>
								</div>

								<i
									class="fa fa-save btn btn-success ml-3"
									@click="saveNewBonusArray()"
								/>
							</div>
						</td>
					</tr>
					<template v-if="bonus.expanded">
						<tr class="collapsable active">
							<td :colspan="fields.length + 2">
								<div class="table__wrapper">
									<table class="table table-responsive table-inner">
										<thead>
											<tr>
												<th class="text-center px-1" />
												<th
													v-for="(field, f) in fields"
													:key="f"
													class="text-left"
													:class="[
														field.class,
														{'b-table-sticky-column l-2 hidden' : field.key == 'target'
														}]"
												>
													{{ field.name }}
												</th>
												<th />
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, i) in newBonusesArray"
												:key="i"
											>
												<td />
												<td>
													<input
														v-model="item.title"
														type="text"
														@change="validate(item[field.key], field.key)"
													>
												</td>
												<td class="no-hover">
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
															v-if="Number(item.source) == 1"
															:key="'a' + source_key"
															v-model="item.group_id"
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
															v-if="Number(item.source) == 1"
															:key="'b' + source_key"
															v-model="item.activity_id"
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
													</div>
												</td>
												<td>
													<select
														v-model="item.unit"
													>
														<option
															value="0"
															selected
														>
															-
														</option>
														<option
															v-for="key in Object.keys(units)"
															:key="key"
															:value="key"
														>
															{{ units[key] }}
														</option>
													</select>
												</td>
												<td class="ho-hover">
													<div class="d-flex">
														<select
															v-model="item.daypart"
														>
															<option
																v-for="key in Object.keys(dayparts)"
																:key="key"
																:value="key"
															>
																{{ dayparts[key] }}
															</option>
															<option
																v-if="item.unit !== 'percent'"
																:value="2"
															>
																Месяц
															</option>
														</select>
														<input
															v-if="item.daypart == 1"
															v-model="item.from"
															type="time"
														>
														<input
															v-if="item.daypart == 1"
															v-model="item.to"
															type="time"
														>
													</div>
												</td>
												<td>
													<input
														v-model="item.sum"
														type="text"
													>
												</td>
												<td>
													<input
														v-model="item.quantity"
														type="text"
													>
												</td>
												<td>
													<input
														v-model="item.text"
														type="textarea"
													>
												</td>
												<td>{{ item.created_at }}</td>
												<td>{{ item.updated_at }}</td>
												<td>{{ item.created_by }}</td>
												<td>{{ item.updated_by }}</td>
												<td class="ho-hover">
													<div class="d-flex px-2">
														<i
															class="fa fa-save btn btn-success btn-icon"
															@click="saveNewBonus(i)"
														/>
														<i
															class="fa fa-trash btn btn-danger btn-icon"
															@click="deleteNewBonus(i)"
														/>
													</div>
												</td>
											</tr>
											<tr>
												<td
													colspan="13"
													class="plus-item"
												>
													<div
														class="p-4"
														@click="addBonus()"
													>
														<i class="fa fa-plus mr-2" /> <b>Добавить бонус</b>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</td>
						</tr>
					</template>
				</template>

				<template v-for="(page_item, p) in page_items">
					<tr :key="p">
						<td
							class="pointer b-table-sticky-column"
							@click="expand(p)"
						>
							<div class="d-flex align-items-center px-2">
								<span class="mr-2">{{ p + 1 }}</span>
								<i
									v-if="page_item.expanded"
									class="fa fa-minus mt-1"
								/>
								<i
									v-else
									class="fa fa-plus mt-1"
								/>
							</div>
						</td>
						<td class="text-left">
							<!-- <superselect
						v-if="item.target == null"
						class="w-full"
						:values="[]"
						:single="true"
						@choose="(target) => item.target = target"
					/>  -->
							<div class="d-flex aic">
								<i
									v-if="page_item.type == 1"
									class="fa fa-user ml-2 color-user"
								/>
								<i
									v-if="page_item.type == 2"
									class="fa fa-users ml-2 color-group"
								/>
								<i
									v-if="page_item.type == 3"
									class="fa fa-briefcase ml-2 color-position"
								/>
								<span class="ml-2">{{ page_item.name }}</span>
								<i
									class="fa fa-save btn btn-success btn-icon ml-a"
									@click="saveAll(p)"
								/>
							</div>
						</td>
					</tr>
					<template v-if="page_item.items !== undefined && page_item.items.length > 0">
						<tr
							:key="p + 'a'"
							class="collapsable"
							:class="{'active': page_item.expanded}"
						>
							<td :colspan="fields.length + 2">
								<div class="table__wrapper w-100">
									<table class="table table-responsive table-inner">
										<thead>
											<tr>
												<th />
												<th
													v-for="(field, f) in fields"
													:key="f"
													:class="[
														field.class,
														{'b-table-sticky-column l-2 hidden' : field.key == 'target'}
													]"
												>
													{{ field.name }}
													<img
														v-if="field.key === 'sum'"
														v-b-popover.hover.right.html="'Укажите в количестве<br> или в процентах %'"
														class="KPIBonuses-icon-info"
														src="/images/dist/profit-info.svg"
														alt="info icon"
													>
												</th>
												<th />
											</tr>
										</thead>
										<tbody>
											<tr
												v-for="(item, i) in page_item.items"
												:key="i"
											>
												<td class="text-center">
													{{ i + 1 }}
												</td>
												<td
													v-for="(field, f) in fields"
													:key="f"
													:class="[
														field.class,
														{'b-table-sticky-column l-2 hidden' : field.key == 'target'},
														{'no-hover' : field.key == 'daypart'},
														{'no-hover' : field.key == 'activity_id' && item.source != undefined}
													]"
												>
													<template v-if="field.key == 'target'" />
													<template v-else-if="field.key == 'created_by' && item.creator != null">
														{{ item.creator.last_name + ' ' + item.creator.name }}
													</template>
													<template v-else-if="field.key == 'updated_by' && item.updater != null">
														{{ item.updater.last_name + ' ' + item.updater.name }}
													</template>
													<template v-else-if="non_editable_fields.includes(field.key)">
														{{ item[field.key] }}
													</template>
													<template
														v-else-if="field.key == 'activity_id' && item.source != undefined"
													>
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
																v-if="Number(item.source) == 1"
																:key="'c' + source_key"
																v-model="item.group_id"
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
																v-if="Number(item.source) == 1"
																:key="'d' + source_key"
																v-model="item.activity_id"
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
														</div>
													</template>
													<template v-else-if="field.key == 'unit'">
														<select
															v-model="item.unit"
															@change="onChangeUnit(item)"
														>
															<option
																value="0"
																selected
															>
																-
															</option>
															<option
																v-for="key in Object.keys(units)"
																:key="key"
																:value="key"
															>
																{{ units[key] }}
															</option>
														</select>
													</template>
													<template v-else-if="field.key == 'daypart'">
														<div class="d-flex">
															<select
																v-model="item.daypart"
															>
																<option
																	v-for="key in Object.keys(dayparts)"
																	:key="key"
																	:value="key"
																>
																	{{ dayparts[key] }}
																</option>
																<option
																	v-if="item.unit !== 'percent'"
																	:value="2"
																>
																	Месяц
																</option>
															</select>
															<input
																v-if="item.daypart == 1"
																v-model="item.from"
																type="time"
															>
															<input
																v-if="item.daypart == 1"
																v-model="item.to"
																type="time"
															>
														</div>
													</template>
													<template v-else-if="field.key === 'sum'">
														<input
															v-model="item[field.key]"
															:type="field.type"
															@change="validate(item[field.key], field.key)"
														>
														{{ item.unit === 'percent' ? '%' : '' }}
													</template>
													<template v-else-if="field.key === 'quantity' && item.unit === 'percent'">
														<!-- Ничего -->
													</template>
													<template v-else>
														<input
															v-model="item[field.key]"
															:type="field.type"
															@change="validate(item[field.key], field.key)"
														>
													</template>
												</td>
												<td class="no-hover">
													<div class="d-flex px-2">
														<b-form-checkbox
															class="kpi-status-switch"
															switch
															:checked="!!item.is_active"
															:disabled="requestInProgress"
															@input="changeStatus(item, $event)"
														>
															&nbsp;
														</b-form-checkbox>
														<i
															class="fa fa-save btn btn-success btn-icon"
															@click="saveItemFromTable(p, i)"
														/>
														<!--													<i-->
														<!--															class="fa fa-pen btn btn-primary btn-icon"-->
														<!--															@click="openSidebar(p, i)"-->
														<!--													/>-->
														<i
															class="fa fa-trash btn btn-danger btn-icon"
															@click="deleteItem(p, i)"
														/>
													</div>
												</td>
											</tr>
											<tr>
												<td
													colspan="13"
													class="plus-item"
												>
													<div
														class="px-4 py-3"
														@click="addBonusGroup(page_item)"
													>
														<i class="fa fa-plus mr-2" /> <b>Добавить бонус</b>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
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
			class=""
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
			title="Настройка списка"
			ok-text="Закрыть"
			size="lg"
			@ok="modalAdjustVisibleFields = !modalAdjustVisibleFields"
		>
			<div class="row">
				<div
					v-for="(field, f) in all_fields"
					:key="f"
					class="col-md-4 mb-2"
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

		<!--		v-if="activeItem != null"-->
		<Sidebar
			v-if="false"
			title="Настроить бонус"
			:open="showSidebar"
			width="40%"
			@close="closeSidebar"
		>
			<div class="row m-0">
				<div
					v-for="(field, f) in all_fields"
					:key="f"
					class="mb-3"
					:class="field.alter_class"
				>
					<div class="mb-2 mt-2 field">
						{{ field.name }}
					</div>
					<div v-if="field.key == 'target'" />
					<!--<div v-if="field.key == 'target'" class="mr-5">
                                <superselect
                                    v-if="activeItem.id == 0"
                                    class="w-full"
                                    :values="activeItem.target == null ? [] : [activeItem.target]"
                                    :single="true"
                                    @choose="(target) => activeItem.target = target" />
                                <div v-else class="d-flex aic">
                                    <i class="fa fa-user ml-2 color-user" v-if="activeItem.target.type == 1"></i>
                                    <i class="fa fa-users ml-2 color-group" v-if="activeItem.target.type == 2"></i>
                                    <i class="fa fa-briefcase ml-2 color-position" v-if="activeItem.target.type == 3"></i>
                                    <span class="ml-2">{{ activeItem.target.name }}</span>
                                </div>
                            </div>-->

					<div v-else-if="field.key == 'created_by' && activeItem.creator != null">
						{{ activeItem.creator.last_name + ' ' + activeItem.creator.name }}
					</div>

					<div v-else-if="field.key == 'updated_by' && activeItem.updater != null">
						{{ activeItem.updater.last_name + ' ' + activeItem.updater.name }}
					</div>

					<div v-else-if="non_editable_fields.includes(field.key)">
						{{ activeItem[field.key] }}
					</div>


					<!--<div v-else-if="field.key == 'activity_id' && activeItem.source != undefined">
                                <div class="d-flex">
                                    <select
                                        v-model="activeItem.source"
                                        @change="++source_key"
                                    >
                                        <option v-for="key in Object.keys(sources)"
                                            :value="key">
                                            {{ sources[key] }}
                                        </option>
                                    </select>

                                    <select
                                        v-if="Number(activeItem.source) == 1"
                                        v-model="activeItem.group_id"
                                        :key="'a' + source_key"
                                    >
                                        <option value="0" selected>-</option>
                                        <option v-for="(group, id) in groups" :value="id">{{ group }}</option>
                                    </select>

                                    <select
                                        v-model="activeItem.activity_id"
                                        :key="'b' + source_key"
                                    >
                                        <option value="0" selected>-</option>
                                        <option v-for="activity in grouped_activities(activeItem.source, activeItem.group_id)" :value="activity.id">{{ activity.name }}</option>
                                    </select>
                                </div>
                            </div>-->

					<!--<div v-else-if="field.key == 'unit'">
                                <select
                                    v-model="activeItem.unit"
                                >
                                    <option value="0" selected>-</option>
                                    <option v-for="key in Object.keys(units)" :value="key">{{ units[key] }}</option>
                                </select>
                            </div>-->

					<!--<div v-else-if="field.key == 'daypart'">
                                <select
                                    v-model="activeItem.daypart"
                                >
                                    <option v-for="key in Object.keys(dayparts)" :value="key">{{ dayparts[key] }}</option>
                                </select>
                            </div>-->

					<div v-else-if="field.key == 'text'">
						<textarea v-model="activeItem[field.key]" />
					</div>

					<!--<div v-else>
                                <input :type="field.type" v-model="activeItem[field.key]" @change="validate(activeItem[field.key], field.key)" />
                            </div>-->
				</div>
				<div>
					<button
						class="d-flex aic  btn btn-success ml-3"
						@click="saveItem"
					>
						<i
							class="fa fa-save"
						/>
						<span class="ml-2">Сохранить</span>
					</button>
				</div>
			</div>
		</Sidebar>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import JwPagination from 'jw-vue-pagination'
import SuperFilter from '@/pages/kpi/SuperFilter' // filter like bitrix
import SuperSelect from '@/components/SuperSelect'
import Sidebar from '@/components/ui/Sidebar' // сайдбар table

import {fields, newBonus} from './bonuses.js';
import {findModel/* , groupBy */, sources} from './helpers.js';

export default {
	name: 'KPIBonuses',
	components: {
		JwPagination,
		SuperFilter,
		SuperSelect,
		Sidebar,
	},
	props: {},
	data() {
		return {
			my_items: [],
			new_target: null,
			bonus: null,
			groupsArray: [],
			counter: 0,
			newBonusesArray: [],
			newBonusExpanded: false,
			addNewBonus: false,
			active: 1,
			activeItem: null,
			uri: 'bonus',
			showSidebar: false,
			show_fields: [],
			fields: [],
			all_fields: fields,
			groups: [],
			searchText: '',
			modalAdjustVisibleFields: false,
			page_items: [],
			pageSize: 20,
			paginationKey: 1,
			items: [], // after filter changes
			all_items: [],
			activities: [],
			source_key: 1,
			dayparts: {
				0: 'Полный день',
				1: 'Период',
			},
			units: {
				one: 'За каждую единицу',
				all: 'За все',
				first: 'Первый кто достигнет',
				percent: '% с продаж',
			},
			sources: sources,
			non_editable_fields: [
				'created_at',
				'updated_at',
				'created_by',
				'updated_by',
			],
			requestInProgress: false,
			timeout: null,
			filters: null,
		}
	},
	watch: {
		show_fields: {
			handler: function (val) {
				localStorage.bonus_show_fields = JSON.stringify(val);
				this.prepareFields();
			},
			deep: true
		},
		pageSize: {
			handler: function (val) {
				if (val < 1) {
					val = 1;
					return;
				}

				if (val > 100) {
					val = 100;
					return;
				}
				this.paginationKey++;
			}
		},
		newBonusesArray(after) {
			if (after.length == 0) {
				this.counter = 0;
				this.new_target = null;
			}
		},
		searchText(){
			this.onSearchQuery()
		},
	},

	created() {
		this.setDefaultShowFields()
		this.prepareFields();
		this.addStatusToItems();
	},
	mounted() {
		this.fetch();
		this.$watch(
			'$refs.child.searchText',
			new_value => (this.searchText = new_value)
		);

	},
	methods: {
		changeStatus(item, e){
			if(this.requestInProgress) return
			this.requestInProgress = true
			this.axios.post('/bonus/set/status', {
				bonus_id: item.id,
				is_active: e
			}).then(() => {
				this.$toast.success('Статус изменен')
				this.requestInProgress = false
			}).catch(() => {
				this.$toast.error('Статус не изменен')
				this.requestInProgress = false
			})
		},
		onChangeUnit(item){
			if(item.unit === 'percent'){
				item.quantity = null
			}
		},
		addBonusGroup(page) {
			page.items.push(newBonus());
			page.items[page.items.length - 1].target = {
				id: page.id,
				type: page.type
			};
		},
		saveNewBonusArray() {
			const item = this.newBonusesArray[this.newBonusesArray.length - 1];
			item.target = this.new_target;
			/**
				 * validate item
				 */

			const not_validated_msg = this.validateMsg(item);
			if (not_validated_msg != '') {
				this.$toast.error(not_validated_msg)
				return;
			}

			/**
				 * prepare fields
				 */
			const loader = this.$loading.show();
			const method = item.id == 0 ? 'save' : 'update';
			const titles = [];
			const sums = [];
			const activity_ids = [];
			const units = [];
			const quantities = [];
			const dayparts = [];
			const froms = [];
			const tos = [];
			const texts = [];
			this.newBonusesArray.forEach(bonus => {
				titles.push(bonus.title);
				sums.push(bonus.sum);
				activity_ids.push(bonus.activity_id);
				units.push(bonus.unit);
				quantities.push(bonus.quantity);
				dayparts.push(bonus.daypart);
				froms.push(bonus.from);
				tos.push(bonus.to);
				texts.push(bonus.text);
			});
			const fields = {
				...item,
				targetable_id: item.target.id,
				targetable_type: findModel(item.target.type),
			};
			const req = item.id == 0
				? this.axios.post(this.uri + '/' + method, {
					bonuses: [{
						...fields,
						targetable_type: item.target.type,
					}]
				})
				: this.axios.put(this.uri + '/' + method, fields);
				/**
				 * request
				 */
			req.then(() => {

				if (method == 'save') {
					this.fetch()
					// const bonus = response.data.bonus;
					// item.id = bonus.id;
					// this.items.unshift(item);

					// const i = this.all_items.findIndex(el => el.type == item.target.type && el.id == item.target.id);
					// if (i != -1) {
					// 	this.all_items[i].items.unshift(item);
					// } else {
					// 	this.all_items.unshift({
					// 		id: item.target.id,
					// 		type: item.target.type,
					// 		name: item.target.name,
					// 		items: [item],
					// 		expanded: false
					// 	});
					// }
					this.showSidebar = false
				}
				this.$toast.info('Сохранено');
				this.newBonusesArray = [];
				loader.hide()
			}).catch(error => {
				let m = error;
				if (error.message == 'Request failed with status code 409') {
					m = 'Выберите другую цель "Кому"';
				}

				loader.hide()
				alert(m)
			});
			return false;
		},
		addBonus() {
			this.newBonusesArray.unshift(newBonus());
			this.newBonusesArray[this.newBonusArray.length - 1].bonus.target = this.new_target;
		},
		addItemRow() {
			if (this.counter == 0) {
				this.newBonusesArray.push(newBonus());
				this.bonus = this.newBonusesArray[0];
				this.counter++;
			}
			this.addNewBonus = true;
		},
		swapFields() {
			const temp = this.fields[4];
			const temp2 = this.fields[6];
			this.fields[6] = temp;
			this.fields[4] = temp2;
		},
		expand(i) {
			this.page_items[i].expanded = !this.page_items[i].expanded
		},
		onChangePage(page_items) {
			this.page_items = page_items;
		},
		fetch(filter = null) {
			const loader = this.$loading.show();
			this.filters = filter

			this.axios.post(this.uri + '/get', {
				filters: {
					...filter,
					query: this.searchText,
				}
			}).then(response => {


				this.all_items = response.data.bonuses
				this.items = response.data.bonuses;
				this.activities = response.data.activities;
				this.groups = response.data.groups;
				this.defineSourcesAndGroups('t');
				this.items.forEach(el => el.expanded = false);
				this.page_items = this.items.slice(0, this.pageSize);
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},
		openSidebar(p, i) {
			this.activeItem = this.page_items[p].items[i]
			this.showSidebar = true
		},
		closeSidebar() {
			this.showSidebar = false
			this.activeItem = null;
		},

		setDefaultShowFields() {
			const obj = {}; // Какие поля показывать
			fields.forEach(field => obj[field.key] = true);
			if (localStorage.bonus_show_fields) {
				this.show_fields = JSON.parse(localStorage.getItem('bonus_show_fields'));
				if (this.show_fields == null) this.show_fields = obj
			} else {
				this.show_fields = obj
			}
		},
		adjustFields() {
			this.modalAdjustVisibleFields = true;
		},
		addStatusToItems() {
			this.items.forEach(el => {
				el.on_edit = false
				el.source = 0
				el.group_id = 0
				el.selected = false
			});
		},
		prepareFields() {
			const visible_fields = []

			fields.forEach(field => {
				if (this.show_fields[field.key] != undefined
						&& this.show_fields[field.key]
				) {
					visible_fields.push(field)
				}
			});
			this.fields = visible_fields;
			this.swapFields();
		},
		addItem() {
			this.activeItem = newBonus()
			this.showSidebar = true
		},
		validateMsg(item) {
			let msg = '';
			if (item.target == null) msg = 'Выберите Кому назначить'
			if (item.title.length <= 1) msg = 'Заполните название'

			// activity id
			let a;
			if (item.source == 1) {
				a = this.activities.findIndex(el => el.source == item.source && el.group_id == item.group_id && el.id == item.activity_id);
			} else {
				a = this.activities.findIndex(el => el.source == item.source && el.id == item.activity_id);
			}

			if ((item.activity_id == 0 || item.activity_id == undefined || a == -1) && item.source != 0) {
				msg = 'Выберите показатель';
				return false;
			}
			// another
			if (item.quantity <= 0 && item.unit !== 'percent') msg = 'Кол-во должно быть больше нуля'
			if (item.sum <= 0) msg = 'Вознаграждение должно быть больше нуля'

			return msg;
		},

		save(item, index) {

			/**
				 * validate item
				 */

			const not_validated_msg = this.validateMsg(item);
			if (not_validated_msg != '') {
				this.$toast.error(not_validated_msg)
				return;
			}

			/**
				 * prepare fields
				 */
			const loader = this.$loading.show();
			const method = item.id == 0 ? 'save' : 'update';
			const fields = {
				...item,
				targetable_id: item.target.id,
				targetable_type: findModel(item.target.type),
			};
			const req = item.id == 0
				? this.axios.post(this.uri + '/' + method, {
					bonuses: [{
						...fields,
						targetable_type: item.target.type
					}]
				})
				: this.axios.put(this.uri + '/' + method, fields);
				/**
				 * request
				 */
			req.then(() => {

				if (method == 'save') {
					this.fetch()
					// const bonus = response.data.bonus;
					// item.id = bonus.id;
					// // this.items.unshift(item);

					// const i = this.all_items.findIndex(el => el.type == item.target.type && el.id == item.target.id);
					// if (i != -1) {
					// 	this.all_items[i].items.unshift(item);
					// } else {
					// 	this.all_items.unshift({
					// 		id: item.target.id,
					// 		type: item.target.type,
					// 		name: item.target.name,
					// 		items: [item],
					// 		expanded: false
					// 	});
					// }
					this.showSidebar = false
				}
				else{
					item.updated_at = this.$moment(Date.now()).format('DD.MM.YYYY HH:mm')
				}
				this.$toast.info('Сохранено');
				this.newBonusesArray.splice(index, 1);
				loader.hide()
			}).catch(error => {
				let m = error;
				if (error.message == 'Request failed with status code 409') {
					m = 'Выберите другую цель "Кому"';
				}
				loader.hide()
				alert(m)
			});
			return false;
		},
		deletee(id, p, i) {
			const loader = this.$loading.show();
			this.axios.delete(this.uri + '/delete/' + id).then(() => {
				this.deleteEvery(id, p, i)
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},
		deleteEvery(id, p, i) {

			// let a = this.all_items.findIndex(el => el.id == id)
			// if(a != -1) this.all_items.splice(a, 1);
			this.all_items[p].items.splice(i, 1);
			if (this.all_items[p].items.length == 0) this.all_items.splice(p, 1)
			// this.onSearch();
			this.$toast.info('Удалено');
		},
		saveItem() {
			this.save(this.activeItem)
		},
		saveItemFromTable(p, i) {
			this.save(this.page_items[p].items[i])
		},
		saveAll(p){
			/**
			 * validate item
			 */
			if(this.requestInProgress) return
			this.requestInProgress = true

			const tosave = []
			const toupdate = []
			let not_validated_msg = ''
			this.page_items[p].items.every(item => {
				not_validated_msg = this.validateMsg(item)
				if (not_validated_msg) return false
				if(item.id){
					toupdate.push({
						...item,
						targetable_id: item.target.id,
						targetable_type: findModel(item.target.type),
					})
				}
				else{
					tosave.push({
						...item,
						targetable_id: item.target.id,
						targetable_type: item.target.type
					})
				}
				return true
			})

			if(not_validated_msg){
				this.requestInProgress = false
				return this.$toast.error(not_validated_msg)
			}

			const loader = this.$loading.show()
			const requests = []
			const errors = []
			requests.push(this.axios.post(this.uri + '/save', {
				bonuses: tosave
			}).catch(error => {
				const msg = error.message == 'Request failed with status code 409' ? 'Выберите другую цель "Кому"' : error
				errors.push(msg)
			}))
			toupdate.forEach(item => {
				requests.push(this.axios.put(this.uri + '/update', item).catch(error => {
					const msg = error.message == 'Request failed with status code 409' ? 'Выберите другую цель "Кому"' : error
					errors.push(msg)
				}))
			})
			Promise.allSettled(requests).then(() => {
				const msg = errors.length
					?	errors.length === requests.length
						? 'Ошибка при сохранении\n\n' + errors.join('\n')
						: 'Частично сохранено\n\n' + errors.join('\n')
					: 'Сохранено'
				this.fetch()
				this.$toast[errors.length ? 'error' : 'info'](msg)
				this.requestInProgress = false
				loader.hide()
			})
			return false;
		},
		deleteItem(p, i) {

			const item = this.page_items[p].items[i]
			if (!confirm('Вы уверены?')) {
				return;
			}
			if (item.id == 0) {
				this.deleteEvery(item.id, p, i);
				return;
			}
			this.deletee(item.id, p, i);
		},
		showStat() {
			this.$toast.info('Показать статистику');
		},

		onSearch() {
			const text = this.searchText.toLowerCase();

			if (this.searchText == '') {
				this.items = this.all_items;
			}
			else {
				this.items = this.all_items.filter(el => {
					let has = false;
					if ( el.name.toLowerCase().indexOf(text) > -1 ) {
						has = true;
					}
					return has;
				});
			}
			this.page_items = this.items.slice(0, this.pageSize);
		},
		validate(value, field) {
			value = Math.abs(Number(value));
			if (isNaN(value) || isFinite(value)) {
				value = 0;
			}
			if (['lower_limit', 'upper_limit'].includes(field) && value > 100) {
				value = 100;
			}
		},
		defineSourcesAndGroups() {
			this.items.forEach(p => {
				p.items.forEach(el => {
					el.source = 0;
					el.group_id = 0;
					if (el.activity_id != 0) {
						const i = this.activities.findIndex(a => a.id == el.activity_id);
						if (i != -1) {
							el.source = this.activities[i].source
							if (el.source == 1) el.group_id = this.activities[i].group_id
						}
					}
				});
			})

		},
		grouped_activities(source, group_id) {
			if (source == 1) {
				return this.activities.filter(el => el.source == source && el.group_id == group_id);
			} else {
				group_id = 0
				return this.activities.filter(el => el.source == source);
			}
		},
		saveNewBonus(b) {
			this.newBonusesArray[b].target = this.new_target;
			this.save(this.newBonusesArray[b], b);
			if (this.newBonusesArray.length == 0) {
				this.counter = 0;
			}
		},
		deleteNewBonus(b) {
			this.newBonusesArray.splice(b, 1);
			if (this.newBonusesArray.length == 0) {
				this.counter = 0;
			}
		},
		onSearchQuery(){
			if(this.timeout) clearTimeout(this.timeout)
			this.timeout = setTimeout(() => {
				this.fetch({
					...this.filters,
					query: this.searchText
				})
			}, 300);
		}
	},

}
</script>

<style lang="scss">
	.bonuses-table{
		tbody{
			th,td {
				vertical-align: middle;
			}
		}
	}
	.KPIBonuses{
		&-icon-info{
			width: 1.6rem;
		}
	}
</style>
