<?php

namespace App\Models\Admin;

use App\Enums\ErrorCode;
use App\Support\Core\CustomException;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagerHasOwner extends Model
{
    use HasFactory;
    protected $connection = 'tenant';
    protected $table = 'manager_has_owner';

    protected $fillable = [
        'manager_id',
        'owner_id'
    ];

    /**
     * @param $query
     * @param int $ownerId
     * @return object|null
     * @throws Exception
     */
    public function scopeGetManagerByOwnerIdOrFail($query, int $ownerId): ?object
    {
        $owner = $query->where('owner_id', $ownerId)->first();

        if($owner == null)
        {
            throw new Exception('Клиент еще не имеет своего менеджера');
        }

        return $owner->managers;
    }

    /**
     * @param $query
     * @param int $managerId
     * @return array
     */
    public function scopeGetOwnerByManagerIdToArray($query, int $managerId): array
    {
        return $query->where('manager_id', $managerId)->get()->pluck('owner_id')->toArray();
    }

    /**
     * @param int $ownerId
     * @param int $managerId
     * @return Builder|Model
     */
    public static function createRecord(
        int $ownerId,
        int $managerId
    ): Builder|Model
    {
        $exist = self::query()->where('owner_id', $ownerId)->exists();

        if ($exist)
        {
            new CustomException("Клиент с ID $ownerId уже имеет привязанного менеджера", ErrorCode::BAD_REQUEST, []);
        }

        return self::query()->create([
            'owner_id' => $ownerId,
            'manager_id' => $managerId
        ]);
    }

    /**
     * @return HasMany
     */
    public function managers(): HasMany
    {
        return $this->hasMany(User::class, 'id', 'manager_id');
    }
}
