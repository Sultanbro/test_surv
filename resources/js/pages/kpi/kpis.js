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

    return d.getDate()  + "." + month + "." + d.getFullYear() + " " +
        d.getHours() + ":" + d.getMinutes();
}

var datestring = formatDate(new Date());


function newKpi() {
    return {
        id: 0,
        target: null,
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
        share: 0
    }
}

module.exports = {
    kpi_fields: kpi_fields,
    newKpi: newKpi,
    newKpiItem: newKpiItem,
};