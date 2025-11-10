<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'experience',
        'skills', 'resume_path', 'status', 'job_id', 'notes', 'created_by'
    ];

    public function opening()
    {
        return $this->belongsTo(Opening::class);
    }
}
