@extends('layouts.app')

@section('content')

<div class="payment">
    <form class="form-payment" action="" method="POST" target="_blank">
        {{ csrf_field() }}
        <div class="message"></div>
        <strong>Заполните реквизиты</strong>
        <div class="col1">
            <label>Услуга</label>
            <select name="service" class="form-control">
                <option value="Автозвонки">Автозвонки</option>
                <option value="СМС Рассылка">СМС Рассылка</option>
                <option value="Услуги обзвона">Услуги обзвона</option>
            </select>
        </div>
        <br>
        <div class="col1">
            <label>Счет для</label>
            <select name="country" class="form-control">
                <option value="kz">Для юридического лица Республики Казахстан</option>
                <option value="ru">Для юридического лица Российской Федерации</option>
            </select>
        </div>
        <br>
        <input type="text" name="name" required="" placeholder="Юридическое наименование организации">
        <div class="form-payment-row">
            <div class="form-payment-rowbox">
                <input type="text" name="phone" required="" placeholder="Телефон">
            </div>
            <div class="form-payment-rowbox">
                <input type="number" name="amount" required="" placeholder="Сумма оплаты">
            </div>
        </div>
        <input type="submit" value="Получить счёт на оплату" class="btn-form-payment">
    </form>
</div>
@endsection
