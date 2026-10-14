<?php

namespace App\Models\Structure;

use App\ProfileGroup;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StructureCard extends Model
{
    use HasFactory;

    protected $table = 'structure_card';

    protected $fillable = ['name', 'parent_id', 'description', 'color','group_id','status','is_group','is_vacant'];

    /**
     * Get the parent card of the structure card.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(StructureCard::class, 'parent_id');
    }

    /**
     * Get the recursive children cards of the structure card.
     *
     * @return HasMany
     */
    public function childrens(): HasMany
    {
        return $this->hasMany(StructureCard::class, 'parent_id', 'id')->with('childrens')->with('users:id')->with('manager');
    }

    /**
     * Get the structure card manager associated with the structure card.
     *
     * @return HasOne
     */
    public function manager(): HasOne
    {
        return $this->hasOne(StructureCardManager::class, 'card_id', 'id');
    }

    /**
     * Get the users associated with the structure card.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'structure_card_users', 'card_id', 'user_id');
    }

    /**
     * Get the profile group associated with the structure card.
     *
     * @return BelongsTo
     */
    public function profileGroup(): BelongsTo
    {
        return $this->belongsTo(ProfileGroup::class, 'group_id');
    }

}
