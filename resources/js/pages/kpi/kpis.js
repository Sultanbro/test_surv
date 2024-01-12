/* eslint-disable camelcase */

var kpi_fields = [
	{
		name: 'Кому',
		key: 'target',
		visible: true,
		type: 'superselect',
		class: 'text-left w-lg',
		alter_class: 'col-md-12'
	},
	{
		name: 'Выполнение KPI от 80-99%',
		key: 'completed_80',
		visible: true,
		type: 'number',
		class: 'text-center'
	},
	{
		name: 'Выполнение KPI от 100%',
		key: 'completed_100',
		visible: true,
		type: 'number',
		class: 'text-center'
	},
	{
		name: 'Нижний порог отсечения премии, %',
		key: 'lower_limit',
		visible: true,
		type: 'number',
		class: 'text-center'
	},
	{
		name: 'Верхний порог отсечения премии, %',
		key: 'upper_limit',
		visible: true,
		type: 'number',
		class: 'text-center'
	},
	{
		name: 'Статистика',
		key: 'stats',
		visible: true,
		type: 'number',
		class: 'text-center'
	},
	{
		name: 'Дата создания',
		key: 'created_at',
		visible: true,
		type: 'date',
		class: 'text-center',
		alter_class: 'col-md-6'
	},
	{
		name: 'Дата изменения',
		key: 'updated_at',
		visible: true,
		type: 'date',
		class: 'text-center',
		alter_class: 'col-md-6'
	},
	{
		name: 'Постановщик',
		key: 'created_by',
		visible: true,
		type: 'text',
		class: 'text-center',
		alter_class: 'col-md-6'
	},
	{
		name: 'Изменил',
		key: 'updated_by',
		visible: true,
		type: 'text',
		class: 'text-center',
		alter_class: 'col-md-6'
	}
];





// dates
function formatDate(d) {
	var day = d.getDate() + '';
	if(day.length == 1) day = '0' + day;

	var month = (d.getMonth() + 1) + '';
	if(month.length == 1) month = '0' + month;

	return d.getDate()  + '.' + month + '.' + d.getFullYear() + ' ' +
        d.getHours() + ':' + d.getMinutes();
}

var datestring = formatDate(new Date());


function newKpi() {
	return {
		id: 0,
		target: null,
		targets: [],
		completed_80: 10000,
		completed_100: 30000,
		lower_limit: 80,
		upper_limit: 100,
		stats: 3,
		created_at: datestring,
		updated_at: datestring,
		created_by: 'Вы',
		updated_by: 'Вы',
		items: [],
		expanded: false
	}
}

function newKpiItem() {
	return {
		id: 0,
		sum: 0,
		method: 1,
		name: 'Активность',
		activity_id: 0,
		plan: 0,
		share: 0,
		cell: '',
		common: 0,
	}
}


function numberize(a) {
	return a == undefined
        || a == null
        || isNaN(Number(a))
        || !isFinite(Number(a))
		? 0 : Number(a)
}

/**
 * Calc completed percent of kpi_item
 *
 * @param {Object} el
 * @returns
 */
function calcCompleted(el) {
	let res = 0;

	let fact = numberize(el.fact)
	let avg = numberize(el.avg)
	// let records_count = numberize(el.records_count)
	let plan = el.plan;
	// let workdays = numberize(el.workdays);

	// if(plan <= 0) return 0;

	if(el.method == 1) {
		res = (fact / plan * 100).toFixed(2);
	}

	if(el.method == 2) {
		res = el.percent
	}

	if(el.method == 3) {
		res = plan - fact >= 0 ? 100 : 0;
	}

	if(el.method == 4) {
		res = plan - avg >= 0 ? 100 : 0;
	}

	if(el.method == 5) {
		res = fact - plan >= 0 ? 100 : 0;
	}

	if(el.method == 6) {
		res = avg - plan >= 0 ? 100 : 0;
	}

	return Number(res);
}

/**
 * Calc sum of kpi_item
 *
 * @param {Object} el kpi_item
 * @param {Object} kpi
 * @param {Number} completed
 * @returns Number
 */
function calcSum(el, kpi, completed) {
	let result = 0; //=ЕСЛИ(F9>$D$3;ЕСЛИ(F9<$E$3;$B$3*D9*(F9-$D$3)*$E$3/($E$3-$D$3);$B$4*D9*F9);0)

	let lower_limit = parseFloat(kpi.lower_limit) / 100.0
	let upper_limit = parseFloat(kpi.upper_limit) / 100.0
	let share = el.share != undefined ? parseFloat(el.share) / 100.0 : 0
	let completed_80 = kpi.completed_80
	let completed_100 = kpi.completed_100

	if(el.histories_latest?.payload?.share){
		share = el.histories_latest.payload.share != undefined ? parseFloat(el.histories_latest.payload.share) / 100.0 : 0
	}

	if(el.full_time == 0) {
		completed_80 /= 2;
		completed_100 /= 2;
	}

	// check overfulfillment
	if(!kpi.allow_overfulfillment && completed > 1) completed = 1;

	if(completed > lower_limit) {

		if (completed < upper_limit) {
			result = completed_80 * share * (completed - lower_limit) * upper_limit / (upper_limit - lower_limit)
		}
		else {
			result = completed_100 * share * completed
		}
	}


	if (result < 0) result = 0;
	return Number(Number(result).toFixed(1));
}

/**
 * Parse kpi json payload
 *
 * @param {Object} kpi
 */
function parseKPI(kpi){
	if(kpi.histories_latest?.payload){
		if(typeof kpi.histories_latest.payload === 'string') {
			kpi.histories_latest.payload = JSON.parse(kpi.histories_latest.payload)
			kpi.lower_limit = kpi.histories_latest.payload.lower_limit
			kpi.upper_limit = kpi.histories_latest.payload.upper_limit
			kpi.completed_80 = kpi.histories_latest.payload.completed_80
			kpi.completed_100 = kpi.histories_latest.payload.completed_100
		}
		if(Object.keys(kpi.histories_latest.payload).includes('is_active')){
			kpi.is_active = kpi.histories_latest.payload.is_active
		}
	}
	kpi.users.forEach(user => {
		if(!user.items) return
		user.items.forEach(item => {
			if(!item.histories_latest?.payload) return
			if(typeof item.histories_latest.payload !== 'string') return
			item.histories_latest.payload = JSON.parse(item.histories_latest.payload)
		})
	})
	return kpi
}

function removeDeletedItems(kpis){
	kpis.forEach(kpi => {
		if(!kpi.users) return
		kpi.users.forEach(user => {
			if(!user.items) return
			user.items = user.items.filter(item => !item.deleted_at)
		})
	})
}

const target2type = {
	'App\\User': 1,
	'App\\Position': 2,
	'App\\ProfileGroup': 3,
}

// eslint-disable-next-line no-undef
module.exports = {
	kpi_fields,
	newKpi,
	newKpiItem,
	numberize,
	calcSum,
	formatDate,
	calcCompleted,
	parseKPI,
	removeDeletedItems,
	target2type,
};
