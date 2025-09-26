<?php

namespace App\Models;

use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory;

    protected $fillable = [
        'campaign_id', 'user_id', 'amount', 'status', 'payment_reference', 'correlation_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /** @return BelongsTo<Campaign, Donation> */
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    
    /** @return BelongsTo<User, Donation> */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}


