<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'details',
        'ip_address',
        'user_agent',
    ];

    public static function record($model, string $action): void
    {
        $ipAddress = request()->header('X-Forwarded-For')
            ? trim((string) explode(',', request()->header('X-Forwarded-For'))[0])
            : (request()->header('CF-Connecting-IP')
                ?: (request()->header('X-Real-IP') ?: request()->ip()));

        self::create([
            'user_id' => Auth::id(),
            'model_type' => $model::class,
            'model_id' => $model->id,
            'action' => $action,
            'details' => $model->name ?? $model->category ?? $model->member_id ?? $model->date ?? 'record',
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
