<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'miles',
        'total',
        'car_id'
    ];

    protected $visible = [
        'id',
        'date',
        'miles',
        'total',
        'car'
    ];

    protected $casts = [
        'date' => 'datetime:m/d/Y',
    ];

    public function car(): BelongsTo {
        return $this->belongsTo(Car::class);
    }
}
