<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'organisation_id',
        'quote_uuid',
        'customer_name',
        'customer_email',
        'customer_phone',
        'message',
    ];
}
