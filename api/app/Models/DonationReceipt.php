<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id', 'receipt_number', 'issued_at', 'metadata'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    /** @return BelongsTo<Donation, DonationReceipt> */
    public function donation(): BelongsTo { return $this->belongsTo(Donation::class); }
}


