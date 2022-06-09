@php
$assetsVersion = '1.03' // т
@endphp 
<!DOCTYPE html>
<html lang='ru'>

<head>
    <meta charset=utf-8>
    <meta http-equiv=X-UA-Compatible content="IE=edge">
    <meta name=viewport content="width=device-width,initial-scale=1">

    <title>База знаний</title>
    <link href=/admin/css/corp_book/app.9a2436bd.css rel=preload as=style>
    <link href=/admin/css/corp_book/chunk-vendors.18c93001.css rel=preload as=style>
    <link href=/admin/js/corp_book/app.5c013872.js rel=preload as=script>
    <link href=/admin/js/corp_book/chunk-vendors.1f444d7b.js rel=preload as=script>
    <link href=/admin/css/corp_book/chunk-vendors.18c93001.css rel=stylesheet>
    <link href=/admin/css/corp_book/app.9a2436bd.css rel=stylesheet>
    <script>

      console.log(localStorage)
      localStorage.setItem('activecat', '46');
      localStorage.setItem('authin', '1');

    </script>
    <style>
      .namebook i {
        display: inline-block;
        margin-left: 10px;
        float: right;
        margin-top: 7px;
        margin-right: 0px;
      }
      .namebook .hider {
        position: absolute;
        left: -10px;
        width: 10px;
        height: 10px;
        opacity: 0;
        display: block;
    }
    .pointer {
        cursor: pointer;
    }
    .namebook {
      width: calc(100% + 15px);
      margin: 0 -15px;
      padding: 15px;
      display: block;
      left:unset;
    }
    </style>
</head>

<body>
  
    <noscript><strong>We're sorry but bpbook doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>
    <div id=app></div>
    <script>
      (function(w,d,u){
          var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
          var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
      })(window,document,'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_8_dzfbjh.js');
  </script>

    <script src="/admin/js/corp_book/chunk-vendors.1f444d7b.js?v={{$assetsVersion}}"></script>
    <script src="/admin/js/corp_book/app.5c013872.js?v={{$assetsVersion}}"></script>
</body>

</html>