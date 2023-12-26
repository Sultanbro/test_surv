<template>
	<div
		:key="skey"
		class="TopGauges d-flex justify-content-start mt-3"
		style="flex-wrap:wrap"
		:class="{'top': page == 'top'}"
	>
		<template v-for="(group, group_index) in utility">
			<div
				:key="group_index"
				:class="wrapper_class"
				class="TopGauges-group"
			>
				<div
					v-if="editable"
					class="TopGauges-title text-center font-bold mb-3 mt-2 d-flex justify-content-center"
				>
					<a
						:href="'/timetracking/an?group='+ group.id + '&active=1&load=1'"
						target="_blank"
					>{{ group.name }}</a>
					<div
						v-if="page == 'top' && group.gauges.length < 4"
						class=" ml-2 pointer"
						title="Добавить новый спидометр"
						@click="showAddWindow(group.id, group_index)"
					>
						<i class="fa fa-plus-square" />
					</div>
				</div>
				<div
					v-if="group.gauges.length"
					class="TopGauges-gauges d-flex justify-content-center"
					:style="page == 'top' ? 'flex-wrap:wrap; width: 240px;' : ''"
				>
					<div
						v-for="(gauge, gauge_index) in group.gauges"
						:key="gauge.id"
						class="TopGauges-gauge text-center gauge"
						:class="{
							'TopGauges-gauge_single': group.gauges.length === 1,
						}"
					>
						<div
							:data-test="gauge_index"
							:class="['scale', {
								'scale-bl': +gauge_index === 0,
								'scale-br': +gauge_index === 1,
								'scale-bl': +gauge_index === 2,
								'scale-br': +gauge_index === 3,
							}]"
						>
							<p
								class="text-center g-title"
								:class="{'underline': gauge.is_main == 1}"
							>
								{{ gauge.name }} <template v-if="page == 'analytics'">
									{{ gauge.diff }}%
								</template>
								<!-- <span class="btn" @click="edit(group_index, gauge_index)"><i class="fa fa-cogs"></i></span> -->
							</p>
							<div
								v-if="page == 'top'"
								:key="gauge.key"
								title="Нажмите, чтобы редактировать"
								@click="edit(group_index, gauge_index)"
							>
								<VGauge
									:value="Number(gauge.value)"
									:height="gauge.height"
									:options="gauge.options"
									:width="gauge.width"
									:unit="gauge.unit.toString()"
									:min-value="Number(gauge.min_value)"
									:max-value="Number(gauge.max_value)"
									:top="true"
									gauge-value-class="gauge-span"
								/>
							</div>
							<div
								v-else
								:key="gauge.key + 'a'"
								title="Нажмите, чтобы редактировать"
								@click="edit(group_index, gauge_index)"
							>
								<VGauge
									:value="Number(gauge.value)"
									height="90px"
									:options="gauge.options"
									width="150px"
									:unit="gauge.unit.toString()"
									:min-value="Number(gauge.min_value)"
									:max-value="Number(gauge.max_value)"
									:top="true"
									gauge-value-class="gauge-span"
								/>
							</div>
							<p class="text-center text-14">
								{{ Number(gauge.value) }}{{ gauge.unit }} из {{ gauge.max_value }}{{ gauge.unit }}
							</p>
						</div>
						<div
							v-show="gauge.editable"
							class="mb-5 edit-window"
						>
							<div>
								<div class="d-flex justify-content-between align-items-center">
									<span class="pr-2 l-label">Min</span>
									<input
										v-model="gauge.min_value"
										type="text"
										class="TopGauges-input form-control form-control-sm w-250 wiwi flex-1"
									>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<span class="pr-2 l-label">Max</span>
									<input
										v-model="gauge.max_value"
										type="text"
										class="TopGauges-input form-control form-control-sm w-250 wiwi"
									>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<span class="pr-2 l-label">Сег</span>
									<input
										v-model="gauge.sections"
										type="text"
										class="TopGauges-input form-control form-control-sm w-250 wiwi"
									>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<span class="pr-2 l-label">Ед.</span>
									<input
										v-model="gauge.unit"
										type="text"
										class="TopGauges-input form-control form-control-sm wiwi"
									>
								</div>
								<template v-if="gauge.fixed == 0">
									<div class="d-flex justify-content-between align-items-center">
										<span class="pr-2 l-label">Наз</span>
										<input
											v-model="gauge.name"
											type="text"
											class="TopGauges-input form-control form-control-sm wiwi"
										>
									</div>
									<div class="d-flex justify-content-between align-items-center">
										<span class="pr-2 l-label">Окр</span>
										<input
											v-model="gauge.round"
											type="text"
											class="TopGauges-input form-control form-control-sm wiwi"
										>
									</div>
									<div class="d-flex justify-content-between align-items-center">
										<span class="pr-2 l-label">Акт</span>
										<select
											v-model="gauge.activity_id"
											class="TopGauges-input form-control form-control-sm h-23"
										>
											<option
												:key="-1"
												:value="-1"
											>
												Ячейка из сводной
											</option>
											<template v-for="(group_activity, key) in group.group_activities">
												<option
													v-if="group_activity.name !== 'Ячейка из сводной'"
													:key="key"
													:value="group_activity.id"
												>
													{{ group_activity.name }}
												</option>
											</template>
										</select>
									</div>
									<div
										v-if="gauge.activity_id > 0"
										class="d-flex justify-content-between align-items-center"
									>
										<span class="pr-2 l-label">Тип</span>
										<select
											v-model="gauge.value_type"
											class="TopGauges-input form-control form-control-sm h-23"
										>
											<option value="sum">
												Сумма выполненного
											</option>
											<option value="avg">
												Среднее значение
											</option>
										</select>
									</div>
									<div
										v-if="gauge.activity_id == -1"
										class="d-flex justify-content-between align-items-center mt-1"
									>
										<span class="pr-2">Ячейка</span>
										<input
											v-model="gauge.cell"
											type="text"
											class="TopGauges-input form-control form-control-sm wiwi text-uppercase"
										>
									</div>
								</template>
								<div>
									<b-form-checkbox
										v-model="gauge.reversed"
										:value="1"
										:unchecked-value="0"
										class="mb-3"
									>
										Отразить цвета
									</b-form-checkbox>
								</div>
								<div>
									<b-form-checkbox
										v-model="gauge.is_main"
										:value="1"
										:unchecked-value="0"
										class="mb-3"
									>
										Ключевой
									</b-form-checkbox>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<input
										v-model="gauge.angle"
										type="range"
										class="TopGauges-input form-control form-control-sm w-250 mr-2 wiwi"
										min="-0.2"
										max="0.2"
										step="0.01"
									>
									{{ gauge.angle }}
								</div>
							</div>
							<div class="d-flex">
								<button
									class="btn btn-primary rounded mt-1 mr-2"
									@click="save(group_index, gauge_index)"
								>
									Сохранить
								</button>
								<button
									class="btn btn-danger rounded mt-1"
									@click="delete_gauge(group_index, gauge_index)"
								>
									Удалить
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</template>

		<!-- Modal Create activity -->
		<b-modal
			v-model="showNewGaugeWindow"
			title="Добавить новый спидометр"
			size="lg"
			class="modalle"
			@ok="create_gauge()"
		>
			<div class="row">
				<div class="col-5">
					<p>Название</p>
				</div>
				<div class="col-7">
					<input
						v-model="newGauge.name"
						type="text"
						class="form-control form-control-sm"
					>
				</div>
			</div>

			<div class="row  mt-1">
				<div class="col-5">
					<p class="">
						Выберите откуда брать данные
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="newGauge.activity_id"
						class="form-control form-control-sm"
					>
						<option
							:key="-1"
							:value="-1"
						>
							Ячейка из сводной
						</option>
						<option
							v-for="(group_activity, key) in group_activities"
							:key="key"
							:value="group_activity.id"
						>
							{{ group_activity.name }}
						</option>
					</select>
				</div>
			</div>

			<div
				v-if="newGauge.activity_id == -1"
				class="row  mt-1"
			>
				<div class="col-5">
					<p class="">
						Выберите ячейку из аналитики
					</p>
				</div>
				<div class="col-7">
					<input
						v-model="newGauge.cell"
						type="text"
						class="form-control form-control-sm"
					>
				</div>
			</div>

			<div
				v-if="newGauge.activity_id != -1"
				class="row  mt-1"
			>
				<div class="col-5">
					<p class="">
						Какое значение
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="newGauge.value_type"
						class="form-control form-control-sm"
					>
						<option value="sum">
							Сумма выполненного
						</option>
						<option value="avg">
							Среднее значение
						</option>
					</select>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

