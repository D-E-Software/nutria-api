<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    protected $fillable = [
        'clinic_id', 'program_id', 'order_ref', 'customer_name',
        'customer_email', 'customer_phone', 'amount', 'currency',
        'status', 'gateway_ref', 'gateway_status', 'paid_at', 'refunded_at',
    ];

    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_REFUNDED,
        ];
    }

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
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_REFUNDED;
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('paid_at');
    }
}
