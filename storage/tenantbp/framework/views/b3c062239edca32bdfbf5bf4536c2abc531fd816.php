<?php $__env->startSection('title', 'Настройки'); ?>
<?php $__env->startSection('content'); ?>

<div class="old__content">

    <div class="">
        <div class="">
 
            <div class="c">

                <div id="app">

                    <div class="default-tab">
                        <nav class="normal mt-4">
                            <div class="nav nav-tabs set-tabs" id="nav-tab" role="tablist">
                                <?php if(auth()->user()->can('users_view') || auth()->user()->can('settings_view')): ?><a class="nav-item nav-link <?php if($active_tab == 1): ?> active <?php endif; ?>" id="nav-person-tab" href="/timetracking/settings?tab=1#nav-person"  aria-controls="nav-person" aria-selected="false">Сотрудники</a><?php endif; ?>
                                <?php if(auth()->user()->can('positions_view') || auth()->user()->can('settings_view')): ?><a class="nav-item nav-link <?php if($active_tab == 2): ?> active <?php endif; ?>" id="nav-home-tab"  href="/timetracking/settings?tab=2#nav-home"  aria-controls="nav-home" aria-selected="true">Должности</a><?php endif; ?>
                                <?php if(auth()->user()->can('groups_view') || auth()->user()->can('settings_view')): ?><a class="nav-item nav-link <?php if($active_tab == 3): ?> active <?php endif; ?>" id="nav-profile-tab"  href="/timetracking/settings?tab=3#nav-profile"  aria-controls="nav-profile" aria-selected="false">Отделы</a><?php endif; ?>
                                <?php if(auth()->user()->can('fines_view') || auth()->user()->can('settings_view')): ?><a class="nav-item nav-link <?php if($active_tab == 4): ?> active <?php endif; ?>" id="nav-fines-tab"  href="/timetracking/settings?tab=4#nav-fines"  aria-controls="nav-fines" aria-selected="false">Штрафы</a><?php endif; ?>
                                <?php if(auth()->user()->can('notifications_view') || auth()->user()->can('settings_view')): ?><a class="nav-item nav-link <?php if($active_tab == 5): ?> active <?php endif; ?>" id="nav-notifications-tab"  href="/timetracking/settings?tab=5" aria-controls="nav-notifications" aria-selected="false">Уведомления</a><?php endif; ?>
                                <?php if(auth()->user()->can('permissions_view') || auth()->user()->can('settings_view')): ?><a class="nav-item nav-link <?php if($active_tab == 6): ?> active <?php endif; ?>" id="nav-permissions-tab"  href="/timetracking/settings?tab=6#nav-permissions" aria-controls="nav-permissions" aria-selected="false">Доступы</a><?php endif; ?>
                                <?php if(auth()->user()->can('checklists_view') || auth()->user()->can('settings_view')): ?><a class="nav-item nav-link <?php if($active_tab == 7): ?> active <?php endif; ?>" id="nav-checkList-tab"  href="/timetracking/settings?tab=7#nav-checkList" aria-controls="nav-checkList" aria-selected="false">Чек-листы</a><?php endif; ?>
                                <?php if(auth()->user()->is_admin == 1): ?><a class="nav-item nav-link <?php if($active_tab == 8): ?> active <?php endif; ?>" id="nav-integrations-tab"  href="/timetracking/settings?tab=8#nav-integrations" aria-controls="nav-integrations" aria-selected="false">Интеграции</a><?php endif; ?>
                                <?php if(auth()->user()->is_admin == 1): ?><a class="nav-item nav-link <?php if($active_tab == 9): ?> active <?php endif; ?>" id="nav-awards-tab"  href="/timetracking/settings?tab=9#nav-awards" aria-controls="nav-awards" aria-selected="false">Награды</a><?php endif; ?>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <?php if($active_tab == 1 && (auth()->user()->can('users_view') || auth()->user()->can('settings_view'))): ?>
                                <div class="tab-pane fade show active  p-3" id="nav-person" role="tabpanel" aria-labelledby="nav-person-tab">
                                    <userlist :is_admin="<?php echo e(auth()->user()->is_admin == 1 ? 'true' : 'false'); ?>" subdomain="<?php echo e(tenant('id')); ?>" positions="<?php echo e(json_encode(\App\Position::all())); ?>"></userlist>
                                </div>
                            <?php endif; ?>
                            <?php if($active_tab == 2 && (auth()->user()->can('positions_view') || auth()->user()->can('settings_view'))): ?>
                                <div class="tab-pane fade show active  p-3" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                    <professions :positions="<?php echo e(json_encode($positions)); ?>"></professions>
                                </div>
                            <?php endif; ?>
                            <?php if($active_tab == 3 && (auth()->user()->can('groups_view') || auth()->user()->can('settings_view'))): ?>
                            <div class="tab-pane fade show active   p-3" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <groups 
                                    statuseses="<?php echo e(json_encode($groups)); ?>" 
                                    archived_groupss="<?php echo e(json_encode($archived_groups)); ?>" 
                                    book_groups="<?php echo e(json_encode($book_groups)); ?>"
                                    corpbooks="<?php echo e(json_encode($corpbooks)); ?>"
                                    activeuserid="<?php echo e(json_encode(auth()->user()->id)); ?>"
                                    ></groups>
                            </div>
                            <?php endif; ?>
                            <?php if($active_tab == 4 && (auth()->user()->can('fines_view') || auth()->user()->can('settings_view'))): ?>
                            <div class="tab-pane fade  show active   p-3" id="nav-fines" role="tabpanel" aria-labelledby="nav-fines-tab">
                                <fines/>
                            </div>
                            <?php endif; ?>
                            <?php if($active_tab == 5 && (auth()->user()->can('notification_view') || auth()->user()->can('settings_view'))): ?>
                            <div class="tab-pane fade show active   p-3" id="nav-notifications" role="tabpanel" aria-labelledby="nav-notifications-tab">
                                <s-notifications groups_with_id="<?php echo e(json_encode($groupsWithId)); ?>"
                                    :users="<?php echo e(json_encode($tab5['users'])); ?>"
                                    :positions="<?php echo e(json_encode($tab5['positions'])); ?>"
                                />
                            </div>
                            <?php endif; ?>
                            <?php if($active_tab == 6 && (auth()->user()->can('permissions_view') || auth()->user()->can('settings_view'))): ?>
                            <div class="tab-pane fade show active   p-3" id="nav-bookgroups" role="tabpanel" aria-labelledby="nav-bookgroups-tab">
                            <permissions />
                            </div>
                            <?php endif; ?>
                        

                            <?php if($active_tab == 7 && (auth()->user()->can('checklists_view') || auth()->user()->can('settings_view'))): ?>
                                    <div class="tab-pane fade show active   p-3" id="checkList" role="tabpanel" aria-labelledby="nav-checkList-tab">
                                        <check-list/>
                                    </div>
                            <?php endif; ?>

                            <?php if($active_tab == 8 && auth()->user()->is_admin == 1): ?>
                                <div class="tab-pane fade show active p-3" id="integrations" role="tabpanel" aria-labelledby="nav-integrations-tab">
                                    
                                    <div class="d-flex">
                                        <div class="d-flex jcc aic mr-2 flex-column" style="width: 150px; height: 120px;padding: 7.5px 10px;background: #f8fcfe;border: 1px solid #daecf5;">
                                            Bitrix24 <span style="color:red;font-size:8px">Не настроен</span>
                                        </div>

                                        <div class="d-flex jcc aic mr-2 flex-column" style="width: 150px; height: 120px;padding: 7.5px 10px;background: #f8fcfe;border: 1px solid #daecf5;">
                                            AmoCRM <span style="color:red;font-size:8px">Не подключен</span>
                                        </div>

                                        <div class="d-flex jcc aic mr-2 flex-column" style="width: 150px; height: 120px;padding: 7.5px 10px;background: #f8fcfe;border: 1px solid #daecf5;">
                                            Callibro <span style="color:red;font-size:8px">Не настроен</span>
                                        </div>
                                    </div>

                                </div>
                            <?php endif; ?>
                            <?php if($active_tab == 9 && auth()->user()->is_admin == 1): ?>
                                <div class="tab-pane fade show active p-3" id="awards" role="tabpanel" aria-labelledby="nav-awards-tab">
                                    <awards />
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<style>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
.container.container-left-padding {
    padding-left: 7rem !important; 
}
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/job/resources/views/admin/settingtimetracking.blade.php ENDPATH**/ ?>