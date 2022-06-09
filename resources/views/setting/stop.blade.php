@extends('layouts.app')

@section('content')



    <div class="container-fluid" style="height: 100%;">
        <div class="row no-gutters">
            <div class="dispatch-panel">

                <div class="forwardsButton " style="width: auto">
                    <a href="#" class="btn-createtwo" data-toggle="modal" data-target="#stoplistmodal">Добавить номер в стоп лист</a>
                </div>


                <form class="sub-search" action="#">
                    <input type="submit" value="" class="btn-search">
                    <div>
                        <input class="stopsearch" id="inputstoplist" data-datatableid="stoplist" type="text" placeholder="Поиск">
                    </div>
                </form>

            </div>
        </div>

        <div class="row no-gutters" style="height: 100%;">
            <div class="contacts-holder" id="progress_cnt">


                <div class="stoplist_wrap">
                    <table id="stoplist" width="100%" data-url="setting/stop_list">
                            <thead>
                            <th style="width:4%;"></th>
                            <th style="width:32%;">
                                <strong style="justify-content: center;">
                                    <em>Телефон</em>
                                    <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span>
                                </strong>
                            </th>
                            <th style="width:32%;">
                                <strong style="justify-content: center;">
                                    <em>Дата отписки</em>
                                    <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span>
                                </strong>
                            </th>
                            <th style="width:32%;">
                                <strong style="justify-content: center;">
                                    <em>Источник отписки</em>
                                    <span><a class="top" href="#">top</a> <a href="#" class="bot">bot</a></span>
                                </strong>
                            </th>
                            </thead>
                            <tbody>
                                @foreach ($normalize_stoplist as $row)
                                <tr>
                                    <td style="width:4%;text-align: center;"></td>
                                    <td style="width:32%;text-align: center;">
                                        {{$row->number}}
                                    </td>
                                    <td style="width:32%;text-align: center;">
                                        {{$row->date}}
                                    </td>
                                    <td style="width:32%;text-align: center;">
                                        {{$row->type}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="stoplistmodal" tabindex="-1" role="dialog" aria-labelledby="stoplistmodal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Добавить в стоп лист</h4>
                            </div>
                            <div class="modal-body">
                                ...
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





@endsection
