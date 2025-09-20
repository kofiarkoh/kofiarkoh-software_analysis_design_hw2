<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DeliveryRegion extends Model
{
    use SoftDeletes, LogsActivity;
    protected $guarded = false;


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty() ;
    }
    public function cities(): HasMany
    {
        return $this->hasMany(DeliveryCity::class , 'region_id');
    }
}
