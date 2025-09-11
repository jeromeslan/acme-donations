<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id', 'user_id', 'content', 'likes', 'parent_id'
    ];

    protected $casts = [
        'likes' => 'integer',
        'parent_id' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Accessor pour formater la date
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y à H:i');
    }

    // Scope pour les commentaires principaux (pas des réponses)
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}

