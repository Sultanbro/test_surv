<?php $__env->startSection('title', 'Настройки'); ?>
<?php $__env->startSection('content'); ?>

    <div class="animated fadeIn">
        <div class="row">

            <div class="col-md-12">

                <div id="app">

                    <div class="default-tab">
                        <nav>
                            <div class="nav nav-tabs set-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link <?php if($active_tab == 1): ?> active <?php endif; ?>" id="nav-person-tab" href="/timetracking/settings?tab=1#nav-person"  aria-controls="nav-person" aria-selected="false">Сотрудники</a>

                                <?php if(isset(auth()->user()->roles['page22']) && auth()->user()->roles['page22'] =='on'): ?>
                                <a class="nav-item nav-link <?php if($active_tab == 2): ?> active <?php endif; ?>" id="nav-home-tab"  href="/timetracking/settings?tab=2#nav-home"  aria-controls="nav-home" aria-selected="true">Должности</a>
                                <a class="nav-item nav-link <?php if($active_tab == 3): ?> active <?php endif; ?>" id="nav-profile-tab"  href="/timetracking/settings?tab=3#nav-profile"  aria-controls="nav-profile" aria-selected="false">Группы</a>
                                <a class="nav-item nav-link <?php if($active_tab == 4): ?> active <?php endif; ?>" id="nav-fines-tab"  href="/timetracking/settings?tab=4#nav-fines"  aria-controls="nav-fines" aria-selected="false">Штрафы</a>
                                <a class="nav-item nav-link <?php if($active_tab == 5): ?> active <?php endif; ?>" id="nav-notifications-tab"  href="/timetracking/settings?tab=5#nav-notifications" aria-controls="nav-notifications" aria-selected="false">Уведомления</a>
                                <a class="nav-item nav-link <?php if($active_tab == 6): ?> active <?php endif; ?>" id="nav-bookgroups-tab"  href="/timetracking/settings?tab=6#nav-bookgroups" aria-controls="nav-bookgroups" aria-selected="false">Обучение</a>
                                <?php endif; ?>
                            </div>
                        </nav>
                        <div class="tab-content pt-3" id="nav-tabContent">
                            <?php if($active_tab == 1): ?>
                                <div class="tab-pane fade show active" id="nav-person" role="tabpanel" aria-labelledby="nav-person-tab">
                                    <userlist ></userlist>
                                </div>
                            <?php endif; ?>
                            <?php if($active_tab == 2): ?>
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                    <professions :positions="<?php echo e(json_encode($positions)); ?>"></professions>
                                </div>
                            <?php endif; ?>
                            <?php if($active_tab == 3): ?>
                            <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <groups 
                                    statuseses="<?php echo e(json_encode($groups)); ?>" 
                                    archived_groupss="<?php echo e(json_encode($archived_groups)); ?>" 
                                    book_groups="<?php echo e(json_encode($book_groups)); ?>"
                                    corpbooks="<?php echo e(json_encode($corpbooks)); ?>"
                                    activeuserid="<?php echo e(json_encode(auth()->user()->ID)); ?>"
                                    ></groups>
                            </div>
                            <?php endif; ?>
                            <?php if($active_tab == 4): ?>
                            <div class="tab-pane fade  show active" id="nav-fines" role="tabpanel" aria-labelledby="nav-fines-tab">
                                <fines/>
                            </div>
                            <?php endif; ?>
                            <?php if($active_tab == 5): ?>
                            <div class="tab-pane fade show active" id="nav-notifications" role="tabpanel" aria-labelledby="nav-notifications-tab">
                                <s-notifications groups_with_id="<?php echo e(json_encode($groupsWithId)); ?>"
                                    :users="<?php echo e(json_encode($tab5['users'])); ?>"
                                    :positions="<?php echo e(json_encode($tab5['positions'])); ?>"
                                />
                            </div>
                            <?php endif; ?>

                            <?php if($active_tab == 6): ?>
                            <div class="tab-pane fade show active" id="nav-bookgroups" role="tabpanel" aria-labelledby="nav-bookgroups-tab">
                                <bookgroups></bookgroups>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>