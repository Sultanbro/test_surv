<template>
	<div class="indicators px-3 py-1">
		<!-- top line -->
		<div class="d-flex mb-2 mt-2 jcsb aifs">
			<div class="d-flex aic mr-2">
				<div class="d-flex aic mr-2">
					<span>Показывать:</span>
					<input
						type="number"
						min="1"
						max="100"
						v-model="pageSize"
						class="form-control ml-2 input-sm"
					>
				</div>
				<input
					class="searcher mr-2 form-control"
					v-model="searchText"
					type="text"
					placeholder="Поиск по совпадениям..."
					@keyup="onSearch"
				>
				<span class="ml-2 whitespace-no-wrap">
					Найдено: {{ items.length }}
				</span>
			</div>
		</div>

		<!-- table NEW -->
		<table class="table table-responsive j-table thead-word-break-normal">
			<thead>
				<tr>
					<th class="b-table-sticky-column text-center">
						<i
							class="fa fa-cog"
							@click="adjustFields"
						/>
					</th>
					<th
						v-for="(field, i) in fields"
						:key="i"
						:class="[
							field.class,
							{'b-table-sticky-column l-60' : field.key == 'name'
							}]"
					>
						{{ field.name }}
					</th>
				</tr>
			</thead>
			<tbody>
				<template v-for="(item, i) in page_items">
					<tr :key="i">
						<td class="b-table-sticky-column text-center">
							{{ i + 1 }}
						</td>
						<td
							v-for="(field, f) in fields"
							:key="f"
							:class="[
								field.class,
								{'b-table-sticky-column l-60' : field.key == 'name'
								}]"
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

							<template v-else-if="field.key == 'source' && item.source != undefined">
								<div class="d-flex text-left">
									<div
										class="mr-4"
										v-if="sources[item.source] !== undefined"
									>
										{{ sources[item.source] }}
									</div>
									<div v-if="Number(item.source) == 1 && groups[item.group_id] !== undefined">
										{{ groups[item.group_id] }}
									</div>
								</div>
							</template>

							<template v-else-if="field.key == 'method'">
								<div v-if="methods[item.method] !== undefined">
									{{ methods[item.method] }}
								</div>
							</template>

							<template v-else-if="field.key == 'view'">
								<div v-if="views[item.view] !== undefined">
									{{ views[item.method] }}
								</div>
							</template>

							<template v-else-if="field.key == 'editable'">
								<input
									type="checkbox"
									:checked="item[field.key]"
									disabled
								>
							</template>

							<template v-else>
								{{ item[field.key] }}
							</template>
						</td>
					</tr>
				</template>
			</tbody>
		</table>

		<!-- pagination -->
		<JwPagination
			class=""
			:key="paginationKey"
			:items="items"
			:labels="{
				first: '<<',
				last: '>>',
				previous: '<',
				next: '>'
			}"
			@changePage="onChangePage"
			:page-size="+pageSize"
		/>


		<!-- modal Adjust Visible fields -->
		<b-modal
			v-model="modalAdjustVisibleFields"
			title="Настройка списка"
			@ok="modalAdjustVisibleFields = !modalAdjustVisibleFields"
			ok-text="Закрыть"
			size="lg"
		>
			<div class="row">
				<div
					class="col-md-4 mb-2"
					v-for="(field, f) in all_fields"
					:key="f"
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
	</div>
</template>

<script>
import JwPagination from 'jw-vue-pagination'
import {fields/* , newItem */} from './indicators.js';
import {sources, methods, views} from './helpers.js';

export default {
	name: 'KPIIndicators',
	components: {
		JwPagination,
	},
	props: {},
	watch: {
		show_fields: {
			handler: function (val) {
				localStorage.activities_show_fields = JSON.stringify(val);
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
		}
	},
	data() {
		return {
			active: 1,
			activeItem: null,
			uri: 'activities',
			show_fields: [],
			fields: [],
			all_fields: fields,
			groups: [],
			searchText: '',
			modalAdjustVisibleFields: false,
			page_items: [],
			pageSize: 100,
			paginationKey: 1,
			items: [], // after filter changes
			all_items: [],
			activities: [],
			source_key: 1,
			sources: sources,
			methods: methods,
			views: views,
			non_editable_fields: [
				'created_at',
				'updated_at',
				'created_by',
				'updated_by',
			]
		}
	},

	created() {
		this.setDefaultShowFields()
		this.prepareFields();
	},

	mounted() {
		this.fetch()
	},

	methods: {

		onChangePage(page_items) {
			this.page_items = page_items;
		},

		fetch(filter = null) {
			let loader = this.$loading.show();

			this.axios.post(this.uri + '/get', {
				filters: filter
			}).then(response => {

				this.all_items = response.data.items
				this.items = response.data.items;
				this.groups = response.data.groups;

				this.page_items = this.items.slice(0, this.pageSize);

				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		setDefaultShowFields() {

			let obj = {}; // Какие поля показывать
			fields.forEach(field => obj[field.key] = true);

			if(localStorage.activities_show_fields) {
				this.show_fields = JSON.parse(localStorage.getItem('activities_show_fields'));
				if(this.show_fields == null) this.show_fields = obj
			} else {
				this.show_fields = obj
			}

		},

		adjustFields() {
			this.modalAdjustVisibleFields = true;
		},

		prepareFields() {
			let visible_fields = []

			fields.forEach(field => {
				if(this.show_fields[field.key] != undefined
                    && this.show_fields[field.key]
				) {
					visible_fields.push(field)
				}
			});

			this.fields = visible_fields;
		},

		validateMsg(item) {
			let msg = '';

			if(item.name.length <= 1) msg = 'Заполните название'
			if(item.weekdays > 7 && item.weekdays < 1) msg = 'Рабочие дни от 1 до 7 дней'
			// if(item.source == 1 && group_id == 0) msg = 'Выберите отдел'

			return msg;
		},

		save(item) {

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
					let indicator = response.data.indicator;
					item.id = indicator.id;

					this.all_items.unshift(item);
				}

				this.$toast.info('Сохранено');
				loader.hide()
			}).catch(error => {
				let m = error;
				loader.hide()
				alert(m)
			});
		},

		deletee(id, i) {
			let loader = this.$loading.show();
			this.axios.delete(this.uri + '/delete/' + id).then(() => {
				this.deleteEvery(id, i)
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		deleteEvery(id) {

			let a = this.all_items.findIndex(el => el.id == id)
			if(a != -1) this.all_items.splice(a, 1);

			this.onSearch();

			this.$toast.info('Удалено');
		},

		onSearch() {
			let text = this.searchText;

			if(this.searchText == '') {

				this.items = this.all_items;

			} else {

				let groups = this.groups;
				let group_ids = Object.keys(groups).filter(key => groups[key].toLowerCase().indexOf(text.toLowerCase()) > -1)
				this.items = this.all_items.filter(el => {
					let has = false;

					if (
						el.name.toLowerCase().indexOf(text.toLowerCase()) > -1
					) {
						has = true;
					}


					if (group_ids.includes[el.group_id]) {
						has = true;
					}


					if (
						el.creator != null
						&& (
							el.creator.name.toLowerCase().indexOf(text.toLowerCase()) > -1
							|| el.creator.last_name.toLowerCase().indexOf(text.toLowerCase()) > -1
						)
					) {
						has = true;
					}

					if (
						el.updater != null
						&& (
							el.updater.name.toLowerCase().indexOf(text.toLowerCase()) > -1
							|| el.updater.last_name.toLowerCase().indexOf(text.toLowerCase()) > -1
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

	},

}
</script>
