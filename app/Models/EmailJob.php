<?php

namespace App\Models;
use App\Enums\Status;

use Illuminate\Database\Eloquent\Model;

class EmailJob extends Model
{
    protected $fillable = [
        'campaign_id',
        'recipient_email',
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
