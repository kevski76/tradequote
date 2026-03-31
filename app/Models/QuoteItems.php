<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuoteItems extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'quote_items';

    protected $fillable = [
        'quote_id',
        'name', 
        'quantity', 
        'unit_price',
        'total_price'
    ];
}
