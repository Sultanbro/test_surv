@extends('layouts.app')

@section('content')

<style>
    tr.detail-row {
        background-color: #e394a745;
    }
    .details-button {
        cursor: pointer;
    }
    .transaktion_list {
        width: 100%;
        max-width: none;
    }
</style>

<div class="sub-balsnse">
    <h1>Вот как Вы можете использовать Ваш баланс:</h1>
    <div class="tab-row fade-tabset">
        <ul class="tabset-1">
            <li>
                <a href="#" onclick="return false;">
                    <div>
                        <strong class="heading">SMS</strong>
                        <span><b><i class="item-15">item</i></b></span>
                        <strong class="col">Осталось:</strong>
  @if(auth()->user()->currency=='kzt')
  <strong class="col">на Казахстан: <em>{{floor(\App\User::balance()/9)}} смс</em></strong>
    <strong class="col">на РФ: <em>{{floor(\App\User::balance()/15)}} смс</em></strong>

  @else
      <strong class="col">на РФ: <em>{{floor(\App\User::balance()/3)}} смс</em></strong>
  <strong class="col">на Казахстан: <em>{{floor(\App\User::balance()/1.5)}} смс</em></strong>


  @endif

                      </div>
                </a>
            </li>
            <li>
                <a href="#" onclick="return false;">
                    <div>
                        <strong class="heading">Автозвонки</strong>
                        <span><b><i class="item-16">item</i></b></span>
                        <strong class="col">Осталось: </strong>


                        @if(auth()->user()->currency=='kzt')
                        <strong class="col">на Казахстан: <em>{{floor(\App\User::balance()/4.5)}}</em></strong>
                          <strong class="col">на РФ: <em>{{floor(\App\User::balance()/15)}}</em></strong>

                        @else
                          <strong class="col">на РФ: <em>{{floor(\App\User::balance()/2.5)}}</em></strong>
                        <strong class="col">на Казахстан: <em>{{floor(\App\User::balance()/0.75)}}</em></strong>

                      @endif
                        </div>
                </a>
            </li>

            <li>
                <a href="#" onclick="return false;">
                    <div>
                        <strong class="heading">U-Calls</strong>
                        <span><b><i class="item-18"></i></b></span>
                        <strong class="col">Осталось: </strong>

                        @if(auth()->user()->currency=='kzt')
                    <strong class="col"><em>{{floor(\App\User::balance()/0.8)}} минут</em></strong>
                        @else
                        <strong class="col"><em>{{floor(\App\User::balance()/0.13)}} минут</em></strong>
                    @endif


                    </div>
                </a>
            </li>

        </ul>
        <div id="tab-2" class="tab-1">
            <a href="/setting/payment" class="btn-balsnse">Пополнить баланс</a>
            <h2>Список транзакций</h2>
            <div class="tableTransactions">
            </div>
            <form class="time-box" action="/setting/transaction" method="post">
                {{ csrf_field() }}
                <strong style="font-size: 14px;">Временной интервал</strong>
                <div class="time-box-area">
                    <span>от:</span>
                    <div class="time-box-block">
                        <input type="text" class="form-control datepicker44" name="start_date" value="{{$start_date}}"/>
                    </div>
                    <span>до:</span>
                    <div class="time-box-block">
                        <input type="text" class="form-control datepicker55" name="end_date" value="{{$end_date}}"/>
                    </div>
                    <div class="time-box-block" style="width:180px;">
                        <select name="product" class="form-control">
                            <option value="all" selected>Все</option>
                            <option value="sip">Sip интеграция</option>
                            <option value="sms">Смс рассылка</option>
                            <option value="sms_integration">Смс по api</option>
                            <option value="smpp">SMPP интеграция</option>
                            <option value="voice_autocall">Автозвонки</option>
                            <option value="voice_integration">Автозвонки по api</option>
                            <option value="robot">Роботы</option>
                            <option value="callibro">U-Calls</option>
                            <!-- <option value="rent_number">Аренда номеров</option> -->
                            <option value="rent_monthly">Плата за номер</option>

                        </select>
                    </div>
                    <input type="submit" name="filter" value="Применить" class="btn-time-box">
                </div>
            </form>

            <div class="transaktion_list">
                <table id="transaktion_list_table">
                    <thead>
                        <tr>
                            <th></th>
                            <th><strong><em>Дата</em> <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span></strong></th>
                            <th><strong><em title="Баланс на начало дня">Баланс</em> <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span></strong></th>
                            <th><strong><em>Расход</em> <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span></strong></th>
                            <th><strong><em>Кол-во</em> <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span></strong></th>
                            <th><strong><em>Услуга</em> <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span></strong></th>
                            <th><strong><em>Приход</em> <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span></strong></th>
                            <th><strong><em>Комментарии</em> <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span></strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($total_expence != 0)
                        <tr>
                            <td>Итого:</td>
                            <td></td>
                            <td></td>
                            <td><em>{{ $total_expence  }} {{auth()->user()->currency=='kzt'?' ₸':'₽'}}</em></td>
                            <td></td>
                            <td></td>
                            <td><i>{{  $total_income  }} {{auth()->user()->currency=='kzt'?' ₸':'₽'}}</i></td>
                            <td></td>
                        </tr>
                        @endif
                        @foreach($transactions as $key => $transaction)
                        @php $key = strtotime($key) @endphp
                        <tr date="{{date('d-m-Y', $key)}}">
                            <td><img src="/static/images/plus.png" class="details-button"></td>
                            <td data-order="{{$key}}">{{date('d.m.Y', $key)}}</td>
                            <td>{{ $transaction['first_balance'] }} {{auth()->user()->currency=='kzt'?' ₸':'₽'}}</td>
                            <td><em>{{ $transaction['expense'] }} {{auth()->user()->currency=='kzt'?' ₸':'₽'}}</em></td>
                            <td></td>
                            <td></td>
                            <td><i>{{$transaction['income']}} {{auth()->user()->currency=='kzt'?' ₸':'₽'}}</i></td>
                            <td>{{$transaction['comment']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
