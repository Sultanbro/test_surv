$(function() {

    //Выбор страны отправки

    $(".directions_button").on("click", function(e) {
        e.preventDefault();
        $(".directions").toggle();
    })
    direction_input_array = new Array;
    direction_inputs = $(".destination_input");
    direction_prefix_array = new Array;
    direction_name_array = new Array;
    direction_prefix = $('input[name = direction]').val();
    direction_name = $('input[name = direction_names]').val();

    if (direction_prefix != '') {
        direction_prefix_array = $("input[name='direction']").map(function() { return $(this).val(); }).get();
    }
    if (direction_name != '') {
        direction_name_array = $("input[name='direction_names']").map(function() { return $(this).val(); }).get();
    }

    console.log('direction_prefix_array,', direction_prefix_array);
    console.log('direction_name_array,', direction_name_array);
    var checkedNum = $('.destination_input:checked').length;
    if (direction_inputs.length == checkedNum) {
        $(".destination_input_all").prop("checked", true);
    }
    // $.each(inputs_group_id, function(index,input) {
    $(".destination_input_all").on("change", this, function() {
        if ($(this).is(":checked")) {
            direction_inputs.prop("checked", true);
            direction_prefix_array = [];
            direction_input_array = [];
            direction_name_array = [];
            $.each(direction_inputs, function(index, input) {
                let namesdestination = $(input).parent().find('.destination_name').text();
                direction_prefix_array.push($(input).val());
                direction_input_array.push($(input));
                direction_name_array.push(namesdestination);
            })
            $('.directions_button').html(direction_name_array.join(","));
            $("input[name=direction]").val(direction_prefix_array);
        } else {
            direction_inputs.prop("checked", false);
            direction_prefix_array = [];
            direction_input_array = [];
            direction_name_array = [];
            if (direction_name_array == '') {
                $('.directions_button').html('Выбрать страну');
            } else {
                $('.directions_button').html(direction_name_array.join(","))
            }
            $("input[name=direction]").val(direction_prefix_array);
        }
    })
    direction_inputs.on("change", this, function() {

        if ($(this).is(":checked")) {

            let namesdestination = $(this).parent().find('.destination_name').text();
            direction_input_array.push($(this));
            direction_prefix_array.push($(this).val());
            direction_name_array.push(namesdestination);
            $('.directions_button').html(direction_name_array.join(","));
            $("input[name=direction]").val(direction_prefix_array);

        } else {

            if ($(".destination_input_all").is(":checked")) {
                $(".destination_input_all").prop("checked", false)
            }
            let namesdestination = $(this).parent().find('.destination_name').text();
            direction_input_array.splice(direction_input_array.indexOf($(this)), 1);
            direction_prefix_array.splice(direction_prefix_array.indexOf($(this).val()), 1);
            direction_name_array.splice(direction_name_array.indexOf(namesdestination), 1);
            if (direction_name_array == '') {
                $('.directions_button').html('Выбрать страну');
            } else {
                $('.directions_button').html(direction_name_array.join(","))
            }
            $("input[name=direction]").val(direction_prefix_array);

        }

    })
    if (direction_name_array == '') {
        $('.directions_button').html('Выбрать страну');
    } else {
        $('.directions_button').html(direction_name_array.join(","))
    }
    //чекбокс на странице создания смс интеграции

    if ($("#message_block").find("textarea[name=message]").val() !== '') {
        $('input[name=unic_message]').prop('checked', true)
        $("#message_block").css('display', 'block');
    } else {
        $('input[name=unic_message]').prop('checked', false);
        $("#message_block").css('display', 'none');
    }
    $(document).on("change", $('input[name=unic_message]'), function() {
        if (!$('input[name=unic_message]').prop('checked')) $("#message_block").find("textarea[name=message]").val('');
    })


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#importValidator').click(function() {
        $('.box_popup.validator').fadeIn();
        return false;
    });

    $('.datepicker2, .datepicker33').datepicker({
        language: 'ru',
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $('.datepicker22').datepicker({
            language: 'ru',
            format: 'yyyy-mm-dd',
            autoclose: true
        })
        .on('hide', shownext);

    function shownext(e) {
        $('.datepicker33').datepicker('show');
    }


    $('.datepicker3, .datepicker55').datepicker({
        language: 'ru',
        format: 'dd.mm.yyyy',
        autoclose: true
    });
    $('.datepicker44').datepicker({
            language: 'ru',
            format: 'dd.mm.yyyy',
            autoclose: true
        })
        .on('hide', shownext2);

    function shownext2(e) {
        $('.datepicker55').datepicker('show');
    }

    $('.datepicker').datepicker({
        language: 'ru',
        multidate: true,
        "setDate": new Date(),
        // startDate: new Date(),
        format: 'dd.mm.yyyy',
        autoclose: false,
    });

    $('#dayprice').on('hidden.bs.modal', function() {
        $('.start-date').val($('input[name=start_date]').val());
    });
    $('.datepicker:not(.not)').datepicker("setDate", new Date());
    $('.input-time').mask('99:99');
    $('.input-phone').mask('79999999999');

    $(".input-time2").mask("99:99", {
        completed: function() {
            $('.input-time3').focus();
        }
    });


    $('#datepicker').datepicker({
        language: 'ru',
        multidate: true,
        "setDate": new Date(),
        // startDate: new Date(),
        format: 'dd.mm.yyyy',
        autoclose: false,
    });

    $('#datepicker').on('changeDate', function() {
        $('#hidden_input').val(
            $('#datepicker').datepicker('getFormattedDate')
        );
    });

    $('#clikdate').on('click', function(e) {
        e.preventDefault();
        let arr2 = [];
        let arr = [];
        let m = new Date();
        yearselect = m.getFullYear();
        let mountselect = $('.datepicker-days .datepicker-switch').text();
        mountselect = mountselect.replace(/2019/g, '');
        mountselect = mountselect.replace(/2018/g, '');
        mountselect = mountselect.replace(/2020/g, '');
        mountselect = mountselect.replace(/\s/g, '');
        let monthNumber = ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"].indexOf(mountselect.toLowerCase()) + 1;
        $(".datepicker-days .day:not(.old):not(.new)").each(function(index) {
            arr2[index] = new Date(yearselect, monthNumber - 1, $(this).text());
        });
        $('#datepicker').datepicker("setDates", arr2);
    });

    function fillCallRepatInfo() {
        if ($(".la_target").val() == 'weekly') {
            $('input[name=call_repeat_info]').val($('.day_wek input:checked').map(function() {
                return this.value;
            }).get().join(','));
        } else if ($(".la_target").val() == 'monthly') {
            $('input[name=call_repeat_info]').val($('.day_month input:checked').map(function() {
                return this.value;
            }).get().join(','));
        } else {
            $('input[name=call_repeat_info]').val('');
        }
    }
    $('.call-repeat-option').click(function() {
        $('.day input:checked').prop('checked', false);
        fillCallRepatInfo();
    })
    $(".la_target").change(function() {
        $('.day input:checked').prop('checked', false);
        fillCallRepatInfo();

        if ($(this).val() !== 'none') {
            $('.days-selection').addClass('hidden')
        } else {
            $('.days-selection').removeClass('hidden')
        }
    });

    $('.day input').click(function() {
        fillCallRepatInfo();
    });

    $('#uploadVoiceFile').click(function(e) {
        e.preventDefault();

        var file_data = $('#voiceFile').prop('files')[0];
        var form_data = new FormData();
        var nameFile = $('#nameFile').val();
        form_data.append('file', file_data);
        form_data.append('nameFile', nameFile);
        form_data.append('_token', $('input[name=_token]').val());

        $.ajax({
            url: '/autocalls/voice',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            beforeSend: function() {
                $('#voiceFile').parent().hide(0);
                $('.box_download').fadeIn(0);
                $('#uploadVoiceFile').hide(0);
                $('.uploadVoices .message').html('');
                $('.n_dow').text($('#nameFile').val());
            },
            xhr: function() {
                var xhr = $.ajaxSettings.xhr(); // получаем объект XMLHttpRequest
                xhr.upload.addEventListener('progress', function(evt) { // добавляем обработчик события progress (onprogress)
                    if (evt.lengthComputable) {
                        var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
                        $('.meter span').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            error: function(a, b, c) {
                console.log(a, b, c);
            },
            success: function(data) {
                console.log(data);
                if (data.success == false) {
                    $('.uploadVoices .message').html('<div class="alert alert-danger">' + data.error + '</div>');
                } else {


                    $('.uploadVoices .message').html('<div class="alert alert-success">Файл загружен успешно!</div>');
                    $('#voiceFile').val('');
                    $('#nameFile').val('');
                    $('.tableVoices table').prepend('<tr><td><input style="width: 20px;"  type="radio" name="voice_id" checked value="' + data.id + '"/></td><td>' + data.title + '</td><td>' + data.duration + '</td><td><div class="playAudio glyphicon glyphicon-play"><audio src="/static/voices/' + data.file + '"></audio></div></td></tr>');
                    $('.modal.in .saveButton').click();
                    $('.playAudio').unbind("click");
                    $('.playAudio').click(function() {
                        playAudio($(this));
                    });
                }
                $('#uploadVoiceFile').show(0);
                $('#voiceFile').parent().show(0);
                $('.box_download').hide();


            }
        });
    });

    $('#syntezButton').click(function(e) {
        e.preventDefault();
        $('#syntezButton').prop("disabled", true);
        $.ajax({
            url: '/autocalls/syntez',
            type: 'POST',
            dataType: 'JSON',
            data: {
                "_method": "post",
                'type': 'syntez',
                'man': $('input[name=man]:checked').val(),
                'message': $('textarea[name=message]').val(),
                '_token': $('input[name=_token]').val()
            },
            error: function(a, b, c) {
                console.log(a, b, c);
            },
            success: function(data) {
                console.log(data);
                if (data.success == false) {
                    $('#uploadVoices .message').html('<div class="alert alert-danger">' + data.error + '</div>');
                } else {
                    $('.box_popup.audio').fadeIn();
                    $('#syntezButton').prop("disabled", false);
                    $('.uploadVoices .message').html('<div class="alert alert-success">Аудио синтезирован успешно!</div>');
                    $('.tableVoices table').prepend('<tr><td><input style="width: 20px;"  type="radio" name="voice_id" checked value="' + data.id + '"/></td><td>' + data.title + '</td><td>' + data.duration + '</td><td><div class="playAudio glyphicon glyphicon-play"><audio src="/static/voices/' + data.file + '"></audio></div></td></tr>')
                    $('.saveButton').trigger('click');

                    $('.playAudio').unbind("click");
                    $('.playAudio').click(function() {
                        playAudio($(this));
                    });
                }
            }
        });
    });

    $('.updateStatus').click(function(e) {
        id = $(this).data('id');
        status = $(this).data('status');

        $.ajax({
            url: '/autocalls/status/' + id,
            type: 'POST',
            dataType: 'JSON',
            data: {
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
                if (data.result == true) {
                    window.location.reload();
                } else {
                    if (data.error == 'balance') {
                        $('a[data-fancybox=balance]').trigger('click');
                    }
                }
            }
        });

        e.preventDefault();
    });


    $('.sub_setting_voice select').change(function() {

        value = $(this).val();
        parent = $(this).parent().parent();

        parent.find('input').addClass('hidden');
        parent.find('input[data-value="' + value + '"]').removeClass('hidden');
    });

    $('[data-toggle="tooltip"]').tooltip();


    $("#autocall_create input[name=save]").click(function(e) {
        if (parseInt($('#balance_value').text()) < parseInt($('.forwardPrice').data('price'))) {
            $('#balance-modal').modal();
            e.preventDefault();
        }
    });

    $('#autocall_create').submit(function(e) {
        console.log('submit');
        console.log('call_start_time', $('input[name=call_start_time]').val());

        if ($('input[name=call_start_time]').val() == 0) {
            $('input[name=call_start_time]').css('border', '1px solid #f00');
            $('input[name=call_start_time]').get(0).setCustomValidity("Введите интервал рассылки");
            $('input[name=call_start_time]').get(0).reportValidity();
        }
        // if ($("input[name=direction]").val() == "") {
        //     $(".directions").show();
        //     $(".directions").css("border", "red 1px solid");
        //      e.preventDefault();
        //      return false;
        // } else {
        //     $(".directions").css("border", "none");
        // }
        if ($('input[name=voice_id]:checked').val() == undefined) {
            e.preventDefault();
            $('#autoaudio').modal('show');

            return false;
        }
        if ($('.group_id_checkbox:checked').val() == undefined) {
            e.preventDefault();
            $('#group').modal('show');
            return false;
        }

        call_start_time = parseInt($('input[name=call_start_time]').val().replace(':', ''));
        call_end_time = parseInt($('input[name=call_end_time]').val().replace(':', ''));

        if (call_end_time <= 600) call_end_time += 2400

        if (call_end_time - call_start_time < 15) { // Кто это придумал ))  Снос башни (╯°□°）╯︵ ┻━┻

            e.preventDefault();

            $('input[name=call_end_time]').css('border', '1px solid #f00');
            document.querySelector("input[name=call_end_time]").setCustomValidity("Интервал рассылки должен быть не менее 15 минут!");
            document.querySelector("input[name=call_end_time]").reportValidity();
            setTimeout(() => {
                document.querySelector("input[name=call_end_time]").setCustomValidity("");
            }, 2000);

            return false;







        }

        button0 = $('select[name=button0]').val();
        button1 = $('select[name=button1]').val();
        button2 = $('select[name=button2]').val();
        button3 = $('select[name=button3]').val();
        button4 = $('select[name=button4]').val();
        button5 = $('select[name=button5]').val();
        button6 = $('select[name=button6]').val();
        button7 = $('select[name=button7]').val();
        button8 = $('select[name=button8]').val();
        button9 = $('select[name=button9]').val();

        button0_action = $('input[name=button0_action][data-value="' + button0 + '"]').val();
        button1_action = $('input[name=button1_action][data-value="' + button1 + '"]').val();
        button2_action = $('input[name=button2_action][data-value="' + button2 + '"]').val();
        button3_action = $('input[name=button3_action][data-value="' + button3 + '"]').val();
        button4_action = $('input[name=button4_action][data-value="' + button4 + '"]').val();
        button5_action = $('input[name=button5_action][data-value="' + button5 + '"]').val();
        button6_action = $('input[name=button6_action][data-value="' + button6 + '"]').val();
        button7_action = $('input[name=button7_action][data-value="' + button7 + '"]').val();
        button8_action = $('input[name=button8_action][data-value="' + button8 + '"]').val();
        button9_action = $('input[name=button9_action][data-value="' + button9 + '"]').val();

        $('input[name=funct_0]').val(button0_action + ':' + button0);
        $('input[name=funct_1]').val(button1_action + ':' + button1);
        $('input[name=funct_2]').val(button2_action + ':' + button2);
        $('input[name=funct_3]').val(button3_action + ':' + button3);
        $('input[name=funct_4]').val(button4_action + ':' + button4);
        $('input[name=funct_5]').val(button5_action + ':' + button5);
        $('input[name=funct_6]').val(button6_action + ':' + button6);
        $('input[name=funct_7]').val(button7_action + ':' + button7);
        $('input[name=funct_8]').val(button8_action + ':' + button8);
        $('input[name=funct_9]').val(button9_action + ':' + button9);

        //$(this).resetForm();
        //$(this).find('input[type=submit]').attr('disabled', 'disabled');

    });




    function forwardPrice(ajax = true) {
        contacts = parseInt($('input[name=group_id]').data('count'));
        group_id = $('input[name=group_id]').val();
        daily_limit = $('select[name=daily_limit]').val();
        if (isNaN(contacts))
            contacts = 0;
        smsPrice = 7;
        voicePrice = 4.5;
        functPrice = 0.33;
        price = 0;
        has_funct = 0;
        has_sms = 0;

        if ($('input[name=has_funct]:checked').size() > 0) {
            $('.sub_setting_voice select[name^=button]').each(function(index) {
                if ($(this).val() != 'NONE') {
                    has_funct++;
                }
            });
        }

        if ($('input[name=has_sms]:checked').size() > 0) {
            has_sms = parseInt($('.count_sms').text().match(/\d/g));
        } else {
            has_sms = 0;
        }
        if (ajax) {
            $.get('/autocalls/cost/', { group_id: group_id, daily_limit: daily_limit, has_funct: has_funct, has_sms: has_sms }, function(data) {
                var currency = data.currency == 'kzt' ? '₸' : '₽';

                $('.forwardPrice').data('price', data.total);
                $('.forwardPrice').attr('cost', data.total_autocall);
                $('.forwardPrice').attr('sms_cost', data.total_sms);
                $('.forwardPrice').attr('currency', currency);
                $('.forwardPrice').text(data.total + ' ' + currency);
                $('input[name=total_cost]').val(data.total);

                // Детализация звонков
                var details = Object.keys(data.details).map(function(key) {
                    return [Number(key), data.details[key]];
                });
                var ru_details = details.filter(function(number) {
                    return number[0].toString().startsWith('89')
                });
                var kz_details = details.filter(function(number) {
                    return !number[0].toString().startsWith('89')
                });

                var callpricekz = 0;
                var callpriceru = 0;
                kz_details.forEach(function(val) {
                    callpricekz = val[1];
                    return false;
                });
                ru_details.forEach(function(val) {
                    callpriceru = val[1];
                    return false;
                });

                $('.callpricekz').text(callpricekz);
                $('.callpriceru').text(callpriceru);
                $('.countcallkz').text(kz_details.length);
                $('.countcallru').text(ru_details.length);
                $('.countcallkzall').text(parseInt(kz_details.length) * callpricekz);
                $('.countcallruall').text(parseInt(ru_details.length) * callpriceru);


                if (kz_details.length > 0) {
                    $('.trcallkz').show();
                } else {
                    $('.trcallkz').hide();
                }
                if (ru_details.length > 0) {
                    $('.trcallru').show();
                } else {
                    $('.trcallru').hide();
                }




                // Детализация кнопок
                var funct_details = Object.keys(data.funct_details).map(function(key) {
                    return [Number(key), data.funct_details[key]];
                });

                //   var ru_funct = funct_details.filter(function(number) {
                //       return number[0].toString().startsWith('89')
                //   });
                //   var kz_funct = funct_details.filter(function(number) {
                //       return !number[0].toString().startsWith('89')
                //   });

                var pricefunct = 0;
                var summapricefunct = 0;
                funct_details.forEach(function(val) {
                    pricefunct = val[1];
                    return false;
                });

                $('.countfunct').text(has_funct);
                $('.pricefunct').text(parseInt(funct_details.length) * pricefunct);
                $('.priceallfunct').text((parseInt(funct_details.length) * pricefunct) * has_funct);


                if (funct_details.length > 0) {
                    $('.trgolos').show();
                } else {
                    $('.trgolos').hide();
                }

                // Детализация смс
                var sms_details = Object.keys(data.sms_details).map(function(key) {
                    return [Number(key), data.sms_details[key]];
                });
                var ru_sms = sms_details.filter(function(number) {
                    return number[0].toString().startsWith('89')
                });
                var kz_sms = sms_details.filter(function(number) {
                    return !number[0].toString().startsWith('89')
                });

                var pricesmskz = 0;
                kz_sms.forEach(function(val) {
                    pricesmskz = val[1];
                    return false;
                });
                $('.countsmskz').text(kz_sms.length);
                $('.smspricekz').text(has_sms * pricesmskz);
                $('.countsmskzall').text(parseInt(kz_sms.length) * pricesmskz * has_sms);

                var pricesmsru = 0;
                ru_sms.forEach(function(val) {
                    pricesmsru = val[1];
                    return false;
                });
                $('.countsmsru').text(ru_sms.length * has_sms);
                $('.smspriceru').text(pricesmsru);
                $('.countsmsruall').text(parseInt(ru_sms.length) * pricesmsru * has_sms);

                if (ru_sms.length > 0) {
                    $('.trsmsru').show();
                } else {
                    $('.trsmsru').hide();
                }
                if (kz_sms.length > 0) {
                    $('.trsmskz').show();
                } else {
                    $('.trsmskz').hide();
                }


                if ($('#my-bonus').length == 0 || $('#my-bonus').data('bonus') < data.total) {
                    $("#use_bonus_chb").prop("disabled", true);
                    $(".bonusline label").attr("data-toggle", "modal");
                    $(".bonusline label").attr("data-target", "#modal_bonus");
                } else {
                    $("#use_bonus_chb").prop("disabled", false);
                    $(".bonusline label").removeAttr("data-toggle");
                    $(".bonusline label").removeAttr("data-target");
                }
            }, );
        } else {
            var total = $('.forwardPrice').attr('cost');
            var total_sms = $('.forwardPrice').attr('sms_cost');
            var currency = $('.forwardPrice').attr('currency');

            total = (parseFloat(total) + (parseFloat(total_sms) * parseFloat(has_sms))).toFixed(2);
            $('.forwardPrice').text(total + ' ' + currency)
        }
    }

    forwardPrice();
    $('.detailpricecall').click(function() {
        $('.detailcallall').slideToggle();
    });

    $("input[name=has_funct]").click(function() {
        forwardPrice();
    });

    $('.sub_setting_voice select[name^=button]').change(function() {
        forwardPrice();
    });

    $("input[name=has_sms]").click(function() {
        forwardPrice();
    });

    $("select[name=daily_limit]").change(function() {
        forwardPrice();
    });


    $('.nameInMessage').click(function() {
        action = $(this).data('action');
        textArea = $(this).parent().parent().find('textarea[name=sms_message]');
        if (action == 'name') {
            textArea.val(textArea.val() + ' [Имя]');
        }
        if (action == 'surname') {
            textArea.val(textArea.val() + ' [Фамилия]');
        }
        if (action == 'info1') {
            textArea.val(textArea.val() + ' [Доп-1]');
        }
        if (action == 'info2') {
            textArea.val(textArea.val() + ' [Доп-2]');
        }
        if (action == 'info3') {
            textArea.val(textArea.val() + ' [Доп-3]');
        }
        if (action == 'unsublink') {
            textArea.val(textArea.val() + ' [Отписка]');
        }
    });


    $('.saveButton').click(function() {
        var fileName = '<span>Файл: ' + $('input[name=voice_id]:checked').parent().siblings('td').first().text() + ': </span>';
        $('.file_name').html(fileName + '<div><audio controls src="' + $('input[name=voice_id]:checked').parent().siblings('td').eq(2).find('audio').attr('src') + '"></audio></div>');

        if ($('.statistic_robot').length) {
            let robot_id = $('.modal').data('robotId');

            if ($(this).hasClass('is_message')) {
                $('.robot_message' + robot_id).val($("textarea[name=robot_message]").val());
                $('.robot_voice' + robot_id).val(0);
            } else {
                $('.robot_message' + robot_id).val('');
                $('.robot_voice' + robot_id).val($('input[name=voice_id]:checked').val());
            }

            let robot_voice_id = $('.robot_voice' + robot_id).val();
            let robot_message = $('.robot_message' + robot_id).val();
            console.log('voice ' + robot_voice_id);
            console.log('text ' + robot_message);


            if (robot_voice_id == 0) {
                robot_actions(robot_id, 'message', robot_message);
                $('.robot_text' + robot_id).text(robot_message);
                $('.file_name' + robot_id).html('');
            } else {
                robot_actions(robot_id, 'voice', robot_voice_id);
                $('.robot_text' + robot_id).text('Аудио запись: ' + $('input[name=voice_id]:checked').parent().siblings('td').first().text());

                $('.file_name' + robot_id).html('<div><audio class="pleer" controls src="' + $('input[name=voice_id]:checked').parent().siblings('td').eq(2).find('audio').attr('src') + '"></audio></div>');

                $('audio.pleer').audioPlayer();
            }
            $('.box_popup').fadeOut();
        }
    });


    $('body').on('keyup', 'textarea', function(e) {

        val = $(this).val();
        reg = / \$\#[A-Z0-9]*\#\$ /g;
        val = val.replace(reg, ':-)');
        reg = /&nbsp;/g;
        val = val.replace(reg, ' ');
        reg = /<br>/g;
        val = val.replace(reg, ' ');
        length = val.length;

        $(this).parent().find('em span.count').text(length);

    });
    $('textarea').trigger('keyup');

    function playAudio(jObject) {
        if (jObject.hasClass('glyphicon-play')) {
            jObject.find('audio')[0].play();
        } else {
            jObject.find('audio')[0].pause();
            jObject.find('audio')[0].currentTime = 0
        }

        jObject.toggleClass('glyphicon-play');
        jObject.toggleClass('glyphicon-stop');
    }
    $('.playAudio').click(function() {
        playAudio($(this));
    });

    $('.deleteAudio').click(function() {
        var voice_id = this.getAttribute('voice_id');
        var row = this.parentNode.parentNode;

        $.get('/autocalls/deleteVoice/', { voice_id: voice_id }, function(data) {
            if (data.success) {
                $(row).css("display", "none");
                row.remove();
            } else {
                console.log('error deleting audio');
            }
        });
    });


    $('.selectContactGroup').click(function() {
        $('#group').modal('show')

    });
    group_input_array = new Array;
    inputs_group_id = $(".group_id_checkbox");
    group_id_array = new Array;
    group_name_array = new Array;
    if ($("input[name='group_id']").val() !== undefined && $("input[name='group_id']").val() !== "") {
        group_id_array = $("input[name='group_id']").val().split(",");
    }
    if ($("input[name='groups_name']").val() !== undefined && $("input[name='groups_name']").val() !== "") {
        group_name_array = $("input[name='groups_name']").val().split(",");
    }

    // group_id = $('input[name = group_id]').val().split(",");
    // group_name = $('input[name = groups_name]').val().split(",");

    // if (group_id !='') {
    //     group_id_array = $("input[name='group_id']").map(function(){return $(this).val();}).get();
    // }
    // if (group_name !='') {
    //      group_name_array = $("input[name='groups_name']").map(function(){return $(this).val();}).get();
    // }

    console.log('group_id_array,', group_id_array);
    console.log('group_name_array,', group_name_array);

    // $.each(inputs_group_id, function(index,input) {
    inputs_group_id.on("change", this, function() {
            if ($(this).is(":checked")) {
                let namesgroup = $(this).parent().siblings('td').first().text();
                namesgroup = jQuery.trim(namesgroup);
                group_input_array.push($(this));
                group_id_array.push($(this).val());
                group_name_array.push(namesgroup);

            } else {
                let namesgroup = $(this).parent().siblings('td').first().text();
                namesgroup = jQuery.trim(namesgroup);
                group_input_array.splice(group_input_array.indexOf($(this)), 1);
                group_id_array.splice(group_id_array.indexOf($(this).val()), 1);
                group_name_array.splice(group_name_array.indexOf(namesgroup), 1);

            }
        })
        // })
    $('.saveContact').click(function() {

        count_contacts = 0;
        $.each(group_input_array, function(index, input) {
            count_contacts += parseInt($(input).data('count'));
        })






        if (group_name_array == '') {
            $('.selectContactGroup').html('Выбрать группу контактов');
        } else {
            $('.selectContactGroup').html(group_name_array.join(","))
        }

        count_contacts = String(count_contacts);
        $('input[name=group_id]').val(group_id_array);
        $('input[name=group_id]').data('count', count_contacts);
        $('.box_popup.group').fadeOut();
        forwardPrice()
    });

    $('.autocalls tr').dblclick(function() {
        window.location.href = '/autocalls/update/' + $(this).data('id');
    });

    $('.table-6').dataTable({
        "language": {
            "sZeroRecords": "Вы еще не создавали автозвонки",
            "paginate": {
                "previous": '<span class="ar-1">arrow</span>',
                'next': '<span class="ar-4">arrow</span>'
            },
            "lengthMenu": "На странице: _MENU_",
            "processing": "Загрузка"
        },
        "bLengthChange": false,
        "bInfo": false,
        "columnDefs": [{
            "targets": 0,
            "orderable": false
        }],
        "filter": false,
        "paging": true
    });

    var url = window.location.href;
    if (url == 'https://cp.mediasend.kz/robot/index/0') {
        $('.itemlink2').addClass('active');
    } else {
        $('.itemlink1').addClass('active');
    }



    $('.chosen-container').on('click', function() {
        $(".chosen-drop").mCustomScrollbar({
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
    });

    $(document).on('click', '.colname .fa-pencil', function() {
        $(this).parent().find('.robot_name').removeAttr('readOnly');
        $(this).parent().find('.robot_name').focus();
    });

    $(document).on('dblclick', '.robot_name', function() {
        $(this).removeAttr('readOnly');
        $(this).focus();
    });

    $(document).on('focusout', '.robot_name', function() {
        $(this).attr('readOnly', 'true');
    });

    $('.tabs').click(function() {
        $('.tabcont').hide();
        $('.tabcont' + $(this).data('cont')).show();


        $('.tabs').removeClass('active');
        $(this).addClass('active');

        if ($(this).data('cont') != 1) {
            $('#content').addClass('gray');
        } else {
            $('#content').removeClass('gray');
        }
    });

    var delay = (function() {
        var timer = 0;
        return function(callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $('body').on('keyup', 'textarea[name=sms_message]', function(e) {
        let self = $(this);
        let has_sms = parseInt($('.count_sms').text().match(/\d/g));
        delay(function() {
            $.post(
                '/autocalls/length', {
                    message: self.val(),
                    latin: $('input[name=latin]:checked').length
                },
                function(data) {
                    if (data.length <= 2000) {
                        self.parent().find('em span').eq(0).text(data.length);
                        self.parent().find('em span').eq(1).text(data.count_sms * data.length_sms);
                        $('.count_sms').text('(' + data.count_sms + ' смс)');
                        if (has_sms == 0) {
                            forwardPrice();
                        } else {
                            forwardPrice();
                        }
                    } else {
                        e.preventDefault();
                    }
                },
                'json'
            );
        }, 0);
    });

    $('body').on('click keyup', 'textarea[name=robot_message]', function(e) {
        var self = $(this);
        delay(function() {
            $.post(
                '/account/forwards/length.php', {
                    message: self.val(),
                    latin: $('input[name=latin]:checked').length
                },
                function(data) {
                    if (data.length <= 2000) {
                        self.parent().find('em span').eq(0).text(data.length);
                        self.parent().find('em span').eq(1).text(data.count_sms * data.length_sms);
                        $('.count_sms').text('(' + data.count_sms + ' смс)');
                        forwardPrice();
                    } else {
                        e.preventDefault();
                    }
                },
                'json'
            );
        }, 0);
    });
    $('textarea[name=robot_message]').trigger('keyup');



    $('#tablestat').DataTable({
        "bStateSave": true,
        "scrollX": false,
        "dom": '<"class.scrooltable"t><"dataTableNav"i<"toolbar">l><"clear">',
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "ordering": false,
        "language": {
            "sZeroRecords": "Ничего не найдено",
            "info": "Всего: _MAX_",
            "paginate": {
                "previous": '<span class="ar-1">arrow</span>',
                'next': '<span class="ar-4">arrow</span>'
            },
            "lengthMenu": "На странице: _MENU_",
            "processing": "Загрузка"
        },
    });
    $(".dataTableNav .toolbar").html('<a href="#" class="export">Экспортировать в Excel</a>');


    //Код для робота start
    $(".robots select").chosen({
        disable_search_threshold: 100,
        placeholder_text_multiple: 'Выберите'
    });



    $('.robots').on('change', '.sel_files', function() {
        let robot_id = $(this).parents('.robot').data('robotId');
        $('.modal').data('robotId', robot_id);
        $("input[name=voice_id]").prop('checked', false);
        $("textarea[name=robot_message]").val('');
        if ($(this).val() == '1') {
            $("input[name=voice_id][value=" + $('.robot_voice' + robot_id).val() + "]").prop('checked', true);

            $('#audio').modal('show');

        } else if ($(this).val() == '2') {
            $("textarea[name=robot_message]").val($('.robot_message' + robot_id).val());
            $('textarea[name=robot_message]').click();

            $('#sms').modal('show');
        } else {
            $('#sms').modal('hide');
            $('#audio').modal('hide');
        }
    });

    $('.robot_type_edit').click(function() {
        $(this).siblings('.sel_files').trigger('change');
    });


    $('.close_popupp').click(function() {
        $('.box_popup.audio').fadeOut();
        $('.box_popup.sms').fadeOut();
    });


    function robot_actions(id, field, value) {
        $.ajax({
            url: '/robot/add',
            type: 'post',
            data: {
                id: id,
                field: field,
                value: value,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (id == 0) {
                    $('.robots').append(data);
                    $(".robots select").chosen({
                        disable_search_threshold: 100,
                        placeholder_text_multiple: 'Выберите'
                    });
                }

            }
        });
    }

    $('.add_bot').click(function(e) {
        robot_actions(0, 'add', 0);

    });

    $('.robot_search').on('keyup', function(e) {

        if ($('.robot_search').data('datatableid')) {
            $('#' + $('.robot_search').data('datatableid')).DataTable().ajax.reload();
        } else {
            window.location.href = window.location.href.split('?')[0] + '?name=' + $('.robot_search').val();
        }

    });
    $('.btn-search').on('click', function(e) {
        e.preventDefault();
        if ($('.robot_search').data('datatableid')) {
            $('#' + $('.robot_search').data('datatableid')).DataTable().ajax.reload();
        } else {
            window.location.href = window.location.href.split('?')[0] + '?name=' + $('.robot_search').val();
        }
    });




    $(".robot_name").on('keyup', function(e) {
        let robot_id = $(this).parents('.robot').data('robotId');
        let robot_name = $(this).val();

        if (robot_name != '') {
            robot_actions(robot_id, 'name', robot_name)
        }
    });

    $('.robots').on('change', '.robot_entity', function() {
        let robot_id = $(this).parents('.robot').data('robotId');
        let robot_entity = $(this).val();
        let robot_entity_type = $(this).find('option:selected').data('type');
        robot_actions(robot_id, 'entity', robot_entity);
        robot_actions(robot_id, 'entity_type', robot_entity_type)

    });

    $('.robots').on('change', '.robot_condition', function() {
        let robot_id = $(this).parents('.robot').data('robotId');
        let robot_condition = $(this).parents('.robot').find('.day').val() + ':' + $(this).parents('.robot').find('.hour').val() + ':' + $(this).parents('.robot').find('.minute').val() + ':' + ($(this).parents('.robot').find('.unique').is(':checked') ? 1 : 0);
        robot_actions(robot_id, 'condition', robot_condition)

    });

    $('.robots').on('change', '.robot_dftm', function() {
        let robot_id = $(this).parents('.robot').data('robotId');
        let robot_dftm = $(this).val();
        robot_actions(robot_id, 'dftm', robot_dftm)

    });

    $('.robots').on('click', 'input[name=status]', function() {
        let robot_id = $(this).parents('.robot').data('robotId');
        let robot_status = $(this).is(':checked') ? 1 : 2;
        robot_actions(robot_id, 'status', robot_status)

    });


    //Код для робота end


    //Код для рассылки на оставшуюся сумму

    $("#send_remaining_balance").on('click', this, function() {
        $('input[name=send_remaining_balance]').val(1);

        if ($('#sms-forward-form')) {
            if ($('.pricekz').text() !== '') {
                cost = $('.pricekz').text();
            } else {
                cost = $('.priceru').text();
            }
            if ($('.trru').css('display') !== 'none') {
                count_sms = $('.trru .countsms').text();
            } else {
                count_sms = $('.trkz .countsms').text();
            }

            $('input[name=message_cost]').val(cost);
            $('input[name=count_sms]').val(count_sms);
            $('#sms-forward-form').submit();
        }


        if ($('#autocall_create')) {
            call_cost = 0;
            sms_cost = 0;
            voice_cost = 0;

            if ($('.callpricekz').text() !== '0') {
                call_cost = $('.callpricekz').text();
            }

            if ($('.callpriceru').text() !== '0') {
                call_cost = $('.callpriceru').text();
            }

            if ($('.smspricekz').text() !== '0') {
                sms_cost = $('.smspricekz').text();
            }

            if ($('.smspriceru').text() !== '0') {
                sms_cost = $('.smspriceru').text();
            }

            if ($('.countfunct').text() !== '0') {
                voice_cost = Number($('.countfunct').text()) * 0.33;
            }
            cost = Number(call_cost) + Number(sms_cost) + Number(voice_cost);

            $('input[name=call_cost]').val(cost);

            $('#autocall_create').submit();

        }


    })

});