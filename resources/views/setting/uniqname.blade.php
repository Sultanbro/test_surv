@extends('layouts.app')

@section('content')

@isset($alert)
    <div class="alert alert-success">
        {{ $alert }}
    </div>
@endisset
    
<div class="payment">
		<form class="form-payment registrationUniqName" action="https://cp.u-marketing.org/setting/uniqname" method="POST">
		{{ csrf_field() }}
			<div class="message">
			</div>
      <div class="formContent">
  			<strong>Зарегистрировать уникальное имя</strong>
  			<input type="text" name="company" required placeholder="Юридическое наименование организации">
  			<div class="form-payment-row">
          <div class="form-payment-rowbox">
            <input type="text" name="name" required placeholder="Уникальное имя">
          </div>
  				<div class="form-payment-rowbox">
  					<input type="text" name="phone" required placeholder="Телефон">
  				</div>
  			</div>
  			<input type="submit" value="Зарегистрировать" class="btn-form-payment">
      </div>
		</form>
</div>

@endsection
