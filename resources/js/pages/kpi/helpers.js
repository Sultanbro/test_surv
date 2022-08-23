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

module.exports = {
    findModel: findModel,
    groupBy: groupBy,
};