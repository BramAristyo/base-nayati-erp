<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $table = 'audit_trails';

    public $timestamps = false;

    protected $fillable = [
        'causer_id',
        'action',
        'description',
        'detail_route',
        'subject_type',
        'subject_id',
        'created_at',
    ];

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

}
