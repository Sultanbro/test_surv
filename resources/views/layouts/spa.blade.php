<!DOCTYPE html>
<html style="font-size:10px;">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="">

    <title>@yield('title')</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico?ver1.2"/>

    <title>@yield('title')</title>

    <meta name="description" content="Jobtron">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/admin/css/normalize.css">
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin/css/custom.css">
    <link rel="stylesheet" href="/admin/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/croppie.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Open+Sans:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('/css/admin/app.css') }}">

    @yield('head')
    @yield('styles')
</head>

<body ontouchstart="">
    <div class="right-panel-app"></div>
    <div class="hidden">@yield('content')</div>

    <script>

        let switchTabs = (tab) => {
            // get all tab list items and remove the is-active class
            let tabs = tab.closest('.tabs').querySelectorAll('.tab__item');
            tabs.forEach(t => {t.classList.remove("is-active");});
            // set is-active on the passed tab element
            tab.classList.add("is-active");
            // get all content elements and remove is-active
            let contents = tab.closest('.tabs').querySelectorAll(".tab__content .tab__content-item");
            contents.forEach(t => {t.classList.remove("is-active");});
            // get the data-index data attribute from the selected tab (passed here)
            let activeTabIndex = tab.getAttribute("data-index");
            // get the corresonding tab content via the data-content attribute
            let activeContent = tab.closest('.tabs').querySelector(`[data-content='${activeTabIndex}']`);
            // set is-active on the corresponding tab content
            activeContent.classList.add("is-active");
        }

        let switchTabsInclude = (tab) => {
            // get all tab list items and remove the is-active class
            let tabs = tab.closest('.tabs__include').querySelectorAll('.tab__item-include');
            tabs.forEach(t => {t.classList.remove("is-active");});
            // set is-active on the passed tab element
            tab.classList.add("is-active");
            // get all content elements and remove is-active
            let contents = tab.closest('.tabs__include').querySelectorAll(".tab__content-include .tab__content-item-include");
            contents.forEach(t => {t.classList.remove("is-active");});
            // get the data-index data attribute from the selected tab (passed here)
            let activeTabIndex = tab.getAttribute("data-index");
            // get the corresonding tab content via the data-content attribute
            let activeContent = tab.closest('.tabs__include').querySelector(`[data-content='${activeTabIndex}']`);
            // set is-active on the corresponding tab content
            activeContent.classList.add("is-active");
        }


    </script>

    <script>
        window.Laravel = @json($laravelToVue);
    </script>
       @yield('scripts')
    <script src="{{ url('/js/croppie.js') }}"></script>
    <script src="{{ url('/js/croppie.min.js') }}"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
