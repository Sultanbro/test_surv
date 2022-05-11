@extends('layouts.app')

@section('content')

<div id="wrapper">

        @include('menu.index')

        <div id="main">
        @include('includes.panel')


            <div class="firstscreen">
                <div class="firstscreen-holder">
                    <div class="firstscreen-image">
                        <strong>Как работать с сервисом?
                          <br>Запустить презентацию</strong>
                    </div>
                    <div class="firstscreen-textbox">
                        <strong>Поздравляем с регистрацией на нашем сервисе!</strong>
                        <p>Для рассылки рекомендуем Вам сразу добавить подпись отправителя (альфа-нумерическое имя), которую клиент будет видеть при получении сообщения. Имя отправителя согласуется у операторов, активация занимает некоторое время.</p>
                        <p>Следующим правильным шагом будет добавление группы или групп номеров для рассылок, тогда Вам будет понятно сколько sms необходимо приобрести.</p>
                        <p>Для оплаты доставленных сообщений рекомендуем заключить договор о предоставлении услуг. До заключения договора производится оплата отправленных сообщений.</p>
                        <p>Поддержка клиентов работает круглосуточно и без выходных. <br>Желаем Вам приятной работы и выгодных рассылок с нашим сервисом!</p>
                    </div>
                </div>
                <div class="firstscreen-news">
                    <strong>Новости сервиса</strong>
                    <ul>
                        <li>

                            <img src="/images/img-6.jpg" alt="image">

                            <div>
                                <strong>Заголовок новости в одну или две строки</strong>
                                <em>16 мая 2017</em>
                            </div>
                        </li>
                        <li>

                            <img src="/images/img-7.jpg" alt="image">

                            <div>
                                <strong>Заголовок новости в одну или две строки</strong>
                                <em>16 мая 2017</em>
                            </div>
                        </li>
                        <li>

                            <img src="/images/img-8.jpg" alt="image">

                            <div>
                                <strong>Заголовок новости в одну или две строки</strong>
                                <em>16 мая 2017</em>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


@endsection
