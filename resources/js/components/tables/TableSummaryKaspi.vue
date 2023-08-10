<template>
	<div>
		<div class="d-flex">
			<input
				v-model="cell"
				type="text"
				class=" form-control form-control-sm mb-2 mr-2 rounded-none text-center"
				style="width: 57px;"
				disabled
			>
			<input
				v-model="shortcut"
				type="text"
				class="w-full form-control form-control-sm mb-2 rounded-none"
				disabled
			>
		</div>

		<b-table
			id="kaspisum"
			:key="'table-kaspi'"
			responsive
			striped
			class="text-nowrap text-right my-table summary-kaspi  mb-3 table-excel"
			:small="true"
			:bordered="true"
			:items="items"
			:fields="fields"
			primary-key="a"
		>
			<template #thead-top>
				<tr>
					<th
						class="grey b-table-sticky-column"
						style="min-width: 55px;border-right:2px solid #ccc !important"
					/>
					<th
						v-for="name in columnNames"
						:key="name"
						class="grey"
					>
						{{ name }}
					</th>
				</tr>
			</template>

			<template #cell()="data">
				<input
					v-if="data.index == 4 && data.field.is_date
						|| data.index == 5 && data.field.is_date
						|| data.index == 8 && data.field.is_date
						|| data.index == 13 && data.field.is_date
						|| data.index == 14 && data.field.is_date
						|| data.index == 15 && data.field.is_date
						|| data.index == 16 && data.field.is_date
						|| data.index == 17 && data.field.is_date
						|| data.index == 18 && data.field.is_date
						|| data.index == 20 && data.field.is_date
						|| data.index == 22 && data.field.is_date
						|| data.index == 23 && data.field.is_date
					"
					type="number"
					class="form-control cell-input "
					:class="data.item._cellVariants[data.field.key]"
					:value="data.value"
					@change="updateSettings($event,data)"
					@click="test(data)"
				>

				<div
					v-else
					:class="data.item._cellVariants[data.field.key]"
					@click="test(data)"
				>
					{{ data.value }}
				</div>
			</template>
		</b-table>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
// function send(message) {
// 	const TOKEN = '1286740490:AAGiR2ch8MqzfP3IVee3Q0Mw4gZu6-ZbnVE';
// 	const KAIR = '577504834';

// 	axios.get('https://api.telegram.org/bot' + TOKEN + '/sendMessage?chat_id=' + KAIR + '&text=' + message);
// }

const S0_TOTAL = 0 // первая пустая строка
const S1_IMPL = 1 // impl
const S2_PRCST = 2 // pr, cst
const S3_PRCSTLL = 3 // pr, cstll
const S4_MINUTE_1_5 = 4 // минуты сегмента 1-5
const S5_OTHER = 5 // минуты остальных сегментов
const S6_FACT = 6 // факт
const S7_PLAN = 7 // план, розовая строка
const S8_LIDS = 8 // поступления лидов
const S9_PLAN_OPERATORS = 9 // план операторов , розовая строка
const S10_FACT_OPERATORS = 10 // факт операторов
const S11_AVR_MINUTE_OPERATOR = 11 // в среднем минут на оператора
const S12_EMPTY = 12 // пустая строка
const S13_1_5_DAY = 13 // 1-5 ежедневный
const S14_RATING = 14 // рейтинг 1-5 за 5 дней, розовая строка
const S15_OVD = 15 // ОВД
const S16_RED_OVD = 16 // RED ОВД
const S17_NOTICE_CREDIT = 17 // напоминания кредит
const S18_RED_NOTICE = 18 // RED напоминаний
const S19_TWO_GROUP = 19 // 2 группа
const S20_CLAIMS = 20 // жалобы
const S21_CONSTANTS = 21 // консты
const S22_CONNECTION_LACK_REMOTE = 22 // отсутствие связи remote
const S23_CONNECTION_LACK_INHOUSE = 23 // отсутствие связи inhouse

// const VxAL300 = Number(270); // @TODO Нужно брать из таблицы минут в день Активити
// const VxAL500 = Number(500);
// const VxAL1000 = Number(1000);


