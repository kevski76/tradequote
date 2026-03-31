<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotes extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'quotes';

    protected $fillable = [
        'organisation_id',
        'created_by', // user who made quote
        'module_id', // panel | closeboard
        'variant_key',
        'length',
        'labour_type',
        'labour_total',
        'materials_total',
        'total_price'
    ];
}
