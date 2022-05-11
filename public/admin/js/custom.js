jQuery(document).ready(function($) {

    $(".standardSelect").chosen({
        disable_search_threshold: 10,
        hide_results_on_select:false,
        no_results_text: "Oops, nothing found!",
        width: "400px"
    });

    $('.selectallchossen').click(function(){
        let idss = $(this).data('id');
        $('select#'+idss+' option').prop('selected', true);
        console.log('select#'+idss+' option')
        $('.standardSelect').trigger("chosen:updated");
    });


    $('input[name=books]').on("change", this, function() {
        $(".books_block").toggle();
    });
});

$('.details-button').click(function(e) {
    var row = e.target;
    var date = row.getAttribute('date');
    $('.detail-row').hide();
    $('.'+date).show();
    $('.sorting_desc, .sorting_asc').trigger('click');
});
book_inputs_array = new Array;
inputs = $("input[name=books_ids]");
if ($("input[name=books_ids]").val() !== undefined) {
    book_inputs_array = $("input[name=books_ids]").val().split(',');
}
book_inputs = $(".book_input");

book_inputs.on("change", this, function() {

    if($(this).is(":checked")) {

        book_inputs_array.push($(this).val());
        $("input[name=books_ids]").val(book_inputs_array);
      
    } else {

      book_inputs_array.splice(book_inputs_array.indexOf($(this).val()), 1);
      $("input[name=books_ids]").val(book_inputs_array); 

    }

})
if ($('input[name=books]').is(":checked")) {
    $(".books_block").css('display', 'block');
} else {
    $(".books_block").css('display', 'none');
}



$('#notification-preview').click(function(e) {

    $('#notification-form').attr('target', '_blank');
    $('#preview-field').val('preview');
    
    $('#notification-form').submit();

    $('#notification-form').attr('target', '');
    $('#preview-field').val('');
});