export default {
	name: 'TableSummaryKaspi',
	props: {
		data: {},
		month: Object,
		totals: {},
		currentYear: Number,
		currentGroup: Number,
	},
	data: function () {
		return {
			data_t_constants: {},
			items: [],
			fields: [],
			workDays: 26,
			columnNames: [],
			hasPremission: false,
			cell: '',
			shortcut: '',
			highlighted: [],
		};
	},
	watch: {
		// эта функция запускается при любом изменении данных
		data: {
			// the callback will be called immediately after the start of the observation
			immediate: true,
			handler () {
				this.setFields()
				this.loadItems()

				// let post_data = {
				// 	date: this.$moment(`${this.month.currentMonth} ${this.currentYear}`, 'MMMM YYYY').format('YYYY-MM-DD'),
				// 	group_id: this.currentGroup,
				// 	settings: this.items
				// };

				if(this.items != null && this.items != undefined) {
					// this.saveTable(post_data)
				}

			}
		},
	},

	mounted() {
		this.setFields()
		this.setConstants()
		this.hasPremission = true
	},

	methods: {
		test(data) {
			//this.items[index]._cellVariants[key] = 'current highlight'
			this.setShortcut(data)
		},

		setShortcut(data) {
			if(data.field.key == 'rownumber') return null

			this.shortcut = data.value

			let formulas = {
				0: { // Первый ряд
					'headers': '=B5 - (C4 + C5)',
					'pr': '=(B12 * 270 * 26) / 1000',
					'plan': '=(B9 * 30) / 1000',
				},
				1: { // Impl
					'pr': '=(B5 / B2) * 100',
					'plan': '=(B5 / С2) * 100',
					'1': '=(E5 / E14) * 100',
					'2': '=(F5 / F14) * 100',
					'3': '=(G5 / G14) * 100',
					'4': '=(H5 / H14) * 100',
					'5': '=(I5 / I14) * 100',
					'6': '=(J5 / J14) * 100',
					'7': '=(K5 / K14) * 100',
					'8': '=(L5 / L14) * 100',
					'9': '=(M5 / M14) * 100',
					'10': '=(N5 / N14) * 100',
					'11': '=(O5 / O14) * 100',
					'12': '=(P5 / P14) * 100',
					'13': '=(Q5 / Q14) * 100',
					'14': '=(R5 / R14) * 100',
					'15': '=(S5 / S14) * 100',
					'16': '=(T5 / T14) * 100',
					'17': '=(U5 / U14) * 100',
					'18': '=(V5 / V14) * 100',
					'19': '=(W5 / W14) * 100',
					'20': '=(X5 / X14) * 100',
					'21': '=(Y5 / Y14) * 100',
					'22': '=(Z5 / Z14) * 100',
					'23': '=(AA5 / AA14) * 100',
					'24': '=(AB5 / AB14) * 100',
					'25': '=(AC5 / AC14) * 100',
					'26': '=(AD5 / AD14) * 100',
					'27': '=(AE5 / AE14) * 100',
					'28': '=(AF5 / AF14) * 100',
					'29': '=(AG5 / AG14) * 100',
					'30': '=(AH5 / AH14) * 100',
					'31': '=(AI5 / AI14) * 100',
				},
				2: { // Pr, cst
					'plan': '=(B12 * 500) / 1000',
				},
				3: { // Pr, cstll
					'pr': '=СУММ(E5:AI5)',
					'plan': '=(B12*3076)/1000',
					'avg': '=СРЗНАЧ(E5:AI5)',
					'1': '=(E8  * 30) / 1000',
					'2': '=(F8  * 30) / 1000',
					'3': '=(G8  * 30) / 1000',
					'4': '=(H8  * 30) / 1000',
					'5': '=(I8  * 30) / 1000',
					'6': '=(J8  * 30) / 1000',
					'7': '=(K8  * 30) / 1000',
					'8': '=(L8  * 30) / 1000',
					'9': '=(M8  * 30) / 1000',
					'10': '=(N8  * 30) / 1000',
					'11': '=(O8  * 30) / 1000',
					'12': '=(P8  * 30) / 1000',
					'13': '=(Q8  * 30) / 1000',
					'14': '=(R8  * 30) / 1000',
					'15': '=(S8  * 30) / 1000',
					'16': '=(T8  * 30) / 1000',
					'17': '=(U8  * 30) / 1000',
					'18': '=(V8  * 30) / 1000',
					'19': '=(W8  * 30) / 1000',
					'20': '=(X8  * 30) / 1000',
					'21': '=(Y8  * 30) / 1000',
					'22': '=(Z8  * 30) / 1000',
					'23': '=(AA8  * 30) / 1000',
					'24': '=(AB8  * 30) / 1000',
					'25': '=(AC8  * 30) / 1000',
					'26': '=(AD8  * 30) / 1000',
					'27': '=(AE8  * 30) / 1000',
					'28': '=(AF8  * 30) / 1000',
					'29': '=(AG8  * 30) / 1000',
					'30': '=(AH8  * 30) / 1000',
					'31': '=(AI8  * 30) / 1000',
				},
				4: {
					'pr': '=СУММ(E7:AI7)',
					'avg': '=СРЗНАЧ(E7:AI7)',
				},
				5: {
					'pr': '=СУММ(E8:AI8)',
					'plan': '=СРЗНАЧ(E8:AI8)',
				},
				6: {
					'pr': '=СУММ(E9:AI9)',
					'plan': '=СРЗНАЧ(E9:AI9)',
					'1': '=(E6 + E7)',
					'2': '=(F6 + F7)',
					'3': '=(G6 + G7)',
					'4': '=(H6 + H7)',
					'5': '=(I6 + I7)',
					'6': '=(J6 + J7)',
					'7': '=(K6 + K7)',
					'8': '=(L6 + L7)',
					'9': '=(M6 + M7)',
					'10': '=(N6 + N7)',
					'11': '=(O6 + O7)',
					'12': '=(P6 + P7)',
					'13': '=(Q6 + Q7)',
					'14': '=(R6 + R7)',
					'15': '=(S6 + S7)',
					'16': '=(T6 + T7)',
					'17': '=(U6 + U7)',
					'18': '=(V6 + V7)',
					'19': '=(W6 + W7)',
					'20': '=(X6 + X7)',
					'21': '=(Y6 + Y7)',
					'22': '=(Z6 + Z7',
					'23': '=(AA6 + AA7)',
					'24': '=(AB6 + AB7)',
					'25': '=(AC6 + AC7)',
					'26': '=(AD6 + AD7)',
					'27': '=(AE6 + AE7)',
					'28': '=(AF6 + AF7)',
					'29': '=(AG6 + AG7)',
					'30': '=(AH6 + AH7)',
					'31': '=(AI6 + AI7)',
				},
				7: { // plan
					'pr': '=СУММ(E10:AI10)',
					'plan': '=СРЗНАЧ(E10:AI10)',
					'1': '=(E10 * 1.5)',
					'2': '=(F10 * 1.5)',
					'3': '=(G10 * 1.5)',
					'4': '=(H10 * 1.5)',
					'5': '=(I10 * 1.5)',
					'6': '=(J10 * 1.5)',
					'7': '=(K10 * 1.5)',
					'8': '=(L10 * 1.5)',
					'9': '=(M10 * 1.5)',
					'10': '=(N10 * 1.5)',
					'11': '=(O10 * 1.5)',
					'12': '=(P10 * 1.5)',
					'13': '=(Q10 * 1.5)',
					'14': '=(R10 * 1.5)',
					'15': '=(S10 * 1.5)',
					'16': '=(T10 * 1.5)',
					'17': '=(U10 * 1.5)',
					'18': '=(V10 * 1.5)',
					'19': '=(W10 * 1.5)',
					'20': '=(X10 * 1.5)',
					'21': '=(Y10 * 1.5)',
					'22': '=(Z10 * 1.5)',
					'23': '=(AA10 * 1.5)',
					'24': '=(AB10 * 1.5)',
					'25': '=(AC10 * 1.5)',
					'26': '=(AD10 * 1.5)',
					'27': '=(AE10 * 1.5)',
					'28': '=(AF10 * 1.5)',
					'29': '=(AG10 * 1.5)',
					'30': '=(AH10 * 1.5)',
					'31': '=(AI10 * 1.5)',
				},
				8: {
					'pr': '=СУММ(E11:AI11)',
					'plan': '=СРЗНАЧ(E11:AI11)',
				},
				9: { // plan оператооров
					'pr': '=СУММ(E12:AI12)',
					'plan': '=СРЗНАЧ(E12:AI12)',
					'1': '=(E9 / 270)',
					'2': '=(F9 / 270)',
					'3': '=(G9 / 270)',
					'4': '=(H9 / 270)',
					'5': '=(I9 / 270)',
					'6': '=(J9 / 270)',
					'7': '=(K9 / 270)',
					'8': '=(L9 / 270)',
					'9': '=(M9 / 270)',
					'10': '=(N9 / 270)',
					'11': '=(O9 / 270)',
					'12': '=(P9 / 270)',
					'13': '=(Q9 / 270)',
					'14': '=(R9 / 270)',
					'15': '=(S9 / 270)',
					'16': '=(T9 / 270)',
					'17': '=(U9 / 270)',
					'18': '=(V9 / 270)',
					'19': '=(W9 / 270)',
					'20': '=(X9 / 270)',
					'21': '=(Y9 / 270)',
					'22': '=(Z9 / 270)',
					'23': '=(AA9 / 270)',
					'24': '=(AB9 / 270)',
					'25': '=(AC9 / 270)',
					'26': '=(AD9 / 270)',
					'27': '=(AE9 / 270)',
					'28': '=(AF9 / 270)',
					'29': '=(AG9 / 270)',
					'30': '=(AH9 / 270)',
					'31': '=(AI9 / 270)',
				},
				10: { // факт оператооров
					'pr': '=СУММ(E13:AI13)',
					'plan': '=СРЗНАЧ(E13:AI13)',
				},
				12: {
					'1': '=(E12 * 270 * 30) / 1000',
					'2': '=(F12 * 270 * 30) / 1000',
					'3': '=(G12 * 270 * 30) / 1000',
					'4': '=(H12 * 270 * 30) / 1000',
					'5': '=(I12 * 270 * 30) / 1000',
					'6': '=(J12 * 270 * 30) / 1000',
					'7': '=(K12 * 270 * 30) / 1000',
					'8': '=(L12 * 270 * 30) / 1000',
					'9': '=(M12 * 270 * 30) / 1000',
					'10': '=(N12 * 270 * 30) / 1000',
					'11': '=(O12 * 270 * 30) / 1000',
					'12': '=(P12 * 270 * 30) / 1000',
					'13': '=(Q12 * 270 * 30) / 1000',
					'14': '=(R12 * 270 * 30) / 1000',
					'15': '=(S12 * 270 * 30) / 1000',
					'16': '=(T12 * 270 * 30) / 1000',
					'17': '=(U12 * 270 * 30) / 1000',
					'18': '=(V12 * 270 * 30) / 1000',
					'19': '=(W12 * 270 * 30) / 1000',
					'20': '=(X12 * 270 * 30) / 1000',
					'21': '=(Y12 * 270 * 30) / 1000',
					'22': '=(Z12 * 270 * 30) / 1000',
					'23': '=(AA12 * 270 * 30) / 1000',
					'24': '=(AB12 * 270 * 30) / 1000',
					'25': '=(AC12 * 270 * 30) / 1000',
					'26': '=(AD12 * 270 * 30) / 1000',
					'27': '=(AE12 * 270 * 30) / 1000',
					'28': '=(AF12 * 270 * 30) / 1000',
					'29': '=(AG12 * 270 * 30) / 1000',
					'30': '=(AH12 * 270 * 30) / 1000',
					'31': '=(AI12 * 270 * 30) / 1000',
				},
				21: { // констатныты
					'pr': 'Какая-то константа, 1000 по умолчанию',
					'plan': 'Рабочие дни, 26 по умолчанию',
					'avg': 'Количество минут разговора, 270 по умолчанию',
					'1': 'Какая-то константа, 500 по умолчанию',
					'2': 'Какая-то константа, 3076 по умолчанию',
				},

			}

			let formula_highlights = { // Какие поля выделять, если есть формула
				0: { // Первый ряд
					'headers': [{index: 3, key: 'pr'}, {index: 2, key: 'plan'}, {index: 3, key: 'plan'}],
					'pr': [{index: 10, key: 'pr'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'plan': [{index: 7, key: 'pr'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}]
				},
				1: { // Impl
					'pr': [{index: 3, key: 'pr'}, {index: 0, key: 'pr'}],
					'plan': [{index: 3, key: 'pr'}, {index: 0, key: 'plan'}],
					'1': [{index: 3, key: '1'}, {index: 12, key: '1'}],
					'2': [{index: 3, key: '2'}, {index: 12, key: '2'}],
					'3': [{index: 3, key: '3'}, {index: 12, key: '3'}],
					'4': [{index: 3, key: '4'}, {index: 12, key: '4'}],
					'5': [{index: 3, key: '5'}, {index: 12, key: '5'}],
					'6': [{index: 3, key: '6'}, {index: 12, key: '6'}],
					'7': [{index: 3, key: '7'}, {index: 12, key: '7'}],
					'8': [{index: 3, key: '8'}, {index: 12, key: '8'}],
					'9': [{index: 3, key: '9'}, {index: 12, key: '9'}],
					'10': [{index: 3, key: '10'}, {index: 12, key: '10'}],
					'11': [{index: 3, key: '11'}, {index: 12, key: '11'}],
					'12': [{index: 3, key: '12'}, {index: 12, key: '12'}],
					'13': [{index: 3, key: '13'}, {index: 12, key: '13'}],
					'14': [{index: 3, key: '14'}, {index: 12, key: '14'}],
					'15': [{index: 3, key: '15'}, {index: 12, key: '15'}],
					'16': [{index: 3, key: '16'}, {index: 12, key: '16'}],
					'17': [{index: 3, key: '17'}, {index: 12, key: '17'}],
					'18': [{index: 3, key: '18'}, {index: 12, key: '18'}],
					'19': [{index: 3, key: '19'}, {index: 12, key: '19'}],
					'20': [{index: 3, key: '20'}, {index: 12, key: '20'}],
					'21': [{index: 3, key: '21'}, {index: 12, key: '21'}],
					'22': [{index: 3, key: '22'}, {index: 12, key: '22'}],
					'23': [{index: 3, key: '23'}, {index: 12, key: '23'}],
					'24': [{index: 3, key: '24'}, {index: 12, key: '24'}],
					'25': [{index: 3, key: '25'}, {index: 12, key: '25'}],
					'26': [{index: 3, key: '26'}, {index: 12, key: '26'}],
					'27': [{index: 3, key: '27'}, {index: 12, key: '27'}],
					'28': [{index: 3, key: '28'}, {index: 12, key: '28'}],
					'29': [{index: 3, key: '29'}, {index: 12, key: '29'}],
					'30': [{index: 3, key: '30'}, {index: 12, key: '30'}],
					'31': [{index: 3, key: '31'}, {index: 12, key: '31'}],
				},
				2: { // Pr, cst
					'plan': [{index: 10, key: 'pr'},{index: 21, key: '1'},{index: 21, key: 'pr'}],
				},
				3: { // Pr, cstll
					'pr': [{index: 3, key: '1'},  {index: 3, key: '5'}, {index: 3, key: '10'},  {index: 3, key: '20'}, {index: 3, key: '25'}, {index: 3, key: '30'}],
					'plan': [{index: 10, key: 'pr'},{index: 21, key: 'pr'},{index: 21, key: '2'}],
					'avg': [{index: 3, key: '1'},  {index: 3, key: '5'}, {index: 3, key: '10'},  {index: 3, key: '20'}, {index: 3, key: '25'}, {index: 3, key: '30'}],
					'1': [{index: 6, key: '1'}],
					'2': [{index: 6, key: '2'}],
					'3': [{index: 6, key: '3'}],
					'4': [{index: 6, key: '4'}],
					'5': [{index: 6, key: '5'}],
					'6': [{index: 6, key: '6'}],
					'7': [{index: 6, key: '7'}],
					'8': [{index: 6, key: '8'}],
					'9': [{index: 6, key: '9'}],
					'10': [{index: 6, key: '10'}],
					'11': [{index: 6, key: '11'}],
					'12': [{index: 6, key: '12'}],
					'13': [{index: 6, key: '13'}],
					'14': [{index: 6, key: '14'}],
					'15': [{index: 6, key: '15'}],
					'16': [{index: 6, key: '16'}],
					'17': [{index: 6, key: '17'}],
					'18': [{index: 6, key: '18'}],
					'19': [{index: 6, key: '19'}],
					'20': [{index: 6, key: '20'}],
					'21': [{index: 6, key: '21'}],
					'22': [{index: 6, key: '22'}],
					'23': [{index: 6, key: '23'}],
					'24': [{index: 6, key: '24'}],
					'25': [{index: 6, key: '25'}],
					'26': [{index: 6, key: '26'}],
					'27': [{index: 6, key: '27'}],
					'28': [{index: 6, key: '28'}],
					'29': [{index: 6, key: '29'}],
					'30': [{index: 6, key: '30'}],
					'31': [{index: 6, key: '31'}],
				},
				4: {
					'pr': [{index: 4, key: '1'},  {index: 4, key: '5'}, {index: 4, key: '10'},  {index: 4, key: '20'}, {index: 4, key: '25'}, {index: 4, key: '30'}],
					'avg': [{index: 4, key: '1'},  {index: 4, key: '5'}, {index: 4, key: '10'},  {index: 4, key: '20'}, {index: 4, key: '25'}, {index: 4, key: '30'}],
				},
				5: {
					'pr': [{index: 5, key: '1'},  {index: 5, key: '5'}, {index: 5, key: '10'},  {index: 5, key: '20'}, {index: 5, key: '25'}, {index: 5, key: '30'}],
					'plan': [{index: 5, key: '1'},  {index: 5, key: '5'}, {index: 5, key: '10'},  {index: 5, key: '20'}, {index: 5, key: '25'}, {index: 5, key: '30'}],
				},
				6: {
					'pr': [{index: 6, key: '1'},  {index: 6, key: '5'}, {index: 6, key: '10'},  {index: 6, key: '20'}, {index: 6, key: '25'}, {index: 6, key: '30'}],
					'plan': [{index: 6, key: '1'},  {index: 6, key: '5'}, {index: 6, key: '10'},  {index: 6, key: '20'}, {index: 6, key: '25'}, {index: 6, key: '30'}],
					'1': [{index: 4, key: '1'}, {index: 5, key: '1'}],
					'2': [{index: 4, key: '2'}, {index: 5, key: '2'}],
					'3': [{index: 4, key: '3'}, {index: 5, key: '3'}],
					'4': [{index: 4, key: '4'}, {index: 5, key: '4'}],
					'5': [{index: 4, key: '5'}, {index: 5, key: '5'}],
					'6': [{index: 4, key: '6'}, {index: 5, key: '6'}],
					'7': [{index: 4, key: '7'}, {index: 5, key: '7'}],
					'8': [{index: 4, key: '8'}, {index: 5, key: '8'}],
					'9': [{index: 4, key: '9'}, {index: 5, key: '9'}],
					'10': [{index: 4, key: '10'}, {index: 5, key: '10'}],
					'11': [{index: 4, key: '11'}, {index: 5, key: '11'}],
					'12': [{index: 4, key: '12'}, {index: 5, key: '12'}],
					'13': [{index: 4, key: '13'}, {index: 5, key: '13'}],
					'14': [{index: 4, key: '14'}, {index: 5, key: '14'}],
					'15': [{index: 4, key: '15'}, {index: 5, key: '15'}],
					'16': [{index: 4, key: '16'}, {index: 5, key: '16'}],
					'17': [{index: 4, key: '17'}, {index: 5, key: '17'}],
					'18': [{index: 4, key: '18'}, {index: 5, key: '18'}],
					'19': [{index: 4, key: '19'}, {index: 5, key: '19'}],
					'20': [{index: 4, key: '20'}, {index: 5, key: '20'}],
					'21': [{index: 4, key: '21'}, {index: 5, key: '21'}],
					'22': [{index: 4, key: '22'}, {index: 5, key: '22'}],
					'23': [{index: 4, key: '23'}, {index: 5, key: '23'}],
					'24': [{index: 4, key: '24'}, {index: 5, key: '24'}],
					'25': [{index: 4, key: '25'}, {index: 5, key: '25'}],
					'26': [{index: 4, key: '26'}, {index: 5, key: '26'}],
					'27': [{index: 4, key: '27'}, {index: 5, key: '27'}],
					'28': [{index: 4, key: '28'}, {index: 5, key: '28'}],
					'29': [{index: 4, key: '29'}, {index: 5, key: '29'}],
					'30': [{index: 4, key: '30'}, {index: 5, key: '30'}],
					'31': [{index: 4, key: '31'}, {index: 5, key: '31'}],
				},
				7: {
					'pr': [{index: 7, key: '1'},  {index: 7, key: '5'}, {index: 7, key: '10'},  {index: 7, key: '20'}, {index: 7, key: '25'}, {index: 7, key: '30'}],
					'plan': [{index: 7, key: '1'},  {index: 7, key: '5'}, {index: 7, key: '10'},  {index: 7, key: '20'}, {index: 7, key: '25'}, {index: 7, key: '30'}],
					'1': [{index: 8, key: '1'}],
					'2': [{index: 8, key: '2'}],
					'3': [{index: 8, key: '3'}],
					'4': [{index: 8, key: '4'}],
					'5': [{index: 8, key: '5'}],
					'6': [{index: 8, key: '6'}],
					'7': [{index: 8, key: '7'}],
					'8': [{index: 8, key: '8'}],
					'9': [{index: 8, key: '9'}],
					'10': [{index: 8, key: '10'}],
					'11': [{index: 8, key: '11'}],
					'12': [{index: 8, key: '12'}],
					'13': [{index: 8, key: '13'}],
					'14': [{index: 8, key: '14'}],
					'15': [{index: 8, key: '15'}],
					'16': [{index: 8, key: '16'}],
					'17': [{index: 8, key: '17'}],
					'18': [{index: 8, key: '18'}],
					'19': [{index: 8, key: '19'}],
					'20': [{index: 8, key: '20'}],
					'21': [{index: 8, key: '21'}],
					'22': [{index: 8, key: '22'}],
					'23': [{index: 8, key: '23'}],
					'24': [{index: 8, key: '24'}],
					'25': [{index: 8, key: '25'}],
					'26': [{index: 8, key: '26'}],
					'27': [{index: 8, key: '27'}],
					'28': [{index: 8, key: '28'}],
					'29': [{index: 8, key: '29'}],
					'30': [{index: 8, key: '30'}],
					'31': [{index: 8, key: '31'}],
				},
				8: {
					'pr': [{index: 8, key: '1'},  {index: 8, key: '5'}, {index: 8, key: '10'},  {index: 8, key: '20'}, {index: 8, key: '25'}, {index: 8, key: '30'}],
					'plan': [{index: 8, key: '1'},  {index: 8, key: '5'}, {index: 8, key: '10'},  {index: 8, key: '20'}, {index: 8, key: '25'}, {index: 8, key: '30'}],
				},
				9: { // plan оператооров
					'pr': [{index: 9, key: '1'},  {index: 9, key: '5'}, {index: 9, key: '10'},  {index: 9, key: '20'}, {index: 9, key: '25'}, {index: 9, key: '30'}],
					'plan': [{index: 9, key: '1'},  {index: 9, key: '5'}, {index: 9, key: '10'},  {index: 9, key: '20'}, {index: 9, key: '25'}, {index: 9, key: '30'}],
					'1': [{index: 7, key: '1'}],
					'2': [{index: 7, key: '2'}],
					'3': [{index: 7, key: '3'}],
					'4': [{index: 7, key: '4'}],
					'5': [{index: 7, key: '5'}],
					'6': [{index: 7, key: '6'}],
					'7': [{index: 7, key: '7'}],
					'8': [{index: 7, key: '8'}],
					'9': [{index: 7, key: '9'}],
					'10': [{index: 7, key: '10'}],
					'11': [{index: 7, key: '11'}],
					'12': [{index: 7, key: '12'}],
					'13': [{index: 7, key: '13'}],
					'14': [{index: 7, key: '14'}],
					'15': [{index: 7, key: '15'}],
					'16': [{index: 7, key: '16'}],
					'17': [{index: 7, key: '17'}],
					'18': [{index: 7, key: '18'}],
					'19': [{index: 7, key: '19'}],
					'20': [{index: 7, key: '20'}],
					'21': [{index: 7, key: '21'}],
					'22': [{index: 7, key: '22'}],
					'23': [{index: 7, key: '23'}],
					'24': [{index: 7, key: '24'}],
					'25': [{index: 7, key: '25'}],
					'26': [{index: 7, key: '26'}],
					'27': [{index: 7, key: '27'}],
					'28': [{index: 7, key: '28'}],
					'29': [{index: 7, key: '29'}],
					'30': [{index: 7, key: '30'}],
					'31': [{index: 7, key: '31'}],
				},
				10: { // факт оператооров
					'pr': [{index: 10, key: '1'},  {index: 10, key: '5'}, {index: 10, key: '10'},  {index: 10, key: '20'}, {index: 10, key: '25'}, {index: 10, key: '30'}],
					'plan': [{index: 10, key: '1'},  {index: 10, key: '5'}, {index: 10, key: '10'},  {index: 10, key: '20'}, {index: 10, key: '25'}, {index: 10, key: '30'}],
				},
				12: {
					'1': [{index: 10, key: '1'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'2': [{index: 10, key: '2'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'3': [{index: 10, key: '3'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'4': [{index: 10, key: '4'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'5': [{index: 10, key: '5'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'6': [{index: 10, key: '6'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'7': [{index: 10, key: '7'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'8': [{index: 10, key: '8'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'9': [{index: 10, key: '9'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'10': [{index: 10, key: '10'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'11': [{index: 10, key: '11'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'12': [{index: 10, key: '12'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'13': [{index: 10, key: '13'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'14': [{index: 10, key: '14'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'15': [{index: 10, key: '15'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'16': [{index: 10, key: '16'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'17': [{index: 10, key: '17'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'18': [{index: 10, key: '18'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'19': [{index: 10, key: '19'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'20': [{index: 10, key: '20'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'21': [{index: 10, key: '21'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'22': [{index: 10, key: '22'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'23': [{index: 10, key: '23'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'24': [{index: 10, key: '24'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'25': [{index: 10, key: '25'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'26': [{index: 10, key: '26'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'27': [{index: 10, key: '27'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'28': [{index: 10, key: '28'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'29': [{index: 10, key: '29'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'30': [{index: 10, key: '30'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
					'31': [{index: 10, key: '31'},{index: 21, key: 'avg'},{index: 21, key: 'plan'},{index: 21, key: 'pr'}],
				},
			}

			this.highlighted.forEach(el => { // если был выделен, убираем выделяющий класс
				if(this.items[el.index] !== undefined && this.items[el.index]._cellVariants !== undefined && this.items[el.index]._cellVariants[el.key] !== undefined) {
					this.items[el.index]._cellVariants[el.key] = this.items[el.index]._cellVariants[el.key].replace('high', '')
				}
			});

			this.highlighted = []; // чистим ранее выделенных

			if(formulas[data.index] !== undefined && formulas[data.index][data.field.key] !== undefined) this.shortcut = formulas[data.index][data.field.key]


			let highlighted_one = []; // собираем новый массив

			if(formula_highlights[data.index] !== undefined && formula_highlights[data.index][data.field.key] !== undefined) {
				formula_highlights[data.index][data.field.key].forEach(el => { // добавляем ячейкам классы для выделения
					if(this.items[el.index]._cellVariants[el.key] !== undefined) {
						this.items[el.index]._cellVariants[el.key] = this.items[el.index]._cellVariants[el.key].replace(' high', '')
						this.items[el.index]._cellVariants[el.key] += ' high';
					} else {
						this.items[el.index]._cellVariants[el.key] = ' high';
					}

					highlighted_one.push({
						index: el.index,
						key: el.key
					});
				});
			}

			this.highlighted = highlighted_one

			this.setCellName(data)

		},

		setCellName(data) {
			let combs = {
				'headers': 'A',
				'pr': 'B',
				'plan': 'C',
				'avg': 'D',
				'1': 'D',
				'2': 'E',
				'3': 'F',
				'4': 'H',
				'5': 'I',
				'6': 'J',
				'7': 'K',
				'8': 'L',
				'9': 'M',
				'10': 'N',
				'11': 'O',
				'12': 'P',
				'13': 'Q',
				'14': 'R',
				'15': 'S',
				'16': 'T',
				'17': 'U',
				'18': 'V',
				'19': 'W',
				'20': 'X',
				'21': 'Y',
				'22': 'Z',
				'23': 'AA',
				'24': 'AB',
				'25': 'AC',
				'26': 'AD',
				'27': 'AE',
				'28': 'AF',
				'29': 'AG',
				'30': 'AH',
				'31': 'AI',
			}

			this.cell = combs[data.field.key] + data.item.rownumber
		},

		setConstants() {
			this.data_t_constants = {
				title_exp_cell: '117 + s',
				title_const_val_1000: 1007,
				title_const_val_500: 507,
				title_const_val_300: 307,
				title_const_val_27: 29,
			}
		},

		setFields() {
			let fields = [];

			fields = [
				{
					key: 'rownumber',
					label: '1',
					variant: 'title',
					class: 'text-center rownumber b-table-sticky-column'
				},
				{
					key: 'headers',
					label: 'KASPI',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'pr',
					label: 'pr',
				},
				{
					key: 'plan',
					label: 'cst',
				},
				{
					key: 'avg',
					label: 'Сред.',
				}
			];

			let days = this.month.daysInMonth;

			// let days = 8;

			let now = new Date()
			let year = now.getFullYear()
			for (let i = 1; i <= days; i++) {
				let dayName = this.$moment(`${i} ${this.month.currentMonth} ${year}`, 'D MMMM YYYY').locale('en').format('ddd')
				fields.push({
					key: `${i}`,
					label: `${i}`,
					class: ` day  ${dayName}`,
					is_date: true
				});
			}
			this.fields = fields;


			this.columnNames = [
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z',
				'AA',
				'AB',
				'AC',
				'AD',
				'AE',
				'AF',
			];

			if(days > 28) this.columnNames.push('AG')
			if(days > 29) this.columnNames.push('AH')
			if(days > 30) this.columnNames.push('AI')
		},

		toFloat(number) {
			return Number(number).toFixed(1);
		},

		async loadItems() {

			let items = [];

			items.push({headers: ' ',rownumber: 2});
			items.push({headers: 'Impl',rownumber: 3});
			items.push({headers: 'Pr, cst',rownumber: 4});
			items.push({headers: 'Pr, cstll',rownumber: 5});

			if(this.currentGroup == 35) {
				items.push({headers: 'Минуты стажеров',rownumber: 6});
				items.push({headers: 'Минуты остальных сегментов',rownumber: 7});
			} else {
				items.push({headers: 'Минуты действующих',rownumber: 6});
				items.push({headers: 'Минуты стажеров',rownumber: 7});
			}



			items.push({headers: 'Факт',rownumber: 8});
			items.push({headers: 'Plan',rownumber: 9});
			items.push({headers: 'Поступление лидов',rownumber: 10});
			items.push({headers: 'plan ОПЕРАТОРОВ',rownumber: 11});
			items.push({headers: 'Факт операторов',rownumber: 12});
			items.push({headers: 'в среднем минут на оператора',rownumber: 13});
			items.push({headers: ' ',rownumber: 14});
			items.push({headers: '',rownumber: 15});
			items.push({headers: '3-6',rownumber: 16});
			items.push({headers: '7-30',rownumber: 17});
			items.push({headers: '31-60',rownumber: 18});
			items.push({headers: 'Нап',rownumber: 19});
			items.push({headers: '',rownumber: 20});
			items.push({headers: '',rownumber: 21});
			items.push({headers: 'Жалобы',rownumber: 22});
			items.push({headers: 'Константы',rownumber: 23});
			items.push({headers: 'Отсутствие связи: remote',rownumber: 23});
			items.push({headers: 'Отсутствие связи: in house',rownumber: 24});


			// let consentPerDay = this.data.consentPerDay;
			// let consentStagerPerDay = this.data.consentStagerPerDay;
			// let notCorrectCalls = this.data.notCorrectCalls;

			let settings = this.data.settings;
			// let lostCalls = this.data.lostCalls;

			items[S3_PRCSTLL]['plan'] = settings[S3_PRCSTLL]['plan'];

			// eslint-disable-next-line no-unused-vars
			let workDays;
			if (settings && settings[S21_CONSTANTS] && typeof settings[S21_CONSTANTS]['plan'] !== 'undefined') {
				items[S21_CONSTANTS]['plan'] = settings[S21_CONSTANTS]['plan']
				// eslint-disable-next-line no-unused-vars
				workDays = settings[S21_CONSTANTS]['plan'];
				this.workDays = settings[S21_CONSTANTS]['plan'];
			} else {
				items[S21_CONSTANTS]['plan'] = this.month.workDays
				// eslint-disable-next-line no-unused-vars
				workDays = this.month.workDays
				this.workDays = this.month.workDays
			}

			if (settings && settings[S21_CONSTANTS] && typeof settings[S21_CONSTANTS]['pr'] !== 'undefined') {
				items[S21_CONSTANTS]['pr'] = settings[S21_CONSTANTS]['pr']
			} else {
				items[S21_CONSTANTS]['pr'] = 1000
			}

			if (settings && settings[S21_CONSTANTS] && typeof settings[S21_CONSTANTS]['avg'] !== 'undefined') {
				items[S21_CONSTANTS]['avg'] = settings[S21_CONSTANTS]['avg']
			} else {
				items[S21_CONSTANTS]['avg'] = 270
			}

			if (settings && settings[S21_CONSTANTS] && typeof settings[S21_CONSTANTS]['1'] !== 'undefined') {
				items[S21_CONSTANTS]['1'] = settings[S21_CONSTANTS]['1']
			} else {
				items[S21_CONSTANTS]['1'] = 500
			}

			if (settings && settings[S21_CONSTANTS] && typeof settings[S21_CONSTANTS]['2'] !== 'undefined') {
				items[S21_CONSTANTS]['2'] = settings[S21_CONSTANTS]['2']
			} else {
				items[S21_CONSTANTS]['2'] = 3076
			}






			// if(notCorrectCalls == null || typeof notCorrectCalls === 'undefined') notCorrectCalls = {}
			// if(consentPerDay == null || typeof consentPerDay === 'undefined') consentPerDay = {}
			// if(consentStagerPerDay == null || typeof consentStagerPerDay === 'undefined') consentStagerPerDay = {}
			// if(lostCalls == null || typeof lostCalls === 'undefined') lostCalls = {}


			//Кол-во некоррект диалогов

			// Object.keys(notCorrectCalls).forEach(key => {
			//     items[S18_RED_NOTICE][key] = notCorrectCalls[key]
			// })


			// Object.keys(consentPerDay).forEach(key => {
			//     items[S4_MINUTE_1_5][key] = Number(consentPerDay[key]) || 0
			// })

			// Object.keys(consentStagerPerDay).forEach(key => {
			//     items[S5_OTHER][key] = Number(consentStagerPerDay[key]) || 0
			//     // items[FACT][key] = consentPerDay[key]
			// })

			// Object.keys(lostCalls).forEach(key => {
			//     items[S19_TWO_GROUP][key] = lostCalls[key]
			// })

			let days = this.month.daysInMonth

			for (let i = 1; i <= days; i++) {
				let minutes1_5 = 0;
				let minutesOthers = 0;
				let fact = 0;
				let factOperators = 0;

				// if (typeof consentPerDay[i] != 'undefined' && typeof consentStagerPerDay[i] != 'undefined') {

				//     items[S6_FACT][i] = consentPerDay[i] + consentStagerPerDay[i]
				// } else if (typeof consentPerDay[i] != 'undefined') {
				//     items[S6_FACT][i] = consentPerDay[i]
				// } else {
				//     items[S6_FACT][i] = null
				// }

				// минуты сегмента 1-5
				if (settings && typeof settings[S4_MINUTE_1_5] !== 'undefined' && typeof settings[S4_MINUTE_1_5][i] !== 'undefined') {
					minutes1_5 = settings[S4_MINUTE_1_5][i];
					items[S4_MINUTE_1_5][i] = minutes1_5
				}
				// минуты остальных сегментов
				if (settings && typeof settings[S5_OTHER] !== 'undefined' && typeof settings[S5_OTHER][i] !== 'undefined') {
					minutesOthers = settings[S5_OTHER][i];
					items[S5_OTHER][i] = settings[S5_OTHER][i]
				}
				fact = minutes1_5 + minutesOthers
				items[S6_FACT][i] = fact

				// 7 строка, план
				if (settings && typeof settings[S7_PLAN] !== 'undefined' && typeof settings[S7_PLAN][i] !== 'undefined') {
					items[S7_PLAN][i] = Number(settings[S7_PLAN][i]).toFixed(0)
				}

				// 8 строка, поступление лидов
				if (settings && settings[S8_LIDS] && typeof settings[S8_LIDS][i] !== 'undefined') {
					items[S8_LIDS][i] = settings[S8_LIDS][i]
				}

				// 10 строка, факт операторов
				/*if (settings && typeof settings[S10_FACT_OPERATORS] !== 'undefined' && typeof settings[S10_FACT_OPERATORS][i] !== 'undefined') {
                  factOperators = settings[S10_FACT_OPERATORS][i]
                  items[S10_FACT_OPERATORS][i] = factOperators
                }*/

				factOperators = Number(this.totals[i]) / 9;
				items[S10_FACT_OPERATORS][i] = isNaN(factOperators) ? 0 : Number(factOperators).toFixed(1)

				// 11 строка, в среднем минут на оператора
				items[S11_AVR_MINUTE_OPERATOR][i] = factOperators > 0 ? this.toFloat(fact / factOperators / 9) : 0;
				// 12 строка, пустая строка
				items[S12_EMPTY][i] = this.toFloat((factOperators * items[S21_CONSTANTS]['avg'] * 30) / items[S21_CONSTANTS]['pr'])

				// 13 строка, 1-5 ежедневный
				if (settings && typeof settings[S13_1_5_DAY] !== 'undefined' && typeof settings[S13_1_5_DAY][i] !== 'undefined') {
					items[S13_1_5_DAY][i] = settings[S13_1_5_DAY][i] || null
				}
				// 14 строка, рейтинг 1-5 за 5 дней, розовая строка
				if (settings && typeof settings[S14_RATING] !== 'undefined' && typeof settings[S14_RATING][i] !== 'undefined') {
					items[S14_RATING][i] = settings[S14_RATING][i] || null
				}
				// 15 строка, ОВД
				if (settings && typeof settings[S15_OVD] !== 'undefined' && typeof settings[S15_OVD][i] !== 'undefined') {
					items[S15_OVD][i] = settings[S15_OVD][i] || null
				}
				// 16 строка, Red ОВД
				if (settings && typeof settings[S16_RED_OVD] !== 'undefined' && typeof settings[S16_RED_OVD][i] !== 'undefined') {
					items[S16_RED_OVD][i] = settings[S16_RED_OVD][i] || null
				}
				// 17 строка, напоминания кредит
				if (settings && typeof settings[S17_NOTICE_CREDIT] !== 'undefined' && typeof settings[S17_NOTICE_CREDIT][i] !== 'undefined') {
					items[S17_NOTICE_CREDIT][i] = settings[S17_NOTICE_CREDIT][i] || null
				}
				// 18 строка, RED напоминаний
				if (settings && typeof settings[S18_RED_NOTICE] !== 'undefined' && typeof settings[S18_RED_NOTICE][i] !== 'undefined') {
					items[S18_RED_NOTICE][i] = settings[S18_RED_NOTICE][i] || null
				}
				// 19 строка, 2 группа
				if (settings && typeof settings[S19_TWO_GROUP] !== 'undefined' && typeof settings[S19_TWO_GROUP][i] !== 'undefined') {
					items[S19_TWO_GROUP][i] = settings[S19_TWO_GROUP][i] || null
				}
				// 20 строка, жалобы
				if (settings && typeof settings[S20_CLAIMS] !== 'undefined' && typeof settings[S20_CLAIMS][i] !== 'undefined') {
					items[S20_CLAIMS][i] = settings[S20_CLAIMS][i] || null
				}

				if (settings && typeof settings[S22_CONNECTION_LACK_REMOTE] !== 'undefined' && typeof settings[S22_CONNECTION_LACK_REMOTE][i] !== 'undefined') {
					items[S22_CONNECTION_LACK_REMOTE][i] = settings[S22_CONNECTION_LACK_REMOTE][i] || null
				}

				if (settings && typeof settings[S23_CONNECTION_LACK_INHOUSE] !== 'undefined' && typeof settings[S23_CONNECTION_LACK_INHOUSE][i] !== 'undefined') {
					items[S23_CONNECTION_LACK_INHOUSE][i] = settings[S23_CONNECTION_LACK_INHOUSE][i] || null
				}



				if (settings &&
                    typeof settings[S15_OVD] != 'undefined' &&
                    typeof settings[S15_OVD][i] != 'undefined') {
					items[S15_OVD][i] = settings[S15_OVD][i] || null
				}

				if (settings &&
                    typeof settings[S16_RED_OVD] != 'undefined' &&
                    typeof settings[S16_RED_OVD][i] != 'undefined') {
					items[S16_RED_OVD][i] = settings[S16_RED_OVD][i] || null
				}

			}

			items.forEach((element, key) => {
				items[key]['_cellVariants'] = [];
				items[key]['highlight'] = false
			});

			this.items = items
			this.updateTable()

			this.items[S0_TOTAL]._cellVariants['headers'] = 'warning text-right';
			this.items[S0_TOTAL]._cellVariants['pr'] = 'warning';
			this.items[S0_TOTAL]._cellVariants['plan'] = 'warning';

			this.items[S1_IMPL]._cellVariants['headers'] = 'warning';
			this.items[S1_IMPL]._cellVariants['pr'] = 'warning';
			this.items[S1_IMPL]._cellVariants['plan'] = 'warning';

			this.items[S2_PRCST]._cellVariants['headers'] = 'warning';
			this.items[S2_PRCST]._cellVariants['pr'] = 'warning';
			this.items[S2_PRCST]._cellVariants['plan'] = 'warning';

			this.items[S3_PRCSTLL]._cellVariants['headers'] = 'warning';
			this.items[S3_PRCSTLL]._cellVariants['pr'] = 'yellow';
			this.items[S3_PRCSTLL]._cellVariants['plan'] = 'yellow';

			this.items[S3_PRCSTLL]._cellVariants['avg'] = 'warning';

			this.items[S13_1_5_DAY]._cellVariants['headers'] = 'success';
			this.items[S13_1_5_DAY]._cellVariants['pr'] = 'success';
			this.items[S13_1_5_DAY]._cellVariants['plan'] = 'success';

			this.items[S15_OVD]._cellVariants['headers'] = 'success';
			this.items[S15_OVD]._cellVariants['pr'] = 'success';
			this.items[S15_OVD]._cellVariants['plan'] = 'success';

			this.items[S16_RED_OVD]._cellVariants['headers'] = 'success';
			this.items[S16_RED_OVD]._cellVariants['pr'] = 'success';
			this.items[S16_RED_OVD]._cellVariants['plan'] = 'success';

			this.items[S17_NOTICE_CREDIT]._cellVariants['headers'] = 'success';
			this.items[S17_NOTICE_CREDIT]._cellVariants['pr'] = 'success';
			this.items[S17_NOTICE_CREDIT]._cellVariants['plan'] = 'success';

			this.items[S18_RED_NOTICE]._cellVariants['headers'] = 'success';
			this.items[S18_RED_NOTICE]._cellVariants['pr'] = 'success';
			this.items[S18_RED_NOTICE]._cellVariants['plan'] = 'success';

			this.items[S19_TWO_GROUP]._cellVariants['headers'] = 'success';
			this.items[S19_TWO_GROUP]._cellVariants['pr'] = 'success';
			this.items[S19_TWO_GROUP]._cellVariants['plan'] = 'success';



		},

		changeFn(data) {

			this.$emit('changeFn', data)
		},

		async updateSettings(e, data) {
			var index = data.index
			var clearedValue = e.target.value.replace(',', '.');
			var value = parseFloat(clearedValue) || null
			var key = data.field.key
			this.items[index][key] = value

			if(index == S8_LIDS) {
				if(this.currentGroup == 35) this.items[S7_PLAN][key] = Number(Number(value) * 1.3).toFixed(0); // Napominanie
				if(this.currentGroup == 42) this.items[S7_PLAN][key] = Number(Number(value) * 1.5).toFixed(0); // Prosrochka

				if(Number(value) == 0) {
					this.items[S7_PLAN][key] = '';
				}
			}



			// eslint-disable-next-line no-unused-vars
			let settings = []

			this.setShortcut(data)


			let year = this.currentYear

			let post_data = {
				date: this.$moment(`${this.month.currentMonth} ${year}`, 'MMMM YYYY').format('YYYY-MM-DD'),
				group_id: this.data.currentGroup,
				settings: this.items
			};

			if(index == S22_CONNECTION_LACK_REMOTE) {
				post_data['add_hours'] = {
					value: value,
					day: key,
					user_type: 'remote'
				};
			}

			if(index == S23_CONNECTION_LACK_INHOUSE) {
				post_data['add_hours'] = {
					value: value,
					day: key,
					user_type: 'office'
				};
			}

			this.data.settings = this.items;
			this.loadItems();
			this.saveTable(post_data)


		},

		async saveTable(post_data) {
			let loader = this.$loading.show()
			this.axios.post('/timetracking/update-settings', post_data).then(() => {
				loader.hide()
			}).catch(error => {
				alert(error)
				loader.hide()
			});
		},

		async updateTable() {
			// let workDays = this.workDays;

			let settings = this.data.settings;

			let days = this.month.daysInMonth

			// 0 строка
			let s0Name = 0; // ( s3PrCsrllTotal -(( s2PrCstCst + s3PrCsrllCst )))
			let s0Pr = 0; // (s10FactOperatorsTotal * this.items[S21_CONSTANTS]['avg'] * workDays)/this.items[S21_CONSTANTS]['pr']
			let s0Cst = 0; // (s7PlanTotal * 27) / this.items[S21_CONSTANTS]['pr']

			// 1 строка, Impl
			let s1ImplPr = 0; // s3PrCsrllCst / s0Pr
			let s1ImplCrt = 0; // s3PrCsrllCst / s0Cst

			// 2 строка, Pr,cst
			let s2PrCstCst = 0

			// 3 строка, Pr,cstll
			let s3PrCsrllTotal = 0
			let s3PrCsrllCount = 0
			let s3PrCsrllCst = 0
			let s3PrCsrllAvg = 0

			// 4 строка, Минуты сегмента 1-5
			let s4Minute1_5Total = 0
			let s4Minute1_5Count = 0
			let s4Minute1_5Avg = 0

			// 5 строка, минуты остальных сегментов
			let s5_OtherTotal = 0
			let s5_OtherCount = 0
			let s5_OtherAvg = 0

			// 6 строка, факт
			let s6FactTotal = 0
			let s6FactCount = 0
			let s6FactAvg = 0

			// 7 строка, план
			let s7PlanTotal = 0
			let s7PlanCount = 0
			let s7PlanAvg = 0

			// 8 строка, поступление лидов
			let totalLids = 0;
			let countLids = 0;
			let s8LidsAvg = 0;

			// 9 строка, план операторов
			let s9PlanOreratorsTotal = 0
			let s9PlanOreratorsCount = 0
			let s9PlanOreratorsAvg = 0

			// 10 строка, факт операторов
			let s10FactOperatorsTotal = 0;
			let s10FactOperatorsCount = 0;
			let s10FactOperatorsAvg = 0;

			let s12Empty = 0;

			let s131_5dayTotal = 0;
			let s131_5dayCount = 0;
			let avgS131_5day = 0;

			let s14RatingTotal = 0;
			let s14RatingCount = 0;
			let s14RatingAvr = 0;

			let s15OVDTotal = 0;
			let s15OVDCount = 0;
			let s15OVDAvr = 0;

			let s16RedOVDTotal = 0;
			let s16RedOVDCount = 0;
			let s16RedOVDAvr = 0;

			let s17CreditTotal = 0;
			let s17CreditCount = 0;
			let s17CreditAvr = 0;

			let s18NoticeTotal = 0;
			let s18NoticeCount = 0;
			let s18NoticeAvr = 0;

			let s19TwoGroupTotal = 0;
			let s19TwoGroupCount = 0;
			let s19TwoGroupAvr = 0;

			let s20ClaimsTotal = 0;
			let s20ClaimsCount = 0;
			let s20ClaimsAvr = 0;

			let s22_total = 0;
			// let s22_count = 0;
			// let s22_avg = 0;

			let s23_total = 0;
			// let s23_count = 0;
			// let s23_avg = 0;

			//  формируем массив для 10 строки, факт операторов
			// let sum = {};

			for (let i = 1; i <= days; i++) {

				let lids = 0;
				let s1ImplDay = 0; // формула (s3PrCsrllCst / s12Empty)

				// 4 строка, Минуты 1-5 сегментов
				if (this.items[S4_MINUTE_1_5] && typeof this.items[S4_MINUTE_1_5][i] !== 'undefined' && typeof this.items[S4_MINUTE_1_5][i]) {
					s4Minute1_5Total += parseInt(this.items[S4_MINUTE_1_5][i])
					s4Minute1_5Count++
				}

				// 5 строка, Минуты остальных сегментов
				if (this.items[S5_OTHER] && typeof this.items[S5_OTHER][i] !== 'undefined' && typeof this.items[S5_OTHER][i]) {
					s5_OtherTotal += parseInt(this.items[S5_OTHER][i])
					s5_OtherCount++
				}

				// 6 строка, Факт
				if (this.items[S6_FACT] && typeof this.items[S6_FACT][i] !== 'undefined' && this.items[S6_FACT][i]) {
					s6FactTotal += parseInt(this.items[S6_FACT][i])
					s6FactCount++
				}

				// 7 строка, План
				if (this.items[S7_PLAN] && typeof this.items[S7_PLAN][i] !== 'undefined' && this.items[S7_PLAN][i]) {
					s7PlanTotal += parseInt(this.items[S7_PLAN][i])
					s7PlanCount++
				}

				// 8 строка, поступления лидов
				if (this.items[S8_LIDS] && typeof this.items[S8_LIDS][i] !== 'undefined' && this.items[S8_LIDS][i]) {
					lids = this.items[S8_LIDS][i]
					this.items[S8_LIDS][i] = lids
					totalLids += lids
					countLids++
				}

				// 9 строка, план операторов
				if (this.items[S7_PLAN] && typeof this.items[S7_PLAN][i] !== 'undefined' && this.items[S7_PLAN][i]) {
					this.items[S9_PLAN_OPERATORS][i] = Number(parseFloat(this.items[S7_PLAN][i] / this.items[S21_CONSTANTS]['avg'])).toFixed(1) || 0
					s9PlanOreratorsTotal += parseFloat(this.items[S9_PLAN_OPERATORS][i])
					s9PlanOreratorsCount++
				}

				// 10 строка факт операторов,

				if (this.totals[i] !== 0 && typeof this.totals[i] !== 'undefined') {
					this.items[S10_FACT_OPERATORS][i] = this.totals[i]
					s10FactOperatorsTotal += Number(this.totals[i])
					s10FactOperatorsCount++;
				}

				// 12 строка, пустая строка
				if (this.items[S12_EMPTY] && typeof this.items[S12_EMPTY][i] !== 'undefined' && this.items[S12_EMPTY][i]) {
					s12Empty = this.toFloat(parseFloat(this.items[S10_FACT_OPERATORS][i] * this.items[S21_CONSTANTS]['avg'] * 30 / this.items[S21_CONSTANTS]['pr']))
					this.items[S12_EMPTY][i] = s12Empty

				}

				// 13 строка, 1-5 ежедневный
				if (this.items[S13_1_5_DAY] && typeof this.items[S13_1_5_DAY][i] !== 'undefined' && this.items[S13_1_5_DAY][i]) {
					s131_5dayTotal += settings[S13_1_5_DAY][i]
					s131_5dayCount++
				}

				// 14 строка, Рейтинг 1-5 за 5 дней
				if (this.items[S14_RATING] && typeof this.items[S14_RATING][i] !== 'undefined' && this.items[S14_RATING][i]) {
					s14RatingTotal += parseInt(settings[S14_RATING][i])
					s14RatingCount++
				}

				// 15 строка, ОВД
				if (this.items[S15_OVD] && typeof this.items[S15_OVD][i] !== 'undefined' && this.items[S15_OVD][i]) {
					s15OVDTotal += parseInt(settings[S15_OVD][i])
					s15OVDCount++
				}

				// 16 строка, Red ОВД
				if (this.items[S16_RED_OVD] && typeof this.items[S16_RED_OVD][i] !== 'undefined' && this.items[S16_RED_OVD][i]) {
					s16RedOVDTotal += parseInt(settings[S16_RED_OVD][i])
					s16RedOVDCount++
				}

				// 17 строка, напоминания кредит
				if (this.items[S17_NOTICE_CREDIT] && typeof this.items[S17_NOTICE_CREDIT][i] !== 'undefined' && this.items[S17_NOTICE_CREDIT][i]) {
					s17CreditTotal += parseInt(settings[S17_NOTICE_CREDIT][i])
					s17CreditCount++
				}

				// 18 строка,  RED напоминаний
				if (this.items[S18_RED_NOTICE] && typeof this.items[S18_RED_NOTICE][i] !== 'undefined' && this.items[S18_RED_NOTICE][i]) {
					s18NoticeTotal += parseInt(settings[S18_RED_NOTICE][i])
					s18NoticeCount++
				}

				// 19 строка, 2 группа
				if (this.items[S19_TWO_GROUP] && typeof this.items[S19_TWO_GROUP][i] !== 'undefined' && this.items[S19_TWO_GROUP][i]) {
					s19TwoGroupTotal += parseInt(settings[S19_TWO_GROUP][i])
					s19TwoGroupCount++
				}

				// 20 строка, жалобы
				if (this.items[S20_CLAIMS] && typeof this.items[S20_CLAIMS][i] !== 'undefined' && this.items[S20_CLAIMS][i]) {
					s20ClaimsTotal += parseInt(settings[S20_CLAIMS][i])
					s20ClaimsCount++
				}


				if (this.items[S22_CONNECTION_LACK_REMOTE] && typeof this.items[S22_CONNECTION_LACK_REMOTE][i] !== 'undefined' && this.items[S22_CONNECTION_LACK_REMOTE][i]) {
					s22_total += parseInt(settings[S22_CONNECTION_LACK_REMOTE][i])
					// s22_count++
				}

				if (this.items[S23_CONNECTION_LACK_INHOUSE] && typeof this.items[S23_CONNECTION_LACK_INHOUSE][i] !== 'undefined' && this.items[S23_CONNECTION_LACK_INHOUSE][i]) {
					s23_total += parseInt(settings[S23_CONNECTION_LACK_INHOUSE][i])
					// s23_count++
				}



				// 3 строка, cstll, формула day = ((6)Факт * workDays)/this.items[S21_CONSTANTS]['pr']
				// формула cst = ((10)Факт операторов * 3076)/this.items[S21_CONSTANTS]['pr']
				// формула pr = тупо сумма
				// формула среднее = тупо среднее
				if (this.items[S6_FACT] && typeof this.items[S6_FACT][i] !== 'undefined' && this.items[S6_FACT][i]) {
					//this.items[S3_PRCSTLL][i] = Number(parseFloat((this.items[S6_FACT][i] * workDays) / this.items[S21_CONSTANTS]['pr'])).toFixed(1) || 0
					this.items[S3_PRCSTLL][i] = Number(parseFloat((this.items[S6_FACT][i] * 30) / this.items[S21_CONSTANTS]['pr'])).toFixed(1) || 0
					s3PrCsrllTotal += parseFloat(this.items[S3_PRCSTLL][i])
					s3PrCsrllCount++
				}

				// 1 строка, Impl
				if (this.items[S3_PRCSTLL] && typeof this.items[S3_PRCSTLL][i] !== 'undefined' && this.items[S3_PRCSTLL][i]) {
					if (this.items[S12_EMPTY] && typeof this.items[S12_EMPTY][i] !== 'undefined' && this.items[S12_EMPTY][i]) {
						s1ImplDay = this.toFloat(this.items[S3_PRCSTLL][i] / this.items[S12_EMPTY][i])
						this.items[S1_IMPL][i] = parseFloat(s1ImplDay * 100).toFixed(0) + '%'

						if (s1ImplDay > 120) {
							this.items[S1_IMPL]._cellVariants[i] = 'success';
						} else {
							this.items[S1_IMPL]._cellVariants[i] = 'danger';
						}
					}
				}

				if (this.items[S6_FACT][i] > this.items[S7_PLAN][i]) {
					this.items[S4_MINUTE_1_5]._cellVariants[i] = 'success';
					this.items[S5_OTHER]._cellVariants[i] = 'success';
					this.items[S6_FACT]._cellVariants[i] = 'success';
				} else if ((this.items[S6_FACT][i] < this.items[S7_PLAN][i]) && this.items[S7_PLAN][i] !== 0) {
					this.items[S4_MINUTE_1_5]._cellVariants[i] = 'danger';
					this.items[S5_OTHER]._cellVariants[i] = 'danger';
					this.items[S6_FACT]._cellVariants[i] = 'danger';
				}

			}

			s4Minute1_5Avg = this.toFloat(s4Minute1_5Total / s4Minute1_5Count)
			s5_OtherAvg = this.toFloat(s5_OtherTotal / s5_OtherCount)
			s6FactAvg = this.toFloat(s6FactTotal / s6FactCount)
			s7PlanAvg = this.toFloat(s7PlanTotal / s7PlanCount)
			s8LidsAvg = this.toFloat(totalLids / countLids);
			s9PlanOreratorsAvg = this.toFloat(s9PlanOreratorsTotal / s9PlanOreratorsCount)
			s10FactOperatorsAvg = s10FactOperatorsCount > 0 ? Number(s10FactOperatorsTotal / s10FactOperatorsCount).toFixed(0) : 0;
			avgS131_5day = this.toFloat(s131_5dayTotal / s131_5dayCount)
			s14RatingAvr = this.toFloat(s14RatingTotal / s14RatingCount)
			s15OVDAvr = this.toFloat(s15OVDTotal / s15OVDCount)
			s16RedOVDAvr = this.toFloat(s16RedOVDTotal / s16RedOVDCount)
			s17CreditAvr = this.toFloat(s17CreditTotal / s17CreditCount)
			s18NoticeAvr = this.toFloat(s18NoticeTotal / s18NoticeCount)
			s19TwoGroupAvr = this.toFloat(s19TwoGroupTotal / s19TwoGroupCount)
			s20ClaimsAvr = this.toFloat(s20ClaimsTotal / s20ClaimsCount)
			// s22_avg = this.toFloat(s22_total / s22_count)
			// s23_avg = this.toFloat(s23_total / s23_count)

			this.items[S0_TOTAL]['pr'] = 'kair-0'
			this.items[S0_TOTAL]['plan'] = 'kair-1'

			this.items[S1_IMPL]['pr'] = 'kair-3'
			this.items[S1_IMPL]['plan'] = 'kair-4'

			this.items[S4_MINUTE_1_5]['pr'] = s4Minute1_5Total

			this.items[S4_MINUTE_1_5]['avg'] = isNaN(s4Minute1_5Avg) ? 0 : s4Minute1_5Avg

			this.items[S5_OTHER]['pr'] = s5_OtherTotal

			this.items[S5_OTHER]['avg'] = isNaN(s5_OtherAvg) ? 0 : s5_OtherAvg

			this.items[S6_FACT]['pr'] = s6FactTotal || 0
			this.items[S6_FACT]['avg'] =  isNaN(s6FactAvg) ? 0 : s6FactAvg

			this.items[S7_PLAN]['pr'] = Number(s7PlanTotal).toFixed(0)
			if (isNaN(s7PlanAvg)) {
				s7PlanAvg = 0
			}
			this.items[S7_PLAN]['avg'] = isNaN(s7PlanAvg) ? 0 : Number(s7PlanAvg).toFixed(0)

			this.items[S8_LIDS]['pr'] =  isNaN(totalLids) ? 0 : totalLids

			if (isNaN(s8LidsAvg)) {
				s8LidsAvg = 0
			}
			this.items[S8_LIDS]['avg'] =  isNaN(s8LidsAvg) ? 0 : s8LidsAvg;

			this.items[S9_PLAN_OPERATORS]['pr'] = Number(s9PlanOreratorsTotal).toFixed(0)
			if (isNaN(s9PlanOreratorsAvg)) {
				s9PlanOreratorsAvg = 0
			}
			this.items[S9_PLAN_OPERATORS]['avg'] = Number(s9PlanOreratorsAvg).toFixed(0)

			this.items[S10_FACT_OPERATORS]['pr'] = Number(s10FactOperatorsTotal).toFixed(1)
			this.items[S10_FACT_OPERATORS]['avg'] = s10FactOperatorsAvg

			this.items[S13_1_5_DAY]['pr'] = avgS131_5day

			if (isNaN(s14RatingAvr)) {
				s14RatingAvr = 0
			}
			this.items[S14_RATING]['pr'] = s14RatingAvr

			if (isNaN(s15OVDAvr)) {
				s15OVDAvr = 0
			}
			this.items[S15_OVD]['pr'] = s15OVDAvr

			if (isNaN(s16RedOVDAvr)) {
				s16RedOVDAvr = 0
			}
			this.items[S16_RED_OVD]['pr'] = s16RedOVDAvr

			if (isNaN(s17CreditAvr)) {
				s17CreditAvr = 0
			}
			this.items[S17_NOTICE_CREDIT]['pr'] = s17CreditAvr

			if (isNaN(s18NoticeAvr)) {
				s18NoticeAvr = 0
			}
			this.items[S18_RED_NOTICE]['pr'] = s18NoticeAvr

			if (isNaN(s19TwoGroupAvr)) {
				s19TwoGroupAvr = 0
			}
			//this.items[S19_TWO_GROUP]['pr'] = s19TwoGroupAvr

			if (isNaN(s20ClaimsAvr)) {
				s20ClaimsAvr = 0
			}
			this.items[S20_CLAIMS]['pr'] = s20ClaimsAvr
			this.items[S22_CONNECTION_LACK_REMOTE]['pr'] = s22_total
			this.items[S23_CONNECTION_LACK_INHOUSE]['pr'] = s23_total

			// 3 строка
			s3PrCsrllAvg = this.toFloat(s3PrCsrllTotal / s3PrCsrllCount)
			s3PrCsrllCst = this.toFloat(parseFloat((this.items[S10_FACT_OPERATORS]['pr'] * this.items[S21_CONSTANTS]['2']) / this.items[S21_CONSTANTS]['pr']))
			this.items[S3_PRCSTLL]['pr'] = this.toFloat(s3PrCsrllTotal)

			this.items[S3_PRCSTLL]['avg'] =  isNaN(s3PrCsrllAvg) ? 0 : s3PrCsrllAvg;

			s2PrCstCst = this.toFloat(parseFloat((this.items[S10_FACT_OPERATORS]['pr'] * this.items[S21_CONSTANTS]['1']) / this.items[S21_CONSTANTS]['pr']))
			this.items[S2_PRCST]['plan'] = s2PrCstCst

			s0Name = this.toFloat(parseFloat(parseFloat(s3PrCsrllTotal) - ((parseFloat(s2PrCstCst) + parseFloat(s3PrCsrllCst)))))
			this.items[S0_TOTAL]['headers'] = s0Name

			s0Pr = (s10FactOperatorsTotal * 270 * 26) / 1000
			s0Pr = Number(s0Pr).toFixed(1);
			this.items[S0_TOTAL]['pr'] = s0Pr

			s0Cst = (s7PlanTotal * 30) / 1000;
			s0Cst = Number(s0Cst).toFixed(1);
			this.items[S0_TOTAL]['plan'] = s0Cst


			s1ImplPr = parseFloat(s3PrCsrllTotal / s0Pr).toFixed(4)
			s1ImplPr = isNaN(s1ImplPr) ? 0 : s1ImplPr;

			this.items[S1_IMPL]['pr'] = parseFloat(s1ImplPr * 100).toFixed(1) + '%'

			s1ImplCrt = parseFloat(s3PrCsrllTotal / s0Cst).toFixed(4)
			s1ImplCrt = isNaN(s1ImplCrt) ? 0 : s1ImplCrt;
			this.items[S1_IMPL]['plan'] = parseFloat(s1ImplCrt * 100).toFixed(1) + '%'

		}
	}
};
</script>

<style lang="scss">
.summary-kaspi {
    tr:nth-child(8),
    tr:nth-child(10) {
        background: #ff69b4 !important;
        color: #000 !important;

        td {
            background: #ff69b4 !important;
            font-weight: bold;

            input {
                font-weight: bold;
            }
        }
    }
    tbody tr:nth-child(22) {
        display: none;
    }
    tbody tr:nth-child(8) *,
    tbody tr:nth-child(10) * {
        font-size: 12px;
    }
}
</style>
<style lang="scss" scoped>
.my-table-max {
    max-height: inherit !important;

    .day {
        padding: 0 !important;
        text-align: center;


        &.Sat,
        &.Sun {
            background-color: #FEF2CB;
        }

    }

    tr:nth-child(4) {

        td:nth-child(2),
        td:nth-child(3) {
            background-color: yellow !important;
        }
    }

    tr:nth-child(8) {
        background: #ff33cc7a !important;
        color: #000 !important;

        td {
            background: #ff33cc7a !important;
            font-weight: bold;

            input {
                font-weight: bold;
            }
        }
    }

    tr:nth-child(10) {
        background: #ff33cc7a !important;
        color: #000 !important;

        td {
            background: #ff33cc7a !important;
            font-weight: bold;

            input {
                font-weight: bold;
            }
        }
    }

}

.cell-input {
    background: none;
    border: none;
    text-align: center;
    -moz-appearance: textfield;
    font-size: .8rem;
    font-weight: normal;
    padding: 0;
    color: #000;
    border-radius: 0;

    &:focus {
        outline: none;
        border: 2px solid #4b89ff;
        background: #e6efff;
    }

    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}
.day div:focus {
    background: red;
}
.summary-kaspi .high {
    border: 2px solid #4b89ff;
    animation: hightlight 3s ease infinite;
}
@keyframes hightlight {
    from {
        border-color: #0065ff;
    }
    50% {
        border-color: #77a6ff;
    }
    to {
        border-color: #0065ff;
    }
}

</style>
