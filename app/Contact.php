<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contact extends Model {

    public $timestamps = false;

    protected $table = 'b_contacts';

    protected $fillable = [
        'id_user',
        'id_group',
        'phone',
        'data',
        'send_or_not',
        'date'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function groups() {
        return Group::bitrixGroup($this->id_group);
    }

    public static function add($user_id, $group_id, $phone, $data) {
        $contact = Contact::where('id_user', $user_id)->where('id_group', $group_id)->where('phone', $phone)->first();
        if ($contact) {
            $id_contact = $contact->id;
        } else {
            $id_contact = Contact::create([
                'id_user' => $user_id,
                'id_group' => $group_id,
                'phone' => $phone,
                'data' => json_encode($data),
                'date' => DB::raw('NOW()')
            ])->id;
        }

        return $id_contact;
    }


}
