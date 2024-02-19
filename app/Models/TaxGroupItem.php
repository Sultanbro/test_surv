<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $tax_group_id
 * @property string $name
 * @property bool $is_percent
 * @property bool $end_subtraction
 * @property int $value
 * @property int $order
 */
class TaxGroupItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tax_group_id', 'name', 'is_percent', 'end_subtraction', 'value', 'order'
    ];
}
