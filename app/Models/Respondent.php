<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respondent extends Model
{
    protected $fillable = [
        'submission_id',
        'nama_resp',
        'nks_resp',
        'kec_sls',
        'desa_sls',
        'nama_sls',
        'photo_path',
    ];

    /**
     * Get the submission that owns the respondent.
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
