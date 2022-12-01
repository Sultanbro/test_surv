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

    $('#register-form').submit(function(e){
        e.preventDefault();
        $('.cabinet-loader-bg').addClass('active');

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
                console.log(data)
                location.assign(data.location);
            },
            error :function( response ) {
                console.log(response)
                $('.cabinet-loader-bg').removeClass('active');
                if( response.status === 422 ) {
                    alert(response.responseJSON.message)
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
