
<script>
$(".sidemenu").on("click", function() {
    $(".sidemenu i.opener").toggleClass("show", 300);
    $(".sidemenu i.closer").toggleClass("show", 300);
    $("#left-panel").toggleClass("show-sidebar", 500);
    $("#right-panel").toggleClass("show-sidebar", 500);
});

$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
    if (!$(this).next().hasClass('show')) {
        $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
    }
    var $subMenu = $(this).next('.dropdown-menu');
    $subMenu.toggleClass('show');


    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
        $('.dropdown-submenu .show').removeClass('show');
    });

    return false;
});
</script>

<script>
    var hash = window.location.hash;
    $('#videomenu').click(function() {
        $('.side-menu').toggleClass('show');
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script>
$('.user').click(function(event) {
    event.stopPropagation();
    $('.profile-menu').toggleClass('show');
});
$(window).click(function() {
    $('.profile-menu').removeClass('show');
});
$('.profile-menu').click(function(event){
    event.stopPropagation();
});
</script>