@extends('layouts.apperror')

@section('content')


<div class="firstscreen" style="background: url(/static/images/error_500.png) center center no-repeat;background-position-x: 82%;background-position-y: top;">
  <div class="firstscreen-holder">
    <div class="firstscreen-textbox">
      <strong>500. Вы видите эту страницу, т.к. на сервере произошла внутренняя ошибка. </strong>
      <strong>
		Возможно, мы обновляем программное обеспечение или проводим профилактические работы. 
		В любом случае делаем что-то очень важное и нужное. 
		Просим извинить за временные неудобства и просим попробовать еще раз позднее. </strong>
    <p></p>
    </div>
  </div>
</div>
<style type="text/css">
	#main #content .firstscreen:before
	{
		display: none;
	}
	#main #content .firstscreen .firstscreen-holder .firstscreen-textbox strong 
	{
	    width: 70%;
	    display: block;
	    font-size: 17px;
	    margin: 0 0 15px;
	    line-height: 30px;
	    color: #61bfe9;
	}
</style>
@endsection