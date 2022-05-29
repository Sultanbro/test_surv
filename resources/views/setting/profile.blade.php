@extends('layouts.app')

@section('content')

<div class="edit">
    <h1>Редактирование Вашего профиля</h1>
    <div class="message">
    </div>
    <form class="form-edit editProfile" method="post">
        {{ csrf_field() }}
        <div class="edit-columns edit-columns-border">
            <div class="edit-column">
                <strong>Личные данные</strong>
                <div class="edit-subrow">
                    <div class="edit-subrow-foto">
                        <div><img src="/images/foto-1.png" alt="foto"></div>
                    </div>
                    <input type="text" name="name" placeholder="Имя" value="{{$user->name}}">
                    <input type="text" name="last_name" placeholder="Фамилия" value="{{$user->last_name}}">
                </div>
                <div class="edit-row">
                    <label class="item-10">Телефон</label>
                    <div><input type="text" name="phone" value="{{$user->PHONE}}"></div>
                </div>
                <div class="edit-row">
                    <label class="item-11">Часовой пояс</label>
                    <div>
                        <select name="timezone" style="    float: left;
    margin: 0;
    padding: 7px 10px;
    background: #f0f0f0;
    width: 260px;
    height: 34px;
    font-size: 12px;
    font: 14px/26px 'Open Sans', Arial, Helvetica, sans-serif;
    box-sizing: border-box;
    border: none;
    border-radius: 3px;
    color: #202226;">
                            @foreach(\App\Setting::TIMEZONES as $offset => $timezone)
                            <option value="{{$offset}}" {{$user->timezone== $offset ? 'selected' :''}}>{{$timezone}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="edit-row">
                    <label class="item-12">E-mail</label>
                    <div><input type="text" value="{{$user->email}}" disabled=""></div>
                </div>
                <div class="edit-row">
                    <label class="item-13">Город</label>
                    <div><input type="text" name="city" value="{{$user->CITY}}"></div>
                </div>
                <div class="edit-row">
                    <label class="item-13">Адрес</label>
                    <div><input type="text" name="address" value="{{$user->ADDRESS}}"></div>
                </div>
            </div>
            <div class="edit-column">
                <strong>Дополнительная информация</strong>
                <div class="edit-row">
                    <span><em><label class="item-lh">Название компании</label></em></span>
                    <div><input type="text" name="company_name" value="{{$user->COMPANY}}" autocomplete="off" readonly
                            onfocus="this.removeAttribute('readonly')"></div>
                </div>
                <div class="edit-row">
                    <label class="item-lh">Описание деятельности компаниии</label>
                    <div><textarea cols="5" name="company_desc" rows="5">{{$user->DESCRIPTION}}</textarea></div>
                </div>
                <div class="edit-row">
                    <span><em><label class="item-lh">Порог уведомлений о балансе</label></em></span>
                    <div><input type="number" name="balance_notice" value="{{$user->BALANCE_NOTIFY}}"></div>
                </div>



            </div>
        </div>
        <div class="edit-columns">
            <div class="edit-column">
                <strong>Изменение пароля</strong>
                <div class="edit-row">
                    <label class="item-14">Пароль</label>
                    <div><input type="password" name="password" autocomplete="off" readonly
                            onfocus="this.removeAttribute('readonly')"></div>
                </div>
                <div class="edit-row">
                    <label class="item-14">Повторить</label>
                    <div><input type="password" name="repassword" autocomplete="off" readonly
                            onfocus="this.removeAttribute('readonly')"></div>
                </div>
            </div>

            <div class="edit-column blockUniqName">
                <strong>Уникальное имя для ваших рассылок</strong>
                <div class="listUniqName">
                    <table id="UniqName">
                        <thead>
                            <tr>
                                <th><strong><em>Название</em> <span><a class="top" href="#">top</a> <a href="#"
                                                class="bot">bot</a></span></strong></th>
                                <th><strong><em>Статус</em> <span><a class="top" href="#">top</a> <a href="#"
                                                class="bot">bot</a></span></strong></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tr>
                            <td>Имя</td>
                            <td>Статус</td>
                            <td><a href="#" class="removeUniqName">Удалить</a></td>
                        </tr>
                    </table>
                </div>
                <div><a href="/setting/uniqname" target="_blank">Зарегистрировать уникальное имя</a></div>
            </div>

        </div>
        <input type="submit" value="Сохранить" class="btn-edit" style="transition:unset; margin:0;">
    </form>

    
</div>





@endsection


