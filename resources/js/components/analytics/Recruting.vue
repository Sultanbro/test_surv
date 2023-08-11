<template>
	<div class="recruting-analytics mb-4 AnalyticsRecruting">
		<div class="AnalyticsRecruting-content mb-4">
			<div class="d-flex justify-content-between abv">
				<p>Принятые сотрудники в этом месяце</p>
				<p class="text ml-2">
					{{ info.applied }} приняты / {{ info.applied_plan }} требуется
				</p>
			</div>
			<ProgressBar
				:progress="widthRemain + '%'"
				class="mt-4"
			/>
			<div class="AnalyticsRecruting-remainDdays mt-3">
				Осталось {{ info.remain_days }} дней
			</div>
			<!-- <div class="AnalyticsRecruting-progress">
				<div
					class="AnalyticsRecruting-progress-indicator"
					:style="'width: ' + widthRemain + '%'"
				/>
				<div class="AnalyticsRecruting-progress-text">
					Осталось {{ info.remain_days }} дней
				</div>
			</div> -->
			<div class="AnalyticsRecruting-days">
				<div class="AnalyticsRecruting-line AnalyticsRecruting-line1" />
				<div
					v-if="month == (new Date().getMonth() + 1)"
					class="AnalyticsRecruting-line AnalyticsRecruting-line2"
					:style="'left: calc(' + widthRemain + '% - 1.95%);'"
				>
					{{ today }} <br>{{ months[month] }}
				</div>
				<div class="AnalyticsRecruting-line AnalyticsRecruting-line3">
					{{ maxdays[month] }} <br>{{ months[month] }}
				</div>
			</div>
		</div>

		<div
			v-if="isAnalyticsPage"
			class="row my-5"
		>
			<div class="col-md-8">
				<h3 class="mb-4 text-center">
					Основные показатели
				</h3>
				<div class="lboxes">
					<div class="lbox green  shadow">
						<p>
							<span>Работают</span>
							<i
								v-b-popover.hover.right.html="'Количество сотрудников на данный момент'"
								class="fa fa-info-circle"
								title="Работают"
							/>
						</p>
						<p>{{ info.working }}</p>
					</div>
					<div class="lbox yellow  shadow">
						<p>
							<span>Осталось принять</span>
							<i
								v-b-popover.hover.right.html="'Количество требуемых сотрудников на данный момент. <br> f: (Кол-во заказа - Кол-во принятых сотрудников)'"
								class="fa fa-info-circle"
								title="Осталось принять"
							/>
						</p>
						<p>{{ info.remain_apply }}</p>
					</div>
					<div class="lbox blue   shadow">
						<p>
							<span>Стажеры</span>
							<i
								v-b-popover.hover.right.html="'Количество стажеров присутствовавших на сегодняшнем обучении.<br> После отметки отсутствовавших руководителями, это число уменьшается и конкретизируется к концу рабочего дня'"
								class="fa fa-info-circle"
								title="Стажеры"
							/>
						</p>
						<p>{{ info.training }}</p>
					</div>
					<div class="lbox green  shadow">
						<p>
							<span>Осталось рабочих дней</span>
							<i
								v-b-popover.hover.right.html="'Все дни, кроме воскресенья'"
								class="fa fa-info-circle"
								title="Осталось рабочих дней"
							/>
						</p>
						<p>{{ info.remain_days }}</p>
					</div>
					<div class="lbox yellow  shadow">
						<p>
							<span>Уволены</span>
							<i
								v-b-popover.hover.right.html="'Уволены в этом месяце <b>по учету ставок</b> сотрудников.<br> Part time считается как 0,5'"
								class="fa fa-info-circle"
								title="Уволены"
							/>
						</p>
						<p>{{ info.fired }}</p>
					</div>
					<div class="lbox blue  shadow">
						<p>
							<span>Принято</span>
							<i
								v-b-popover.hover.right.html="'Принято в этом месяце <b>по учету ставок</b> сотрудников. <br> Part time считается как 0,5 <br><br> Нажмите, чтобы увидеть заказы на этот месяц'"
								class="fa fa-info-circle"
								title="Принято"
							/>
						</p>
						<p>{{ info.applied }}</p>
					</div>
				</div>
			</div>
			<!-- воронка -->
			<div class="col-md-4 static">
				<div>
					<h3 class="mb-4 text-center">
						Воронка соискателей
					</h3>
					<div id="funnel" />
				</div>
			</div>
		</div>

		<div
			v-if="orderVisible"
			class="border shadow p-3 mb-3"
		>
			<h3>Заказы на группы</h3>

			<div class="group" />

			<table class="table table-striped">
				<tr>
					<th>Название группы</th>
					<th>Требуется</th>
					<th>Факт</th>
				</tr>
				<tr
					v-for="(order, index) in orders"
					:key="index"
				>
					<td class="text-left t-name  bgz table-title">
						{{ order.group }}
					</td>
					<td class="text-left table-title">
						{{ order.required }}
					</td>
					<td class="text-left table-title">
						{{ order.fact }}
					</td>
				</tr>
			</table>
		</div>

		<div class="border shadow p-3 rounded">
			<div class="d-flex justify-content-between">
				<h3 class="mb-0">
					Результаты остальных сотрудников
				</h3>
				<JobtronButton @click="showPlans = !showPlans">
					{{ showPlans ? 'Скрыть' : 'Раскрыть' }}
				</JobtronButton>
			</div>


			<div
				v-show="showPlans"
				class="mt-5"
			>
				<div
					v-for="user in recruiters"
					:key="user.id"
					class="plan"
				>
					<div class="mb-2 d-flex justify-content-between">
						<p class="name">
							{{ user.name }}
						</p>
						<div class="d-flex">
							<div class="ind">
								<div class="circle blue" />
								<p>Исходящие</p>
							</div>
							<div class="ind">
								<div class="circle yellow" />
								<p>Сконвертировано</p>
							</div>
							<div class="ind">
								<div class="circle bluish" />
								<p>Принято на работу</p>
							</div>
						</div>
					</div>
					<div class="progress">
						<div
							v-b-popover.hover
							class="indicator main"
							:style="'width: ' + user.out.percent + '%'"
							title="Исходящие"
						>
							{{ user.out.percent }} %
						</div>
						<div class="text">
							{{ user.out.value }} звонков из {{ user.out.plan }} запланированных
						</div>
					</div>
					<div class="progress">
						<div
							v-b-popover.hover
							class="indicator yellow"
							title="Сконвертировано"
							:style="'width: ' + user.converted.percent + '%;'"
						>
							{{ user.converted.percent }} %
						</div>
						<div class="text">
							{{ user.converted.value }} сделок из {{ user.converted.plan }} запланированных
						</div>
					</div>
					<div class="progress">
						<div
							v-b-popover.hover
							class="indicator bluish"
							title="Принято на работу"
							:style="'width: ' + user.applied.percent + '%;'"
						>
							{{ user.applied.percent }} %
						</div>
						<div class="text">
							{{ user.applied.value }} сотрудников из {{ user.applied.plan }} запланированных
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import D3Funnel from 'd3-funnel';
import ProgressBar from '@ui/ProgressBar'
import JobtronButton from '@ui/Button'

