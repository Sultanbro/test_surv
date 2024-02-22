<?php

namespace App\Models;

use App\Models\MorphRelations\HasImages;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property string $contract_number,
 * @property string $password_number,
 */
class UserSignatureHistory extends Model
{
    use HasFactory;
    use HasImages;

    protected $table = 'user_signature_histories';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'contract_number',
        'password_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }
}
