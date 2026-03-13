<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'activity_id',
        'respondent_name',
        'photo_path',
    ];

    /**
     * Get the activity that owns the report.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