export default {
	name: 'AnalyticsRecruting',
	components: {
		ProgressBar,
		JobtronButton,
	},
	props: {
		records: {
			type: Object,
			default: null
		},
		isAnalyticsPage: Boolean,
	},
	data: function () {
		return {
			items: [],
			orderVisible: false,
			showPlans: false,
			chartOptions: {
				options: {
					block: {
						dynamicHeight: true,
						minHeight: 60,
						fill: {
							type: 'gradient' // gradient
						},
						highlight: true
					},
					chart: {
						curve: {
							enabled: false
						},
						animate: 100,
						bottomPinch: 0
					},
					tooltip: {
						enabled : true,
						format: function(label) {
							if(label == 'Создано новых лидов за месяц') return 'Лиды с названиями: Удаленный, inhouse'
							if(label == 'Обработано') return 'Сконвертировано + Забраковано лидов'
							if(label == 'Сконвертировано') return 'Создано сделок на основе лидов'
							if(label == 'Стажируются') return 'Количество стажеров присутствовавших на сегодняшнем обучении'
							if(label == 'Приняты в BP') return 'Приняты сотрудниками'

						}
					},
					label: {
						fontFamily: 'Open Sans',
						fontSize: '12px',
						format: '{l}\n{f}'
					},
					events :{
						click: {
							block: function(/* data */) {

							}
						}
					}
				},
				data: [
					{  value: 10, backgroundColor: '#39dde0', label: 'Создано новых лидов за месяц', formatted: 'test hehe boy'},
					{  value: 10, backgroundColor: '#44d9e0', label: 'Обработано', formatted: 'test hehe boy'},
					{  value: 9, backgroundColor: '#5fd3ec', label: 'Сконвертировано',formatted: 'test hehe boy'},
					{  value: 5, backgroundColor: '#76b5ec', label: 'Стажируются',formatted: 'test hehe boy'},
					{  value: 1, backgroundColor: '#6f8edf', label: 'Приняты в BP',formatted: 'test hehe boy'},
				],
			},
			chart : null,
			plan: {
				hired: 35,
				trainees: 150
			},
			fact: {
				hired: 12,
				trainees: 32
			},
			recruiters: [
				{
					name: 'Кристина Еремеева',
					out: {
						value: 1452,
						percent: 56,
						plan: 56,
					},
					converted: {
						value: 120,
						percent: 9,
						plan: 56,
					}
				}
			],
			orders: [],
			months: {
				1: 'января',
				2: 'февраля',
				3: 'марта',
				4: 'апреля',
				5: 'мая',
				6: 'июня',
				7: 'июля',
				8: 'августа',
				9: 'сентября',
				10: 'октября',
				11: 'ноября',
				12: 'декабря',
			},
			maxdays: {
				1: 31,
				2: 28,
				3: 31,
				4: 30,
				5: 31,
				6: 30,
				7: 31,
				8: 31,
				9: 30,
				10: 31,
				11: 30,
				12: 31,
			},
			info: {
				created: 0,
				converted: 0,
				trainees: 0,
				applied: 0,
				remain_days: 0,
				remain_apply: 0,
				working: 0,
				training: 0,
				fired: 0,
			},
			month: 1,
			today: 1,
			workDays: 26,
		};
	},
	computed: {
		percentageHired: function () {
			return parseFloat(this.info.applied / this.info.applied_plan * 100).toFixed(0)
		},
		widthRemain: function () {
			return (parseFloat((Number(this.maxdays[this.month]) - Number(this.today)) / Number(this.maxdays[this.month]) * 100) - 100) * (-1);
		},
		applied_on: function () {
			let a = parseFloat(this.info.applied / this.info.applied_plan * 100).toFixed(1);
			return a == 'Infinity' ? 0 : a;
		}
	},
	watch: {
		// эта функция запускается при любом изменении данных
		records: {
			// the callback will be called immediately after the start of the observation
			immediate: true,
			handler () {
				this.recruiters = this.records.recruiters

				this.chartOptions.data[0]['value'] = this.records.info.created
				this.chartOptions.data[1]['value'] = this.records.info.processed
				this.chartOptions.data[2]['value'] = this.records.info.converted
				this.chartOptions.data[3]['value'] = this.records.info.trainees
				this.chartOptions.data[4]['value'] = this.records.info.applied

				this.info = this.records.info;
				this.orders = this.records.orders
				this.month = this.records.month
				this.today = this.records.today

				// this.chart = null;
				// this.chart = new D3Funnel('#funnel')
				// this.chart.draw(this.chartOptions.data, this.chartOptions.options);
			}
		},

	},

	created() {

		this.recruiters = this.records.recruiters

		this.chartOptions.data[0]['value'] = this.records.info.created
		this.chartOptions.data[1]['value'] = this.records.info.processed
		this.chartOptions.data[2]['value'] = this.records.info.converted
		this.chartOptions.data[3]['value'] = this.records.info.trainees
		this.chartOptions.data[4]['value'] = this.records.info.applied

		this.info = this.records.info;
		this.orders = this.records.orders
		this.month = this.records.month
		this.today = this.records.today

		if(this.isAnalyticsPage) {
			this.showPlans = false
		} else {
			this.showPlans = false
		}


	},

	mounted() {
		if(this.isAnalyticsPage) {
			this.chart = new D3Funnel('#funnel')
			this.chart.draw(this.chartOptions.data, this.chartOptions.options);
		}
	},
	methods: {
		reload() {
			this.recruiters = this.records.recruiters

			this.chartOptions.data[0]['value'] = this.records.info.created
			this.chartOptions.data[1]['value'] = this.records.info.converted
			this.chartOptions.data[2]['value'] = this.records.info.trainees
			this.chartOptions.data[3]['value'] = this.records.info.applied

			this.info = this.records.info;
			this.orders = this.records.orders
			this.month = this.records.month
			this.today = this.records.today

			if(this.isAnalyticsPage) {
				this.chart = new D3Funnel('#funnel')
				this.chart.draw(this.chartOptions.data, this.chartOptions.options);
			}

		},
	}
};
</script>

