<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'pricing_type', // fixed | markup
        'markup_percent',
        'sell_price'
    ];
}
