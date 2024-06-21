<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = [
        'client_id',
        'date',
        'text',
    ];

    protected $casts = [
        'date' => 'datetime:j F Y',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
