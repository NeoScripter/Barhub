<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable // implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'active_exhibition_id' => null,
        'company_id' => null,
    ];
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
        'remember_token',
    ];

    public function exhibitions(): BelongsToMany
    {
        return $this->belongsToMany(Exhibition::class);
    }

    public function getActiveExhibition(): ?Exhibition
    {
        $activeExhibition = $this->active_exhibition_id ? Exhibition::find($this->active_exhibition_id) : null;

        if ($activeExhibition) {
            return $activeExhibition;
        }

        if ($this->role === UserRole::SUPER_ADMIN) {
            return Exhibition::first();
        }

        if ($this->role !== UserRole::ADMIN) {
            return null;
        }

        return $this->exhibitions()->first();
    }

    public function setActiveExhibition(int $id): void
    {
        abort_unless(
            $this->role === UserRole::SUPER_ADMIN ||
                $this->exhibitions()->whereKey($id)->exists(),
            403,
            'User does not belong to this exhibition'
        );

        $this->update(['active_exhibition_id' => $id]);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function comment(): HasOne
    {
        return $this->hasOne(TaskComment::class);
    }

    public function taskTemplates(): HasMany
    {
        return $this->hasMany(TaskTemplate::class);
    }

    public function assignRole(UserRole $role): void
    {
        $this->update(['role' => $role]);
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

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
            'role' => UserRole::class,
            'active_exhibition_id' => 'integer',
        ];
    }
}
