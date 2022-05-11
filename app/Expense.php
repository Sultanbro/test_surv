<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Expense extends Model {
    public $timestamps = false;

    protected $table = 'b_expense';

    protected $fillable = [
        'name',
        'id_user',
        'type',
        'amount',
        'balance',
        'time',
    ];

    public static function add($cost, $user_id, $type, $name){

        $id = self::create([
            'name' => $name,
            'id_user' => $user_id,
            'type' => $type,
            'amount' => $cost,
            'balance' => \App\User::updateBalance($user_id, $cost),
            'time' => time(),
        ])->id;

        $user = User::find($user_id);

        if ($user->notify_sent <= 0 && $user->UF_BALANCE < 2000 && $user->UF_BALANCE > 100 && $user->currency == 'kzt'){
          $phone = $user->PHONE;
          $out = array();
          $url = 'https://cp.u-marketing.org';
          if(!empty($phone)){
            $phone = preg_replace("/[^0-9]/", '', $phone);
            $data = array(
              'action' => 'addCall',
              'apiKey' => '30a3dbb43478ad7f05b297a7b144b65e',
              'phone' => $phone,
              'appid' => 4565048
            );
            $url = $url.'/api/call?'.http_build_query($data);
            if($curl = curl_init()){
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
              $out = curl_exec($curl);
              curl_close($curl);
            }
          }
          $user->notify_sent=1;
          $user->save();
        } else if ($user->notify_sent <= 1 && $user->UF_BALANCE < 100 && $user->currency == 'kzt') {

          $phone = $user->PHONE;
          $out = array();
          $url = 'https://cp.u-marketing.org';
          if(!empty($phone)){
            $phone = preg_replace("/[^0-9]/", '', $phone);
            $data = array(
              'action' => 'addCall',
              'apiKey' => '30a3dbb43478ad7f05b297a7b144b65e',
              'phone' => $phone,
              'appid' => 4565050
            );
            $url = $url.'/api/call?'.http_build_query($data);
            if($curl = curl_init()){
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
              $out = curl_exec($curl);
              curl_close($curl);
            }
          }

          $user->notify_sent=2;
          $user->save();

        }

        return $id;
    }

    public static function remove($old_cost, $new_cost, $user_id, $type){

        self::create([
            'name' => 'Возврат Автозвонок',
            'id_user' => $user_id,
            'type' => $type,
            'amount' => -$old_cost,
            'balance' => \App\User::updateBalance($user_id, -$old_cost),
            'time' => time(),
        ]);

        $id = self::create([
            'name' => 'Автозвонок новый',
            'id_user' => $user_id,
            'type' => $type,
            'amount' => $new_cost,
            'balance' => \App\User::updateBalance($user_id, $new_cost),
            'time' => time(),
        ])->id;

        return $id;
    }

}
