function findModel(type = 0) {
    let text = '';
    if(type == 1) text = 'App\\User';
    if(type == 2) text = 'App\\ProfileGroup';
    if(type == 3) text = 'App\\Position';
    
    return text;
};


module.exports = {
    findModel: findModel,
};