//progress для страниц
function progressunload(){
     NProgress.start();
    setTimeout(function() { 
     NProgress.done();
    }, 8000);
}    
function progress(){
    NProgress.start();
    NProgress.set(0.4);
    var interval = setInterval(function() { NProgress.inc(); }, 2000);
    $(document).ready(function(){
        NProgress.done();
        clearInterval(interval);
    });
}    
$('#sidebar a').click(function(){
    sessionStorage.setItem("position", "left");
});
$('#main a').click(function(){
    sessionStorage.setItem("position", "right");
});
var position = sessionStorage.getItem("position");
if (position == 'left') {
    NProgress.configure({
        parent: '#content',
    });
    progress();
} else if (position == 'right') {
    NProgress.configure({
        parent: '#progress_cnt',
    });
    progress();
} else {
    NProgress.configure({
        parent: 'body',
    });
    progress();
}
window.addEventListener('beforeunload', function(event) {
    
$('#sidebar a').click(function(){
    sessionStorage.setItem("position", "left");
});
$('#main a').click(function(){
    sessionStorage.setItem("position", "right");
});
var position = sessionStorage.getItem("position");
    if (position == 'left') {
    NProgress.configure({
        parent: '#content',
    });
    progressunload();
} else if (position == 'right') {
    NProgress.configure({
        parent: '#progress_cnt',
    });
    progressunload();
} else {
    NProgress.configure({
        parent: 'body',
    });
   progressunload();
}
    
});