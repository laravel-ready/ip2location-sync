<?php

namespace DragAndPublish\Ip2locationSync\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ip2LocationSync extends Model
{
    use HasFactory;

    public $fillable = [
        'file_path',
        'sync_status'
    ];

    public $casts = [
        'sync_status' => 'boolean'
    ];
}
