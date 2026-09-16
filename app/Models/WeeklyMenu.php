<?php

namespace App\Models;

use App\Models\Traits\BelongsToMess;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyMenu extends Model
{
    use HasFactory, BelongsToMess;

    protected $fillable = ['weekday', 'menu', 'market_person', 'market_condition', 'mess_id'];
}