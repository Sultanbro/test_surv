<template>
	<div
		v-if="$can('kpi_edit')"
		class="quartal-premiums px-3 py-1"
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
				@click="addRowItem"
			>
				<i class="fa fa-plus mr-2" />
				<span>Добавить</span>
			</button>
		</div>

		<!-- table NEW -->
		<table class="j-table collapse-table">
			<thead>
				<tr>
					<th class="text-center pointer">
						<i
							class="icon-nd-settings"
							@click="adjustFields"
						/>
					</th>
					<th class="text-left w-100">
						Кому
					</th>
				</tr>
			</thead>
			<tbody>
				<template v-if="premium && newPremiumArray.length > 0">
					<tr>
						<td
							class="text-center"
							@click="premium.expanded = !premium.expanded"
						>
							<i
								v-if="premium.expanded"
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
								class="mr-5"
							>
								<SuperSelect
									v-if="newPremiumArray[0].id == 0"
									style="width: 60%;"
									:values="(new_target == null && newPremiumArray.length > 0) ? [] : [new_target]"
									:single="true"
									@choose="(target) => new_target = target"
									@remove="() => new_target = null"
								/>
								<div
									v-else
									class="d-flex aic"
								>
									<i
										v-if="newPremiumArray[0].target.type == 1"
										class="fa fa-user ml-2 color-user"
									/>
									<i
										v-if="newPremiumArray[0].target.type == 2"
										class="fa fa-users ml-2 color-group"
									/>
									<i
										v-if="newPremiumArray[0].target.type == 3"
										class="fa fa-briefcase ml-2 color-position"
									/>
									<span class="ml-2">{{ newPremiumArray[0].target.name }}</span>
								</div>
							</div>
						</td>
					</tr>
					<template v-if="premium.expanded">
						<tr class="collapsable active">
							<td :colspan="fields.length + 2">
								<div class="table__wrapper">
									<table class="table table-responsive table-inner">
										<thead>
											<tr>
												<th />
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
												v-for="(item, i) in newPremiumArray"
												:key="i"
											>
												<td />
												<td
													v-for="(field, f) in fields"
													:key="f"
													:class="[
														field.class,
														{'b-table-sticky-column l-2 hidden' : field.key == 'target'},
														{'no-hover' : field.key == 'activity_id' && item.source != undefined}
													]"
												>
													<template v-if="field.key == 'created_by' && item.creator != null">
														{{ item.creator.last_name + ' ' + item.creator.name }}
													</template>
													<template v-else-if="field.key == 'updated_by' && item.updater != null">
														{{ item.updater.last_name + ' ' + item.updater.name }}
													</template>

													<template v-else-if="non_editable_fields.includes(field.key)">
														{{ item[field.key] }}
													</template>
													<template v-else-if="field.key == 'activity_id' && item.source != undefined">
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
													</template>

													<template v-else-if="field.key == 'unit'">
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
													</template>

													<template v-else-if="field.key == 'daypart'">
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
														</select>
													</template>

													<template v-else-if="field.key == 'text'">
														<textarea v-model="item[field.key]" />
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
														<i
															class="fa fa-save btn btn-success btn-icon"
															@click="saveNewQuartal(i)"
														/>
														<i
															class="fa fa-trash btn btn-danger btn-icon"
															@click="deleteNewQuartal(i)"
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
														@click="addPremium()"
													>
														<i class="fa fa-plus mr-2" /> <b>Добавить премию</b>
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
							class="pointer"
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
												v-for="(item, i) in page_item.items"
												:key="i"
											>
												<td class="text-center">
													<!--                                           <input class="ml-2" type="checkbox" />-->
													<div>{{ i + 1 }}</div>
												</td>
												<td
													v-for="(field, f) in fields"
													:key="f"
													:class="[
														field.class,
														{'b-table-sticky-column l-2 hidden' : field.key == 'target'},
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
													<template v-else-if="field.key == 'activity_id' && item.source != undefined">
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
														</select>
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
															:disabled="statusRequest"
															@input="changeStatus(item, $event)"
														>
															&nbsp;
														</b-form-checkbox>
														<i
															class="fa fa-save btn btn-success btn-icon"
															@click="saveItemFromTable(p, i)"
														/>
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
														@click="addPremiumGroup(page_item)"
													>
														<i class="fa fa-plus mr-2" /> <b>Добавить премию</b>
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

		<Sidebar
			v-if="activeItem != null"
			title="Настроить премию"
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

					<div
						v-if="field.key == 'target'"
						class="mr-5"
					>
						<SuperSelect
							v-if="activeItem.id == 0"
							class="w-full"
							:values="activeItem.target == null ? [] : [activeItem.target]"
							:single="true"
							@choose="(target) => activeItem.target = target"
						/>
						<div
							v-else
							class="d-flex aic"
						>
							<i
								v-if="activeItem.target.type == 1"
								class="fa fa-user ml-2 color-user"
							/>
							<i
								v-if="activeItem.target.type == 2"
								class="fa fa-users ml-2 color-group"
							/>
							<i
								v-if="activeItem.target.type == 3"
								class="fa fa-briefcase ml-2 color-position"
							/>
							<span class="ml-2">{{ activeItem.target.name }}</span>
						</div>
					</div>

					<div v-else-if="field.key == 'created_by' && activeItem.creator != null">
						{{ activeItem.creator.last_name + ' ' + activeItem.creator.name }}
					</div>

					<div v-else-if="field.key == 'updated_by' && activeItem.updater != null">
						{{ activeItem.updater.last_name + ' ' + activeItem.updater.name }}
					</div>

					<div v-else-if="non_editable_fields.includes(field.key)">
						{{ activeItem[field.key] }}
					</div>



					<div v-else-if="field.key == 'activity_id' && activeItem.source != undefined">
						<div class="d-flex">
							<select
								v-model="activeItem.source"
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
								v-if="Number(activeItem.source) == 1"
								:key="'a' + source_key"
								v-model="activeItem.group_id"
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
								:key="'b' + source_key"
								v-model="activeItem.activity_id"
							>
								<option
									value="0"
									selected
								>
									-
								</option>
								<option
									v-for="activity in grouped_activities(activeItem.source, activeItem.group_id)"
									:key="activity.id"
									:value="activity.id"
								>
									{{ activity.name }}
								</option>
							</select>
						</div>
					</div>

					<div v-else-if="field.key == 'unit'">
						<select
							v-model="activeItem.unit"
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
					</div>

					<div v-else-if="field.key == 'daypart'">
						<select
							v-model="activeItem.daypart"
						>
							<option
								v-for="key in Object.keys(dayparts)"
								:key="key"
								:value="key"
							>
								{{ dayparts[key] }}
							</option>
						</select>
					</div>

					<div v-else-if="field.key == 'text'">
						<textarea v-model="activeItem[field.key]" />
					</div>

					<div v-else>
						<input
							v-model="activeItem[field.key]"
							:type="field.type"
							@change="validate(activeItem[field.key], field.key)"
						>
					</div>
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

