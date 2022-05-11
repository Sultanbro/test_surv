let delay = (function() {
  let timer = 0;
  return function(callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();

$(function() {
  //



  $(".showrent_numbers").on("click", function(e) {
    e.preventDefault();
    $('.formrent_numbers').slideDown();
    $('html, body').stop().animate({
      scrollTop: $('.formrent_numbers').offset().top
    }, 777);
  });

  $(".tooglenotifi, .bgpanel").on("click", function(e) {
    e.preventDefault();
    $('.kolokolchik .panel').toggle();
    $('.bgpanel').toggleClass('active');
  });

  $(".panel_head .panel_in").on("click", function(e) {
    e.preventDefault();
    $('.panel_head .panel_in, .panel_body .panel_out').removeClass('active');
    $(this).addClass('active');
    var numb = $(this).data('tab');
    $('.panel_out[data-id=' + numb + ']').addClass('active');
  });




  //роботs

  var tablerobot = $('#tablerobot').DataTable({
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




  //статистика
  var table = $('#ajax-datatable').DataTable({
    "bStateSave": true,
    "scrollX": false,
    "dom": '<"class.scrooltable"t><"dataTableNav"i<"sum">p<"toolbar">l><"clear">',
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
    "processing": true,
    "serverSide": true,
    "bDestroy": true,
    "paging": true,
    "ajax": {
      "url": $('#ajax-datatable').data('url'),
      "type": "POST",
      "data": function(d) {
        d['_token'] = $('meta[name="csrf-token"]').attr('content');

        var formData = $("#ajax-datatable-filter").serializeArray();
        $.each(formData, function(i, field) {
          if (field.name != 'excel') {
            d[field.name] = field.value;
          }
        });
      },
      "dataSrc": function(json) {
          var sum = json.sum;
          $(".sum").html("На сумму: " + sum);
        return json.data;
      }
    },
    "fnDrawCallback": function() {
      setTimeout(function() {
        $('audio.pleer').audioPlayer();
      }, 400);
    },

  });

  $("#ajax-datatable_wrapper .dataTableNav .toolbar").html('<a href="#" class="export" id="ajax-datatable-excel">Экспортировать в Excel</a>');

  $('#ajax-datatable-filter-apply').click(function() {
    $('#ajax-datatable').DataTable().ajax.reload();
  });

  $('#ajax-datatable-excel').click(function() {
    $('form#ajax-datatable-filter').submit();
  });


  //рассылка
  values = new Array();

  function smsbuttonsOnChange () {
    if (values.length > 0) {
      $('#activate_sms_sending').stop().fadeIn(0);
      $('#delete_sms_sending').stop().fadeIn(0);
    } else {
      $('#activate_sms_sending').stop().fadeOut(0);
      $('#delete_sms_sending').stop().fadeOut(0);
    }
  }
  
  var tablesms = $('#smslist').DataTable({
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
    "drawCallback": function(  ) {

        if($('.removeAll').is(":checked")){

            $('#smslist input[name=remove]').each(function(ind, el){

              values.push($(el).val());
              $(this).prop('checked', true);
              smsbuttonsOnChange();
            });

        } else {

            $('#smslist input[name=remove]').each(function(ind, el){

              values.splice(values.indexOf($(el).val()), 1);
              $(this).prop('checked', false);
              smsbuttonsOnChange();
            });

        }
        
        $('#smslist input[name=remove]').on('change', this, function(){

            if($(this).is(":checked")) {

              values.push($(this).val());
              smsbuttonsOnChange();
            } else if (!$(this).is(":checked")) {

              $('.removeAll').prop('checked', false);
              values.splice(values.indexOf($(this).val()), 1);
              smsbuttonsOnChange();
            }

        });
    },
  });
  $("#delete_sms_sending").on("click", function() {
    $.ajax({
      type: 'POST',
      url: '/sms/status',
      data: {
          values : values,
          status : -1
      },
      success: function(data) {
          window.location.href = '/sms/index/archive';
      }
    });
  });
  $("#activate_sms_sending").on("click", function() {
    $.ajax({
      type: 'POST',
      url: '/sms/status',
      data: {
          values : values
      },
      success: function(data) {
          window.location.href = '/sms/index';
      }
    });
  });
  $('#sms-forward-form input[type=submit]').click(function() {
    console.log('test');
  });


  //смс редактирвоание

  // $('#datepicker').datetimepicker({
  //   format: 'DD.MM.YYYY',
  // });

  var gropupkont = $('#groupkont').DataTable({
    "bStateSave": false,
    select: true,
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
    "drawCallback": function(settings) {
      // Выделяем radio button по нажатии на имя второй яейки
      $(".modal table td:nth-child(2)").on("click", function() {
      //  $(this).parent().find('input').prop("checked", true);
      });
      // Выделяем radio button по двойному нажатии и сохраняем
      $(".modal table td:nth-child(2)").on("dblclick", function() {
      //  $(this).parent().find('input').prop("checked", true);
      //  $('.modal.in .save_choise').click();
      });



      $('#groupkont input').on('change', this, function(){



          if($(this).is(":checked")) {


          } else if (!$(this).is(":checked")) {


          }

      });

      //Сохранять радиокнопку активной при пагинации

      $.each($('input[name=group_id]'), function (ind, input) {

     //   if($(input).val() == $("input[name=selected_group]").val()) {
     //     $(input).attr('checked', true)
     //   } else {
     //     $(input).attr('checked', false)
     //   };

      })
    }
  });


  //profile table list unik

  var unik_list_table = $('#unik_list_table').DataTable({
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

  // autocals
  function autocallbuttonsOnChange () {
    if (values.length > 0) {
      $('#activate_autocall_sending').stop().fadeIn(0);
      $('#delete_autocall_sending').stop().fadeIn(0);
    } else {
      $('#activate_autocall_sending').stop().fadeOut(0);
      $('#delete_autocall_sending').stop().fadeOut(0);
    }
  }
  var autocall_list_table = $('#autocall_list_table').DataTable({
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
    "drawCallback": function(  ) {

        if($('.removeAll').is(":checked")){

            $('#autocall_list_table input[name=remove]').each(function(ind, el){

              values.push($(el).val());
              $(this).prop('checked', true);
              autocallbuttonsOnChange();
            });

        } else {

            $('#autocall_list_table input[name=remove]').each(function(ind, el){

              values.splice(values.indexOf($(el).val()), 1);
              $(this).prop('checked', false);
              autocallbuttonsOnChange();
            });

        }
        
        $('#autocall_list_table input[name=remove]').on('change', this, function(){

            if($(this).is(":checked")) {

              values.push($(this).val());
              autocallbuttonsOnChange();
            } else if (!$(this).is(":checked")) {

              $('.removeAll').prop('checked', false);
              values.splice(values.indexOf($(this).val()), 1);
              autocallbuttonsOnChange();
            }

        });
    },
  });

  $("#delete_autocall_sending").on("click", function() {
    $.ajax({
      type: 'POST',
      url: '/autocalls/status',
      data: {
          values : values,
          status : -1
      },
      success: function(data) {
          window.location.href = '/autocalls/index/archive';
      }
    });
  });
  $("#activate_autocall_sending").on("click", function() {
    $.ajax({
      type: 'POST',
      url: '/autocalls/status',
      data: {
          values : values
      },
      success: function(data) {
          window.location.href = '/autocalls/index';
      }
    });
  });



  // редактирвоание или создание автозвонка

  var list_group = $('#list_group').DataTable({
    "bStateSave": false,
    "pageLength": 10,
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
    "drawCallback": function(settings) {

    }
  });


  var audio_list = $('#audio_list').DataTable({
    "bStateSave": false,
    "scrollX": false,
    "pageLength": 5,
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
    "drawCallback": function(settings) {


    }
  });






  // scroll contact


  $(".contacts-holder .asideleft").mCustomScrollbar({
    axis: "y",
    theme: "dark-3",
    advanced: {
      autoExpandHorizontalScroll: true
    },
    scrollButtons: {
      enable: true,
      scrollType: "stepped"
    },
    scrollInertia: 400
  });


  // отправка жалобы или пожелания, форма в панели вверху

  $('.form_complaint').submit(function(e) {
    var data = {};
    var valid = true;
    data.sms = $(".form_complaint #sms").val();
    data.account = $(".panel .id").text();
    data.type = "form1";
    if (data.sms.length <= 5) {
      $(".form_complaint #sms").addClass("invalid");
      valid = false;
    }
    if (valid) {
      $.ajax({
        type: "POST",
        url: "/send.php",
        data: data,
        success: function(data) {
          $(".form_complaint #sms").val("");
          $(".form_complaint #sms").removeClass("invalid");
          $('#complaint').modal('hide')
          alert('Сообщение отправленно!');
        }
      });
    }
    e.preventDefault();
  });

  // search faq
  $('.search-input').jcOnPageFilter({
    parentSectionClass: 'sectionflow',
    parentLookupClass: 'rowflow',
    childBlockClass: 'searchable',
  });



  $('.search-input').keyup(function() {
    if (!$(this).val()) {
      $('.panel-group .collapse').collapse('hide');
    } else {
      $('.panel-group .collapse').collapse('show');
    }
  });



  // /partner/edit скрываем и раскрываем реквизиты

  $('#chek_requisites_ur').click(function() {
    if ($('#chek_requisites_ur').is(':checked')) {
      $('.requisitesur').slideDown();
    } else {
      $('.requisitesur').slideUp();
    }
  });

  $('#chek_requisites_fiz').click(function() {
    if ($('#chek_requisites_fiz').is(':checked')) {
      $('.requisitesfiz').slideDown();
    } else {
      $('.requisitesfiz').slideUp();
    }
  });





  function scrooltable() {

    $(".scrooltable").mCustomScrollbar({
      axis: "x",
      theme: "rounded-dark",
      scrollInertia: 500,
      mouseWheel: false,
      callbacks: {
        whileScrolling: function() {
          if (this.mcs.leftPct > 5) {
            $(".buttonLeft").fadeIn();
          } else if (this.mcs.leftPct < 5) {
            $(".buttonLeft").fadeOut();
          }

          if (this.mcs.leftPct < 95) {
            $(".buttonRight").fadeIn();
          } else if (this.mcs.leftPct > 95) {
            $(".buttonRight").fadeOut();
          }
        }
      },
      scrollButtons: {
        enable: true,
        scrollType: "stepped"
      },
    });
  }
  scrooltable();





  // stat partner

  var stat_partner = $('#stat_partner').DataTable({
    "bStateSave": true,
    "scrollX": false,
    "dom": '<"class.scrooltable"t><"dataTableNav"><"clear">',
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
    "drawCallback": function() {
      scrooltable();
    },
  });



  var stat_partner2 = $('#stat_partner2').DataTable({
    "bStateSave": false,
    "scrollX": false,
    "dom": '<"class.scrooltable"t><"dataTableNav"ipl><"clear">',
    "bLengthChange": true,
    "bFilter": true,
    "pageLength": 50,
    "bInfo": true,
    "ordering": true,
    "order": [
      [8, "desc"]
    ],
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
    "drawCallback": function() {
      scrooltable();
    },
  });


  // для загрузка файлов
  (function(document, window, index) {
    var inputs = document.querySelectorAll('.inputfile');
    Array.prototype.forEach.call(inputs, function(input) {
      var label = input.nextElementSibling,
        labelVal = label.innerHTML;

      input.addEventListener('change', function(e) {
        var fileName = '';
        if (this.files && this.files.length > 1)
          fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
        else
          fileName = e.target.value.split('\\').pop();

        if (fileName)
          label.querySelector('span').innerHTML = fileName;
        else
          label.innerHTML = labelVal;
      });

      // Firefox bug fix
      input.addEventListener('focus', function() {
        input.classList.add('has-focus');
      });
      input.addEventListener('blur', function() {
        input.classList.remove('has-focus');
      });
    });
  }(document, window, 0));

});
