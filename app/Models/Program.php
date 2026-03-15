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
 * @property string $title
 * @property float $price
 * @property string $currency
 * @property string|null $duration
 * @property string|null $description
 * @property array|null $features
 * @property string|null $pdf_path
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $sort_order
 * @property array|null $translations
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Clinic $clinic
 * @property-read Collection<Order> $orders
 */
class Program extends Model
{
    protected $fillable = [
        'clinic_id', 'title', 'price', 'currency', 'duration',
        'description', 'features', 'pdf_path', 'is_active',
        'is_featured', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'translations' => 'array',
    ];

    public function translate(string $field, string $locale = 'tr'): mixed
    {
        if ($locale === 'tr') return $this->$field;
        return $this->translations[$locale][$field] ?? $this->$field;
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
