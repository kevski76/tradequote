<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganisationPrices extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'organisation_prices';

    protected $fillable = [
        'organisation_id',
        'module_item_id',
        'cost_price',
        'cost_prices_by_height',
        'pricing_type', // fixed | markup
        'markup_percent',
        'sell_price',
        'sell_prices_by_height',
    ];

    protected $casts = [
        'cost_prices_by_height' => 'array',
        'sell_prices_by_height' => 'array',
    ];

    public function moduleItem(): BelongsTo
    {
        return $this->belongsTo(ModuleItems::class, 'module_item_id');
    }
}
