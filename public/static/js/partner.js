'use strict';

;


$('.other').click(function() {
  if ($('.other #che').is(':checked')) {
    $('.block_other').slideDown();
  } else {
    $('.block_other').slideUp();
  }
});


function copies() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  document.execCommand("copy");
  $('.alert-success').show();
  setTimeout(function() {
$('.alert-success').hide();
}, 2500);

}



$('.first').bind('input', function() {
  var first = $(this).val();
  var second = parseInt($(this).parents().eq(1).find('.two').text());
  second = second / 100;
  $(this).parents().eq(1).find('.rezult').text(first * second);
  var summa = 0;
  $(this).parents().eq(2).find('.rezult').each(function(i, elem) {
    summa = parseInt(summa) + parseInt($(elem).text());
  });
  $(this).parents().eq(2).find('.finale').text(summa);
  $('.itog1').text(parseInt($('.finale1').text()) + parseInt($('.finale2').text()));
  $('.itog2').text(parseInt($('.finale1').text()) + parseInt($('.finale2').text()) + parseInt($('.finale3').text()));

});

$('.details-button').click(function(e) {
  var row = e.target;
  var date = row.getAttribute('date');
  $('.detail-row').hide();
  $('.' + date).show();
});


$.fn.extend({
  toggleText: function(a, b) {
    return this.text(this.text() == b ? a : b);
  }
});

$('.tab-link').on('click', function(event) {
  event.preventDefault();
  $('[data-toggle="tab"][href="' + this.hash + '"]').trigger('click');
})


$('.demotoogle').on('click', function(event) {
  event.preventDefault();
  event.stopPropagation();
  $('.demo').slideToggle();
  $('.demo').removeClass('hidden');
  $(".demotoogle").toggleText('Показать DEMO', 'Скрыть DEMO');
  $(".demotoogle").toggleClass('blink');
  $(".textdemos").toggleClass('hide');
  $(".tbodydemos").toggleClass('demotext');


  var demo = $(event.target).attr("data-demo");

  $.get('/partner/statistic/demo/' + demo, {}, function(data) {
    if (demo) {
      $('.demo-wrapper').hide();
    } else {
      $('.demo-wrapper').show()
    }
  });
});

$('#country-select').on('change', function() {
  var country = $(this).find(":selected").val();
  $('#download-link').attr('href', '/partner/download/' + country);
  $('#terms-link').attr('href', '/partner/terms/' + country);
});

$('.form-control').focusout(function() {
  submitForm();
});

$('.control-checkbox').click(function(e) {
  submitForm();
});

$('#file-1,#file-2,#file-3,#file-4,#file-5').change(function() {

  var formData = new FormData();

  var name = $(this).attr('name')

  var file = this.files[0];

  formData.append(name, file);

  var url = "/partner/edit";

  $.ajax({
    type: "POST",
    url: url,
    data: formData,
    processData: false,
    contentType: false,
    success: function(data) {
      console.log('Uploaded File');
    }
  });
});

function submitForm() {
  var url = "/partner/edit";

  $.ajax({
    type: "POST",
    url: url,
    data: $("#edit-form").serialize(),
    success: function(data) {
      console.log('Updated');
    }
  });

  return false;
}

$('.submit-btn').click(function() {
  $('#invoiceModal').modal('hide');
});
