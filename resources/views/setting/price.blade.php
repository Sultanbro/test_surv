@extends('layouts.app')

@section('content')
<div id="progress_cnt"></div>

<div class="price" id="vib">


  <div class="contentprice" style="font-weight: bold;">

    <span>
      Понятные цены для пользователей
    </span>
    <div class="dropdown">
      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <span>{{auth()->user()->currency == 'kzt'?'в тенге':'в рублях'}}</span>
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item kazpricebutton">в тенге</a></li>
        <li><a class="dropdown-item ruspricebutton">в рублях</a></li>
      </div>
    </div>


  </div>


  <div class="price-holder blockprice">
    <div class="price-col price-col1">
      <h3>автозвонки</h3>

      <div class="kazprice" style="display:{{auth()->user()->currency=='kzt'?'block':'none'}}; height: 295px;">

        <div class="price-col1-box" style="margin: 20px 0;">
          <span style="font-size: 12px;margin-bottom: 0;">Стоимость одного уведомления</span>
        </div>
        <ul class="price-col1-red">
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Казахстан</strong> <span>4.5 <em>тг</em></span>
          </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Россия</strong> <span>15 <em>тг</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Украина</strong> <span>99 <em>тг</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Беларусь</strong> <span>99 <em>тг</em></span> </li>
        </ul>
        <div class="price-col1-box" style="margin-bottom: 0; margin-top: 15px;">
          <span style="font-size: 12px;margin-bottom: 0;">Цена на более 100 000 уведомлений расчитывается
            индивидуально</span>
        </div>


      </div>
      <div class="rusprice" style="display:{{auth()->user()->currency=='kzt'?'none':'block'}}; height: 295px;">
        <div class="price-col1-box" style="margin: 20px 0;">
          <span style="font-size: 12px;margin-bottom: 0;">Стоимость одного уведомления</span>
        </div>
        <ul class="price-col1-red">

          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Россия</strong> <span>2.5 <em>р</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Казахстан</strong> <span>0.75 <em>р</em></span>
          </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Украина</strong> <span>16.5 <em>р</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Беларусь</strong> <span>16.5 <em>р</em></span> </li>
        </ul>
        <div class="price-col1-box" style="margin-bottom: 0; margin-top: 15px;">
          <span style="font-size: 12px;margin-bottom: 0;">Цена на более 100 000 уведомлений расчитывается
            индивидуально</span>
        </div>

      </div>
      <div class="price-col1-holder">
        <strong>Возможность использования<br /> голосового меню</strong>
      </div>
    </div>
    <div class="price-col price-col2">
      <h3>смс</h3>

      <div class="kazprice" style="display:{{auth()->user()->currency=='kzt'?'block':'none'}}; height: 295px;">
        <div class="price-col1-box" style="margin: 20px 0;">
          <span style="font-size: 12px;margin-bottom: 0;">Стоимость одного смс</span>
        </div>
        <ul class="price-col1-red">
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Казахстан</strong> <span>14 <em>тг</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Россия</strong> <span>18 <em>тг</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Украина</strong> <span>27 <em>тг</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Беларусь</strong> <span>33 <em>тг</em></span> </li>
        </ul>




      </div>


      <div class="rusprice" style="display:{{auth()->user()->currency=='kzt'?'none':'block'}}; height: 295px;">


        <div class="price-col1-box" style="    margin: 20px 0;">
          <span style="font-size: 12px;margin-bottom: 0;">Стоимость одного смс</span>
        </div>
        <ul class="price-col1-red">
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Россия</strong> <span>3 <em>р</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Казахстан</strong> <span>1.7 <em>р</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Украина</strong> <span>4.5 <em>р</em></span> </li>
          <li style="margin: 0 0 8px;padding-bottom: 8px;"> <strong>Беларусь</strong> <span>5.5 <em>р</em></span> </li>
        </ul>

      </div>

      <div class="price-col1-holder">
        <strong>API и SMPP интеграция<br /> БЕСПЛАТНО</strong>
      </div>
    </div>
    <div class="price-col price-col3">
      <h3 style="line-height: normal;
      height: 60px;
      padding: 10px 0 0 0;     margin-bottom: 75px;">сервис обзвона<br />U-Calls</h3>


      <div class="kazprice" style="display:{{auth()->user()->currency=='kzt'?'block':'none'}};">
        <ul>
          <li> <strong>1 <em> минута </em></strong> <span>0.80 <em>тиын</em></span> </li>

        </ul>

      </div>
      <div class="rusprice" style="display:{{auth()->user()->currency=='kzt'?'none':'block'}};">
        <ul>
          <li> <strong>1 <em> минута </em></strong> <span>0.13 <em>копеек</em></span> </li>

        </ul>

      </div>

      <div class="price-col1-box" style="margin-bottom: 0;     padding-bottom: 82px;">
        <strong>1 минута успешного соединения<br /> и больше никаких дополнительных оплат!</strong>
      </div>


      <div class="price-col1-holder">
        <strong>SIP, API, SMPP, SMTP<br /> интеграции </strong>
      </div>
    </div>
  </div>



