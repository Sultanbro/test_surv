<?php

namespace App\Models\Analytics;

use App\Position;
use App\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $group_id
 * @property int $position_id
 * @property string $date
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ReportCard extends Model
{
    use HasFactory;

    protected $table = 'report_cards';

    protected $fillable = [
        'group_id',
        'position_id',
        'date',
        'divide_to'
    ];

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ProfileGroup::class, 'group_id');
    }

    /**
     * @return BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
