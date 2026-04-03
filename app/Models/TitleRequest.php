<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitleRequest extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'requested_title',
        'requested_type',
        'message',
        'status',
        'reviewed_by_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
