<?php

namespace App\Models\Traits;

use App\Models\Mess;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToMess
{
    protected static function bootBelongsToMess()
    {
        static::addGlobalScope('mess', function (Builder $builder) {
            if (auth()->check() && auth()->user()->mess_id) {
                $builder->where('mess_id', auth()->user()->mess_id);
            }
        });

        static::creating(function ($model) {
            if (! empty($model->mess_id)) {
                return;
            }

            if (auth()->check() && auth()->user()->mess_id) {
                $model->mess_id = auth()->user()->mess_id;
                return;
            }

            $fallbackMess = Mess::query()->first();

            if (! $fallbackMess) {
                $fallbackMess = Mess::query()->create([
                    'name' => 'Default Mess',
                    'address' => 'Auto-created default mess',
                ]);
            }

            $model->mess_id = $fallbackMess->id;
        });
    }

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }
}
