<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    public $timestamps = false;

    protected $fillable = [
        'causer_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
