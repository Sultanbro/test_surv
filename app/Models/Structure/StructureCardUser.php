<?php

namespace App\Models\Structure;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StructureCardUser extends Model
{
    use HasFactory;

    protected $table = 'structure_card_users';

    protected $fillable = ['user_id', 'card_id'];

    /**
     * @return BelongsTo
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function card():BelongsTo
    {
        return $this->belongsTo(StructureCard::class);
    }
}
