<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $tax_group_id
 * @property int $user_id
 * @property int $status
 * @property string $from
 * @property string $to
 * @property string $payload
 */
class UserTax extends Model
{
    use HasFactory;

    protected $table = 'user_taxes';

    const ACTIVE = 1;
    const REMOVED = 0;

    protected $fillable = [
        'tax_group_id', 'user_id', 'status', 'from', 'to', 'payload'
    ];

    public function taxGroup()
    {
        return $this->belongsTo(TaxGroup::class)->withTrashed();
    }
}
