<?php

namespace App\Models;

use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_HIRED = 'hired';

    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'resume_url',
        'status',
        'internal_notes',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
