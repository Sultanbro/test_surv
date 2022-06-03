@extends('layouts.admin')

@section('content')
    <div class="">
        <div class="row">
            <form action="" method="get">
                <div class="col-lg-4">
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
                <div class="col-lg-4">
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
                <div class="col-lg-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> применить
                    </button>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="report-table" class="table table-striped table-bordered" style="width:100%">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Линия</th>
                        <th scope="col">Сумма</th>
                        @for ($i = 1; $i <= $show_days; $i++)
                            <th scope="col">{{$i}}</th>
                        @endfor
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($gates as $gate=>$data)
                    <tr>
                        <th scope="row">{{\App\SmsDailyReport::title($gate)}}</th>
                        <th scope="row">{{$data['total']}}</th>
                        @for ($i = 1; $i <= $show_days; $i++)
                            <td>{{isset($data[$i])?$data[$i]:''}}</td>
                        @endfor
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- .animated -->
@endsection