</div>

<style>
  .price {
    padding: 62px 0 110px;
    width: 100%;
    overflow: hidden;
    background: #fcfcfc;
    text-align: center
  }

  .price h2 {
    margin: 0 0 65px;
    font-size: 28px;
    line-height: 30px;
    font-family: 'Exo 2', sans-serif;
    letter-spacing: .6px;
    color: #000
  }

  .price-holder {
    margin: 0 auto 43px;
    padding: 0 10px;
    box-sizing: border-box;
    max-width: 760px;
    font-size: 0
  }

  .price-holder:after {
    display: block;
    clear: both;
    content: ""
  }

  .price-col {
    display: inline-block;
    vertical-align: top;
    margin: 0;
    width: 246px;
    box-sizing: border-box;
    background: #faf5f3;
    box-shadow: 2px 2px 20px rgba(0, 0, 0, 0.4)
  }

  .price-col:hover {
    margin: -24px 0 -22px;
    padding: 52px 0 22px;
    z-index: 5
  }

  .price-col1 {
    padding: 28px 0 0;
    position: relative;
    z-index: 5
  }

  .price-col1 h3 {
    margin: 0 0 5px;
    background: #fda085;
    font-size: 18px;
    line-height: 60px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #fff;
    position: relative;
    z-index: 6
  }


  .price-col1-box {
    margin: 0 0 20px;
    padding: 0 10px
  }

  .price-col1-box span {
    display: block;
    margin: 0 0 10px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 1px;
    color: #000
  }

  .price-col1-box strong {
    display: block;
    margin: 0 0 10px;
    font-size: 16px;
    line-height: 20px;
    font-weight: bold;
    letter-spacing: 1px;
    color: #000
  }

  .price-col1-block {
    margin: 0 10px 16px;
    padding: 0 0 17px;
    box-sizing: border-box;
    overflow: hidden;
    border-bottom: 1px dashed #c3c2c1
  }

  .price-col1-block strong {
    float: left;
    box-sizing: border-box;
    padding: 0 5px;
    font-size: 20px;
    line-height: 22px;
    font-family: 'Lato', sans-serif;
    font-weight: 900;
    text-align: center;
    letter-spacing: 1px;
    width: 50%
  }

  .price-col1-block strong span {
    display: block;
    font-size: 11px;
    line-height: 12px;
    font-weight: normal;
    letter-spacing: 1px;
    color: #7c7c7c
  }

  .price-col1-block strong em {
    font-size: 14px
  }

  .price-col1-area {
    margin: 0 0 16px;
    padding: 0 10px
  }

  .price-col1-area strong {
    display: block;
    margin: 0 0 15px;
    font-size: 15px;
    line-height: 18px;
    letter-spacing: 1px;
    color: #7c7c7c
  }

  .price-col1-area span {
    display: block;
    margin: 0 0 12px;
    padding: 0 5px;
    font-size: 20px;
    line-height: 22px;
    font-family: 'Lato', sans-serif;
    font-weight: 900;
    text-align: center;
    letter-spacing: 1px
  }

  .price-col1-area span i {
    font-size: 14px
  }

  .price-col1-area em {
    display: block;
    margin: 0 0 10px;
    font-size: 11px;
    line-height: 13px;
    letter-spacing: 1px;
    color: #7c7c7c
  }

  .price-col1-holder {
    border-top: 1px solid #ff4c17;
    padding: 13px 10px 15px;
    height: 80px
  }

  .price-col1-holder strong {
    display: block;
    font-size: 14px;
    line-height: 17px;
    color: #ff4c17;
    letter-spacing: 1px
  }

  .price-col2 {
    position: relative;
    padding: 28px 0 0;
    z-index: 2;
    background: #f2f8f9
  }

  .price-col3 {
    position: relative;
    padding: 28px 0 0;
    z-index: 2;
    background: #f2e8fe
  }

  .price-col2 h3 {
    margin: 0 0 5px;
    background: #79d5f0;
    font-size: 18px;
    line-height: 60px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #fff;
    position: relative;
    z-index: 6
  }

  .price-col1 ul {
    margin: 0 10px;
    overflow: hidden
  }

  .price-col2 ul,
  .price-col3 ul {
    margin: 0 25px -1px;
    overflow: hidden
  }

  .price-col1 ul li,
  .price-col2 ul li,
  .price-col3 ul li {
    margin: 0 0 23px;
    padding: 0 0 20px;
    width: 100%;
    overflow: hidden;
    border-bottom: 1px dashed #c1c3c3
  }

  .price-col2 ul li {
    margin: 0 0 8px;
    padding: 0 0 8px
  }

  .price-col1 ul li {
    margin: 0 0 16px
  }

  .price-col1 ul li:last-child,
  .price-col2 ul li:last-child,
  .price-col3 ul li:last-child {
    border: 0
  }

  .price-col1 ul li strong,
  .price-col2 ul li strong,
  .price-col3 ul li strong {
    float: left;
    margin: 0 5px 0 0;
    font-size: 15px;
    line-height: 22px;
    font-family: 'Lato', sans-serif;
    font-weight: 900;
    letter-spacing: .6px
  }

  .price-col1 ul li strong em,
  .price-col2 ul li strong em,
  .price-col3 ul li strong em {
    font-size: 13px;
    font-weight: normal
  }

  .price-col1 ul li span,
  .price-col2 ul li span,
  .price-col3 ul li span {
    float: right;
    margin: 0 5px 0 0;
    font-size: 20px;
    line-height: 22px;
    font-family: 'Lato', sans-serif;
    font-weight: 900;
    letter-spacing: 1px
  }

  .price-col1 ul li span em,
  .price-col2 ul li span em,
  .price-col3 ul li span em {
    font-size: 13px;
    font-weight: normal
  }

  .price-col2-holder {
    border-top: 1px solid #00b2e8;
    padding: 13px 10px 15px;
    height: 80px
  }

  .price-col2-holder span {
    display: block;
    font-size: 14px;
    line-height: 17px;
    color: #53b6e8;
    letter-spacing: 1px
  }

  .price-col3 h3 {
    margin: 0 0 15px;
    background: #b88de9;
    font-size: 18px;
    line-height: 60px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #fff;
    position: relative;
    z-index: 6
  }


  .price-col3-holder {
    border-top: 1px solid #7e32d5;
    padding: 13px 10px 15px;
    height: 80px
  }

  .price-col3-holder span {
    display: block;
    font-size: 14px;
    line-height: 17px;
    color: #9d63df;
    letter-spacing: 1px
  }

  .contentprice {
    display: table;
    margin: 0 auto;
    margin-bottom: 30px;
    font-size: 16px;
  }

  .contentprice>span {
    display: block;
    float: left;
    padding: 5px 10px 0 0;
  }

  .contentprice .dropdown {
    float: left;
  }

  .contentprice button {
    position: relative;
    padding: 5px 40px 5px 10px;
    margin: 0 auto;
    background: #9bc7de;
    color: black;
    outline: none;
    cursor: pointer;
    font-weight: bold;
    border: 0;
    text-align: left;
  }

  .contentprice button:after {
    content: "\f0da";
    display: inline-block;
    font: normal normal normal 14px/1 FontAwesome;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    width: 30px;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    text-align: center;
    line-height: 30px;
    font-size: 20px;
  }

  .contentprice .dropdown-menu {
    margin: 0;
    border: 0;
    border-radius: 0;
  }

  .price .dropdown-item {
    cursor: pointer;
  }

  #main #content strong {
    font-weight: bold;
  }

  .dropdown-menu>li>a {
    display: block;
    padding: 3px 20px;
    clear: both;
    font-weight: normal;
    line-height: 1.42857143;
    color: #333333;
    white-space: nowrap;
  }
</style>
@endsection