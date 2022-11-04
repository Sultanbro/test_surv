<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingLogo extends Model
{
    use HasFactory;

    protected $table = 'images';

    public $timestamps = false;

    protected $guarded = [];

    protected $fillable = [
        'id',
        'name',
        'url',
    ];
}