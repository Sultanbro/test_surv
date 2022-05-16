<?php $__env->startSection('title', 'Сотрудник'); ?>
<?php $__env->startSection('content'); ?>

<div class="animated fadeIn user-page">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-between align-items-start">
            <a href="/timetracking/settings?tab=1" class="btn btn-rounded"
                style="background: #a0a6ab;color: white;font-size: 14px;">
                <i class="fa fa-chevron-left"></i> Назад
            </a>


            <div class="data-information">
                <?php if(isset($user)): ?>
                    <?php if($user->is_trainee): ?>
                    <button class="btn btn-warning mr-2 rounded" id="submit_job">Принять на работу</button>
                    <button class="btn btn-primary mr-2 rounded" id="submit_trainee">Сохранить</button>
                    <?php else: ?>
                    <button class="btn btn-primary mr-2 rounded" id="submitx">Сохранить</button>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-primary mr-2 rounded" id="submitx2">Пригласить без стажировки</button>
                    <button class="btn btn-warning mr-2 rounded" id="submit_trainee">Пригласить со стажировкой</button>
                <?php endif; ?>
                <?php if(isset($user)): ?>
                <?php if( is_null($user->deleted_at)): ?>

                    <?php if(!$user->is_trainee): ?>
                    <button type="button" class="btn btn-danger rounded mr-2" id="deleteModalBtn" data-toggle="modal"
                    data-target="#deactivateUserModal">
                        Уволить без отработки
                    </button>
                    <button type="button" class="btn btn-danger rounded" id="deleteModalBtn2" data-toggle="modal"
                        data-target="#deactivateUserModal">
                        Уволить с отработкой
                    </button>
                    <?php else: ?>
                    <button type="button" class="btn btn-danger rounded" id="deleteModalBtn" data-toggle="modal"
                    data-target="#deactivateUserModal">
                        Уволить стажера
                    </button>
                    <?php endif; ?>
                
                <?php else: ?>
                <button type="button" class="btn btn-success rounded" id="restoreUserBtn" data-toggle="modal"
                    data-target="#activateUserModal">
                    Восстановить
                </button>
                <?php endif; ?>
                <?php endif; ?>
            </div>


            <!-- <div class="bread d-flex align-items-center ml-3">
                <a href="/timetracking/settings?tab=1">Настройки <i class="fa fa-chevron-right"></i></a>
                <a href="/timetracking/settings?tab=1">Сотрудники</a>
            </div> -->
        </div>
    </div>




    <div class="row">
        <div class="col-md-12">
            <div class="contact-information">

                <?php if(isset($user)): ?>
                    <form action="/timetracking/person/update" method="post" enctype="multipart/form-data"
                        class="form-horizontal" id="form" name="user_form">
                        <input class="form-control" type="hidden" name="id" value="<?php echo e($user->ID); ?>">
                <?php else: ?>
                    <form action="/timetracking/person/store" method="post" enctype="multipart/form-data"
                        class="form-horizontal" id="form" name="user_form">

                <?php endif; ?>
                    <input class="form-control" type="hidden" id="trainee" name="is_trainee" value="false">
                    <input class="form-control" type="hidden" id="increment_provided" name="increment_provided" value="false">
                        <?php echo e(csrf_field()); ?>





                        <div class="data-information mt-4 user-flex">
                                
                            <div id="list-example" class="list-group user-nav sticky">

                            
                                <!-- PROFILE IMAGE -->
                                <div class="">
                                    <input type="file" name="file6" id="file-6"
                                        class="inputfile inputfile-6"
                                        data-multiple-caption="{count} files selected"
                                        style="    display: none;" accept="image/*,.pdf">
                                    <label class="label-6" for="file-6" style="cursor:pointer">
                                        <?php if(isset($user) && !is_null($user->photo) && $user->photo->path !==
                                        ''): ?>
                                        <img
                                            src="/static/profiles/<?php echo e($user->ID); ?>/photo/<?php echo e($user->photo->path); ?>">
                                        <?php else: ?>
                                        <img src="https://cp.callibro.org/files/img/8.png" alt="img">
                                        <?php endif; ?>
                                    </label>

                                    <div class="mt-2 font-weight-bold font-sm " style="width:100%">
                                        
                                        <?php if(isset($user)): ?> <?php echo e($user->LAST_NAME); ?> <?php echo e($user->NAME); ?> <?php else: ?> Новый сотрудник <?php endif; ?>
                                        
                                    </div>
                                    <?php if(isset($user)): ?> 
                                    <div class="mt-0 mb-3 font-sm text-center " style="width:100%">
                                    <?php echo e($user->EMAIL); ?>

                                    </div>
                                    <?php endif; ?>
                                    <div class="mt-0 mb-3 font-sm text-ceer " style="width:100%">
                                       


                                        <?php if(isset($user)): ?>
                                            <?php if($user->position_id): ?>
                                                <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($user->position_id == $position->id): ?>
                                                        <?php echo e($position->position); ?>

                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                Пользователь CP.U_MARKETING.ORG 
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Новый пользователь
                                        <?php endif; ?>
                                                


                                        <?php if(isset($user)): ?>
                                            <?php if($user->bitrix_id != 0): ?>
                                            <p>
                                                <b>Битрикс: <span class="text-green">Есть</span></b>
                                                <a href="https://infinitys.bitrix24.kz/company/personal/user/<?php echo e($user->bitrix_id); ?>/" target="_blank">
                                                    <i class="fa fa-link pointer"></i>
                                                </a>
                                                <i class="fa fa-cog pointer" title="Редактировать" id="bitrix_editor"></i>
                                            </p>
                                            <?php else: ?>
                                            <p>
                                                <b>
                                                    Битрикс:
                                                    <span class="text-red">Нет</span>
                                                </b> 
                                                <i class="fa fa-cog pointer" title="Редактировать" id="bitrix_editor"></i>
                                            </p>
                                            <?php endif; ?>
                                           
                                            <input type="text" 
                                                style="display:none;"
                                                class="form-control form-control-sm"
                                                id="bitrix_id_input"
                                                value="<?php echo e($user->bitrix_id); ?>"
                                                name="bitrix_id"
                                                placeholder="ID профиля в битриксе">
                                          
                                            
                                            <profile-kpi-button :user_id="<?php echo e($user->ID); ?>"/>




                                        <?php endif; ?>


                                    </div>

                                    
                                </div>

                            </div>

                            <!-- ============================================================== -->

                            <div class="xtab-content card scrollspy-example bg-white p-30" id="xmyTabContent" data-spy="scroll" data-target="#list-example">
                                <!-- first tab -->
                                <div class="xtab-pane xfade show active" id="contact" role="tabpanel"
                                    aria-labelledby="contact-tab">
                                    
                                    <h5 class="mb-4">Профиль сотрудника</h5>
                                    <!-- PROFILE INFO -->
                                    <div class="d-flex row">
                                        <div class="contacts-info col-md-6">
                                            
                                            <div class="form-group row">
                                                <label for="firstName"
                                                    class="col-sm-4 col-form-label font-weight-bold">Имя <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="name" id="firstName" required
                                                        placeholder="Имя сотрудника"
                                                        <?php if(isset($user)): ?> value="<?php echo e($user->NAME); ?>"
                                                        <?php else: ?> value="<?php echo e(old('name')); ?>"
                                                        <?php endif; ?>
                                                        >
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="lastName"
                                                    class="col-sm-4 col-form-label font-weight-bold">Фамилия <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="last_name" id="lastName" required
                                                        placeholder="Фамилия сотрудника"
                                                        <?php if(isset($user)): ?> value="<?php echo e($user->LAST_NAME); ?>"
                                                        <?php else: ?> value="<?php echo e(old('last_name')); ?>"
                                                        <?php endif; ?>
                                                        >
                                                </div>
                                            </div>

                                            <?php if(isset($user)): ?>
                                                <div class="form-group row">
                                                    <label for="email" class="col-sm-4 col-form-label font-weight-bold">Email <span class="red">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="email" name="email" id="email" required
                                                            placeholder="Email"
                                                            value="<?php echo e($user->EMAIL); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="email" class="col-sm-4 col-form-label font-weight-bold">Новый пароль </label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="text" name="new_pwd" id="new_pwd"
                                                            placeholder=""
                                                            value="">
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="form-group row">
                                                    <label for="email" class="col-sm-4 col-form-label font-weight-bold">Email <span class="red">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="email" name="email" id="email" required
                                                            placeholder="Email"
                                                            value="<?php echo e(old('email')); ?>">
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            
                                            <div class="form-group row">
                                                <label for="lastName"
                                                    class="col-sm-4 col-form-label font-weight-bold">День рождения <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="date" name="birthday" id="birthday" required
                                                        <?php if(isset($user)): ?><?php if($user->birthday != null): ?>value="<?php echo e(\Carbon\Carbon::parse($user->birthday)->format('Y-m-d')); ?>"<?php endif; ?> <?php else: ?> value="<?php echo e(old('birthday')); ?>"<?php endif; ?>>
                                                </div>
                                            </div>



                                            <div class="form-group row">
                                                <label for="position"
                                                    class="col-sm-4 col-form-label font-weight-bold">Должность </label>
                                                <div class="col-sm-8">
                                                    <select name="position" id="position" class="form-control mb-2" onchange="raf()">
                                                        <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($position->id); ?>" <?php if(isset($user) && $user->
                                                            position_id == $position->id): ?>
                                                            selected="selected" <?php endif; ?>>
                                                            <?php echo e($position->position); ?>

                                                        </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    
                                                    <?php if(isset($user)): ?>
                                                    <profile-groups id="position_group" 
                                                        :groups="<?php echo e($groups); ?>"
                                                        :user_id="<?php echo e($user->ID); ?>"
                                                        :in_groups="<?php echo e(json_encode($user->head_in_groups)); ?>"
                                                        :user_role="2" />
                                                    <?php endif; ?>   
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="userType"
                                                    class="col-sm-4 col-form-label font-weight-bold">Тип <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <select name="user_type" id="userType" class="form-control"
                                                        required>
                                                        <option value="office" <?php if(isset($user) && $user->user_type ==
                                                            'office'): ?> selected <?php endif; ?>>
                                                            Офисный работник
                                                        </option>
                                                        <option value="remote" <?php if(isset($user) && $user->user_type ==
                                                            'remote'): ?> selected <?php endif; ?> >
                                                            Удаленный работник
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="programType"
                                                    class="col-sm-4 col-form-label font-weight-bold">ПО <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <select name="program_type" id="programType" class="form-control"
                                                        required>
                                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($program->id); ?>" <?php if(isset($user) && $user->
                                                            program_id==$program->id): ?> selected="selected" <?php endif; ?>>
                                                            <?php echo e($program->name); ?>

                                                        </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="workingDays"
                                                    class="col-sm-4 col-form-label font-weight-bold">Рабочие дни <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <select name="working_days" id="workingDays" class="form-control"
                                                        required>
                                                        <?php $__currentLoopData = $workingDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($item->id); ?>" <?php if(isset($user) && $user->
                                                            working_day_id == $item->id): ?> selected="selected"
                                                            <?php endif; ?>><?php echo e($item->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="full_time"
                                                    class="col-sm-4 col-form-label font-weight-bold">Ставка</label>
                                                <div class="col-sm-8 d-flex">
                                                    <label class="radio mb-0  mr-4" for="ftr1">
                                                        <input class="form-control" id="ftr1" type="radio" name="full_time" value="1" <?php if(isset($user) && $user->full_time == 1): ?> checked="checked"<?php endif; ?>> <span>Full-Time</span>
                                                    </label>
                                                    <label class="radio mb-0" for="ftr0">
                                                        <input class="form-control" id="ftr0" type="radio" name="full_time" value="0" <?php if(isset($user) && $user->full_time == 0): ?> checked="checked"<?php endif; ?> <?php if(!isset($user)): ?> checked="checked" <?php endif; ?>> <span>Part-Time</span>  
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="workingTimes"
                                                    class="col-sm-4 col-form-label font-weight-bold">Рабочие
                                                    часы</label>
                                                <div class="col-sm-8">
                                                    <select name="working_times" id="workingTimes" class="form-control">
                                                        <?php $__currentLoopData = $workingTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($item->id); ?>" <?php if(isset($user) && $user->
                                                            working_time_id == $item->id): ?> selected="selected"
                                                            <?php endif; ?>><?php echo e($item->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row" id="workShedule">
                                                <label for="workingTimes"
                                                    class="col-sm-4 col-form-label font-weight-bold">Рабочий график</label>
                                                <div class="col-sm-8 form-inline">
                                                    <input class="form-control" type="time" class="form-control mr-2 work-start-time"
                                                        name="work_start_time" id="workStartTime"
                                                        value="<?php if(isset($user)): ?><?php echo e($user->work_start); ?><?php endif; ?>">
                                                    <label for="workEndTime">До </label>
                                                    <input class="form-control" type="time" class="form-control mx-2 work-end-time"
                                                        name="work_start_end" id="workEndTime"
                                                        value="<?php if(isset($user)): ?><?php echo e($user->work_end); ?><?php endif; ?>">
                                                </div>
                                            </div>
                                         
                                            <div class="form-group row" id="weekdays">
                                                <label for="workingTimes"
                                                    class="col-sm-4 col-form-label font-weight-bold">Выходные</label>
                                                <div class="col-sm-8 form-inline">
                                                    <input type="hidden" name="weekdays" value="" id="weekdays-input">

                                                    <div class="weekday <?php if(isset($user) && $user->weekdays[1] == 1 ): ?> active <?php endif; ?>" data-id="1" >Пн</div>   
                                                    <div class="weekday <?php if(isset($user) && $user->weekdays[2] == 1 ): ?> active <?php endif; ?>" data-id="2">Вт</div>   
                                                    <div class="weekday <?php if(isset($user) && $user->weekdays[3] == 1 ): ?> active <?php endif; ?>" data-id="3">Ср</div>   
                                                    <div class="weekday <?php if(isset($user) && $user->weekdays[4] == 1 ): ?> active <?php endif; ?>" data-id="4">Чт</div>   
                                                    <div class="weekday <?php if(isset($user) && $user->weekdays[5] == 1 ): ?> active <?php endif; ?>" data-id="5">Пт</div>   
                                                    <div class="weekday <?php if(isset($user) && $user->weekdays[6] == 1 ): ?> active <?php endif; ?>" data-id="6">Сб</div>   
                                                    <div class="weekday <?php if(isset($user) && $user->weekdays[0] == 1 ): ?> active <?php endif; ?>" data-id="0">Вс</div>   
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="description"
                                                    class="col-sm-4 col-form-label font-weight-bold">Дополнительно</label>
                                                <div class="col-sm-8">
                                                    <textarea rows="3" name="description" class="form-control"
                                                        id="description"><?php if(isset($user)): ?> <?php echo e($user->DESCRIPTION); ?>  <?php endif; ?></textarea>
                                                </div>
                                            </div>

                                        </div>






                                        <div class="col-md-6">
                                            <?php if(isset($user)): ?>
                                            <div class="table-responsive">
                                                <table class="my-table table user-list">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2"><span>Дополнительная информация</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span>Дата регистрации</span>
                                                                </td>
                                                                <td>
                                                                    <span><?php echo e(\Carbon\Carbon::parse($user->DATE_REGISTER)->format('d.m.Y')); ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php if($user->applied_at != null && $user->is_trainee == 0): ?>
                                                            <tr>
                                                                <td>
                                                                    <span>Дата принятия на работу</span>
                                                                </td>
                                                                <td>
                                                                    <span><?php echo e(\Carbon\Carbon::parse($user->applied_at)->format('d.m.Y')); ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                        <?php if($user->applied_at == null && $user->is_trainee == 0): ?>
                                                            <tr>
                                                                <td>
                                                                    <span>Дата принятия на работу</span>
                                                                </td>
                                                                <td>
                                                                    <span><?php echo e(\Carbon\Carbon::parse($user->DATE_REGISTER)->format('d.m.Y')); ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                        <tr>
                                                            <td>
                                                                <span>Успел стать частью команды ~</span>
                                                            </td>
                                                            <td>
                                                                <span><?php echo e($user->worked_with_us); ?></span>
                                                            </td>
                                                        </tr>   

                                                        <?php if($user->delete_time != null): ?>
                                                            <tr>
                                                                <td>
                                                                    <span>Дата отработки</span>
                                                                </td>
                                                                <td>
                                                                    <span><?php echo e(\Carbon\Carbon::parse($user->delete_time)->format('d.m.Y')); ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>


                                                        <?php if($user->deactivate_date != null && $user->deactivate_date != '0000-00-00 00:00:00'): ?>
                                                            <tr>
                                                                <td>
                                                                    <span>Дата увольнения</span>
                                                                </td>
                                                                <td>
                                                                    <span><?php echo e(\Carbon\Carbon::parse($user->deactivate_date)->format('d.m.Y')); ?></span>
                                                                </td>
                                                            </tr>
                                                                 
                                                            <?php if(isset($user->downloads) && $user->downloads->resignation): ?>
                                                            <tr>
                                                                <td>
                                                                    <span>Заявление об увольнении</span>
                                                                </td>
                                                                <td>
                                                                    <a  download=""
                                                                        class="d-block"
                                                                        href="/static/profiles/<?php echo e($user->ID); ?>/resignation/<?php echo e($user->downloads->resignation); ?>">Скачать</a>
                                                                </td>
                                                            </tr>
                                                            <?php endif; ?>

                                                            <?php if($user->fire_cause != null): ?>
                                                            <tr>
                                                                <td>
                                                                    <span>Причина увольнения</span>
                                                                </td>
                                                                <td>
                                                                    <span><?php echo e($user->fire_cause); ?></span>
                                                                </td>
                                                            </tr>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                  
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                            <p class="adaptation-title mt-3 mb-2">Адаптационные беседы</p>
                                            <?php $__currentLoopData = $user->adaptation_talks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $talk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="adaptation_talk">
                                                    <div class="div_1"><?php echo e($talk['day']); ?>й день
                                                        <input type="hidden" name="adaptation_talks[<?php echo e($key); ?>][day]" value="<?php echo e($talk['day']); ?>">
                                                    </div>
                                                    <div class="div_2">
                                                        <input type="text" name="adaptation_talks[<?php echo e($key); ?>][inter_id]" placeholder="Кто провел" value="<?php echo e($talk['inter_id']); ?>"> 
                                                        

                                                        <input class="form-control" type="date" name="adaptation_talks[<?php echo e($key); ?>][date]"
                                                        <?php if($talk['date'] != null): ?> value="<?php echo e(\Carbon\Carbon::parse($talk['date'])->format('Y-m-d')); ?>" 
                                                        <?php else: ?>  value="null" <?php endif; ?>>
                                                    </div>
                                                    <div class="div_3">
                                                        <textarea name="adaptation_talks[<?php echo e($key); ?>][text]" placeholder="Комментарии"><?php echo e($talk['text']); ?></textarea>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            <?php endif; ?>


                                            <!-- dannye -->


                                            <!-- groups tab -->
                                            <div class="mb-3 xfade" id="iphones3">
                                                <!--  -->

                                                <h5 class="mb-4">Группы</h5>

                                              
                                                <?php if(isset($user)): ?>
                                                <profile-groups :groups="<?php echo e($groups); ?>" :user_id="<?php echo e($user->ID); ?>" :in_groups="<?php echo e(json_encode($user->in_groups)); ?>" />
                                                <?php else: ?>
                                                <select name="group" id="group" class="form-control">
                                                    <option>Выберите группу</option>
                                                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($group->id); ?>"><?php echo e($group->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <?php endif; ?>

                                                  
                                                
                                               
                                                
                                                <!--  -->
                                            </div>

                                            <div class="mb-4">
                                                <?php if(isset($user)): ?>
                                                <h5 class="mb-4 mt-4">Книги</h5>

                                                <profile-books :books="<?php echo e($corpbooks); ?>" :user_id="<?php echo e($user->ID); ?>" 
                                                    <?php if(isset($user->in_books)): ?> 
                                                        :in_books="<?php echo e($user->in_books); ?>"
                                                    <?php else: ?>
                                                        :in_books="[]"
                                                    <?php endif; ?>
                                                    />
                                                        
                                                <?php endif; ?>
                                            </div>

                                            <!-- end of groups and books tab -->

                                            <!-- documents tab -->
                                            <div class="xtab-pane xfade" id="iphones4">
                                                <!--  -->
                                                <h5 class="mb-4 mt-1">Документы (RAR, ZIP)</h5>

                                                <div class="box mb-4 data-information file-uploads">



                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file5" id="file-5"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        <?php if(isset($user->downloads) && $user->downloads->ud_lich): ?>
                                                        <label for="file-5">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#<?php echo e(pathinfo($user->downloads->ud_lich, PATHINFO_EXTENSION)); ?>-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Удостоверение личности</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/<?php echo e($user->ID); ?>/ud_lich/<?php echo e($user->downloads->ud_lich); ?>">Скачать</a>
                                                        <?php else: ?>
                                                        <label for="file-5">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Удостоверение личности</span>
                                                        </label>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file1" id="file-1"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        <?php if(isset($user->downloads) && $user->downloads->dog_okaz_usl): ?>
                                                        <label for="file-1">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#<?php echo e(pathinfo($user->downloads->dog_okaz_usl, PATHINFO_EXTENSION)); ?>-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Договор оказания услуг</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/<?php echo e($user->ID); ?>/dog_okaz_usl/<?php echo e($user->downloads->dog_okaz_usl); ?>">Скачать</a>
                                                        <?php else: ?>
                                                        <label for="file-1">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Договор оказания услуг</span>
                                                        </label>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-inline-block text-center align-text-bottom">
                                                        <input type="file" name="file2" id="file-2"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        <?php if(isset($user->downloads) && $user->downloads->sohr_kom_tainy): ?>
                                                        <label for="file-2">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#<?php echo e(pathinfo($user->downloads->sohr_kom_tainy, PATHINFO_EXTENSION)); ?>-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Сохранение комм. тайны</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/<?php echo e($user->ID); ?>/sohr_kom_tainy/<?php echo e($user->downloads->sohr_kom_tainy); ?>">Скачать</a>
                                                        <?php else: ?>
                                                        <label for="file-2">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Сохранение комм. тайны</span>
                                                        </label>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="box mb-4 data-information file-uploads">

                                                    <div class="d-inline-block text-center align-text-bottom">
                                                        <input type="file" name="file3" id="file-3"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        <?php if(isset($user->downloads) && $user->downloads->dog_o_nekonk): ?>
                                                        <label for="file-3">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#<?php echo e(pathinfo($user->downloads->dog_o_nekonk, PATHINFO_EXTENSION)); ?>-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Договор о неконкуренции</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/<?php echo e($user->ID); ?>/dog_o_nekonk/<?php echo e($user->downloads->dog_o_nekonk); ?>">Скачать</a>
                                                        <?php else: ?>
                                                        <label for="file-3">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Договор о неконкуренции</span>
                                                        </label>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file4" id="file-4"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        <?php if(isset($user->downloads) && $user->downloads->trud_dog): ?>
                                                        <label for="file-4">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#<?php echo e(pathinfo($user->downloads->trud_dog, PATHINFO_EXTENSION)); ?>-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Трудовой <br> договор</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/<?php echo e($user->ID); ?>/trud_dog/<?php echo e($user->downloads->trud_dog); ?>">Скачать</a>
                                                        <?php else: ?>
                                                        <label for="file-4">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Трудовой <br> договор</span>
                                                        </label>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file7" id="file-7"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        <?php if(isset($user->downloads) && $user->downloads->archive): ?>
                                                        <label for="file-7">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#<?php echo e(pathinfo($user->downloads->archive, PATHINFO_EXTENSION)); ?>-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Архив <br>документов</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/<?php echo e($user->ID); ?>/archive/<?php echo e($user->downloads->archive); ?>">Скачать</a>
                                                        <?php else: ?>
                                                        <label for="file-7">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Архив <br> документов</span>
                                                        </label>
                                                        <?php endif; ?>
                                                    </div>

                                                    
                                                

                                                    <!-- может лишний -->
                                                    <input type="file" name="logo" id="file-6" class="inputfile inputfile-1"
                                                        data-multiple-caption="{count} files selected" style="    display: none;"
                                                        accept="image/*,.pdf">
                                                    <!-- -------- -->
                                                </div>

                                                <!--  -->
                                            </div>
                                            <!-- end of documents -->
                                          
                                        </div>

                                        



                                    </div>










                                </div>

                                <!-- second tab -->
                                <div class="xtab-pane xfade" id="phones" role="tabpanel" aria-labelledby="phones-tab">
                                    <!--  -->
                                    <!-- PROFILE PHONES -->
                                    <div class="profile-contacts mb-3 row">
                                        <div class="phones col-md-6">
                                            <h5 class="mb-4">Номера телефонов</h5>
                                            <div class="d-flex phone-row form-group mb-2">
                                                <label for="phone" class="col-sm-4 col-form-label font-weight-bold">Мобильный <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" class="form-control mr-1 col-sm-8" type="text"
                                                    value="<?php if(isset($user)): ?><?php echo e($user->PHONE); ?><?php else: ?><?php echo e(old('phone')); ?><?php endif; ?>"
                                                    name="phone" id="phone" placeholder="Телефон">
                                                </div>
                                            </div>
                                            <div class="d-flex phone-row form-group mb-2">
                                                <label for="phone_1" class="col-sm-4 col-form-label font-weight-bold">Домашний <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" class="form-control mr-1 col-sm-8" type="text"
                                                    value="<?php if(isset($user)): ?><?php echo e($user->phone_1); ?><?php else: ?><?php echo e(old('phone_1')); ?><?php endif; ?>"
                                                    name="phone_1" id="phone_1" placeholder="Телефон">
                                                </div>
                                            </div>
                                            <div class="d-flex phone-row form-group mb-2">
                                                <label for="phone_2" class="col-sm-4 col-form-label font-weight-bold">Супруга/Муж <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" class="form-control mr-1 col-sm-8" type="text"
                                                    value="<?php if(isset($user)): ?><?php echo e($user->phone_2); ?><?php else: ?><?php echo e(old('phone_2')); ?><?php endif; ?>"
                                                    name="phone_2" id="phone_2" placeholder="Телефон">
                                                </div>
                                            </div>
                                            <div class="d-flex phone-row form-group mb-2">
                                                <label for="phone_3" class="col-sm-4 col-form-label font-weight-bold">Друг/Брат/Сестра <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" class="form-control mr-1 col-sm-8" type="text"
                                                    value="<?php if(isset($user)): ?><?php echo e($user->phone_3); ?><?php else: ?><?php echo e(old('phone_3')); ?><?php endif; ?>"
                                                    name="phone_3" id="phone_3" placeholder="Телефон">
                                                </div>
                                            </div>
                                            <div class="d-flex phone-row form-group mb-2">
                                                <label for="phone_4" class="col-sm-4 col-form-label font-weight-bold">Сын/Дочь</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" class="form-control mr-1 col-sm-8" type="text"
                                                    value="<?php if(isset($user)): ?><?php echo e($user->phone_4); ?><?php else: ?><?php echo e(old('phone_4')); ?><?php endif; ?>"
                                                    name="phone_4" id="phone_4" placeholder="Телефон">
                                                </div>
                                            </div>

                                            <?php if(isset($user)): ?>
                                            <?php $__currentLoopData = $user->profileContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($contact->type == 'phone'): ?>
                                            <div class="d-flex phone-row form-group m0">
                                                <input class="form-control" class="form-control mr-1" type="text" value="<?php echo e($contact->name); ?>"
                                                    name="contacts[phone][<?php echo e($key); ?>][name]" placeholder="Название">
                                                <input class="form-control" class="form-control mr-1" type="text" value="<?php echo e($contact->value); ?>"
                                                    name="contacts[phone][<?php echo e($key); ?>][value]" placeholder="Телефон">
                                                <button class="btn btn-danger btn-sm contact-delete rounded"
                                                    type="button" onclick="deletePhone(event)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                            <button class="btn btn-phone btn-rounded mb-2 mt-2" type="button"
                                                onclick="addPhone()">
                                                <i class="fa fa-plus"></i> Добавить телефон
                                            </button>
                                        </div>
                                        <!-- end of phones -->
                                        
                                         <!-- zarplata tab -->
                                        <div class="col-md-12 mt-3">

                                            <h5 class="mb-4">Зарплата</h5>

                                            <div class="form-group row">
                                                <label for="zarplata" class="col-sm-3 col-form-label font-weight-bold">Оклад <span class="red">*</span></label>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="zarplata" id="zarplata" required
                                                    placeholder="Оклад" 
                                                    <?php if(isset($user->zarplata)): ?>
                                                        <?php if($user->zarplata->zarplata == 0): ?> value="0"
                                                        <?php else: ?>  value="<?php echo e($user->zarplata->zarplata); ?>"
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php if(old('zarplata') == ''): ?>  value="0"
                                                        <?php else: ?>  value="<?php echo e(old('zarplata')); ?>"
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    >
                                                </div>


                                                <div class="col-sm-3">
                                                    <select name="currency" id="currency" class="form-control form-control-sm">
                                                        <option selected disabled> Валюта</option>
                                                        <option value="kzt" <?php if(isset($user) && $user->currency == 'kzt'): ?> <?php endif; ?>>KZT Казахстанский тенге</option>
                                                        <option value="rub" <?php if(isset($user) && $user->currency == 'rub'): ?> selected <?php endif; ?>>RUB Российский рубль</option>
                                                        <option value="kgs" <?php if(isset($user) && $user->currency == 'kgs'): ?> selected <?php endif; ?>>KGS Киргизский сом</option>
                                                        <option value="uzs" <?php if(isset($user) && $user->currency == 'uzs'): ?> selected <?php endif; ?>>UZS Узбекский сум</option>
                                                        <option value="uah" <?php if(isset($user) && $user->currency == 'uah'): ?> selected <?php endif; ?>>UAH Украинская гривна</option>
                                                        <option value="byn" <?php if(isset($user) && $user->currency == 'byn'): ?> selected <?php endif; ?>>BYN Белорусский рубль</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 pl-0" >







                                                    <profile-quarter-button :user_id="<?php echo e($user->ID); ?>"/>


                                                </div>


                                            </div>
                                            <div class="form-group row">
                                                <label for="kaspi" class="col-sm-3 col-form-label font-weight-bold">KASPI BANK</label>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="kaspi_cardholder" id="kaspi_cardholder"
                                                        placeholder="Имя на карте"
                                                        value="<?php if(isset($user->zarplata)): ?><?php echo e($user->zarplata->kaspi_cardholder); ?><?php else: ?><?php echo e(old('kaspi_cardholder')); ?><?php endif; ?>">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="kaspi" id="kaspi"
                                                        placeholder="+7 (777) 777-77-77"
                                                        value="<?php if(isset($user->zarplata)): ?><?php echo e($user->zarplata->kaspi); ?><?php else: ?><?php echo e(old('kaspi')); ?><?php endif; ?>">
                                                </div>
                                                <div class="col-sm-3" style="padding-left:0">
                                                    <input class="form-control card-number" type="text" name="card_kaspi" id="card_kaspi"
                                                        placeholder="XXXX XXXX XXXX XXXX" style="font-size: 14px;"
                                                        value="<?php if(isset($user->zarplata)): ?><?php echo e($user->zarplata->card_kaspi); ?><?php else: ?><?php echo e(old('card_kaspi')); ?><?php endif; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="jysan"
                                                    class="col-sm-3 col-form-label font-weight-bold">JYSAN BANK</label>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="jysan_cardholder" id="jysan_cardholder"
                                                        placeholder="Имя на карте"
                                                        value="<?php if(isset($user->zarplata)): ?><?php echo e($user->zarplata->jysan_cardholder); ?><?php else: ?><?php echo e(old('jysan_cardholder')); ?><?php endif; ?>">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="jysan" id="jysan"
                                                        placeholder="+7 (777) 777-77-77"
                                                        value="<?php if(isset($user->zarplata)): ?><?php echo e($user->zarplata->jysan); ?><?php else: ?><?php echo e(old('jysan')); ?><?php endif; ?>">
                                                </div>
                                                <div class="col-sm-3" style="padding-left:0">
                                                    <input class="form-control card-number" type="text" name="card_jysan" id="card_jysan"
                                                        placeholder="XXXX XXXX XXXX XXXX" style="font-size: 14px;"
                                                        value="<?php if(isset($user->zarplata)): ?><?php echo e($user->zarplata->card_jysan); ?><?php else: ?><?php echo e(old('card_jysan')); ?><?php endif; ?>">
                                                </div>
                                            </div>
                
                                            <div class="cards">
                                                <?php if(isset($user)): ?>
                                                <?php $__currentLoopData = $user->cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="d-flex form-group m0 card-row">
                                                        <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1<?php echo e($card->id); ?>][bank]" placeholder="Банк" value="<?php echo e($card->bank); ?>">
                                                        <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1<?php echo e($card->id); ?>][country]" placeholder="Страна" value="<?php echo e($card->country); ?>">
                                                        <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1<?php echo e($card->id); ?>][cardholder]" placeholder="Имя на карте" value="<?php echo e($card->cardholder); ?>">
                                                        <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1<?php echo e($card->id); ?>][phone]" placeholder="Телефон" value="<?php echo e($card->phone); ?>">
                                                        <input  class="form-control mr-1 col-sm-3 card-number" type="text" name="cards[1<?php echo e($card->id); ?>][number]" placeholder="Номер карты" value="<?php echo e($card->number); ?>">
                                                        <button class="btn btn-danger btn-sm card-delete rounded" type="button" onclick="deleteCard(event)"><i class="fa fa-trash"></i></button>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </div>

                                            <button class="btn btn-phone btn-rounded mb-2 mt-2" type="button"
                                                    onclick="addCard()">
                                                    <i class="fa fa-plus"></i> Добавить карту
                                            </button>
                                            <!-- END OF OKLAD -->
                                        </div>


                                        <?php if(isset($user)): ?>
                                         <!-- additional tab -->
                                        <div class="col-md-12 mt-3">
                                            <h5 class="mb-4">Прочие данные</h5>
                                        </div>
                                        <div class="col-md-8 ">

                                            <div class="form-group row">
                                                <label for="description"
                                                    class="col-sm-4 col-form-label font-weight-bold">Примечание для внешнего рекрутера</label>
                                                <div class="col-sm-8">
                                                    <textarea name="recruiter_comment" class="form-control"
                                                        id="recruiter_comment"><?php echo e($user->recruiter_comment); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <?php if($user->lead): ?>
                                            <div class="table-responsive">
                                                <table class="my-table table user-list">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2"><span>Битрикс</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <span>Сегмент</span>
                                                            </td>
                                                            <td>
                                                                <span><?php echo e($user->segment); ?></span>
                                                            </td>
                                                        </tr>

                                                        <?php if($user->lead->lead_id != 0): ?>
                                                        <tr>
                                                            <td>
                                                                <span>Лид</span>
                                                            </td>
                                                            <td>
                                                                <span>
                                                                    <a href="https://infinitys.bitrix24.kz/crm/lead/details/<?php echo e($user->lead->lead_id); ?>/"
                                                                        target="_blank">
                                                                        <?php echo e($user->lead->lead_id); ?>

                                                                    </a>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>

                                                        <?php if($user->lead->deal_id != 0): ?>
                                                        <tr>
                                                            <td>
                                                                <span>Сделка</span>
                                                            </td>
                                                            <td>
                                                                <span>
                                                                    <a href="https://infinitys.bitrix24.kz/crm/lead/details/<?php echo e($user->lead->deal_id); ?>/"
                                                                        target="_blank">
                                                                        <?php echo e($user->lead->deal_id); ?>

                                                                    </a>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php endif; ?>
                                        </div>


                                        <div class="col-md-8">

                                            <div class="form-group row">
                                                <label for="headphones_amount_checkbox"
                                                    class="col-sm-4 col-form-label font-weight-bold">Выданы наушники <br> <?php echo e($user->headphones_date); ?></label>
                                                    
                                                <div class="col-sm-2">
                                                   <input type="checkbox" class="form-control" id="headphones_amount_checkbox" <?php if($user->headphones_amount > 0): ?> checked <?php endif; ?>>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for="headphones_amount"
                                                        class="col-form-label font-weight-bold">На сумму</label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input name="headphones_amount" class="form-control" type="number"
                                                        id="headphones_amount" value="<?php echo e($user->headphones_amount); ?>" <?php if($user->headphones_amount == 0): ?> disabled <?php endif; ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                    </div>
                                    <!--  -->
                                </div>

                                

                                
                            </div>
                        </div>
                        <?php if($errors->any()): ?>
                        <u-modal items="<?php echo e(json_encode($errors->all())); ?>" title="Не сохранено" />
                        <?php endif; ?>
                    </form>
            </div>
        </div>
    </div>
