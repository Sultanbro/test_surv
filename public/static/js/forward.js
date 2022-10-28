$(function () {

    $("input[name=create]").click(function (e) {
        if(parseInt($('#balance_value').html()) < parseInt($('.js-sms-forward').html())) {
            $('#balance-modal').modal();
            e.preventDefault();
        }
    });


    $("#sms-forward-form").submit(function (e) {
        if ($('input[name=group_id]').val() == "") {
            $('#group-modal').modal();
            e.preventDefault();
        }
        // if ($("input[name=direction]").val() == "") {
        //     $(".directions").show();
        //     $(".directions").css("border", "red 1px solid");
        //      e.preventDefault();
        // } else {
        //     $(".directions").css("border", "none");
        // }

        let start_time = parseInt($('input[name=start_time]').val().replace(':', ''));
        let end_time = parseInt($('input[name=end_time]').val().replace(':', ''));

       console.log('start_time',start_time);
        console.log('end_time',end_time);

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

    function fillCallRepatInfoTwo() {
        if ($(".la_target").val() == 'weekly') {
            $('input[name=sms_repeat_info]').val($('.day_wek input:checked').map(function() {
                return this.value;
            }).get().join(','));
        } else if ($(".la_target").val() == 'monthly') {
            $('input[name=sms_repeat_info]').val($('.day_month input:checked').map(function() {
                return this.value;
            }).get().join(','));
        } else {
            $('input[name=sms_repeat_info]').val('');
        }
    }

    $('.day input').click(function() {
        fillCallRepatInfoTwo();
    });


    // Выделяем radio button по двойному нажатии и сохраняем
    // $('.modal table td:nth-child(2)').dblclick(function() { $(this).parent().find('input').prop("checked", true);
    //  $('.modal.in .btn-save').click();
    //                                                      });

    $('input[name=time_start]').on('keyup', function () {
        $('input[name=time_start]').get(0).setCustomValidity("");

    });



    $('input[name=end_time]').on('keyup', function () {
        $('input[name=end_time]').get(0).setCustomValidity("");
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
                        forwardPrice(false);
                    } else {
                        e.preventDefault();
                    }
                },
                'json'
            );
        }, 0);
    });

    $('textarea[name=message]').trigger('keyup');

    $('input[name=latin]').change(function () {
        $('textarea[name=message]').trigger('keyup');
    });
    
    $('#group-modal .btn-save').click(function (e) {
        $('#group-modal').modal('toggle');
        count_contacts = 0;
        let namecont = '';
        $.each(group_input_array, function(index,input) {
            
            count_contacts += parseInt($(input).data('count'));
            namecont+= $(input).parent().siblings('td').first().text()+', ';

        })

        if (group_name_array == '') {
            $('.selecthref').html('Выбрать отдел контактов');
        } else {
            $('.selecthref').html(group_name_array.join(","))
        }

        count_contacts = String(count_contacts);




        $('input[name=group_id]').val(group_id_array);
        $('input[name=group_id]').data('count', count_contacts);

        forwardPrice();
    });

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
        if($(this).data('action') == 'unsublink'){
            $('textarea[name=message]').val($('textarea[name=message]').val() + ' [Отписка]');
        }
    });

    $('.js-update-status-sms').click(function (e) {
        $.post(
            '/sms/status/' + $(this).data('id'),
            {
                status: $(this).data('status')
            }, function (data) {
                window.location.reload();
            },
            'json'
        );
    });

    forwardPrice();

});



$('#unikname').click(function(){
		if ($('#unikname').is(':checked')) {
			$('.multi_name').slideDown();
		} else {
			$('.multi_name').slideUp();
		}
	});
  $('.detailpriceon').click(function(){
    $('.detailsmsall').slideToggle();
  });


function forwardPrice(ajax = true) {
    group_id = $('input[name=group_id]').val();
    let count_sms = parseInt($('.count_sms').text().match(/\d/g));
    var price = $('.js-sms-forward').attr('price');

    if(!ajax && (price > 0)) {
        $('.js-sms-forward').text(price * count_sms);
    } else if(group_id){
        $.get('/sms/cost/', {group_id: group_id}, function (data) {
            price = parseInt(data.cost) * count_sms;

            $('.js-sms-forward').data('price', data.total);
            $('.js-sms-forward').text(data.total * count_sms);
            $('input[name=total_cost]').val(data.total * count_sms);

            // Детализация смс
            var details = Object.keys(data.details).map(function(key) {
                return [Number(key), data.details[key]];
            });
            var ru_details = details.filter(function(number) {
                return number[0].toString().startsWith('89')
            });
            var kz_details = details.filter(function(number) {
                return !number[0].toString().startsWith('89')
            });
            // console.log(data.details.length);
      //      console.log('RU detail');
      //      ru_details.forEach(function(val) {
      //        console.log(val[0]+' - '+val[1])
      //      });
      //      console.log('KZ detail');
      //      kz_details.forEach(function(val) {
      //        console.log(val[0]+' - '+val[1])
      //      });

            var pricekz = 0;
            var priceru = 0;
            kz_details.forEach(function(val) {
              pricekz = val[1];
              return false;
            });
            ru_details.forEach(function(val) {
              priceru = val[1];
              return false;
            });

            $('.detailcountsms').text(count_sms);
            $('.countcontakt').text(data.countcontakt);
            $('.countkz').text(kz_details.length);
            $('.countru').text(ru_details.length);
            $('.pricekz').text(pricekz);
            $('.priceru').text(priceru);
            $('.countsms').text(count_sms);
            $('.countkzall').text((count_sms * parseInt(kz_details.length)) * parseInt(pricekz));
            $('.countruall').text((count_sms * parseInt(ru_details.length)) * parseInt(priceru));
if (kz_details.length > 0) {
  $('.trkz').show();
} else {
  $('.trkz').hide();
}
if (ru_details.length > 0) {
  $('.trru').show();
} else {
  $('.trru').hide();
}

        },);
    }

  }
