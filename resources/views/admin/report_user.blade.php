@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="row">
            <form action="" method="get">
                <div class="col-lg-3">
                    <div class="row form-group">
                        <div class="col-lg-4">
                            <label for="select" class=" form-control-label">Год</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="year" class="form-control">
                                @for ($y = 2018; $y <= (int)date('Y'); $y++)
                                    <option {{$year == $y?'selected':''}} value="{{$y}}">{{$y}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row form-group">
                        <div class="col-lg-6">
                            <label for="select" class=" form-control-label">Месяц</label>
                        </div>
                        <div class="col-lg-6">
                            <select name="month" class="form-control">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option {{$month == $m?'selected':''}} value="{{$m}}">{{$m}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> применить
                    </button>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="report-table-new"  class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2">-</th>
                            <th colspan="4">TOTAL</th>
                            @for ($i = 1; $i <= $show_days; $i++)
                                <th colspan="4">{{$i}}</th>
                            @endfor
                        </tr>
                        <tr>
                            <th style="background: url(/static/images/beeline.png) no-repeat; background-size: 90%" height="20px"></th>
                            <th style="background: url(/static/images/kcell.png) no-repeat; background-size: 90%"></th>
                            <th style="background: url(/static/images/tele2.png) no-repeat; background-size: 90%"></th>
                            <th style="background: url(/static/images/altel.png) no-repeat; background-size: 90%"></th>

                            @for ($i = 1; $i <= $show_days; $i++)
                                <th style="background: url(/static/images/beeline.png) no-repeat; background-size: 90%;  border-left-width: 5px;" height="20px"></th>
                                <th style="background: url(/static/images/kcell.png) no-repeat; background-size: 90%"></th>
                                <th style="background: url(/static/images/tele2.png) no-repeat; background-size: 90%"></th>
                                <th style="background: url(/static/images/altel.png) no-repeat; background-size: 90%"></th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($gates as $email=>$data)
                    <tr>
                        @if($email == 'TOTAL')
                            <td>{{$email}}</td>
                            <td style="background: yellow; border-left-width: 5px;" data-order="{{$data[\App\SmsDailyReport::$beeline]+$data[\App\SmsDailyReport::$beeline_gate] }}">
                                {{$data[\App\SmsDailyReport::$beeline]}}/
                                {{$data[\App\SmsDailyReport::$beeline_gate]}}
                            </td>
                            <td style="background: #00b7f4" data-order="{{$data[\App\SmsDailyReport::$kcell]+$data[\App\SmsDailyReport::$kcell_gate] }}">
                                {{$data[\App\SmsDailyReport::$kcell]}}/
                                {{$data[\App\SmsDailyReport::$kcell_gate]}}
                            </td>
                            <td style="background: gainsboro">
                                {{$data[\App\SmsDailyReport::$tele2_gate]}}
                            </td>
                            <td style="background: #ff6666">
                                {{$data[\App\SmsDailyReport::$altel_gate]}}
                            </td>
                        @else
                            <td>{{$email}}</td>
                            <td style="background: yellow;" data-order="{{isset($gates['TOTAL'][$email])?($gates['TOTAL'][$email][\App\SmsDailyReport::$beeline]+$gates['TOTAL'][$email][\App\SmsDailyReport::$beeline_gate]):0 }}">
                                {{isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$beeline]:''}}/
                                {{isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$beeline_gate]:''}}
                            </td>
                            <td style="background: #00b7f4" data-order="{{isset($gates['TOTAL'][$email])?($gates['TOTAL'][$email][\App\SmsDailyReport::$kcell]+$gates['TOTAL'][$email][\App\SmsDailyReport::$kcell_gate]):0 }}">
                                {{isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$kcell]:''}}/
                                {{isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$kcell_gate]:''}}
                            </td>
                            <td style="background: gainsboro">
                                {{isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$tele2_gate]:''}}
                            </td>
                            <td style="background: #ff6666">
                                {{isset($gates['TOTAL'][$email])?$gates['TOTAL'][$email][\App\SmsDailyReport::$altel_gate]:''}}
                            </td>
                        @endif

                        @for ($i = 1; $i <= $show_days; $i++)
                            <td style="background: yellow; border-left-width: 5px;" data-order="{{isset($data[$i])?($data[$i][\App\SmsDailyReport::$beeline]+$data[$i][\App\SmsDailyReport::$beeline_gate]):0 }}">
                                {{isset($data[$i])?$data[$i][\App\SmsDailyReport::$beeline]:''}}/
                                {{isset($data[$i])?$data[$i][\App\SmsDailyReport::$beeline_gate]:''}}
                            </td>
                            <td style="background: #00b7f4" data-order="{{isset($data[$i])?($data[$i][\App\SmsDailyReport::$kcell]+$data[$i][\App\SmsDailyReport::$kcell_gate]):0}}">
                                {{isset($data[$i])?$data[$i][\App\SmsDailyReport::$kcell]:''}}/
                                {{isset($data[$i])?$data[$i][\App\SmsDailyReport::$kcell_gate]:''}}
                            </td>
                            <td style="background: gainsboro">
                                {{isset($data[$i])?$data[$i][\App\SmsDailyReport::$tele2_gate]:''}}
                            </td>
                            <td style="background: #ff6666">
                                {{isset($data[$i])?$data[$i][\App\SmsDailyReport::$altel_gate]:''}}
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- .animated -->
@endsection
