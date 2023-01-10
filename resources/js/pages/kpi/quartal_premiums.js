var fields = [
	{
		name: 'Кому',
		key: 'target',
		visible: true,
		type: 'superselect',
		class: 'text-left w-230',
		alter_class: 'col-md-12'
	},
	{
		name: 'Название',
		key: 'title',
		visible: true,
		type: 'text',
		class: 'text-center',
		alter_class: 'col-md-12'
	},
	{
		name: 'Показатели',
		key: 'activity_id',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-12'
	},
	{
		name: 'План',
		key: 'plan',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-2'
	},
	{
		name: 'Вознаграждение',
		key: 'sum',
		visible: true,
		type: 'number',
		class: 'text-center',
		alter_class: 'col-md-2'
	},
	{
		name: 'Период с',
		key: 'from',
		visible: true,
		type: 'date',
		class: 'text-center',
		alter_class: 'col-md-4'
	},
	{
		name: 'Период по',
		key: 'to',
		visible: true,
		type: 'date',
		class: 'text-center',
		alter_class: 'col-md-4'
	},

	{
		name: 'Текст',
		key: 'text',
		visible: true,
		type: 'text',
		class: 'text-center',
		alter_class: 'col-md-12'
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


function newQuartalPremium() {
	return  {
		id: 0,
		target: null,
		title: '',
		source: 0,
		group_id: 0,
		activity_id: 0,
		sum: 0,
		plan: 1,
		from: null,
		to: null,
		text: '',
		created_at: datestring,
		updated_at: datestring,
		created_by: 'Вы',
		updated_by: 'Вы',
		expanded: false
	};
}

// eslint-disable-next-line no-undef
module.exports = {
	fields: fields,
	newQuartalPremium: newQuartalPremium
};