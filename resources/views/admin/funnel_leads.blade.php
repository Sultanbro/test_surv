<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Стажеры</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/admin/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>
    <style>
        * {
            font-family: 'Open Sans', sans-serif;
        }
    </style>
</head>
<body>



<div class="">
    <div class="row">
        <div class="col-xl-12">

            <h2>Стажеры</h2>
            <p>Количество: <span id="quan">{{ $leads->count() }}</span></p>

            <select id="myDate" class="mb-2" onchange="filter('myDate', 0)">
                @foreach($dates as $key => $value)
                <option value="{{ $key }}" @if($key == $active_date) selected="selected" @endif>{{ $value }}</option>
                @endforeach
            </select>

            <select  id="myStatus" class="mb-2" onchange="filter('myStatus', 6)">
                @foreach($statuses as $key => $value)
                <option value="{{ $key }}" @if($key == $active_status) selected="selected" @endif>{{ $value }}</option>
                @endforeach
            </select>
            
            <table class="table b-table table-striped table-bordered table-sm" id="leads" >
                <tr>
                    <th style="display:none;">Дата</th>
                    <th>Дата подписи</th>
                    <th>ФИО</th>
                    <th>Сот.телефон</th>
                    <th>Страна</th>
                    <th>Приглашен на</th>
                    <th>Статус</th>
                    <th>Причина увольнения</th>
                    <th>1й месяц</th>
                    <th>2й месяц</th>
                    <th>3й месяц</th>
                </tr>
                @foreach($leads as $lead)
                <tr>
                    <td style="display:none;">{{ $lead->date }}</td>
                    <td>{{ $lead->skyped }} {{ $lead->inhouse }} </td>
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->phone }}</td>
                    <td>{{ $lead->country }}</td>
                    <td>{{ $lead->invite_at }}</td>
                    <td>{{ $lead->status }}</td>
                    <td>{{ $lead->fire_cause }}</td>
                    <td class="{{ $lead->month_1 }}">
                        @if($lead->applied) 
                            {{ $lead->applied }}  
                            
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="left" title="{{ $lead->recruiter_comment }}"></span>
                           
                        @endif
                    </td>
                    <td class="{{ $lead->month_2 }}"></td>
                    <td class="{{ $lead->month_3 }}"></td>
                </tr>
                @endforeach
            </table>

            


        </div>  
    </div>
</div>


<style>
#leads {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#leads td, #leads th {
  border: 1px solid #ddd;
  padding: 8px;
}

#leads tr:nth-child(even){background-color: #f2f2f2;}

#leads tr:hover {background-color: #ddd;}

#leads th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}
.colored {
    background: green;
}
th:nth-child(7),
td:nth-child(7) {
    max-width: 200px;
}
* {
    font-size: 14px;
}
</style>
    


<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<script>
filter();

function filter() {
    fil('myDate', 0, false);
    fil('myStatus', 6, true);
}

function fil(id, index, check) {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById(id);
    filter = input.value.toUpperCase();
    table = document.getElementById("leads");
    tr = table.getElementsByTagName("tr");

    quan = document.getElementById("quan");
    var show = 0;
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[index];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                if(check) {
                    tr[i].style.display = tr[i].style.display == "none" ? "none" : "";
                } else {
                    tr[i].style.display = "";
                }
                show++;
            } else {
                tr[i].style.display = "none";
            }
        }       
    }

    quan.innerText = show;
}
</script>
</body>
</html>