</div>












<?php if(isset($user)): ?>
<?php if(is_null($user->deleted_at)): ?>
<div class="modal fade" id="deactivateUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Вы уверены что хотите уволить пользователя?</h6>
            </div>
            <div class="text-center mb-3">
                <form action="/timetracking/delete-person" id="deleteForm" enctype="multipart/form-data"  method="post">
                    <input class="form-control" type="hidden" name="id" value="<?php echo e($user->ID); ?>">
                    <input class="form-control" type="hidden" name="delay" value="1" id="delay">

                    <?php echo e(csrf_field()); ?>

                    <div class="row align-items-center justify-content-center p-3" style="padding-bottom:0!important">
                        
                        <div class="col-md-12 mb-2">
                            <select name="cause" id="cause" class="form-control form-control-sm">
                                <option value="" selected disabled>Выберите причину</option>
                                <?php $__currentLoopData = $fire_causes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cause): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cause); ?>"><?php echo e($cause); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <?php if(isset($user) && !$user->is_trainee): ?>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="d-inline-block text-center" style="width:100%">
                                <input type="file" name="file8" id="file-8"
                                        class="inputfile inputfile-1" style="display:none">
                                    <?php if(isset($user->downloads) && $user->downloads->resignation): ?>
                                    <label for="file-8">
                                        <svg width="20" height="30" class="">
                                            <use
                                                xlink:href="#<?php echo e(pathinfo($user->downloads->resignation, PATHINFO_EXTENSION)); ?>-icon">
                                            </use>
                                        </svg>
                                        <span>Заявление об <br>увольнении</span>
                                    </label>
                                    <?php else: ?>
                                    <label for="file-8">
                                        <svg width="20" height="30" class="">
                                            <use xlink:href="#download-icon"></use>
                                        </svg>
                                        <span>Заявление об <br>увольнении</span>
                                    </label>
                                    <?php endif; ?>
                                </div>
                            </div>  
                        </div>
                        <div class="col-md-6 d-flex justify-content-between flex-column">
                            <button type="submit" class="btn btn-success rounded mb-2" id="deleteUser">Да</button>
                            <button type="reset" class="btn btn-primary rounded " data-dismiss="modal">Нет</button>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12 mb-2">
                            <input class="form-control" type="text" name="cause2" placeholder="Или напишите свой вариант" value="" id="cause2" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 d-flex justify-content-between flex-column">
                            <button type="submit" class="btn btn-success rounded">Да</button>
                        </div>
                        <div class="col-md-6 d-flex justify-content-between flex-column">
                            <button type="reset" class="btn btn-primary rounded" data-dismiss="modal">Нет</button>
                        </div>
                        <?php endif; ?>
                        
                        
                                        

                        
                    </div>
                    <div class="row mt-2 px-5">
                        <span id="deleteError" style="color:red;text-align:center;width:100%"></span>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="modal fade" id="activateUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Вы уверены что хотите востановить пользователя?</h6>
            </div>
            <div class="text-center mb-3 ">
                <form action="/timetracking/recover-person">
                    <input class="form-control" type="hidden" name="id" value="<?php echo e($user->ID); ?>">
                    <button type="submit" class="btn btn-success" id="deleteUserButton">Да</button>
                    <button type="reset" class="btn btn-primary" data-dismiss="modal">Нет</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>



