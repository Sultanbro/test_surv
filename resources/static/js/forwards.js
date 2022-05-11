jQuery(function($){



    $('.nameInMessage').click(function(){
        action = $(this).data('action');
        value = $('textarea[name=message]').val();
        if(action == 'name'){
            $('textarea[name=message]').val('[Имя] ' + value);
        }
        if(action == 'patronymic'){
            $('textarea[name=message]').val('[Отчество] ' + value);
        }
    });


    if($('.dispatch-instruction').size() > 0)
        forwardPrice();

    $('input[name=create], input[name=draft], input[name=save]').click(function(){
        $('input[name=create], input[name=draft], input[name=save]').removeClass('click');
        $(this).addClass('click');
    });
    
    $('input[name=time_start]').on('keyup',function () {
        $('input[name=time_start]').get(0).setCustomValidity("");
    });

    $('input[name=end_time]').on('keyup', function () {
        $('input[name=end_time]').get(0).setCustomValidity("");
    });


    $('#createForward, #editForward').submit(function(e){

        error = 0;
        var error_elem = null;
        //message = kemoji.getValue(); // Это для смайлов
        message = $('textarea[name=message]').val();

        name = $.trim($('input[name=name]').val());
        start_time = $.trim($('input[name=start_time]').val());
        end_time = $.trim($('input[name=end_time]').val());

        $('input[name=name], textarea[name=message], .selectContacts, input[name=start_time], input[name=end_time], textarea[name=message], input[name=time_start]').css('border', 'none')

        if(name == ''){
            error_elem = $('input[name=name]');
            $('input[name=name]').css('border', '1px solid #f00');
            error++;
        }

        if(start_time == ''){
            error_elem = $('input[name=start_time]');
            $('input[name=start_time]').css('border', '1px solid #f00');
            error++;
        }

        if(end_time == ''){
            error_elem = $('input[name=end_time]');
            $('input[name=end_time]').css('border', '1px solid #f00');
            error++;
        }

        if(message == ''){
            error_elem = $('textarea[name=message]');
            $('textarea[name=message]').css('border', '1px solid #f00');
            error++;
        }
        time_start = parseInt($('input[name=time_start]').val().replace(':', ''));
        start_time = parseInt($('input[name=start_time]').val().replace(':', ''));
        end_time = parseInt($('input[name=end_time]').val().replace(':', ''));

        if((time_start > end_time || time_start < start_time) && error == 0){
            error_elem = $('input[name=time_start]');
            $('input[name=time_start]').css('border', '1px solid #f00');
            
            if(time_start < start_time) {
                $('input[name=time_start]').get(0).setCustomValidity('Время начала должен лежать в интервале рассылки!');
                $('input[name=time_start]').get(0).reportValidity();
            } else if(time_start > end_time) {
                $('input[name=time_start]').get(0).setCustomValidity('"Время начала" должно быть меньше времени окончания "Интервала рассылки"');
                $('input[name=time_start]').get(0).reportValidity();
            }
            error++;
        }

        /*if(message == ''){
            $('textarea[name=message]').css('border', '1px solid #f00');
            error++;
        }
        $('textarea[name=message]').val(message);*/

        if($('input[name=group]:checked').size() == 0){
            error_elem = $('.selectContacts');
            $('.selectContacts').css('border', '1px solid #f00');
            error++;
        }

        if(end_time - start_time < 500 && error == 0){
            error_elem = $('input[name=end_time]');
            $('input[name=end_time]').css('border', '1px solid #f00');
            $('input[name=end_time]').get(0).setCustomValidity("Интервал рассылки должен быть не менее 5 часов!");
            $('input[name=end_time]').get(0).reportValidity();
            error++;
        }

        price = parseFloat($('.forwardPrice').text());
        balance = parseFloat($('.balanse em').text());
        modal = $('.fancybox-inner #balance').size();
        if(price > balance && error == 0 && modal == 0 && ($('input[name=create]').hasClass('click') || $('input[name=save]').hasClass('click'))){
            error++;
            $('a[data-fancybox=balance]').trigger('click');
        }

        if(modal == 1){
            $('input[name=balance]').val(1);
        }


        if(error > 0){
            $('html, body').animate({ scrollTop: error_elem.offset().top}, 1000);
            e.preventDefault();
        }

    });
    
    $('#balance button').click(function(){
        $('#createForward input[name=create], #editForward input[name=save]').trigger('click');
    });

    $('body').on('keydown keypress', function(e){
        /*var e = e || event, k = e.which || e.button;

        if(e.ctrlKey && k == 86) return false;*/
    });
    
    /*$('body').on('keyup', 'textarea[name=message]', function(e){

        val = $(this).val();
        reg = / \$\#[A-Z0-9]*\#\$ /g;
        val = val.replace(reg, ':-)');
        reg = /&nbsp;/g;
        val = val.replace(reg, ' ');
        reg = /<br>/g;
        val = val.replace(reg, ' ');
        length = val.length;

        var simbols_in_one_sms = 70;

        if($('input[name=latin]:checked').length) {
            simbols_in_one_sms = 160;
        }

        count_sms = parseInt((length - 1) / simbols_in_one_sms) + 1;
        if(length <= 2000){
          lengthBlock = $(this).parent().find('em span').eq(0);
          lengthBlock.text(length);
          $(this).parent().find('em span').eq(1).text(count_sms*simbols_in_one_sms);
          $('.count_sms').text('('+count_sms+' смс)');
        }else{
          e.preventDefault();
        }
        forwardPrice();
    });*/

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $('body').on('keyup', 'textarea[name=message]', function(e){
        var self = $(this);
        delay(function(){
            $.post(
                '/account/forwards/length.php',
                {
                    message : self.val(),
                    latin : $('input[name=latin]:checked').length
                }, function(data){
                    if(data.length <= 2000){
                        self.parent().find('em span').eq(0).text(data.length);
                        self.parent().find('em span').eq(1).text(data.count_sms*data.length_sms);
                        $('.count_sms').text('('+data.count_sms+' смс)');
                        forwardPrice();
                    }else{
                        e.preventDefault();
                    }
                },
                'json'
            );
        }, 1000 );
    });
    $('textarea[name=message]').trigger('keyup');

    $('input[name=latin]').change(function() {
        $('textarea[name=message]').trigger('keyup');
    });

    $('body').on('click', '.KEmoji_Smile', function(){
          val = kemoji.getValue();
          reg = / \$\#[A-Z0-9]*\#\$ /g;
          val = val.replace(reg, ':-)');
          reg = /&nbsp;/g;
          val = val.replace(reg, ' ');
          reg = /<br>/g;
          val = val.replace(reg, ' ');
          length = val.length;

          count_sms = parseInt((length - 1) / 70) + 1;

          if(length <= 2000){
            lengthBlock = $('textarea[name=message]').parent().find('em span').eq(0);
            lengthBlock.text(length);
            $(this).parent().parent().parent().find('em span').eq(1).text(count_sms*70)
            console.log(count_sms, count_sms*70);
            $('.count_sms').text('('+count_sms+' смс)');
          }else{
            e.preventDefault();
          }
          if(length > 0){
              $('.placeholder').css('display', 'none');
          }else{
              $('.placeholder').css('display', 'block');
          }

          forwardPrice();
    });

    $('#getSipAcc').submit(function(e){

        e.preventDefault();

        form = $(this);
        message = $(this).find('.message');

        name = form.find('input[name=name]').val();
        description = form.find('textarea').val();
        record = form.find('input[name=record]:checked').size();

        $.ajax({
            url: '/interface/myaccount/sip/getSipAcc.php',
            type: 'POST',
            data: {
              name: name,
              description: description,
              record: record,
              action: 'create_sip'
            },
            dataType: 'json',
            beforeSend: function(){
                message.html('');
            },
            success: function(out){
                console.log(out);
                if(out.success == true){
                    message.html('<div class="alert alert-success">SIP аккаунт успешно подключен.</div>');
                    $('.connectToSip').parent().removeClass('hidden');
                    form.find('.formContent').slideUp(0)
                }
            },
            error: function(a, b, c){
                console.log(a,b,c);
            }
        });

    });

    $('#integration-phone-buttons .integration-block-line select').change(function(){

        value = $(this).val();
        parent = $(this).parent().parent();

        parent.find('input').addClass('hidden');
        parent.find('input[data-value="'+value+'"]').removeClass('hidden');

    });

    $('.forwardDescription').keyup(function(e){

        length = $(this).val().length;
        if(length <= 1000){
          lengthBlock = $(this).parent().find('em span');
          lengthBlock.text(length);
        }

    });


    $('.table-6').dataTable({
        "language": {
          "paginate": {
            "previous": '<span class="ar-1">arrow</span>',
            'next':'<span class="ar-4">arrow</span>'
          }
        },
        "bLengthChange": false,
        "bInfo": false,
        "columnDefs": [ {
          "targets": 0,
          "orderable": false
        } ],
        "filter": true
    });

    $('.table-6').on('dblclick', 'tr td', function(){
        console.log($(this))
        if(!$(this).parent().hasClass('sip')){
          link = $(this).parent().find('td.table-last a').attr('href');
          window.location.href = link;
        }

    });

    $('.tableModalContacts').dataTable({
        "language": {
          "paginate": {
            "previous": '<span class="ar-1">arrow</span>',
            'next':'<span class="ar-4">arrow</span>'
          }
        },
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "ordering": true
    });



    $('.companies_table input[name=remove]').change(function(){
        size = $('.companies_table input[name=remove]:checked').size();
        if(size > 0 && $(this).hasClass('statusFinish'))
            $('#deleteCompany').stop().fadeIn(0);
        else
            $('#deleteCompany').stop().fadeOut(0);
    });

    $('#deleteCompany').click(function(e){
        var companies = [];

        $('.companies_table input[name=remove]:checked').each(function(ind, elem){
            companies.push($(elem).val());
        });

        $.post(
            '/interface/myaccount/company/deleteCompany.php',
            {
                id: companies
            }, function(data){
                window.location.reload();
            }
        );

        e.preventDefault();
    });


    $('.popup .btn-save').click(function(e){

        $('.popup .close').trigger('click');

        ind = $('.popup input[name=group]:checked').index('.popup input[name=group]');
        text = $('.tableModalContacts tbody tr').eq(ind).find('td').eq(1).text();

        $('.titleSelectContact').text(text).data('select', '1');

        forwardPrice();

        e.preventDefault();
    });

    $('.updateStatus').click(function(e){
        id = $(this).data('id');
        status = $(this).data('status');

        $.post(
            '/interface/myaccount/company/updateCompany.php',
            {
                id : id,
                status : status
            }, function(data){
              console.log(data);
                if(data.result == true){
                    window.location.reload();
                }else{
                    if(data.error == 'balance'){
                        $('a[data-fancybox=balance]').trigger('click');
                    }
                }

            },
            'json'
        )

        e.preventDefault();
    });

    $('#createIntegration').submit(function(e){
        error = 0;

        $(this).find('input[name=name], .selectContacts, input[name=time_start], input[name=time_end]').css('border', 'none');
        $(this).find('textarea[name=sms]').parent().css('border', 'none');

        name = $.trim($(this).find('input[name=name]').val());
        group = $(this).find('input[name=group]:checked').val();
        time_start = $(this).find('input[name=time_start]').val();
        time_end = $(this).find('input[name=time_start]').val();


        if(name == ''){
            $(this).find('input[name=name]').css('border', '1px solid #f00');
            error++;
        }

        if(!group || $('.titleSelectContact').data('select') == '0'){
            $('.selectContacts').css('border', '1px solid #f00');
            error++;
        }

        time_start = time_start.split(':');
        if(time_start[0] > 23 || time_start[1] > 59){
            $(this).find('input[name=time_start]').css('border', '1px solid #f00');
            error++;
        }

        if(time_end[0] > 23 || time_end[1] > 59){
            $(this).find('input[name=time_end]').css('border', '1px solid #f00');
            error++;
        }

        if(error > 0)
          e.preventDefault();

    });

    $('input[name=synthesize]').change(function(){

        forwardPrice();

    });

    $('.multi_name').keyup(function(){
        forwardPrice();
    });

    $('.addMultiName').change(function(){
        if($('.addMultiName:checked').size() > 0 && $('.add_uniq_name:checked').size() > 0){
            $('.add_uniq_name').trigger('click');
        }
        forwardPrice();
    });

    $('.add_uniq_name').change(function(){
        $('.multi_name').slideToggle(500);

        if($('.addMultiName:checked').size() > 0 && $('.add_uniq_name:checked').size() > 0){
            $('.addMultiName').trigger('click');
        }

        if($('.add_uniq_name:checked').size() == 0){
            $('select[name=multi_name]').val('').trigger('change');
        }

        forwardPrice();
    });

    $('select[name=multi_name]').change(function(){
        forwardPrice();
    });
    
});

