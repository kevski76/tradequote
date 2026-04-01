<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'module_item_id',
        'name',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quotes::class, 'quote_id');
    }

    public function moduleItem(): BelongsTo
    {
        return $this->belongsTo(ModuleItems::class, 'module_item_id');
    }
}
