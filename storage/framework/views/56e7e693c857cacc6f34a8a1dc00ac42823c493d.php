<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
        <title>U-marketing.org</title>
        <link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <link rel="stylesheet" href="/static/new/css/auth.min.css?ver1.4231">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php echo $__env->yieldContent('content'); ?>
        <script>window.roistatCookieDomain = 'u-marketing.org';</script>
        <script>
            (function(w, d, s, h, id) {
                w.roistatProjectId = id; w.roistatHost = h;
                var p = d.location.protocol == "https:" ? "https://" : "http://";
                var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init";
                var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
            })(window, document, 'script', 'cloud.roistat.com', '92537ae8425b40443bb504e4503594fd');
        </script>

        <script>

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

jQuery(document).ready(function($) {
if (getUrlParameter('reg') == 'show') {
  setTimeout(function() {$('.tabset li:nth-child(2) a').trigger( "click" )  }, 100);

}
 });
</script>

        <script src="/static/new/js/auth.js?v=1.31"></script>
    </body>
</html>
