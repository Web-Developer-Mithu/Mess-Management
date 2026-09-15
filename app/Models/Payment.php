<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToMess;

class Payment extends Model
{
    use HasFactory, BelongsToMess;

    protected $fillable = ['member_id', 'date', 'amount', 'payment_type', 'note', 'mess_id'];

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

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
