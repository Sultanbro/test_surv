function findModel(type = 0) {
    let text = '';
    if(type == 1) text = 'App\\User';
    if(type == 2) text = 'App\\ProfileGroup';
    if(type == 3) text = 'App\\Position';
    
    return text;
};

function groupBy(xs, key) {
    return xs.reduce(function(rv, x) {
        (rv[x[key]] = rv[x[key]] || []).push(x);
        return rv;
    }, {});
}

// sources of activities 
let sources = {
    0: 'без источника',
    1: 'вкладка "Аналитика"',
    2: 'из битрикса',
    3: 'из амосрм',
    4: 'другие',
    5: 'вкладка "Табель"',
    6: 'вкладка "HR"',
}

// kpi methods
let methods = {
    1: 'сумма',
    2: 'сред значение',
    3: 'сумма не более',
    4: 'среднее не более',
    5: 'сумма не менее',
    6: 'сумма не более',
}

// views of activities
let views = {
    0: 'по умолчанию',
    1: 'коллекция',
    2: 'контроль качества',
    3: 'рентабельность',
    4: 'текучка',
    5: 'кол-во сотрудников',
    6: 'конверсия',
}

module.exports = {
    findModel: findModel,
    groupBy: groupBy,
    sources: sources,
    methods: methods,
    views: views,
};