<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitleView extends Model
{
    protected $fillable = [
        'title_id',
        'user_id',
        'ip_hash',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
