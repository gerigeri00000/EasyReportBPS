<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'activity_id',
        'nama_mitra',
    ];

    /**
     * Get the activity that owns the submission.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the respondents for the submission.
     */
    public function respondents()
    {
        return $this->hasMany(Respondent::class);
    }
}
