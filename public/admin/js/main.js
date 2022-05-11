$.noConflict();

jQuery(document).ready(function($) {

  "use strict";



  $('#file-1,#file-2,#file-3,#file-4,#file-5').change(function() {

    var formData = new FormData();

    var name = $(this).attr('name')

    var file = this.files[0];

    formData.append(name, file);

    var url = "/partner/edit";

    /*    $.ajax({
           type: "POST",
           url: url,
           data: formData,
           processData: false,
           contentType: false,
           success: function(data) {
               console.log('Uploaded File');
           }
       });
       */

  });




  [].slice.call(document.querySelectorAll('select.cs-select')).forEach(function(el) {
    new SelectFx(el);
  });

  jQuery('.selectpicker').selectpicker;


  $('#menuToggle').on('click', function(event) {
    $('body').toggleClass('open');
  });

  $('.search-trigger').on('click', function(event) {
    event.preventDefault();
    event.stopPropagation();
    $('.search-trigger').parent('.header-left').addClass('open');
  });

  $('.search-close').on('click', function(event) {
    event.preventDefault();
    event.stopPropagation();
    $('.search-trigger').parent('.header-left').removeClass('open');
  });

  // $('.user-area> a').on('click', function(event) {
  // 	event.preventDefault();
  // 	event.stopPropagation();
  // 	$('.user-menu').parent().removeClass('open');
  // 	$('.user-menu').parent().toggleClass('open');
  // });

 /* $('.card-body').hide();

  $('.card-header').on('click', function(event) {
    event.preventDefault();
    event.stopPropagation();
    $(this).parent().parent().toggleClass('col-lg-12');
    $(this).parent().find('.card-body').slideToggle();


  });

*/
  (function(document, window, index) {/*
    var inputs = document.querySelectorAll('.inputfile');
    Array.prototype.forEach.call(inputs, function(input) {
      var label = input.nextElementSibling,
          labelVal = label.innerHTML;

      input.addEventListener('change', function(e) {
        var fileName = '';
        if (this.files && this.files.length > 1)
          fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
        else
          fileName = e.target.value.split('\\').pop();

        if (fileName)
          label.querySelector('span').innerHTML = fileName;
        else
          label.innerHTML = labelVal;
      });

      // Firefox bug fix
      input.addEventListener('focus', function() {
        input.classList.add('has-focus');
      });
      input.addEventListener('blur', function() {
        input.classList.remove('has-focus');
      });
    });*/
  }(document, window, 0));

});
