<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = [
        'causer_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'created_at',
    ];

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

}
