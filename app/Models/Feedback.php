<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'type_id',
        'category_id',
        'title',
        'description',
        'image_path',
        'status',
        'is_anonymous',
        'urgency',
        'ai_summary',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackType(): BelongsTo
    {
        return $this->belongsTo(FeedbackType::class, 'type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(FeedbackUpvote::class);
    }

    public function isUpvotedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('upvotes')) {
            return $this->upvotes->contains('user_id', $user->id);
        }

        return $this->upvotes()->where('user_id', $user->id)->exists();
    }
}