<div class="modal " id="beforeSubmit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-left">
                <h5>Заполните все поля</h5>
            </div>
            <div class="text-left mb-3 texter px-3"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="bitrix_quarter_id_input" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Вы уверены что хотите востановить пользователя?</h6>
            </div>
            <div class="text-center mb-3 ">
                <form action="/timetracking/recover-person">

                    <button type="submit" class="btn btn-success" id="deleteUserButton">Да</button>
                    <button type="reset" class="btn btn-primary" data-dismiss="modal">Нет</button>
                </form>
            </div>
        </div>
    </div>
</div>




<?php echo $__env->make('admin.svg.icons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<script>

    function bitrix_quarter_editor(){
        console.log('1995');
        $('#bitrix_quarter_id_input').show();
    }


var counter = 0

$('#submitx').click(function(e) {
    e.preventDefault();
    $('#trainee').val(false)
    submitx();
});

$('#submitx2').click(function(e) {
    e.preventDefault();
    $('#trainee').val(false)
    $('#increment_provided').val(true)
    submitx();
});

$('#submit_job').click(function(e) {
    e.preventDefault();
    $('#trainee').val(false)
    $('#increment_provided').val(true)
    submitx();
});

$('#submit_trainee').click(function(e) {
    e.preventDefault();
    $('#trainee').val(true)
    submit_trainee();
});
$('#deleteModalBtn').click(function(e) {
    $('#delay').val(0)
});
$('#deleteModalBtn2').click(function(e) {
    $('#delay').val(1)
});
$('#deleteUser').click(function(e) {
    e.preventDefault();
    // if($('#delay').val() == 0) {
    //     $('#deleteError').text('');
    //     $('#deleteForm').submit();
    
    if(($('#file-8').val() =='' || $('#file-8').val() == null) && $('#cause').val() != 'Дубликат, 2 учетки') {
        $('#deleteError').text('Прикрепите Заявление об увольнении!');
    } else {
        $('#deleteError').text('');
        $('#deleteForm').submit();
    }
    
    
    
});


function submitx() {
    
    counter = 0;

    let phone = $('#phone').val(),
        phone_1 = $('#phone_1').val(),
        phone_2 = $('#phone_2').val(),
        phone_3 = $('#phone_3').val(),
        name = $('#firstName').val(),
        last_name = $('#lastName').val(),
        birthday = $('#birthday').val(),
        email = $('#email').val(),
        zarplata = $('#zarplata').val();


    $('#beforeSubmit .texter').html('');

    $('#zarplata').val(zarplata.replace(/\D/g, ""));



    let profile_errors = 0;

    if (name.length < 2) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>Имя</b></div>');
        counter++;
        profile_errors++
    }

    if (last_name.length < 3) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>Фамилия</b></div>');
        counter++;
        profile_errors++
    }

    if (!validateEmail(email)) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>Email </b> не корректный</div>');
        counter++;
        profile_errors++
    }

    if (birthday.length == 0) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>День рождения</b></div>');
        counter++;
        profile_errors++
    }


    let phone_errors = 0;

    if (phone.length < 11) {
        $('#beforeSubmit .texter').append('<div>Контакты: <b>Мобильный</b></div>');
        counter++;
        phone_errors++
    }

    if (phone_1.length < 6) {
        $('#beforeSubmit .texter').append('<div>Контакты: <b>Домашний</b></div>');
        counter++;
        phone_errors++
    }

    if (phone_2.length < 11) {
        $('#beforeSubmit .texter').append('<div>Контакты: <b>Супруга/Муж</b></div>');
        counter++;
        phone_errors++
    }

    if (phone_3.length < 11) {
        $('#beforeSubmit .texter').append('<div>Контакты: <b>Друг/Брат/Сестра</b></div>');
        counter++;
        phone_errors++
    }

    let zarplata_errors = 0;

    // if (validateCnum()) {
    //     $('#beforeSubmit .texter').append('<div>Зарплата: <b>Номер карты</b></div>');
    //     counter++;
    //     zarplata_errors++;
    // }

    ///////////////////////////////////////
    if(profile_errors != 0){
        $('.listo1').text(profile_errors);
    } else {
        $('.listo1').text('');
    }

    if(phone_errors != 0){
        $('.listo2').text(phone_errors);
    } else {
        $('.listo2').text('');
    }

    if(zarplata_errors != 0){
        $('.listo3').text(zarplata_errors);
    } else {
        $('.listo3').text('');
    }
    //////////////////////////////////////////////
   
    
    if (counter > 0) {
        $('#beforeSubmit').modal('show');
    } else {
        $('#form').submit();
    }

}

