<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    protected $fillable = [
        'comment_id',
        'reported_by_user_id',
        'reason',
        'comment_snapshot',
        'parent_comment_snapshot',
        'comment_author_snapshot',
        'title_snapshot',
        'status',
        'reviewed_by_user_id',
        'review_note',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