function forwardPrice(){

    forward = $('.forwardPrice');
    contacts = parseInt($('input[name=group]:checked').parent().parent().parent().find('td').eq(-1).text());
    if(isNaN(contacts))
        contacts = 0;
    priceContact = 4;
    price = 0;
    count_sms = parseInt($('.count_sms').text().match(/\d/g));

    if($('input[name=synthesize]:checked').size() > 0){
        priceContact = 2.5;
    }

    if($('.add_uniq_name:checked').size() > 0){
        priceContact += 2.7;
        multi_name = $.trim($('select[name=multi_name]').val());
        
        if(multi_name != ''){
            price += 30000;
        }
    }
    if($('.addMultiName:checked').size() > 0){
        priceContact += 2.7;
    }

    price += priceContact * contacts * count_sms;

    forward.text(price)

}


var forEach = function (array, callback, scope) {
	for (var i = 0; i < array.length; i++) {
		callback.call(scope, i, array[i]);
	}
};

var file_inputs = document.querySelectorAll('[type=file]');
forEach(file_inputs, function (index, value) {
	if (value.dataset.value){
		value.addEventListener('change', function() {
			var files = new Array();
			for (var x = 0; x < this.files.length; x++) {
				files.push(this.files[x].name);
			}
			document.getElementById(this.dataset.value).value = files.join(', ');
      // this.value.replace(/^C:\\fakepath\\/, '');
    });
	}
});


