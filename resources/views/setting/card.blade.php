@extends('layouts.app')

@section('content')

<div class="payment" id="progress_cnt">
    <form class="form-payment wallet" method="post">
        <div class="form-payment-row">
            <div class="form-payment-rowbox">
                <input type="text" placeholder="Телефон" name="phone" required="">
            </div>
            <div class="form-payment-rowbox">
                <input type="text" name="amount" required="" placeholder="Сумма оплаты">
            </div>
        </div>
        <input type="submit" value="Оплатить" class="btn-form-payment">
    </form>
</div>
@endsection
