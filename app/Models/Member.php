<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\BelongsToMess;

class Member extends Model
{
    use HasFactory, BelongsToMess, SoftDeletes;

    protected $fillable = ['name', 'phone', 'status', 'join_date', 'mess_id'];

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

        static::restored(function ($model) {
            ActivityLog::record($model, 'restored');
        });
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
