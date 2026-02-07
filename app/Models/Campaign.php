<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'subject',
        'body',
        'recipient_count',
        'status'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'status' => Status::class,
        ];
    }
}
