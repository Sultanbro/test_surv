@extends('layouts.admin')
@section('title', 'Сотрудник')
@section('content')


<div class="user-page">
    <div class="mt-3">
        <div class="col-md-12 d-flex justify-content-between align-items-start">
            <a href="/timetracking/settings?tab=1" class="btn btn-rounded"
                style="background: #a0a6ab;color: white;font-size: 14px;">
                <i class="fa fa-chevron-left"></i> Назад
            </a>

            <div class="data-information d-flex">
                @if(isset($user))
                    @if($user->user_description && $user->user_description->is_trainee == 1)
                    <button class="btn btn-warning mr-2 rounded" id="submit_job">Принять на работу</button>
                    <button class="btn btn-primary mr-2 rounded" id="submit_trainee">Сохранить</button>
                    @else
                    <button class="btn btn-primary mr-2 rounded" id="submitx">Сохранить</button>
                    @endif
                @else
                    <button class="btn btn-primary mr-2 rounded" id="submitx2">Пригласить без стажировки</button>
                    <button class="btn btn-warning mr-2 rounded" id="submit_trainee">Пригласить со стажировкой</button>
                @endif
                @if(isset($user))
                @if( is_null($user->deleted_at) )

                    @if($user->user_description && $user->user_description->is_trainee == 1)
                    <button type="button" class="btn btn-danger rounded" id="deleteModalBtn" v-b-modal.modal-deactivate>
                        Уволить стажера
                    </button>
                    @else
                    <button type="button" class="btn btn-danger rounded mr-2" id="deleteModalBtn" v-b-modal.modal-deactivate>
                        Уволить без отработки
                    </button>
                    <button type="button" class="btn btn-danger rounded" id="deleteModalBtn2" v-b-modal.modal-deactivate>
                        Уволить с отработкой
                    </button>
                    @endif

                  
                @else

                <button type="button" class="btn btn-success rounded" v-b-modal.modal-activate>
                Восстановить
                </button>

                @endif
                @endif
            </div>

            <!-- <div class="bread d-flex align-items-center ml-3">
                <a href="/timetracking/settings?tab=1">Настройки <i class="fa fa-chevron-right"></i></a>
                <a href="/timetracking/settings?tab=1">Сотрудники</a>
            </div> -->
        </div>
    </div>




    <div class="">
        <div class="col-md-12 p-0">
            <div class="contact-information">

                @if(isset($user))
                    <form action="/timetracking/person/update" method="post" enctype="multipart/form-data"
                        class="form-horizontal" id="form" name="user_form">
                        <input class="form-control" type="hidden" name="id" value="{{$user->id}}">
                @else
                    <form action="/timetracking/person/store" method="post" enctype="multipart/form-data"
                        class="form-horizontal" id="form" name="user_form">

                @endif
                    <input class="form-control" type="hidden" id="trainee" name="is_trainee" value="false">
                    <input class="form-control" type="hidden" id="increment_provided" name="increment_provided" value="false">
                        {{ csrf_field() }}




                        <div class="data-information mt-4 user-flex">
                                
                            <div id="list-example" class="list-group user-nav sticky">






                                <!-- PROFILE IMAGE -->
                                <div class="">
{{--                                    <input type="file" name="file6" id="file-6"--}}
{{--                                        class="inputfile inputfile-6"--}}
{{--                                        data-multiple-caption="{count} files selected"--}}
{{--                                        style="    display: none;" accept="image/*,.pdf">--}}



                                        <input hidden type="file" name="image" id="upload_image" accept="image/*" />
                                        <input hidden type="file" name="photo" id="photo" >


                                     @if(isset($user))
                                         <input id="user_id_img" value="{{$user->id}}" hidden>
                                         <input name="file_name_img" value="empty" id="file_name_img" hidden>
                                     @else
                                         <input id="user_id_img" hidden value="new_user" name="user_img">
                                         <input name="file_name_img" value="empty" id="file_name_img" hidden>
                                     @endif



                                    <label class="my-label-6 img_url_md" for="upload_image" style="cursor:pointer;border: 1px solid #f8f8f8;background-color: unset" >
                                        @if(isset($user) && !is_null($user->img_url))

                                        <img style="width: 200px;height: 200px" id="{{$user->img_url}}"
                                            src="/users_img/{{$user->img_url}}"  >
                                        @else
                                        <img style="width: 200px;height: 200px" src="/users_img/noavatar.png" alt="img">
                                        @endif

                                    </label>



                                    <div class="mt-2 font-weight-bold font-sm text-center " style="width:100%">
                                        
                                        @if(isset($user))
                                            {{$user->last_name}} {{$user->name }}
                                        @else
                                            Новый сотрудник
                                        @endif
                                        
                                    </div>
                                    @if(isset($user)) 
                                    <div class="mt-0 mb-3 font-sm text-center " style="width:100%">
                                    {{$user->email}}
                                    </div>
                                    @endif
                                    <div class="mt-0 mb-3 font-weight-bold font-sm text-center " style="width:100%">
                                        @if(isset($user))
                                            @if($user->position_id)
                                                @foreach ($positions as $position)
                                                    @if($user->position_id == $position->id)
                                                     {{$position->position}}
                                                    @endif
                                                @endforeach
                                            @else
                                                Пользователь CP.U_MARKETING.ORG 
                                            @endif
                                        @else
                                            Новый пользователь
                                        @endif



                                    </div>


                                    <div class="list-item">
                                        <ul class="p-0">
                                            <li  class="bg-this" id="bg-this-1" style="background-color: #e2e2e2" onclick="showBlock('1',this)">
                                                <a href="#" >Основные данные </a>
                                                <span id="check-1"  class="ml-2 fa fa-check none-check" ></span>
                                            </li>


                                            <li class="bg-this" id="bg-this-9" onclick="showBlock('9',this)">
                                                <a href="#" >Документы</a>
                                                <span id="check-9"  class="ml-2 fa fa-check none-check" style="color: #272c33;display: none"></span>
                                            </li>
                                            <li class="bg-this" id="bg-this-4" onclick="showBlock('4',this)">
                                                <a href="#" >Контакты</a>
                                                <span id="check-4"  class="ml-2 fa fa-check none-check" style="color: #272c33;display: none"></span>
                                            </li>
                                            <li class="bg-this" id="bg-this-5"  onclick="showBlock('5',this)">
                                                <a href="#">Оплата</a>
                                                <span id="check-5"  class="ml-2 fa fa-check none-check" style="color: #272c33;display: none"></span>
                                            </li>
                                            <li class="bg-this" id="bg-this-6" onclick="showBlock('6',this)">
                                                <a href="#" >Прочие данные</a>
                                                <span id="check-6"  class="ml-2 fa fa-check none-check" style="color: #272c33;display: none"></span>
                                            </li>
                                            @if(isset($user))
                                            <li class="bg-this" id="bg-this-7" onclick="showBlock('7',this)">
                                                <a href="#" >Адаптационные  данные</a>
                                                <span id="check-7"  class="ml-2 fa fa-check none-check" style="color: #272c33;display: none"></span>
                                            </li>
                                            @endif

                                        </ul>
                                    </div>




                                </div>

                            </div>

                            <!-- ============================================================== -->

                            <div class="xtab-content card scrollspy-example bg-white p-30" id="xmyTabContent"  data-spy="scroll" data-target="#list-example">
                                <!-- first tab -->
                                <div class="xtab-pane xfade show active" id="contact" role="tabpanel"
                                    aria-labelledby="contact-tab">

                                    <!-- PROFILE INFO -->
                                    <div class="d-flex row">
                                        <div class="contacts-info col-md-6 none-block" id="profile_d">
                                            <h5 class="mb-4">Профиль сотрудника </h5>
                                            <div class="form-group row">
                                                <label for="firstName"
                                                    class="col-sm-4 col-form-label font-weight-bold">Имя <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="name" id="firstName" required
                                                        placeholder="Имя сотрудника"
                                                        @if(isset($user)) value="{{$user->name}}"
                                                        @else value="{{old('name')}}"
                                                        @endif
                                                        >
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="lastName"
                                                    class="col-sm-4 col-form-label font-weight-bold">Фамилия <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="last_name" id="lastName" required
                                                        placeholder="Фамилия сотрудника"
                                                        @if(isset($user)) value="{{$user->last_name}}"
                                                         @else value="{{old('last_name')}}"
                                                        @endif
                                                        >
                                                </div>
                                            </div>

                                            @if(isset($user))
                                                <div class="form-group row">
                                                    <label for="email" class="col-sm-4 col-form-label font-weight-bold">Email <span class="red">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="email" name="email" id="email" required
                                                            placeholder="Email"
                                                            value="{{ $user->email }}">
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
                                            @else
                                                <div class="form-group row">
                                                    <label for="email" class="col-sm-4 col-form-label font-weight-bold">Email <span class="red">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="email" name="email" id="email" required
                                                            placeholder="Email"
                                                            value="{{old('email')}}">
                                                    </div>
                                                </div>
                                            @endif


                                            <div class="form-group row">
                                                <label for="lastName"
                                                    class="col-sm-4 col-form-label font-weight-bold">День рождения <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="date" name="birthday" id="birthday" required
                                                        @if(isset($user))@if($user->birthday != null)value="{{ \Carbon\Carbon::parse($user->birthday)->format('Y-m-d')}}"@endif @else value="{{old('birthday')}}"@endif>
                                                </div>
                                            </div>



                                            <div class="form-group row">
                                                <label for="position"
                                                    class="col-sm-4 col-form-label font-weight-bold">Должность </label>
                                                <div class="col-sm-8">
                                                    <select name="position" id="position" class="form-control mb-2" onchange="raf()">
                                                        @foreach ($positions as $position)
                                                        <option value="{{$position->id}}" @if(isset($user) && $user->
                                                            position_id == $position->id)
                                                            selected="selected" @endif>
                                                            {{$position->position}}
                                                        </option>
                                                        @endforeach
                                                    </select>

                                                    @if(isset($user))
                                                    <profile-groups id="position_group"
                                                        :groups="{{ $groups }}"
                                                        :user_id="{{ $user->id }}"
                                                        :in_groups="{{ json_encode($user->head_in_groups) }}"
                                                        :user_role="2" />
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="userType"
                                                    class="col-sm-4 col-form-label font-weight-bold">Тип <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <select name="user_type" id="userType" class="form-control"
                                                        required>
                                                        <option value="office" @if(isset($user) && $user->user_type ==
                                                            'office') selected @endif>
                                                            Офисный работник
                                                        </option>
                                                        <option value="remote" @if(isset($user) && $user->user_type ==
                                                            'remote') selected @endif >
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
                                                        @foreach ($programs as $program)
                                                        <option value="{{$program->id}}" @if(isset($user) && $user->
                                                            program_id==$program->id) selected="selected" @endif>
                                                            {{$program->name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="workingDays"
                                                    class="col-sm-4 col-form-label font-weight-bold">Рабочие дни <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <select name="working_days" id="workingDays" class="form-control"
                                                        required>
                                                        @foreach ($workingDays as $item)
                                                        <option value="{{$item->id}}" @if(isset($user) && $user->
                                                            working_day_id == $item->id) selected="selected"
                                                            @endif>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="workingDays"
                                                       class="col-sm-4 col-form-label font-weight-bold">Найти город <span class="red">*</span></label>
                                                <div class="col-sm-8">
                                                    <div class="mb-3 xfade">
                                                        <div class="form-group row " id="selectedCityRU" >
                                                            <div class="col-sm-12">
                                                                <input class="form-control" name="selectedCityInput" id="selectedCityInput"
                                                                       @if(isset($user) && !empty($user->working_country))
                                                                           value="{{$user->working_country}}"
                                                                       @endif
                                                                       placeholder="Поиск городов ">


                                                                <input hidden @if(isset($user) && !empty($user->working_city) )
                                                                    value="{{$user->working_city}}"
                                                                       @endif
                                                                       name="working_city" id="working_city">

                                                                <div id="listSearchResult" class="listSearchResult">
                                                                    <ul class="p-0 searchResultCountry" id="searchResultCountry" style="margin-bottom: 0px;">

                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="full_time"
                                                    class="col-sm-4 col-form-label font-weight-bold">Ставка</label>
                                                <div class="col-sm-8 d-flex">
                                                    <label class="radio mb-0  mr-4" for="ftr1">
                                                        <input class="form-control" id="ftr1" type="radio" name="full_time" value="1" @if(isset($user) && $user->full_time == 1) checked="checked"@endif> <span>Full-Time</span>
                                                    </label>
                                                    <label class="radio mb-0" for="ftr0">
                                                        <input class="form-control" id="ftr0" type="radio" name="full_time" value="0" @if(isset($user) && $user->full_time == 0) checked="checked"@endif @if(!isset($user)) checked="checked" @endif> <span>Part-Time</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="workingTimes"
                                                    class="col-sm-4 col-form-label font-weight-bold">Рабочие
                                                    часы</label>
                                                <div class="col-sm-8">
                                                    <select name="working_times" id="workingTimes" class="form-control">
                                                        @foreach ($workingTimes as $item)
                                                        <option value="{{$item->id}}" @if(isset($user) && $user->
                                                            working_time_id == $item->id) selected="selected"
                                                            @endif>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row" id="workShedule">
                                                <label for="workingTimes"
                                                    class="col-sm-4 col-form-label font-weight-bold">Рабочий график</label>
                                                <div class="col-sm-8 form-inline">
                                                    <input class="form-control" type="time" class="form-control mr-2 work-start-time"
                                                        name="work_start_time" id="workStartTime"
                                                        value="@if(isset($user)){{$user->work_start}}@endif">
                                                    <label for="workEndTime">До </label>
                                                    <input class="form-control" type="time" class="form-control mx-2 work-end-time"
                                                        name="work_start_end" id="workEndTime"
                                                        value="@if(isset($user)){{$user->work_end}}@endif">
                                                </div>
                                            </div>

                                            <div class="form-group row" id="weekdays">
                                                <label for="workingTimes"
                                                    class="col-sm-4 col-form-label font-weight-bold">Выходные</label>
                                                <div class="col-sm-8 form-inline">
                                                    <input type="hidden" name="weekdays" value="" id="weekdays-input">

                                                    <div class="weekday @if(isset($user) && $user->weekdays[1] == 1 ) active @endif" data-id="1" >Пн</div>
                                                    <div class="weekday @if(isset($user) && $user->weekdays[2] == 1 ) active @endif" data-id="2">Вт</div>
                                                    <div class="weekday @if(isset($user) && $user->weekdays[3] == 1 ) active @endif" data-id="3">Ср</div>
                                                    <div class="weekday @if(isset($user) && $user->weekdays[4] == 1 ) active @endif" data-id="4">Чт</div>
                                                    <div class="weekday @if(isset($user) && $user->weekdays[5] == 1 ) active @endif" data-id="5">Пт</div>
                                                    <div class="weekday @if(isset($user) && $user->weekdays[6] == 1 ) active @endif" data-id="6">Сб</div>
                                                    <div class="weekday @if(isset($user) && $user->weekdays[0] == 1 ) active @endif" data-id="0">Вс</div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="description"
                                                    class="col-sm-4 col-form-label font-weight-bold">Дополнительно</label>
                                                <div class="col-sm-8">
                                                    <textarea rows="3" name="description" class="form-control"
                                                        id="description">@if(isset($user)) {{$user->description}}  @endif</textarea>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6  add_info">
                                            @if(isset($user))

                                                <div id="add_info" class="none-block" >
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
                                                                    <span>{{ \Carbon\Carbon::parse($user->created_at)->format('d.m.Y')}}</span>
                                                                </td>
                                                            </tr>
                                                            @if($user->applied_at != null && $user->is_trainee == 0)
                                                                <tr>
                                                                    <td>
                                                                        <span>Дата принятия на работу</span>
                                                                    </td>
                                                                    <td>
                                                                        <span>{{ \Carbon\Carbon::parse($user->applied_at)->format('d.m.Y')}}</span>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            @if($user->applied_at == null && $user->is_trainee == 0)
                                                                <tr>
                                                                    <td>
                                                                        <span>Дата принятия на работу</span>
                                                                    </td>
                                                                    <td>
                                                                        <span>{{ \Carbon\Carbon::parse($user->created_at)->format('d.m.Y')}}</span>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <td>
                                                                    <span>Успел стать частью команды ~</span>
                                                                </td>
                                                                <td>
                                                                    <span>{{ $user->worked_with_us }}</span>
                                                                </td>
                                                            </tr>

                                                            @if($user->delete_time != null)
                                                                <tr>
                                                                    <td>
                                                                        <span>Дата отработки</span>
                                                                    </td>
                                                                    <td>
                                                                        <span>{{ \Carbon\Carbon::parse($user->delete_time)->format('d.m.Y')}}</span>
                                                                    </td>
                                                                </tr>
                                                            @endif


                                                            @if($user->deleted_at != null && $user->deleted_at != '0000-00-00 00:00:00')
                                                                <tr>
                                                                    <td>
                                                                        <span>Дата увольнения</span>
                                                                    </td>
                                                                    <td>
                                                                        <span>{{ \Carbon\Carbon::parse($user->deleted_at)->format('d.m.Y')}}</span>
                                                                    </td>
                                                                </tr>

                                                                @if (isset($user->downloads) && $user->downloads->resignation)
                                                                    <tr>
                                                                        <td>
                                                                            <span>Заявление об увольнении</span>
                                                                        </td>
                                                                        <td>
                                                                            <a  download=""
                                                                                class="d-block"
                                                                                href="/static/profiles/{{$user->id}}/resignation/{{$user->downloads->resignation}}">Скачать</a>
                                                                        </td>
                                                                    </tr>
                                                                @endif

                                                                @if($user->fire_cause != null)
                                                                    <tr>
                                                                        <td>
                                                                            <span>Причина увольнения</span>
                                                                        </td>
                                                                        <td>
                                                                            <span>{{ $user->fire_cause }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endif


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>


                                                <div id="adaptation_conversations" class="none-block" style="display: none">
                                                    <p class="adaptation-title mt-3 mb-2">Адаптационные беседы</p>
                                                    @foreach($user->adaptation_talks as $key => $talk)
                                                        <div class="adaptation_talk col-12 mt-3">

                                                            <div class="col-2 div_1 ml-0 p-0">{{ $talk['day'] }} й день
                                                                <input type="hidden" name="adaptation_talks[{{ $key }}][day]" value="{{ $talk['day'] }}">
                                                            </div>

                                                            <div class="col-3 div_2 p-0" style="flex-direction:unset">

                                                                <input class="mr-2" type="text" name="adaptation_talks[{{ $key }}][inter_id]" placeholder="Кто провел" value="{{ $talk['inter_id'] }}">

                                                                <input  class="form-control ml-2" type="date" name="adaptation_talks[{{ $key }}][date]"
                                                                       @if($talk['date'] != null) value="{{ \Carbon\Carbon::parse($talk['date'])->format('Y-m-d')}}"
                                                                       @else  value="null" @endif>
                                                            </div>

                                                            <div class="col-6 div_3 p-0 ml-4">
                                                                <textarea name="adaptation_talks[{{ $key }}][text]" placeholder="Комментарии">{{ $talk['text'] }}</textarea>
                                                            </div>

                                                        </div>
                                                    @endforeach
                                                </div>

                                            @endif


                                            <!-- dannye -->


                                            <!-- groups tab -->
                                            <div class="mb-3 xfade none-block" id="iphones3" >
                                                <!--  -->

                                                <h5 class="mb-4">Группы</h5>


                                                @if(isset($user))
                                                <profile-groups :groups="{{ $groups }}" :user_id="{{ $user->id }}" :in_groups="{{ json_encode($user->in_groups) }}" />
                                                @else
                                                <select name="group" id="group" class="form-control">
                                                    <option>Выберите группу</option>
                                                    @foreach ($groups as $group)
                                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                                    @endforeach
                                                </select>
                                                @endif





                                                <!--  -->
                                            </div>







                                            <div class="mb-4 none-block" id="profile_books" >
                                                @if(isset($user))
                                                <h5 class="mb-4 mt-4">Книги</h5>

                                                <profile-books :books="{{ $corpbooks }}" :user_id="{{ $user->id }}"
                                                    @if(isset($user->in_books))
                                                        :in_books="{{ $user->in_books }}"
                                                    @else
                                                        :in_books="[]"
                                                    @endif
                                                    />

                                                @endif
                                            </div>

                                            <!-- end of groups and books tab -->

                                            <!-- documents tab -->
                                            <div class="xtab-pane xfade none-block" id="iphones4" style="display: none">
                                                <!--  -->
                                                <h5 class="mb-4 mt-1">Документы (RAR, ZIP)</h5>

                                                <div class="box mb-4 data-information file-uploads">



                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file5" id="file-5"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        @if(isset($user->downloads) && $user->downloads->ud_lich)
                                                        <label for="file-5">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#{{ pathinfo($user->downloads->ud_lich, PATHINFO_EXTENSION) }}-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Удостоверение личности</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/{{$user->id}}/ud_lich/{{$user->downloads->ud_lich}}">Скачать</a>
                                                        @else
                                                        <label for="file-5">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Удостоверение личности</span>
                                                        </label>
                                                        @endif
                                                    </div>
                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file1" id="file-1"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        @if (isset($user->downloads) && $user->downloads->dog_okaz_usl)
                                                        <label for="file-1">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#{{ pathinfo($user->downloads->dog_okaz_usl, PATHINFO_EXTENSION) }}-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Договор оказания услуг</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/{{$user->id}}/dog_okaz_usl/{{$user->downloads->dog_okaz_usl}}">Скачать</a>
                                                        @else
                                                        <label for="file-1">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Договор оказания услуг</span>
                                                        </label>
                                                        @endif
                                                    </div>
                                                    <div class="d-inline-block text-center align-text-bottom">
                                                        <input type="file" name="file2" id="file-2"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        @if (isset($user->downloads) && $user->downloads->sohr_kom_tainy)
                                                        <label for="file-2">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#{{ pathinfo($user->downloads->sohr_kom_tainy, PATHINFO_EXTENSION) }}-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Сохранение комм. тайны</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/{{$user->id}}/sohr_kom_tainy/{{$user->downloads->sohr_kom_tainy}}">Скачать</a>
                                                        @else
                                                        <label for="file-2">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Сохранение комм. тайны</span>
                                                        </label>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="box mb-4 data-information file-uploads">

                                                    <div class="d-inline-block text-center align-text-bottom">
                                                        <input type="file" name="file3" id="file-3"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        @if (isset($user->downloads) && $user->downloads->dog_o_nekonk)
                                                        <label for="file-3">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#{{ pathinfo($user->downloads->dog_o_nekonk, PATHINFO_EXTENSION) }}-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Договор о неконкуренции</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/{{$user->id}}/dog_o_nekonk/{{$user->downloads->dog_o_nekonk}}">Скачать</a>
                                                        @else
                                                        <label for="file-3">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Договор о неконкуренции</span>
                                                        </label>
                                                        @endif
                                                    </div>
                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file4" id="file-4"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        @if (isset($user->downloads) && $user->downloads->trud_dog)
                                                        <label for="file-4">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#{{ pathinfo($user->downloads->trud_dog, PATHINFO_EXTENSION) }}-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Трудовой <br> договор</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/{{$user->id}}/trud_dog/{{$user->downloads->trud_dog}}">Скачать</a>
                                                        @else
                                                        <label for="file-4">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Трудовой <br> договор</span>
                                                        </label>
                                                        @endif
                                                    </div>

                                                    <div class="d-inline-block text-center">
                                                        <input type="file" name="file7" id="file-7"
                                                            class="inputfile inputfile-1 d-none"
                                                            data-multiple-caption="{count} files selected">
                                                        @if (isset($user->downloads) && $user->downloads->archive)
                                                        <label for="file-7">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use
                                                                    xlink:href="#{{ pathinfo($user->downloads->archive, PATHINFO_EXTENSION) }}-icon">
                                                                </use>
                                                            </svg>
                                                            <span>Архив <br>документов</span>
                                                        </label>
                                                        <a download="" class="d-block"
                                                            href="/static/profiles/{{$user->id}}/archive/{{$user->downloads->archive}}">Скачать</a>
                                                        @else
                                                        <label for="file-7">
                                                            <svg width="20" height="30" class="iconsfile">
                                                                <use xlink:href="#download-icon"></use>
                                                            </svg>
                                                            <span>Архив <br> документов</span>
                                                        </label>
                                                        @endif
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
                                        <div class="phones col-md-6 none-block" id="profile_contacts" style="display: none">
                                         <h5 class="mb-4">Контакты</h5>

                                         <div class="d-flex phone-row form-group mb-2 ">
                                             <label for="phone" class="col-sm-4 col-form-label font-weight-bold">Мобильный <span class="red">*</span></label>

                                             <div class="col-sm-12">
                                                 <input  class="form-control mr-1 col-sm-8" type="text"
                                                 value="@if(isset($user)){{$user->phone}}@else{{old('phone')}}@endif"
                                                 name="phone" id="phone" placeholder="Телефон">
                                             </div>

                                         </div>

                                        <div class="d-flex phone-row form-group mb-2">
                                            <label for="phone_1" class="col-sm-4 col-form-label font-weight-bold">Домашний <span class="red">*</span></label>
                                            <div class="col-sm-12">
                                                <input class="form-control mr-1 col-sm-8" type="text"
                                                value="@if(isset($user)){{$user->phone_1}}@else{{old('phone_1')}}@endif"
                                                name="phone_1" id="phone_1" placeholder="Телефон">
                                            </div>

                                        </div>

                                        <div class="d-flex phone-row form-group mb-2">
                                            <label for="phone_2" class="col-sm-4 col-form-label font-weight-bold">Супруга/Муж <span class="red">*</span></label>
                                            <div class="col-sm-12">
                                                <input class="form-control mr-1 col-sm-8" type="text"
                                                value="@if(isset($user)){{$user->phone_2}}@else{{old('phone_2')}}@endif"
                                                name="phone_2" id="phone_2" placeholder="Телефон">
                                            </div>
                                        </div>
                                        <div class="d-flex phone-row form-group mb-2">
                                            <label for="phone_3" class="col-sm-4 col-form-label font-weight-bold">Друг/Брат/Сестра <span class="red">*</span></label>
                                            <div class="col-sm-12">
                                                <input class="form-control mr-1 col-sm-8" type="text"
                                                value="@if(isset($user)){{$user->phone_3}}@else{{old('phone_3')}}@endif"
                                                name="phone_3" id="phone_3" placeholder="Телефон">
                                            </div>
                                        </div>
                                        <div class="d-flex phone-row form-group mb-2">
                                            <label for="phone_4" class="col-sm-4 col-form-label font-weight-bold">Сын/Дочь</label>
                                            <div class="col-sm-12">
                                                <input  class="form-control mr-1 col-sm-8" type="text"
                                                value="@if(isset($user)){{$user->phone_4}}@else{{old('phone_4')}}@endif"
                                                name="phone_4" id="phone_4" placeholder="Телефон">
                                            </div>
                                        </div>

                                            @if(isset($user))
                                            @foreach($user->profileContacts as $key => $contact)
                                            @if($contact->type == 'phone')
                                            <div class="d-flex phone-row form-group m0">
                                                <input class="form-control mr-2" class="form-control mr-1" type="text" value="{{$contact->name}}"
                                                    name="contacts[phone][{{$key}}][name]" placeholder="Название">
                                                <input class="form-control" class="form-control mr-1" type="text" value="{{$contact->value}}"
                                                    name="contacts[phone][{{$key}}][value]" placeholder="Телефон">
                                                <button class="btn btn-danger btn-sm contact-delete rounded"
                                                    type="button" onclick="deletePhone(event)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            @endif
                                            @endforeach
                                            @endif

                                            <button class="btn btn-phone btn-rounded mb-2 mt-2" type="button"
                                                onclick="addPhone()">
                                                <i class="fa fa-plus"></i> Добавить телефон
                                            </button>
                                        </div>
                                        <!-- end of phones -->


                                         <!-- zarplata tab -->
                                        <div class="col-md-12 mt-3 none-block" id="profile_salary" style="display: none">

                                            <h5 class="mb-4">Оплата</h5>

                                            <div class="form-group row">
                                                <label for="zarplata"

                                                       @if(isset($user)) class="col-sm-3 col-form-label font-weight-bold" @else class="col-sm-2 col-form-label font-weight-bold mr-3"  @endif


                                                      >Оклад <span class="red">*</span></label>

                                                <div @if(isset($user)) class="col-sm-3" @else class="col-sm-4 p-0 "  @endif>
                                                    <input class="form-control" type="text" name="zarplata" id="zarplata" required
                                                    placeholder="Оклад"
                                                    @if(isset($user->zarplata))
                                                        @if($user->zarplata->zarplata == 0) value="0"
                                                        @else  value="{{$user->zarplata->zarplata}}"
                                                        @endif
                                                    @else
                                                        @if(old('zarplata') == '')  value="0"
                                                        @else  value="{{old('zarplata')}}"
                                                        @endif
                                                    @endif
                                                    >
                                                </div>


                                                <div @if(isset($user)) class="col-sm-3" @else class="col-sm-5 pl-1 pr-0"  @endif  >
                                                    <select name="currency" id="currency" class="form-control form-control-sm">
                                                        <option selected disabled> Валюта</option>
                                                        <option value="kzt" @if(isset($user) && $user->currency == 'kzt') @endif>KZT Казахстанский тенге</option>
                                                        <option value="rub" @if(isset($user) && $user->currency == 'rub') selected @endif>RUB Российский рубль</option>
                                                        <option value="kgs" @if(isset($user) && $user->currency == 'kgs') selected @endif>KGS Киргизский сом</option>
                                                        <option value="uzs" @if(isset($user) && $user->currency == 'uzs') selected @endif>UZS Узбекский сум</option>
                                                        <option value="uah" @if(isset($user) && $user->currency == 'uah') selected @endif>UAH Украинская гривна</option>
                                                        <option value="byn" @if(isset($user) && $user->currency == 'byn') selected @endif>BYN Белорусский рубль</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 pl-0" >
                                                    @if(isset($user))
                                                        <profile-quarter-button :user_id="{{ $user->id }}"/>
                                                    @endif
                                                </div>


                                            </div>
                                            @if(isset($user))

                                             @if(isset($user->zarplata->kaspi_cardholder) && !empty($user->zarplata->kaspi_cardholder))
                                                 <div class="form-group row">
                                                <label for="kaspi" class="col-sm-3 col-form-label font-weight-bold">KASPI BANK</label>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="kaspi_cardholder" id="kaspi_cardholder"
                                                        placeholder="Имя на карте"
                                                        value="@if(isset($user->zarplata)){{$user->zarplata->kaspi_cardholder}}@else{{old('kaspi_cardholder')}}@endif">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="kaspi" id="kaspi"
                                                        placeholder="+7 (777) 777-77-77"
                                                        value="@if(isset($user->zarplata)){{$user->zarplata->kaspi}}@else{{old('kaspi')}}@endif">
                                                </div>
                                                <div class="col-sm-3" style="padding-left:0">
                                                    <input class="form-control card-number" type="text" name="card_kaspi" id="card_kaspi"
                                                        placeholder="XXXX XXXX XXXX XXXX" style="font-size: 14px;"
                                                        value="@if(isset($user->zarplata)){{$user->zarplata->card_kaspi}}@else{{old('card_kaspi')}}@endif">
                                                </div>
                                            </div>
                                             @endif

                                            @if (isset($user->zarplata->jysan_cardholder) && !empty($user->zarplata->jysan_cardholder))
                                                 <div class="form-group row">
                                                <label for="jysan"
                                                    class="col-sm-3 col-form-label font-weight-bold">JYSAN BANK</label>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="jysan_cardholder" id="jysan_cardholder"
                                                        placeholder="Имя на карте"
                                                        value="@if(isset($user->zarplata)){{$user->zarplata->jysan_cardholder}}@else{{old('jysan_cardholder')}}@endif">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input class="form-control" type="text" name="jysan" id="jysan"
                                                        placeholder="+7 (777) 777-77-77"
                                                        value="@if(isset($user->zarplata)){{$user->zarplata->jysan}}@else{{old('jysan')}}@endif">
                                                </div>
                                                <div class="col-sm-3" style="padding-left:0">
                                                    <input class="form-control card-number" type="text" name="card_jysan" id="card_jysan"
                                                        placeholder="XXXX XXXX XXXX XXXX" style="font-size: 14px;"
                                                        value="@if(isset($user->zarplata)){{$user->zarplata->card_jysan}}@else{{old('card_jysan')}}@endif">
                                                </div>
                                            </div>
                                            @endif

                                            @endif


                                            @if(isset($user))
                                            <div class="form-group row">
                                                <label for="headphones_amount_checkbox"
                                                       class="col-sm-3 col-form-label font-weight-bold">Выданы наушники <br>
                                                    @if(isset($user))
                                                        {{ $user->headphones_date }}
                                                    @endif
                                                </label>

                                                <div class="col-sm-1">
                                                    <input type="checkbox" class="form-control" id="headphones_amount_checkbox"
                                                           @if(isset($user))
                                                                 @if($user->headphones_amount > 0) checked @endif
                                                           @endif
                                                    >
                                                </div>
                                                <div class="col-sm-2" >
                                                    <label for="headphones_amount"
                                                           class="col-form-label font-weight-bold">На сумму</label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input name="headphones_amount" class="form-control" type="number"
                                                           id="headphones_amount" value="{{ $user->headphones_amount }}" @if(isset($user)) @if($user->headphones_amount == 0) disabled @endif @endif>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="cards">

                                                @if(isset($user))
                                                    @foreach($user->cards as $card)
                                                        <div class="d-flex form-group m0 card-row">
                                                            <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1{{$card->id}}][bank]" placeholder="Банк" value="{{ $card->bank }}">
                                                            <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1{{$card->id}}][country]" placeholder="Страна" value="{{ $card->country }}">
                                                            <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1{{$card->id}}][cardholder]" placeholder="Имя на карте" value="{{ $card->cardholder }}">
                                                            <input  class="form-control mr-1 col-sm-2" type="text" name="cards[1{{$card->id}}][phone]" placeholder="Телефон" value="{{ $card->phone }}">
                                                            <input  class="form-control mr-1 col-sm-3 card-number mr-5" type="text" name="cards[1{{$card->id}}][number]" placeholder="Номер карты" value="{{ $card->number }}">
                                                            <button class="btn btn-danger btn-sm card-delete rounded ml-5" type="button" onclick="deleteCard(event)"><i class="fa fa-trash"></i></button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <button class="btn btn-phone btn-rounded mb-2 mt-2" type="button"
                                                    onclick="addCard()">
                                                    <i class="fa fa-plus"></i> Добавить карту
                                            </button>




                                            <!-- END OF OKLAD -->
                                        </div>




                                         <!-- additional tab -->
                                        <div id="other_data" class="none-block" style="display: none">
                                            <div class="col-md-12 mt-3">
                                                <h5 class="mb-4">Прочие данные</h5>
                                            </div>

                                            <div class="col-md-8 ">

                                                <div class="form-group row">
                                                    <label for="description"
                                                           class="col-sm-4 col-form-label font-weight-bold">Примечание для внешнего рекрутера</label>
                                                    <div class="col-sm-8">
                                                    <textarea name="recruiter_comment" class="form-control"
                                                              id="recruiter_comment"  placeholder="Коментарии">
                                                         @if(isset($user))
                                                              {{$user->recruiter_comment}}
                                                         @endif

                                                    </textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                              @if(isset($user))
                                                @if($user->lead)
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
                                                                    <span>{{ $user->segment }}</span>
                                                                </td>
                                                            </tr>

                                                            @if($user->lead->lead_id != 0)
                                                                <tr>
                                                                    <td>
                                                                        <span>Лид</span>
                                                                    </td>
                                                                    <td>
                                                                <span>
                                                                    <a href="https://infinitys.bitrix24.kz/crm/lead/details/{{$user->lead->lead_id}}/"
                                                                       target="_blank">
                                                                        {{ $user->lead->lead_id }}
                                                                    </a>
                                                                </span>
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            @if($user->lead->deal_id != 0)
                                                                <tr>
                                                                    <td>
                                                                        <span>Сделка</span>
                                                                    </td>
                                                                    <td>
                                                                <span>
                                                                    <a href="https://infinitys.bitrix24.kz/crm/lead/details/{{$user->lead->deal_id}}/"
                                                                       target="_blank">
                                                                        {{ $user->lead->deal_id }}
                                                                    </a>
                                                                </span>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                              @endif
                                            </div>




                                        </div>



                                        @if(isset($user))
                                        <div id="other_data_bitrix" class="none-block" style="display: none">
                                            <div class="col-md-12">

                                                @if($user->bitrix_id != 0)
                                                    <p>
                                                        <b>Битрикс: <span class="text-green">Есть</span></b>
                                                        <a href="https://infinitys.bitrix24.kz/company/personal/user/{{ $user->bitrix_id }}/" target="_blank">
                                                            <i class="fa fa-link pointer"></i>
                                                        </a>
                                                        <i class="fa fa-cogs pointer" title="Редактировать" id="bitrix_editor"></i>
                                                    </p>
                                                @else
                                                    <p>
                                                        <b>
                                                            Битрикс:
                                                            <span class="text-red">Нет</span>
                                                        </b>
                                                        <i class="fa fa-cogs pointer" title="Редактировать" id="bitrix_editor"></i>
                                                    </p>
                                                @endif
                                                <input type="text"
                                                       style="display:none;"
                                                       class="form-control form-control-sm"
                                                       id="bitrix_id_input"
                                                       value="{{ $user->bitrix_id }}"
                                                       name="bitrix_id"
                                                       placeholder="ID профиля в битриксе">
                                                <profile-kpi-button :user_id="{{ $user->id }}"/>
                                            </div>
                                        </div>
                                        @endif


                                        


                                    </div>
                                    <!--  -->
                                </div>

                                

                                
                            </div>
                        </div>
                        @if($errors->any())
                        <u-modal items="{{ json_encode($errors->all()) }}" title="Не сохранено" />
                        @endif
                    </form>

            </div>
        </div>
    </div>
