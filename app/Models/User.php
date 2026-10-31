<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements Auditable, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;
    use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'change_password',
        'keycloak_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    /**
     * Never write credentials into the audit log.
     *
     * @var list<string>
     */
    protected $auditExclude = [
        'password',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the user's full name
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class);
    }

    /**
     * Link to set an own password (password broker token, expiry as configured in auth.passwords)
     */
    public function passwordSetUrl(): string
    {
        return route('password.reset', ['token' => Password::createToken($this), 'email' => $this->email]);
    }

    /**
     * Check if the user can be impersonated
     */
    public function canBeImpersonated(): bool
    {
        // Only regular accounts without any role or permission (directly or via role)
        return $this->roles()->doesntExist() && $this->getAllPermissions()->isEmpty();
    }

    /**
     * Check if user is currently being impersonated
     */
    public function isBeingImpersonated(): bool
    {
        return session()->has('impersonate_original_user') &&
               session('impersonate_original_user') !== $this->id;
    }
}
