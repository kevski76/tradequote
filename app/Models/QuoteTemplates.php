<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuoteTemplates extends Model
{   
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'created_by', // user_id
        'name',
        'module_id',
        'variant_key',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
