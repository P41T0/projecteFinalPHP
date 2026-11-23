<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;

class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable;

    use PasskeyAuthenticatable {
        getPasskeyUserHandle as private defaultPasskeyUserHandle;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'passkey_user_handle',
        'admin',
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
            'admin' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getPasskeyUserHandle(): string
    {
        if ($this->passkey_user_handle !== null) {
            $handle = base64_decode($this->passkey_user_handle, strict: true);

            if ($handle === false) {
                throw new \UnexpectedValueException('Invalid preserved passkey user handle.');
            }

            return $handle;
        }

        return $this->defaultPasskeyUserHandle();
    }

    /**
     * @return HasMany<Comanda, $this>
     */
    public function comandes(): HasMany
    {
        return $this->hasMany(Comanda::class, 'usuari_id');
    }

    public function isAdmin(): bool
    {
        return (bool) $this->admin;
    }
}
