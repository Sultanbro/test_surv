@extends('layouts.app')

@section('content')

<div class="payment">
    <div class="payment-content">
        <div class="payment-box" onclick="window.location.href='/setting/invoice'">
            <div class="payment-box-holder">
                <img src="/static/images/ico-checklist.png" alt="ico">
                <strong>Получить счёт на оплату</strong>
            </div>

            <ul class="payment-ico">
                <li><a href="#"><span><img src="/static/images/ico-20.png" alt="ico"></span></a></li>
                <li><a href="#"><span><img src="/static/images/ico-21.png" alt="ico"></span></a></li>
                <li><a href="#"><span><img src="/static/images/qiwi2.png" alt="ico"></span></a></li>
            </ul>
        </div>
        <div class="payment-box" onclick="window.location.href='/setting/card'">
            <div class="payment-box-holder">
                <img src="/static/images/ico-payment.png" alt="ico">
                <strong>Оплата картой</strong>
            </div>
            <ul class="payment-ico">
                <li><a href="#"><span><img title="Kaspi" src="/static/images/kaspi.png" alt="ico"></span></a></li>
                <li><a href="#"><span><img src="/static/images/ico-22.png" alt="ico"></span></a></li>
                <li><a href="#"><span><img src="/static/images/ico-23.png" alt="ico"></span></a></li>
  <li><a href="#"><span><img title="Мир" src="/static/images/mir.png" alt="ico"></span></a></li>

            </ul>
        </div>
        <div class="payment-box kassa24" onclick="window.location.href='/setting/kassa24'">
            <div class="payment-box-holder">
                <img src="/static/images/ico-payment.png" alt="ico">
                <strong style="line-height: 18px;
    margin-bottom: 5px;">Оплата банковской картой</strong>
            </div>
            <ul class="payment-ico">


                <li><a href="#"><span><img title="Webmoney" style="width: 100%;" src="/static/images/webmoney.png" alt="ico"></span></a>
<a style="    margin: 0 auto;" href="#"><span><img src="/static/images/ico-22.png" alt="ico"></span></a>
                </li>
                <li><a href="#"><span><img title="Сбербанк" src="/static/images/sber.png" alt="ico"></span></a>
<a href="#"><span><img src="/static/images/ico-23.png" alt="ico"></span></a>
                </li>


            </ul>


        </div>
    </div>
</div>


<style>
.kassa24{
  padding: 47px 50px 5px 0px!important;
position: relative;
}
</style>
@endsection
