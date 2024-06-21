<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'client_id',
        'start',
        'end',
        'notes',
    ];

    protected $dates = [
        'start',
        'end',
    ];

    public function getTimeAttribute(): string
    {
        return $this->start->format('l j F Y, H:i') . ' to ' . $this->end->format('H:i');
    }
}
