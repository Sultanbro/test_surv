var fields = [
    {
        name: 'Кому',
        key: 'target',
        visible: true,
        type: 'superselect',
        class: 'text-left w-lg'
    },
    {
        name: 'Название',
        key: 'title',
        visible: true,
        type: 'text',
        class: 'text-center'
    },
    {
        name: 'Показатели',
        key: 'activity_id',
        visible: true,
        type: 'number',
        class: 'text-center'
    },
    {
        name: 'За',
        key: 'unit',
        visible: true,
        type: 'text',
        class: 'text-center'
    },
    {
        name: 'Кол-во',
        key: 'quantity',
        visible: true,
        type: 'number',
        class: 'text-center'
    },
    {
        name: 'Период',
        key: 'daypart',
        visible: true,
        type: 'number',
        class: 'text-center'
    },
    {
        name: 'Текст',
        key: 'text',
        visible: true,
        type: 'number',
        class: 'text-center'
    },
    {
        name: 'Вознаграждение',
        key: 'sum',
        visible: true,
        type: 'number',
        class: 'text-center'
    },
    {
        name: 'Дата создания',
        key: 'created_at',
        visible: true,
        type: 'date',
        class: 'text-center'
    },
    {
        name: 'Дата изменения',
        key: 'updated_at',
        visible: true,
        type: 'date',
        class: 'text-center'
    },
    {
        name: 'Постановщик',
        key: 'created_by',
        visible: true,
        type: 'text',
        class: 'text-center'
    },
    {
        name: 'Изменил',
        key: 'updated_by',
        visible: true,
        type: 'text',
        class: 'text-center'
    }
];

module.exports = {
    fields: fields,
};