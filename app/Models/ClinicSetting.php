<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property int $clinic_id
 * @property string $key
 * @property string|null $value
 * @property string $group
 * @property Carbon|null $updated_at
 * @property-read Clinic $clinic
 */
class ClinicSetting extends Model
{
    public $timestamps = false;

    protected $fillable = ['clinic_id', 'key', 'value', 'group'];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
