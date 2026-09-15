<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToMess;

class Expense extends Model
{
    use HasFactory, BelongsToMess;

    protected $fillable = [
        'date',
        'category',
        'amount',
        'vendor',
        'note',
        'type',
        'is_fixed',
        'member_ids',
        'member_adjustments',
        'member_shares',
        'mess_id',
    ];

    protected static function booted(): void
    {
        static::created(function ($model) {
            ActivityLog::record($model, 'created');
        });

        static::updated(function ($model) {
            ActivityLog::record($model, 'updated');
        });

        static::deleted(function ($model) {
            ActivityLog::record($model, 'deleted');
        });
    }

    protected $casts = [
        'is_fixed' => 'boolean',
        'member_ids' => 'array',
        'member_adjustments' => 'array',
        'member_shares' => 'array',
    ];

    public static function shareForMembers(float $amount, array $memberIds): float
    {
        $memberIds = array_values(array_filter(array_map('intval', $memberIds)));
        $count = count($memberIds);

        if ($count === 0) {
            return 0.0;
        }

        return round($amount / $count, 2);
    }

    public static function memberShareForExpense(float $amount, array $memberIds, array $adjustments = [], ?int $memberId = null): float
    {
        $memberIds = array_values(array_filter(array_map('intval', $memberIds)));
        if ($memberId === null) {
            return 0.0;
        }

        $baseShare = self::shareForMembers($amount, $memberIds);
        $adjustment = (float) ($adjustments[$memberId] ?? 0);

        return round($baseShare + $adjustment, 2);
    }

    public function selectedMembers()
    {
        return Member::whereIn('id', $this->member_ids ?? [])->get();
    }
}
