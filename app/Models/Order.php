<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $clinic_id
 * @property int $program_id
 * @property string $order_ref
 * @property string $customer_name
 * @property string $customer_email
 * @property string|null $customer_phone
 * @property float $amount
 * @property string $currency
 * @property string $status
 * @property string|null $gateway_ref
 * @property string|null $gateway_status
 * @property Carbon|null $paid_at
 * @property Carbon|null $refunded_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Clinic $clinic
 * @property-read Program $program
 * @property-read Collection<Email> $emails
 */
class Order extends Model
{
    protected $fillable = [
        'clinic_id', 'program_id', 'order_ref', 'customer_name',
        'customer_email', 'customer_phone', 'amount', 'currency',
        'status', 'gateway_ref', 'gateway_status', 'paid_at', 'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
