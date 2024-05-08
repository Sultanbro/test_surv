<?php

namespace App\Models\Admin\Headhunter;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    protected $table = 'headhunter_negotiations';

    public $timestamps = true;

    protected $fillable = [
        'vacancy_id',
        'negotiation_id',
        'lead_id',
        'has_updated',
        'phone',
        'name',
        'resume_id',
        'from'
    ];
}