//////////////

function submit_trainee() {
    
    counter = 0;

    let phone = $('#phone').val(),
        name = $('#firstName').val(),
        last_name = $('#lastName').val(),
        birthday = $('#birthday').val(),
        email = $('#email').val(),
        zarplata = $('#zarplata').val();

    $('#beforeSubmit .texter').html('');

    $('#zarplata').val(zarplata.replace(/\D/g, ""));
    //$('#zarplata').val(0);


    let profile_errors = 0;

    if (name.length < 2) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>Имя</b></div>');
        counter++;
        profile_errors++
    }

    if (last_name.length < 3) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>Фамилия</b></div>');
        counter++;
        profile_errors++
    }

    if (!validateEmail(email)) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>Email </b> не корректный</div>');
        counter++;
        profile_errors++
    }

    if (birthday.length == 0) {
        $('#beforeSubmit .texter').append('<div>Профиль: <b>День рождения</b></div>');
        counter++;
        profile_errors++
    }


    ///////////////////////////////////////
    if(profile_errors != 0){
        $('.listo1').text(profile_errors);
    } else {
        $('.listo1').text('');
    }

    $('.listo2').text('');
    $('.listo3').text('');
    //////////////////////////////////////////////
   
    if (counter > 0) {
        $('#beforeSubmit').modal('show');
    } else {
        $('#form').submit();
    }

}