<style lang="scss">
.d3-funnel-tooltip{
	// position: fixed !important;
	// left: 0 !important;
	// bottom: 0 !important;
}
.AnalyticsRecruting{
	&-content{
    display: flex;
    flex-flow: column wrap;
    align-items: stretch;
    justify-content: center;

    padding: 15px;
    margin-bottom: 15px;

    position: relative;

    background: #F8FBFF;
    border-radius: 12px;
	}
	&-remainDdays{
		text-align: right;
		font-size: 1rem;
	}
	&-days{
		position: relative;
		height: 55px;
	}
	&-line{
    padding-top: 15px;
		position: absolute;
    top: 8px;
    text-align: center;
		&:before {
			content: '';
			display: block;
			width: 2px;
			height: 15px;

			position: absolute;
			top: 0px;

			background: #ccc;
		}
	}
	&-line1 {
		left: 0;
		&:before {
			left: 0;
		}
	}
	&-line2 {
		left: 82%;
		&:before {
			left: 50%;
		}
	}
	&-line3 {
		right: 0;
		&:before {
			right: 0;
		}
	}
}
</style>
<style lang="scss" scoped>
.row{
	display: flex;
	flex-flow: row nowrap;
	gap: 1rem;
	margin-left: 0;
	margin-right: 0;
}
.col-md-4,
.col-md-8{
	padding-left: 0;
	padding-right: 0;
	flex-shrink: 1 !important;
}
.border {
	border: 1px solid #fafafa;
}
// .shadow {
//     box-shadow: 0 3px 15px #d3d6da;
//     &.hover:hover {
//         box-shadow: 0 3px 15px #bbbdbe;
//         cursor: pointer
//     }
// }
.p-2 {
	padding: 15px;
}

