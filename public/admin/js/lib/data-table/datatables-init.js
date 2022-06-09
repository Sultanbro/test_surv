(function ($) {
    //    "use strict";


    /*  Data Table
    -------------*/


    $('#report-table').DataTable({
        scrollX: true,
        //dom: 'lBfrtip',
        language: {
            "processing": "Подождите...",
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные",
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }
        },
        buttons: [
        //    'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        fixedColumns:   {
            leftColumns: 2,
            rightColumns: 1
        },
        paging:   false,
        ordering: false,
        info:     false
    });

 

    $('#report-table-new').DataTable({
        scrollX: true,
        //dom: 'lBfrtip',
        language: {
            "processing": "Подождите...",
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные",
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }
        },
        lengthMenu: [[50, 100], [50, 100]],
        order: [[ 0, "desc" ]],
        buttons: [
            {
                text: 'Reload',
                action: function ( e, dt, node, config ) {
                    dt.ajax.reload();
                }
            }
        ],
        //bFilter: false,
        fixedColumns: {
            leftColumns: 1
        },
    });


    $('#report-table-asterisk').DataTable({
        scrollX: true,
        //dom: 'lBfrtip',
        language: {
            "processing": "Подождите...",
            // "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные",
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }
        },
        lengthMenu: [[50, 100], [50, 100]],
        order: [[ 0, "desc" ]],
        buttons: [
            //    'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        bFilter: false,
        paging: false,
        fixedColumns: {
            leftColumns: 1
        }
    });

    $('#bootstrap-data-table').DataTable({
        scrollX: true,
        //dom: 'lBfrtip',
        language: {
            "processing": "Подождите...",
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные",
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }
        },
        lengthMenu: [[10, 20, 50], [10, 20, 50]],
        buttons: [
        //    'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        //bFilter: false,
		serverSide: true,
        ajax: {
            "url": $('#bootstrap-data-table').data('url'),
            "type": "POST",
            "data": function ( d ) {
                if($("#ajax-filter").length) {
                    if($("#ajax-filter").hasClass('first')) {
                        $("#ajax-filter").removeClass('first')
                    } else {
                        var formData = $("#ajax-filter").serializeArray();
                        $.each(formData, function (i, field) {
                            d[field.name] = field.value;
                        });
                    }
                }

                var data = $('table.ajax').data();
                for(var key in data){
                    d[key] = data[key];
                }

                d['_token'] = $('meta[name="csrf-token"]').attr('content');
                d['year'] = $('select[name=year]').val();
                d['month'] = $('select[name=month]').val();
                d['user_id'] = $('#user_ids').val();
            }
        },
        fixedColumns:   {
            leftColumns: 2,
            rightColumns: 1
        }
    });

})(jQuery);