/////////////////
var phoneIndex = 1;

function validateEmail(email) {
    const re =
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function addPhone(e) {
    if ($('.phones .phone-row').length < 10) {
        var index = phoneIndex++;
        $('.phones').append(`<div class="d-flex phone-row form-group m0">
            <input class="form-control mr-1 col-sm-4" type="text" value="Свой" name="contacts[phone][${index}][name]" placeholder="Название">
            <input class="form-control mr-1 col-sm-8" type="text" name="contacts[phone][${index}][value]" placeholder="Телефон">
            <button class="btn btn-danger btn-sm contact-delete rounded" type="button" onclick="deletePhone(event)"><i class="fa fa-trash"></i></button>
        </div>`);
    }
}

function deletePhone(e) {
    if (confirm('Удалить номер?')) {
        $(e.target).parents('.phone-row').remove();
    }
}


var cardIndex = 1;
function addCard(e) {
    var index = cardIndex++;
    $('.cards').append(`<div class="d-flex form-group m0 card-row">
        <input class="form-control mr-1 col-sm-2" type="text" name="cards[${index}][bank]" placeholder="Банк">
        <input class="form-control mr-1 col-sm-2" type="text" name="cards[${index}][country]" placeholder="Страна">
        <input class="form-control mr-1 col-sm-2" type="text" name="cards[${index}][cardholder]" placeholder="Имя на карте">
        <input class="form-control mr-1 col-sm-2" type="text" name="cards[${index}][phone]" placeholder="Телефон">
        <input class="form-control mr-1 col-sm-3 card-number" type="text" name="cards[${index}][number]" placeholder="Номер карты">
        <button class="btn btn-danger btn-sm card-delete rounded" type="button" onclick="deleteCard(event)"><i class="fa fa-trash"></i></button>
    </div>`);

    $(".card-number").inputmask({"mask": "9999 9999 9999 9999"});
}

function deleteCard(e) {
    if (confirm('Удалить карту?')) {
        $(e.target).parents('.card-row').remove();
    }
}


</script>
<script>
$('#headphones_amount_checkbox').click(function() {
    var el = $('#headphones_amount');
    if (el.attr('disabled')) {
        el.attr('value', 0);
        el.val(0);
        el.removeAttr('disabled');
    } else {
        el.attr('disabled', 'disabled');
    }
});
</script>
<script>
$(document).ready(function(){
    raf();
});
var position = document.querySelector('#position');
var position_group = document.querySelector('#position_group');

function raf() {
    if(document.querySelector('#position').value == '45') {
        document.querySelector('#position_group').style.display = 'block';
    } else {
        document.querySelector('#position_group').style.display = 'none';
    }
}
</script>

<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
<script>
$(".card-number").inputmask({"mask": "9999 9999 9999 9999"});
</script>



<script>
$('#bitrix_editor').click(function() {
    $('#bitrix_id_input').slideToggle(100);
});


</script>

<script>
String.prototype.replaceAt = function(index, replacement) {
    return this.substring(0, index) + replacement + this.substring(index + replacement.length);
}

<?php if(isset($user)): ?>
$('#weekdays-input').val('<?php echo e($user->weekdays); ?>');
<?php else: ?>
$('#weekdays-input').val('0000000');
<?php endif; ?>
$('#weekdays .weekday').click(function() {
    let val = $('#weekdays input').val();
    let el = $(this);
    let id = el.data('id');
    if(el.hasClass('active')) {
        $(this).removeClass('active');

        val = val.replaceAt(id, "0");
    } else {
        $(this).addClass('active');
        val = val.replaceAt(id, "1");
    }

    $('#weekdays-input').val(val);

});


</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<?php echo $__env->make('admin.users.common', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<style>
.modal-backdrop {
    opacity: 0.4;
}
</style>
<style>
.weekday {
    text-align: center;
    display: flex;
    align-items:center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 3px;
    border: 1px solid #efefef;
    margin-right: 3px;
    cursor: pointer;
    background: #fff;
    color: #000;
    padding: 15px;
}
.weekday.active {
    background: #28a745;
}
.weekday:hover {
    background: green;
}
</style>
<style>
.adaptation_talk {
    display: flex;
}
.adaptation_talk .div_1 {
    margin-right: 7px;
    font-size: 12px;
    width: 69px;
    font-weight: 600;
}
.adaptation-title {
    font-size: 14px;
    color: #045e91;
    font-weight: 700;
}
.adaptation_talk .div_2 input{
    width: 120px;
    padding: 2px 5px !important;
    height: 30px;
    font-size: 12px;

}  
.adaptation_talk .div_2 {
    display: flex;
    flex-direction:column;
    margin-bottom: 10px;
}
.adaptation_talk .div_3 {
    flex: 4;
    margin-left: 7px;
}
.adaptation_talk .div_3 textarea{
    width: 100%;

   
    border-radius:3px;
    font-size: 12px;
}
input[type="checkbox"],
input[type="radio"] {
    width: auto !important;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>