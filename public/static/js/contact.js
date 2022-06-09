$(function () {
    //contacts
    // checked_contacts = $('#contactslist').find('input[name=remove]:checked');
    // arr_checked_contacts = jQuery.makeArray(checked_contacts);
    // values = new Array();
    // jQuery.map(arr_checked_contacts, function(el) {
    //     values.push(el.value);
    //     // console.log(values);
    // });
    let arr = $('#grouplist').find('tr.active').find('a').attr('href').split('/');
    if (arr[3]) {
        $('#id_group').val(arr[3]);
    }
    values = new Array();
    function contactbuttonOnChange () {
        if (values.length > 0) {
            $('#sending_form').stop().fadeIn(0);
        } else {
            $('#sending_form').stop().fadeOut(0);
        }
        if (values.length < $('#contactslist input[name=remove]').length) {
            $(".removeAll").prop('checked', false);
        }
    }
    var contactslist = $('#contactslist').DataTable({
        "bStateSave": true,
        "scrollX": false,
        "dom": '<"class.scrooltable"t><"dataTableNav"ipl><"clear">',
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "ordering": true,
        select: {
            style: 'os',
            selector: 'td:first-child'
        },
        "language": {
            "sZeroRecords": "В данной группе нет контактов",
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
        "serverSide": true,
        "bDestroy": true,
        "ajax": {
            "url": $('#contactslist').data('url'),
            "type": "POST",
            "data": function (d) {
                d['_token'] = $('meta[name="csrf-token"]').attr('content');
                d['group'] = $('#contactslist').data('group');
                d['search_phone'] = $('input[name=search_phone]').val()
            }
        },
        "drawCallback": function () {

            if (values) {
                $.each(values, function (ind, elem) {
                    $('#contact' + elem).attr('checked', true);
                })
            }
        
            // if ($('.removeAll').is(":checked")) {
            //     values = [];
            //     $('#contactslist input[name=remove]').each(function (ind, el) {
            //         $(this).prop('checked', true);

            //         values.push($(el).val());
            //     });
            //     contactbuttonOnChange();
            // } else {
            //     $('#contactslist input[name=remove]').each(function (ind, el) {
            //         $(this).prop('checked', false);
            //     });
            //     values = [];
            //     contactbuttonOnChange();
            // }

            $('#grouplist input[name=remove]').on('change', this, function () {
                if ($(this).is(":checked")) {
                    
                    $("#sending_form option[value='sms']").hide();
                    $("#sending_form option[value='call']").hide();
                    contactbuttonOnChange();
                } else {
                    $("#sending_form option[value='sms']").show();
                    $("#sending_form option[value='call']").show();
                    contactbuttonOnChange();
                }
            });
            $('#contactslist input[name=remove]').on('change', this, function () {
                if ($(this).is(":checked")) {
                    $('#deleteContacts').stop().fadeIn(0);
                    $('#copyContacts').stop().fadeIn(0);
                    
                    values.push($(this).val());
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'checked_contact' + $(this).val(),
                        name: 'contact' + $(this).val(),
                        value: $(this).val(),
                        class: 'new_contact_inputs'
                    }).appendTo('#sending_form');
                    contactbuttonOnChange();
                } else {
                    $('#deleteContacts').stop().fadeOut(0);
                    $('#copyContacts').stop().fadeOut(0);
                    
                    values.splice(values.indexOf($(this).val()), 1);
                    $('#checked_contact' + $(this).val()).remove();
                    contactbuttonOnChange();
                }
            });
        },

        "formatNumber": function (toFormat) {
            return toFormat.toString().replace(
                /\B(?=(\d{3})+(?!\d))/g, " "
            );
        }
    });


    $('#fileupload').fileupload({
        url: '/contact/import1',
        submit: function (e, data) {
            if ($("select[name=group_id]").val() == "") {
                $("select[name=group_id]").addClass('blink');
                return false;
            }
            data.formData = {
                group_id: $("select[name=group_id]").val()
            };
        },
        done: function (e, data) {
            $('.import-first-step').hide();
            $('.import-second-step').html(data.result);
            $('.import-second-step').show();
            $('#import-modal li:nth-child(2) a').addClass('active');

            $("#second_form").submit(function (event) {
                $('#second-form-message').text('');
                if (!$('.is-contact-phone').filter(':selected').length) {
                    $('#second-form-message').text('Выберите Номер телефона!');
                    event.preventDefault();
                }
                $('#second_form .contact-modal-yellow-btn').val('идет загрузка...');
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        },
        fail: function (e, data) {
            console.log(data.jqXHR.responseText);
        },
    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');


    $('#contactslist .removeAll').change(function () {
        if ($('.removeAll').is(":checked")) {
            values = [];
            $('#contactslist input[name=remove]').each(function (ind, el) {
                $(this).prop('checked', true);

                values.push($(el).val());
            });
            contactbuttonOnChange();
        } else {
            $('#contactslist input[name=remove]').each(function (ind, el) {
                $(this).prop('checked', false);
            });
            values = [];
            contactbuttonOnChange();
        }
    });


    $('#exportContacts').click(function (e) {
        e.preventDefault();

        let contacts_group = [];

        $('#grouplist input[name=remove]:checked').each(function (ind, elem) {
            contacts_group.push($(elem).val());
        });

        if (contacts_group.length == 0) {
            contacts_group.push($(this).data('group'));
        }

        $('#export_group_ids').val(contacts_group);

        $('#export_group_form').submit();
    });

    $(document).unbind().on("change", "#sending", function () {
        $(document).unbind().on('click', '#sending_submit_button', function (e) {
            e.preventDefault();
            if ($("#sending").find('option:selected').val() == "sms" && $("#sending").find('option:selected').val() !== "call") {

                $.ajax({
                    type: 'POST',
                    url: '/contact/index/',
                    data: {
                        sending: $('#sending').val(),
                        group_id: $('#id_group').val(),
                        values: values
                    },
                    success: function (data) {
                        // console.log(data);
                        document.location.href = '/sms/update/';
                    }
                })
            } else if ($("#sending").find('option:selected').val() == "call" && $("#sending").find('option:selected').val() !== "sms") {
                $.ajax({
                    type: 'POST',
                    url: '/contact/index/',
                    data: {
                        sending: $('#sending').val(),
                        group_id: $('#id_group').val(),
                        values: values
                    },
                    success: function (data) {
                        // console.log('test');
                        document.location.href = '/autocalls/update/';
                    }
                })
            } else if ($("#sending").find('option:selected').val() == "copyContacts") {
                let contacts_group = [];
                let contacts = [];

                $('#grouplist input[name=remove]:checked').each(function (ind, elem) {
                    contacts_group.push($(elem).val());
                });

                $('#contactslist input[name=remove]:checked').each(function (ind, elem) {
                    contacts.push($(elem).val());
                });

                if (contacts_group.length > 0) {
                    $('#group-copy-modal').modal();
                    $('#copy_group_ids').val(contacts_group);
                } else if (contacts.length > 0) {
                    $('#contact-copy-modal').modal();
                    $('#copy_contact_ids').val(contacts);
                }
            } else if ($("#sending").find('option:selected').val() == "deleteContacts") {
                var contacts = [];
                var contacts_group = [];

                $('#contactslist input[name=remove]:checked').each(function (ind, elem) {
                    contacts.push($(elem).val());
                });

                $('#grouplist input[name=remove]:checked').each(function (ind, elem) {
                    contacts_group.push($(elem).val());
                });

                $.post(
                    '/contact/delete',
                    {
                        contacts: contacts,
                        groups: contacts_group
                    }, function (data) {
                        window.location.reload();
                    }
                );
            }
        });
    });
});
