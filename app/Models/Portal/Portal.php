<?php

namespace App\Models\Portal;

use App\Models\CentralUser;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portal extends Model
{
    use HasFactory;

    protected $table = 'portals';

    protected $connection = 'mysql';

    protected $fillable = [
        'tenant_id',
        'owner_id',
        'currency',
        'main_page_video',
        'main_page_video_show_days_amount',
        'kpi_backlight'
    ];

    protected $casts = [
        'kpi_backlight' => 'array',
    ];

    /**
     * @param string $tenantId
     * @return Portal
     */
    public static function getByTenantIdOrFail(
        string $tenantId
    ): Portal
    {
        return self::query()
            ->where('tenant_id', $tenantId)
            ->firstOrFail();
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(CentralUser::class, 'owner_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function tenants(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }


    //TODO Portal refactor: add 'portal__user' relation with data migrated from 'tenant_pivot'
}
