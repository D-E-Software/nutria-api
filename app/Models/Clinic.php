<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $domain
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<ClinicSetting> $settings
 * @property-read Collection<User> $users
 * @property-read Collection<Program> $programs
 * @property-read Collection<Order> $orders
 * @property-read Collection<Email> $emails
 */
class Clinic extends Model
{
    protected $fillable = ['name', 'slug', 'domain', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function settings(): HasMany
    {
        return $this->hasMany(ClinicSetting::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function setting(string $key, $default = null): ?string
    {
        return $this->settings->firstWhere('key', $key)?->value ?? $default;
    }
}
