<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modules extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'modules';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function moduleItems(): HasMany
    {
        return $this->hasMany(ModuleItems::class, 'module_id');
    }
}
