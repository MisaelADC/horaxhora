<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DowntimeReason extends Model
{
    protected $table = 'DowntimeReasons';
    protected $primaryKey = 'IdDowntimeReason';
    public $timestamps = false;

    protected $fillable = [
        'Reason'
    ];
    
}
