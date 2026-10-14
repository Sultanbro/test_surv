// folder: public/static/new/js/auth.js


/*
jQuery(function ($) {
	var lFollowX = 0,
		lFollowY = 0,
		x = 0,
		y = 0,
		friction = 1 / 30;

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	function moveBackground() {
		x += (lFollowX - x) * friction;
		y += (lFollowY - y) * friction;

		translate = 'translate(' + x + 'px, ' + y + 'px) scale(1.1)';

		$('.bg').css({
			'-webit-transform': translate,
			'-moz-transform': translate,
			'transform': translate
		});

		window.requestAnimationFrame(moveBackground);
	}

	$(window).on('mousemove click', function (e) {

		var lMouseX = Math.max(-100, Math.min(100, $(window).width() / 2 - e.clientX));
		var lMouseY = Math.max(-100, Math.min(100, $(window).height() / 2 - e.clientY));
		lFollowX = (20 * lMouseX) / 100; // 100 : 12 = lMouxeX : lFollow
		lFollowY = (10 * lMouseY) / 100;

	});

	moveBackground();


	$('.tabset li a').click(function (e) {
		e.preventDefault();
		$('#forgetPass, .tab').addClass('js-tab-hidden');
		$('.tabset li a').removeClass('active');
		$(this).addClass('active');
		id = $(this).attr('href');
		$(id).addClass('active').removeClass('js-tab-hidden')
	});

	$('#openForgetPass').click(function (e) {
		e.preventDefault();
		$('.tabset li a').removeClass('active');
		$('#tab-30, #tab-31').addClass('js-tab-hidden').removeClass('active');
		$('#forgetPass').removeClass('js-tab-hidden');
	});

	function animatePreloader() {
		var preloader = $('.preloader');
		var frontpage = $('.frontpage');
		var properties = {
			'top': `0`
		};
		var options = {
			duration: 1000,
			easing: 'swing',
			complete() {
				preloader.remove();
			}
		};
		frontpage.delay(500).animate(properties, options);
	}

	const email = $('#register-form input#email');
	const phone = $('#register-form input#phone');
	const password = $('#register-form input#password');
	const passwordConfirmation = $('#register-form input#password_confirmation');
	const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
	const validation = {
		email: false,
		phone: false,
		password: false,
		password_confirmation: false
	};

	function removeHelper(el) {
		$(`#register-form input#${el}`).parent().removeClass('error');
		$(`#register-form .help-block.${el}`).removeClass('show');
		validation[el] = true;
		console.log(validation);
	}

	function addHelper(el) {
		$(`#register-form input#${el}`).parent().addClass('error');
		$(`#register-form .help-block.${el}`).addClass('show');
		validation[el] = false;
		console.log(validation);
	}


	email.change(({target}) => target.value.match(emailRegex) ? removeHelper('email') : addHelper('email'));


	phone.on('input', ({target}) => {
		$('#register-form .help-block.phone b').text(`Сейчас (${target.value.length})`);
		if (target.value.length >= 11) removeHelper('phone');
	});
	phone.change(({target}) => target.value.length < 11 ? addHelper('phone') : removeHelper('phone'));


	password.on('input', ({target}) => {
		$('#register-form .help-block.password b').text(`Сейчас (${target.value.length})`);
		if (target.value.length >= 8) removeHelper('password');
		passwordConfirmation.val().length && target.value !== passwordConfirmation.val() ? addHelper('password_confirmation') : removeHelper('password_confirmation');
	});
	password.change(({target}) => {
		target.value.length < 8 ? addHelper('password') : removeHelper('password');
		passwordConfirmation.val().length && target.value !== passwordConfirmation.val() ? addHelper('password_confirmation') : removeHelper('password_confirmation');
	});

	passwordConfirmation.change(({target}) => {
		if(!password.val().length) return;
		target.value !== password.val() ? addHelper('password_confirmation') : removeHelper('password_confirmation');
	});




	$('#register-form').submit(function (e) {
		e.preventDefault();
		for(let valid in validation){
			if(!validation[valid]) return;
		}
		$('.preloader').addClass('preloader_active');
		$('.help-block').remove();

		var form = document.querySelector('#register-form');

		// form data
		var formData = new FormData(form);

		var data = {};
		formData.forEach(function (value, key) {
			data[key] = value;
		});

		$.ajax({
			url: form.action,
			data: data,
			processData: true,
			type: 'POST',
			cache: false,
			success: function (data) {
				console.log(data);
				$('.preloader__status-text').html('Начнем работу!');
				animatePreloader();
				setTimeout(function () {
					location.assign(data.link);
				}, 3000);
			},
			error: function (response) {
				$('.preloader').removeClass('preloader_active');
				if (response.status === 422) {
					validatorFormAfter(response.responseJSON.errors);
				} else {
					alert('Ошибка на стороне сервера')
				}
				if (document.querySelector('.g-recaptcha')) grecaptcha.reset();
			}
		});
	});

	function validatorFormAfter(errors) {
		for (let inputName in errors) {
			const errorMessage = errors[inputName];
			$('#' + inputName)
				.closest('.form-registration-row')
				.after(`<span class="help-block error-${inputName}">${errorMessage}</span>`);
			setTimeout(function () {
				const parentDiv = $('#' + inputName).closest('.form-registration-row');
				parentDiv.addClass('error');
				$(`.error-${inputName}`).addClass('show');
			}, 300);
		}
		if (errors.phone) {
			$('input#phone').on('input', ({target}) => {
				console.log(target.value);
				if (target.value.length >= 11) {
					const parentDiv = $('input#phone').closest('.form-registration-row');
					parentDiv.removeClass('error');
					$(`.error-phone`).removeClass('show');
				}
			})
		}
	}

	$('form#forget').submit(function (e) {
		e.preventDefault();

		let email = $(this).find('input[name=email]').val();

		$.ajax({
			url: '/setting/reset',
			data: {
				email: email
			},
			type: 'POST',
			success: function (data) {
				if (data.success) {
					alert('На вашу почту отправлен новый пароль!');
					$('#forgetPass, #tab-31').addClass('js-tab-hidden').removeClass('active');
					$('#tab-30').removeClass('js-tab-hidden').addClass('active');
				} else {
					alert('Вы не зарегистрированы в нашей системе!');
				}

			},
		});
	});
});

 */
