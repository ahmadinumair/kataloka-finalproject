<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'type_id');
    }
}