const VGauge = () => import(/* webpackChunkName: "VGauge" */ 'vgauge')
export default {
	name: 'TopGauges',
	components:{
		VGauge,
	},
	props: {
		utility_items: {
			type: Array,
			default: null
		},
		editable: {
			type: Boolean,
		},
		wrapper_class: {
			type: String,
			default: ''
		},
		page: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			utility: [],
			skey: 1,
			newGauge: {
				activity_id: null,
				group_id: null,
				name: null,
				cell: '',
				value_type: 'sum'
			},
			showNewGaugeWindow: false,
			group_activities: [],
			colors: {
				'#ca0013': 7, // red
				'#F03E3E': 2, // red
				'#fd7e14': 4, // orange
				'#ffc107': 6, // orange light
				'#FFDD00': 3, // yellow
				'#42e467': 5, // green light,
				'#30B32D': 1, // green,
			},
			reverseColors: {
				'#30B32D': 1, // green,
				'#42e467': 5, // green light,
				'#FFDD00': 3, // yellow
				'#ffc107': 6, // orange light
				'#fd7e14': 4, // orange
				'#F03E3E': 2, // red
				'#ca0013': 7, // red
			},
		}
	},

	watch: {
		utility_items() {
			this.utility = this.utility_items;
			this.normalize();
			//vm.$forceUpdate()
		},
	},
	created() {
		this.utility = this.utility_items;
		this.normalize();
	},

	methods: {
		normalize() {
			if(this.page == 'top') {
				this.utility.forEach(group => {
					group.gauges.forEach(gauge => {
						if(group.gauges.length > 1) {
							gauge.height = '42px';
							gauge.width = '75px';
							if(gauge.options.staticLabels !== undefined) {
								gauge.options.staticLabels.font = '7px sans-serif';
							}
						}
						else {
							gauge.height = '90px';
							gauge.width = '150px';
							if(gauge.options.staticLabels !== undefined) {
								gauge.options.staticLabels.font = '11px sans-serif';
							}
						}
					});
				});
			}
			else {
				this.utility.forEach(group => {
					group.gauges.forEach(gauge => {
						gauge.height = '90px';
						gauge.width = '150px';
						if(gauge.options.staticLabels !== undefined) {
							gauge.options.staticLabels.font = '7px sans-serif';
						}
					});
				});
			}
		},

		save(group, gauge) {
			if(!this.editable) return '';
			let points = JSON.parse(this.utility[group].gauges[gauge].sections)
			this.utility[group].gauges[gauge].options.angle = Number(this.utility[group].gauges[gauge].angle)
			this.utility[group].gauges[gauge].options.staticLabels.labels = points
			this.utility[group].gauges[gauge].options.staticZones = this.getStaticZones(points, this.utility[group].gauges[gauge].unit)
			this.utility[group].gauges[gauge].key++

			this.utility[group].gauges[gauge].editable = false

			this.saveDB(group, gauge)
		},

		delete_gauge(group, gauge) {
			if(!this.editable) return '';

			this.axios.post('/timetracking/top/delete_gauge', {
				gauge: this.utility[group].gauges[gauge]
			}).then(() => {
				this.$toast.success('Успешно удален!')
				this.utility[group].gauges.splice(gauge, 1);
			}).catch(error => {
				alert(error)
			});
		},

		saveDB(group, gauge_index) {
			this.axios.post('/timetracking/top/save_top_value', {
				gauge: this.utility[group].gauges[gauge_index]
			}).then(response => {
				if(response.data.code == 200) {
					this.$toast.success('Успешно сохранено!')

					let this_gauge = this.utility[group].gauges[gauge_index];

					this_gauge.value = response.data.value;
					this_gauge.options = response.data.options;
					if(this.utility[group].gauges[gauge_index].is_main == 1) {
						this.utility[group].gauges.splice(gauge_index, 1);
						this.utility[group].gauges.forEach(item =>  {
							item.is_main = 0;
						});
						this.utility[group].gauges.unshift(this_gauge);
					}

					this.skey++
				}
				else {
					this.$toast.error('Попробуйте нажать еще раз')
				}
			}).catch(error => {
				alert(error)
			});
		},

		edit(group, gauge) {
			if(!this.editable) return '';
			this.utility[group].gauges[gauge].editable = !this.utility[group].gauges[gauge].editable

			if(this.visible_gauge_group_index != null && this.visible_gauge_index != null) { // Close prev
				this.utility[this.visible_gauge_group_index].gauges[this.visible_gauge_index].editable = false
			}

			if(this.utility[group].gauges[gauge].editable) {
				this.visible_gauge_group_index = group;
				this.visible_gauge_index = gauge;
			}
			else {
				this.visible_gauge_group_index = null;
				this.visible_gauge_index = null;
			}
		},

		getStaticZones(points, unit) {
			let staticZones = [],
				first = 0,
				second = 1,
				colors = this.colors;

			if(unit == 'мин') {
				colors = this.reverseColors;
			}

			Object.keys(colors).forEach(function (key) {
				if(Number(colors[key]) + 1 <= points.length) {
					staticZones.push({
						strokeStyle: key,
						min: points[first],
						max: points[second]
					});
					first++;
					second++;
				}
			});

			return staticZones;
		},

		showAddWindow(group_id, group_index) {
			this.newGauge.group_id = group_id
			this.newGauge.group_index = group_index

			this.axios.post('/timetracking/top/get_activities', {
				group_id: group_id,
			}).then(response => {
				this.group_activities = response.data;
			}).catch(error => {
				alert(error)
			});

			this.showNewGaugeWindow = true
		},

		create_gauge() {
			this.axios.post('/timetracking/top/create_gauge', {
				group_id: this.newGauge.group_id,
				activity_id: this.newGauge.activity_id,
				name: this.newGauge.name,
				value_type: this.newGauge.value_type,
				cell: this.newGauge.cell,
			}).then(response => {
				this.utility[this.newGauge.group_index].gauges.push(response.data);

				this.newGauge = {
					activity_id: null,
					group_id: null,
					name: null,
					cell:'',
					value_type: 'sum'
				};

				this.skey++
				this.normalize();

				this.showNewGaugeWindow = false;
				this.$toast.success('Успешно сохранено!')
			}).catch(error => {
				alert(error)
			});
		}
	}
}
</script>