import {fields, newQuartalPremium} from './quartal_premiums.js';
import {findModel, sources} from './helpers.js';

export default {
	name: 'QuartalPremiums',
	components: {
		JwPagination,
		SuperFilter,
		SuperSelect,
		Sidebar,
	},
	props: {},
	data() {
		return {
			new_target: null,
			counter: 0,
			premium: null,
			newPremiumArray: [],
			active: 1,
			activeItem: null,
			uri: '/quartal-premiums',
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
			sources: sources,
			non_editable_fields: [
				'created_at',
				'updated_at',
				'created_by',
				'updated_by',
			],
			statusRequest: false,
			timeout: null,
			filters: null,
		}
	},
	watch: {
		show_fields: {
			handler: function (val) {
				localStorage.quartal_premiums_show_fields = JSON.stringify(val);
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
		newPremiumArray(after){
			if(after.length == 0){
				this.counter = 0;
				this.new_target = null;
			}
		},
		searchText(){
			this.onSearchQuery()
		}
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
			if(this.statusRequest) return
			if(item.is_active === e) return
			this.statusRequest = true
			this.axios.post('/quartal-premiums/set/status', {
				premium_id: item.id,
				is_active: e
			}).then(() => {
				this.$toast.success('Статус изменен')
				this.statusRequest = false
			}).catch(() => {
				this.$toast.error('Статус не изменен')
				this.statusRequest = false
			})
		},
		addPremiumGroup(page){
			page.items.push(newQuartalPremium());
			page.items[page.items.length - 1].target = {
				id: page.id,
				type: page.type
			};
		},
		addPremium(){
			this.newPremiumArray.push(newQuartalPremium());
		},
		saveNewQuartal(i){
			this.newPremiumArray[i].target = this.new_target;
			this.save(this.newPremiumArray[i],i);
		},
		deleteNewQuartal(i){
			this.newPremiumArray.splice(i,1);
		},
		addRowItem(){
			if(this.counter == 0){
				this.newPremiumArray.push(newQuartalPremium());
				this.premium = this.newPremiumArray[0];
				this.counter++;
			}
		},
		expand(i) {
			this.page_items[i].expanded = !this.page_items[i].expanded
		},

		onChangePage(page_items) {
			this.page_items = page_items;
		},

		fetch(filter = null) {
			let loader = this.$loading.show();
			this.filters = filter

			this.axios.post( this.uri + '/get', {
				filters: {
					...filter,
					query: this.searchText,
				}
			}).then(response => {
				this.all_items = response.data.items
				this.items = response.data.items;
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

			let obj = {}; // Какие поля показывать
			fields.forEach(field => obj[field.key] = true);

			if(localStorage.quartal_premiums_show_fields) {
				this.show_fields = JSON.parse(localStorage.getItem('quartal_premiums_show_fields'));
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
				el.on_edit = false
				el.source = 0
				el.group_id = 0
				el.selected = false
			});
		},

		prepareFields() {
			let visible_fields = []

			fields.forEach(field => {
				if(this.show_fields[field.key] != undefined && this.show_fields[field.key]) {
					visible_fields.push(field)
				}
			});

			this.fields = visible_fields;
		},

		addItem() {
			this.activeItem = newQuartalPremium()
			this.showSidebar = true
		},

		validateMsg(item) {
			let msg = '';
			if(item.target == null)    msg = 'Выберите Кому назначить'
			if(item.title.length <= 1) msg = 'Заполните название'

			// activity id
			// let a;
			// if(item.source == 1) {
			// 	a = this.activities.findIndex(el => el.source == item.source && el.group_id == item.group_id && el.id == item.activity_id);
			// } else {
			// 	a = this.activities.findIndex(el => el.source == item.source && el.id == item.activity_id);
			// }

			if(item.text.length === 0) {
				msg = 'Заполните поле Текст';
			}

			// another
			if(item.from == null)      msg = 'Выберите начало периода'
			if(item.to == null)        msg = 'Выберите конец периода'
			if(item.quantity <= 0)     msg = 'Кол-во должно быть больше нуля'
			if(item.sum <= 0)          msg = 'Вознаграждение должно быть больше нуля'

			return msg;
		},

		save(item, index) {

			/**
			 * validate item
			 */
			let not_validated_msg = this.validateMsg(item);
			if(not_validated_msg != '') {
				this.$toast.error(not_validated_msg)
				return;
			}

			/**
			 * prepare fields
			 */
			let loader = this.$loading.show();
			let method = item.id == 0 ? 'save' : 'update';

			let fields = {
				targetable_id: item.target.id,
				targetable_type: findModel(item.target.type),
				...item
			};

			let req = item.id == 0
				? this.axios.post(this.uri + '/' + method, fields)
				: this.axios.put(this.uri + '/' + method, fields);

			/**
			 * request
			 */
			req.then(response => {

				if(method == 'save') {
					let quartal_premium = response.data.quartal_premium;
					item.id = quartal_premium.id;
					// this.items.unshift(item);


					let i = this.all_items.findIndex(el => el.type == item.target.type && el.id == item.target.id);
					if(i != -1) {
						let j = this.all_items[i].items.findIndex(el => el.id === item.id);
						if (j !== -1) {
							this.all_items[i].items.splice(j, 1, item);
						} else {
							this.all_items[i].items.unshift(item);
						}
					} else {
						this.all_items.unshift({
							id: item.target.id,
							type: item.target.type,
							name: item.target.name,
							items: [item],
							expanded: false
						});
					}


					this.showSidebar = false
				}
				else{
					item.updated_at = this.$moment(Date.now()).format('DD.MM.YYYY HH:mm')
				}

				this.$toast.info('Сохранено');
				this.newPremiumArray.splice(index,1);
				loader.hide()
			}).catch(error => {
				let m = error;
				if(error.message == 'Request failed with status code 409') {
					m = 'Выберите другую цель "Кому"';
				}

				loader.hide()
				alert(m)
			});

			return req
		},

		deletee(id, p, i) {
			let loader = this.$loading.show();
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
			if(this.all_items[p].items.length == 0) this.all_items.splice(p, 1)
			// this.onSearch();

			this.$toast.info('Удалено');
		},

		saveItem() {
			this.save(this.activeItem)
		},

		saveItemFromTable(p, i) {
			const item = this.page_items[p].items[i]
			this.save(item).then(() => {
				if(item.id === 0){
					this.page_items[p].splice(i, 1)
				}
			})
		},

		saveAll(p){
			this.page_items[p].items.forEach((item, i) => {
				this.saveItemFromTable(p, i)
			})
		},

		deleteItem(p, i) {

			let item = this.page_items[p].items[i]

			if(!confirm('Вы уверены?')) {
				return;
			}

			if(item.id == 0) {
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

			if(this.searchText == '') {
				this.items = this.all_items;
			} else {
				this.items = this.all_items.filter(el => {
					let has = false;

					if (el.name.toLowerCase().indexOf(text) > -1) {
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

		defineSourcesAndGroups() {
			this.items.forEach(p => {
				p.items.forEach(el => {
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
			})

		},

		grouped_activities(source, group_id) {
			if(source == 1) {
				return this.activities.filter(el => el.source == source && el.group_id == group_id);
			} else {
				group_id = 0
				return this.activities.filter(el => el.source == source);
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
