<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'event_type',
        'celular',
        'name',
        'api_phone_id',
        'api_phone_number',
        'message',
        'interactive_id',
        'status',
        'status_id',
        'conversation',
        'message_id',
    ];

    protected $casts = [
        'conversation' => 'array',
    ];
}
