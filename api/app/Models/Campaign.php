<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
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

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'creator_id'); }
    public function donations(): HasMany { return $this->hasMany(Donation::class); }
}


