$(function () {
    //sonic

    var soniclist = $('#soniclist').DataTable({
        "bStateSave" : true,
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

    $('input[name=call_start_time]').on('keyup', function () {
        $('input[name=call_start_time]').get(0).setCustomValidity("");
    });

    $('input[name=call_end_time]').on('keyup', function () {
        $('input[name=call_end_time]').get(0).setCustomValidity("");
    });


    $('#group .savesonic').click(function() {

            $('.selectContactGroup').html($('input[name=group_sonic_id]:checked').parent().parent().find('td:nth-child(2)').text())

    });

    $('.links a').click(function(e){
        id = $(this).data('id');
        status = $(this).data('status');

        $.ajax({
            url: '/sonic/status/'+id,
            type: 'GET',
            dataType: 'JSON',
            data: {
                id : id,
                status : status,
                _token : $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                 window.location.reload();
            }
        });

        e.preventDefault();
    });


    $('#sonic-form').submit(function(e){

        if ($('input[name=voice_id]:checked').val()  == undefined) {
            $('#autoaudio').modal('show')
            e.preventDefault();
        }

        if ($('input[name=group_sonic_id]:checked').val()  == undefined) {
            $('#group').modal('show')
            e.preventDefault();
        }

        let call_start_time = parseInt($('input[name=call_start_time]').val().replace(':', ''));
        let call_end_time = parseInt($('input[name=call_end_time]').val().replace(':', ''));

        if (call_end_time <= call_start_time) {
            $('input[name=call_end_time]').css('border', '1px solid #f00');
            $('input[name=call_end_time]').get(0).setCustomValidity("Время окончания должен быть больше время начало!");
            $('input[name=call_end_time]').get(0).reportValidity();
            e.preventDefault();
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

        button0_action = $('input[name=button0_action][data-value="'+button0+'"]').val();
        button1_action = $('input[name=button1_action][data-value="'+button1+'"]').val();
        button2_action = $('input[name=button2_action][data-value="'+button2+'"]').val();
        button3_action = $('input[name=button3_action][data-value="'+button3+'"]').val();
        button4_action = $('input[name=button4_action][data-value="'+button4+'"]').val();
        button5_action = $('input[name=button5_action][data-value="'+button5+'"]').val();
        button6_action = $('input[name=button6_action][data-value="'+button6+'"]').val();
        button7_action = $('input[name=button7_action][data-value="'+button7+'"]').val();
        button8_action = $('input[name=button8_action][data-value="'+button8+'"]').val();
        button9_action = $('input[name=button9_action][data-value="'+button9+'"]').val();

        $('input[name=funct_0]').val(button0_action+':'+button0);
        $('input[name=funct_1]').val(button1_action+':'+button1);
        $('input[name=funct_2]').val(button2_action+':'+button2);
        $('input[name=funct_3]').val(button3_action+':'+button3);
        $('input[name=funct_4]').val(button4_action+':'+button4);
        $('input[name=funct_5]').val(button5_action+':'+button5);
        $('input[name=funct_6]').val(button6_action+':'+button6);
        $('input[name=funct_7]').val(button7_action+':'+button7);
        $('input[name=funct_8]').val(button8_action+':'+button8);
        $('input[name=funct_9]').val(button9_action+':'+button9);
    });
});
