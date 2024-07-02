/* eslint-disable camelcase */

var fields = [
	{
		name: 'Название',
		key: 'name',
		visible: true,
		type: 'text',
		class: 'text-left w-lg',
		alter_class: 'col-md-12'
	},
	{
		name: 'Источник',
		key: 'source',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-12'
	},
	{
		name: 'Функция',
		key: 'method',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-4'
	},
	{
		name: 'Дневной план',
		key: 'daily_plan',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-4'
	},
	{
		name: 'Ед.изм.',
		key: 'unit',
		visible: true,
		type: 'text',
		class: 'text-center',
		alter_class: 'col-md-4'
	},
	{
		name: 'Вид',
		key: 'view',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-3'
	},
	{
		name: 'Рабочие дни',
		key: 'weekdays',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-3'
	},
	{
		name: 'Очередь',
		key: 'order',
		visible: true,
		type: 'text',
		class: 'text-center',
		alter_class: 'col-md-3'
	},
	{
		name: 'Редактируемый',
		key: 'editable',
		visible: true,
		type: 'checkbox',
		class: 'text-center',
		alter_class: 'col-md-3'
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


function newItem() {
	return  {
		id: 0,
		name: '',
		source: 0,
		method: 1,
		daily_plan: 0,
		unit: '',
		view: 0,
		weekdays: 5,
		order: 0,
		group_id: 0,
		editable: true,
		created_at: datestring,
		updated_at: datestring,
		created_by: 'Вы',
		updated_by: 'Вы'
	};
}

// eslint-disable-next-line no-undef
module.exports = {
	fields: fields,
	newItem: newItem
};
