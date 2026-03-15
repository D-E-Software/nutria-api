<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property int $clinic_id
 * @property int|null $order_id
 * @property string $to_email
 * @property string $subject
 * @property string $type
 * @property string|null $body
 * @property string $status
 * @property Carbon|null $sent_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Clinic $clinic
 * @property-read Order|null $order
 */
class Email extends Model
{

    use HasFactory;
    const TYPE_ORDER_CONFIRMATION = 'order_confirmation';
    const TYPE_WELCOME = 'welcome';

    const TYPE_PROGRAM_INFO = 'program_info';

    const TYPE_REMINDER = 'reminder';

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    public const TYPE_PROGRAM_DELIVERY = 'program_delivery';
    public const TYPE_CONTACT_FORM = 'contact_form';



    protected $fillable = [
        'clinic_id', 'order_id', 'to_email', 'subject',
        'type', 'status', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isProgramDelivery(): bool
    {
        return $this->type === self::TYPE_PROGRAM_DELIVERY;
    }

    public function isContactForm(): bool
    {
        return $this->type === self::TYPE_CONTACT_FORM;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
