<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/** Mfanyakazi wa ofisi (guard: staff) */
class StaffUser extends Authenticatable
{
    /** Majukumu: viewer = kuona tu; officer = kuweka matangazo na kubadilisha hali za maombi; admin = yote + watumiaji */
    public const ROLES = [
        'viewer'  => 'Viewer (view only)',
        'officer' => 'Officer (vacancies and applications)',
        'admin'   => 'Administrator (everything, users)',
    ];

    protected $table = 'staff_users';

    protected $fillable = ['name', 'first_name', 'middle_name', 'last_name', 'phone', 'email', 'password', 'role', 'is_active', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    /** Jina kamili hutungwa kutoka first/middle/last name zikijazwa */
    protected static function booted(): void
    {
        static::saving(function (self $u) {
            if (filled($u->first_name) || filled($u->last_name)) {
                $u->name = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$u->first_name, $u->middle_name, $u->last_name]))));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'password'      => 'hashed',
            'is_active'     => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Anaweza kuweka/kuhariri matangazo, waajiri na kubadilisha hali za maombi */
    public function canManage(): bool
    {
        return in_array($this->role, ['admin', 'officer'], true);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
