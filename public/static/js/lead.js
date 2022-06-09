var ajaxlidtable = $('#ajax-lid').DataTable({
  "bStateSave": true,
  "scrollX": false,
  "dom": '<"class.scrooltable"t><"dataTableNav"ipl><"clear">',
  "bLengthChange": true,
  "bFilter": true,
  "bInfo": true,
  "ordering": false,
  "language": {
    "sZeroRecords": "Тут появится статистика, после первого запуска рассылки",
    "info": "Всего: _MAX_",
    "infoEmpty": "Всего: 0",
    "paginate": {
      "previous": '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
      'next': '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
    },
    "lengthMenu": "На странице: _MENU_",
    "processing": "Загрузка"
  },
  "paging": true
});
