


<div class="bgpanel"></div>
<div class="panel">
    <div class="tail"></div>
    <div class="panel_head">
        <div class="panel_in active" data-tab="1">Уведомления @if($unread > 0){{ $unread }}@endif</div>
        <div class="panel_in " data-tab="2">Уведомления прочитанные</div>
    </div>
    <div class="panel_body">
        <div class="panel_out active" data-id="1">
            <div class="notification_list">
                @foreach ($unread_notifications as $item)
                    <div class="notification_item unread_noti" id="unnoti{{$item->id}}">
                        <div class="notifi_top">
                            <!-- <div class="label-wrapper {{$item->type=='important'?'':'hidden'}}"><span
                                        class="label-wrapper_text">{{$item->type=='important'?'ВАЖНОЕ':''}}</span>
                            </div>  -->
                            <span class="notification-date">{{$item->formattedDate()}}</span>
                            <!-- <span class="notification-projectId">{{$item->formattedDate()}}</span> -->
                        </div>
                        <div class="notification-title">{{$item->title}}</div>
                        <div class="notification-text">
                            {!!$item->message!!}
                        </div>

                        
                        <a class="set-read" data-id="{{ $item->id }}" 
                            @if($item->title == 'Пропал с обучения: 1 день' || $item->title == 'Пропал с обучения: 2 день') data-comment="1"  @else data-comment="0" @endif
                            @if($item->title == 'Заполните отчет') data-type="report"  @else data-type="empty" @endif>
                            <div class="notification-change"><i class="fa fa-check"></i></div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="panel_out " data-id="2">
            <div class="notification_list">
                @foreach ($read_notifications as $item)
                    <div class="notification_item read_noti" >
                        <div class="notifi_top" style="display:flex;justify-content:space-between">
                            <!-- <div class="label-wrapper {{$item->type=='important'?'':'hidden'}}">
                                <span class="label-wrapper_text">{{$item->type=='important'?'ВАЖНОЕ':''}}</span>
                            </div> -->
                            <span class="notification-date">{{$item->formattedDate()}}</span>
                            <span class="notification-projectId">{{ $item->read_at }}</span>
                        </div>
                        <div class="notification-title">{{$item->title}}</div>
                        <div class="notification-text">
                            {!!$item->message!!}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <div class="panel_foot">
        <a id="setRead">
            <button><i class="fa fa-check"></i>Отметить все, как прочитанные</button>
        </a>
    </div>
</div>





<div class="modal" id="setReadReportModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Заполните отчет</h6>
                <p>Заполните отчет о том, какую необычно полезную работу Вы сделали на этой неделе.</p>
            </div>
            <div class="text-center mb-3 col-md-12">
                <input class="form-control" type="hidden" id="modalReport_noti_id">
                
                <textarea name="text" id="modalReport_text" class="form-control form-control-sm mb-2" placeholder="Минимум 200 символов" onkeyup="count_char()"></textarea>
                <span class="char_counter">0 / 200</span>
                <button type="submit" class="btn btn-success btn-sm rounded" id="saveReport">Сохранить</button>
                <button type="reset" class="btn btn-primary btn-sm rounded hidemodals">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="setReadCommentModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Напишите причину отсутствия</h6>
            </div>
            <div class="text-center mb-3 col-md-12">
                <input class="form-control" type="hidden" id="idForNotification">
              
                <select id="selectForNotification" class="form-control form-control-sm mb-2">
                    <option value="" selected disabled>Выберите причину</option>
                    <option value="Отсутствовал, предупредил руководителя">Отсутствовал, предупредил руководителя</option>
                    <option value="Был полностью на обучении">Был полностью на обучении</option>
                    <option value="Был на основной работе">Был на основной работе</option>
                    <option value="Бросает трубку">Бросает трубку</option>
                    <option value="Вышел (-ла) из группы">Вышел (-ла) из группы</option>
                    <option value="Думал обман, будет обучаться">Думал обман, будет обучаться</option>
                    <option value="Забыл (-а), после обеда присутствует">Забыл (-а), после обеда присутствует</option>
                    <option value="Заболел (-а)">Заболел (-а)</option>
                    <option value="Нашел другую работу">Нашел другую работу</option>
                    <option value="Не понравились условия оплаты труда">Не понравились условия оплаты труда</option>
                    <option value="Не интересно, покинул обучение">Не интересно, покинул обучение</option>
                    <option value="Не хочет работать 6 дней">Не хочет работать 6 дней</option>
                    <option value="Не сдал экзамен">Не сдал экзамен</option>
                    <option value="Не хочет долго стажироваться">Не хочет долго стажироваться</option>
                    <option value="Не выходит на связь">Не выходит на связь</option>
                    <option value="Не смог подключиться">Не смог подключиться</option>
                    <option value="Не был на обучении / стажировке">Не был на обучении / стажировке</option>
                    <option value="Не дозвонились с 2-х раз">Не дозвонились с 2-х раз</option>
                    <option value="Нет компьютера">Нет компьютера</option>
                    <option value="Не было света">Не было света</option>
                    <option value="Не было интернета">Не было интернета</option>
                    <option value="Не добавили в группы обучения">Не добавили в группы обучения</option>
                    <option value="Не смог пройти по ссылке на обучение">Не смог пройти по ссылке на обучение</option>
                    <option value="Отсутствовал(а) более 3 дней">Отсутствовал(а) более 3 дней</option>
                    <option value="Ребенок заболел, не сможет совмещать">Ребенок заболел, не сможет совмещать</option>
                    <option value="НУдалился (-ась), не актуально">Удалился (-ась), не актуально</option>
                </select>
                
    
                
                <button type="submit" class="btn btn-success btn-sm rounded" id="setReadWithComment">Сохранить</button>
                <button type="reset" class="btn btn-primary btn-sm rounded hidemodals">Отмена</button>
            </div>
        </div>
    </div>
</div>




<div class="modal" id="transferTrainingModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h6>Выберите дату и время для переноса обучения</h6>
            </div>
            <div class="text-center mb-3 col-md-12">
                <input class="form-control" type="hidden" id="userIdForTransfer">

                <div class="d-flex mb-2">
                    <input class="form-control mr-2 col-8" type="date" id="dateForTransfer">
                    <input class="form-control col-4" type="time" id="timeForTransfer" value="09:30">
                </div>  

                <button type="submit" class="btn btn-success rounded" id="transferTraining">Перенести</button>
                <button type="reset" class="btn btn-primary rounded hidemodals">Отмена</button>
            </div>
        </div>
    </div>
</div>

