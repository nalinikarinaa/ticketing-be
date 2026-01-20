<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'user_id',
        'subject',
        'category',
        'priority',
        'description',
        'status',
        'attachment',
    ];

    protected $casts = [
        'priority' => 'string',
        'status'   => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->ticket_code = 'TCK-' . strtoupper(uniqid());
        });
    }

    /**
     * Relasi: Ticket dimiliki oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Ticket punya banyak balasan
     */
    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }
}
