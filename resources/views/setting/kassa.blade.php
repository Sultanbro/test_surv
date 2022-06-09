@extends('layouts.app')

@section('content')
<div class="payment">
    <form class="form-payment" action="{{route('kassa')}}" method="post">
        {{ csrf_field() }}
        <div class="form-payment-row">
            <div class="form-payment-rowbox">
                <input type="text" placeholder="Телефон" name="phone" required="">
            </div>
            <div class="form-payment-rowbox">
                <input type="text" name="amount" required="" placeholder="Сумма пополнения">
            </div>
        </div>
        <input type="submit" value="Оплатить" class="btn-form-payment">
    </form>
</div>
@endsection