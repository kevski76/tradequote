<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'key', // panels, posts, boards, rails
        'name',
        'type',
        'calculation',
    ];
}
