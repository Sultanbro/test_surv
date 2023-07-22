<?php

namespace App\Models\Admin\Headhunter;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    const FROM_TYPE_HH = 1;
    const FROM_TYPE_HH2 = 2;

    protected $table = 'headhunter_negotiations';

    public $timestamps = true;

    protected $fillable = [
        'vacancy_id',
        'negotiation_id',
        'lead_id',
        'has_updated',
        'phone',
        'name',
        'resume_id'
    ];
}
