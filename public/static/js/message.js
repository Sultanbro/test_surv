$(function() {
  //sonic

  //message
  var messagelist = $('#messagelist').DataTable({
    "bStateSave": true,
    "scrollX": false,
    "dom": '<"class.scrooltable"t><"dataTableNav"p><"clear">',
    "bLengthChange": true,
    "bFilter": true,
    "bInfo": true,
    "ordering": true,
    "language": {
      "sZeroRecords": "Ничего не найдено",
      "info": "Всего: _MAX_",
      "infoEmpty": "Всего: 0",
      "paginate": {
        "previous": '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
        'next': '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
      },
      "lengthMenu": "На странице: _MENU_",
      "processing": "Загрузка"
    },
    "paging": true,
  });


  $('input[name=start_time]').on('keyup', function() {
    $('input[name=start_time]').get(0).setCustomValidity("");
  });

  $('input[name=end_time]').on('keyup', function() {
    $('input[name=end_time]').get(0).setCustomValidity("");
  });


  $('#message-form').submit(function(e) {

    if ($('input[name=selected_group]:checked').val() == "") {
      $('#group-modal').modal();
      e.preventDefault();
    }
    // if ($('input[name=group_id]:checked').val() == undefined) {
    //   $('#group-modal').modal();
    //   e.preventDefault();
    // }

    let start_time = parseInt($('input[name=start_time]').val().replace(':', ''));
    let end_time = parseInt($('input[name=end_time]').val().replace(':', ''));

    if (start_time < end_time) {
      if (end_time - start_time < 500) {
        $('input[name=end_time]').css('border', '1px solid #f00');
        $('input[name=end_time]').get(0).setCustomValidity("Интервал рассылки должен быть не менее 5 часов!");
        $('input[name=end_time]').get(0).reportValidity();
        e.preventDefault();
      }
    } else{
      if (start_time - end_time >= 1900) {
        $('input[name=end_time]').css('border', '1px solid #f00');
        $('input[name=end_time]').get(0).setCustomValidity("Интервал рассылки должен быть не менее 5 часов!");
        $('input[name=end_time]').get(0).reportValidity();
        e.preventDefault();
      }
    }
  });



  $('body').on('keyup', 'textarea[name=message]', function (e) {
    let self = $(this);
    delay(function () {
      $.post(
          '/autocalls/length',
          {
            message: self.val(),
            latin: $('input[name=latin]:checked').length
          }, function (data) {
            if (data.length <= 2000) {
              self.parent().find('em span').eq(0).text(data.length);
              self.parent().find('em span').eq(1).text(data.count_sms * data.length_sms);
              $('.count_sms').text('(' + data.count_sms + ' смс)');

            } else {
              e.preventDefault();
            }
          },
          'json'
      );
    }, 0);
  });

  $('textarea[name=message]').trigger('keyup');

  $('.nameInMessage').click(function(){
    if($(this).data('action') == 'name'){
      $('textarea[name=message]').val($('textarea[name=message]').val() + ' [Имя]');
    }
    if($(this).data('action') == 'patronymic'){
      $('textarea[name=message]').val($('textarea[name=message]').val() + ' [Отчество]');
    }
    if($(this).data('action') == 'info1'){
      $('textarea[name=message]').val($('textarea[name=message]').val() + ' [Доп-1]');
    }
    if($(this).data('action') == 'info2'){
      $('textarea[name=message]').val($('textarea[name=message]').val() + ' [Доп-2]');
    }
    if($(this).data('action') == 'info3'){
      $('textarea[name=message]').val($('textarea[name=message]').val() + ' [Доп-3]');
    }

    $('textarea[name=message]').trigger('keyup');
  });

});