<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Quotes extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_WORK_COMPLETE = 'work_complete';

    public static array $statuses = [self::STATUS_DRAFT, self::STATUS_ACCEPTED, self::STATUS_DECLINED, self::STATUS_WORK_COMPLETE];

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
        'customer_phone',
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
        'status' => 'string',
        'calculation_data' => 'array',
        'pdf_generated_at' => 'datetime',
    ];

    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItems::class, 'quote_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Modules::class, 'module_id');
    }

    /**
     * Normalize status values for backward compatibility
     * Maps 'rejected' → 'declined', validates against known statuses
     */
    public function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));
        
        // Map legacy 'rejected' to 'declined'
        if ($status === 'rejected') {
            return self::STATUS_DECLINED;
        }
        
        // Return status if valid, else default to draft
        return in_array($status, self::$statuses, true) ? $status : self::STATUS_DRAFT;
    }

    /**
     * Check if quote is in a final status (cannot be responded to further)
     */
    public function isFinalStatus(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_DECLINED, self::STATUS_WORK_COMPLETE], true);
    }
}
