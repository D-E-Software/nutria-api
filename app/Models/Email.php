<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property int $clinic_id
 * @property int|null $order_id
 * @property string $to_email
 * @property string $subject
 * @property string $type
 * @property string $status
 * @property Carbon|null $sent_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Clinic $clinic
 * @property-read Order|null $order
 */
class Email extends Model
{
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
}
