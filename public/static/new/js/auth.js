jQuery(function($){
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

    $(window).on('mousemove click', function(e) {

        var lMouseX = Math.max(-100, Math.min(100, $(window).width() / 2 - e.clientX));
        var lMouseY = Math.max(-100, Math.min(100, $(window).height() / 2 - e.clientY));
        lFollowX = (20 * lMouseX) / 100; // 100 : 12 = lMouxeX : lFollow
        lFollowY = (10 * lMouseY) / 100;

    });

    moveBackground();



    $('.tabset li a').click(function(e){
        e.preventDefault();
        $('#forgetPass, .tab').addClass('js-tab-hidden');
        $('.tabset li a').removeClass('active');
        $(this).addClass('active');
        id = $(this).attr('href');
        $(id).addClass('active').removeClass('js-tab-hidden')
    });

    $('#openForgetPass').click(function(e){
        e.preventDefault();
        $('.tabset li a').removeClass('active');
        $('#tab-30, #tab-31').addClass('js-tab-hidden').removeClass('active');
        $('#forgetPass').removeClass('js-tab-hidden');
    });

    function animatePreloader(){
        var preloader = $('.preloader');
        var frontpage = $('.frontpage');
        var properties = {
            'top': `0`
        };
        var options    = {
            duration: 1000,
            easing: 'swing',
            complete(){
                preloader.remove();
            }
        };
        frontpage.delay(500).animate(properties, options);
    }

    $('#register-form').submit(function(e){
        e.preventDefault();
        $('.preloader').addClass('preloader_active');
        $('.help-block').remove();

        var form = document.querySelector('#register-form');

        // form data
        var formData = new FormData(form);

        var data = {};
        formData.forEach(function(value, key){
            data[key] = value;
        });

        $.ajax({
            url: form.action,
            data: data,
            processData: true,
            type: 'POST',
            success: function ( data ) {
                console.log(data);
                $('.preloader__status-text').html('Начнем работу!');
                animatePreloader();
                setTimeout(function(){
                    location.assign(data.location);
                }, 3000);
            },
            error :function( response ) {
                console.log(response)
                $('.preloader').removeClass('preloader_active');
                if( response.status === 422 ) {
                    for(var inputName in response.responseJSON.errors){
                        var errorMessage = response.responseJSON.errors[inputName];
                        $('#' + inputName)
                            .closest('.form-registration-row')
                            .after('<span class="help-block"><strong>' + errorMessage + '</strong></span>');
                    }
                } else {
                    alert('Ошибка на стороне сервера')
                }
            }
        });
    });


    $('form#forget').submit(function(e){
        e.preventDefault();

        let email =  $(this).find('input[name=email]').val();

        $.ajax({
            url: '/setting/reset',
            data: {
                email: email
            },
            type:'POST',
            success: function(data){
                if(data.success) {
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
