<?php

namespace App\Models;

use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'job_postings';

    protected $fillable = [
        'title',
        'description',
        'location',
        'employment_type',
        'status',
        'salary_min',
        'salary_max',
        'hiring_budget',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'hiring_budget' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}
