$(function() {
    //settings

    $('.details-button').click(function(e) {
        var row = e.target.parentNode.parentNode;
        var date = row.getAttribute('date');
        $('.details-button').attr("src", "/static/images/plus.png");

        $('.detail-row').remove();


        if ($(e.target).hasClass("expanded")) {
            $(e.target).removeClass("expanded");
            $(e.target).attr("src", "/static/images/plus.png");
            return;
        }

        $('.details-button').removeClass("expanded");
        $(e.target).attr("src", "/static/images/minus.png");
        $(e.target).addClass("expanded");

        $.get('/setting/transaction/details/', { date: date }, function(data) {

            var services = {
                voice_autocall: ['Автозвонки', 0, 0],
                voice_integration: ['Автозвонки по api', 0, 0],
                sms_integration: ['Смс по api', 0, 0],
                smpp: ['SMPP интеграция', 0, 0],
                sip: ['SIP интеграция', 0, 0],
                sms: ['Смс рассылка', 0, 0],
                robot: ['Роботы', 0, 0],
                callibro: ['U-Calls', 0, 0],
                rent_monthly: ['Плата за номер', 0, 0],
            };

            $.each(data.rows, function(index, value) {

                if (value.type.includes('voice_autocall') || value.type.includes('sms_autocall')) {
                    services.voice_autocall[1] += Number(value.total);
                    services.voice_autocall[2] += Number(value.total_count);
                } else if (value.type.includes('voice_integration')) {
                    services.voice_integration[1] += Number(value.total);
                    services.voice_integration[2] += Number(value.total_count);
                } else if (value.type.includes('sms_integration')) {
                    services.sms_integration[1] += Number(value.total);
                    services.sms_integration[2] += Number(value.total_count);
                } else if (value.type.includes('smpp')) {
                    services.smpp[1] += Number(value.total);
                    services.smpp[2] += Number(value.total_count);
                } else if (value.type.includes('sip')) {
                    services.sip[1] += Number(value.total);
                    services.sip[2] += Number(value.total_count);
                } else if (value.type.includes('robot')) {
                    services.robot[1] += Number(value.total);
                    services.robot[2] += Number(value.total_count);
                } else if (value.type.includes('sms')) {
                    services.sms[1] += Number(value.total);
                    services.sms[2] += Number(value.total_count);
                } else if (value.type.includes('callibro')) {
                    services.callibro[1] += Number(value.total);
                    services.callibro[2] += Number(value.total_count);
                } else if (value.type.includes('rent_number')) {
                    services.rent_number[1] += Number(value.total);
                    services.rent_number[2] += Number(value.total_count);
                } else if (value.type.includes('rent_monthly')) {
                    services.rent_monthly[1] += Number(value.total);
                    services.rent_monthly[2] += Number(value.total_count);
                } else {
                    services[value.type] = [value.type, value.total, value.total_count];
                }
            });

            $.each(services, function(index, value) {
                if (value[1] > 0) {
                    var detailRow = document.createElement('tr');
                    $(detailRow).addClass("detail-row");
                    detailRow.insertCell(-1);
                    detailRow.insertCell(-1);
                    detailRow.insertCell(-1);
                    var amount = detailRow.insertCell(-1);
                    amount.innerHTML = '<em>' + value[1] + '</em>';
                    var total_count = detailRow.insertCell(-1);
                    total_count.innerHTML = '<span style="color:red;">' + value[2] + '</span>';
                    var service = detailRow.insertCell(-1);
                    service.innerHTML = value[0];
                    detailRow.insertCell(-1);
                    detailRow.insertCell(-1);

                    row.parentNode.insertBefore(detailRow, row.nextSibling);
                }
            });

        });

    });

    var transaktion_list_table = $('#transaktion_list_table').DataTable({
        "bStateSave": true,
        "scrollX": false,
        "dom": '<"class.scrooltable"t><"dataTableNav"p><"clear">',
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "ordering": false,
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

    // потом наверное надо будеть удалить 
    $('form.generatePay').submit(function(e) {
        e.preventDefault();

        let form = $(this);
        let amount = form.find('input[name=amount]').val();
        let phone = form.find('input[name=phone]').val();
        let name = form.find('input[name=name]').val();

        data = {
            amount: amount,
            phone: phone,
            name: name,
            action: 'createOrderScore'
        };

        $.ajax({
            url: '/setting/invoice',
            data: data,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                form.find('input[type=submit]').attr('disabled', 'disabled');
            },
            success: function(out) {
                console.log(out);
                if (out.success == true) {
                    form.find('.message').html('<div class="alert alert-success">Спасибо! Мы получили вашу заявку и скоро свяжемся с Вами.</div>');
                } else {
                    form.find('.message').html('<div class="alert alert-danger">' + out.error + '</div>');
                    form.find('input[type=submit]').removeAttr('disabled')
                }
            },
            error: function(a, b, c) {
                console.log(a, b, c);
            }
        });
    });


    $('form.wallet input[type=submit]').click(function(e) {
        e.preventDefault();
        let form = $('form.wallet');

        let amount = form.find('input[name=amount]').val();
        let phone = form.find('input[name=phone]').val();



        form.find('input[name=amount], input[name=phone]').css('border', 'none');

        if (phone == '') {
            form.find('input[name=phone]').css('border', '1px solid #f00');
            return false;
        }

        if (amount == '') {
            form.find('input[name=amount]').css('border', '1px solid #f00');
            return false;
        }


        data = {
            amount: amount,
            phone: phone,
            action: 'createOrder'
        };

        $.ajax({
            url: '/setting/card',
            data: data,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {

            },
            success: function(out) {
                console.log(out);
                if (out.success == true) {
                    form.after(out.form);
                    $('#walletone').trigger('submit');
                }
            },
            error: function(a, b, c) {
                console.log(a, b, c);
            }
        });


    });



    var UniqName = $('#UniqName').DataTable({
        "bStateSave": true,
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


    var stoplisttable = $('#stoplist').DataTable({
        "bStateSave": false,
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

    $('#inputstoplist').on('keyup', function() {
        stoplisttable.search(this.value).draw();
    });


});