<div class="row">

    <div class="col-lg-12">

        <form id="second_form" method="post" action="/contact/import2">
            {{ csrf_field() }}
            <input type="hidden" name="group_id" value="{{$group_id}}">
            <input type="hidden" name="file_path" value="{{$file_path}}">
            <input type="hidden" name="file_name" value="{{$file_name}}">

            <span style="color: red" id="second-form-message"></span>
            
            <p class="title">Настройка соответствия полей</p>

            <div class="row">

                <table>
                    <thead>
                        <tr>
                            <th style="text-align: right; padding: 0 10px 0 0;">Поле в файле</th>
                            <th style="text-align: center;">Загрузить в поле</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($excel[0]))
                            @php $i = 0; @endphp
                            @foreach($excel[0] as $column=>$value)
                                <tr>
                                    <td><p class="contact-name">{{$value}}</p></td>
                                    <td>
                                        <select name="column_type[]" class="form-control form-control-sm">
                                            <option value="">Не использвать</option>
                                            @foreach($fields as $field => $title)
                                                <option value="{{$field.'_'.PHPExcel_Cell::stringFromColumnIndex($i)}}" class="@if($field == 'phone') is-contact-phone @endif" >{{$title}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        @endif
                    </tbody>
                </table>
                
                <div class="chek">
                <label class="control control-checkbox" style="    display: table;
    margin: 20px auto;">
                    <input type="checkbox" checked="checked" name="is_header" value="1">
                    <div class="control_indicator dialer_control_indicator2"></div>
                    <span>Первая строка содержит заголовки</span>
                </label>
            </div>
            </div>
            <div class="row">
                <input type="submit" class="yellow_btn contact-modal-yellow-btn " style=" display: table; margin: 0 auto;
   " value="Импортировать">
            </div>
        </form>

    </div>
</div>
