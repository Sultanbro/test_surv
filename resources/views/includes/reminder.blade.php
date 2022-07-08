<div class="reminder" id="reminder">
    <div class="inner">
        <h5 class="text-center ae mb-5">У вас есть непрочитанные уведомления ({{ $unread }})</h5>

        <button class="btn btn-primary rounded m-auto btn-sm mt-3" id="goToNoti">
            <span class="text">Перейти к уведомлениям</span>
        </button>
    </div>      
</div>



<script>
$(document).ready(function() {

    $('#goToNoti').click(function () {
        $('#reminder').hide();
        $('#toggle_panel').trigger('click');
        window.scrollTo(0,0)
    });
});


</script>
<style>
.m-auto {
    margin: 0 auto;
    display: block;
    cursor: pointer;
}
.aet {
    color: #042a40c9;
    font-size: 18px;
    border-bottom: 1px dashed #e1e1e1;
    padding-bottom: 9px;
}
.reminder {
    position: fixed;
    width: 100vw;
    top: 0;
    z-index: 9999;
    height: 100vh;
    padding: 15px 30px 15px;
    background: rgba(0,0,0,0.7);
}
.reminder .inner {
    margin: 0 auto;
    border-radius: 5px;
    padding: 20px 20px 20px;
    background: #fff;
    width: 410px;
    overflow: auto;
    max-height: 100%;
}
</style>