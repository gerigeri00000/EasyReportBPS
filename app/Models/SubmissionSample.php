<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionSample extends Model
{
    protected $fillable = [
        'submission_id',
        'respondent_index',
        'sample_index',
        'name',
        'photo_path',
    ];

    protected $casts = [
        'respondent_index' => 'integer',
        'sample_index' => 'integer',
    ];

    /**
     * Get the submission that owns the sample.
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
