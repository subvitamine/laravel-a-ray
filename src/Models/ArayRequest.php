<?php

namespace Subvitamine\LaravelARay\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArayRequest extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'aray_requests';
    protected $primaryKey = 'id';


    protected $casts = [
        'request' => 'array',
        'response' => 'array'
    ];

    protected $guarded = [];
}
