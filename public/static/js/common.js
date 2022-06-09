var forEach = function (array, callback, scope) {
	for (var i = 0; i < array.length; i++) {
		callback.call(scope, i, array[i]);
	}
};

$(function () {
	$('.mon1').on('click', function(){
		$(this).toggleClass('selector');
		$('.box_day_wek').toggle();
		return false;
	});

	$('.monte').on('click', function(){
		$(this).toggleClass('selector');
		$('.box_day_monthh').toggle();
		return false;
	});


// остается открытой меню при перезагрузке
	$('.opener span,.opener em').click(function() {
			var classs = 'activetest';
			var a = $(this).index();
			localStorage.setItem('ztest1', a);
			localStorage.setItem('myKey', classs)
			
	});
			var localValue = localStorage.getItem('ztest1');
			var localIndex = localStorage.getItem('myKey');
			$('.opener span,.opener em').eq(localValue).addClass(localIndex);


			$('.opener span.activetest').closest('#sidebar').addClass('active active_no_transition');
// остается открытой меню при перезагрузке


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
        $('.input-time').unmask('99:99');
        $(this).parents('.bdotw').clone(true, true).appendTo('.box_bdotw');
        $('.input-time').mask('99:99');
		$('.bdotw:not(:first)').addClass('clone');

	});


	$('.chois_music').click(function(){
		$('#autoaudio').modal('show');
		return false;
	});

	$('.close_popupp').click(function(){
		$('.box_popup').fadeOut();
	});

	$('.saveButton').click(function(){
		$('.box_popup.audio').fadeOut();
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


	$('.box_setting_voice .control input').click(function(){
		if ($('.box_setting_voice .control input').is(':checked')) {
			$('.sub_setting_voice').slideDown();
		} else {
			$('.sub_setting_voice').slideUp();
		}
	});
	
	$('.box_dop_optionn .control #sms-after').click(function(){
		if ($('.box_dop_optionn .control #sms-after').is(':checked')) {
			$('.box_dop_optionn .box_fordi').slideDown();
		} else {
			$('.box_dop_optionn .box_fordi').slideUp();
		}
	});




//	$('.save_choise').click(function(){
//		$('.example').addClass('active');
//		$('.example label').css('display','none');
//		$('.box_download').css('display', 'inline-block');
//		$(this).css('opacity','0.5');
//		return false;
//	});

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

    function la_target() {
        if($(".la_target").val() == 'monthly'){
            //$('.box_dayyyy').css('display', 'flex');
            $('.box_bdotw').css('display','block');
            $('.date_icon_box.mon1').css('display','none');
            $('.date_icon_box.monte').css('display','block');
            $('.time_begin').css('display','flex');
        } else if($(".la_target").val() == 'weekly'){
            //$('.box_dayyyy').css('display', 'none');
            $('.box_bdotw').css('display','block');
            $('.date_icon_box.monte').css('display','none');
            $('.date_icon_box.mon1').css('display','block');
            $('.time_begin').css('display','flex');
            $('.clone').remove();
        } else if($(".la_target").val() == 'daily') {
            //$('.box_dayyyy').css('display', 'none');
            $('.box_bdotw').css('display','none');
            $('.date_icon_box.mon1').css('display','none');
            $('.date_icon_box.monte').css('display','none');
            $('.time_begin').css('display','flex');
        } else {
            $('.box_bdotw').css('display','none');
            $('.box_dayyyy').css('display', 'none');
            $('.date_icon_box.mon1').css('display','none');
            $('.date_icon_box.monte').css('display','none');
            $('.time_begin').css('display','none');
        }
    }

    la_target();
    $(".la_target").change(function(){
        la_target();
    });


});