.plan p.name {
	flex: 0 0 25%;
	font-weight: 400;
	color: #111;
	margin-top: 15px;
	font-size: 0.9rem;
}
.progress {
	flex: 0 0 75%;
	position: relative;
	border: 1px solid #fafafa;
	height: 1.8rem;
	font-size: 0.9rem;
	border-radius: 0;
}
.progress .indicator{
	background: #2dad4a;
	color: #fff;
	font-weight: 700;
	//background: repeating-linear-gradient(45deg, #008eff, #0b92fc 10px, #33a1f8 10px, #52adf4 20px);
	position: absolute;
	height: 100%;
	padding: 0 15px;
	width: 0;
	border-radius: 0;
	cursor: pointer;
	display: flex;
	align-items: center;
	max-width: 100%;
	min-width: 5px;
	&:hover {
		background: #41c7e4;
	}
	&.green {
		background: #8bc34a;
		&:hover {
			background: #99da4c;
		}
	}
	&.yellow {
		background: #5fd3ec;
		&:hover {
			background: #55d8f5;
		}
	}
	&.bluish {
		background: #045e92;
		&:hover {
			background: #055583;
		}
	}
}
.recruting-analytics h3 {
	font-size: 1.8rem;
	margin-bottom: 20px;
}
.recruting-analytics h3.mb-0 {
	margin-bottom: 0;
}
.lboxes {
	display: flex;
	justify-content: space-between;
	flex-wrap:wrap;
	gap: 30px;
	.lbox {
		align-items: unset;
	}
}
.lbox {
	flex: 0 0 48%;
	padding: 15px 0;
	flex-wrap: wrap;
	border-left-width: 0px !important;
	border-left-style: solid;
	background: #F8FBFF;
	padding: 15px;
	align-items: self-end;
	display: flex;
	flex-direction: column;
	justify-content: center;
	position: relative;
	border-radius: 12px;
	p:first-child {
		font-weight: 400;
		color: #000;
	}
	i.fa {
		color: #000000;
		cursor: pointer;
		display: inline-block;
		&:hover {
			color: #000;
		}
	}
	p:last-child {
		font-weight: 600;
		color: #000;
		font-size: 1.5rem;
		margin-bottom: 0;
	}
}
.lboxes2 {
	display: flex;
	flex-wrap: wrap;
	align-items: flex-start;
	justify-content: space-between;
}
.lboxes2 .lbox {
	flex: 0 0 47%;
}
@media(max-width: 768px) {
	.lbox {
		flex: 0 0 47%;
	}
	.lboxes2 .lbox {
		flex: 0 0 100%;
	}
}
.table td, .table th {
	padding: 0.5rem;
	border: 1px solid #dee2e6;
}
.ind {
	display: flex;
	align-items: center;
	margin-left: 15px;
	p {
		margin-bottom: 0;
		font-size: 0.8rem;
	}
	.circle {
		width: 6px;
		height: 6px;
		margin-right: 7px;
		border-radius: 6px;
		&.blue {background: #76b5ec;}
		&.bluish {background: #045e92;}
		&.green {background: #8bc34a;}
		&.yellow {background: #5fd3ec;}
	}
}
.plan .progress .indicator {
	min-width: max-content;
}
.progress .text {
	width: 100%;
	text-align: right;
	position: absolute;
	height: 100%;
	display: flex;
	justify-content: flex-end;
	padding-right: 10px;
	align-items: center;
}
.abv p {
	font-size: 1.2rem;
	font-weight: 600;
	color: #000;
}
.pointer {
	cursor: pointer;
	&:hover {

		filter: grayscale(1)
	}
}
#funnel {
	max-width: 335px;
	display: block;
	margin: 0 auto;
	height: 330px;
}
.static{
	position: static;
}
</style>
