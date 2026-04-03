<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function searchLogs()
    {
        return $this->hasMany(SearchLog::class);
    }

    public function titleViews()
    {
        return $this->hasMany(TitleView::class);
    }

    public function importLogs()
    {
        return $this->hasMany(ImportLog::class, 'admin_user_id');
    }

    public function reviewedCommentReports()
    {
        return $this->hasMany(CommentReport::class, 'reviewed_by_user_id');
    }

    public function reviewedTitleRequests()
    {
        return $this->hasMany(TitleRequest::class, 'reviewed_by_user_id');
    }

    public function titleRequests()
    {
        return $this->hasMany(TitleRequest::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        $roleName = $this->role?->name;

        return $roleName !== null && in_array($roleName, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isModerator(): bool
    {
        return $this->hasRole('moderator');
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->hasAnyRole(['admin', 'moderator']);
    }
}
