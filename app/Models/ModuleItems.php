<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModuleItems extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'module_items';

    protected $fillable = [
        'module_id',
        'formula_key',
        'key', // panels, posts, boards, rails
        'name', // -- "Fence Panel"
        'type', // -- material | labour
        'calculation', // -- formula | direct
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Modules::class, 'module_id');
    }

    public function organisationPrices(): HasMany
    {
        return $this->hasMany(OrganisationPrices::class, 'module_item_id');
    }
}
