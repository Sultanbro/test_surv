@extends('layouts.admin')
@section('title', 'Депремирование')
@section('content')

<div class="container p-4">
  <div class="card p-3 content">
      <h5 class="mb-3"><strong>Система депремирования&nbsp;</strong></h5>
      <table cellspacing="0" cellpadding="0" class="table table-striped">
        <tbody>
          @foreach($fines as $fine)
          <tr>
            <td class="pr-5 text-left"><span><strong>{{$fine->name}}</strong></span></td>
            <td class="p-3 text-right primary" style="background: #dc354573;
              font-weight: 700;">- {{$fine->penalty_amount}} тенге</td>
          </tr>
          @endforeach
      
        </tbody>
      </table>
 
  </div>
</div>

@endsection

@section('styles')
<style>
  ul,
  ol {
    padding-left: 30px;
  }

  .content p {
    color: #000;
  }

  .content a {
    color: #007bff;
    text-decoration: dashed;
  }
</style>
<script>
    (function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
    })(window,document,'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_8_dzfbjh.js');
</script>
@endsection