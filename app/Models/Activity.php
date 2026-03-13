<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'activity_date',
        'nama_pemeriksa',
        'provinsi',
        'kabupaten',
        'partners',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'partners' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            if (empty($activity->uuid)) {
                $activity->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the submissions for the activity.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
