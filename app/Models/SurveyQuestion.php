<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'options',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'order' => 'integer',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    /**
     * Dapatkan daftar opsi standar berdasarkan tipe pertanyaan
     */
    public function getAvailableOptions(): array
    {
        if (!empty($this->options) && is_array($this->options)) {
            return $this->options;
        }

        return match ($this->type) {
            'satisfaction_4' => [
                'Sangat Tidak Puas',
                'Kurang Puas',
                'Puas',
                'Sangat Puas',
            ],
            'satisfaction_5' => [
                'Sangat Tidak Puas',
                'Kurang Puas',
                'Cukup Puas',
                'Puas',
                'Sangat Puas',
            ],
            default => ['1', '2', '3', '4', '5'],
        };
    }
}
