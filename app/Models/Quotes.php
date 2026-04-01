<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Quotes extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'quotes';

    protected static function booted(): void
    {
        static::creating(function (self $quote) {
            if (empty($quote->uuid)) {
                $quote->uuid = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'organisation_id',
        'created_by', // user who made quote
        'module_id', // panel | closeboard
        'variant_key',
        'uuid',
        'customer_name',
        'job_name',
        'status',
        'length',
        'labour_type',
        'labour_total',
        'materials_total',
        'subtotal_price',
        'vat_rate',
        'vat_total',
        'payment_terms',
        'calculation_data',
        'pdf_path',
        'pdf_generated_at',
        'total_price'
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'calculation_data' => 'array',
        'pdf_generated_at' => 'datetime',
    ];
}
