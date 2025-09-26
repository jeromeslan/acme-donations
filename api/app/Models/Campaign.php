<?php

namespace App\Models;

use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'goal_amount', 'donated_amount', 'status', 'featured',
        'category_id', 'creator_id', 'published_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'goal_amount' => 'decimal:2',
        'donated_amount' => 'decimal:2',
    ];

    /** @return BelongsTo<Category, Campaign> */
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    
    /** @return BelongsTo<User, Campaign> */
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'creator_id'); }
    
    /** @return HasMany<Donation, Campaign> */
    public function donations(): HasMany { return $this->hasMany(Donation::class); }
    
    /** @return HasMany<Donation, Campaign> */
    public function successfulDonations(): HasMany { 
        return $this->hasMany(Donation::class)->whereIn('status', ['completed', 'succeeded']); 
    }
}


