<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_id',
        'identifier_value',
        'submitter_type',
        'submitter_id',
        'external_ref',
        'ip',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FormAnswer::class);
    }

    // Helper Methods
    public function getAnswerByFieldId(int $fieldId): ?FormAnswer
    {
        return $this->answers->firstWhere('form_field_id', $fieldId);
    }

    public function getAnswerValue(int $fieldId): mixed
    {
        $answer = $this->getAnswerByFieldId($fieldId);
        return $answer?->value;
    }
}
