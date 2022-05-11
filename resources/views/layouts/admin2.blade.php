<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Mediasend.kz Управление</title>
        <meta name="description" content="Mediasend.kz Управление">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="apple-touch-icon" href="apple-icon.png">
        <link rel="shortcut icon" href="favicon.ico">

        <link rel="stylesheet" href="/admin/css/normalize.css">
        <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
        <link rel="stylesheet" href="/admin/css/font-awesome.min.css">
        <link rel="stylesheet" href="/admin/css/themify-icons.css">
        <link rel="stylesheet" href="/admin/css/flag-icon.min.css">
        <link rel="stylesheet" href="/admin/css/cs-skin-elastic.css">
        <!-- <link rel="stylesheet" href="admin/css/bootstrap-select.less"> -->
        <link rel="stylesheet" href="/admin/css/lib/datatable/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="/admin/css/lib/datatable/fixedColumns.bootstrap4.min.css">
        <link rel="stylesheet" href="/admin/css/lib/chosen/chosen.min.css">

        <link rel="stylesheet" href="/admin/css/style.css?ver1.4">
        <link rel="stylesheet" href="/admin/css/custom.css">

        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

        <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
<style>
    body{
        padding: 0!important;
    }
</style>
    </head>
    <body>
        <!-- Left Panel -->
            <div class="right-panel-app">
                @yield('content')
                </div>
<style>
    .modal-open .modal{
        opacity: 1;
    }
    .listbook li a:hover, .listbook li a:focus{
        color:#007bff!important;
    }
    .modal-backdrop.fade {
        opacity: 0.3;
    }
    body, html, #app{
        height: 100%;
    }
</style>
        <div class="modal fade" id="animmodal" tabindex="-1" role="dialog" aria-labelledby="animmodal"
             aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="animmodaltitle">Сохраненно</h5>
                    </div>
                    <div class="modal-body">

                        Описание успешно сохраненно
                    </div>
                    <div class="modal-footer">


                    </div>
                </div>
            </div>
        </div>
        <script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
        <script src="/admin/js/popper.min.js"></script>
        <script src="/admin/js/plugins.js"></script>
        <script src="/admin/js/main.js?1.11"></script>
        <script src="/admin/js/lib/data-table/datatables.min.js"></script>
        <script src="/admin/js/lib/data-table/dataTables.fixedColumns.min.js"></script>
        <script src="/admin/js/lib/data-table/dataTables.bootstrap.min.js"></script>
        <script src="/admin/js/lib/data-table/dataTables.buttons.min.js"></script>
        <script src="/admin/js/lib/data-table/buttons.bootstrap.min.js"></script>
        <script src="/admin/js/lib/data-table/jszip.min.js"></script>
        <script src="/admin/js/lib/data-table/pdfmake.min.js"></script>
        <script src="/admin/js/lib/data-table/vfs_fonts.js"></script>
        <script src="/admin/js/lib/data-table/buttons.html5.min.js"></script>
        <script src="/admin/js/lib/data-table/buttons.print.min.js"></script>
        <script src="/admin/js/lib/data-table/buttons.colVis.min.js"></script>
        <script src="/admin/js/lib/data-table/datatables-init.js?v=16"></script>
        <script src="/admin/js/lib/chosen/chosen.jquery.min.js"></script>
        <script src="/admin/js/custom.js"></script>
        <script src="/js/app2.js?ver4231"></script>
    </body>
</html>
