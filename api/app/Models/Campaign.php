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
        'title', 'description', 'goal_amount', 'current_amount', 'status', 'featured',
        'category_id', 'creator_id', 'published_at', 'start_date', 'end_date',
        'image', 'tags', 'likes', 'comments_count', 'supporters_count',
        'short_description', 'impact_description', 'submission_status'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'goal_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'likes' => 'integer',
        'comments_count' => 'integer',
        'supporters_count' => 'integer',
        'tags' => 'array',
    ];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'creator_id'); }
    public function donations(): HasMany { return $this->hasMany(Donation::class); }
    public function comments(): HasMany { return $this->hasMany(Comment::class); }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        return $this->goal_amount > 0 ? round(($this->current_amount / $this->goal_amount) * 100, 2) : 0;
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) return null;
        $now = now();
        $end = $this->end_date;
        $diff = $end->diffInDays($now, false);
        return $diff > 0 ? $diff : 0;
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && (!$this->end_date || $this->end_date->isFuture());
    }
}


