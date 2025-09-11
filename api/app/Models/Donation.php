<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id', 'user_id', 'amount', 'status', 'payment_reference', 'correlation_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}


