<aside class="right-menu">

<div class="top">

<div class="btn-rm">
    <i class="fa fa-question"></i>
</div>
  
<div class="btn-rm" id="toggle_panel">
    <i class="fa fa-bell"></i>
    <div class="blink-notification" style="border-color: rgb(0, 242, 246); position: absolute; inset: 0px; border-radius: 50%;z-index: -1;"></div>
    <?php if($unread > 0): ?>
    <div class="nn-number" style="display:none">

        <div class="pulse"></div>
        <div class="marker"></div>
    </div>
    <?php endif; ?>
</div>





    <?php if(!empty(auth()->user()->checklists->toArray())): ?>
        <auth-check-list :auth_check_list="<?php echo e(json_encode(auth()->user()->checklists)); ?>" :open_check="<?php echo e(0); ?>"></auth-check-list>
    <?php endif; ?>
<div class="btn-rm">
    <i class="fa fa-search"></i>
</div>



</div>
<div class="chat noscrollbar">
    
</div>
</aside>
<?php /**PATH /var/www/job/resources/views/layouts/right_menu.blade.php ENDPATH**/ ?>