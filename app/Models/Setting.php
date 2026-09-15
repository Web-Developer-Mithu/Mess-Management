<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToMess;

class Setting extends Model
{
    use HasFactory, BelongsToMess;

    protected $fillable = ['month', 'meal_rate', 'mess_id'];
}