</div>




@if(isset($user))
@if (is_null($user->deleted_at))
<b-modal id="modal-deactivate" hide-footer hide-header>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Вы уверены что хотите уволить пользователя?</h6>
            </div>
            <div class="text-center mb-3">
                <form action="/timetracking/delete-person" id="deleteForm" enctype="multipart/form-data"  method="post">
                    <input class="form-control" type="hidden" name="id" value="{{$user->id}}">
                    <input class="form-control" type="hidden" name="delay" value="1" id="delay">

                    {{ csrf_field() }}
                    <div class="row align-items-center justify-content-center p-3" style="padding-bottom:0!important">
                        
                        <div class="col-md-12 mb-2">
                            <select name="cause" id="cause" class="form-control form-control-sm">
                                <option value="" selected disabled>Выберите причину</option>
                                @foreach($fire_causes as $cause)
                                    <option value="{{ $cause }}">{{ $cause }}</option>
                                @endforeach
                            </select>
                        </div>

                        
                        @if(isset($user) && !$user->is_trainee)
                        <div class="col-md-6">
                            <div class="box">
                                <div class="d-inline-block text-center" style="width:100%">
                                <input type="file" name="file8" id="file-8"
                                        class="inputfile inputfile-1" style="display:none">
                                    @if (isset($user->downloads) && $user->downloads->resignation)
                                    <label for="file-8">
                                        <svg width="20" height="30" class="">
                                            <use
                                                xlink:href="#{{ pathinfo($user->downloads->resignation, PATHINFO_EXTENSION) }}-icon">
                                            </use>
                                        </svg>
                                        <span>Заявление об <br>увольнении</span>
                                    </label>
                                    @else
                                    <label for="file-8">
                                        <svg width="20" height="30" class="">
                                            <use xlink:href="#download-icon"></use>
                                        </svg>
                                        <span>Заявление об <br>увольнении</span>
                                    </label>
                                    @endif
                                </div>
                            </div>  
                        </div>
                        <div class="col-md-6 d-flex justify-content-between flex-column">
                            <button type="submit" class="btn btn-success rounded mb-2" id="deleteUser">Да</button>
                            <button type="reset" class="btn btn-primary rounded " data-dismiss="modal">Нет</button>
                        </div>
                        @else
                        <div class="col-md-12 mb-2">
                            <input class="form-control" type="text" name="cause2" placeholder="Или напишите свой вариант" value="" id="cause2" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 d-flex justify-content-between flex-column">
                            <button type="submit" class="btn btn-success rounded">Да</button>
                        </div>
                        <div class="col-md-6 d-flex justify-content-between flex-column">
                            <button type="reset" class="btn btn-primary rounded" data-dismiss="modal">Нет</button>
                        </div>
                        @endif
                        
                        
                                        

                        
                    </div>
                    <div class="row mt-2 px-5">
                        <span id="deleteError" style="color:red;text-align:center;width:100%"></span>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</b-modal>
