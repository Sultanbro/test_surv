
<!DOCTYPE html>
<html lang=ru>

<head>
    <meta charset=utf-8>
    <meta http-equiv=X-UA-Compatible content="IE=edge">
    <meta name=viewport content="width=device-width,initial-scale=1">

    <title><?php echo e($title); ?></title>
    <link href=/admin/css/corp_book/app.9a2436bd.css rel=preload as=style>
    <link href=/admin/css/corp_book/chunk-vendors.18c93001.css rel=preload as=style>
    <link href=/admin/js/corp_book/chunk-vendors.1f444d7b.js rel=preload as=script>
    <link href=/admin/css/corp_book/chunk-vendors.18c93001.css rel=stylesheet>
    <link href=/admin/css/corp_book/app.9a2436bd.css rel=stylesheet>
</head>

<body>

  <div class="container">
    <h1 style="font-size:24px; text-align:center;margin-bottom:25px;margin-top: 25px"><?php echo e($title); ?></h1>
    <div>
      <?php echo $text; ?>

    </div>
  </div>
   


<script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
<script>
function replaceSrc()
{
    var images = document.getElementsByTagName('img');
 
    for(var i = 0; i < images.length; i++)
    {
        var img = images[i];
        var link = 'https://bp.jobtron.org/corp_book/';
       
        if(img.src .substring(link.length, 0) == link)
        {
          img.src = img.src.replace(link, '/');
        }
    }
}


window.onload = replaceSrc;
</script>

  
</body>

</html><?php /**PATH /var/www/job/resources/views/admin/corp_book/opened_corp_book.blade.php ENDPATH**/ ?>