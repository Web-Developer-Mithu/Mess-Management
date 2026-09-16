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
        'mess_id',
        'model_type',
        'model_id',
        'action',
        'details',
        'old_values',
        'new_values',
        'ip_address',
        'device_type',
        'browser',
        'platform',
        'location',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public static function record($model, string $action): void
    {
        $ipAddress = request()->header('X-Forwarded-For')
            ? trim((string) explode(',', request()->header('X-Forwarded-For'))[0])
            : (request()->header('CF-Connecting-IP')
                ?: (request()->header('X-Real-IP') ?: request()->ip()));
        $userAgent = (string) request()->userAgent();

        self::create([
            'user_id' => Auth::id(),
            'mess_id' => Auth::user()?->mess_id,
            'model_type' => $model::class,
            'model_id' => $model->id,
            'action' => $action,
            'details' => self::details($model),
            'old_values' => $action === 'created' ? null : self::snapshot($model->getOriginal()),
            'new_values' => $action === 'deleted' ? null : self::snapshot($action === 'updated' ? $model->getAttributes() : $model->getAttributes()),
            'ip_address' => $ipAddress,
            'device_type' => self::deviceType($userAgent),
            'browser' => self::browser($userAgent),
            'platform' => self::platform($userAgent),
            'location' => self::location(),
            'user_agent' => $userAgent,
        ]);
    }

    private static function snapshot(array $values): array
    {
        unset($values['password'], $values['remember_token']);

        return $values;
    }

    private static function details($model): string
    {
        if ($model instanceof Payment) {
            return 'Payment ৳' . number_format((float) $model->amount, 2)
                . ' | Member #' . ($model->member_id ?? 'unknown')
                . ' | ' . ($model->payment_type ?? 'unknown method');
        }

        if ($model instanceof Meal) {
            return 'Meal ' . $model->meal_count . ' | Member #' . ($model->member_id ?? 'unknown')
                . ' | ' . ($model->date ?? 'unknown date');
        }

        if ($model instanceof Expense) {
            return 'Expense ৳' . number_format((float) $model->amount, 2)
                . ' | ' . ($model->category ?? 'uncategorized');
        }

        return (string) ($model->name ?? $model->category ?? $model->member_id ?? $model->date ?? 'record');
    }

    private static function deviceType(string $userAgent): string
    {
        return preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)
            ? (preg_match('/iPad|Tablet/i', $userAgent) ? 'Tablet' : 'Mobile')
            : 'Desktop';
    }

    private static function browser(string $userAgent): string
    {
        return match (true) {
            preg_match('/Edg\//i', $userAgent) === 1 => 'Microsoft Edge',
            preg_match('/OPR\//i', $userAgent) === 1 => 'Opera',
            preg_match('/Chrome\//i', $userAgent) === 1 => 'Google Chrome',
            preg_match('/Firefox\//i', $userAgent) === 1 => 'Mozilla Firefox',
            preg_match('/Safari\//i', $userAgent) === 1 => 'Safari',
            default => 'Unknown',
        };
    }

    private static function platform(string $userAgent): string
    {
        return match (true) {
            preg_match('/Windows/i', $userAgent) === 1 => 'Windows',
            preg_match('/Android/i', $userAgent) === 1 => 'Android',
            preg_match('/iPhone|iPad/i', $userAgent) === 1 => 'iOS',
            preg_match('/Mac OS/i', $userAgent) === 1 => 'macOS',
            preg_match('/Linux/i', $userAgent) === 1 => 'Linux',
            default => 'Unknown',
        };
    }

    private static function location(): string
    {
        $country = request()->header('CF-IPCountry') ?: request()->header('X-Country');
        $city = request()->header('CF-IPCity') ?: request()->header('X-City');

        return collect([$city, $country])->filter()->implode(', ') ?: 'Unknown';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