@else
<b-modal id="modal-activate" hide-footer hide-header>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Вы уверены что хотите востановить пользователя?</h6>
            </div>
            <div class="text-center mb-3 ">
                <form action="/timetracking/recover-person">
                    <input class="form-control" type="hidden" name="id" value="{{$user->id}}">
                    <button type="submit" class="btn btn-success" id="deleteUserButton">Да</button>
                    <button type="reset" class="btn btn-primary" data-dismiss="modal">Нет</button>
                </form>
            </div>
        </div>
    </div>
</b-modal>
@endif
@endif
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
@include('admin.svg.icons')

@endsection

@section('scripts')




    <div id="uploadimageModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <a onclick="$('#uploadimageModal').modal('hide')"  class="close" >&times;</a>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 text-center">
                            <div id="image_demo" style="width:455px; margin-top:15px"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button  class="btn btn-default crop_image" onclick="crop_image()" >Сохранить</button>
                </div>
            </div>
        </div>
    </div>
<script>

 var getFileKis;

    $(document).ready(function(){



        $image_crop = $('#image_demo').croppie({
            enableExif: true,
            viewport: {
                width:200,
                height:200,
                type:'square' //circle
            },
            boundary:{
                width:300,
                height:300
            }
        });


        $('#upload_image').on('change', function(){

            var reader = new FileReader();
            reader.onload = function (event) {
                $image_crop.croppie('bind', {
                    url: event.target.result
                }).then(function(){


                    console.log('jQuery bind complete');
                });
            }

             getFileKis = this.files[0];

            reader.readAsDataURL(this.files[0]);

            $('#uploadimageModal').modal('show');


        });


    });

    function crop_image(){
        $image_crop.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function(response){
            var user_id = $("#user_id_img").val();
            var file_name = $("#file_name_img").val();
            var origin_file = $("#upload_image").val();



           console.log(response,'789465312')

            $.ajax({
                type:'POST',
                url: "/profile/upload/edit/",
                data:{
                    "image": response,
                    'user_id':user_id,
                    'file_name':file_name,
                     // file:getFileKis
                },
                // cache: false,
                // contentType: 'json',
                // processData: false,
                success: (data) => {

                    console.log(data,'imasheev kis')

                    $('#uploadimageModal').modal('hide');

                    $(".img_url_md").html(data['src'])

                    $(".img_url_sm").html(data['src'])

                    $("#file_name_img").attr('value',data['filename'])


                },
                error: function(data){
                    console.log(data);
                }
            });
        })
    }

    $("#profile_d").show();
    $("#add_info").show(" ");///доп инф
    $("#profile_books").show(" "); /// база знание
    $("#iphones3").show(" "); /// группы


    function showBlock(type,elem)
    {

        $(".none-block").hide();
        $(".none-check").hide();
        $(".bg-this").css('background-color','rgb(248 248 248)')


        $("#check-"+type).show();
        $("#bg-this-"+type).css('background-color','rgb(227 229 232)')



        if(type == 1){
            $("#profile_d").show();
            $("#add_info").show(" ");///доп инф
            $("#profile_books").show(" "); /// база знание
            $("#iphones3").show(" "); /// группы
        }else if(type == 7){
            $("#adaptation_conversations").show(" ")
        }else if(type == 2){
            $("#iphones3").show(" ");
        }else if(type == 5){
            $("#profile_salary").show(" ");
        }else if(type == 9){
            $("#iphones4").show(" ");
        }else if(type == 4){
            $("#profile_contacts").show(" ");
        }else if(type == 6){
            $("#other_data").show(" ");
            $("#other_data_bitrix").show(" ");
        }




    }

    function liHoverOver(element){
        $("#li-hover-jquery-"+element).css('background-color','rgb(236 244 249)');
    }

    function liHoverOut(element){
        $("#li-hover-jquery-"+element).css('background-color','rgb(245, 245, 245)');
    }

    document.getElementById('selectedCityInput').onkeyup = function() {
        let _token   = $('meta[name="csrf-token"]').attr('content');
        var value = document.getElementById('selectedCityInput').value;

        if (value.length){
            $.ajax({
                url: "/selected-country/search/",
                type:"POST",
                data:{
                    value:value,
                    _token: _token,
                },
                success:function(response){
                    $("#searchResultCountry").empty();
                    $("#listSearchResult").css('display','block');

                    if (response[0].length > 0){
                        for (let i = 0; i < response[0].length; i++) {
                            $("#searchResultCountry").append(
                                '<li id="li-hover-jquery-'+response[0][i]['id']+'"  onmouseover="liHoverOver('+response[0][i]['id']+')"    onmouseout="liHoverOut('+response[0][i]['id']+')"   onclick="pasteSearchInput('+response[0][i]['id']+')"  style="cursor: pointer; background-color: #f5f5f5;padding: 10px;border-bottom: 1px solid white;" class="searchResultCountry">' +
                                    '<a id="hiddenCity-'+response[0][i]['id']+'"><strong>Страна: </strong> '+response[0][i]['country']+' <strong>Город: </strong> '+response[0][i]['city']+'</a><' +
                                '/li>'
                            );
                        }
                    }else{
                        $("#searchResultCountry").append(
                            '<li style="cursor: pointer; background-color: #f5f5f5;padding: 10px;" class="searchResultCountry"><a>Нет найденных городов </a></li>'
                        );
                    }

                },

            });
        }else{

            $("#searchResultCountry").empty();
            $("#listSearchResult").css('display','none');
        }




    }


    function pasteSearchInput(type_id){
        var kis = $("#hiddenCity-"+type_id).text();
        $("#working_city").val(type_id);
        $("#selectedCityInput").val(kis);
        $("#listSearchResult").hide();

    }




    function bitrix_quarter_editor(){
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
        name = $('#firstName').val(),
        last_name = $('#lastName').val(),
        birthday = $('#birthday').val(),
        email = $('#email').val(),
        zarplata = $('#zarplata').val();
        workingCity = $('#workingCity').val();



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

    // if (phone_1.length < 6) {
    //     $('#beforeSubmit .texter').append('<div>Контакты: <b>Домашний</b></div>');
    //     counter++;
    //     phone_errors++
    // }

    // if (phone_2.length < 11) {
    //     $('#beforeSubmit .texter').append('<div>Контакты: <b>Супруга/Муж</b></div>');
    //     counter++;
    //     phone_errors++
    // }

    // if (phone_3.length < 11) {
    //     $('#beforeSubmit .texter').append('<div>Контакты: <b>Друг/Брат/Сестра</b></div>');
    //     counter++;
    //     phone_errors++
    // }


    // if (workingCity == null) {
    //     $('#beforeSubmit .texter').append('<div>Город: <b>Астана</b></div>');
    //     counter++;
    //     profile_errors++
    // }

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
        <input class="form-control mr-1 col-sm-3 card-number mr-5" type="text" name="cards[${index}][number]" placeholder="Номер карты">
        <button class="btn btn-danger btn-sm card-delete rounded ml-5" type="button" onclick="deleteCard(event)"><i class="fa fa-trash"></i></button>
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

@if(isset($user))
$('#weekdays-input').val('{{$user->weekdays}}');
@else
$('#weekdays-input').val('0000000');
@endif
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
<script>
function selectedCountry() {

    let value = $("#workingCountry").val();
    let _token   = $('meta[name="csrf-token"]').attr('content');


    $.ajax({
    url: "/selected-country",
    type:"POST",
    data:{
        value:value,
        _token: _token,
    },
    success:function(response){







        if (response['success'] == 1){
            $("#workingCity").append().empty();
            $("#selectedCityHide").show();
            $("#selectedCityRU").hide();
            for (var i = 0; i < response.city.length;i++){
                $("#workingCity").append('<option value="' + response.city[i]['id'] + '">' + response.city[i]['city'] + '</option>');
            }
        }else if (response['success'] == 2){
            $("#selectedCityRU").show();
            $("#selectedCityHide").hide();

            input.oninput = function() {
                console.log(input.value,'1122333')
            };
        }else{
            console.log(response);
            alert('ggg')
        }







    },
    error: function(error) {
        console.log(error);
    }
    });



}
</script>
@endsection

@section('styles')
@include('admin.users.common')
<style>
.modal-backdrop {
    opacity: 0.4;
}

#other_data{
    width: 100%;
}

.bg-this a {
    color: black;
}

.bg-this{
    cursor: pointer;
}

.none-block{
    display: none;
}

    .my-label-6{
        display: flex;
        flex-direction: column;
        text-align: center;
        width: fit-content;
        align-items: center;
        background: #e9ecef;
        margin: 0 auto;
        border-radius: 3px
    }
  #list-example{
      flex: 0 0 290px;
      max-width: 290px;
      overflow-x: hidden;
      float: left;
      padding: 10px;
      background: #f8f8f8;
      display: flex;
      height: 100%;
      min-height: 100vh;
      border-right: 1px solid #dfdfdf;
      position: -webkit-sticky;
      position: sticky;
      top: 0;
      width: 290px;
      flex-direction: column;
  }

  .list-item li{
      padding: 5px;

  }

  .list-item ul li a {

  }

  .list-item ul li  {
      margin-top: 2px;
  }

.listSearchResult{
    border: 1px solid #d9d9d9;
    margin-top: 10px;
    overflow-x: hidden;
    max-height: 200px;
    display:none;
}
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

.add_info{

    margin-top: 100px;
}
</style>
@endsection