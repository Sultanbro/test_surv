<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Памятка</title>


    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">

    <link rel="stylesheet" href="/admin/css/font-awesome.min.css">

<style>
.bg-agrey {
    background: #fefefe;
}
.bg-grey {
    background: rgb(246 246 250);
}
.block {
    display: none;
}
.block.active {
    display: block;
}
.docs_sidebar ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
.docs_sidebar ul li {
    display: block;
    padding: 0.65em 0;
    white-space: nowrap;
}
.docs_sidebar ul li a {
    color: #232323;
    display: block;
    font-size: .875em;
    font-weight: 500;
    text-decoration: none;
    transition: transform .3s ease;
}
.docs_sidebar ul ul li a {
    font-weight: 400;
    line-height: 1.25;
    padding-left: 1em;
    position: relative;
}
.docs_sidebar ul ul li.active a:before {
    background: url(/img/icons/active_marker.min.svg) no-repeat 50%;
    content: "";
    height: 0.5rem;
    left: 0;
    position: absolute;
    top: 0.25em;
    width: 0.5rem;
}
.docs_sidebar ul li h2 {
    color: #232323;
    cursor: pointer;
    display: block;
    font-size: .875em;
    font-weight: 500;
    margin-bottom: 0;
    text-decoration: none;
    transition: all .3s ease;
}
.docs_sidebar ul .sub--on>h2 {
    margin-bottom: 1em;
}
aside {
    min-height: 100vh;
}
.logo {
    width: 160px;
}
body {
    font-family: 'Helvetica Neue';
}
.p-30 {
    padding: 30px;
}
.link {
    font-size: 14px;
    cursor: pointer;
}
.link:hover {
    color: blue;
}
.ttitle {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
    font-style: italic;
}
.welcome-img {
    box-shadow: 0 0 1px 10px inset #ececec;
    border: 2px solid #e6e6e6;
    padding: 12px;
}
</style>
</head>

<body>

   
    <div class="container-fluid bg-agrey">
        <div class="row">
            <aside class="col-3 bg-grey docs_sidebar p-30">

                <img src="https://admin.u-marketing.org/admin/images/logo.png" alt="" class="logo mb-2">
                <p class="ttitle"> Заметки ленивого разработчика</p>
                <ul>
                    <li class="link" data-page="1">Начисления</li>
                    <li class="link" data-page="2">Табель</li>
                    <li class="link" data-page="3">Аналитика</li>
                    <li class="link" data-page="4">Рекрутинг</li>
                    <li class="link" data-page="5">Настройки</li>
                </ul>
            </aside>
            <div class="col-9 p-30">



                <div class="block active" id="block-1">

                    <h2>Начисления</h2>
                    
                    <img src="/docs/pages/salary.jpeg" alt="" class="img-fluid mb-3 welcome-img">

                    <p>1. Выбор группы</p>
                    <p>2. Выбор месяца</p>
                    <p>3. Выбор года</p>
                    <p>4. Обновить страницу</p>
                    <p>5. Экспорт начислений группы за выбранный период</p>
                    <p>6. Скрыть / Показать некоторые поля в таблице</p>
                    <p>7. Доступ к группе</p>
                    <p>8. Инфо: начисления без вычета штрафов и авансов</p>
                    <p>9. Фильтр по сотрудникам</p>
                    <p>10. Кол-во сотрудников / ставки</p>
                    <p>11. До подтвержения: кнопка  После: кто и когда нажимал</p>
                    <p>Начисления нужно подтверждать в начале месяца</p>
                    
                    <p><b>Кроны</b>    </p>
                    <p>DailySalaryUpdate - Cоздает записи в Salary</p>
                    <p>SaveGroupSalary - создает записи в GroupSalary</p>
                    <p>SaveUserKpi - создает записи в SavedKpi</p>
                </div>

                <div class="block" id="block-2">
                    <h2>Табель</h2>
            
                </div>

                <div class="block" id="block-3">
                    <h2>Аналитика</h2>
   
                </div>

                <div class="block" id="block-4">
                    <h2>Рекрутинг</h2>
   
                </div>

                <div class="block" id="block-5">
                    <h2>Настройки</h2>
       
                </div>
            </div>
        </div>
    </div>



    <script src="/admin/js/vendor/jquery-2.1.4.min.js"></script>
    

    <script>
        $('.link').click(function() {
            let id = $(this).data('page');
            
            $('.block').removeClass('active');
            $('#block-' + id).addClass('active');

        });
    </script>
</body>
</html>