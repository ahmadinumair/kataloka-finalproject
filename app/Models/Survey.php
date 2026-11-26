<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Survey extends Model
{
    protected $fillable = [
        'title',
        'description',
        'target_audience',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function answers(): HasManyThrough
    {
        return $this->hasManyThrough(SurveyAnswer::class, SurveyResponse::class);
    }

    public function isCompletedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('responses')) {
            return $this->responses->contains('user_id', $user->id);
        }

        return $this->responses()->where('user_id', $user->id)->exists();
    }

    /**
     * Menghitung nilai kepuasan rata-rata survei (skala 1 - 5)
     */
    public function getAverageScoreAttribute(): float
    {
        $avg = $this->answers()->whereNotNull('score')->avg('score');
        return $avg ? round((float) $avg, 2) : 0.0;
    }
}
