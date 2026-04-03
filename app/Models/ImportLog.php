<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'admin_user_id',
        'tmdb_id',
        'tmdb_type',
        'action',
        'status',
        'message',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
