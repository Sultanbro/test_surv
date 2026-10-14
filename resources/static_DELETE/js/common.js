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