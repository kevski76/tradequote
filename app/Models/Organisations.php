<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organisations extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'organisations';

    protected $fillable = [
        'name',
        'owner_id',
        'address',
        'city',
        'postcode',
        'phone',
        'logo',
        'quote_defaults',
    ];

    protected $casts = [
        'quote_defaults' => 'array',
    ];
}
