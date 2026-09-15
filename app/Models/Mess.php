<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mess extends Model
{
    protected $fillable = [
        'name',
        'address',
        'logo',
        'balance_alert_threshold',
        'balance_alert_comment',
        'total_balance_warning_threshold',
        'total_balance_warning_message',
        'dining_scene_message',
    ];

    protected $casts = [
        'balance_alert_threshold' => 'decimal:2',
        'total_balance_warning_threshold' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
