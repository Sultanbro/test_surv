<script type="text/javascript">
$(document).ready(function () {
    $('.tooglenotifi').click(function() {
        $('.kolokolchik .panel').toggle();
        $('.bgpanel').toggleClass('active');
    })
});
$(".bgpanel").on("click", function(e) {
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




$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});



/**
  Сохранить причину отсутствия и дать оценку руководителю AJAX
 */
$('#setReadWithComment').click(function() {

  var select = $('#selectForNotification').val();


  if(select != null) {
    ajax_setRead({
      id: $('#idForNotification').val(), 
      comment: select,
    });
  } else {
    alert('Выберите причину!!!');
    return '';
  }
  
  
});

/**
  Основная функция для прочтения уведомления
 */
var noti_id = 0;

$('.set-read').click(function() {
  var id = $(this).data('id');
      noti_id = $(this).data('id');
  var comment = $(this).data('comment');
  var type = $(this).data('type');
  
  if(type == 'report') {
    $('#modalReport_noti_id').val(id);
    $('#setReadReportModal').fadeIn();
    return '';
  } else {
    if(comment == 1) {
      $('#idForNotification').val(id);
      $('#setReadCommentModal').fadeIn();
      return '';
    } 
  }
  
  ajax_setRead({id: id});
  
});

/**
  Отрктыть модалку для заполнения отчета
 */

$('.report-modal').click(function() {
  global_noti_id = $(this).parent().parent().find('.set-read').data('id');
  $('#modalReport_noti_id').val(global_noti_id);
  $('#setReadReportModal').fadeIn();
});

/**
  Сохранить отчет AJAX
 */
$('#saveReport').click(function() {

  if($('#modalReport_noti_id').val().length > 200) {
    alert('Наберите минимум 200 символов для сохранения!!!');
    return;
  }

  ajax_setRead({
      id: $('#modalReport_noti_id').val(), 
      comment: 0,
      type: 'report',
      text: $('#modalReport_text').val()
    });
});

/**
  Подсчет символов в textarea (Отчет)
 */
function count_char() {
  var length = $('#modalReport_text').val().length;
  $('.char_counter').text(length + ' / 200');
}



/**
  Открыть модалку для переноса обучения 
 */ 

var global_noti_id = 0;
$('.transfer-training').click(function() {
  $('#transferTrainingModal').fadeIn();

  let user_id = $(this).data('userid');

  global_noti_id = $(this).parent().parent().find('.set-read').data('id');
  $('#userIdForTransfer').val(user_id);
});

/**
  Перенос обучения (дня стажировки на другой день) AJAX
 */ 
$('#transferTraining').click(function() {
    let user_id = $('#userIdForTransfer').val(),
        date = $('#dateForTransfer').val(),
        time = $('#timeForTransfer').val();
    console.log(global_noti_id);
    let noti_id = global_noti_id;
    
    $.ajax({
        url: '/notifications/set-read/',
        type: 'POST',
        dataType: 'JSON',
        data: {
          user_id: user_id,
          date: date,
          time: time,
          id: noti_id,
          type: 'transfer',
        },
        success: function(response) {
          if(response == 1) {
            
            $('#unnoti' + noti_id).slideUp();
            $('#transferTrainingModal').fadeOut();
          } 
          if(response == 'LeadNotFound') {
            alert('Не удалось перенести обучение. Не найден лид стажера');
          }
          if(response == 'DealNotFound') {
            alert('Не удалось перенести обучение. Не найдена сделка стажера');
          }
          
        }
    });

});

/**
  Просто ajax функция
 */
function ajax_setRead(sdata) {
  $.ajax({
      url: '/notifications/set-read/',
      type: 'POST',
      dataType: 'JSON',
      data: sdata,
      success: function(data) {
        if(data == 1) {
          $('#unnoti' + sdata.id).slideUp();
          $('#setReadCommentModal').fadeOut();
          $('#setReadReportModal').fadeOut();
          
          nullify(dateForTransfer);

        }
      }
  });
}

/**
  Кнопка закрывающая модалку
 */
$('.hidemodals').click(function() {
  $('.modal').fadeOut();
  //$('#setReadCommentModal').fadeOut();
  //$('#setReadReportModal').fadeOut();
  nullify();
});

function nullify() {
  $('#dateForTransfer').val('');
  $('#timeForTransfer').val('09:30');
  $('#selectForNotification').val('');

  $('#modalReport_text').val('');
}

$('.modal-dialog').on('hide', function(){
  $('.modal').fadeOut();
  nullify();
})

/**
  Отметить все уведомления проитанными
 */
$('#setRead').click(function() {
  $.ajax({
      url: '/notifications/set-read-all/',
      type: 'POST',
      dataType: 'JSON',
      data: {},
      success: function(data) {
        if(data == '1') {
          $('.unread_noti').slideUp();
        }
      }
  });
});
</script>
<style>
#setReadCommentModal,
#setReadReportModal,
#transferTrainingModal { 
    z-index: 10000;
    background: rgba(0,0,0,0.45);
}
.checked {
  color: orange;
}
#rating {
  position: relative;
}
#rating span {
  display: inline-block;
    position: relative;
}
#rating span:after {
  position: absolute;
    bottom: 105%;
    left: 16%;
    font-size: 11px;
    font-family: 'Open Sans';
}
#rating span:nth-child(1):after {content: "1";}
#rating span:nth-child(2):after {content: "2";}
#rating span:nth-child(3):after {content: "3";}
#rating span:nth-child(4):after {content: "4";}
#rating span:nth-child(5):after {content: "5";}
#rating span:nth-child(6):after {content: "6";}
#rating span:nth-child(7):after {content: "7";}
#rating span:nth-child(8):after {content: "8";}
#rating span:nth-child(9):after {content: "9";}
#rating span:nth-child(10):after {content: "10";}

/** */
#modalReport_text {
  min-height: 120px;
}
.char_counter {
  display: block;
  margin-bottom: 15px;
}
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<style>
  .select2-container {z-index: 20000;}
.select2 {width: 100% !important;margin-bottom: 8px;}
#rating {margin-top: 16px !important;display: block;}
#rating span {cursor: pointer;}
.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #868e96;
    text-align: left;
}
.notification-text .btn {
  margin: 0;
  padding: 0px 5px;
    line-height: 19px;
    font-size: 12px;
}
</style>

<style>
      a {
        cursor: pointer;
    }
    .notification-text a.btn {
        color: #fff;
    }
    .kolokolchik .panel .panel_body .notification_list .notification_item {
        overflow: unset;
    }
    .kolokolchik .panel .panel_body .notification_list .notification_item:hover .notification-change {

        animation: 2s ease-in anim_pulse infinite;
    }
    @keyframes  anim_pulse {
        0% {
            box-shadow: 0 0 0px 0 #6ecb15;
        }
        60% {
            box-shadow: 0 0 5px 3px #6ecb15;
        }
        70% {
            box-shadow: 0 0 10px 3px #6ecb15;
        }
        66% {
            box-shadow: 0 0 0px 0 #6ecb15;
        }
    }
</style>