$(document).ready(function() {
	$('.mon1').on('click', function(){
		$(this).toggleClass('selector');
		$('.box_day_wek').fadeToggle();
		return false;
	});

	$('.monte').on('click', function(){
		$(this).toggleClass('selector');
		$('.box_day_monthh').fadeOut();
		$(this).siblings('.box_day_monthh').fadeToggle();
		return false;
	});

	$(document).click( function(event){
		if( $(event.target).closest(".box_day_wek, .box_day_monthh").length ) 
			return;
		$(".date_icon_box").removeClass('selector');
		$(".box_day_wek, .box_day_monthh").fadeOut("slow");
		event.stopPropagation();
	});

	$('.la_ok').click(function(){
		$('.box_day_wek, .box_day_monthh').fadeOut();
		$('.date_icon_box').removeClass('selector');
	});


	$('.box_dayyyy').on('click', function(){
		$(this).parents('.bdotw').clone(true).appendTo('.box_bdotw');
		$('.bdotw:not(:first)').addClass('clone');

	});


	$('.chois_music').click(function(){
		$('.box_popup').fadeIn();
		return false;
	});

	$('.close_popupp').click(function(){
		$('.box_popup').fadeOut();
	});


	$('.chois_f_la_sintez').click(function(){
		$('.box_la_dt2').slideToggle();
		$('.la_sound').slideUp();
		return false;
	});

	$('.chois_f_la_rec').click(function(){
		$('.la_sound').slideToggle();
		$('.box_la_dt2').slideUp();
		return false;
	});

	$('.box_setting_voice .dispatch-row2 input').click(function(){
		if ($('.box_setting_voice .dispatch-row2 input').is(':checked')) {
			$('.sub_setting_voice').slideDown();
		} else {
			$('.sub_setting_voice').slideUp();
		}
	});

	$('.box_mar_desp .ch111 input').click(function(){
		if ($('.box_mar_desp .dispatch-row2 input').is(':checked')) {
			$('.box_mar_desp .box_fordi').slideDown();
		} else {
			$('.box_mar_desp .box_fordi').slideUp();
		}
	});

	$(document).mouseup(function (e) {
		var container = $(".box_popup");
		if (container.has(e.target).length === 0){
			container.fadeOut();
		}
	});

	$('.save_choise').click(function(){
		$('.example').addClass('active');
		$('.example label').css('display','none');
		$('.box_download').css('display', 'inline-block');
		$(this).css('opacity','0.5');
		return false;
	});

	$(".meter > span").each(function() {
		$(this)
		.data("origWidth", $(this).width())
		.width(0)
		.animate({
			width: $(this).data("origWidth")
		}, 1200);
	});


	$('ul.tabs__caption').on('click', 'li:not(.active)', function() {
		$(this)
		.addClass('active').siblings().removeClass('active')
		.closest('div.tabs').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
	});


// $(".dec-1 .jcf-hidden").change(function(event) {
// var selectform = $(".dec-1 .jcf-select-text span").attr('data-index');	 
// console.log(selectform);
// });


// if ($(selectform).val('Каждый месяц')){
// 	$('.date_icon_box').toggleClass('dadas');
// }

// $(".target").change(function() {
//  console.log( "Handler for .change() called." );
// });

$(".la_target").change(function(){
	if($(this).val() == 0) {
		return false;
	}
	if($(this).val() == ('la_every_month')){
		$('.box_dayyyy').css('display', 'flex');
		$('.date_icon_box.mon1').css('display','none');
		$('.date_icon_box.monte').css('display','block');
	} else if( ($(this).val() == ('la_every_week'))||($(this).val() == ('la_every_day'))){
		$('.box_dayyyy').css('display', 'none');
		$('.date_icon_box.monte').css('display','none');
		$('.date_icon_box.mon1').css('display','block');
		$('.clone').remove();
	}
});


});