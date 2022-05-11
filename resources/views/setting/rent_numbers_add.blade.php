@extends('layouts.app')

@section('content')
<div id="progress_cnt"></div>

@isset($alert)
    <div class="alert alert-success">
        {{ $alert }}
    </div>
@endisset

<div class="payment">


  <form class="form-payment" action="https://cp.u-marketing.org/setting/rent_numbers_add"  method="POST">
    {{ csrf_field() }}
    <div class="formContent">
      <strong>Подключить телефоный номер</strong>
      <select name="strana" required>
        <option>номер Республики Казахстан</option>
        <option>номер Российской Федерации</option>
      </select>
      <div class="form-payment-row">
        <div class="form-payment-rowbox">
          <input type="text" name="name" required placeholder="Имя заказчика">
        </div>
        <div class="form-payment-rowbox">
          <input type="text" name="phone" required placeholder="Телефон">
        </div>
      </div>
      <input type="submit" value="Заказать номер телефона" class="btn-form-payment">
    </div>
  </form>

</div>
<style>
.formContent select{
  display: block;
  margin: 0 0 10px;
  box-sizing: border-box;
  background: #e2e2e2;
  border: none;
  border-radius: 3px;
  width: 100%;
  font: 17px/30px 'Open Sans', Arial, Helvetica, sans-serif;
  color: #202226;
  font-size: 13px;
  padding: 5px 16px;
  height: 32px;
}
</style>
@endsection