<style lang="scss">
.l-label {
	width: 39px;
	text-align: left;
}
.gauge-title {
	font-weight: bold;
	display: none;
	text-align: center;
	font-size: 20px;
}
.h-23 {
	height: 23px !important;
	padding: 0 !important;
}
.w-250 {
	width: 200px;
}
.w-full {
	width: 100%;
}

.gauge:hover .fa-cog {
	display: block;
}
.gauge {
	cursor: pointer;

	&:last-child {
		border-bottom: none;
	}
}
.br-1 {
	border-right: 1px solid #f3f3f3;
	border-bottom: 1px solid #f3f3f3;
}
.text-14 {
	font-size: 14px;
	line-height: 14px;
}
input.form-control.form-control-sm.wiwi {
	padding: 0 10px;
	margin-bottom: 4px;
	width: 100%;
}
.scale {
	transition: 0.3s ease all;
	transform-origin: bottom left;
}
.top .scale:hover {
	transform: scale(1.5);
	background: #ffffff;
	box-shadow: 0 0 15px 15px #efefef;

	// .edit-window {
	// 	transform: scale(0.66);
	// 	transform-origin: top left;
	// }
}

.scale-tl {
	transform-origin: top left;
}
.underline {
	text-decoration: underline;
}
.scale-tr {
	transform-origin: top right;
}
.scale-bl {
	transform-origin: bottom left;
}
.scale-br {
	transform-origin: bottom right;
}
.w-50 {
	width: 50%;
}
.g-title {
	font-size: 13px;
	line-height: 16px;
	font-weight: 600;
	// width: 160px;
}
.edit-window {
	width: 300px;
	position: absolute;
	background: aliceblue;
	padding: 15px;
	border: 1px solid #ddd;
	border-radius: 3px;
	z-index: 222222;
}
.custom-control {
	display: flex;
}

.TopGauges{
	&-gauges{}
	&-gauge{
		width: 50%;
		// &_single{
		// 	width: 100%;
		// }
	}
	&-input{
		flex: 1;
		width: auto;
		min-width: auto;
		margin-bottom: 10px !important;
	}
}
</style>
