@extends('layouts.admin')

@section('content')
<tr>
  <td>{{$user->ID}}</td>
  <td>{{$user->EMAIL}}</td>
  <td>{{$user->NAME}}</td>
  <td>{{$user->UF_ADMIN}}</td>




</tr>





<form method="post">
    {{ csrf_field() }}
    <div class="form-group">
      <label> Доступ в админ панель <input  class=""@if( $user->UF_ADMIN =='1') checked="checked" @endif type="checkbox" class=""   name="admin" /></label>

      <label> СМС рекламные <input @if( $user->roles['page1'] =='on') checked="checked" @endif type="checkbox" class=""   name="str1" /></label>
      <label> СМС интеграции <input @if( $user->roles['page2'] =='on') checked="checked" @endif type="checkbox" class="" name="str2" /></label>
      <label> СМС пользователи <input @if( $user->roles['page3'] =='on') checked="checked" @endif type="checkbox" class="" name="str3" /></label>
      <label> Отчет по шлюзам(старый) <input @if( $user->roles['page4'] =='on') checked="checked" @endif type="checkbox" class="" name="str4" /></label>
      <label> Отчет по SIP <input @if( $user->roles['page5'] =='on') checked="checked" @endif type="checkbox" class="" name="str5" /></label>
      <label> Балансы пользователей <input @if( $user->roles['page6'] =='on') checked="checked" @endif type="checkbox" class="" name="str6" /></label>
      <label> Бонусы пользователей <input @if( $user->roles['page7'] =='on') checked="checked" @endif  type="checkbox" class="" name="str7" /></label>
      <label> Лимит линий <input @if( $user->roles['page8'] =='on') checked="checked" @endif type="checkbox" class="" name="str8" /></label>
      <label> Цены для СМС <input @if( $user->roles['page9'] =='on') checked="checked" @endif type="checkbox" class="" name="str9" /></label>
      <label> Цены для Автозвонков <input @if( $user->roles['page10'] =='on') checked="checked" @endif type="checkbox" class="" name="str10" /></label>
      <label> Почистить валидатор <input @if( $user->roles['page11'] =='on') checked="checked" @endif type="checkbox" class="" name="str11" /></label>

      <label> Новости bpartners <input @if( $user->roles['page12'] =='on') checked="checked" @endif type="checkbox" class="" name="str12" /></label>
      <label> Новости umarketing <input @if( $user->roles['page13'] =='on') checked="checked" @endif type="checkbox" class="" name="str13" /></label>

      <label> Партнеры <input @if( $user->roles['page14'] =='on') checked="checked" @endif type="checkbox" class="" name="str14" /></label>
      <label> Счета на оплату Партнеры <input @if( $user->roles['page15'] =='on') checked="checked" @endif type="checkbox" class="" name="str15" /></label>
      <label> Уведомления <input @if( $user->roles['page16'] =='on') checked="checked" @endif type="checkbox" class="" name="str16" /></label>
      <label> Кредиты <input @if(isset($user->roles['page17']) && $user->roles['page17'] =='on') checked="checked" @endif type="checkbox" class="" name="str17" /></label>
      <label> Аренда номеров <input @if(isset($user->roles['page18']) && $user->roles['page18'] =='on') checked="checked" @endif type="checkbox" class="" name="str18" /></label>
      <label> Платежи <input @if(isset($user->roles['page19']) && $user->roles['page19'] =='on') checked="checked" @endif type="checkbox" class="" name="str19" /></label>



        <label> Учет времени (Табель, Аналитика, Начисления, Экзамены, ОКК) <input @if(isset($user->roles['page21']) && $user->roles['page21'] =='on') checked="checked" @endif type="checkbox" class="" name="str21" /></label>
        <label> Учет времени (Настройки) <input @if(isset($user->roles['page22']) && $user->roles['page22'] =='on') checked="checked" @endif type="checkbox" class="" name="str22" /></label>
        <label> Учет времени (Только сотрудники) <input @if(isset($user->roles['persons']) && $user->roles['persons'] =='on') checked="checked" @endif type="checkbox" class="" name="persons" /></label>

        <label> Показать книги? <input @if(isset($user->roles['page20']) && $user->roles['page20'] =='on') checked="checked" @endif type="checkbox" class="" name="str20" /></label>


        <label> Книги <input @if($user->books != NULL) checked @endif type="checkbox" class="" name="books" /></label>
      <div class="books_block" style="display: none;
                                      position: relative;
                                      background: #e2e2e2;
                                      border: 0 solid #ccc;
                                      font-size: 12px;
                                      line-height: 20px;
                                      padding: 0 20px;
                                      min-width: 170px;
                                      max-width: 170px;">
          
          @foreach ($books as $book)
          @php 
            $books_ids = explode(',', $user->books);
            
            if (in_array($book->id, $books_ids)) {
                $name = $book->name;
            }
          @endphp
          <div class="book">
              <input type="checkbox" @if(isset($name) && $name == $book->name) checked @endif class="book_input" value="{{ $book->id }}">
              <span class="book_name">{{$book->name}}</span>
          </div>
          @endforeach  
      </div>
      <input type="hidden" name="books_ids" value="{{ $user->books }}">
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa fa-dot-circle-o"></i> Сохранить
        </button>
    </div>
</form>




@endsection

@section('styles')
    <style>
      .form-group label {
        display: block;
        clear: both;
        margin-bottom: 10px;
      }
    </style>
@endsection